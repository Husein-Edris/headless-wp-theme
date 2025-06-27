<?php
/**
 * GraphQL Enhancements for Headless Pro Theme
 * 
 * @package HeadlessPro
 */

if (!defined('ABSPATH')) {
    exit;
}

class HeadlessProGraphQL {
    
    public function __construct() {
        // Only initialize if WPGraphQL plugin is active
        add_action('plugins_loaded', array($this, 'init_graphql_features'));
    }
    
    /**
     * Initialize GraphQL features only if WPGraphQL is available
     */
    public function init_graphql_features() {
        if (!class_exists('WPGraphQL\WPGraphQL')) {
            return;
        }
        
        add_action('graphql_register_types', array($this, 'register_custom_fields'));
        add_action('graphql_register_types', array($this, 'register_custom_queries'));
        add_filter('graphql_cors_allowed_headers', array($this, 'add_cors_headers'));
        add_action('init', array($this, 'enable_graphql_introspection'));
    }
    
    /**
     * Register custom GraphQL fields
     */
    public function register_custom_fields() {
        if (!function_exists('register_graphql_field')) {
            return;
        }
        
        // Add reading time to posts
        register_graphql_field('Post', 'readingTime', array(
            'type' => 'String',
            'description' => 'Estimated reading time for the post',
            'resolve' => function($post) {
                $content = get_post_field('post_content', $post->ID);
                $word_count = str_word_count(strip_tags($content));
                $reading_time = ceil($word_count / 200); // Average reading speed
                return $reading_time . ' min read';
            }
        ));
        
        // Add excerpt without HTML tags
        register_graphql_field('Post', 'plainExcerpt', array(
            'type' => 'String',
            'description' => 'Post excerpt without HTML tags',
            'resolve' => function($post) {
                $excerpt = get_the_excerpt($post->ID);
                return wp_strip_all_tags($excerpt);
            }
        ));
        
        // Add author avatar URL
        register_graphql_field('User', 'avatarUrl', array(
            'type' => 'String',
            'description' => 'User avatar URL',
            'resolve' => function($user) {
                return get_avatar_url($user->ID, array('size' => 96));
            }
        ));
        
        // Add featured image alt text
        register_graphql_field('MediaItem', 'altText', array(
            'type' => 'String',
            'description' => 'Alternative text for the image',
            'resolve' => function($media_item) {
                return get_post_meta($media_item->ID, '_wp_attachment_image_alt', true);
            }
        ));
        
        // Add custom post type counts
        register_graphql_field('RootQuery', 'postTypeCounts', array(
            'type' => array('list_of' => 'PostTypeCount'),
            'description' => 'Count of posts for each post type',
            'resolve' => function() {
                $post_types = get_post_types(array('public' => true), 'objects');
                $counts = array();
                
                foreach ($post_types as $post_type) {
                    $count = wp_count_posts($post_type->name);
                    $counts[] = array(
                        'postType' => $post_type->name,
                        'count' => $count->publish ?? 0,
                        'label' => $post_type->label,
                    );
                }
                
                return $counts;
            }
        ));
    }
    
    /**
     * Register custom GraphQL types
     */
    public function register_custom_queries() {
        if (!function_exists('register_graphql_object_type')) {
            return;
        }
        
        // Register PostTypeCount type
        register_graphql_object_type('PostTypeCount', array(
            'description' => 'Post type count information',
            'fields' => array(
                'postType' => array(
                    'type' => 'String',
                    'description' => 'Post type name',
                ),
                'count' => array(
                    'type' => 'Int',
                    'description' => 'Number of published posts',
                ),
                'label' => array(
                    'type' => 'String',
                    'description' => 'Post type label',
                ),
            ),
        ));
        
        // Register custom query for related posts
        register_graphql_field('Post', 'relatedPosts', array(
            'type' => array('list_of' => 'Post'),
            'description' => 'Related posts based on categories',
            'args' => array(
                'limit' => array(
                    'type' => 'Int',
                    'description' => 'Number of related posts to return',
                    'defaultValue' => 3,
                ),
            ),
            'resolve' => function($post, $args) {
                $categories = wp_get_post_categories($post->ID);
                
                if (empty($categories)) {
                    return array();
                }
                
                $related_posts = get_posts(array(
                    'category__in' => $categories,
                    'post__not_in' => array($post->ID),
                    'posts_per_page' => $args['limit'],
                    'post_status' => 'publish',
                ));
                
                return $related_posts;
            }
        ));
        
        // Register custom query for popular posts
        register_graphql_field('RootQuery', 'popularPosts', array(
            'type' => array('list_of' => 'Post'),
            'description' => 'Most popular posts based on comments',
            'args' => array(
                'limit' => array(
                    'type' => 'Int',
                    'description' => 'Number of popular posts to return',
                    'defaultValue' => 5,
                ),
            ),
            'resolve' => function($root, $args) {
                $popular_posts = get_posts(array(
                    'posts_per_page' => $args['limit'],
                    'meta_key' => 'post_views_count',
                    'orderby' => 'meta_value_num',
                    'order' => 'DESC',
                    'post_status' => 'publish',
                ));
                
                // Fallback to comment count if no view count
                if (empty($popular_posts)) {
                    $popular_posts = get_posts(array(
                        'posts_per_page' => $args['limit'],
                        'orderby' => 'comment_count',
                        'order' => 'DESC',
                        'post_status' => 'publish',
                    ));
                }
                
                return $popular_posts;
            }
        ));
        
        // Register site settings query
        register_graphql_field('RootQuery', 'siteSettings', array(
            'type' => 'SiteSettings',
            'description' => 'Global site settings',
            'resolve' => function() {
                return array(
                    'siteName' => get_bloginfo('name'),
                    'siteDescription' => get_bloginfo('description'),
                    'siteUrl' => home_url(),
                    'adminEmail' => get_option('admin_email'),
                    'language' => get_locale(),
                    'timezone' => get_option('timezone_string'),
                    'dateFormat' => get_option('date_format'),
                    'timeFormat' => get_option('time_format'),
                );
            }
        ));
        
        // Register SiteSettings type
        register_graphql_object_type('SiteSettings', array(
            'description' => 'Site settings and configuration',
            'fields' => array(
                'siteName' => array('type' => 'String'),
                'siteDescription' => array('type' => 'String'),
                'siteUrl' => array('type' => 'String'),
                'adminEmail' => array('type' => 'String'),
                'language' => array('type' => 'String'),
                'timezone' => array('type' => 'String'),
                'dateFormat' => array('type' => 'String'),
                'timeFormat' => array('type' => 'String'),
            ),
        ));
    }
    
    /**
     * Add CORS headers for GraphQL
     */
    public function add_cors_headers($headers) {
        $headers[] = 'X-WP-Total';
        $headers[] = 'X-WP-TotalPages';
        return $headers;
    }
    
    /**
     * Enable GraphQL introspection in all environments
     */
    public function enable_graphql_introspection() {
        if (!defined('GRAPHQL_DEBUG')) {
            define('GRAPHQL_DEBUG', true);
        }
    }
}

// Initialize GraphQL enhancements
new HeadlessProGraphQL();

/**
 * Custom GraphQL query complexity analysis
 */
function headless_pro_graphql_query_complexity($max_query_complexity, $query_complexity, $introspection_query) {
    // Allow higher complexity for development
    if (defined('WP_DEBUG') && WP_DEBUG) {
        return 1000;
    }
    
    // Moderate complexity for production
    return 500;
}
add_filter('graphql_query_max_complexity', 'headless_pro_graphql_query_complexity', 10, 3);

/**
 * Add custom scalar types for better data handling
 */
function headless_pro_register_scalar_types() {
    if (!function_exists('register_graphql_scalar_type')) {
        return;
    }
    
    // Register JSON scalar type
    register_graphql_scalar_type('JSON', array(
        'description' => 'JSON data',
        'serialize' => function($value) {
            return json_encode($value);
        },
        'parseValue' => function($value) {
            return json_decode($value, true);
        },
        'parseLiteral' => function($ast) {
            return json_decode($ast->value, true);
        },
    ));
}
add_action('graphql_register_types', 'headless_pro_register_scalar_types');

/**
 * Optimize GraphQL queries with caching
 */
function headless_pro_graphql_cache_query_results($result, $request) {
    // Only cache GET requests
    if ($request->get_method() !== 'GET') {
        return $result;
    }
    
    $query_id = md5($request->get_query());
    $cache_key = 'graphql_query_' . $query_id;
    
    // Try to get from cache first
    $cached_result = wp_cache_get($cache_key, 'graphql');
    if ($cached_result !== false) {
        return $cached_result;
    }
    
    // Cache the result for 5 minutes
    wp_cache_set($cache_key, $result, 'graphql', 300);
    
    return $result;
}
add_filter('graphql_request_results', 'headless_pro_graphql_cache_query_results', 10, 2);