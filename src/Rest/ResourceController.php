<?php

namespace EnterpriseContentHub\Rest;

use EnterpriseContentHub\Application\ResourceQuery;
use EnterpriseContentHub\Domain\Resource\Resource;
use EnterpriseContentHub\Services\ResourceService;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

final class ResourceController {

	private const NAMESPACE = 'ech/v1';

	public function __construct(
		private readonly ResourceService $resource_service
	) {
	}

	public function register_routes(): void {

		register_rest_route(
			self::NAMESPACE,
			'/resources',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'index' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'search'     => array(
						'type'              => 'string',
						'required'          => false,
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					),

					'department' => array(
						'type'              => 'string',
						'required'          => false,
						'default'           => '',
						'sanitize_callback' => 'sanitize_title',
					),

					'page'       => array(
						'type'              => 'integer',
						'required'          => false,
						'default'           => 1,
						'sanitize_callback' => 'absint',
						'validate_callback' => static function ( $value ): bool {
							return (int) $value >= 1;
						},
					),

					'per_page'   => array(
						'type'              => 'integer',
						'required'          => false,
						'default'           => 10,
						'sanitize_callback' => 'absint',
						'validate_callback' => static function ( $value ): bool {
							$value = (int) $value;

							return $value >= 1 && $value <= 50;
						},
					),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/resources/(?P<id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'show' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'id' => array(
						'type'              => 'integer',
						'required'          => true,
						'sanitize_callback' => 'absint',
						'validate_callback' => static function ( $value ): bool {
							return (int) $value > 0;
						},
					),
				),
			)
		);
	}

	public function index( WP_REST_Request $request ): WP_REST_Response {

		$search = $request->get_param( 'search' );

		if ( ! is_string( $search ) ) {
			$search = '';
		}

		$department = $request->get_param( 'department' );

		if ( ! is_string( $department ) ) {
			$department = '';
		}

		$page = $request->get_param( 'page' );

		if ( ! is_numeric( $page ) ) {
			$page = 1;
		}

		$per_page = $request->get_param( 'per_page' );

		if ( ! is_numeric( $per_page ) ) {
			$per_page = 10;
		}

		$query = new ResourceQuery(
			search: $search,
			department: $department,
			page: (int) $page,
			per_page: (int) $per_page
		);

		$result = $this->resource_service->search_resources( $query );

		$data = array_map(
			static function ( Resource $resource_item ): array {
				return $resource_item->to_array();
			},
			$result['data']
		);

		return new WP_REST_Response(
			array(
				'data'       => $data,
				'pagination' => $result['pagination'],
			),
			200
		);
	}

	public function show( WP_REST_Request $request ) {

		$id = $request->get_param( 'id' );

		if ( ! is_numeric( $id ) ) {
			return new WP_Error(
				'ech_invalid_resource_id',
				__( 'Invalid resource ID.', 'wp-enterprise-content-hub' ),
				array(
					'status' => 400,
				)
			);
		}

		$resource = $this->resource_service->get_resource(
			(int) $id
		);

		if (
			null === $resource ||
			'publish' !== $resource->get_status()
		) {
			return new WP_Error(
				'ech_resource_not_found',
				__( 'Resource not found.', 'wp-enterprise-content-hub' ),
				array(
					'status' => 404,
				)
			);
		}

		return new WP_REST_Response(
			array(
				'data' => $resource->to_array(),
			),
			200
		);
	}
}
