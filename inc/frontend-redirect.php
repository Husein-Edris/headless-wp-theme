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
    if (headless_pro_is_allowlisted($request_uri, HeadlessProConfig::get_frontend_redirect_allowlist())) {
        return; // Don't redirect API endpoints
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

    // Redirect all other frontend requests to the frontend route for the same content.
    $path = strpos($request_uri, '/') === 0 ? $request_uri : '/' . $request_uri;
    $target = rtrim($frontend, '/') . headless_pro_frontend_path($path, headless_pro_redirect_object());
    wp_redirect($target, 301);
    exit;
}

// Allowlist entries are path prefixes. A substring match let content URLs such as
// /technologies/graphql/ (matching '/graphql') skip the redirect and render on the CMS.
function headless_pro_is_allowlisted(string $request_uri, array $allowlist): bool {
    $path = (string) wp_parse_url($request_uri, PHP_URL_PATH);
    foreach ($allowlist as $pattern) {
        if ($pattern !== '' && strpos($path, $pattern) === 0) {
            return true;
        }
    }
    return false;
}

// CMS permalinks differ from the frontend routes (posts live under /notebook, books on
// /bookshelf, ...), so keeping the path sent most CMS URLs to a 404 on the frontend.
// $object comes from headless_pro_redirect_object(). Unknown content keeps its path; a
// mapped route keeps the query string so tracking parameters (utm_*) survive the redirect.
function headless_pro_frontend_path(string $path, array $object): string {
    $route = headless_pro_frontend_route((string) wp_parse_url($path, PHP_URL_PATH), $object);
    if ($route === null) {
        return $path;
    }
    $query = (string) wp_parse_url($path, PHP_URL_QUERY);
    return $query === '' ? $route : $route . '?' . $query;
}

// Frontend route for a CMS path, or null when the frontend has no matching route.
function headless_pro_frontend_route(string $path_only, array $object): ?string {
    if (preg_match('#^/[a-z0-9_-]*sitemap[a-z0-9_-]*\.xml$#i', $path_only)) {
        return '/sitemap.xml';
    }

    $section = array(
        'book'  => '/bookshelf',
        'tech'  => '/tech-stack',
        'skill' => '/about',
        'hobby' => '/about',
    );
    $kind = $object['kind'] ?? 'other';
    $post_type = $object['post_type'] ?? '';
    $slug = $object['slug'] ?? '';

    if (($kind === 'singular' || $kind === 'archive') && isset($section[$post_type])) {
        return $section[$post_type];
    }
    if ($kind === 'singular' && $post_type === 'post' && $slug !== '') {
        return '/notebook/' . $slug;
    }
    if ($kind === 'singular' && $post_type === 'project' && $slug !== '') {
        return '/projects/' . $slug;
    }
    if ($kind === 'singular' && $post_type === 'service' && $slug !== '') {
        // The post slug is the English one; the German slug lives in ACF slug_de.
        return '/services/' . $slug;
    }
    if ($kind === 'singular' && $post_type === 'page' && $slug === 'about-me') {
        return '/about';
    }
    if ($kind === 'singular' && $post_type === 'page' && $slug === 'home') {
        return '/';
    }
    if ($kind === 'archive' && $post_type === 'project') {
        return '/projects';
    }
    if ($kind === 'archive' && $post_type === 'service') {
        return '/services';
    }
    if ($kind === 'term') {
        return ($object['taxonomy'] ?? '') === 'tech_category' ? '/tech-stack' : '/notebook';
    }

    return null;
}

// Describe the queried CMS object for headless_pro_frontend_path().
function headless_pro_redirect_object(): array {
    if (is_singular()) {
        $post = get_queried_object();
        if ($post instanceof WP_Post) {
            return array('kind' => 'singular', 'post_type' => $post->post_type, 'slug' => $post->post_name);
        }
    }
    if (is_post_type_archive()) {
        $post_type = get_query_var('post_type');
        return array('kind' => 'archive', 'post_type' => is_array($post_type) ? (string) reset($post_type) : (string) $post_type);
    }
    if (is_category() || is_tag() || is_tax()) {
        $term = get_queried_object();
        if ($term instanceof WP_Term) {
            return array('kind' => 'term', 'taxonomy' => $term->taxonomy);
        }
    }
    return array('kind' => 'other');
}

// Hook into template_redirect (runs before any HTML is output)
add_action('template_redirect', 'redirect_frontend_to_main_site');

// The frontend publishes the only sitemap; WordPress core would otherwise list CMS URLs.
add_filter('wp_sitemaps_enabled', '__return_false');
