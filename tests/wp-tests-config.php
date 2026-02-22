<?php
/**
 * WordPress test suite configuration for Headless Pro theme.
 *
 * IMPORTANT: The test database is DROP'd and recreated on every run.
 * NEVER point this at your development database.
 *
 * @package HeadlessPro\Tests
 */

// Path to the WordPress codebase (Local Sites root -> app/public/).
define( 'ABSPATH', dirname( __DIR__, 4 ) . '/' );

// Test database — MUST be separate from your dev database.
define( 'DB_NAME', getenv( 'WP_TESTS_DB_NAME' ) ?: 'wordpress_test' );
define( 'DB_USER', getenv( 'WP_TESTS_DB_USER' ) ?: 'root' );
define( 'DB_PASSWORD', getenv( 'WP_TESTS_DB_PASSWORD' ) ?: 'root' );

// Local by Flywheel: use the MySQL socket for reliable connection.
define( 'DB_HOST', getenv( 'WP_TESTS_DB_HOST' ) ?: 'localhost:/Users/edrishusein/Library/Application Support/Local/run/Zm_Zp39YK/mysql/mysqld.sock' );

define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

// WordPress test configuration.
define( 'WP_TESTS_DOMAIN', 'blueprint.local' );
define( 'WP_TESTS_EMAIL', 'admin@example.com' );
define( 'WP_TESTS_TITLE', 'Headless Pro Test Site' );
define( 'WP_TESTS_NETWORK_TITLE', 'Test Network' );
define( 'WP_TESTS_SUBDOMAIN_INSTALL', false );

$table_prefix = 'wptests_';

// PHP binary path (required by WP test suite).
define( 'WP_PHP_BINARY', '/Applications/Local.app/Contents/Resources/extraResources/lightning-services/php-8.2.27+1/bin/darwin/bin/php' );

// PHPUnit polyfills path (required since WP 5.8.2).
define(
	'WP_TESTS_PHPUNIT_POLYFILLS_PATH',
	dirname( __DIR__ ) . '/vendor/yoast/phpunit-polyfills'
);
