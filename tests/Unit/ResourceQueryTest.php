<?php

namespace EnterpriseContentHub\Tests\Unit;

use EnterpriseContentHub\Application\ResourceQuery;
use PHPUnit\Framework\TestCase;

final class ResourceQueryTest extends TestCase {

	public function test_default_values_are_correct(): void {

		$query = new ResourceQuery();

		self::assertSame(
			'',
			$query->get_search()
		);

		self::assertSame(
			'',
			$query->get_department()
		);

		self::assertSame(
			1,
			$query->get_page()
		);

		self::assertSame(
			10,
			$query->get_per_page()
		);

		self::assertSame(
			'publish',
			$query->get_status()
		);
	}

	public function test_page_cannot_be_less_than_one(): void {

		$query = new ResourceQuery(
			page: 0
		);

		self::assertSame(
			1,
			$query->get_page()
		);
	}

	public function test_per_page_cannot_be_less_than_one(): void {

		$query = new ResourceQuery(
			per_page: 0
		);

		self::assertSame(
			1,
			$query->get_per_page()
		);
	}

	public function test_per_page_cannot_exceed_fifty(): void {

		$query = new ResourceQuery(
			per_page: 500
		);

		self::assertSame(
			50,
			$query->get_per_page()
		);
	}

	public function test_query_values_are_available(): void {

		$query = new ResourceQuery(
			search: 'security',
			department: 'engineering',
			page: 2,
			per_page: 20
		);

		self::assertSame(
			'security',
			$query->get_search()
		);

		self::assertSame(
			'engineering',
			$query->get_department()
		);

		self::assertSame(
			2,
			$query->get_page()
		);

		self::assertSame(
			20,
			$query->get_per_page()
		);
	}
}
