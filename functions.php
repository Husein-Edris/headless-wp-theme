<?php

/**
 * Headless Pro Theme Functions
 * 
 * Modern, secure WordPress theme for headless/JAMstack projects
 * 
 * @package HeadlessPro
 * @version 2.1.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Theme constants
define('HEADLESS_THEME_VERSION', wp_get_theme()->get('Version'));
define('HEADLESS_THEME_PATH', get_template_directory());
define('HEADLESS_THEME_URL', get_template_directory_uri());

/**
 * Centralized Configuration
 *
 * Reads from wp-config.php constants with sensible defaults.
 * Define these in wp-config.php to override:
 *   - HEADLESS_FRONTEND_URL  (string, default: 'https://edrishusein.com')
 *   - HEADLESS_ALLOWED_ORIGINS (comma-separated string)
 */
class HeadlessProConfig
{
    private static $default_origins = array(
        'http://localhost:3000',
        'http://localhost:3001',
        'https://edrishusein.com',
        'https://www.edrishusein.com',
        'https://magical-swirles.82-165-132-190.plesk.page',
        'http://82.165.132.190',
        'https://82.165.132.190',
    );

    public static function get_frontend_url()
    {
        if (defined('HEADLESS_FRONTEND_URL')) {
            return rtrim(HEADLESS_FRONTEND_URL, '/');
        }
        $opt = get_option('headless_pro_frontend_url', '');
        if (is_string($opt) && trim($opt) !== '') {
            return rtrim(trim($opt), '/');
        }
        return 'https://edrishusein.com';
    }

    public static function get_allowed_origins()
    {
        if (defined('HEADLESS_ALLOWED_ORIGINS') && !empty(HEADLESS_ALLOWED_ORIGINS)) {
            return array_map('trim', explode(',', HEADLESS_ALLOWED_ORIGINS));
        }
        $opt = get_option('headless_pro_allowed_origins', '');
        if (is_string($opt) && trim($opt) !== '') {
            return self::parse_list($opt);
        }
        return self::$default_origins;
    }

    public static function get_frontend_redirect_mode(): string
    {
        $mode = get_option('headless_pro_redirect_mode', 'always');
        $mode = is_string($mode) ? strtolower(trim($mode)) : 'always';
        return in_array($mode, array('always', 'prod_staging', 'off'), true) ? $mode : 'always';
    }

    public static function get_default_frontend_redirect_allowlist(): array
    {
        return array(
            '/wp-json/',           // REST API
            '/graphql',            // GraphQL endpoint
            '/wp-admin/',          // Admin area
            '/wp-login.php',       // Login page
            '/wp-content/',        // Assets (images, CSS, JS)
            '/wp-includes/',       // WordPress core files
            '/.well-known/',       // SSL verification, etc.
            '/xmlrpc.php',         // XML-RPC (if needed)
            '/feed/',              // RSS feeds
            '/sitemap',            // Sitemaps
        );
    }

    public static function get_frontend_redirect_allowlist(): array
    {
        $opt = get_option('headless_pro_redirect_allowlist', '');
        if (is_string($opt) && trim($opt) !== '') {
            return self::parse_list($opt);
        }
        return self::get_default_frontend_redirect_allowlist();
    }

    public static function is_cors_debug_enabled(): bool
    {
        return (bool) get_option('headless_pro_cors_debug', false);
    }

    private static function parse_list(string $raw): array
    {
        $raw = str_replace(array("\r\n", "\r"), "\n", $raw);
        $parts = preg_split('/[,\n]+/', $raw) ?: array();
        $items = array();
        foreach ($parts as $p) {
            $p = trim($p);
            if ($p === '') {
                continue;
            }
            $items[] = $p;
        }
        return array_values(array_unique($items));
    }
}

/**
 * Theme Setup
 */
function headless_pro_setup()
{
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    add_theme_support('custom-logo');
    add_theme_support('customize-selective-refresh-widgets');

    // Image sizes for different use cases
    add_image_size('headless-thumbnail', 400, 300, true);
    add_image_size('headless-medium', 800, 600, true);
    add_image_size('headless-large', 1200, 900, true);

    // Remove unnecessary features for headless
    remove_theme_support('widgets-block-editor');
}
add_action('after_setup_theme', 'headless_pro_setup');

/**
 * Security Enhancements
 */
class HeadlessProSecurity
{

    public function __construct()
    {
        add_action('init', array($this, 'security_headers'));
        add_action('wp_head', array($this, 'remove_wp_version'));
        add_filter('rest_authentication_errors', array($this, 'secure_rest_api'));
        add_action('init', array($this, 'disable_xmlrpc'));
        add_filter('wp_headers', array($this, 'security_headers_filter'));
    }

    public function security_headers()
    {
        if (!is_admin()) {
            header('X-Content-Type-Options: nosniff');
            header('X-Frame-Options: SAMEORIGIN');
            header('X-XSS-Protection: 1; mode=block');
            header('Referrer-Policy: strict-origin-when-cross-origin');
        }
    }

    public function security_headers_filter($headers)
    {
        $headers['X-Content-Type-Options'] = 'nosniff';
        $headers['X-Frame-Options'] = 'SAMEORIGIN';
        $headers['X-XSS-Protection'] = '1; mode=block';
        return $headers;
    }

    public function remove_wp_version()
    {
        remove_action('wp_head', 'wp_generator');
    }

    public function secure_rest_api($result)
    {
        // Add custom authentication logic here if needed
        return $result;
    }

    public function disable_xmlrpc()
    {
        if (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) {
            http_response_code(403);
            exit('XML-RPC is disabled for security reasons.');
        }
    }
}
new HeadlessProSecurity();

/**
 * Performance Optimizations
 */
class HeadlessProPerformance
{

    public function __construct()
    {
        add_action('init', array($this, 'disable_emojis'));
        add_action('wp_enqueue_scripts', array($this, 'dequeue_scripts'), 20);
        add_action('wp_head', array($this, 'remove_unnecessary_headers'), 1);
        add_filter('wp_resource_hints', array($this, 'remove_dns_prefetch'), 10, 2);
    }

    public function disable_emojis()
    {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    }

    public function dequeue_scripts()
    {
        if (!is_admin()) {
            wp_dequeue_script('jquery');
            wp_dequeue_script('wp-embed');
            wp_dequeue_style('wp-block-library');
            wp_dequeue_style('wp-block-library-theme');
            wp_dequeue_style('classic-theme-styles');
        }
    }

    public function remove_unnecessary_headers()
    {
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'wp_shortlink_wp_head');
        remove_action('wp_head', 'rest_output_link_wp_head');
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
    }

    public function remove_dns_prefetch($urls, $relation_type)
    {
        if ('dns-prefetch' === $relation_type) {
            return array_diff(wp_dependencies_unique_hosts(), $urls);
        }
        return $urls;
    }
}
new HeadlessProPerformance();

/**
 * CORS Configuration for Headless Apps
 *
 * Single source of truth for all CORS headers.
 * Origins configured via HEADLESS_ALLOWED_ORIGINS in wp-config.php
 * or the 'headless_pro_allowed_origins' filter.
 */
class HeadlessProCORS
{

    public function __construct()
    {
        add_action('init', array($this, 'handle_cors'), 1);
    }

    public function handle_cors()
    {
        $origin = $this->get_request_origin();
        $allowed_origins = $this->get_allowed_origins();

        if ($origin && in_array($origin, $allowed_origins, true)) {
            header('Access-Control-Allow-Origin: ' . $origin);
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
            header('Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With, Origin, Accept, Cache-Control');
            if (HeadlessProConfig::is_cors_debug_enabled() || (defined('WP_DEBUG') && WP_DEBUG)) {
                header('X-HeadlessPro-CORS: allowed');
                header('X-HeadlessPro-Origin: ' . $origin);
            }
        } elseif (HeadlessProConfig::is_cors_debug_enabled() || (defined('WP_DEBUG') && WP_DEBUG)) {
            header('X-HeadlessPro-CORS: blocked');
            if ($origin) {
                header('X-HeadlessPro-Origin: ' . $origin);
            }
        }

        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            status_header(200);
            exit();
        }
    }

    private function get_request_origin()
    {
        if (function_exists('get_http_origin')) {
            return get_http_origin();
        }
        return isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
    }

    private function get_allowed_origins()
    {
        return apply_filters('headless_pro_allowed_origins', HeadlessProConfig::get_allowed_origins());
    }
}
new HeadlessProCORS();

/**
 * Load theme modules safely
 */
function headless_pro_load_modules()
{
    $modules = array(
        'post-types.php',
        'acf-fields.php',
        'admin.php',
        'api.php',
        'frontend-redirect.php'
    );

    foreach ($modules as $module) {
        $file_path = HEADLESS_THEME_PATH . '/inc/' . $module;
        if (file_exists($file_path)) {
            require_once $file_path;
        }
    }
}

// Load modules after plugins are loaded
add_action('after_setup_theme', 'headless_pro_load_modules');

/**
 * Theme Customizer (minimal for headless)
 */
function headless_pro_customize_register($wp_customize)
{
    // Add basic site identity options
    $wp_customize->add_section('headless_settings', array(
        'title' => 'Headless Settings',
        'priority' => 30,
    ));

    $wp_customize->add_setting('frontend_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control('frontend_url', array(
        'label' => 'Frontend URL',
        'section' => 'headless_settings',
        'type' => 'url',
        'description' => 'URL of your frontend application',
    ));
}
add_action('customize_register', 'headless_pro_customize_register');

/**
 * Cleanup and optimization
 */
function headless_pro_cleanup()
{
    // Remove unnecessary menu pages for headless setup
    if (!current_user_can('manage_options')) {
        remove_menu_page('themes.php');
        remove_menu_page('customize.php');
    }
}
add_action('admin_menu', 'headless_pro_cleanup', 999);

