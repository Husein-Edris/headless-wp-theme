<?php

/**
 * Admin Customizations for Headless Pro Theme
 *
 * Provides dashboard pages, admin bar links, dashboard widget,
 * and admin notices for the headless CMS setup.
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
        add_action('admin_bar_menu', array($this, 'add_admin_bar_links'), 100);
        add_action('wp_dashboard_setup', array($this, 'add_dashboard_widgets'));
        add_action('admin_notices', array($this, 'show_headless_notices'));
    }

    /**
     * Add custom admin menu for headless management.
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

    // ------------------------------------------------------------------
    // Data methods
    // ------------------------------------------------------------------

    /**
     * Perform a live health check against the REST API.
     *
     * @return array{status: string, url: string, message: string}
     */
    public function check_rest_api_health(): array
    {
        $url = rest_url('wp/v2/types/post');

        $sslverify = apply_filters('https_local_ssl_verify', false);
        $response  = wp_remote_get($url, array(
            'timeout'   => 10,
            'sslverify' => $sslverify,
        ));

        if (is_wp_error($response)) {
            return array(
                'status'  => 'error',
                'url'     => rest_url(),
                'message' => 'Connection failed: ' . $response->get_error_message(),
            );
        }

        $code = wp_remote_retrieve_response_code($response);

        if (200 === $code) {
            return array(
                'status'  => 'active',
                'url'     => rest_url(),
                'message' => 'REST API is reachable',
            );
        }

        return array(
            'status'  => 'inactive',
            'url'     => rest_url(),
            'message' => 'Unexpected response code: ' . $code,
        );
    }

    /**
     * Perform a live health check against the GraphQL endpoint.
     *
     * @return array{status: string, url: string, message: string}
     */
    public function check_graphql_health(): array
    {
        if (!class_exists('WPGraphQL')) {
            return array(
                'status'  => 'not_installed',
                'url'     => '',
                'message' => 'WPGraphQL plugin is not active',
            );
        }

        $url = function_exists('graphql_get_endpoint_url')
            ? graphql_get_endpoint_url()
            : site_url('/graphql');

        $sslverify = apply_filters('https_local_ssl_verify', false);
        $response  = wp_remote_get($url, array(
            'timeout'   => 10,
            'sslverify' => $sslverify,
        ));

        if (is_wp_error($response)) {
            return array(
                'status'  => 'error',
                'url'     => $url,
                'message' => 'Connection failed: ' . $response->get_error_message(),
            );
        }

        $code = wp_remote_retrieve_response_code($response);

        if ($code >= 200 && $code < 500) {
            return array(
                'status'  => 'active',
                'url'     => $url,
                'message' => 'GraphQL endpoint is reachable',
            );
        }

        return array(
            'status'  => 'inactive',
            'url'     => $url,
            'message' => 'Unexpected response code: ' . $code,
        );
    }

    /**
     * Get stats for all registered public post types.
     *
     * @return array<int, array>
     */
    public function get_content_type_stats(): array
    {
        $post_types = get_post_types(array('public' => true), 'objects');
        $stats      = array();

        foreach ($post_types as $pto) {
            $counts = wp_count_posts($pto->name);

            // Attachments use 'inherit' status, not 'publish'.
            $published = ($pto->name === 'attachment')
                ? (int) ($counts->inherit ?? 0)
                : (int) ($counts->publish ?? 0);

            $stats[] = array(
                'name'                => $pto->name,
                'label'               => $pto->labels->name,
                'published_count'     => $published,
                'draft_count'         => (int) ($counts->draft ?? 0),
                'show_in_rest'        => !empty($pto->show_in_rest),
                'rest_base'           => $pto->rest_base ?: $pto->name,
                'show_in_graphql'     => !empty($pto->show_in_graphql),
                'graphql_single_name' => $pto->graphql_single_name ?? null,
                'graphql_plural_name' => $pto->graphql_plural_name ?? null,
                'edit_url'            => admin_url('edit.php?post_type=' . $pto->name),
            );
        }

        return $stats;
    }

    /**
     * Get the current CORS and environment configuration.
     *
     * @return array{origins: string[], environment_type: string, acf_admin_visible: bool, frontend_url: string}
     */
    public function get_cors_config(): array
    {
        $origins = apply_filters(
            'headless_pro_allowed_origins',
            HeadlessProConfig::get_allowed_origins()
        );

        return array(
            'origins'           => array_map('trim', $origins),
            'environment_type'  => wp_get_environment_type(),
            'acf_admin_visible' => (bool) apply_filters('acf/settings/show_admin', true),
            'frontend_url'      => HeadlessProConfig::get_frontend_url(),
        );
    }

    /**
     * Check for missing requirements and return notices.
     *
     * @return array<int, array{type: string, message: string, dismissible: bool, context: string}>
     */
    public function get_missing_requirements(): array
    {
        $notices = array();

        if (!class_exists('WPGraphQL')) {
            $notices[] = array(
                'type'        => 'error',
                'message'     => 'Headless Pro requires the <strong>WPGraphQL</strong> plugin. Please install and activate it.',
                'dismissible' => false,
                'context'     => 'all',
            );
        }

        if (!class_exists('ACF')) {
            $notices[] = array(
                'type'        => 'error',
                'message'     => 'Headless Pro requires <strong>Advanced Custom Fields PRO</strong>. Please install and activate it.',
                'dismissible' => false,
                'context'     => 'all',
            );
        }

        if (!class_exists('WPGraphQLAcf')) {
            $notices[] = array(
                'type'        => 'warning',
                'message'     => 'Headless Pro recommends the <strong>WPGraphQL for ACF</strong> plugin to expose custom fields in GraphQL.',
                'dismissible' => false,
                'context'     => 'all',
            );
        }

        $permalink_structure = get_option('permalink_structure');
        if ($permalink_structure !== '/%postname%/') {
            $notices[] = array(
                'type'        => 'warning',
                'message'     => 'Headless Pro works best with <strong>Post name</strong> permalinks. <a href="' . esc_url(admin_url('options-permalink.php')) . '">Update permalink settings</a>.',
                'dismissible' => true,
                'context'     => 'all',
            );
        }

        if (!defined('HEADLESS_FRONTEND_URL')) {
            $notices[] = array(
                'type'        => 'info',
                'message'     => 'Define <code>HEADLESS_FRONTEND_URL</code> in <code>wp-config.php</code> for accurate frontend integration.',
                'dismissible' => true,
                'context'     => 'headless-pro',
            );
        }

        return $notices;
    }

    // ------------------------------------------------------------------
    // Render methods
    // ------------------------------------------------------------------

    /**
     * Main admin page.
     */
    public function admin_page()
    {
        $config       = $this->get_cors_config();
        $rest_health  = $this->check_rest_api_health();
        $gql_health   = $this->check_graphql_health();
        ?>
<div class="wrap">
    <h1><span class="dashicons dashicons-rest-api"></span> Headless Pro Settings</h1>

    <div class="headless-admin-grid">
        <div class="headless-card">
            <h2><span class="dashicons dashicons-chart-pie"></span> API Status</h2>
            <p>Monitor your REST API and GraphQL endpoints.</p>
            <a href="<?php echo esc_url(admin_url('admin.php?page=headless-api-status')); ?>" class="button button-primary">View API Status</a>
        </div>

        <div class="headless-card">
            <h2><span class="dashicons dashicons-admin-tools"></span> Content Management</h2>
            <p>Manage your custom post types and content structure.</p>
            <a href="<?php echo esc_url(admin_url('admin.php?page=headless-content')); ?>" class="button button-primary">Manage Content</a>
        </div>

        <div class="headless-card">
            <h2><span class="dashicons dashicons-admin-links"></span> Frontend URL</h2>
            <p>Current frontend: <strong><?php echo esc_html($config['frontend_url']); ?></strong></p>
        </div>

        <div class="headless-card">
            <h2><span class="dashicons dashicons-admin-settings"></span> Configuration</h2>
            <p>
                <strong>Environment:</strong>
                <span class="env-badge env-<?php echo esc_attr($config['environment_type']); ?>">
                    <?php echo esc_html(ucfirst($config['environment_type'])); ?>
                </span>
            </p>
            <p>
                <strong>ACF Admin:</strong>
                <?php echo $config['acf_admin_visible'] ? 'Visible' : 'Hidden'; ?>
            </p>
        </div>
    </div>

    <div class="headless-admin-grid" style="margin-top: 0;">
        <div class="headless-card">
            <h3>REST API</h3>
            <p class="health-status health-<?php echo esc_attr($rest_health['status']); ?>">
                <?php $this->render_status_icon($rest_health['status']); ?>
                <?php echo esc_html(ucfirst($rest_health['status'])); ?>
            </p>
            <?php if ($rest_health['url']) : ?>
            <p><code><?php echo esc_html($rest_health['url']); ?></code></p>
            <?php endif; ?>
        </div>
        <div class="headless-card">
            <h3>GraphQL</h3>
            <p class="health-status health-<?php echo esc_attr($gql_health['status']); ?>">
                <?php $this->render_status_icon($gql_health['status']); ?>
                <?php echo esc_html(ucfirst($gql_health['status'])); ?>
            </p>
            <?php if ($gql_health['url']) : ?>
            <p><code><?php echo esc_html($gql_health['url']); ?></code></p>
            <?php endif; ?>
        </div>
        <div class="headless-card">
            <h3>ACF PRO</h3>
            <p class="health-status health-<?php echo class_exists('ACF') ? 'active' : 'inactive'; ?>">
                <?php $this->render_status_icon(class_exists('ACF') ? 'active' : 'inactive'); ?>
                <?php echo class_exists('ACF') ? 'Active' : 'Inactive'; ?>
            </p>
        </div>
    </div>

    <div class="headless-quick-stats">
        <h2><span class="dashicons dashicons-chart-pie"></span> Quick Stats</h2>
        <div class="stats-grid">
            <?php $this->render_quick_stats(); ?>
        </div>
    </div>
</div>

<?php $this->render_admin_styles(); ?>
<?php
    }

    /**
     * API Status page.
     */
    public function api_status_page()
    {
        $rest_health = $this->check_rest_api_health();
        $gql_health  = $this->check_graphql_health();
        $config      = $this->get_cors_config();
        ?>
<div class="wrap">
    <h1><span class="dashicons dashicons-chart-pie"></span> API Status</h1>

    <div class="api-status-grid">
        <?php $this->render_api_status(); ?>
    </div>

    <div class="api-endpoints">
        <h2><span class="dashicons dashicons-admin-links"></span> API Endpoints</h2>
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
                    <td><code><?php echo esc_html(rest_url()); ?></code></td>
                    <td>
                        <?php $this->render_status_icon($rest_health['status']); ?>
                        <?php echo esc_html(ucfirst($rest_health['status'])); ?>
                    </td>
                    <td><a href="<?php echo esc_url(rest_url()); ?>" target="_blank" class="button button-small">Test</a></td>
                </tr>
                <tr>
                    <td>GraphQL</td>
                    <td><code><?php echo esc_html($gql_health['url'] ?: 'Not available'); ?></code></td>
                    <td>
                        <?php $this->render_status_icon($gql_health['status']); ?>
                        <?php echo esc_html(ucfirst($gql_health['status'])); ?>
                    </td>
                    <td>
                        <?php if ($gql_health['url']) : ?>
                        <a href="<?php echo esc_url($gql_health['url']); ?>" target="_blank" class="button button-small">Test</a>
                        <?php else : ?>
                        <span class="description">N/A</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>WPGraphQL for ACF</td>
                    <td><code>&#8212;</code></td>
                    <td>
                        <?php $this->render_status_icon(class_exists('WPGraphQLAcf') ? 'active' : 'not_installed'); ?>
                        <?php echo class_exists('WPGraphQLAcf') ? 'Active' : 'Not installed'; ?>
                    </td>
                    <td><span class="description">Bridge plugin</span></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="cors-config" style="margin-top: 20px;">
        <h2><span class="dashicons dashicons-shield"></span> CORS Allowed Origins</h2>
        <ul class="cors-origins-list">
            <?php foreach ($config['origins'] as $origin) : ?>
            <li><code><?php echo esc_html(trim($origin)); ?></code></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<?php $this->render_admin_styles(); ?>
<?php
    }

    /**
     * Content Management page.
     */
    public function content_management_page()
    {
        ?>
<div class="wrap">
    <h1><span class="dashicons dashicons-admin-tools"></span> Content Management</h1>

    <div class="content-types-grid">
        <?php $this->render_content_types(); ?>
    </div>
</div>

<?php $this->render_admin_styles(); ?>
<?php
    }

    /**
     * Render quick stats for all registered public post types.
     */
    private function render_quick_stats()
    {
        $stats = $this->get_content_type_stats();

        foreach ($stats as $type) {
            echo '<div class="stat-item">';
            echo '<div class="stat-number">' . esc_html($type['published_count']) . '</div>';
            echo '<div class="stat-label">' . esc_html($type['label']) . '</div>';
            echo '</div>';
        }
    }

    /**
     * Render API status cards with live health checks.
     */
    private function render_api_status()
    {
        $checks = array(
            'REST API' => $this->check_rest_api_health(),
            'GraphQL'  => $this->check_graphql_health(),
            'ACF PRO'  => array(
                'status'  => class_exists('ACF') ? 'active' : 'inactive',
                'message' => class_exists('ACF') ? 'ACF PRO is active' : 'ACF PRO is not active',
            ),
        );

        foreach ($checks as $label => $check) {
            $status_class = 'status-' . esc_attr($check['status']);
            ?>
<div class="api-status-item <?php echo $status_class; ?>">
    <h3><?php echo esc_html($label); ?></h3>
    <p class="status">
        <?php $this->render_status_icon($check['status']); ?>
        <?php echo esc_html(ucfirst($check['status'])); ?>
    </p>
</div>
<?php
        }
    }

    /**
     * Render content types with REST/GraphQL metadata.
     */
    private function render_content_types()
    {
        $stats = $this->get_content_type_stats();

        foreach ($stats as $type) {
            echo '<div class="headless-card">';
            echo '<h3><span class="dashicons dashicons-admin-post"></span> ' . esc_html($type['label']) . '</h3>';
            echo '<p>Published: <strong>' . esc_html($type['published_count']) . '</strong>';
            if ($type['draft_count'] > 0) {
                echo ' &middot; Drafts: ' . esc_html($type['draft_count']);
            }
            echo '</p>';

            if ($type['show_in_rest']) {
                echo '<p><span class="dashicons dashicons-yes-alt" style="color:#00a32a;"></span> REST: <code>/' . esc_html($type['rest_base']) . '</code></p>';
            }

            if ($type['show_in_graphql']) {
                echo '<p><span class="dashicons dashicons-yes-alt" style="color:#00a32a;"></span> GraphQL: ';
                echo '<code>' . esc_html($type['graphql_single_name']) . '</code> / ';
                echo '<code>' . esc_html($type['graphql_plural_name']) . '</code></p>';
            }

            echo '<a href="' . esc_url($type['edit_url']) . '" class="button">Manage</a>';
            echo '</div>';
        }
    }

    /**
     * Render a dashicon status indicator.
     *
     * @param string $status One of: active, inactive, error, not_installed.
     */
    private function render_status_icon(string $status)
    {
        switch ($status) {
            case 'active':
                echo '<span class="dashicons dashicons-yes-alt" style="color:#00a32a;"></span>';
                break;
            case 'inactive':
            case 'error':
                echo '<span class="dashicons dashicons-no" style="color:#d63638;"></span>';
                break;
            case 'not_installed':
                echo '<span class="dashicons dashicons-warning" style="color:#dba617;"></span>';
                break;
            default:
                echo '<span class="dashicons dashicons-minus"></span>';
        }
    }

    // ------------------------------------------------------------------
    // Admin bar, dashboard widget, notices
    // ------------------------------------------------------------------

    /**
     * Add links to admin bar.
     */
    public function add_admin_bar_links($wp_admin_bar)
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $wp_admin_bar->add_node(array(
            'id'    => 'headless-pro',
            'title' => '<span class="ab-icon dashicons dashicons-rest-api"></span> Headless Pro',
            'href'  => admin_url('admin.php?page=headless-pro'),
        ));

        $wp_admin_bar->add_node(array(
            'id'     => 'headless-rest',
            'parent' => 'headless-pro',
            'title'  => 'REST API',
            'href'   => rest_url(),
        ));
    }

    /**
     * Add dashboard widgets.
     */
    public function add_dashboard_widgets()
    {
        wp_add_dashboard_widget(
            'headless_pro_status',
            'Headless Pro Status',
            array($this, 'dashboard_widget_status')
        );
    }

    /**
     * Dashboard widget content.
     */
    public function dashboard_widget_status()
    {
        $frontend_url    = HeadlessProConfig::get_frontend_url();
        $environment     = wp_get_environment_type();
        ?>
<div class="headless-dashboard-widget">
    <p><strong>Theme:</strong> Headless Pro v<?php echo esc_html(HEADLESS_THEME_VERSION); ?></p>
    <p><strong>Frontend URL:</strong>
        <a href="<?php echo esc_url($frontend_url); ?>" target="_blank"><?php echo esc_html($frontend_url); ?></a>
    </p>
    <p><strong>Environment:</strong> <?php echo esc_html(ucfirst($environment)); ?></p>
    <div style="margin-top: 15px;">
        <a href="<?php echo esc_url(admin_url('admin.php?page=headless-pro')); ?>" class="button button-primary">Dashboard</a>
    </div>
</div>
<?php
    }

    /**
     * Show admin notices for missing requirements.
     */
    public function show_headless_notices()
    {
        $notices = $this->get_missing_requirements();

        if (empty($notices)) {
            return;
        }

        $screen = get_current_screen();

        foreach ($notices as $notice) {
            // Filter by context.
            if ($notice['context'] === 'headless-pro') {
                if (!$screen || strpos($screen->id, 'headless') === false) {
                    continue;
                }
            }

            $classes = 'notice notice-' . esc_attr($notice['type']);
            if ($notice['dismissible']) {
                $classes .= ' is-dismissible';
            }

            echo '<div class="' . $classes . '">';
            echo '<p><strong>Headless Pro:</strong> ' . wp_kses_post($notice['message']) . '</p>';
            echo '</div>';
        }
    }

    // ------------------------------------------------------------------
    // Shared inline styles
    // ------------------------------------------------------------------

    /**
     * Render admin CSS inline (no external stylesheet).
     */
    private function render_admin_styles()
    {
        ?>
<style>
.headless-admin-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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

.headless-card h2,
.headless-card h3 {
    margin-top: 0;
    color: #1d2327;
}

.headless-card h2 .dashicons,
.headless-card h3 .dashicons,
h1 .dashicons,
h2 .dashicons {
    vertical-align: middle;
    margin-right: 4px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
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

.content-types-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.api-status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin: 20px 0;
}

.api-status-item {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 8px;
    padding: 15px;
}

.api-status-item .status .dashicons {
    vertical-align: middle;
    margin-right: 4px;
}

.health-status .dashicons {
    vertical-align: middle;
    margin-right: 4px;
}

.env-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.env-production {
    background: #d63638;
    color: #fff;
}

.env-staging {
    background: #dba617;
    color: #fff;
}

.env-development {
    background: #00a32a;
    color: #fff;
}

.env-local {
    background: #2271b1;
    color: #fff;
}

.cors-origins-list {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 6px;
    padding: 15px 15px 15px 35px;
    margin-top: 10px;
}

.cors-origins-list li {
    margin-bottom: 6px;
}
</style>
<?php
    }
}

// Initialize admin customizations
new HeadlessProAdmin();
