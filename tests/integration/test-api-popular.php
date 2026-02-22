<?php
/**
 * Integration tests for GET /headless/v1/posts/popular endpoint.
 *
 * @group integration-api
 * @package HeadlessPro\Tests\Integration
 */

class Test_HeadlessProAPIPopular extends WP_UnitTestCase {

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

	public function test_popular_ordered_by_views_with_views_field(): void {
		$low  = self::factory()->post->create( array( 'post_title' => 'Low Views' ) );
		$high = self::factory()->post->create( array( 'post_title' => 'High Views' ) );

		update_post_meta( $low, 'post_views_count', 10 );
		update_post_meta( $high, 'post_views_count', 100 );

		$request  = new WP_REST_Request( 'GET', '/headless/v1/posts/popular' );
		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$this->assertEquals( 200, $response->get_status() );
		$this->assertNotEmpty( $data );
		$this->assertArrayHasKey( 'views', $data[0] );
		$this->assertGreaterThanOrEqual( $data[1]['views'], $data[0]['views'] );
	}

	public function test_popular_limit_parameter(): void {
		for ( $i = 0; $i < 5; $i++ ) {
			$pid = self::factory()->post->create();
			update_post_meta( $pid, 'post_views_count', $i + 1 );
		}

		$request = new WP_REST_Request( 'GET', '/headless/v1/posts/popular' );
		$request->set_param( 'limit', 2 );
		$response = $this->server->dispatch( $request );

		$this->assertLessThanOrEqual( 2, count( $response->get_data() ) );
	}

	public function test_popular_excludes_posts_without_views(): void {
		$with_views    = self::factory()->post->create( array( 'post_title' => 'Has Views' ) );
		$without_views = self::factory()->post->create( array( 'post_title' => 'No Views' ) );

		update_post_meta( $with_views, 'post_views_count', 5 );

		$request  = new WP_REST_Request( 'GET', '/headless/v1/posts/popular' );
		$response = $this->server->dispatch( $request );
		$ids      = wp_list_pluck( $response->get_data(), 'id' );

		$this->assertContains( $with_views, $ids );
		$this->assertNotContains( $without_views, $ids );
	}
}
