<?php

namespace EnterpriseContentHub\Contracts;

use EnterpriseContentHub\Application\ResourceQuery;
use EnterpriseContentHub\Domain\Resource\Resource;

interface ResourceRepository {

	public function find( int $id ): ?Resource;

	/**@return array<int, Resource>**/
	public function search( ResourceQuery $query ): array;

	public function count( ResourceQuery $query ): int;
}
