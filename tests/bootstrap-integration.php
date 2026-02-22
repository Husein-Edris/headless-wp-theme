<?php
/**
 * PHPUnit bootstrap for integration tests.
 *
 * Boots a full WordPress test environment against a test database.
 *
 * @package HeadlessPro\Tests
 */

// Composer autoloader (loads phpunit-polyfills, wp-phpunit, etc.).
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// Resolve the WordPress test library directory.
// wp-phpunit/wp-phpunit sets WP_PHPUNIT__DIR automatically via its autoload file.
$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = getenv( 'WP_PHPUNIT__DIR' );
}

if ( ! $_tests_dir ) {
	// Fallback to vendor path (wp-phpunit package location).
	$_tests_dir = dirname( __DIR__ ) . '/vendor/wp-phpunit/wp-phpunit';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find WordPress test library at: {$_tests_dir}" . PHP_EOL;
	echo 'Make sure WP_PHPUNIT__TESTS_CONFIG is set in phpunit.xml.dist and points to a valid wp-tests-config.php.' . PHP_EOL;
	exit( 1 );
}

// Load WP test functions (needed before bootstrap for filters).
require_once $_tests_dir . '/includes/functions.php';

/**
 * Activate the Headless Pro theme before WordPress sets up.
 */
function _headless_pro_manually_load_theme() {
	add_filter( 'stylesheet', function () {
		return 'headless-wp-theme';
	} );
	add_filter( 'template', function () {
		return 'headless-wp-theme';
	} );
}
tests_add_filter( 'setup_theme', '_headless_pro_manually_load_theme' );

// Boot the WordPress test environment.
require $_tests_dir . '/includes/bootstrap.php';
