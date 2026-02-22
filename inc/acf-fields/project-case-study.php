<?php

/**
 * ACF Fields: Project Case Study
 *
 * GraphQL field name: caseStudy
 * Post type: project
 *
 * Groups: Overview (tech relationship), Content (challenge/solution), Key Features (repeater), Gallery, Links
 *
 * @package HeadlessPro
 */

if (!defined('ABSPATH')) {
    exit;
}

acf_add_local_field_group(array(
    'key' => 'group_project_case_study',
    'title' => 'Project Case Study',
    'fields' => array(

        // --- Project Overview (with tech relationship) ---
        array(
            'key' => 'field_project_overview',
            'label' => 'Project Overview',
            'name' => 'project_overview',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_tech_stack',
                    'label' => 'Tech Stack',
                    'name' => 'tech_stack',
                    'type' => 'relationship',
                    'instructions' => 'Select technologies used in this project',
                    'post_type' => array('tech'),
                    'multiple' => 1,
                    'return_format' => 'object',
                    'min' => 0,
                    'max' => 0,
                    'filters' => array('search', 'post_type', 'taxonomy'),
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'technologies',
                ),
            ),
        ),

        // --- Project Content (challenge + solution) ---
        array(
            'key' => 'field_project_content',
            'label' => 'Project Content',
            'name' => 'project_content',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_challenge',
                    'label' => 'The Challenge',
                    'name' => 'challenge',
                    'type' => 'wysiwyg',
                    'required' => 1,
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                    'delay' => 0,
                    'show_in_graphql' => 1,
                ),
                array(
                    'key' => 'field_solution',
                    'label' => 'The Solution',
                    'name' => 'solution',
                    'type' => 'wysiwyg',
                    'required' => 1,
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                    'delay' => 0,
                    'show_in_graphql' => 1,
                ),
            ),
        ),

        // --- Key Features (repeater) ---
        array(
            'key' => 'field_key_features',
            'label' => 'Key Features',
            'name' => 'key_features',
            'type' => 'repeater',
            'instructions' => 'Highlight the key features of this project',
            'layout' => 'block',
            'min' => 0,
            'max' => 0,
            'button_label' => 'Add Feature',
            'show_in_graphql' => 1,
            'sub_fields' => array(
                array(
                    'key' => 'field_feature_title',
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => 'text',
                    'required' => 1,
                    'show_in_graphql' => 1,
                ),
                array(
                    'key' => 'field_feature_description',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                    'rows' => 3,
                    'required' => 0,
                    'show_in_graphql' => 1,
                ),
            ),
        ),

        // --- Gallery ---
        array(
            'key' => 'field_project_gallery',
            'label' => 'Project Gallery',
            'name' => 'project_gallery',
            'type' => 'gallery',
            'return_format' => 'array',
            'preview_size' => 'medium',
            'insert' => 'append',
            'library' => 'all',
            'min' => 0,
            'max' => 0,
            'show_in_graphql' => 1,
        ),

        // --- Links ---
        array(
            'key' => 'field_project_links',
            'label' => 'Project Links',
            'name' => 'project_links',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_live_site',
                    'label' => 'Live Site URL',
                    'name' => 'live_site',
                    'type' => 'url',
                    'show_in_graphql' => 1,
                ),
                array(
                    'key' => 'field_github',
                    'label' => 'GitHub Repository',
                    'name' => 'github',
                    'type' => 'url',
                    'show_in_graphql' => 1,
                ),
            ),
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
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'active' => true,
    'show_in_graphql' => 1,
    'graphql_field_name' => 'caseStudy',
    'map_graphql_types_from_location_rules' => 0,
));
