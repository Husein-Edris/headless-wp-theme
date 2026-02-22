<?php
/**
 * Integration tests for POST /headless/v1/contact and GET /headless/v1/nonce endpoints.
 *
 * @group integration-api
 * @package HeadlessPro\Tests\Integration
 */

class Test_HeadlessProAPIContact extends WP_UnitTestCase {

	private $server;

	public function set_up(): void {
		parent::set_up();

		global $wp_rest_server;
		$this->server = $wp_rest_server = new WP_REST_Server();
		do_action( 'rest_api_init' );
	}

	public function tear_down(): void {
		global $wp_rest_server;
		$wp_rest_server = null;

		parent::tear_down();
	}

	public function test_nonce_endpoint_returns_nonce(): void {
		$request  = new WP_REST_Request( 'GET', '/headless/v1/nonce' );
		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$this->assertEquals( 200, $response->get_status() );
		$this->assertArrayHasKey( 'contact_nonce', $data );
		$this->assertNotEmpty( $data['contact_nonce'] );
	}

	public function test_contact_with_valid_nonce(): void {
		add_filter( 'pre_wp_mail', '__return_true' );
		$this->setExpectedDeprecated( 'is_email' );

		$nonce   = wp_create_nonce( 'headless_contact_form' );
		$request = new WP_REST_Request( 'POST', '/headless/v1/contact' );
		$request->set_param( 'name', 'Test User' );
		$request->set_param( 'email', 'test@example.com' );
		$request->set_param( 'message', 'Hello, this is a test message.' );
		$request->set_param( 'nonce', $nonce );

		$response = $this->server->dispatch( $request );

		$this->assertNotEquals( 403, $response->get_status() );
		$this->assertNotEquals( 401, $response->get_status() );

		remove_filter( 'pre_wp_mail', '__return_true' );
	}

	public function test_contact_with_invalid_nonce_is_rejected(): void {
		$this->setExpectedDeprecated( 'is_email' );

		$request = new WP_REST_Request( 'POST', '/headless/v1/contact' );
		$request->set_param( 'name', 'Test User' );
		$request->set_param( 'email', 'test@example.com' );
		$request->set_param( 'message', 'Hello.' );
		$request->set_param( 'nonce', 'invalid_nonce_value' );

		$response = $this->server->dispatch( $request );

		$this->assertContains(
			$response->get_status(),
			array( 401, 403 ),
			'Invalid nonce should return 401 or 403.'
		);
	}
}
