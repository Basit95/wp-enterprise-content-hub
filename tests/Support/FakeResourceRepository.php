<?php

namespace EnterpriseContentHub\Tests\Support;

use EnterpriseContentHub\Application\ResourceQuery;
use EnterpriseContentHub\Contracts\ResourceRepository;
use EnterpriseContentHub\Domain\Resource\Resource;

final class FakeResourceRepository implements ResourceRepository {

	/**@param array<int, Resource> $resources*/
	public function __construct(
		private array $resources = array()
	) {
	}

	public function find( int $id ): ?Resource {

		foreach ( $this->resources as $resource ) {
			if ( $resource->get_id() === $id ) {
				return $resource;
			}
		}

		return null;
	}

	/**@return array<int, Resource>*/
	public function search( ResourceQuery $query ): array {

		return array_slice(
			$this->resources,
			0,
			$query->get_per_page()
		);
	}

	public function count( ResourceQuery $query ): int {

		return count( $this->resources );
	}
}
