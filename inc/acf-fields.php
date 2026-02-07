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

class HeadlessProACFFields
{

    public function __construct()
    {
        // File is loaded during after_setup_theme, so plugins_loaded has already fired.
        // Hook directly to acf/init which fires during init (still ahead of us).
        if (class_exists('ACF')) {
            add_action('acf/init', array($this, 'register_field_groups'));
        }
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
