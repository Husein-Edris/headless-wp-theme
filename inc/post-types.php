<?php
/**
 * Custom Post Types for Headless Pro Theme
 * 
 * @package HeadlessPro
 */

if (!defined('ABSPATH')) {
    exit;
}

class HeadlessProPostTypes {
    
    public function __construct() {
        add_action('init', array($this, 'register_post_types'));
        add_action('init', array($this, 'flush_rewrite_rules_maybe'));
    }
    
    /**
     * Register all custom post types
     */
    public function register_post_types() {
        $this->register_skills_cpt();
        $this->register_hobbies_cpt();
        $this->register_projects_cpt();
        $this->register_tech_cpt();
    }
    
    /**
     * Register Skills Custom Post Type
     */
    private function register_skills_cpt() {
        $labels = array(
            'name' => 'Skills',
            'singular_name' => 'Skill',
            'menu_name' => 'Skills',
            'name_admin_bar' => 'Skill',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Skill',
            'new_item' => 'New Skill',
            'edit_item' => 'Edit Skill',
            'view_item' => 'View Skill',
            'all_items' => 'All Skills',
            'search_items' => 'Search Skills',
            'not_found' => 'No skills found.',
            'not_found_in_trash' => 'No skills found in Trash.',
            'featured_image' => 'Skill Image',
            'set_featured_image' => 'Set skill image',
            'remove_featured_image' => 'Remove skill image',
            'use_featured_image' => 'Use as skill image',
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_admin_bar' => true,
            'show_in_rest' => true,
            'rest_base' => 'skills',
            'show_in_graphql' => true,
            'graphql_single_name' => 'skill',
            'graphql_plural_name' => 'skills',
            'query_var' => true,
            'rewrite' => array('slug' => 'skills'),
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'menu_position' => 25,
            'menu_icon' => 'dashicons-star-filled',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'description' => 'Skills and technologies for the portfolio',
        );

        register_post_type('skill', $args);
    }
    
    /**
     * Register Hobbies Custom Post Type
     */
    private function register_hobbies_cpt() {
        $labels = array(
            'name' => 'Hobbies',
            'singular_name' => 'Hobby',
            'menu_name' => 'Hobbies',
            'name_admin_bar' => 'Hobby',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Hobby',
            'new_item' => 'New Hobby',
            'edit_item' => 'Edit Hobby',
            'view_item' => 'View Hobby',
            'all_items' => 'All Hobbies',
            'search_items' => 'Search Hobbies',
            'not_found' => 'No hobbies found.',
            'not_found_in_trash' => 'No hobbies found in Trash.',
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_admin_bar' => true,
            'show_in_rest' => true,
            'rest_base' => 'hobbies',
            'show_in_graphql' => true,
            'graphql_single_name' => 'hobby',
            'graphql_plural_name' => 'hobbies',
            'query_var' => true,
            'rewrite' => array('slug' => 'hobbies'),
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'menu_position' => 26,
            'menu_icon' => 'dashicons-heart',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'description' => 'Personal hobbies and interests',
        );

        register_post_type('hobby', $args);
    }
    
    /**
     * Register Projects Custom Post Type (Enhanced)
     */
    private function register_projects_cpt() {
        // Only register if not already registered
        if (post_type_exists('project')) {
            return;
        }
        
        $labels = array(
            'name' => 'Projects',
            'singular_name' => 'Project',
            'menu_name' => 'Projects',
            'name_admin_bar' => 'Project',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Project',
            'new_item' => 'New Project',
            'edit_item' => 'Edit Project',
            'view_item' => 'View Project',
            'all_items' => 'All Projects',
            'search_items' => 'Search Projects',
            'not_found' => 'No projects found.',
            'not_found_in_trash' => 'No projects found in Trash.',
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'rest_base' => 'projects',
            'show_in_graphql' => true,
            'graphql_single_name' => 'project',
            'graphql_plural_name' => 'projects',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'menu_icon' => 'dashicons-portfolio',
            'menu_position' => 20,
            'has_archive' => true,
            'rewrite' => array('slug' => 'projects'),
        );

        register_post_type('project', $args);
    }
    
    /**
     * Register Tech/Technologies Custom Post Type (Enhanced)
     */
    private function register_tech_cpt() {
        // Only register if not already registered
        if (post_type_exists('tech')) {
            return;
        }
        
        $labels = array(
            'name' => 'Technologies',
            'singular_name' => 'Technology',
            'menu_name' => 'Tech Stack',
            'name_admin_bar' => 'Technology',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Technology',
            'new_item' => 'New Technology',
            'edit_item' => 'Edit Technology',
            'view_item' => 'View Technology',
            'all_items' => 'All Technologies',
            'search_items' => 'Search Technologies',
            'not_found' => 'No technologies found.',
            'not_found_in_trash' => 'No technologies found in Trash.',
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'rest_base' => 'tech',
            'show_in_graphql' => true,
            'graphql_single_name' => 'tech',
            'graphql_plural_name' => 'techs',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'menu_icon' => 'dashicons-admin-tools',
            'menu_position' => 27,
            'has_archive' => false,
            'rewrite' => array('slug' => 'tech'),
        );

        register_post_type('tech', $args);
    }
    
    /**
     * Flush rewrite rules on theme activation
     */
    public function flush_rewrite_rules_maybe() {
        if (get_option('headless_pro_flush_rewrite_rules')) {
            flush_rewrite_rules();
            delete_option('headless_pro_flush_rewrite_rules');
        }
    }
}

// Initialize the post types
new HeadlessProPostTypes();

/**
 * Set flag to flush rewrite rules on theme activation
 */
function headless_pro_activation() {
    add_option('headless_pro_flush_rewrite_rules', true);
}
add_action('after_switch_theme', 'headless_pro_activation');