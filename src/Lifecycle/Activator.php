<?php

namespace EnterpriseContentHub\Lifecycle;

use EnterpriseContentHub\Content\ResourcePostType;
use EnterpriseContentHub\Content\ResourceTaxonomy;
use EnterpriseContentHub\Security\Capabilities;

final class Activator {

	public static function activate(): void {

		self::add_capabilities();

		$resource_post_type = new ResourcePostType();
		$resource_taxonomy  = new ResourceTaxonomy();

		$resource_post_type->register_post_type();
		$resource_taxonomy->register_taxonomy();

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
