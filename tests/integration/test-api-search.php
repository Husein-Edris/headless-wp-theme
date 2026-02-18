<?php
/**
 * Integration tests for GET /headless/v1/search endpoint.
 *
 * @group integration-api
 * @package HeadlessPro\Tests\Integration
 */

class Test_HeadlessProAPISearch extends WP_UnitTestCase {

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

	public function test_search_returns_matching_results_with_correct_structure(): void {
		self::factory()->post->create( array( 'post_title' => 'Unique Searchable Title' ) );

		$request = new WP_REST_Request( 'GET', '/headless/v1/search' );
		$request->set_param( 'query', 'Unique Searchable' );
		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$this->assertEquals( 200, $response->get_status() );
		$this->assertArrayHasKey( 'results', $data );
		$this->assertArrayHasKey( 'total', $data );
		$this->assertArrayHasKey( 'query', $data );
		$this->assertGreaterThan( 0, $data['total'] );

		$item = $data['results'][0];
		$this->assertArrayHasKey( 'id', $item );
		$this->assertArrayHasKey( 'title', $item );
		$this->assertArrayHasKey( 'permalink', $item );
		$this->assertArrayHasKey( 'post_type', $item );
	}

	public function test_search_no_matches_returns_empty(): void {
		$request = new WP_REST_Request( 'GET', '/headless/v1/search' );
		$request->set_param( 'query', 'xyznonexistent123' );
		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$this->assertEquals( 0, $data['total'] );
		$this->assertEmpty( $data['results'] );
	}

	public function test_search_limit_parameter(): void {
		for ( $i = 0; $i < 5; $i++ ) {
			self::factory()->post->create( array( 'post_title' => "Limited Post {$i}" ) );
		}

		$request = new WP_REST_Request( 'GET', '/headless/v1/search' );
		$request->set_param( 'query', 'Limited Post' );
		$request->set_param( 'limit', 2 );
		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$this->assertLessThanOrEqual( 2, count( $data['results'] ) );
	}
}
