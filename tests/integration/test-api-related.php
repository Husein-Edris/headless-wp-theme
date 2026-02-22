<?php
/**
 * Integration tests for GET /headless/v1/posts/{id}/related endpoint.
 *
 * @group integration-api
 * @package HeadlessPro\Tests\Integration
 */

class Test_HeadlessProAPIRelated extends WP_UnitTestCase {

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

	public function test_related_excludes_source_post(): void {
		$cat_id = self::factory()->category->create( array( 'name' => 'Shared Cat' ) );
		$source = self::factory()->post->create( array(
			'post_title'    => 'Source',
			'post_category' => array( $cat_id ),
		) );
		self::factory()->post->create( array(
			'post_title'    => 'Related 1',
			'post_category' => array( $cat_id ),
		) );

		$request  = new WP_REST_Request( 'GET', "/headless/v1/posts/{$source}/related" );
		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$this->assertEquals( 200, $response->get_status() );
		$this->assertIsArray( $data );
		$ids = wp_list_pluck( $data, 'id' );
		$this->assertNotContains( $source, $ids );
	}

	public function test_related_with_no_shared_categories(): void {
		$post_id = self::factory()->post->create( array( 'post_title' => 'Isolated Post' ) );
		wp_set_post_categories( $post_id, array() );

		$request  = new WP_REST_Request( 'GET', "/headless/v1/posts/{$post_id}/related" );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );
		$this->assertIsArray( $response->get_data() );
	}
}
