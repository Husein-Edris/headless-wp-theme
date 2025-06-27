<?php
/**
 * ACF Fields Registration for Headless Pro Theme
 * 
 * @package HeadlessPro
 */

if (!defined('ABSPATH')) {
    exit;
}

class HeadlessProACFFields {
    
    public function __construct() {
        // Only initialize if ACF is active
        add_action('plugins_loaded', array($this, 'init_acf_features'));
    }
    
    /**
     * Initialize ACF features only if ACF is available
     */
    public function init_acf_features() {
        if (!class_exists('ACF')) {
            return;
        }
        
        add_action('acf/init', array($this, 'register_field_groups'));
        add_action('acf/init', array($this, 'configure_acf_settings'));
    }
    
    /**
     * Configure ACF settings for headless
     */
    public function configure_acf_settings() {
        // Enable GraphQL support
        if (function_exists('acf_update_setting')) {
            acf_update_setting('show_in_graphql', true);
        }
    }
    
    /**
     * Register all ACF field groups
     */
    public function register_field_groups() {
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
    private function register_skills_fields() {
        acf_add_local_field_group(array(
            'key' => 'group_skills_fields',
            'title' => 'Skill Details',
            'fields' => array(
                array(
                    'key' => 'field_skill_short_description',
                    'label' => 'Short Description',
                    'name' => 'short_description',
                    'type' => 'textarea',
                    'instructions' => 'Brief description of this skill or technology',
                    'required' => 1,
                    'rows' => 3,
                    'placeholder' => 'Enter a brief description...',
                ),
                array(
                    'key' => 'field_skill_proficiency',
                    'label' => 'Proficiency Level',
                    'name' => 'proficiency_level',
                    'type' => 'select',
                    'choices' => array(
                        'beginner' => 'Beginner',
                        'intermediate' => 'Intermediate',
                        'advanced' => 'Advanced',
                        'expert' => 'Expert',
                    ),
                    'default_value' => 'intermediate',
                ),
                array(
                    'key' => 'field_skill_category',
                    'label' => 'Category',
                    'name' => 'category',
                    'type' => 'select',
                    'choices' => array(
                        'frontend' => 'Frontend',
                        'backend' => 'Backend',
                        'database' => 'Database',
                        'devops' => 'DevOps',
                        'design' => 'Design',
                        'other' => 'Other',
                    ),
                    'default_value' => 'frontend',
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
            'show_in_graphql' => 1,
            'graphql_field_name' => 'skillFields',
        ));
    }
    
    /**
     * Hobbies Fields
     */
    private function register_hobbies_fields() {
        acf_add_local_field_group(array(
            'key' => 'group_hobbies_fields',
            'title' => 'Hobby Details',
            'fields' => array(
                array(
                    'key' => 'field_hobby_description',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                    'instructions' => 'Detailed description of this hobby or interest',
                    'required' => 1,
                    'rows' => 4,
                    'placeholder' => 'Describe this hobby...',
                ),
                array(
                    'key' => 'field_hobby_category',
                    'label' => 'Category',
                    'name' => 'category',
                    'type' => 'select',
                    'choices' => array(
                        'creative' => 'Creative',
                        'sports' => 'Sports',
                        'technology' => 'Technology',
                        'reading' => 'Reading/Learning',
                        'social' => 'Social',
                        'other' => 'Other',
                    ),
                    'default_value' => 'other',
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
            'show_in_graphql' => 1,
            'graphql_field_name' => 'hobbyFields',
        ));
    }
    
    /**
     * About Page Fields
     */
    private function register_about_page_fields() {
        acf_add_local_field_group(array(
            'key' => 'group_about_page_fields',
            'title' => 'About Page Content',
            'fields' => array(
                // Hero Section Tab
                array(
                    'key' => 'field_about_hero_tab',
                    'label' => 'Hero Section',
                    'type' => 'tab',
                ),
                array(
                    'key' => 'field_about_hero_title',
                    'label' => 'Hero Title',
                    'name' => 'about_hero_title',
                    'type' => 'text',
                    'default_value' => 'About Me',
                ),
                array(
                    'key' => 'field_about_hero_subtitle',
                    'label' => 'Hero Subtitle',
                    'name' => 'about_hero_subtitle',
                    'type' => 'textarea',
                    'rows' => 2,
                    'default_value' => 'Full-stack developer passionate about creating exceptional digital experiences',
                ),
                array(
                    'key' => 'field_about_hero_image',
                    'label' => 'Hero Image',
                    'name' => 'about_hero_image',
                    'type' => 'image',
                    'return_format' => 'array',
                ),
                
                // Experience Section Tab
                array(
                    'key' => 'field_about_experience_tab',
                    'label' => 'Experience',
                    'type' => 'tab',
                ),
                array(
                    'key' => 'field_experience_section_title',
                    'label' => 'Section Title',
                    'name' => 'experience_section_title',
                    'type' => 'text',
                    'default_value' => 'Experience',
                ),
                array(
                    'key' => 'field_experience_items',
                    'label' => 'Experience Items',
                    'name' => 'experience_items',
                    'type' => 'repeater',
                    'layout' => 'table',
                    'button_label' => 'Add Experience',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_company_name',
                            'label' => 'Company',
                            'name' => 'company_name',
                            'type' => 'text',
                            'required' => 1,
                        ),
                        array(
                            'key' => 'field_position',
                            'label' => 'Position',
                            'name' => 'position',
                            'type' => 'text',
                            'required' => 1,
                        ),
                        array(
                            'key' => 'field_duration',
                            'label' => 'Duration',
                            'name' => 'duration',
                            'type' => 'text',
                            'required' => 1,
                            'placeholder' => 'e.g., 2020 - Present',
                        ),
                        array(
                            'key' => 'field_exp_description',
                            'label' => 'Description',
                            'name' => 'description',
                            'type' => 'textarea',
                            'rows' => 3,
                            'required' => 1,
                        ),
                        array(
                            'key' => 'field_technologies',
                            'label' => 'Technologies',
                            'name' => 'technologies',
                            'type' => 'text',
                            'placeholder' => 'e.g., React, Node.js, MongoDB',
                        ),
                    ),
                ),
                
                // Skills Section Tab
                array(
                    'key' => 'field_about_skills_tab',
                    'label' => 'Skills',
                    'type' => 'tab',
                ),
                array(
                    'key' => 'field_skills_section_title',
                    'label' => 'Section Title',
                    'name' => 'skills_section_title',
                    'type' => 'text',
                    'default_value' => 'Skills & Technologies',
                ),
                array(
                    'key' => 'field_selected_skills',
                    'label' => 'Selected Skills',
                    'name' => 'selected_skills',
                    'type' => 'post_object',
                    'post_type' => array('skill'),
                    'multiple' => 1,
                    'return_format' => 'object',
                    'ui' => 1,
                ),
                
                // Personal Section Tab
                array(
                    'key' => 'field_about_personal_tab',
                    'label' => 'Personal',
                    'type' => 'tab',
                ),
                array(
                    'key' => 'field_personal_section_title',
                    'label' => 'Section Title',
                    'name' => 'personal_section_title',
                    'type' => 'text',
                    'default_value' => 'Personal',
                ),
                array(
                    'key' => 'field_personal_content',
                    'label' => 'Personal Content',
                    'name' => 'personal_content',
                    'type' => 'wysiwyg',
                    'tabs' => 'visual,text',
                    'toolbar' => 'basic',
                ),
                array(
                    'key' => 'field_personal_image',
                    'label' => 'Personal Image',
                    'name' => 'personal_image',
                    'type' => 'image',
                    'return_format' => 'array',
                ),
                array(
                    'key' => 'field_selected_hobbies',
                    'label' => 'Selected Hobbies',
                    'name' => 'selected_hobbies',
                    'type' => 'post_object',
                    'post_type' => array('hobby'),
                    'multiple' => 1,
                    'return_format' => 'object',
                    'ui' => 1,
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'page_template',
                        'operator' => '==',
                        'value' => 'default',
                    ),
                    array(
                        'param' => 'page',
                        'operator' => '==',
                        'value' => get_option('page_about', 0),
                    ),
                ),
            ),
            'show_in_graphql' => 1,
            'graphql_field_name' => 'aboutPageFields',
        ));
    }
    
    /**
     * Homepage Fields (existing but enhanced)
     */
    private function register_homepage_fields() {
        // This assumes you already have homepage fields
        // We'll enhance them if needed
        acf_add_local_field_group(array(
            'key' => 'group_homepage_sections',
            'title' => 'Homepage Sections',
            'fields' => array(
                // Hero Section
                array(
                    'key' => 'field_hero_section_tab',
                    'label' => 'Hero Section',
                    'type' => 'tab',
                ),
                array(
                    'key' => 'field_hero_title',
                    'label' => 'Hero Title',
                    'name' => 'hero_title',
                    'type' => 'text',
                    'default_value' => 'Welcome to My Portfolio',
                ),
                array(
                    'key' => 'field_hero_copy',
                    'label' => 'Hero Copy',
                    'name' => 'hero_copy',
                    'type' => 'textarea',
                    'rows' => 3,
                ),
                array(
                    'key' => 'field_hero_image',
                    'label' => 'Hero Image',
                    'name' => 'hero_image',
                    'type' => 'image',
                    'return_format' => 'array',
                ),
                
                // About Section
                array(
                    'key' => 'field_about_section_tab',
                    'label' => 'About Section',
                    'type' => 'tab',
                ),
                array(
                    'key' => 'field_about_section_title',
                    'label' => 'About Title',
                    'name' => 'about_section_title',
                    'type' => 'text',
                    'default_value' => 'About Me',
                ),
                array(
                    'key' => 'field_about_me_text',
                    'label' => 'About Text',
                    'name' => 'about_me_text',
                    'type' => 'wysiwyg',
                    'tabs' => 'visual,text',
                    'toolbar' => 'basic',
                ),
                
                // Contact Section
                array(
                    'key' => 'field_contact_section_tab',
                    'label' => 'Contact Section',
                    'type' => 'tab',
                ),
                array(
                    'key' => 'field_contact_subtitle',
                    'label' => 'Contact Subtitle',
                    'name' => 'contact_subtitle',
                    'type' => 'text',
                    'default_value' => 'Get in touch',
                ),
                array(
                    'key' => 'field_contact_title',
                    'label' => 'Contact Title',
                    'name' => 'contact_title',
                    'type' => 'text',
                    'default_value' => 'Let\'s work together',
                ),
                array(
                    'key' => 'field_contact_email',
                    'label' => 'Contact Email',
                    'name' => 'contact_email',
                    'type' => 'email',
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
            'show_in_graphql' => 1,
            'graphql_field_name' => 'homepageSections',
        ));
    }
    
    /**
     * Enhanced Project Fields
     */
    private function register_project_fields() {
        if (!post_type_exists('project')) {
            return;
        }
        
        acf_add_local_field_group(array(
            'key' => 'group_project_case_study',
            'title' => 'Project Case Study',
            'fields' => array(
                // Project Overview Tab
                array(
                    'key' => 'field_project_overview_tab',
                    'label' => 'Project Overview',
                    'type' => 'tab',
                ),
                array(
                    'key' => 'field_project_overview_title',
                    'label' => 'Project Title',
                    'name' => 'project_title',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_project_overview_description',
                    'label' => 'Project Description',
                    'name' => 'project_description',
                    'type' => 'wysiwyg',
                ),
                array(
                    'key' => 'field_project_technologies',
                    'label' => 'Technologies Used',
                    'name' => 'technologies',
                    'type' => 'post_object',
                    'post_type' => array('tech'),
                    'multiple' => 1,
                    'return_format' => 'object',
                    'ui' => 1,
                ),
                
                // Project Links Tab
                array(
                    'key' => 'field_project_links_tab',
                    'label' => 'Project Links',
                    'type' => 'tab',
                ),
                array(
                    'key' => 'field_project_live_site',
                    'label' => 'Live Site URL',
                    'name' => 'live_site',
                    'type' => 'url',
                ),
                array(
                    'key' => 'field_project_github',
                    'label' => 'GitHub URL',
                    'name' => 'github',
                    'type' => 'url',
                ),
                
                // Project Gallery Tab
                array(
                    'key' => 'field_project_gallery_tab',
                    'label' => 'Project Gallery',
                    'type' => 'tab',
                ),
                array(
                    'key' => 'field_project_gallery',
                    'label' => 'Project Images',
                    'name' => 'project_gallery',
                    'type' => 'gallery',
                    'return_format' => 'array',
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
            'show_in_graphql' => 1,
            'graphql_field_name' => 'caseStudy',
        ));
    }
    
    /**
     * Blog Post Fields
     */
    private function register_blog_post_fields() {
        acf_add_local_field_group(array(
            'key' => 'group_blog_post_fields',
            'title' => 'Blog Post Fields',
            'fields' => array(
                array(
                    'key' => 'field_reading_time',
                    'label' => 'Reading Time',
                    'name' => 'reading_time',
                    'type' => 'text',
                    'placeholder' => 'e.g., 5 min read',
                ),
                array(
                    'key' => 'field_custom_excerpt',
                    'label' => 'Custom Excerpt',
                    'name' => 'custom_excerpt',
                    'type' => 'textarea',
                    'rows' => 3,
                ),
                array(
                    'key' => 'field_conclusion_section',
                    'label' => 'Conclusion Section',
                    'name' => 'conclusion_section',
                    'type' => 'group',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_conclusion_title',
                            'label' => 'Conclusion Title',
                            'name' => 'conclusion_title',
                            'type' => 'text',
                            'default_value' => 'Conclusion',
                        ),
                        array(
                            'key' => 'field_conclusion_points',
                            'label' => 'Key Points',
                            'name' => 'conclusion_points',
                            'type' => 'repeater',
                            'button_label' => 'Add Point',
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_point_text',
                                    'label' => 'Point',
                                    'name' => 'point_text',
                                    'type' => 'text',
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    'key' => 'field_custom_tags',
                    'label' => 'Custom Tags',
                    'name' => 'custom_tags',
                    'type' => 'repeater',
                    'button_label' => 'Add Tag',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_tag_name',
                            'label' => 'Tag Name',
                            'name' => 'tag_name',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_tag_color',
                            'label' => 'Tag Color',
                            'name' => 'tag_color',
                            'type' => 'color_picker',
                            'default_value' => '#0073aa',
                        ),
                    ),
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
            'show_in_graphql' => 1,
            'graphql_field_name' => 'blogPostFields',
        ));
    }
}

// Initialize ACF fields
new HeadlessProACFFields();