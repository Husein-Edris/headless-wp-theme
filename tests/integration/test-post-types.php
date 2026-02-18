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
