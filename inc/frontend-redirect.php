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

    $request_uri = $_SERVER['REQUEST_URI'] ?? '';

    // Optional redirect mode.
    $mode = HeadlessProConfig::get_frontend_redirect_mode();
    if ($mode === 'off') {
        return;
    }
    if ($mode === 'prod_staging') {
        $env = wp_get_environment_type();
        if ($env !== 'production' && $env !== 'staging') {
            return;
        }
    }

    // Preserve these endpoints (allowlist).
    foreach (HeadlessProConfig::get_frontend_redirect_allowlist() as $pattern) {
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

    $frontend = HeadlessProConfig::get_frontend_url();
    $frontend_host = wp_parse_url($frontend, PHP_URL_HOST);
    $current_host = $_SERVER['HTTP_HOST'] ?? '';
    if ($frontend_host && $current_host && $frontend_host === $current_host) {
        return;
    }

    // Redirect all other frontend requests to main site (preserve path/query).
    $target = rtrim($frontend, '/') . (strpos($request_uri, '/') === 0 ? $request_uri : '/' . $request_uri);
    wp_redirect($target, 301);
    exit;
}

// Hook into template_redirect (runs before any HTML is output)
add_action('template_redirect', 'redirect_frontend_to_main_site');
