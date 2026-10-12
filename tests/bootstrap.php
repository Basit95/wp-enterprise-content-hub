<?php

$autoload = dirname( __DIR__ ) . '/vendor/autoload.php';

if ( ! file_exists( $autoload ) ) {
	throw new RuntimeException(
		'Composer dependencies are missing. Run composer install.'
	);
}

require_once $autoload;
