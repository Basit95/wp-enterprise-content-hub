<?php

namespace EnterpriseContentHub\Infrastructure;

use EnterpriseContentHub\Contracts\CacheStore;

final class WordPressTransientCache implements CacheStore {

	private const VERSION_OPTION = 'ech_cache_version';

	private const PREFIX = 'ech_cache_';

	public function get( string $key ): array {

		$cached = get_transient(
			$this->get_storage_key( $key )
		);

		if (
			false === $cached ||
			! is_array( $cached ) ||
			! array_key_exists( 'value', $cached )
		) {
			return array(
				'hit'   => false,
				'value' => null,
			);
		}

		return array(
			'hit'   => true,
			'value' => $cached['value'],
		);
	}

	public function set(
		string $key,
		mixed $value,
		int $ttl
	): void {

		set_transient(
			$this->get_storage_key( $key ),
			array(
				'value' => $value,
			),
			max( 1, $ttl )
		);
	}

	public function flush(): void {

		update_option(
			self::VERSION_OPTION,
			$this->get_version() + 1,
			false
		);
	}

	private function get_storage_key( string $key ): string {

		return self::PREFIX
			. $this->get_version()
			. '_'
			. md5( $key );
	}

	private function get_version(): int {

		return max(
			1,
			absint(
				get_option(
					self::VERSION_OPTION,
					1
				)
			)
		);
	}
}
