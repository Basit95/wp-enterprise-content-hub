<?php

namespace EnterpriseContentHub\Tests\Unit;

use EnterpriseContentHub\Domain\Resource\Resource;
use PHPUnit\Framework\TestCase;

final class ResourceTest extends TestCase {

	public function test_resource_exposes_expected_values(): void {

		$resource = new Resource(
			id: 100,
			title: 'Application Security Policy',
			excerpt: 'Security policy description.',
			content: 'Full policy content.',
			status: 'publish',
			permalink: 'https://example.com/resource/security-policy',
			departments: array(
				'Engineering',
			),
			updated_at: '2026-10-09T10:00:00+00:00'
		);

		self::assertSame(
			100,
			$resource->get_id()
		);

		self::assertSame(
			'Application Security Policy',
			$resource->get_title()
		);

		self::assertSame(
			array(
				'Engineering',
			),
			$resource->get_departments()
		);

		self::assertSame(
			'publish',
			$resource->get_status()
		);
	}

	public function test_resource_can_be_converted_to_array(): void {

		$resource = new Resource(
			id: 100,
			title: 'Application Security Policy',
			excerpt: 'Security policy description.',
			content: 'Full policy content.',
			status: 'publish',
			permalink: 'https://example.com/resource/security-policy',
			departments: array(
				'Engineering',
			),
			updated_at: '2026-10-09T10:00:00+00:00'
		);

		$data = $resource->to_array();

		self::assertSame(
			100,
			$data['id']
		);

		self::assertSame(
			'Application Security Policy',
			$data['title']
		);

		self::assertSame(
			array(
				'Engineering',
			),
			$data['departments']
		);
	}
}
