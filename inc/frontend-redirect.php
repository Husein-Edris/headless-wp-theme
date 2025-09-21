<?php
/**
 * WordPress Frontend Redirect for Headless Pro Theme
 * Redirects frontend visitors to main site while preserving API access
 * 
 * @package HeadlessPro
 */

if (!defined('ABSPATH')) {
    exit;
}

// Redirect frontend to main site but preserve API endpoints
function redirect_frontend_to_main_site() {
    // Don't redirect if we're in admin area
    if (is_admin()) {
        return;
    }
    
    // Don't redirect if this is an AJAX request
    if (wp_doing_ajax()) {
        return;
    }
    
    // Don't redirect API endpoints (REST API, GraphQL, etc.)
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    
    // Preserve these endpoints
    $api_patterns = [
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
    ];
    
    // Check if current request matches any API pattern
    foreach ($api_patterns as $pattern) {
        if (strpos($request_uri, $pattern) !== false) {
            return; // Don't redirect API endpoints
        }
    }
    
    // Don't redirect if accessing via cron or CLI
    if (wp_doing_cron() || (defined('WP_CLI') && WP_CLI)) {
        return;
    }
    
    // Don't redirect if this is a preview request
    if (is_preview()) {
        return;
    }
    
    // Redirect all other frontend requests to main site
    wp_redirect('https://edrishusein.com', 301);
    exit;
}

// Hook into template_redirect (runs before any HTML is output)
add_action('template_redirect', 'redirect_frontend_to_main_site');

/**
 * Alternative method: More specific approach
 * Uncomment this and comment out the above if you need more control
 */
/*
function redirect_frontend_selective() {
    // Only redirect on these conditions
    if (
        !is_admin() && 
        !wp_doing_ajax() && 
        !wp_doing_cron() && 
        !is_preview() &&
        !is_feed() &&
        !(defined('WP_CLI') && WP_CLI)
    ) {
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        
        // Only redirect actual page requests (not assets or API)
        if (
            !preg_match('/\.(css|js|png|jpg|jpeg|gif|webp|svg|ico|woff|woff2|ttf|eot)$/i', $request_uri) &&
            strpos($request_uri, '/wp-json/') === false &&
            strpos($request_uri, '/graphql') === false &&
            strpos($request_uri, '/wp-admin/') === false &&
            strpos($request_uri, '/wp-content/') === false &&
            strpos($request_uri, '/wp-includes/') === false
        ) {
            wp_redirect('https://edrishusein.com', 301);
            exit;
        }
    }
}
add_action('template_redirect', 'redirect_frontend_selective');
*/

/**
 * Optional: Add CORS headers for API requests from main domain
 */
function add_cors_headers_for_main_domain() {
    // Allow requests from your main domain
    $allowed_origins = [
        'https://edrishusein.com',
        'http://localhost:3000',  // For development
        'http://localhost:3001'   // Alternative dev port
    ];
    
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    
    if (in_array($origin, $allowed_origins)) {
        header("Access-Control-Allow-Origin: $origin");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization, Cache-Control');
    }
}
add_action('init', 'add_cors_headers_for_main_domain');

/**
 * Handle preflight OPTIONS requests
 */
function handle_preflight_requests() {
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        add_cors_headers_for_main_domain();
        http_response_code(200);
        exit;
    }
}
add_action('init', 'handle_preflight_requests');

/**
 * Optional: Customize the redirect message for specific cases
 */
function custom_redirect_with_message() {
    // If you want to show a message before redirecting
    if (isset($_GET['show_redirect_message'])) {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Redirecting...</title>
            <meta http-equiv="refresh" content="3;url=https://edrishusein.com">
            <style>
                body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                .message { max-width: 500px; margin: 0 auto; }
            </style>
        </head>
        <body>
            <div class="message">
                <h1>Redirecting to Main Site</h1>
                <p>You're being redirected to <strong>edrishusein.com</strong></p>
                <p>If you're not redirected automatically, <a href="https://edrishusein.com">click here</a>.</p>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}
add_action('template_redirect', 'custom_redirect_with_message', 1);

?>