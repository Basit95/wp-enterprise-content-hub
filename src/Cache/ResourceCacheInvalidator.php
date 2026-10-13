<?php

namespace EnterpriseContentHub\Cache;

use EnterpriseContentHub\Content\ResourcePostType;
use EnterpriseContentHub\Content\ResourceTaxonomy;
use EnterpriseContentHub\Contracts\CacheStore;
use WP_Post;

final class ResourceCacheInvalidator {

	public function __construct(
		private readonly CacheStore $cache
	) {
	}

	public function register(): void {

		add_action(
			'save_post_' . ResourcePostType::POST_TYPE,
			array( $this, 'resource_saved' ),
			10,
			2
		);

		add_action(
			'deleted_post',
			array( $this, 'resource_deleted' ),
			10,
			2
		);

		add_action(
			'set_object_terms',
			array( $this, 'terms_changed' ),
			10,
			6
		);

		add_action(
			'created_' . ResourceTaxonomy::TAXONOMY,
			array( $this, 'department_changed' )
		);

		add_action(
			'edited_' . ResourceTaxonomy::TAXONOMY,
			array( $this, 'department_changed' )
		);

		add_action(
			'delete_' . ResourceTaxonomy::TAXONOMY,
			array( $this, 'department_changed' )
		);
	}

	public function resource_saved(
		int $post_id,
		WP_Post $post
	): void {

		if (
			wp_is_post_revision( $post_id ) ||
			wp_is_post_autosave( $post_id )
		) {
			return;
		}

		if ( ResourcePostType::POST_TYPE !== $post->post_type ) {
			return;
		}

		$this->cache->flush();
	}

	public function resource_deleted(
		int $post_id,
		WP_Post $post
	): void {

		unset( $post_id );

		if ( ResourcePostType::POST_TYPE !== $post->post_type ) {
			return;
		}

		$this->cache->flush();
	}

	public function terms_changed(
		int $object_id,
		array $terms,
		array $term_taxonomy_ids,
		string $taxonomy,
		bool $append,
		array $old_term_taxonomy_ids
	): void {

		unset(
			$terms,
			$term_taxonomy_ids,
			$append,
			$old_term_taxonomy_ids
		);

		if ( ResourceTaxonomy::TAXONOMY !== $taxonomy ) {
			return;
		}

		if (
			ResourcePostType::POST_TYPE !==
			get_post_type( $object_id )
		) {
			return;
		}

		$this->cache->flush();
	}

	public function department_changed(): void {

		$this->cache->flush();
	}
}
