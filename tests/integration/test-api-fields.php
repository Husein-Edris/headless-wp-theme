<?php
/**
 * Integration tests for post field enhancements (reading_time, plain_excerpt,
 * author_avatar, views).
 *
 * @group integration-api
 * @package HeadlessPro\Tests\Integration
 */

class Test_HeadlessProAPIFields extends WP_UnitTestCase {

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

	public function test_post_has_reading_time_calculated_from_content(): void {
		$content = str_repeat( 'word ', 400 );
		$post_id = self::factory()->post->create( array(
			'post_title'   => 'Reading Time Test',
			'post_content' => $content,
		) );

		$request  = new WP_REST_Request( 'GET', "/wp/v2/posts/{$post_id}" );
		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$this->assertArrayHasKey( 'reading_time', $data );
		$this->assertMatchesRegularExpression( '/\d+ min read/', $data['reading_time'] );
	}

	public function test_post_has_plain_excerpt_and_author_avatar(): void {
		$post_id = self::factory()->post->create( array(
			'post_title'   => 'Fields Test',
			'post_content' => '<p>This is <strong>bold</strong> content.</p>',
		) );

		$request  = new WP_REST_Request( 'GET', "/wp/v2/posts/{$post_id}" );
		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$this->assertArrayHasKey( 'plain_excerpt', $data );
		$this->assertIsString( $data['plain_excerpt'] );
		$this->assertArrayHasKey( 'author_avatar', $data );
		$this->assertIsString( $data['author_avatar'] );
	}

	public function test_post_views_field_reflects_meta(): void {
		$post_id = self::factory()->post->create( array( 'post_title' => 'Viewed Post' ) );
		update_post_meta( $post_id, 'post_views_count', 42 );

		$request  = new WP_REST_Request( 'GET', "/wp/v2/posts/{$post_id}" );
		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$this->assertArrayHasKey( 'views', $data );
		$this->assertEquals( 42, $data['views'] );
	}
}
