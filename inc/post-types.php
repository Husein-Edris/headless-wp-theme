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
            'menu_icon' => 'dashicons-portfolio',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
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
            'menu_icon' => 'dashicons-code-standards',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'has_archive' => true,
            'rewrite' => array('slug' => 'technologies'),
            'menu_position' => 23,
        ));
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
