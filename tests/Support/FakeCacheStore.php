<?php

namespace EnterpriseContentHub\Tests\Support;

use EnterpriseContentHub\Contracts\CacheStore;

final class FakeCacheStore implements CacheStore {

	/**
	 * @var array<string, mixed>
	 */
	private array $values = array();

	public int $flush_count = 0;

	public function get( string $key ): array {

		if ( ! array_key_exists( $key, $this->values ) ) {
			return array(
				'hit'   => false,
				'value' => null,
			);
		}

		return array(
			'hit'   => true,
			'value' => $this->values[ $key ],
		);
	}

	public function set(
		string $key,
		mixed $value,
		int $ttl
	): void {

		unset( $ttl );

		$this->values[ $key ] = $value;
	}

	public function flush(): void {

		++$this->flush_count;

		$this->values = array();
	}
}
