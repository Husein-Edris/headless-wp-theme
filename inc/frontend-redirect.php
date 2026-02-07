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
    wp_redirect(HeadlessProConfig::get_frontend_url(), 301);
    exit;
}

// Hook into template_redirect (runs before any HTML is output)
add_action('template_redirect', 'redirect_frontend_to_main_site');
