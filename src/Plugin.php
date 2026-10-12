<?php

namespace EnterpriseContentHub;

use EnterpriseContentHub\Blocks\ResourceExplorerBlock;
use EnterpriseContentHub\Content\ResourcePostType;
use EnterpriseContentHub\Content\ResourceTaxonomy;
use EnterpriseContentHub\Infrastructure\WordPressResourceRepository;
use EnterpriseContentHub\Rest\DepartmentController;
use EnterpriseContentHub\Rest\ResourceController;
use EnterpriseContentHub\Services\ResourceService;

final class Plugin {

	private ResourceService $resource_service;

	public function __construct() {

		$repository = new WordPressResourceRepository();

		$this->resource_service = new ResourceService(
			$repository
		);
	}

	public function boot(): void {

		$resource_post_type = new ResourcePostType();
		$resource_taxonomy  = new ResourceTaxonomy();

		$resource_block = new ResourceExplorerBlock(
			$this->resource_service
		);

		$resource_post_type->register();
		$resource_taxonomy->register();
		$resource_block->register();

		$this->register_rest_api();
	}

	private function register_rest_api(): void {

		$resource_controller = new ResourceController(
			$this->resource_service
		);

		$department_controller = new DepartmentController();

		add_action(
			'rest_api_init',
			array( $resource_controller, 'register_routes' )
		);

		add_action(
			'rest_api_init',
			array( $department_controller, 'register_routes' )
		);
	}

	public function get_resource_service(): ResourceService {

		return $this->resource_service;
	}
}
