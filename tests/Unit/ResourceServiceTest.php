<?php

namespace EnterpriseContentHub\Tests\Unit;

use EnterpriseContentHub\Application\ResourceQuery;
use EnterpriseContentHub\Domain\Resource\Resource;
use EnterpriseContentHub\Services\ResourceService;
use EnterpriseContentHub\Tests\Support\FakeResourceRepository;
use PHPUnit\Framework\TestCase;

final class ResourceServiceTest extends TestCase {

	public function test_service_returns_resource_by_id(): void {

		$resource = $this->create_resource(
			1,
			'Application Security Policy'
		);

		$repository = new FakeResourceRepository(
			array(
				$resource,
			)
		);

		$service = new ResourceService(
			$repository
		);

		$result = $service->get_resource( 1 );

		self::assertNotNull( $result );

		self::assertSame(
			1,
			$result->get_id()
		);

		self::assertSame(
			'Application Security Policy',
			$result->get_title()
		);
	}

	public function test_service_returns_null_for_invalid_id(): void {

		$repository = new FakeResourceRepository();

		$service = new ResourceService(
			$repository
		);

		self::assertNull(
			$service->get_resource( 0 )
		);

		self::assertNull(
			$service->get_resource( -10 )
		);
	}

	public function test_service_returns_null_when_resource_does_not_exist(): void {

		$repository = new FakeResourceRepository();

		$service = new ResourceService(
			$repository
		);

		self::assertNull(
			$service->get_resource( 999 )
		);
	}

	public function test_search_returns_resources_and_pagination(): void {

		$repository = new FakeResourceRepository(
			array(
				$this->create_resource(
					1,
					'Security Policy'
				),
				$this->create_resource(
					2,
					'Employee Handbook'
				),
				$this->create_resource(
					3,
					'Financial Policy'
				),
			)
		);

		$service = new ResourceService(
			$repository
		);

		$query = new ResourceQuery(
			page: 1,
			per_page: 2
		);

		$result = $service->search_resources(
			$query
		);

		self::assertCount(
			2,
			$result['data']
		);

		self::assertSame(
			1,
			$result['pagination']['page']
		);

		self::assertSame(
			2,
			$result['pagination']['per_page']
		);

		self::assertSame(
			3,
			$result['pagination']['total']
		);

		self::assertSame(
			2,
			$result['pagination']['total_pages']
		);
	}

	private function create_resource(
		int $id,
		string $title
	): Resource {

		return new Resource(
			id: $id,
			title: $title,
			excerpt: 'Test excerpt.',
			content: 'Test content.',
			status: 'publish',
			permalink: 'https://example.com/resource/' . $id,
			departments: array(
				'Engineering',
			),
			updated_at: '2026-10-09T10:00:00+00:00'
		);
	}
}
