<?php
/**
 * Integration tests for post field enhancements (reading_time, plain_excerpt,
 * author_avatar, views) and the acf_fields key on services.
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

	/**
	 * ACF values the get_fields() stub returns, keyed by post ID.
	 * ACF is not active in tests, and get_acf_fields() needs get_fields().
	 *
	 * @var array
	 */
	public static $acf_values = array();

	/**
	 * Define the ACF stubs and load the German translation group, which
	 * declares edris_i18n_translation_post_types() (normally on acf/init).
	 */
	private function load_acf_stubs(): void {
		if ( ! function_exists( 'get_fields' ) ) {
			function get_fields( $post_id ) {
				return Test_HeadlessProAPIFields::$acf_values[ (int) $post_id ] ?? false;
			}
		}
		// Same capture target as the stubs in test-acf-fields.php and
		// test-i18n-fields.php, whichever of the three runs first.
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			function acf_add_local_field_group( array $group ): void {
				if ( class_exists( 'Test_HeadlessProACFFields' ) ) {
					Test_HeadlessProACFFields::$captured_groups[ $group['key'] ] = $group;
				} elseif ( class_exists( 'Test_HeadlessProI18nFields' ) ) {
					Test_HeadlessProI18nFields::$captured_groups[ $group['key'] ] = $group;
				}
			}
		}
		require_once HEADLESS_THEME_PATH . '/inc/acf-fields/translation-de.php';
	}

	private function get_acf_fields_over_rest( string $rest_base, int $post_id ): array {
		$request  = new WP_REST_Request( 'GET', "/wp/v2/{$rest_base}/{$post_id}" );
		$response = $this->server->dispatch( $request );
		$this->assertSame( 200, $response->get_status() );

		$data = $response->get_data();
		$this->assertArrayHasKey( 'acf_fields', $data );

		return $data['acf_fields'];
	}

	public function test_service_acf_fields_carry_slug_de_and_german_title(): void {
		$this->load_acf_stubs();
		$service_id = self::factory()->post->create( array(
			'post_type'  => 'service',
			'post_title' => 'Website development',
			'post_name'  => 'website-development',
		) );
		self::$acf_values[ $service_id ] = array(
			'headline' => 'Website development for businesses in Vorarlberg',
			'slug_de'  => 'website-erstellen-lassen',
			'title_de' => 'Website erstellen lassen',
		);

		$fields = $this->get_acf_fields_over_rest( 'service', $service_id );

		$this->assertSame( 'website-erstellen-lassen', $fields['slug_de'] );
		$this->assertSame( 'Website erstellen lassen', $fields['title_de'] );
		$this->assertSame( '', $fields['excerpt_de'] );
		$this->assertSame( '', $fields['content_de'] );
		$this->assertSame( 'Website development for businesses in Vorarlberg', $fields['headline'] );
	}

	public function test_service_acf_fields_list_slug_de_and_twins_when_unsaved(): void {
		$this->load_acf_stubs();
		$service_id = self::factory()->post->create( array( 'post_type' => 'service' ) );

		$fields = $this->get_acf_fields_over_rest( 'service', $service_id );

		$this->assertSame( '', $fields['slug_de'] );
		$this->assertSame( '', $fields['title_de'] );
		$this->assertSame( '', $fields['excerpt_de'] );
		$this->assertSame( '', $fields['content_de'] );
	}

	public function test_slug_de_is_only_added_to_services(): void {
		$this->load_acf_stubs();
		$project_id = self::factory()->post->create( array( 'post_type' => 'project' ) );

		$fields = $this->get_acf_fields_over_rest( 'project', $project_id );

		$this->assertArrayNotHasKey( 'slug_de', $fields );
		$this->assertSame( '', $fields['title_de'] );
	}
}
