<?php
/**
 * Integration tests for ACF field group registration and configuration.
 *
 * Tests the slug-based location matcher (runtime) and validates
 * ACF field definition arrays by capturing acf_add_local_field_group calls.
 *
 * @group integration-acf
 * @package HeadlessPro\Tests\Integration
 */

class Test_HeadlessProACFFields extends WP_UnitTestCase {

	/**
	 * Captured field groups from acf_add_local_field_group calls.
	 * Public so the stub function can write to it.
	 *
	 * @var array
	 */
	public static $captured_groups = array();

	/**
	 * Whether field groups have been captured yet.
	 *
	 * @var bool
	 */
	private static $groups_loaded = false;

	/**
	 * Load and capture ACF field group definitions.
	 *
	 * Since ACF plugin is not active in tests, we define a stub
	 * acf_add_local_field_group() that captures the array, then
	 * require each field definition file.
	 */
	private static function load_field_groups(): void {
		if ( self::$groups_loaded ) {
			return;
		}

		// Define stub if ACF not available.
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			function acf_add_local_field_group( array $group ): void {
				Test_HeadlessProACFFields::$captured_groups[ $group['key'] ] = $group;
			}
		}

		$field_files = array(
			'about-page',
			'homepage',
			'skills',
			'hobbies',
			'project-case-study',
			'blog-post',
		);

		foreach ( $field_files as $file ) {
			$path = HEADLESS_THEME_PATH . '/inc/acf-fields/' . $file . '.php';
			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}

		self::$groups_loaded = true;
	}

	/**
	 * Get a captured field group by key.
	 */
	private function get_group( string $key ): ?array {
		self::load_field_groups();
		return self::$captured_groups[ $key ] ?? null;
	}

	/**
	 * Find a field by key in a fields array (recursive for sub_fields).
	 */
	private function find_field( array $fields, string $key ): ?array {
		foreach ( $fields as $field ) {
			if ( isset( $field['key'] ) && $field['key'] === $key ) {
				return $field;
			}
			if ( ! empty( $field['sub_fields'] ) ) {
				$found = $this->find_field( $field['sub_fields'], $key );
				if ( $found ) {
					return $found;
				}
			}
		}
		return null;
	}

	// =========================================================================
	// Phase 2: Slug-based location rule matcher
	// =========================================================================

	/**
	 * T002: Slug matcher matches page with correct slug.
	 */
	public function test_slug_matcher_matches_correct_slug(): void {
		$page_id = self::factory()->post->create( array(
			'post_type'  => 'page',
			'post_name'  => 'about-me',
			'post_title' => 'About Me',
			'post_status' => 'publish',
		) );

		$acf_fields = new HeadlessProACFFields();

		$rule = array(
			'param'    => 'page',
			'operator' => '==',
			'value'    => 'slug:about-me',
		);
		$screen = array( 'post_id' => $page_id );

		$result = $acf_fields->match_page_by_slug( false, $rule, $screen, array() );
		$this->assertTrue( $result, 'Slug matcher should match page with slug about-me.' );
	}

	/**
	 * T002: Slug matcher supports ACF-style post_### screen IDs.
	 */
	public function test_slug_matcher_supports_post_id_prefix(): void {
		$page_id = self::factory()->post->create( array(
			'post_type'   => 'page',
			'post_name'   => 'about-me',
			'post_title'  => 'About Me',
			'post_status' => 'publish',
		) );

		$acf_fields = new HeadlessProACFFields();

		$rule = array(
			'param'    => 'page',
			'operator' => '==',
			'value'    => 'slug:about-me',
		);
		$screen = array( 'post_id' => 'post_' . $page_id );

		$result = $acf_fields->match_page_by_slug( false, $rule, $screen, array() );
		$this->assertTrue( $result, 'Slug matcher should match post_### screen IDs.' );
	}

	/**
	 * T002: Slug matcher returns false for different slug.
	 */
	public function test_slug_matcher_rejects_wrong_slug(): void {
		$page_id = self::factory()->post->create( array(
			'post_type'  => 'page',
			'post_name'  => 'contact',
			'post_title' => 'Contact',
			'post_status' => 'publish',
		) );

		$acf_fields = new HeadlessProACFFields();

		$rule = array(
			'param'    => 'page',
			'operator' => '==',
			'value'    => 'slug:about-me',
		);
		$screen = array( 'post_id' => $page_id );

		$result = $acf_fields->match_page_by_slug( false, $rule, $screen, array() );
		$this->assertFalse( $result, 'Slug matcher should reject page with different slug.' );
	}

	/**
	 * T002: Slug matcher returns false when no post_id in screen.
	 */
	public function test_slug_matcher_returns_false_without_post_id(): void {
		$acf_fields = new HeadlessProACFFields();

		$rule = array(
			'param'    => 'page',
			'operator' => '==',
			'value'    => 'slug:about-me',
		);
		$screen = array();

		$result = $acf_fields->match_page_by_slug( false, $rule, $screen, array() );
		$this->assertFalse( $result, 'Slug matcher should return false without post_id.' );
	}

	/**
	 * T002: Non-slug rules pass through unchanged.
	 */
	public function test_slug_matcher_passes_through_non_slug_rules(): void {
		$acf_fields = new HeadlessProACFFields();

		$rule = array(
			'param'    => 'page',
			'operator' => '==',
			'value'    => '42',
		);
		$screen = array( 'post_id' => 42 );

		$result = $acf_fields->match_page_by_slug( true, $rule, $screen, array() );
		$this->assertTrue( $result, 'Non-slug rule with true match should pass through as true.' );

		$result = $acf_fields->match_page_by_slug( false, $rule, $screen, array() );
		$this->assertFalse( $result, 'Non-slug rule with false match should pass through as false.' );
	}

	/**
	 * T002: Slug matcher handles != operator.
	 */
	public function test_slug_matcher_handles_not_equal_operator(): void {
		$page_id = self::factory()->post->create( array(
			'post_type'  => 'page',
			'post_name'  => 'about-me',
			'post_title' => 'About Me',
			'post_status' => 'publish',
		) );

		$acf_fields = new HeadlessProACFFields();

		$rule = array(
			'param'    => 'page',
			'operator' => '!=',
			'value'    => 'slug:about-me',
		);
		$screen = array( 'post_id' => $page_id );

		$result = $acf_fields->match_page_by_slug( false, $rule, $screen, array() );
		$this->assertFalse( $result, 'Not-equal with matching slug should return false.' );
	}

	// =========================================================================
	// Phase 3 (US1): About page repeater optimization
	// =========================================================================

	/**
	 * T004: Technologies sub-field has return_format => id.
	 */
	public function test_technologies_field_returns_id_format(): void {
		$group = $this->get_group( 'group_64a1b2c3d4e63' );
		$this->assertNotNull( $group, 'About page field group should be captured.' );

		$field = $this->find_field( $group['fields'], 'field_64a1b2c3d4e75' );
		$this->assertNotNull( $field, 'Technologies field should exist.' );
		$this->assertEquals( 'id', $field['return_format'], 'Technologies return_format should be id.' );
	}

	/**
	 * T005: Description WYSIWYG sub-field has delay => 1.
	 */
	public function test_description_wysiwyg_has_delay(): void {
		$group = $this->get_group( 'group_64a1b2c3d4e63' );
		$this->assertNotNull( $group, 'About page field group should be captured.' );

		$field = $this->find_field( $group['fields'], 'field_64a1b2c3d4e74' );
		$this->assertNotNull( $field, 'Description field should exist.' );
		$this->assertEquals( 1, $field['delay'], 'Description WYSIWYG delay should be 1.' );
	}

	/**
	 * T006: Experience repeater has rows_per_page => 10.
	 */
	public function test_experience_repeater_rows_per_page(): void {
		$group = $this->get_group( 'group_64a1b2c3d4e63' );
		$this->assertNotNull( $group, 'About page field group should be captured.' );

		$field = $this->find_field( $group['fields'], 'field_64a1b2c3d4e70' );
		$this->assertNotNull( $field, 'Experience repeater field should exist.' );
		$this->assertEquals( 10, $field['rows_per_page'], 'Experience repeater rows_per_page should be 10.' );
	}

	// =========================================================================
	// Phase 4 (US2): Location rule uses slug
	// =========================================================================

	/**
	 * T011: About page field group location rule uses slug:about-me.
	 */
	public function test_about_page_location_uses_slug(): void {
		$group = $this->get_group( 'group_64a1b2c3d4e63' );
		$this->assertNotNull( $group, 'About page field group should be captured.' );
		$this->assertNotEmpty( $group['location'], 'Location rules should not be empty.' );

		$first_rule = $group['location'][0][0];
		$this->assertEquals( 'page', $first_rule['param'], 'Location param should be page.' );
		$this->assertEquals( '==', $first_rule['operator'], 'Location operator should be ==.' );
		$this->assertEquals( 'slug:about-me', $first_rule['value'], 'Location value should be slug:about-me.' );
	}

	// =========================================================================
	// Phase 5 (US3): Homepage tabs
	// =========================================================================

	/**
	 * T014: Homepage field group has 14 top-level fields (7 tabs + 7 groups).
	 */
	public function test_homepage_has_tabs_and_groups(): void {
		$group = $this->get_group( 'group_homepage_sections' );
		$this->assertNotNull( $group, 'Homepage field group should be captured.' );

		$fields = $group['fields'];
		$this->assertCount( 14, $fields, 'Homepage should have 14 top-level fields (7 tabs + 7 groups).' );

		$tabs = array_filter( $fields, function( $f ) {
			return $f['type'] === 'tab';
		} );
		$groups = array_filter( $fields, function( $f ) {
			return $f['type'] === 'group';
		} );

		$this->assertCount( 7, $tabs, 'Homepage should have 7 tab fields.' );
		$this->assertCount( 7, $groups, 'Homepage should have 7 group fields.' );

		foreach ( $tabs as $tab ) {
			$this->assertEquals( 'top', $tab['placement'], "Tab '{$tab['label']}' should have placement top." );
		}
	}

	/**
	 * T014: Homepage tabs appear in correct order before their groups.
	 */
	public function test_homepage_tabs_in_correct_order(): void {
		$group = $this->get_group( 'group_homepage_sections' );
		$this->assertNotNull( $group, 'Homepage field group should be captured.' );

		$expected_labels = array(
			'Hero', 'Projects', 'About Me', 'Bookshelf', 'Tech Stack', 'Notebook', 'Contact',
		);

		$tab_labels = array();
		foreach ( $group['fields'] as $field ) {
			if ( $field['type'] === 'tab' ) {
				$tab_labels[] = $field['label'];
			}
		}

		$this->assertEquals( $expected_labels, $tab_labels, 'Tabs should appear in the correct order.' );
	}

	// =========================================================================
	// Phase 6 (US4): Company name labels on collapsed rows
	// =========================================================================

	/**
	 * T017: Experience repeater collapsed setting points to company_name.
	 */
	public function test_experience_repeater_collapsed_label(): void {
		$group = $this->get_group( 'group_64a1b2c3d4e63' );
		$this->assertNotNull( $group, 'About page field group should be captured.' );

		$field = $this->find_field( $group['fields'], 'field_64a1b2c3d4e70' );
		$this->assertNotNull( $field, 'Experience repeater field should exist.' );
		$this->assertEquals(
			'field_64a1b2c3d4e71',
			$field['collapsed'],
			'Experience repeater collapsed should point to company_name field key.'
		);
	}
}
