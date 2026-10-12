<?php

/**
 * Custom Post Types for Headless Pro Theme
 * 
 * @package HeadlessPro
 */

if (!defined('ABSPATH')) {
    exit;
}

class HeadlessProPostTypes
{

    public function __construct()
    {
        // Register post types on init
        add_action('init', array($this, 'register_post_types'));
        
        // Flush rewrite rules on theme activation
        add_action('after_switch_theme', array($this, 'flush_rewrite_rules'));

        // Testimonials list: client name, company and a short quote excerpt.
        add_filter('manage_testimonial_posts_columns', array($this, 'testimonial_columns'));
        add_action('manage_testimonial_posts_custom_column', array($this, 'render_testimonial_column'), 10, 2);
    }

    /**
     * Register all custom post types
     */
    public function register_post_types()
    {
        $this->register_skills_cpt();
        $this->register_hobbies_cpt();
        $this->register_projects_cpt();
        $this->register_tech_cpt();
        $this->register_books_cpt();
        $this->register_testimonials_cpt();
    }

    /**
     * Register Skills Custom Post Type
     */
    private function register_skills_cpt()
    {
        // Register Skills post type
        register_post_type('skill', array(
            'labels' => array(
                'name' => 'Skills',
                'singular_name' => 'Skill',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Skill',
                'edit_item' => 'Edit Skill',
                'new_item' => 'New Skill',
                'view_item' => 'View Skill',
                'search_items' => 'Search Skills',
                'not_found' => 'No skills found',
                'not_found_in_trash' => 'No skills found in Trash',
                'all_items' => 'All Skills',
                'archives' => 'Skill Archives',
                'insert_into_item' => 'Insert into skill',
                'uploaded_to_this_item' => 'Uploaded to this skill',
            ),
            'public' => true,
            'show_in_rest' => true,
            'show_in_graphql' => true,
            'graphql_single_name' => 'skill',
            'graphql_plural_name' => 'skills',
            'menu_icon' => 'dashicons-star-filled',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'has_archive' => true,
            'rewrite' => array('slug' => 'skills'),
            'menu_position' => 20,
        ));
    }

    /**
     * Register Hobbies Custom Post Type
     */
    private function register_hobbies_cpt()
    {
        // Register Hobbies post type
        register_post_type('hobby', array(
            'labels' => array(
                'name' => 'Hobbies',
                'singular_name' => 'Hobby',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Hobby',
                'edit_item' => 'Edit Hobby',
                'new_item' => 'New Hobby',
                'view_item' => 'View Hobby',
                'search_items' => 'Search Hobbies',
                'not_found' => 'No hobbies found',
                'not_found_in_trash' => 'No hobbies found in Trash',
                'all_items' => 'All Hobbies',
                'archives' => 'Hobby Archives',
                'insert_into_item' => 'Insert into hobby',
                'uploaded_to_this_item' => 'Uploaded to this hobby',
            ),
            'public' => true,
            'show_in_rest' => true,
            'show_in_graphql' => true,
            'graphql_single_name' => 'hobby',
            'graphql_plural_name' => 'hobbies',
            'menu_icon' => 'dashicons-heart',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'has_archive' => true,
            'rewrite' => array('slug' => 'hobbies'),
            'menu_position' => 21,
        ));
    }

    /**
     * Register Projects Custom Post Type (Enhanced)
     */
    private function register_projects_cpt()
    {
        // Register Projects post type
        register_post_type('project', array(
            'labels' => array(
                'name' => 'Projects',
                'singular_name' => 'Project',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Project',
                'edit_item' => 'Edit Project',
                'new_item' => 'New Project',
                'view_item' => 'View Project',
                'search_items' => 'Search Projects',
                'not_found' => 'No projects found',
                'not_found_in_trash' => 'No projects found in Trash',
                'all_items' => 'All Projects',
                'archives' => 'Project Archives',
                'insert_into_item' => 'Insert into project',
                'uploaded_to_this_item' => 'Uploaded to this project',
            ),
            'public' => true,
            'show_in_rest' => true,
            'show_in_graphql' => true,
            'graphql_single_name' => 'project',
            'graphql_plural_name' => 'projects',
            'menu_icon' => 'dashicons-portfolio',
            // 'page-attributes' enables the native menu_order field, which the
            // headless frontend uses to sort projects (drag-and-drop ordering is
            // handled by HeadlessProProjectOrder in inc/project-order.php).
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'),
            'has_archive' => true,
            'rewrite' => array('slug' => 'projects'),
            'menu_position' => 22,
        ));
    }

    /**
     * Register Tech/Technologies Custom Post Type (Enhanced)
     */
    private function register_tech_cpt()
    {
        // Register Technologies post type
        register_post_type('tech', array(
            'labels' => array(
                'name' => 'Technologies',
                'singular_name' => 'Technology',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Technology',
                'edit_item' => 'Edit Technology',
                'new_item' => 'New Technology',
                'view_item' => 'View Technology',
                'search_items' => 'Search Technologies',
                'not_found' => 'No technologies found',
                'not_found_in_trash' => 'No technologies found in Trash',
                'all_items' => 'All Technologies',
                'archives' => 'Technology Archives',
                'insert_into_item' => 'Insert into technology',
                'uploaded_to_this_item' => 'Uploaded to this technology',
            ),
            'public' => true,
            'show_in_rest' => true,
            'show_in_graphql' => true,
            'graphql_single_name' => 'tech',
            'graphql_plural_name' => 'techs',
            'menu_icon' => 'dashicons-code-standards',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'taxonomies' => array('tech_category'),
            'has_archive' => true,
            'rewrite' => array('slug' => 'technologies'),
            'menu_position' => 23,
        ));

        register_taxonomy('tech_category', 'tech', array(
            'labels' => array(
                'name' => 'Tech Categories',
                'singular_name' => 'Tech Category',
                'search_items' => 'Search Tech Categories',
                'all_items' => 'All Tech Categories',
                'parent_item' => 'Parent Tech Category',
                'parent_item_colon' => 'Parent Tech Category:',
                'edit_item' => 'Edit Tech Category',
                'update_item' => 'Update Tech Category',
                'add_new_item' => 'Add New Tech Category',
                'new_item_name' => 'New Tech Category Name',
                'menu_name' => 'Tech Categories',
            ),
            'hierarchical' => true,
            'public' => true,
            'show_in_rest' => true,
            'show_in_graphql' => true,
            'graphql_single_name' => 'techCategory',
            'graphql_plural_name' => 'techCategories',
            'rewrite' => array('slug' => 'tech-category'),
            'show_admin_column' => true,
        ));
    }

    /**
     * Register Bookshelf Custom Post Type
     */
    private function register_books_cpt()
    {
        register_post_type('book', array(
            'labels' => array(
                'name' => 'Bookshelf',
                'singular_name' => 'Book',
                'add_new' => 'Add New Book',
                'add_new_item' => 'Add New Book',
                'edit_item' => 'Edit Book',
                'new_item' => 'New Book',
                'view_item' => 'View Book',
                'search_items' => 'Search in Bookshelf',
                'not_found' => 'No Books found',
                'not_found_in_trash' => 'No Book found in Trash',
                'all_items' => 'All Books',
                'archives' => 'Books Archives',
                'menu_name' => 'Bookshelf',
            ),
            'public' => true,
            'show_in_rest' => true,
            'show_in_graphql' => true,
            'graphql_single_name' => 'book',
            'graphql_plural_name' => 'books',
            'menu_icon' => 'dashicons-book',
            'supports' => array('title', 'excerpt', 'thumbnail', 'custom-fields'),
            'taxonomies' => array('category'),
            'has_archive' => false,
            'menu_position' => 24,
        ));
    }

    /**
     * Register Testimonials Custom Post Type
     *
     * Not public (no single pages or archive), edited in WP admin and read by the
     * headless frontend over REST. The Home page picks and orders the shown ones
     * (ACF relationship in inc/acf-fields/testimonials.php). Title = client name.
     */
    private function register_testimonials_cpt()
    {
        register_post_type('testimonial', array(
            'labels' => array(
                'name' => 'Testimonials',
                'singular_name' => 'Testimonial',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Testimonial',
                'edit_item' => 'Edit Testimonial',
                'new_item' => 'New Testimonial',
                'view_item' => 'View Testimonial',
                'search_items' => 'Search Testimonials',
                'not_found' => 'No testimonials found',
                'not_found_in_trash' => 'No testimonials found in Trash',
                'all_items' => 'All Testimonials',
                'enter_title_here' => 'Client name',
            ),
            'public' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => false,
            'show_in_rest' => true,
            'show_in_graphql' => true,
            'graphql_single_name' => 'testimonial',
            'graphql_plural_name' => 'testimonials',
            'menu_icon' => 'dashicons-format-quote',
            // 'page-attributes' enables menu_order (Order box in the sidebar).
            'supports' => array('title', 'page-attributes'),
            'has_archive' => false,
            'rewrite' => false,
            'menu_position' => 25,
        ));
    }

    /**
     * Admin list columns for testimonials.
     *
     * @param array $columns Default columns.
     * @return array Columns with company and quote after the title.
     */
    public function testimonial_columns($columns)
    {
        $result = array();
        foreach ($columns as $key => $label) {
            $result[$key] = ($key === 'title') ? 'Client name' : $label;
            if ($key === 'title') {
                $result['testimonial_company'] = 'Company and role';
                $result['testimonial_quote'] = 'Quote';
            }
        }
        return $result;
    }

    /**
     * Render the custom testimonial columns.
     *
     * @param string $column  Column key.
     * @param int    $post_id Testimonial ID.
     */
    public function render_testimonial_column($column, $post_id)
    {
        if (!function_exists('get_field')) {
            return;
        }
        if ($column === 'testimonial_company') {
            echo esc_html((string) get_field('company', $post_id));
        } elseif ($column === 'testimonial_quote') {
            echo esc_html(wp_trim_words((string) get_field('quote', $post_id), 14));
        }
    }

    /**
     * Flush rewrite rules on theme activation
     */
    public function flush_rewrite_rules()
    {
        $this->register_post_types();
        flush_rewrite_rules();
    }
}

// Initialize the post types
new HeadlessProPostTypes();

/**
 * Set flag to flush rewrite rules on theme activation
 */
function headless_pro_activation()
{
    add_option('headless_pro_flush_rewrite_rules', true);
}
add_action('after_switch_theme', 'headless_pro_activation');
