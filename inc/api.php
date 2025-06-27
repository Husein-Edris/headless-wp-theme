<?php

/**
 * API Enhancements for Headless Pro Theme
 * 
 * @package HeadlessPro
 */

if (!defined('ABSPATH')) {
    exit;
}

class HeadlessProAPI
{

    public function __construct()
    {
        add_action('rest_api_init', array($this, 'register_custom_endpoints'));
        add_action('rest_api_init', array($this, 'modify_default_endpoints'));
        add_filter('rest_prepare_post', array($this, 'add_custom_post_fields'), 10, 3);
        add_filter('rest_prepare_page', array($this, 'add_custom_page_fields'), 10, 3);
        add_action('init', array($this, 'add_cors_headers'));
    }

    /**
     * Register custom REST API endpoints
     */
    public function register_custom_endpoints()
    {
        // Site information endpoint
        register_rest_route('headless/v1', '/site-info', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_site_info'),
            'permission_callback' => '__return_true',
        ));

        // Navigation menus endpoint
        register_rest_route('headless/v1', '/menus', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_menus'),
            'permission_callback' => '__return_true',
        ));

        // Search endpoint with advanced filters
        register_rest_route('headless/v1', '/search', array(
            'methods' => 'GET',
            'callback' => array($this, 'advanced_search'),
            'permission_callback' => '__return_true',
            'args' => array(
                'query' => array(
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'post_type' => array(
                    'default' => 'any',
                    'type' => 'string',
                ),
                'limit' => array(
                    'default' => 10,
                    'type' => 'integer',
                ),
            ),
        ));

        // Related posts endpoint
        register_rest_route('headless/v1', '/posts/(?P<id>\d+)/related', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_related_posts'),
            'permission_callback' => '__return_true',
            'args' => array(
                'id' => array(
                    'required' => true,
                    'type' => 'integer',
                ),
                'limit' => array(
                    'default' => 3,
                    'type' => 'integer',
                ),
            ),
        ));

        // Popular posts endpoint
        register_rest_route('headless/v1', '/posts/popular', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_popular_posts'),
            'permission_callback' => '__return_true',
            'args' => array(
                'limit' => array(
                    'default' => 5,
                    'type' => 'integer',
                ),
                'time_range' => array(
                    'default' => 'all',
                    'type' => 'string',
                ),
            ),
        ));

        // Contact form endpoint
        register_rest_route('headless/v1', '/contact', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_contact_form'),
            'permission_callback' => array($this, 'verify_contact_nonce'),
            'args' => array(
                'name' => array(
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'email' => array(
                    'required' => true,
                    'type' => 'string',
                    'validate_callback' => 'is_email',
                ),
                'message' => array(
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_textarea_field',
                ),
                'nonce' => array(
                    'required' => true,
                    'type' => 'string',
                ),
            ),
        ));
    }

    /**
     * Get site information
     */
    public function get_site_info()
    {
        return array(
            'name' => get_bloginfo('name'),
            'description' => get_bloginfo('description'),
            'url' => home_url(),
            'language' => get_locale(),
            'timezone' => get_option('timezone_string'),
            'date_format' => get_option('date_format'),
            'time_format' => get_option('time_format'),
            'theme' => array(
                'name' => wp_get_theme()->get('Name'),
                'version' => wp_get_theme()->get('Version'),
            ),
            'wordpress_version' => get_bloginfo('version'),
            'api_endpoints' => $this->get_api_endpoints(),
        );
    }

    /**
     * Get navigation menus
     */
    public function get_menus()
    {
        $menus = wp_get_nav_menus();
        $menu_data = array();

        foreach ($menus as $menu) {
            $menu_items = wp_get_nav_menu_items($menu->term_id);
            $structured_items = array();

            foreach ($menu_items as $item) {
                $structured_items[] = array(
                    'id' => $item->ID,
                    'title' => $item->title,
                    'url' => $item->url,
                    'target' => $item->target,
                    'parent' => $item->menu_item_parent,
                    'order' => $item->menu_order,
                    'classes' => $item->classes,
                );
            }

            $menu_data[] = array(
                'id' => $menu->term_id,
                'name' => $menu->name,
                'slug' => $menu->slug,
                'items' => $structured_items,
            );
        }

        return $menu_data;
    }

    /**
     * Advanced search functionality
     */
    public function advanced_search($request)
    {
        $query = $request['query'];
        $post_type = $request['post_type'];
        $limit = $request['limit'];

        $search_args = array(
            's' => $query,
            'post_status' => 'publish',
            'posts_per_page' => $limit,
        );

        if ($post_type !== 'any') {
            $search_args['post_type'] = $post_type;
        }

        $search_query = new WP_Query($search_args);
        $results = array();

        while ($search_query->have_posts()) {
            $search_query->the_post();
            $post = get_post();

            $results[] = array(
                'id' => $post->ID,
                'title' => get_the_title(),
                'excerpt' => get_the_excerpt(),
                'permalink' => get_permalink(),
                'post_type' => get_post_type(),
                'date' => get_the_date('c'),
                'featured_image' => get_the_post_thumbnail_url($post->ID, 'medium'),
            );
        }

        wp_reset_postdata();

        return array(
            'results' => $results,
            'total' => $search_query->found_posts,
            'query' => $query,
        );
    }

    /**
     * Get related posts
     */
    public function get_related_posts($request)
    {
        $post_id = $request['id'];
        $limit = $request['limit'];

        $categories = wp_get_post_categories($post_id);
        $tags = wp_get_post_tags($post_id, array('fields' => 'ids'));

        $related_args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            'post__not_in' => array($post_id),
        );

        if (!empty($categories)) {
            $related_args['category__in'] = $categories;
        } elseif (!empty($tags)) {
            $related_args['tag__in'] = $tags;
        }

        $related_query = new WP_Query($related_args);
        $related_posts = array();

        while ($related_query->have_posts()) {
            $related_query->the_post();
            $post = get_post();

            $related_posts[] = array(
                'id' => $post->ID,
                'title' => get_the_title(),
                'excerpt' => get_the_excerpt(),
                'permalink' => get_permalink(),
                'date' => get_the_date('c'),
                'featured_image' => get_the_post_thumbnail_url($post->ID, 'medium'),
                'author' => array(
                    'name' => get_the_author(),
                    'id' => get_the_author_meta('ID'),
                ),
            );
        }

        wp_reset_postdata();

        return $related_posts;
    }

    /**
     * Get popular posts
     */
    public function get_popular_posts($request)
    {
        $limit = $request['limit'];
        $time_range = $request['time_range'];

        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            'meta_query' => array(
                array(
                    'key' => 'post_views_count',
                    'compare' => 'EXISTS',
                ),
            ),
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
        );

        // Add date query based on time range
        if ($time_range !== 'all') {
            $date_query = array();

            switch ($time_range) {
                case 'week':
                    $date_query['after'] = '1 week ago';
                    break;
                case 'month':
                    $date_query['after'] = '1 month ago';
                    break;
                case 'year':
                    $date_query['after'] = '1 year ago';
                    break;
            }

            if (!empty($date_query)) {
                $args['date_query'] = array($date_query);
            }
        }

        $popular_query = new WP_Query($args);
        $popular_posts = array();

        while ($popular_query->have_posts()) {
            $popular_query->the_post();
            $post = get_post();

            $popular_posts[] = array(
                'id' => $post->ID,
                'title' => get_the_title(),
                'excerpt' => get_the_excerpt(),
                'permalink' => get_permalink(),
                'date' => get_the_date('c'),
                'featured_image' => get_the_post_thumbnail_url($post->ID, 'medium'),
                'views' => get_post_meta($post->ID, 'post_views_count', true) ?: 0,
                'author' => array(
                    'name' => get_the_author(),
                    'id' => get_the_author_meta('ID'),
                ),
            );
        }

        wp_reset_postdata();

        return $popular_posts;
    }

    /**
     * Handle contact form submissions
     */
    public function handle_contact_form($request)
    {
        $name = $request['name'];
        $email = $request['email'];
        $message = $request['message'];

        // Send email to admin
        $to = get_option('admin_email');
        $subject = sprintf('[%s] New Contact Form Submission', get_bloginfo('name'));
        $body = "Name: {$name}\nEmail: {$email}\n\nMessage:\n{$message}";
        $headers = array('Content-Type: text/plain; charset=UTF-8');

        $sent = wp_mail($to, $subject, $body, $headers);

        if ($sent) {
            return array(
                'success' => true,
                'message' => 'Thank you for your message. We will get back to you soon!',
            );
        } else {
            return new WP_Error('email_failed', 'Failed to send email. Please try again.', array('status' => 500));
        }
    }

    /**
     * Verify contact form nonce
     */
    public function verify_contact_nonce($request)
    {
        $nonce = $request['nonce'];
        return wp_verify_nonce($nonce, 'headless_contact_form');
    }

    /**
     * Add custom fields to post responses
     */
    public function add_custom_post_fields($response, $post, $request)
    {
        // Add reading time
        $content = get_post_field('post_content', $post->ID);
        $word_count = str_word_count(strip_tags($content));
        $reading_time = ceil($word_count / 200);

        $response->data['reading_time'] = $reading_time . ' min read';
        $response->data['plain_excerpt'] = wp_strip_all_tags(get_the_excerpt($post->ID));
        $response->data['author_avatar'] = get_avatar_url(get_the_author_meta('ID', $post->post_author));

        // Add view count if available
        $views = get_post_meta($post->ID, 'post_views_count', true);
        if ($views) {
            $response->data['views'] = intval($views);
        }

        return $response;
    }

    /**
     * Add custom fields to page responses
     */
    public function add_custom_page_fields($response, $page, $request)
    {
        // Add page-specific custom fields here
        $response->data['template'] = get_page_template_slug($page->ID);

        return $response;
    }

    /**
     * Modify default REST API endpoints
     */
    public function modify_default_endpoints()
    {
        // Add custom fields to default post type endpoints
        register_rest_field('post', 'acf_fields', array(
            'get_callback' => array($this, 'get_acf_fields'),
            'schema' => array(
                'description' => 'ACF Fields',
                'type' => 'object',
            ),
        ));

        register_rest_field('page', 'acf_fields', array(
            'get_callback' => array($this, 'get_acf_fields'),
            'schema' => array(
                'description' => 'ACF Fields',
                'type' => 'object',
            ),
        ));

        // Add custom fields to custom post types
        $custom_post_types = array('project', 'skill', 'hobby');
        foreach ($custom_post_types as $post_type) {
            if (post_type_exists($post_type)) {
                register_rest_field($post_type, 'acf_fields', array(
                    'get_callback' => array($this, 'get_acf_fields'),
                    'schema' => array(
                        'description' => 'ACF Fields',
                        'type' => 'object',
                    ),
                ));
            }
        }
    }

    /**
     * Get ACF fields for REST API
     */
    public function get_acf_fields($object)
    {
        if (!function_exists('get_fields')) {
            return array();
        }

        $fields = get_fields($object['id']);
        return $fields ?: array();
    }

    /**
     * Add CORS headers for API requests
     */
    public function add_cors_headers()
    {
        if (defined('REST_REQUEST') && REST_REQUEST) {
            $origin = get_http_origin();
            $allowed_origins = apply_filters('headless_pro_allowed_origins', array(
                'http://localhost:3000',
                'http://localhost:3001',
                'https://edrishusein.com',
            ));

            if (in_array($origin, $allowed_origins)) {
                header('Access-Control-Allow-Origin: ' . $origin);
            }

            header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With');
        }
    }

    /**
     * Get API endpoints
     */
    private function get_api_endpoints()
    {
        return array(
            'rest' => rest_url(),
            'rest_prefix' => rest_get_url_prefix(),
        );
    }

    /**
     * Get API status
     */
    private function get_api_status()
    {
        return array(
            'rest_api' => true,
            'acf' => class_exists('ACF'),
        );
    }
}

// Initialize API enhancements
new HeadlessProAPI();

/**
 * Add endpoint for generating nonces
 */
add_action('rest_api_init', function () {
    register_rest_route('headless/v1', '/nonce', array(
        'methods' => 'GET',
        'callback' => function () {
            return array(
                'contact_nonce' => wp_create_nonce('headless_contact_form'),
            );
        },
        'permission_callback' => '__return_true',
    ));
});

/**
 * Track post views for popular posts functionality
 */
function headless_pro_track_post_views($post_id)
{
    if (!is_single()) return;
    if (empty($post_id)) {
        global $post;
        $post_id = $post->ID;
    }

    $views = get_post_meta($post_id, 'post_views_count', true);
    $views = empty($views) ? 1 : $views + 1;
    update_post_meta($post_id, 'post_views_count', $views);
}
add_action('wp_head', 'headless_pro_track_post_views');
