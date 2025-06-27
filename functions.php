<?php
/**
 * Headless Pro Theme Functions
 * 
 * Modern, secure WordPress theme for headless/JAMstack projects
 * 
 * @package HeadlessPro
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Theme constants
define('HEADLESS_THEME_VERSION', '1.0.0');
define('HEADLESS_THEME_PATH', get_template_directory());
define('HEADLESS_THEME_URL', get_template_directory_uri());

/**
 * Theme Setup
 */
function headless_pro_setup() {
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
class HeadlessProSecurity {
    
    public function __construct() {
        add_action('init', array($this, 'security_headers'));
        add_action('wp_head', array($this, 'remove_wp_version'));
        add_filter('rest_authentication_errors', array($this, 'secure_rest_api'));
        add_action('init', array($this, 'disable_xmlrpc'));
        add_filter('wp_headers', array($this, 'security_headers_filter'));
    }
    
    public function security_headers() {
        if (!is_admin()) {
            header('X-Content-Type-Options: nosniff');
            header('X-Frame-Options: SAMEORIGIN');
            header('X-XSS-Protection: 1; mode=block');
            header('Referrer-Policy: strict-origin-when-cross-origin');
        }
    }
    
    public function security_headers_filter($headers) {
        $headers['X-Content-Type-Options'] = 'nosniff';
        $headers['X-Frame-Options'] = 'SAMEORIGIN';
        $headers['X-XSS-Protection'] = '1; mode=block';
        return $headers;
    }
    
    public function remove_wp_version() {
        remove_action('wp_head', 'wp_generator');
    }
    
    public function secure_rest_api($result) {
        // Allow GraphQL and specific endpoints
        if (strpos($_SERVER['REQUEST_URI'], '/graphql') !== false) {
            return $result;
        }
        
        // Add custom authentication logic here if needed
        return $result;
    }
    
    public function disable_xmlrpc() {
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
class HeadlessProPerformance {
    
    public function __construct() {
        add_action('init', array($this, 'disable_emojis'));
        add_action('wp_enqueue_scripts', array($this, 'dequeue_scripts'), 20);
        add_action('wp_head', array($this, 'remove_unnecessary_headers'), 1);
        add_filter('wp_resource_hints', array($this, 'remove_dns_prefetch'), 10, 2);
    }
    
    public function disable_emojis() {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    }
    
    public function dequeue_scripts() {
        if (!is_admin()) {
            wp_dequeue_script('jquery');
            wp_dequeue_script('wp-embed');
            wp_dequeue_style('wp-block-library');
            wp_dequeue_style('wp-block-library-theme');
            wp_dequeue_style('classic-theme-styles');
        }
    }
    
    public function remove_unnecessary_headers() {
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'wp_shortlink_wp_head');
        remove_action('wp_head', 'rest_output_link_wp_head');
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
    }
    
    public function remove_dns_prefetch($urls, $relation_type) {
        if ('dns-prefetch' === $relation_type) {
            return array_diff(wp_dependencies_unique_hosts(), $urls);
        }
        return $urls;
    }
}
new HeadlessProPerformance();

/**
 * CORS Configuration for Headless Apps
 */
class HeadlessProCORS {
    
    public function __construct() {
        add_action('rest_api_init', array($this, 'add_cors_headers'));
        add_action('graphql_init', array($this, 'add_cors_headers'));
    }
    
    public function add_cors_headers() {
        $allowed_origins = $this->get_allowed_origins();
        
        $origin = get_http_origin();
        
        if (in_array($origin, $allowed_origins)) {
            header('Access-Control-Allow-Origin: ' . $origin);
        }
        
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With');
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            status_header(200);
            exit();
        }
    }
    
    private function get_allowed_origins() {
        // Add your frontend domains here
        $origins = array(
            'http://localhost:3000',
            'http://localhost:3001',
            'https://edrishusein.com',
            'https://www.edrishusein.com',
        );
        
        // Allow additional origins from environment or settings
        if (defined('HEADLESS_ALLOWED_ORIGINS')) {
            $additional_origins = explode(',', HEADLESS_ALLOWED_ORIGINS);
            $origins = array_merge($origins, $additional_origins);
        }
        
        return apply_filters('headless_pro_allowed_origins', $origins);
    }
}
new HeadlessProCORS();

/**
 * Load theme modules safely
 */
function headless_pro_load_modules() {
    $modules = array(
        'post-types.php',
        'acf-fields.php', 
        'graphql.php',
        'admin.php',
        'api.php'
    );
    
    foreach ($modules as $module) {
        $file_path = HEADLESS_THEME_PATH . '/inc/' . $module;
        if (file_exists($file_path)) {
            require_once $file_path;
        }
    }
}
add_action('after_setup_theme', 'headless_pro_load_modules');

/**
 * Theme Customizer (minimal for headless)
 */
function headless_pro_customize_register($wp_customize) {
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
function headless_pro_cleanup() {
    // Remove unnecessary menu pages for headless setup
    if (!current_user_can('manage_options')) {
        remove_menu_page('themes.php');
        remove_menu_page('customize.php');
    }
}
add_action('admin_menu', 'headless_pro_cleanup', 999);

/**
 * Add helpful admin notices
 */
function headless_pro_admin_notices() {
    $screen = get_current_screen();
    
    if ($screen->id === 'dashboard') {
        echo '<div class="notice notice-info">
            <p><strong>Headless Pro Theme Active:</strong> Your WordPress backend is optimized for headless/JAMstack applications. 
            <a href="' . home_url() . '" target="_blank">View frontend status</a></p>
        </div>';
    }
}
add_action('admin_notices', 'headless_pro_admin_notices');

/**
 * Enqueue admin styles
 */
function headless_pro_admin_styles() {
    wp_enqueue_style(
        'headless-pro-admin',
        HEADLESS_THEME_URL . '/assets/admin.css',
        array(),
        HEADLESS_THEME_VERSION
    );
}
add_action('admin_enqueue_scripts', 'headless_pro_admin_styles');