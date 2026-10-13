<?php

namespace EnterpriseContentHub\Infrastructure;

use EnterpriseContentHub\Application\ResourceQuery;
use EnterpriseContentHub\Contracts\CacheStore;
use EnterpriseContentHub\Contracts\ResourceRepository;
use EnterpriseContentHub\Domain\Resource\Resource;

final class CachedResourceRepository implements ResourceRepository {

	private const CACHE_TTL = 300;

	public function __construct(
		private readonly ResourceRepository $repository,
		private readonly CacheStore $cache
	) {
	}

	public function find( int $id ): ?Resource {

		$key = 'resource:' . $id;

		$cached = $this->cache->get( $key );

		if ( $cached['hit'] ) {
			return $cached['value'];
		}

		$resource = $this->repository->find( $id );

		$this->cache->set(
			$key,
			$resource,
			self::CACHE_TTL
		);

		return $resource;
	}

	public function search( ResourceQuery $query ): array {

		$key = 'resource_search:' . $this->query_hash( $query );

		$cached = $this->cache->get( $key );

		if (
			$cached['hit'] &&
			is_array( $cached['value'] )
		) {
			return $cached['value'];
		}

		$resources = $this->repository->search( $query );

		$this->cache->set(
			$key,
			$resources,
			self::CACHE_TTL
		);

		return $resources;
	}

	public function count( ResourceQuery $query ): int {

		$key = 'resource_count:' . $this->query_hash( $query );

		$cached = $this->cache->get( $key );

		if (
			$cached['hit'] &&
			is_int( $cached['value'] )
		) {
			return $cached['value'];
		}

		$total = $this->repository->count( $query );

		$this->cache->set(
			$key,
			$total,
			self::CACHE_TTL
		);

		return $total;
	}

	private function query_hash( ResourceQuery $query ): string {

		$data = array(
			'search'     => $query->get_search(),
			'department' => $query->get_department(),
			'page'       => $query->get_page(),
			'per_page'   => $query->get_per_page(),
			'status'     => $query->get_status(),
		);

		return hash(
			'sha256',
			http_build_query(
				$data,
				'',
				'&',
				PHP_QUERY_RFC3986
			)
		);
	}
}
