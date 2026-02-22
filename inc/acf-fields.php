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
        // Hook to acf/init; register_field_groups() will no-op if ACF isn't active.
        add_action('acf/init', array($this, 'register_field_groups'));

        // US3: Hide ACF admin UI in production/staging environments.
        add_filter('acf/settings/show_admin', array($this, 'maybe_hide_admin'));

        // Slug-based location rule matcher for ACF field groups.
        add_filter('acf/location/rule_match/page', array($this, 'match_page_by_slug'), 10, 4);

        // US4: ACF JSON sync — save in dev only, load everywhere.
        add_filter('acf/settings/save_json', array($this, 'get_save_json_path'));
        add_filter('acf/settings/load_json', array($this, 'add_load_json_path'));

        // Guaranteed editor UX: render About ACF fields in our own metabox on the About page.
        // This bypasses Gutenberg's per-user hidden panel preferences which can hide ACF metaboxes.
        add_action('add_meta_boxes', array($this, 'maybe_add_forced_about_fields_metabox'), 100, 2);
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

    /**
     * Match ACF location rules by page slug instead of numeric ID.
     *
     * Rules with a value starting with 'slug:' are intercepted and matched
     * against the post's slug at render time. Non-slug rules pass through.
     *
     * @param bool  $match       Current match result from ACF.
     * @param array $rule        Location rule array (param, operator, value).
     * @param array $screen      Current screen context (post_id, etc.).
     * @param array $field_group The field group being evaluated.
     * @return bool Whether the rule matches.
     */
    public function match_page_by_slug($match, $rule, $screen, $field_group)
    {
        if (strpos($rule['value'], 'slug:') !== 0) {
            return $match;
        }

        if (empty($screen['post_id'])) {
            return false;
        }

        $target_slug = substr($rule['value'], 5);
        $post_id = $screen['post_id'];
        if (is_string($post_id) && strpos($post_id, 'post_') === 0) {
            $post_id = (int) substr($post_id, 5);
        } else {
            $post_id = (int) $post_id;
        }

        if ($post_id <= 0) {
            return false;
        }

        $post = get_post($post_id);

        if (!$post) {
            return false;
        }

        $slugs_match = ($post->post_name === $target_slug);

        if ($rule['operator'] === '!=') {
            return !$slugs_match;
        }

        return $slugs_match;
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

    private function is_about_page(WP_Post $post): bool
    {
        if ($post->post_type !== 'page') {
            return false;
        }
        return in_array($post->post_name, array('about-me', 'about'), true);
    }

    public function maybe_add_forced_about_fields_metabox(string $post_type, $post): void
    {
        if ($post_type !== 'page' || !($post instanceof WP_Post)) {
            return;
        }

        if (!$this->is_about_page($post)) {
            return;
        }

        if (!function_exists('acf_get_fields') || !function_exists('acf_render_fields')) {
            return;
        }

        $this->remove_native_about_acf_metaboxes($post_type);

        add_meta_box(
            'headless_forced_about_fields',
            'About Page Fields',
            array($this, 'render_forced_about_fields_metabox'),
            'page',
            'normal',
            'high'
        );
    }

    public function render_forced_about_fields_metabox(WP_Post $post): void
    {
        if (!function_exists('acf_get_fields') || !function_exists('acf_render_fields')) {
            echo '<p>ACF is not active.</p>';
            return;
        }

        $fields = acf_get_fields('group_64a1b2c3d4e63');
        if (empty($fields) || !is_array($fields)) {
            echo '<p>Could not load fields for About Page group.</p>';
            return;
        }

        // Ensure ACF form data (nonce, post_id) exists even if the native ACF metabox is hidden.
        if (function_exists('acf_form_data')) {
            static $acf_form_data_printed = false;
            if (!$acf_form_data_printed) {
                $acf_form_data_printed = true;
                acf_form_data(array('post_id' => (int) $post->ID));
            }
        }

        acf_render_fields((int) $post->ID, $fields);
    }

    private function remove_native_about_acf_metaboxes(string $post_type): void
    {
        global $wp_meta_boxes;
        if (empty($wp_meta_boxes[$post_type]) || !is_array($wp_meta_boxes[$post_type])) {
            return;
        }

        $needle = '64a1b2c3d4e63';
        $contexts = array('normal', 'advanced', 'side');

        foreach ($contexts as $context) {
            foreach (array('high', 'core', 'default', 'low') as $priority) {
                if (empty($wp_meta_boxes[$post_type][$context][$priority]) || !is_array($wp_meta_boxes[$post_type][$context][$priority])) {
                    continue;
                }

                foreach ($wp_meta_boxes[$post_type][$context][$priority] as $id => $box) {
                    if (is_string($id) && strpos($id, $needle) !== false) {
                        remove_meta_box($id, $post_type, $context);
                    }
                }
            }
        }
    }
}

new HeadlessProACFFields();
