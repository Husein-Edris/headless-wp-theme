<?php

/**
 * ACF Fields Registration for Headless Pro Theme
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
        // Only initialize if ACF is active
        add_action('plugins_loaded', array($this, 'init_acf_features'));
    }

    /**
     * Initialize ACF features only if ACF is available
     */
    public function init_acf_features()
    {
        if (!class_exists('ACF')) {
            return;
        }

        add_action('acf/init', array($this, 'register_field_groups'));
        add_action('acf/init', array($this, 'configure_acf_settings'));
    }

    /**
     * Configure ACF settings for headless
     */
    public function configure_acf_settings()
    {
        // Add any ACF-specific settings here
    }

    /**
     * Register all ACF field groups
     */
    public function register_field_groups()
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        $this->register_skills_fields();
        $this->register_hobbies_fields();
        $this->register_about_page_fields();
        $this->register_homepage_fields();
        $this->register_project_fields();
        $this->register_blog_post_fields();
    }

    /**
     * Skills Fields
     */
    private function register_skills_fields()
    {
        acf_add_local_field_group(array(
            'key' => 'group_skills',
            'title' => 'Skill Details',
            'fields' => array(
                array(
                    'key' => 'field_skill_level',
                    'label' => 'Skill Level',
                    'name' => 'skill_level',
                    'type' => 'number',
                    'min' => 1,
                    'max' => 100,
                    'step' => 1,
                    'default_value' => 50,
                ),
                array(
                    'key' => 'field_skill_category',
                    'label' => 'Category',
                    'name' => 'skill_category',
                    'type' => 'select',
                    'choices' => array(
                        'frontend' => 'Frontend',
                        'backend' => 'Backend',
                        'design' => 'Design',
                        'devops' => 'DevOps',
                        'other' => 'Other',
                    ),
                    'default_value' => 'other',
                    'allow_null' => 0,
                    'multiple' => 0,
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'skill',
                    ),
                ),
            ),
        ));
    }

    /**
     * Hobbies Fields
     */
    private function register_hobbies_fields()
    {
        acf_add_local_field_group(array(
            'key' => 'group_hobbies',
            'title' => 'Hobby Details',
            'fields' => array(
                array(
                    'key' => 'field_hobby_icon',
                    'label' => 'Icon',
                    'name' => 'hobby_icon',
                    'type' => 'text',
                    'instructions' => 'Enter an emoji or icon class',
                ),
                array(
                    'key' => 'field_hobby_frequency',
                    'label' => 'Frequency',
                    'name' => 'hobby_frequency',
                    'type' => 'select',
                    'choices' => array(
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                        'occasionally' => 'Occasionally',
                    ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'hobby',
                    ),
                ),
            ),
        ));
    }

    /**
     * About Page Fields
     */
    private function register_about_page_fields()
    {
        acf_add_local_field_group(array(
            'key' => 'group_about',
            'title' => 'About Page Sections',
            'fields' => array(
                array(
                    'key' => 'field_about_intro',
                    'label' => 'Introduction',
                    'name' => 'about_intro',
                    'type' => 'wysiwyg',
                ),
                array(
                    'key' => 'field_about_image',
                    'label' => 'Profile Image',
                    'name' => 'about_image',
                    'type' => 'image',
                    'return_format' => 'array',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'page_template',
                        'operator' => '==',
                        'value' => 'default',
                    ),
                ),
            ),
        ));
    }

    /**
     * Homepage Fields
     */
    private function register_homepage_fields()
    {
        acf_add_local_field_group(array(
            'key' => 'group_homepage',
            'title' => 'Homepage Sections',
            'fields' => array(
                array(
                    'key' => 'field_hero_title',
                    'label' => 'Hero Title',
                    'name' => 'hero_title',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_hero_subtitle',
                    'label' => 'Hero Subtitle',
                    'name' => 'hero_subtitle',
                    'type' => 'textarea',
                ),
                array(
                    'key' => 'field_hero_image',
                    'label' => 'Hero Image',
                    'name' => 'hero_image',
                    'type' => 'image',
                    'return_format' => 'array',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'page_type',
                        'operator' => '==',
                        'value' => 'front_page',
                    ),
                ),
            ),
        ));
    }

    /**
     * Project Fields
     */
    private function register_project_fields()
    {
        acf_add_local_field_group(array(
            'key' => 'group_project',
            'title' => 'Project Details',
            'fields' => array(
                array(
                    'key' => 'field_project_url',
                    'label' => 'Project URL',
                    'name' => 'project_url',
                    'type' => 'url',
                ),
                array(
                    'key' => 'field_project_github',
                    'label' => 'GitHub URL',
                    'name' => 'project_github',
                    'type' => 'url',
                ),
                array(
                    'key' => 'field_project_gallery',
                    'label' => 'Project Gallery',
                    'name' => 'project_gallery',
                    'type' => 'gallery',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'project',
                    ),
                ),
            ),
        ));
    }

    /**
     * Blog Post Fields
     */
    private function register_blog_post_fields()
    {
        acf_add_local_field_group(array(
            'key' => 'group_blog_post',
            'title' => 'Additional Post Details',
            'fields' => array(
                array(
                    'key' => 'field_featured',
                    'label' => 'Featured Post',
                    'name' => 'featured_post',
                    'type' => 'true_false',
                    'ui' => 1,
                ),
                array(
                    'key' => 'field_post_subtitle',
                    'label' => 'Post Subtitle',
                    'name' => 'post_subtitle',
                    'type' => 'text',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'post',
                    ),
                ),
            ),
        ));
    }
}

// Initialize ACF fields
new HeadlessProACFFields();
