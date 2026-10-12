<?php

namespace EnterpriseContentHub\Infrastructure;

use EnterpriseContentHub\Application\ResourceQuery;
use EnterpriseContentHub\Content\ResourcePostType;
use EnterpriseContentHub\Content\ResourceTaxonomy;
use EnterpriseContentHub\Contracts\ResourceRepository;
use EnterpriseContentHub\Domain\Resource\Resource;
use WP_Post;
use WP_Query;

final class WordPressResourceRepository implements ResourceRepository {

	public function find( int $id ): ?Resource {

		$post = get_post( $id );

		if (
			! $post instanceof WP_Post ||
			ResourcePostType::POST_TYPE !== $post->post_type
		) {
			return null;
		}

		return $this->map_post_to_resource( $post );
	}

	public function search( ResourceQuery $query ): array {

		$wp_query = new WP_Query(
			$this->build_query_args( $query )
		);

		$resources = array();

		foreach ( $wp_query->posts as $post ) {

			if ( ! $post instanceof WP_Post ) {
				continue;
			}

			$resources[] = $this->map_post_to_resource( $post );
		}

		return $resources;
	}

	public function count( ResourceQuery $query ): int {

		$args = $this->build_query_args( $query );

		$args['posts_per_page'] = 1;
		$args['fields']         = 'ids';
		$args['no_found_rows']  = false;

		$wp_query = new WP_Query( $args );

		return (int) $wp_query->found_posts;
	}

	private function build_query_args( ResourceQuery $query ): array {

		$args = array(
			'post_type'      => ResourcePostType::POST_TYPE,
			'post_status'    => $query->get_status(),
			'posts_per_page' => $query->get_per_page(),
			'paged'          => $query->get_page(),
			'orderby'        => 'modified',
			'order'          => 'DESC',
		);

		if ( '' !== $query->get_search() ) {
			$args['s'] = $query->get_search();
		}

		if ( '' !== $query->get_department() ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => ResourceTaxonomy::TAXONOMY,
					'field'    => 'slug',
					'terms'    => $query->get_department(),
				),
			);
		}

		return $args;
	}

	private function map_post_to_resource( WP_Post $post ): Resource {

		$departments = wp_get_post_terms(
			$post->ID,
			ResourceTaxonomy::TAXONOMY,
			array(
				'fields' => 'names',
			)
		);

		if ( is_wp_error( $departments ) ) {
			$departments = array();
		}

		return new Resource(
			id: (int) $post->ID,
			title: get_the_title( $post ),
			excerpt: get_the_excerpt( $post ),
			content: $post->post_content,
			status: $post->post_status,
			permalink: get_permalink( $post ),
			departments: array_values( $departments ),
			updated_at: get_post_modified_time(
				'c',
				true,
				$post
			)
		);
	}
}
