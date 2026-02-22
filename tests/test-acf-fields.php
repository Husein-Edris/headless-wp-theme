<?php
/**
 * Tests for ACF field group registration.
 *
 * Verifies that all expected field groups are registered via PHP code
 * with correct keys, titles, GraphQL attributes, and field definitions.
 *
 * @package HeadlessPro\Tests
 */

class Test_ACF_Fields extends WP_UnitTestCase
{
    /**
     * Expected field groups: key => [title, graphql_field_name, field_count]
     */
    private static array $expected_groups = [
        'group_skills_fields' => [
            'title' => 'Skill Fields',
            'graphql_field_name' => 'skillFields',
            'field_count' => 1,
        ],
        'group_hobbies_fields' => [
            'title' => 'Hobby Fields',
            'graphql_field_name' => 'hobbyFields',
            'field_count' => 1,
        ],
        'group_homepage_sections' => [
            'title' => 'Homepage Content',
            'graphql_field_name' => 'homepageSections',
            'field_count' => 7,
        ],
        'group_64a1b2c3d4e63' => [
            'title' => 'About Page Fields',
            'graphql_field_name' => 'aboutPageFields',
            'field_count' => 15,
        ],
        'group_project_case_study' => [
            'title' => 'Project Case Study',
            'graphql_field_name' => 'caseStudy',
            'field_count' => 4,
        ],
        'group_blog_post_fields' => [
            'title' => 'Blog Post Content',
            'graphql_field_name' => 'blogPostFields',
            'field_count' => 4,
        ],
    ];

    /**
     * T009: Verify all expected field groups are registered with correct
     * group key, title, and graphql_field_name.
     */
    public function test_all_field_groups_registered(): void
    {
        if (!function_exists('acf_get_field_groups')) {
            $this->markTestSkipped('ACF is not active.');
        }

        $registered = acf_get_field_groups();
        $registered_keys = array_column($registered, 'key');

        foreach (self::$expected_groups as $key => $expected) {
            $this->assertContains(
                $key,
                $registered_keys,
                "Field group '{$key}' ({$expected['title']}) is not registered."
            );
        }
    }

    /**
     * T009: Verify each field group has the correct title.
     */
    public function test_field_group_titles(): void
    {
        if (!function_exists('acf_get_field_groups')) {
            $this->markTestSkipped('ACF is not active.');
        }

        $registered = acf_get_field_groups();

        foreach ($registered as $group) {
            if (isset(self::$expected_groups[$group['key']])) {
                $expected = self::$expected_groups[$group['key']];
                $this->assertEquals(
                    $expected['title'],
                    $group['title'],
                    "Field group '{$group['key']}' has wrong title."
                );
            }
        }
    }

    /**
     * T009: Verify each field group has the correct graphql_field_name.
     */
    public function test_field_group_graphql_names(): void
    {
        if (!function_exists('acf_get_field_groups')) {
            $this->markTestSkipped('ACF is not active.');
        }

        $registered = acf_get_field_groups();

        foreach ($registered as $group) {
            if (isset(self::$expected_groups[$group['key']])) {
                $expected = self::$expected_groups[$group['key']];
                $this->assertEquals(
                    $expected['graphql_field_name'],
                    $group['graphql_field_name'] ?? '',
                    "Field group '{$group['key']}' has wrong graphql_field_name."
                );
                $this->assertEquals(
                    1,
                    $group['show_in_graphql'] ?? 0,
                    "Field group '{$group['key']}' should have show_in_graphql = 1."
                );
            }
        }
    }

    /**
     * T010: Verify each field group contains the expected number of
     * top-level fields with correct keys, types, and names.
     */
    public function test_field_group_field_counts(): void
    {
        if (!function_exists('acf_get_fields')) {
            $this->markTestSkipped('ACF is not active.');
        }

        foreach (self::$expected_groups as $key => $expected) {
            $fields = acf_get_fields($key);
            $this->assertCount(
                $expected['field_count'],
                $fields,
                "Field group '{$key}' should have {$expected['field_count']} top-level fields, got " . count($fields) . "."
            );
        }
    }

    /**
     * T010: Verify skill fields have expected keys and types.
     */
    public function test_skill_fields(): void
    {
        if (!function_exists('acf_get_fields')) {
            $this->markTestSkipped('ACF is not active.');
        }

        $fields = acf_get_fields('group_skills_fields');
        $this->assertNotEmpty($fields);

        $field = $fields[0];
        $this->assertEquals('field_skill_short_description', $field['key']);
        $this->assertEquals('short_description', $field['name']);
        $this->assertEquals('textarea', $field['type']);
        $this->assertEquals('shortDescription', $field['graphql_field_name']);
    }

    /**
     * T010: Verify hobby fields have expected keys and types.
     */
    public function test_hobby_fields(): void
    {
        if (!function_exists('acf_get_fields')) {
            $this->markTestSkipped('ACF is not active.');
        }

        $fields = acf_get_fields('group_hobbies_fields');
        $this->assertNotEmpty($fields);

        $field = $fields[0];
        $this->assertEquals('field_hobby_description', $field['key']);
        $this->assertEquals('description', $field['name']);
        $this->assertEquals('textarea', $field['type']);
        $this->assertEquals('description', $field['graphql_field_name']);
    }

    /**
     * T010: Verify homepage has all 7 section groups with correct GraphQL names.
     */
    public function test_homepage_section_fields(): void
    {
        if (!function_exists('acf_get_fields')) {
            $this->markTestSkipped('ACF is not active.');
        }

        $fields = acf_get_fields('group_homepage_sections');
        $expected_sections = [
            'heroSection', 'projectsSection', 'aboutSection',
            'bookshelfSection', 'techstackSection', 'notebookSection', 'contactSection',
        ];

        $graphql_names = array_map(function ($f) {
            return $f['graphql_field_name'] ?? '';
        }, $fields);

        foreach ($expected_sections as $section) {
            $this->assertContains(
                $section,
                $graphql_names,
                "Homepage missing section: {$section}"
            );
        }
    }

    /**
     * T010: Verify blog post fields have expected structure.
     */
    public function test_blog_post_fields(): void
    {
        if (!function_exists('acf_get_fields')) {
            $this->markTestSkipped('ACF is not active.');
        }

        $fields = acf_get_fields('group_blog_post_fields');
        $field_map = [];
        foreach ($fields as $f) {
            $field_map[$f['key']] = $f;
        }

        // reading_time
        $this->assertArrayHasKey('field_blog_reading_time', $field_map);
        $this->assertEquals('text', $field_map['field_blog_reading_time']['type']);

        // conclusion_section (group with repeater sub-field)
        $this->assertArrayHasKey('field_blog_conclusion', $field_map);
        $this->assertEquals('group', $field_map['field_blog_conclusion']['type']);

        // custom_tags (repeater)
        $this->assertArrayHasKey('field_blog_tags_custom', $field_map);
        $this->assertEquals('repeater', $field_map['field_blog_tags_custom']['type']);

        // author_bio_override
        $this->assertArrayHasKey('field_blog_author_bio', $field_map);
        $this->assertEquals('textarea', $field_map['field_blog_author_bio']['type']);
    }

    /**
     * T008: Verify headless_pro_get_page_id_by_slug() helper works.
     */
    public function test_page_slug_helper_returns_id(): void
    {
        $page_id = self::factory()->post->create([
            'post_type' => 'page',
            'post_name' => 'about-me',
            'post_title' => 'About me',
            'post_status' => 'publish',
        ]);

        $result = headless_pro_get_page_id_by_slug('about-me');
        $this->assertEquals($page_id, $result);
    }

    /**
     * T008: Verify headless_pro_get_page_id_by_slug() returns 0 for missing slug.
     */
    public function test_page_slug_helper_returns_zero_for_missing(): void
    {
        $result = headless_pro_get_page_id_by_slug('nonexistent-page-slug');
        $this->assertEquals(0, $result);
    }

    /**
     * T029: Verify acf/settings/show_admin returns false in production.
     */
    public function test_acf_admin_hidden_in_production(): void
    {
        if (!function_exists('acf_get_setting')) {
            $this->markTestSkipped('ACF is not active.');
        }

        \Patchwork\redefine('wp_get_environment_type', function () {
            return 'production';
        });

        $result = apply_filters('acf/settings/show_admin', true);
        $this->assertFalse($result, 'ACF admin should be hidden in production.');

        \Patchwork\restoreAll();
    }

    /**
     * T029: Verify acf/settings/show_admin returns false in staging.
     */
    public function test_acf_admin_hidden_in_staging(): void
    {
        if (!function_exists('acf_get_setting')) {
            $this->markTestSkipped('ACF is not active.');
        }

        \Patchwork\redefine('wp_get_environment_type', function () {
            return 'staging';
        });

        $result = apply_filters('acf/settings/show_admin', true);
        $this->assertFalse($result, 'ACF admin should be hidden in staging.');

        \Patchwork\restoreAll();
    }

    /**
     * T030: Verify acf/settings/show_admin returns true in local.
     */
    public function test_acf_admin_visible_in_local(): void
    {
        if (!function_exists('acf_get_setting')) {
            $this->markTestSkipped('ACF is not active.');
        }

        \Patchwork\redefine('wp_get_environment_type', function () {
            return 'local';
        });

        $result = apply_filters('acf/settings/show_admin', true);
        $this->assertTrue($result, 'ACF admin should be visible in local.');

        \Patchwork\restoreAll();
    }

    /**
     * T030: Verify acf/settings/show_admin returns true in development.
     */
    public function test_acf_admin_visible_in_development(): void
    {
        if (!function_exists('acf_get_setting')) {
            $this->markTestSkipped('ACF is not active.');
        }

        \Patchwork\redefine('wp_get_environment_type', function () {
            return 'development';
        });

        $result = apply_filters('acf/settings/show_admin', true);
        $this->assertTrue($result, 'ACF admin should be visible in development.');

        \Patchwork\restoreAll();
    }

    /**
     * T034: Verify acf/settings/save_json returns theme acf-json/ path in local.
     */
    public function test_acf_save_json_path_in_local(): void
    {
        if (!function_exists('acf_get_setting')) {
            $this->markTestSkipped('ACF is not active.');
        }

        \Patchwork\redefine('wp_get_environment_type', function () {
            return 'local';
        });

        $result = apply_filters('acf/settings/save_json', '');
        $expected = get_template_directory() . '/acf-json';
        $this->assertEquals($expected, $result, 'ACF save_json should point to theme acf-json/ in local.');

        \Patchwork\restoreAll();
    }

    /**
     * T034: Verify acf/settings/save_json returns theme acf-json/ path in development.
     */
    public function test_acf_save_json_path_in_development(): void
    {
        if (!function_exists('acf_get_setting')) {
            $this->markTestSkipped('ACF is not active.');
        }

        \Patchwork\redefine('wp_get_environment_type', function () {
            return 'development';
        });

        $result = apply_filters('acf/settings/save_json', '');
        $expected = get_template_directory() . '/acf-json';
        $this->assertEquals($expected, $result, 'ACF save_json should point to theme acf-json/ in development.');

        \Patchwork\restoreAll();
    }

    /**
     * T035: Verify acf/settings/save_json returns empty string in production.
     */
    public function test_acf_save_json_disabled_in_production(): void
    {
        if (!function_exists('acf_get_setting')) {
            $this->markTestSkipped('ACF is not active.');
        }

        \Patchwork\redefine('wp_get_environment_type', function () {
            return 'production';
        });

        $result = apply_filters('acf/settings/save_json', get_template_directory() . '/acf-json');
        $this->assertEmpty($result, 'ACF save_json should be empty in production.');

        \Patchwork\restoreAll();
    }

    /**
     * T035: Verify acf/settings/save_json returns empty string in staging.
     */
    public function test_acf_save_json_disabled_in_staging(): void
    {
        if (!function_exists('acf_get_setting')) {
            $this->markTestSkipped('ACF is not active.');
        }

        \Patchwork\redefine('wp_get_environment_type', function () {
            return 'staging';
        });

        $result = apply_filters('acf/settings/save_json', get_template_directory() . '/acf-json');
        $this->assertEmpty($result, 'ACF save_json should be empty in staging.');

        \Patchwork\restoreAll();
    }

    /**
     * T036: Verify acf/settings/load_json includes theme acf-json/ path in all environments.
     */
    public function test_acf_load_json_includes_theme_path(): void
    {
        if (!function_exists('acf_get_setting')) {
            $this->markTestSkipped('ACF is not active.');
        }

        $expected_path = get_template_directory() . '/acf-json';

        foreach (['local', 'development', 'staging', 'production'] as $env) {
            \Patchwork\redefine('wp_get_environment_type', function () use ($env) {
                return $env;
            });

            $result = apply_filters('acf/settings/load_json', []);
            $this->assertContains(
                $expected_path,
                $result,
                "ACF load_json should include theme acf-json/ path in '{$env}' environment."
            );

            \Patchwork\restoreAll();
        }
    }
}
