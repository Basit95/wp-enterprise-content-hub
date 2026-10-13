<?php

namespace EnterpriseContentHub\Cli;

use EnterpriseContentHub\Content\ResourcePostType;
use EnterpriseContentHub\Content\ResourceTaxonomy;
use WP_CLI;
use WP_Query;

final class ResourceCommand {

	private const SEED_META_KEY = '_ech_seed_key';

	/**
	 * Seed demonstration resources and departments.
	 *
	 * ## EXAMPLES
	 *
	 *     wp ech resources seed
	 */
	public function seed(): void {

		$resources = $this->get_seed_data();

		$created = 0;
		$skipped = 0;

		foreach ( $resources as $resource ) {

			$term_id = $this->ensure_department(
				$resource['department']
			);

			if ( $this->resource_exists( $resource['seed_key'] ) ) {
				++$skipped;

				WP_CLI::log(
					sprintf(
						'Skipped existing resource: %s',
						$resource['title']
					)
				);

				continue;
			}

			$post_id = wp_insert_post(
				array(
					'post_type'    => ResourcePostType::POST_TYPE,
					'post_status'  => 'publish',
					'post_title'   => $resource['title'],
					'post_excerpt' => $resource['excerpt'],
					'post_content' => $resource['content'],
				),
				true
			);

			if ( is_wp_error( $post_id ) ) {

				WP_CLI::warning(
					sprintf(
						'Could not create "%s": %s',
						$resource['title'],
						$post_id->get_error_message()
					)
				);

				continue;
			}

			update_post_meta(
				$post_id,
				self::SEED_META_KEY,
				$resource['seed_key']
			);

			wp_set_object_terms(
				$post_id,
				array( $term_id ),
				ResourceTaxonomy::TAXONOMY
			);

			++$created;

			WP_CLI::log(
				sprintf(
					'Created resource: %s',
					$resource['title']
				)
			);
		}

		WP_CLI::success(
			sprintf(
				'Seeding complete. Created: %d, skipped: %d.',
				$created,
				$skipped
			)
		);
	}

	private function ensure_department(
		string $department
	): int {

		$existing = term_exists(
			$department,
			ResourceTaxonomy::TAXONOMY
		);

		if ( is_array( $existing ) ) {
			return (int) $existing['term_id'];
		}

		if ( is_int( $existing ) ) {
			return $existing;
		}

		$result = wp_insert_term(
			$department,
			ResourceTaxonomy::TAXONOMY
		);

		if ( is_wp_error( $result ) ) {

			WP_CLI::error(
				sprintf(
					'Could not create department "%s": %s',
					$department,
					$result->get_error_message()
				)
			);
		}

		return (int) $result['term_id'];
	}

	private function resource_exists(
		string $seed_key
	): bool {

		$query = new WP_Query(
			array(
				'post_type'      => ResourcePostType::POST_TYPE,
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::SEED_META_KEY,
				'meta_value'     => $seed_key,
				'no_found_rows'  => true,
			)
		);

		return ! empty( $query->posts );
	}

	/**
	 * Get resources used by the seed command.
	 *
	 * @return array<int, array{
	 *     seed_key: string,
	 *     title: string,
	 *     department: string,
	 *     excerpt: string,
	 *     content: string
	 * }>
	 */
	private function get_seed_data(): array {

		return array(
			array(
				'seed_key'   => 'application-security-policy',
				'title'      => 'Application Security Policy',
				'department' => 'Engineering',
				'excerpt'    => 'Guidelines for authentication, authorization, secure coding, and application security reviews.',
				'content'    => 'This resource defines the application security requirements and secure development practices used by the engineering organization.',
			),
			array(
				'seed_key'   => 'employee-onboarding-guide',
				'title'      => 'Employee Onboarding Guide',
				'department' => 'Human Resources',
				'excerpt'    => 'Step-by-step onboarding process covering access, policies, tools, and team orientation.',
				'content'    => 'This guide describes the standard employee onboarding process, including account access, company policies, tools, and team orientation.',
			),
			array(
				'seed_key'   => 'financial-approval-policy',
				'title'      => 'Financial Approval Policy',
				'department' => 'Finance',
				'excerpt'    => 'Approval thresholds, authorization levels, and financial governance procedures.',
				'content'    => 'This policy defines financial approval thresholds, authorization responsibilities, and governance requirements.',
			),
		);
	}
}
