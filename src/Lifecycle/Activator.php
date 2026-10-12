<?php

namespace EnterpriseContentHub\Lifecycle;

use EnterpriseContentHub\Security\Capabilities;

final class Activator {

	public static function activate(): void {

		self::add_capabilities();

		flush_rewrite_rules();
	}

	private static function add_capabilities(): void {

		$administrator = get_role( 'administrator' );

		if ( ! $administrator ) {
			return;
		}

		foreach ( Capabilities::all() as $capability ) {
			$administrator->add_cap( $capability );
		}
	}
}
