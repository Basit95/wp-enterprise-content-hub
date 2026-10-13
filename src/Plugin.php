<?php

namespace EnterpriseContentHub;

use EnterpriseContentHub\Blocks\ResourceExplorerBlock;
use EnterpriseContentHub\Cache\ResourceCacheInvalidator;
use EnterpriseContentHub\Cli\CacheCommand;
use EnterpriseContentHub\Cli\ResourceCommand;
use EnterpriseContentHub\Content\ResourcePostType;
use EnterpriseContentHub\Content\ResourceTaxonomy;
use EnterpriseContentHub\Contracts\CacheStore;
use EnterpriseContentHub\Infrastructure\CachedResourceRepository;
use EnterpriseContentHub\Infrastructure\WordPressResourceRepository;
use EnterpriseContentHub\Infrastructure\WordPressTransientCache;
use EnterpriseContentHub\Rest\DepartmentController;
use EnterpriseContentHub\Rest\ResourceController;
use EnterpriseContentHub\Services\ResourceService;
use WP_CLI;

final class Plugin {

	private ResourceService $resource_service;

	private CacheStore $cache;

	public function __construct() {

		$this->cache = new WordPressTransientCache();

		$wordpress_repository =
			new WordPressResourceRepository();

		$repository = new CachedResourceRepository(
			$wordpress_repository,
			$this->cache
		);

		$this->resource_service = new ResourceService(
			$repository
		);

		$cache_invalidator =
			new ResourceCacheInvalidator(
				$this->cache
			);

		$cache_invalidator->register();
	}

	public function boot(): void {

		$resource_post_type =
			new ResourcePostType();

		$resource_taxonomy =
			new ResourceTaxonomy();

		$resource_block =
			new ResourceExplorerBlock(
				$this->resource_service
			);

		$resource_post_type->register();

		$resource_taxonomy->register();

		$resource_block->register();

		$this->register_rest_api();

		$this->register_cli_commands();
	}

	private function register_rest_api(): void {

		$resource_controller =
			new ResourceController(
				$this->resource_service
			);

		$department_controller =
			new DepartmentController();

		add_action(
			'rest_api_init',
			array(
				$resource_controller,
				'register_routes',
			)
		);

		add_action(
			'rest_api_init',
			array(
				$department_controller,
				'register_routes',
			)
		);
	}

	private function register_cli_commands(): void {

		if (
			! defined( 'WP_CLI' ) ||
			! WP_CLI
		) {
			return;
		}

		WP_CLI::add_command(
			'ech cache',
			new CacheCommand(
				$this->cache
			)
		);

		WP_CLI::add_command(
			'ech resources',
			new ResourceCommand()
		);
	}

	public function get_resource_service(): ResourceService {

		return $this->resource_service;
	}
}
