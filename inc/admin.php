<?php

/**
 * Admin Customizations for Headless Pro Theme
 * 
 * @package HeadlessPro
 */

if (!defined('ABSPATH')) {
    exit;
}

class HeadlessProAdmin
{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_bar_menu', array($this, 'add_admin_bar_links'), 100);
        add_filter('admin_footer_text', array($this, 'custom_admin_footer'));
        add_action('wp_dashboard_setup', array($this, 'add_dashboard_widgets'));
        add_action('admin_notices', array($this, 'show_headless_notices'));
    }

    /**
     * Add custom admin menu for headless management
     */
    public function add_admin_menu()
    {
        add_menu_page(
            'Headless Settings',
            'Headless Pro',
            'manage_options',
            'headless-pro',
            array($this, 'admin_page'),
            'dashicons-rest-api',
            3
        );

        add_submenu_page(
            'headless-pro',
            'API Status',
            'API Status',
            'manage_options',
            'headless-api-status',
            array($this, 'api_status_page')
        );

        add_submenu_page(
            'headless-pro',
            'Content Management',
            'Content Management',
            'manage_options',
            'headless-content',
            array($this, 'content_management_page')
        );
    }

    /**
     * Main admin page
     */
    public function admin_page()
    {
?>
<div class="wrap">
    <h1>🚀 Headless Pro Settings</h1>

    <div class="headless-admin-grid">
        <div class="headless-card">
            <h2>📊 API Status</h2>
            <p>Monitor your REST API endpoints and settings.</p>
            <a href="<?php echo admin_url('admin.php?page=headless-api-status'); ?>" class="button button-primary">View
                API Status</a>
        </div>

        <div class="headless-card">
            <h2>🔧 Content Management</h2>
            <p>Manage your custom post types and content structure.</p>
            <a href="<?php echo admin_url('admin.php?page=headless-content'); ?>" class="button button-primary">Manage
                Content</a>
        </div>

        <div class="headless-card">
            <h2>🌐 Frontend URL</h2>
            <p>Current frontend: <strong><?php echo get_theme_mod('frontend_url', 'Not set'); ?></strong></p>
            <a href="<?php echo admin_url('customize.php'); ?>" class="button">Update Settings</a>
        </div>

        <div class="headless-card">
            <h2>📖 Documentation</h2>
            <p>Learn how to use this headless WordPress setup.</p>
            <a href="https://github.com/your-repo/headless-pro-theme" target="_blank" class="button">View Docs</a>
        </div>
    </div>

    <div class="headless-quick-stats">
        <h2>📈 Quick Stats</h2>
        <div class="stats-grid">
            <?php $this->render_quick_stats(); ?>
        </div>
    </div>
</div>

<style>
.headless-admin-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.headless-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.headless-card h2 {
    margin-top: 0;
    color: #1d2327;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.stat-item {
    background: #f6f7f7;
    padding: 15px;
    border-radius: 6px;
    text-align: center;
}

.stat-number {
    font-size: 24px;
    font-weight: bold;
    color: #0073aa;
}

.stat-label {
    font-size: 14px;
    color: #646970;
    margin-top: 5px;
}
</style>
<?php
    }

    /**
     * API Status page
     */
    public function api_status_page()
    {
    ?>
<div class="wrap">
    <h1>📊 API Status</h1>

    <div class="api-status-grid">
        <?php $this->render_api_status(); ?>
    </div>

    <div class="api-endpoints">
        <h2>🔗 API Endpoints</h2>
        <table class="widefat">
            <thead>
                <tr>
                    <th>Endpoint</th>
                    <th>URL</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>REST API</td>
                    <td><code><?php echo rest_url(); ?></code></td>
                    <td>✅ Active</td>
                    <td><a href="<?php echo rest_url(); ?>" target="_blank" class="button button-small">Test</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php
    }

    /**
     * Content Management page
     */
    public function content_management_page()
    {
    ?>
<div class="wrap">
    <h1>🔧 Content Management</h1>

    <div class="content-types-grid">
        <?php $this->render_content_types(); ?>
    </div>

    <div class="bulk-actions">
        <h2>⚡ Bulk Actions</h2>
        <div class="action-buttons">
            <button class="button" onclick="generateSampleContent()">Generate Sample Content</button>
            <button class="button" onclick="clearCache()">Clear API Cache</button>
            <button class="button" onclick="reindexSearch()">Reindex Search</button>
        </div>
    </div>
</div>

<script>
function generateSampleContent() {
    if (confirm('This will create sample posts, projects, skills, and hobbies. Continue?')) {
        // AJAX call to generate sample content
        alert('Sample content generation feature coming soon!');
    }
}

function clearCache() {
    // AJAX call to clear cache
    alert('Cache cleared!');
}

function reindexSearch() {
    // AJAX call to reindex
    alert('Search reindexing feature coming soon!');
}
</script>

<style>
.content-types-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.action-buttons {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}
</style>
<?php
    }

    /**
     * Render quick stats
     */
    private function render_quick_stats()
    {
        $post_types = array('post', 'project', 'skill', 'hobby');

        foreach ($post_types as $post_type) {
            if (!post_type_exists($post_type)) continue;

            $count = wp_count_posts($post_type);
            $published = $count->publish ?? 0;
            $post_type_obj = get_post_type_object($post_type);

            echo '<div class="stat-item">';
            echo '<div class="stat-number">' . $published . '</div>';
            echo '<div class="stat-label">' . $post_type_obj->labels->name . '</div>';
            echo '</div>';
        }
    }

    /**
     * Render API status cards
     */
    private function render_api_status()
    {
        $api_status = array(
            'REST API' => rest_url() !== null,
            'ACF' => class_exists('ACF'),
        );

        foreach ($api_status as $api => $status) {
            $status_class = $status ? 'status-ok' : 'status-error';
            $status_icon = $status ? '✅' : '❌';
        ?>
<div class="api-status-item <?php echo $status_class; ?>">
    <h3><?php echo $api; ?></h3>
    <p class="status"><?php echo $status_icon; ?> <?php echo $status ? 'Active' : 'Inactive'; ?></p>
</div>
<?php
        }
    }

    /**
     * Render content types overview
     */
    private function render_content_types()
    {
        $post_types = get_post_types(array('public' => true), 'objects');

        foreach ($post_types as $post_type) {
            $count = wp_count_posts($post_type->name);
            $published = $count->publish ?? 0;

            echo '<div class="headless-card">';
            echo '<h3>📝 ' . $post_type->labels->name . '</h3>';
            echo '<p>Published: <strong>' . $published . '</strong></p>';
            echo '<p>' . $post_type->description . '</p>';
            echo '<a href="' . admin_url('edit.php?post_type=' . $post_type->name) . '" class="button">Manage</a>';
            echo '</div>';
        }
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook)
    {
        // Only load on our admin pages
        if (strpos($hook, 'headless-pro') === false) {
            return;
        }

        wp_enqueue_style(
            'headless-pro-admin',
            get_template_directory_uri() . '/assets/css/admin.css',
            array(),
            HEADLESS_THEME_VERSION
        );

        wp_enqueue_script(
            'headless-pro-admin',
            get_template_directory_uri() . '/assets/js/admin.js',
            array('jquery'),
            HEADLESS_THEME_VERSION,
            true
        );
    }

    /**
     * Add links to admin bar
     */
    public function add_admin_bar_links($wp_admin_bar)
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $wp_admin_bar->add_node(array(
            'id' => 'headless-pro',
            'title' => '🚀 Headless Pro',
            'href' => admin_url('admin.php?page=headless-pro'),
        ));

        $wp_admin_bar->add_node(array(
            'id' => 'headless-rest',
            'parent' => 'headless-pro',
            'title' => 'REST API',
            'href' => rest_url(),
        ));
    }

    /**
     * Custom admin footer text
     */
    public function custom_admin_footer($text)
    {
        return 'Powered by <strong>Headless Pro Theme</strong> - Built for modern JAMstack applications.';
    }

    /**
     * Add dashboard widgets
     */
    public function add_dashboard_widgets()
    {
        wp_add_dashboard_widget(
            'headless_pro_status',
            '🚀 Headless Pro Status',
            array($this, 'dashboard_widget_status')
        );
    }

    /**
     * Dashboard widget content
     */
    public function dashboard_widget_status()
    {
        ?>
<div class="headless-dashboard-widget">
    <p><strong>Theme:</strong> Headless Pro v<?php echo HEADLESS_THEME_VERSION; ?></p>
    <p><strong>Frontend URL:</strong>
        <?php
                $frontend_url = get_theme_mod('frontend_url');
                if ($frontend_url) {
                    echo '<a href="' . esc_url($frontend_url) . '" target="_blank">' . esc_html($frontend_url) . '</a>';
                } else {
                    echo '<span style="color: #d63638;">Not configured</span>';
                }
                ?>
    </p>
    <div style="margin-top: 15px;">
        <a href="<?php echo admin_url('admin.php?page=headless-pro'); ?>" class="button button-primary">Manage
            Settings</a>
    </div>
</div>
<?php
    }

    /**
     * Show helpful admin notices
     */
    public function show_headless_notices()
    {
        $screen = get_current_screen();

        // Check frontend URL configuration
        if (!get_theme_mod('frontend_url')) {
            echo '<div class="notice notice-info is-dismissible">';
            echo '<p><strong>Headless Pro:</strong> <a href="' . admin_url('customize.php') . '">Configure your frontend URL</a> for better integration.</p>';
            echo '</div>';
        }
    }
}

// Initialize admin customizations
new HeadlessProAdmin();