<?php

/**
 * Drag-and-drop ordering for the Projects custom post type.
 *
 * Adds a sortable Projects list in wp-admin. Dragging a row persists the new
 * order to each project's `menu_order`, which the headless frontend reads via
 * WPGraphQL ( orderby: { field: MENU_ORDER, order: ASC } ). The top three in
 * this order are the "featured" projects shown on the homepage.
 *
 * No third-party plugin required — the whole feature lives in the theme so it
 * ships through the normal git push / pull-on-live workflow.
 *
 * @package HeadlessPro
 */

if (!defined('ABSPATH')) {
    exit;
}

class HeadlessProProjectOrder
{
    /**
     * Post type this feature applies to.
     */
    const POST_TYPE = 'project';

    /**
     * Capability required to reorder projects.
     */
    const CAPABILITY = 'edit_others_posts';

    /**
     * Nonce action / AJAX action name (kept identical for clarity).
     */
    const ACTION = 'headless_pro_reorder_projects';

    /**
     * Max projects shown on one admin page so drag ordering spans the full set.
     */
    const ADMIN_PER_PAGE = 100;

    public function __construct()
    {
        add_action('pre_get_posts', array($this, 'order_admin_list'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('admin_notices', array($this, 'show_reorder_hint'));
        add_action('wp_ajax_' . self::ACTION, array($this, 'handle_reorder'));
    }

    /**
     * Default the admin Projects list to menu_order (ascending) and show the
     * whole set on one page, so the drag order is global rather than per-page.
     */
    public function order_admin_list($query): void
    {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        if ($query->get('post_type') !== self::POST_TYPE) {
            return;
        }

        // Respect an explicit column sort chosen by the user.
        if (isset($_GET['orderby'])) {
            return;
        }

        $query->set('orderby', 'menu_order');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', self::ADMIN_PER_PAGE);
    }

    /**
     * Whether the current admin request is the sortable Projects list view.
     */
    private function is_sortable_screen($hook): bool
    {
        if ($hook !== 'edit.php') {
            return false;
        }

        $screen = get_current_screen();
        if (!$screen || $screen->post_type !== self::POST_TYPE) {
            return false;
        }

        if (!current_user_can(self::CAPABILITY)) {
            return false;
        }

        // Only allow dragging in the default, unfiltered menu_order view —
        // a column sort or search makes drag positions ambiguous.
        if (isset($_GET['orderby']) || !empty($_GET['s'])) {
            return false;
        }

        return true;
    }

    /**
     * Enqueue the sortable script + styles on the Projects list screen.
     */
    public function enqueue_assets($hook): void
    {
        if (!$this->is_sortable_screen($hook)) {
            return;
        }

        wp_enqueue_style(
            'headless-pro-project-order',
            HEADLESS_THEME_URL . '/assets/css/project-order.css',
            array(),
            HEADLESS_THEME_VERSION
        );

        wp_enqueue_script(
            'headless-pro-project-order',
            HEADLESS_THEME_URL . '/assets/js/project-order.js',
            array('jquery', 'jquery-ui-sortable'),
            HEADLESS_THEME_VERSION,
            true
        );

        wp_localize_script(
            'headless-pro-project-order',
            'headlessProProjectOrder',
            array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'action'  => self::ACTION,
                'nonce'   => wp_create_nonce(self::ACTION),
            )
        );
    }

    /**
     * One-line hint above the sortable list so editors know it's draggable.
     */
    public function show_reorder_hint(): void
    {
        $screen = get_current_screen();
        if (!$screen || $screen->id !== 'edit-' . self::POST_TYPE) {
            return;
        }

        if (!$this->is_sortable_screen('edit.php')) {
            return;
        }

        echo '<div class="notice notice-info"><p><strong>Headless Pro:</strong> '
            . 'Drag the projects into the order you want. The top three appear as the featured projects on the homepage. The order saves automatically.'
            . '</p></div>';
    }

    /**
     * Persist a new project order. Receives an array of post IDs in display
     * order and writes a sequential menu_order to each.
     */
    public function handle_reorder(): void
    {
        if (!current_user_can(self::CAPABILITY)) {
            wp_send_json_error(array('message' => 'Insufficient permissions.'), 403);
        }

        check_ajax_referer(self::ACTION, 'nonce');

        $raw_order = isset($_POST['order']) ? wp_unslash($_POST['order']) : array();
        if (!is_array($raw_order)) {
            wp_send_json_error(array('message' => 'Invalid order payload.'), 400);
        }

        $ids = array_values(array_filter(array_map('absint', $raw_order)));
        if (empty($ids)) {
            wp_send_json_error(array('message' => 'No projects to reorder.'), 400);
        }

        global $wpdb;
        $position = 0;
        foreach ($ids as $id) {
            // Verify the ID is actually a project before touching it.
            if (get_post_type($id) !== self::POST_TYPE) {
                continue;
            }

            // Direct update avoids bumping post_modified / creating revisions on
            // a pure reorder; clean_post_cache keeps object + GraphQL caches fresh.
            $wpdb->update($wpdb->posts, array('menu_order' => $position), array('ID' => $id));
            clean_post_cache($id);
            $position++;
        }

        wp_send_json_success(array('updated' => $position));
    }
}

new HeadlessProProjectOrder();
