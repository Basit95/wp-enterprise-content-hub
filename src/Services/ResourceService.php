<?php

namespace EnterpriseContentHub\Services;

use EnterpriseContentHub\Application\ResourceQuery;
use EnterpriseContentHub\Contracts\ResourceRepository;
use EnterpriseContentHub\Domain\Resource\Resource;

final class ResourceService {

	public function __construct(
		private readonly ResourceRepository $repository
	) {
	}

	public function get_resource( int $id ): ?Resource {

		if ( $id <= 0 ) {
			return null;
		}

		return $this->repository->find( $id );
	}

	/**@return array{
	 *     data: array<int, Resource>,
	 *     pagination: array{
	 *         page: int,
	 *         per_page: int,
	 *         total: int,
	 *         total_pages: int
	 *     }
	 * }
	 */
	public function search_resources( ResourceQuery $query ): array {

		$resources = $this->repository->search( $query );

		$total = $this->repository->count( $query );

		$per_page = $query->get_per_page();

		return array(
			'data'       => $resources,
			'pagination' => array(
				'page'        => $query->get_page(),
				'per_page'    => $per_page,
				'total'       => $total,
				'total_pages' => (int) ceil( $total / $per_page ),
			),
		);
	}
}
