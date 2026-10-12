<?php

namespace EnterpriseContentHub\Lifecycle;

final class Deactivator {

	public static function deactivate(): void {

		flush_rewrite_rules();
	}
}
