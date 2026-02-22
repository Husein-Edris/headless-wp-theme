<?php
/**
 * Integration tests for frontend redirect logic.
 *
 * Strategy: Intercept wp_redirect via filter to capture location/status,
 * throw a custom exception to prevent exit(). Passthrough URIs return
 * before reaching wp_redirect, so no exception is thrown.
 *
 * @group integration-redirect
 * @package HeadlessPro\Tests\Integration
 */

class HeadlessPro_Redirect_Exception extends Exception {
	public string $location;
	public int $redirect_status;

	public function __construct( string $location, int $status ) {
		$this->location        = $location;
		$this->redirect_status = $status;
		parent::__construct( "Redirect to: {$location} ({$status})" );
	}
}

class Test_HeadlessProRedirect extends WP_UnitTestCase {

	private $server_backup;

	public function set_up(): void {
		parent::set_up();
		$this->server_backup = $_SERVER;

		add_filter( 'wp_redirect', function ( $location, $status ) {
			throw new HeadlessPro_Redirect_Exception( $location, $status );
		}, 10, 2 );
	}

	public function tear_down(): void {
		$_SERVER = $this->server_backup;
		remove_all_filters( 'wp_redirect' );
		parent::tear_down();
	}

	/**
	 * @dataProvider passthrough_uri_provider
	 */
	public function test_passthrough_uris_do_not_redirect( string $uri ): void {
		$_SERVER['REQUEST_URI'] = $uri;

		$redirected = false;
		try {
			redirect_frontend_to_main_site();
		} catch ( HeadlessPro_Redirect_Exception $e ) {
			$redirected = true;
		}

		$this->assertFalse( $redirected, "URI '{$uri}' should NOT trigger a redirect." );
	}

	public function passthrough_uri_provider(): array {
		return array(
			'REST API'    => array( '/wp-json/wp/v2/posts' ),
			'GraphQL'     => array( '/graphql' ),
			'Admin area'  => array( '/wp-admin/edit.php' ),
			'Login page'  => array( '/wp-login.php' ),
			'WP content'  => array( '/wp-content/uploads/image.jpg' ),
			'WP includes' => array( '/wp-includes/js/jquery.js' ),
			'Well-known'  => array( '/.well-known/acme-challenge/test' ),
			'RSS feed'    => array( '/feed/' ),
			'Sitemap'     => array( '/sitemap.xml' ),
		);
	}

	public function test_frontend_page_triggers_301_to_frontend_url(): void {
		$_SERVER['REQUEST_URI'] = '/about-me/';

		try {
			redirect_frontend_to_main_site();
			$this->fail( 'Expected a redirect but none occurred.' );
		} catch ( HeadlessPro_Redirect_Exception $e ) {
			$this->assertEquals( HeadlessProConfig::get_frontend_url(), $e->location );
			$this->assertEquals( 301, $e->redirect_status );
		}
	}

	public function test_root_url_triggers_redirect(): void {
		$_SERVER['REQUEST_URI'] = '/';

		try {
			redirect_frontend_to_main_site();
			$this->fail( 'Expected a redirect but none occurred.' );
		} catch ( HeadlessPro_Redirect_Exception $e ) {
			$this->assertEquals( 301, $e->redirect_status );
		}
	}
}
