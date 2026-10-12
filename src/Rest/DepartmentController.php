<?php

namespace EnterpriseContentHub\Rest;

use EnterpriseContentHub\Content\ResourceTaxonomy;
use WP_Error;
use WP_REST_Response;

final class DepartmentController {

	private const NAMESPACE = 'ech/v1';

	public function register_routes(): void {

		register_rest_route(
			self::NAMESPACE,
			'/departments',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'index' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	public function index() {

		$terms = get_terms(
			array(
				'taxonomy'   => ResourceTaxonomy::TAXONOMY,
				'hide_empty' => false,
			)
		);

		if ( is_wp_error( $terms ) ) {
			return new WP_Error(
				'ech_departments_error',
				__( 'Unable to load departments.', 'wp-enterprise-content-hub' ),
				array(
					'status' => 500,
				)
			);
		}

		$data = array_map(
			static function ( $term ): array {
				return array(
					'id'    => (int) $term->term_id,
					'name'  => $term->name,
					'slug'  => $term->slug,
					'count' => (int) $term->count,
				);
			},
			$terms
		);

		return new WP_REST_Response(
			array(
				'data' => $data,
			),
			200
		);
	}
}
