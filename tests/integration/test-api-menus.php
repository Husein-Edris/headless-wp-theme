<?php
/**
 * Integration tests for GET /headless/v1/menus endpoint.
 *
 * @group integration-api
 * @package HeadlessPro\Tests\Integration
 */

class Test_HeadlessProAPIMenus extends WP_UnitTestCase {

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

	public function test_menus_returns_correct_structure_with_items(): void {
		$menu_id = wp_create_nav_menu( 'Test Menu' );
		wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-title'  => 'Home',
			'menu-item-url'    => home_url( '/' ),
			'menu-item-status' => 'publish',
		) );

		$request  = new WP_REST_Request( 'GET', '/headless/v1/menus' );
		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$this->assertEquals( 200, $response->get_status() );
		$this->assertNotEmpty( $data );

		$menu = $data[0];
		$this->assertArrayHasKey( 'id', $menu );
		$this->assertArrayHasKey( 'name', $menu );
		$this->assertArrayHasKey( 'slug', $menu );
		$this->assertArrayHasKey( 'items', $menu );
		$this->assertEquals( 'Test Menu', $menu['name'] );

		$item = $menu['items'][0];
		$this->assertArrayHasKey( 'id', $item );
		$this->assertArrayHasKey( 'title', $item );
		$this->assertArrayHasKey( 'url', $item );

		wp_delete_nav_menu( $menu_id );
	}

	public function test_menu_with_no_items_does_not_fatal(): void {
		$menu_id = wp_create_nav_menu( 'Empty Menu' );

		$request  = new WP_REST_Request( 'GET', '/headless/v1/menus' );
		$response = $this->server->dispatch( $request );

		$this->assertNotEquals( 500, $response->get_status() );

		wp_delete_nav_menu( $menu_id );
	}
}
