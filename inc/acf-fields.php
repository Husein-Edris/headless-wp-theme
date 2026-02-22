<?php

/**
 * ACF Fields Loader for Headless Pro Theme
 *
 * Registers all field groups from individual files in inc/acf-fields/.
 * Field definitions match the live site export — edit individual files to customize.
 *
 * @package HeadlessPro
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Resolve a page slug to its post ID.
 *
 * Used by ACF location rules to avoid hardcoding page IDs.
 * Returns 0 if no page with the given slug exists.
 *
 * @param string $slug The page slug to look up.
 * @return int The page ID, or 0 if not found.
 */
function headless_pro_get_page_id_by_slug(string $slug): int
{
    $page = get_page_by_path($slug);
    return $page ? (int) $page->ID : 0;
}

class HeadlessProACFFields
{

    public function __construct()
    {
        // File is loaded during after_setup_theme, so plugins_loaded has already fired.
        // Hook directly to acf/init which fires during init (still ahead of us).
        if (class_exists('ACF')) {
            add_action('acf/init', array($this, 'register_field_groups'));
        }

        // US3: Hide ACF admin UI in production/staging environments.
        add_filter('acf/settings/show_admin', array($this, 'maybe_hide_admin'));

        // US4: ACF JSON sync — save in dev only, load everywhere.
        add_filter('acf/settings/save_json', array($this, 'get_save_json_path'));
        add_filter('acf/settings/load_json', array($this, 'add_load_json_path'));
    }

    /**
     * US3: Hide ACF field group editor in production and staging.
     *
     * @param bool $show Current show_admin setting.
     * @return bool False in production/staging, unchanged otherwise.
     */
    public function maybe_hide_admin($show)
    {
        $env = wp_get_environment_type();
        if ($env === 'production' || $env === 'staging') {
            return false;
        }
        return $show;
    }

    /**
     * US4: Set ACF JSON save path — only in local/development environments.
     *
     * @param string $path Current save path.
     * @return string Theme acf-json/ path in dev, empty string in production/staging.
     */
    public function get_save_json_path($path)
    {
        $env = wp_get_environment_type();
        if ($env === 'production' || $env === 'staging') {
            return '';
        }
        return get_template_directory() . '/acf-json';
    }

    /**
     * US4: Add theme acf-json/ directory as a JSON load point in all environments.
     *
     * @param array $paths Current load paths.
     * @return array Paths with theme acf-json/ added.
     */
    public function add_load_json_path($paths)
    {
        $paths[] = get_template_directory() . '/acf-json';
        return $paths;
    }

    public function register_field_groups()
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        $field_files = array(
            'skills',
            'hobbies',
            'homepage',
            'about-page',
            'project-case-study',
            'blog-post',
        );

        foreach ($field_files as $file) {
            $path = HEADLESS_THEME_PATH . '/inc/acf-fields/' . $file . '.php';
            if (file_exists($path)) {
                require_once $path;
            }
        }
    }
}

new HeadlessProACFFields();
