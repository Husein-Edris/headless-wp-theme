<?php
/**
 * Integration tests for custom post type registration.
 *
 * @group integration-cpt
 * @package HeadlessPro\Tests\Integration
 */

class Test_HeadlessProPostTypes extends WP_UnitTestCase {

	private $cpts = array(
		'skill'   => array( 'single' => 'skill', 'plural' => 'skills', 'slug' => 'skills' ),
		'hobby'   => array( 'single' => 'hobby', 'plural' => 'hobbies', 'slug' => 'hobbies' ),
		'project' => array( 'single' => 'project', 'plural' => 'projects', 'slug' => 'projects' ),
		'tech'    => array( 'single' => 'tech', 'plural' => 'techs', 'slug' => 'technologies' ),
	);

	public function test_all_cpts_registered_with_rest_and_graphql(): void {
		foreach ( $this->cpts as $post_type => $meta ) {
			$this->assertTrue( post_type_exists( $post_type ), "{$post_type} should exist." );

			$obj = get_post_type_object( $post_type );
			$this->assertTrue( $obj->public, "{$post_type} should be public." );
			$this->assertTrue( $obj->show_in_rest, "{$post_type} should show in REST." );
			$this->assertTrue( $obj->show_in_graphql, "{$post_type} should show in GraphQL." );
			$this->assertEquals( $meta['single'], $obj->graphql_single_name );
			$this->assertEquals( $meta['plural'], $obj->graphql_plural_name );
			$this->assertEquals( $meta['slug'], $obj->rewrite['slug'] );
		}
	}

	// WPGraphQL is deactivated since 2026-06-22, so service is checked on its own
	// instead of joining the GraphQL-asserting $cpts list.
	public function test_service_cpt_is_public_in_rest_under_services(): void {
		$this->assertTrue( post_type_exists( 'service' ), 'service should exist.' );

		$obj = get_post_type_object( 'service' );
		$this->assertTrue( $obj->public, 'service should be public.' );
		$this->assertTrue( $obj->show_in_rest, 'service should show in REST.' );
		$this->assertSame( '/wp/v2/service', rest_get_route_for_post_type_items( 'service' ) );
		$this->assertEmpty( $obj->show_in_graphql, 'service should stay out of GraphQL.' );
		$this->assertTrue( $obj->has_archive );
		$this->assertSame( 'services', $obj->rewrite['slug'] );
		$this->assertSame( 'dashicons-hammer', $obj->menu_icon );
		$this->assertSame( 'Services', $obj->labels->name );
		$this->assertSame( 'Service', $obj->labels->singular_name );
		$this->assertSame( 'Add New Service', $obj->labels->add_new_item );
		$this->assertSame( 'All Services', $obj->labels->all_items );
	}

	public function test_service_cpt_supports_plan_features(): void {
		$expected = array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions' );

		foreach ( $expected as $feature ) {
			$this->assertTrue( post_type_supports( 'service', $feature ), "service should support '{$feature}'." );
		}
	}

	public function test_all_cpts_support_required_features(): void {
		$expected = array( 'title', 'editor', 'thumbnail', 'excerpt' );

		foreach ( array_keys( $this->cpts ) as $post_type ) {
			foreach ( $expected as $feature ) {
				$this->assertTrue(
					post_type_supports( $post_type, $feature ),
					"{$post_type} should support '{$feature}'."
				);
			}
		}
	}
}
