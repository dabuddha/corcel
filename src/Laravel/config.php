<?php

return [

	/**
	 * The WordPress database configuration
	 */
	'database' => [
		'driver' => 'mysql',
		'host' => env('WP_DB_HOST', 'localhost'),
		'port' => env('WP_DB_PORT', '3306'),
		'database' => env('WP_DB_DATABASE', 'wordpress'),
		'username' => env('WP_DB_USERNAME', 'root'),
		'password' => env('WP_DB_PASSWORD', ''),
		'charset' => 'utf8',
		'collation' => 'utf8_unicode_ci',
		'prefix' => 'wp_',
		'strict' => false,
		'engine' => null,
	],

	/**
	 * If you want to register custom shortcodes use this config array to map
	 * each shortcode with a custom class
	 */
	'shortcodes' => [
//		'example' => Path\To\Something::class,
	],

];