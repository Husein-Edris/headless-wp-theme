<?php
/**
 * Integration tests for GET /headless/v1/site-info endpoint.
 *
 * @group integration-api
 * @package HeadlessPro\Tests\Integration
 */

class Test_HeadlessProAPISiteInfo extends WP_UnitTestCase {

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

	public function test_site_info_returns_complete_response(): void {
		$request  = new WP_REST_Request( 'GET', '/headless/v1/site-info' );
		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$this->assertEquals( 200, $response->get_status() );

		$required_keys = array(
			'name', 'description', 'url', 'language', 'timezone',
			'date_format', 'time_format', 'theme', 'wordpress_version', 'api_endpoints',
		);
		foreach ( $required_keys as $key ) {
			$this->assertArrayHasKey( $key, $data, "Missing key: {$key}" );
		}

		$this->assertArrayHasKey( 'name', $data['theme'] );
		$this->assertArrayHasKey( 'version', $data['theme'] );
		$this->assertArrayHasKey( 'rest', $data['api_endpoints'] );
	}
}
