<?php

/**
 * ACF Fields: About Page Fields
 *
 * GraphQL field name: aboutPageFields
 * Location: About page (matched by slug 'about-me' via headless_pro_get_page_id_by_slug())
 *
 * Sections: Hero, Experience (repeater with tech relationships), Skills, Personal, Hobbies
 *
 * @package HeadlessPro
 */

if (!defined('ABSPATH')) {
    exit;
}

acf_add_local_field_group(array(
    'key' => 'group_64a1b2c3d4e63',
    'title' => 'About Page Fields',
    'fields' => array(

        // --- Hero Section Tab ---
        array(
            'key' => 'field_64a1b2c3d4e64',
            'label' => 'Hero Section',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        ),
        array(
            'key' => 'field_64a1b2c3d4e65',
            'label' => 'About Hero Title',
            'name' => 'about_hero_title',
            'type' => 'text',
            'instructions' => 'Main title for the about page hero section',
            'default_value' => 'About Edris Husein',
        ),
        array(
            'key' => 'field_64a1b2c3d4e66',
            'label' => 'About Hero Subtitle',
            'name' => 'about_hero_subtitle',
            'type' => 'textarea',
            'instructions' => 'Subtitle text for the hero section',
            'default_value' => 'Full-stack developer passionate about creating exceptional digital experiences',
            'rows' => 2,
        ),
        array(
            'key' => 'field_64a1b2c3d4e67',
            'label' => 'About Hero Image',
            'name' => 'about_hero_image',
            'type' => 'image',
            'instructions' => 'Profile image for the hero section',
            'return_format' => 'array',
            'preview_size' => 'medium',
            'library' => 'all',
        ),

        // --- Experience Section Tab ---
        array(
            'key' => 'field_64a1b2c3d4e68',
            'label' => 'Experience Section',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        ),
        array(
            'key' => 'field_64a1b2c3d4e69',
            'label' => 'Experience Section Title',
            'name' => 'experience_section_title',
            'type' => 'text',
            'instructions' => 'Title for the experience section',
            'default_value' => 'Experience',
        ),
        array(
            'key' => 'field_64a1b2c3d4e70',
            'label' => 'Experience Items',
            'name' => 'experience_items',
            'type' => 'repeater',
            'instructions' => 'Add your work experience and projects',
            'collapsed' => 'field_64a1b2c3d4e71',
            'min' => 0,
            'max' => 0,
            'layout' => 'block',
            'button_label' => 'Add Experience',
            'rows_per_page' => 20,
            'sub_fields' => array(
                array(
                    'key' => 'field_64a1b2c3d4e71',
                    'label' => 'Company Name',
                    'name' => 'company_name',
                    'type' => 'text',
                    'required' => 1,
                    'placeholder' => 'Company or Client Name',
                    'parent_repeater' => 'field_64a1b2c3d4e70',
                ),
                array(
                    'key' => 'field_64a1b2c3d4e72',
                    'label' => 'Position',
                    'name' => 'position',
                    'type' => 'text',
                    'required' => 1,
                    'placeholder' => 'Job Title or Role',
                    'parent_repeater' => 'field_64a1b2c3d4e70',
                ),
                array(
                    'key' => 'field_64a1b2c3d4e73',
                    'label' => 'Duration',
                    'name' => 'duration',
                    'type' => 'text',
                    'required' => 1,
                    'placeholder' => 'e.g. 2020 - Present',
                    'parent_repeater' => 'field_64a1b2c3d4e70',
                ),
                array(
                    'key' => 'field_64a1b2c3d4e74',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'wysiwyg',
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                    'delay' => 0,
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'description',
                    'parent_repeater' => 'field_64a1b2c3d4e70',
                ),
                array(
                    'key' => 'field_64a1b2c3d4e75',
                    'label' => 'Technologies',
                    'name' => 'technologies',
                    'type' => 'post_object',
                    'post_type' => array('tech'),
                    'return_format' => 'object',
                    'multiple' => 1,
                    'allow_null' => 0,
                    'ui' => 1,
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'technologies',
                    'graphql_connection_type' => 'one_to_many',
                    'parent_repeater' => 'field_64a1b2c3d4e70',
                ),
            ),
        ),

        // --- Skills Section Tab ---
        array(
            'key' => 'field_64a1b2c3d4e76',
            'label' => 'Skills Section',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        ),
        array(
            'key' => 'field_64a1b2c3d4e77',
            'label' => 'Skills Section Title',
            'name' => 'skills_section_title',
            'type' => 'text',
            'instructions' => 'Title for the skills section',
            'default_value' => 'Skills & Technologies',
        ),
        array(
            'key' => 'field_64a1b2c3d4e78',
            'label' => 'Selected Skills',
            'name' => 'selected_skills',
            'type' => 'post_object',
            'instructions' => 'Select the skills to display on the about page',
            'post_type' => array('skill'),
            'allow_null' => 0,
            'multiple' => 1,
            'return_format' => 'object',
            'ui' => 1,
        ),

        // --- Personal Section Tab ---
        array(
            'key' => 'field_64a1b2c3d4e79',
            'label' => 'Personal Section',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        ),
        array(
            'key' => 'field_64a1b2c3d4e80',
            'label' => 'Personal Section Title',
            'name' => 'personal_section_title',
            'type' => 'text',
            'instructions' => 'Title for the personal section',
            'default_value' => 'Personal',
        ),
        array(
            'key' => 'field_64a1b2c3d4e81',
            'label' => 'Personal Content',
            'name' => 'personal_content',
            'type' => 'wysiwyg',
            'instructions' => 'Personal information and background',
            'tabs' => 'all',
            'toolbar' => 'full',
            'media_upload' => 1,
            'delay' => 0,
        ),
        array(
            'key' => 'field_64a1b2c3d4e82',
            'label' => 'Personal Image',
            'name' => 'personal_image',
            'type' => 'image',
            'instructions' => 'Optional image for the personal section',
            'return_format' => 'array',
            'preview_size' => 'medium',
            'library' => 'all',
        ),
        array(
            'key' => 'field_64a1b2c3d4e83',
            'label' => 'Selected Hobbies',
            'name' => 'selected_hobbies',
            'type' => 'post_object',
            'instructions' => 'Select hobbies and interests to display',
            'post_type' => array('hobby'),
            'allow_null' => 0,
            'multiple' => 1,
            'return_format' => 'object',
            'ui' => 1,
        ),

    ),
    'location' => array(
        array(
            array(
                'param' => 'page',
                'operator' => '==',
                'value' => (string) headless_pro_get_page_id_by_slug('about-me'),
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'seamless',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'active' => true,
    'show_in_rest' => 1,
    'show_in_graphql' => 1,
    'graphql_field_name' => 'aboutPageFields',
    'map_graphql_types_from_location_rules' => 0,
));
