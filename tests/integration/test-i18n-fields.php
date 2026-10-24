<?php
/**
 * Tests for the German twin cloner (inc/acf-fields/i18n.php) and the field
 * groups that use it.
 *
 * ACF is not active in tests, so group files are captured through the
 * acf_add_local_field_group() stub of Test_HeadlessProACFFields when it is
 * defined, or through a local stub otherwise.
 *
 * @group integration-acf
 * @package HeadlessPro\Tests\Integration
 */

class Test_HeadlessProI18nFields extends WP_UnitTestCase {

	/**
	 * Captured field groups, keyed by group key.
	 *
	 * @var array
	 */
	public static $captured_groups = array();

	public static function set_up_before_class(): void {
		parent::set_up_before_class();
		require_once HEADLESS_THEME_PATH . '/inc/acf-fields/i18n.php';
	}

	/**
	 * Run a group file and return the group it registers.
	 */
	private function capture_group_file( string $file, string $key ): array {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			function acf_add_local_field_group( array $group ): void {
				Test_HeadlessProI18nFields::$captured_groups[ $group['key'] ] = $group;
			}
		}

		include HEADLESS_THEME_PATH . '/inc/acf-fields/' . $file . '.php';

		$groups = array_merge(
			class_exists( 'Test_HeadlessProACFFields' ) ? Test_HeadlessProACFFields::$captured_groups : array(),
			self::$captured_groups
		);
		$this->assertArrayHasKey( $key, $groups, "$file.php should register $key." );

		return $groups[ $key ];
	}

	private function names( array $fields ): array {
		return array_values( array_filter( array_column( $fields, 'name' ) ) );
	}

	private function find_by_name( array $fields, string $name ): ?array {
		foreach ( $fields as $field ) {
			if ( ( $field['name'] ?? '' ) === $name ) {
				return $field;
			}
		}
		return null;
	}

	public function test_clones_text_textarea_wysiwyg_right_after_source(): void {
		$group = edris_with_de_fields( array(
			'key'    => 'group_test',
			'fields' => array(
				array( 'key' => 'field_a', 'name' => 'a', 'label' => 'A', 'type' => 'text' ),
				array( 'key' => 'field_b', 'name' => 'b', 'label' => 'B', 'type' => 'textarea', 'rows' => 3 ),
				array( 'key' => 'field_c', 'name' => 'c', 'label' => 'C', 'type' => 'wysiwyg', 'toolbar' => 'basic' ),
				array( 'key' => 'field_d', 'name' => 'd', 'label' => 'D', 'type' => 'image' ),
				array( 'key' => 'field_e', 'name' => 'e', 'label' => 'E', 'type' => 'url' ),
				array( 'key' => 'field_tab', 'name' => '', 'label' => 'Tab', 'type' => 'tab' ),
			),
		) );

		$this->assertSame( array( 'a', 'a_de', 'b', 'b_de', 'c', 'c_de', 'd', 'e' ), $this->names( $group['fields'] ) );
		$this->assertSame( 'field_tab', end( $group['fields'] )['key'], 'Tabs are kept and not cloned.' );
	}

	public function test_twin_naming_and_reset_settings(): void {
		$group = edris_with_de_fields( array(
			'key'    => 'group_test',
			'fields' => array(
				array(
					'key'                => 'field_title',
					'name'               => 'title',
					'label'              => 'Title',
					'type'               => 'wysiwyg',
					'required'           => 1,
					'default_value'      => 'Hello',
					'conditional_logic'  => array( array( array( 'field' => 'field_x', 'operator' => '==', 'value' => '1' ) ) ),
					'toolbar'            => 'full',
					'media_upload'       => 1,
					'graphql_field_name' => 'title',
				),
			),
		) );

		$twin = $group['fields'][1];
		$this->assertSame( 'field_title_de', $twin['key'] );
		$this->assertSame( 'title_de', $twin['name'] );
		$this->assertSame( 'Title (DE)', $twin['label'] );
		$this->assertSame( 'wysiwyg', $twin['type'] );
		$this->assertSame( 0, $twin['required'] );
		$this->assertSame( '', $twin['default_value'] );
		$this->assertSame( 0, $twin['conditional_logic'] );
		$this->assertSame( 'full', $twin['toolbar'] );
		$this->assertSame( 1, $twin['media_upload'] );
		$this->assertSame( 'titleDe', $twin['graphql_field_name'] );

		$source = $group['fields'][0];
		$this->assertSame( 1, $source['required'], 'The English source keeps its settings.' );
		$this->assertSame( 'Hello', $source['default_value'] );
	}

	public function test_recurses_into_group_and_repeater_sub_fields(): void {
		$group = edris_with_de_fields( array(
			'key'    => 'group_test',
			'fields' => array(
				array(
					'key'        => 'field_g',
					'name'       => 'g',
					'type'       => 'group',
					'sub_fields' => array(
						array( 'key' => 'field_g_t', 'name' => 't', 'label' => 'T', 'type' => 'text' ),
						array(
							'key'        => 'field_g_r',
							'name'       => 'r',
							'type'       => 'repeater',
							'sub_fields' => array(
								array( 'key' => 'field_g_r_x', 'name' => 'x', 'label' => 'X', 'type' => 'textarea', 'parent_repeater' => 'field_g_r' ),
								array( 'key' => 'field_g_r_y', 'name' => 'y', 'label' => 'Y', 'type' => 'select' ),
							),
						),
					),
				),
			),
		) );

		$this->assertSame( array( 'g' ), $this->names( $group['fields'] ), 'Containers are not cloned.' );
		$sub = $group['fields'][0]['sub_fields'];
		$this->assertSame( array( 't', 't_de', 'r' ), $this->names( $sub ) );
		$this->assertSame( array( 'x', 'x_de', 'y' ), $this->names( $sub[2]['sub_fields'] ) );
		$this->assertSame( 'field_g_r', $sub[2]['sub_fields'][1]['parent_repeater'] );
	}

	public function test_group_files_register_german_twins(): void {
		$project = $this->capture_group_file( 'project-case-study', 'group_project_case_study' );
		$content = $this->find_by_name( $project['fields'], 'project_content' );
		$this->assertSame( array( 'challenge', 'challenge_de', 'solution', 'solution_de' ), $this->names( $content['sub_fields'] ) );

		$features = $this->find_by_name( $project['fields'], 'key_features' );
		$this->assertSame( array( 'title', 'title_de', 'description', 'description_de' ), $this->names( $features['sub_fields'] ) );
	}

	public function test_testimonials_are_not_cloned(): void {
		$testimonial = $this->capture_group_file( 'testimonials', 'group_testimonial_post' );
		foreach ( $this->names( $testimonial['fields'] ) as $name ) {
			$this->assertStringEndsNotWith( '_de', $name );
		}
	}

	public function test_site_content_has_navigation_seo_tab_with_twins(): void {
		$site = $this->capture_group_file( 'site-content', 'group_site_content' );
		$names = $this->names( $site['fields'] );

		$labels = array_column( array_filter( $site['fields'], static function ( $field ) {
			return $field['type'] === 'tab';
		} ), 'label' );
		$this->assertContains( 'Navigation & SEO', $labels );

		$expected = array(
			'nav_projects', 'nav_about', 'nav_bookshelf', 'nav_notebook', 'nav_tech_stack', 'nav_contact',
			'breadcrumb_home', 'skip_to_content',
			'meta_home_title', 'meta_home_description', 'meta_about_title', 'meta_about_description',
			'meta_projects_title', 'meta_projects_description', 'meta_notebook_title', 'meta_notebook_description',
			'meta_bookshelf_title', 'meta_bookshelf_description', 'meta_tech_stack_title', 'meta_tech_stack_description',
			'meta_contact_title', 'meta_contact_description', 'meta_cookie_settings_title', 'meta_cookie_settings_description',
		);
		foreach ( $expected as $name ) {
			$this->assertContains( $name, $names );
			$this->assertContains( $name . '_de', $names );
			$expected_type = substr( $name, -12 ) === '_description' ? 'textarea' : 'text';
			$this->assertSame( $expected_type, $this->find_by_name( $site['fields'], $name )['type'], $name );
		}
	}

	public function test_translation_group_fields_and_locations(): void {
		$group = $this->capture_group_file( 'translation-de', 'group_translation_de' );

		$this->assertSame( 'German translation (DE)', $group['title'] );
		$this->assertSame( array( 'title_de', 'excerpt_de', 'content_de' ), $this->names( $group['fields'] ) );
		$this->assertSame( array( 'text', 'textarea', 'wysiwyg' ), array_column( $group['fields'], 'type' ) );
		$this->assertSame( 3, $group['fields'][1]['rows'] );
		$this->assertSame( 1, $group['fields'][2]['media_upload'] );

		$post_types = array();
		foreach ( $group['location'] as $rule_group ) {
			$post_types[] = $rule_group[0]['value'];
		}
		$this->assertSame( array( 'post', 'project', 'page', 'tech', 'book', 'hobby', 'skill' ), $post_types );
	}
}
