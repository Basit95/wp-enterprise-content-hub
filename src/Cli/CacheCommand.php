<?php

namespace EnterpriseContentHub\Cli;

use EnterpriseContentHub\Contracts\CacheStore;
use WP_CLI;

final class CacheCommand {

	public function __construct(
		private readonly CacheStore $cache
	) {
	}

	/**
	 * Clear Enterprise Content Hub cache.
	 *
	 * ## EXAMPLES
	 *
	 *     wp ech cache clear
	 */
	public function clear(): void {

		$this->cache->flush();

		WP_CLI::success(
			'Enterprise Content Hub cache cleared.'
		);
	}
}
