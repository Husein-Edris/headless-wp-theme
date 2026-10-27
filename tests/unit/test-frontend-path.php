<?php
/**
 * Tests for headless_pro_frontend_path().
 *
 * @group unit
 * @package HeadlessPro\Tests\Unit
 */

class Test_HeadlessProFrontendPath extends WP_UnitTestCase {

	/**
	 * @dataProvider mapping_provider
	 */
	public function test_maps_cms_object_to_frontend_route( string $path, array $object, string $expected ): void {
		$this->assertSame( $expected, headless_pro_frontend_path( $path, $object ) );
	}

	public function mapping_provider(): array {
		return array(
			'post lives under /notebook'    => array( '/website-erstellen-lassen/', array( 'kind' => 'singular', 'post_type' => 'post', 'slug' => 'website-erstellen-lassen' ), '/notebook/website-erstellen-lassen' ),
			'project keeps its slug'        => array( '/projects/beschutzerbox/', array( 'kind' => 'singular', 'post_type' => 'project', 'slug' => 'beschutzerbox' ), '/projects/beschutzerbox' ),
			'about-me page is /about'       => array( '/about-me/', array( 'kind' => 'singular', 'post_type' => 'page', 'slug' => 'about-me' ), '/about' ),
			'home page is the root'         => array( '/home/', array( 'kind' => 'singular', 'post_type' => 'page', 'slug' => 'home' ), '/' ),
			'other page keeps its path'     => array( '/imprint/', array( 'kind' => 'singular', 'post_type' => 'page', 'slug' => 'imprint' ), '/imprint/' ),
			'book goes to the bookshelf'    => array( '/book/dont-make-me-think/', array( 'kind' => 'singular', 'post_type' => 'book', 'slug' => 'dont-make-me-think' ), '/bookshelf' ),
			'tech goes to the tech stack'   => array( '/technologies/php/', array( 'kind' => 'singular', 'post_type' => 'tech', 'slug' => 'php' ), '/tech-stack' ),
			'skill goes to about'           => array( '/skills/initiative/', array( 'kind' => 'singular', 'post_type' => 'skill', 'slug' => 'initiative' ), '/about' ),
			'hobby goes to about'           => array( '/hobbies/football/', array( 'kind' => 'singular', 'post_type' => 'hobby', 'slug' => 'football' ), '/about' ),
			'project archive'               => array( '/projects/', array( 'kind' => 'archive', 'post_type' => 'project' ), '/projects' ),
			'tech archive'                  => array( '/technologies/', array( 'kind' => 'archive', 'post_type' => 'tech' ), '/tech-stack' ),
			'skill archive'                 => array( '/skills/', array( 'kind' => 'archive', 'post_type' => 'skill' ), '/about' ),
			'category goes to the notebook' => array( '/category/blog/', array( 'kind' => 'term', 'taxonomy' => 'category' ), '/notebook' ),
			'tech category to tech stack'   => array( '/tech-category/cms/', array( 'kind' => 'term', 'taxonomy' => 'tech_category' ), '/tech-stack' ),
			'unknown path is kept'          => array( '/test-post', array( 'kind' => 'other' ), '/test-post' ),
			'unknown path keeps its query'  => array( '/?s=wordpress', array( 'kind' => 'other' ), '/?s=wordpress' ),
			'mapped post keeps its query'   => array( '/website-erstellen-lassen/?utm_source=x&utm_medium=y', array( 'kind' => 'singular', 'post_type' => 'post', 'slug' => 'website-erstellen-lassen' ), '/notebook/website-erstellen-lassen?utm_source=x&utm_medium=y' ),
			'mapped page keeps its query'   => array( '/about-me/?utm_source=x', array( 'kind' => 'singular', 'post_type' => 'page', 'slug' => 'about-me' ), '/about?utm_source=x' ),
			'mapped term keeps its query'   => array( '/category/blog/?utm_source=x', array( 'kind' => 'term', 'taxonomy' => 'category' ), '/notebook?utm_source=x' ),
		);
	}

	/**
	 * @dataProvider sitemap_provider
	 */
	public function test_sitemap_paths_go_to_the_frontend_sitemap( string $path ): void {
		$this->assertSame( '/sitemap.xml', headless_pro_frontend_path( $path, array( 'kind' => 'other' ) ) );
	}

	public function sitemap_provider(): array {
		return array(
			'RankMath index'     => array( '/sitemap_index.xml' ),
			'RankMath child'     => array( '/post-sitemap.xml' ),
			'WP core sitemap'    => array( '/wp-sitemap.xml' ),
			'plain sitemap.xml'  => array( '/sitemap.xml' ),
		);
	}
}
