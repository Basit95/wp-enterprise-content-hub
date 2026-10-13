<?php

namespace EnterpriseContentHub\Contracts;

interface CacheStore {

	/**
	 * Get a cached value.
	 *
	 * @return array{
	 *     hit: bool,
	 *     value: mixed
	 * }
	 */
	public function get( string $key ): array;

	/**
	 * Store a value in cache.
	 */
	public function set(
		string $key,
		mixed $value,
		int $ttl
	): void;

	/**
	 * Invalidate all plugin cache entries.
	 */
	public function flush(): void;
}
