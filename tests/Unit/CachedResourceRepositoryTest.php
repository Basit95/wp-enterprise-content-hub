<?php

namespace EnterpriseContentHub\Tests\Unit;

use EnterpriseContentHub\Application\ResourceQuery;
use EnterpriseContentHub\Contracts\ResourceRepository;
use EnterpriseContentHub\Domain\Resource\Resource;
use EnterpriseContentHub\Infrastructure\CachedResourceRepository;
use EnterpriseContentHub\Tests\Support\FakeCacheStore;
use PHPUnit\Framework\TestCase;

final class CachedResourceRepositoryTest extends TestCase {

	public function test_find_uses_cache_after_first_lookup(): void {

		$resource = $this->create_resource();

		$repository = $this->createMock(
			ResourceRepository::class
		);

		$repository
			->expects( self::once() )
			->method( 'find' )
			->with( 10 )
			->willReturn( $resource );

		$cached_repository = new CachedResourceRepository(
			$repository,
			new FakeCacheStore()
		);

		self::assertSame(
			$resource,
			$cached_repository->find( 10 )
		);

		self::assertSame(
			$resource,
			$cached_repository->find( 10 )
		);
	}

	public function test_find_caches_missing_resource(): void {

		$repository = $this->createMock(
			ResourceRepository::class
		);

		$repository
			->expects( self::once() )
			->method( 'find' )
			->with( 99 )
			->willReturn( null );

		$cached_repository = new CachedResourceRepository(
			$repository,
			new FakeCacheStore()
		);

		self::assertNull(
			$cached_repository->find( 99 )
		);

		self::assertNull(
			$cached_repository->find( 99 )
		);
	}

	public function test_search_uses_cache_for_identical_query(): void {

		$query = new ResourceQuery(
			department: 'engineering',
			page: 1,
			per_page: 6
		);

		$resources = array(
			$this->create_resource(),
		);

		$repository = $this->createMock(
			ResourceRepository::class
		);

		$repository
			->expects( self::once() )
			->method( 'search' )
			->with( $query )
			->willReturn( $resources );

		$cached_repository = new CachedResourceRepository(
			$repository,
			new FakeCacheStore()
		);

		self::assertSame(
			$resources,
			$cached_repository->search( $query )
		);

		self::assertSame(
			$resources,
			$cached_repository->search( $query )
		);
	}

	public function test_count_uses_cache_for_identical_query(): void {

		$query = new ResourceQuery(
			search: 'security',
			page: 1,
			per_page: 10
		);

		$repository = $this->createMock(
			ResourceRepository::class
		);

		$repository
			->expects( self::once() )
			->method( 'count' )
			->with( $query )
			->willReturn( 3 );

		$cached_repository = new CachedResourceRepository(
			$repository,
			new FakeCacheStore()
		);

		self::assertSame(
			3,
			$cached_repository->count( $query )
		);

		self::assertSame(
			3,
			$cached_repository->count( $query )
		);
	}

	private function create_resource(): Resource {

		return new Resource(
			id: 10,
			title: 'Application Security Policy',
			excerpt: 'Security guidance.',
			content: 'Security content.',
			status: 'publish',
			permalink: 'https://example.com/resources/application-security-policy/',
			departments: array(
				'Engineering',
			),
			updated_at: '2026-10-13T10:00:00+00:00'
		);
	}
}
