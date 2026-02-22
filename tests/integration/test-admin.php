<?php
/**
 * Tests for HeadlessProAdmin.
 *
 * Covers health check methods, data retrieval, notice logic,
 * and HTML output (no emoji, no placeholder JS).
 *
 * @package HeadlessPro\Tests
 */

class Test_Admin extends WP_UnitTestCase
{
    private HeadlessProAdmin $admin;

    public function set_up(): void
    {
        parent::set_up();
        $this->admin = new HeadlessProAdmin();
    }

    // ------------------------------------------------------------------
    // T016: check_rest_api_health()
    // ------------------------------------------------------------------

    public function test_check_rest_api_health_returns_valid_structure(): void
    {
        $result = $this->admin->check_rest_api_health();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('url', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertContains($result['status'], array('active', 'inactive', 'error'));
        $this->assertNotEmpty($result['url']);
    }

    public function test_check_rest_api_health_handles_wp_error(): void
    {
        // Simulate a connection failure via pre_http_request filter.
        add_filter('pre_http_request', function ($preempt, $args, $url) {
            if (strpos($url, 'wp/v2/types/post') !== false) {
                return new WP_Error('http_request_failed', 'Connection refused');
            }
            return $preempt;
        }, 10, 3);

        $result = $this->admin->check_rest_api_health();

        $this->assertSame('error', $result['status']);
        $this->assertStringContainsString('Connection failed', $result['message']);

        remove_all_filters('pre_http_request');
    }

    // ------------------------------------------------------------------
    // T017: check_graphql_health()
    // ------------------------------------------------------------------

    public function test_check_graphql_health_returns_not_installed_when_no_wpgraphql(): void
    {
        // WPGraphQL may or may not be loaded in the test suite.
        // If it IS loaded, skip this test.
        if (class_exists('WPGraphQL')) {
            $this->markTestSkipped('WPGraphQL is loaded; cannot test not_installed path.');
        }

        $result = $this->admin->check_graphql_health();

        $this->assertSame('not_installed', $result['status']);
        $this->assertEmpty($result['url']);
    }

    public function test_check_graphql_health_returns_valid_structure(): void
    {
        $result = $this->admin->check_graphql_health();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('url', $result);
        $this->assertArrayHasKey('message', $result);
    }

    // ------------------------------------------------------------------
    // T018: get_content_type_stats()
    // ------------------------------------------------------------------

    public function test_get_content_type_stats_includes_all_public_types(): void
    {
        $stats = $this->admin->get_content_type_stats();

        $this->assertIsArray($stats);
        $this->assertNotEmpty($stats);

        // Every public post type should be present.
        $names = array_column($stats, 'name');
        $this->assertContains('post', $names);
        $this->assertContains('page', $names);
    }

    public function test_get_content_type_stats_entry_has_required_keys(): void
    {
        $stats = $this->admin->get_content_type_stats();
        $first = $stats[0];

        $required = array(
            'name', 'label', 'published_count', 'draft_count',
            'show_in_rest', 'rest_base', 'show_in_graphql',
            'graphql_single_name', 'graphql_plural_name', 'edit_url',
        );

        foreach ($required as $key) {
            $this->assertArrayHasKey($key, $first, "Missing key: $key");
        }

        $this->assertIsInt($first['published_count']);
        $this->assertIsInt($first['draft_count']);
        $this->assertIsBool($first['show_in_rest']);
        $this->assertIsBool($first['show_in_graphql']);
    }

    // ------------------------------------------------------------------
    // T024: get_cors_config()
    // ------------------------------------------------------------------

    public function test_get_cors_config_returns_expected_structure(): void
    {
        $config = $this->admin->get_cors_config();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('origins', $config);
        $this->assertArrayHasKey('environment_type', $config);
        $this->assertArrayHasKey('acf_admin_visible', $config);
        $this->assertArrayHasKey('frontend_url', $config);

        $this->assertIsArray($config['origins']);
        $this->assertNotEmpty($config['origins']);
        $this->assertIsString($config['environment_type']);
        $this->assertIsBool($config['acf_admin_visible']);
        $this->assertIsString($config['frontend_url']);
    }

    public function test_get_cors_config_origins_match_headless_config(): void
    {
        $config  = $this->admin->get_cors_config();
        $origins = HeadlessProConfig::get_allowed_origins();

        // The dashboard should show at least the origins from config.
        foreach ($origins as $origin) {
            $this->assertContains(trim($origin), $config['origins']);
        }
    }

    // ------------------------------------------------------------------
    // T030: get_missing_requirements()
    // ------------------------------------------------------------------

    public function test_get_missing_requirements_returns_array(): void
    {
        $notices = $this->admin->get_missing_requirements();
        $this->assertIsArray($notices);
    }

    public function test_get_missing_requirements_checks_permalink_structure(): void
    {
        // Set permalinks to "Plain" (empty string).
        update_option('permalink_structure', '');

        $notices  = $this->admin->get_missing_requirements();
        $messages = array_column($notices, 'message');
        $found    = false;
        foreach ($messages as $msg) {
            if (strpos($msg, 'Post name') !== false) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Expected a permalink warning when structure is not /%postname%/');

        // Restore to correct value.
        update_option('permalink_structure', '/%postname%/');
    }

    public function test_get_missing_requirements_no_permalink_warning_when_correct(): void
    {
        update_option('permalink_structure', '/%postname%/');

        $notices  = $this->admin->get_missing_requirements();
        $messages = array_column($notices, 'message');
        foreach ($messages as $msg) {
            $this->assertStringNotContainsString('Post name', $msg);
        }
    }

    public function test_get_missing_requirements_entry_structure(): void
    {
        // Force at least one notice by setting wrong permalinks.
        update_option('permalink_structure', '');

        $notices = $this->admin->get_missing_requirements();
        $this->assertNotEmpty($notices);

        $first = $notices[0];
        $this->assertArrayHasKey('type', $first);
        $this->assertArrayHasKey('message', $first);
        $this->assertArrayHasKey('dismissible', $first);
        $this->assertArrayHasKey('context', $first);
        $this->assertContains($first['type'], array('error', 'warning', 'info'));
        $this->assertContains($first['context'], array('all', 'headless-pro'));

        update_option('permalink_structure', '/%postname%/');
    }

    // ------------------------------------------------------------------
    // T012: Output checks — no emoji, no alert() stubs
    // ------------------------------------------------------------------

    public function test_admin_page_output_contains_no_emoji(): void
    {
        ob_start();
        $this->admin->admin_page();
        $output = ob_get_clean();

        $this->assert_no_emoji($output, 'admin_page');
    }

    public function test_api_status_page_output_contains_no_emoji(): void
    {
        ob_start();
        $this->admin->api_status_page();
        $output = ob_get_clean();

        $this->assert_no_emoji($output, 'api_status_page');
    }

    public function test_content_management_page_output_contains_no_emoji(): void
    {
        ob_start();
        $this->admin->content_management_page();
        $output = ob_get_clean();

        $this->assert_no_emoji($output, 'content_management_page');
    }

    public function test_content_management_page_has_no_alert_stubs(): void
    {
        ob_start();
        $this->admin->content_management_page();
        $output = ob_get_clean();

        $this->assertStringNotContainsString('alert(', $output);
        $this->assertStringNotContainsString('generateSampleContent', $output);
        $this->assertStringNotContainsString('clearCache', $output);
        $this->assertStringNotContainsString('reindexSearch', $output);
    }

    public function test_admin_page_has_no_placeholder_urls(): void
    {
        ob_start();
        $this->admin->admin_page();
        $output = ob_get_clean();

        $this->assertStringNotContainsString('github.com/your-repo', $output);
    }

    // ------------------------------------------------------------------
    // T028: Content types show REST/GraphQL metadata
    // ------------------------------------------------------------------

    public function test_render_content_types_shows_rest_metadata(): void
    {
        ob_start();
        $this->admin->content_management_page();
        $output = ob_get_clean();

        // Posts are always registered with show_in_rest.
        $this->assertStringContainsString('REST:', $output);
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function assert_no_emoji(string $output, string $context): void
    {
        // Check for common emoji characters used in the old admin.
        $emoji_chars = array(
            "\xF0\x9F\x9A\x80", // 🚀
            "\xF0\x9F\x93\x8A", // 📊
            "\xF0\x9F\x94\xA7", // 🔧
            "\xF0\x9F\x8C\x90", // 🌐
            "\xF0\x9F\x93\x96", // 📖
            "\xF0\x9F\x93\x88", // 📈
            "\xF0\x9F\x94\x97", // 🔗
            "\xF0\x9F\x93\x9D", // 📝
            "\xE2\x9A\xA1",     // ⚡
            "\xE2\x9C\x85",     // ✅
            "\xE2\x9D\x8C",     // ❌
        );

        foreach ($emoji_chars as $emoji) {
            $this->assertStringNotContainsString(
                $emoji,
                $output,
                "$context output contains emoji character (hex: " . bin2hex($emoji) . ')'
            );
        }
    }
}
