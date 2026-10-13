<?php
/**
 * Plugin Name: WP Enterprise Content Hub
 * Plugin URI:  https://github.com/basit95/wp-enterprise-content-hub
 * Description: Production-oriented WordPress plugin demonstrating enterprise architecture, Gutenberg, REST APIs, testing, CI/CD, and modern WordPress engineering practices.
 * Version:     1.1.0
 * Author:      Basit Ahmed
 * License:     GPL-2.0-or-later
 * Text Domain: wp-enterprise-content-hub
 */

defined( 'ABSPATH' ) || exit;

define( 'ECH_VERSION', '1.1.0' );
define( 'ECH_FILE', __FILE__ );
define( 'ECH_PATH', plugin_dir_path( __FILE__ ) );
define( 'ECH_URL', plugin_dir_url( __FILE__ ) );

$autoload = ECH_PATH . 'vendor/autoload.php';

if ( ! file_exists( $autoload ) ) {
	return;
}

require_once $autoload;

use EnterpriseContentHub\Lifecycle\Activator;
use EnterpriseContentHub\Lifecycle\Deactivator;
use EnterpriseContentHub\Plugin;

register_activation_hook(
	__FILE__,
	array( Activator::class, 'activate' )
);

register_deactivation_hook(
	__FILE__,
	array( Deactivator::class, 'deactivate' )
);

add_action(
	'plugins_loaded',
	static function (): void {
		$plugin = new Plugin();
		$plugin->boot();
	}
);
