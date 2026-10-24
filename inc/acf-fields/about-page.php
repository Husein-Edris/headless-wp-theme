<?php

/**
 * ACF Fields: About Page Fields
 *
 * GraphQL field name: aboutPageFields
 * Location: About page (matched by slug 'about-me' or 'about' via slug-based location matcher)
 *
 * Sections: Hero, Experience (repeater with tech relationships), Skills, Personal, Hobbies
 *
 * @package HeadlessPro
 */

if (!defined('ABSPATH')) {
    exit;
}

$location_rules = array(
    // Slug-based matching (portable across environments).
    array(
        array(
            'param' => 'page',
            'operator' => '==',
            'value' => 'slug:about-me',
        ),
    ),
    array(
        array(
            'param' => 'page',
            'operator' => '==',
            'value' => 'slug:about',
        ),
    ),
);

// Fallback: resolve page IDs at runtime so location matching remains portable.
if (function_exists('headless_pro_get_page_id_by_slug')) {
    $about_me_id = headless_pro_get_page_id_by_slug('about-me');
    if ($about_me_id > 0) {
        $location_rules[] = array(
            array(
                'param' => 'page',
                'operator' => '==',
                'value' => (string) $about_me_id,
            ),
        );
    }

    $about_id = headless_pro_get_page_id_by_slug('about');
    if ($about_id > 0) {
        $location_rules[] = array(
            array(
                'param' => 'page',
                'operator' => '==',
                'value' => (string) $about_id,
            ),
        );
    }
}

acf_add_local_field_group(edris_with_de_fields(array(
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
            'key' => 'field_wo_about_summary',
            'label' => 'Profile Summary',
            'name' => 'about_summary',
            'type' => 'textarea',
            'instructions' => 'Your bio paragraph — used in CV, fit analysis, and blog generation.',
            'rows' => 4,
        ),
        array(
            'key' => 'field_wo_about_location',
            'label' => 'Location',
            'name' => 'about_location',
            'type' => 'text',
            'instructions' => 'City and country shown on CV, e.g. Dornbirn, Österreich',
            'default_value' => 'Dornbirn, Österreich',
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
            'key' => 'field_about_experience_technologies_label',
            'label' => 'Technologies label',
            'name' => 'experience_technologies_label',
            'type' => 'text',
            'instructions' => 'Label before the technology list of each experience item. Leave empty to show: Technologies:',
            'default_value' => 'Technologies:',
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
            'rows_per_page' => 10,
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
                    'delay' => 1,
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
                    'return_format' => 'id',
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
            'key' => 'field_about_hobbies_section_title',
            'label' => 'Hobbies Section Title',
            'name' => 'hobbies_section_title',
            'type' => 'text',
            'instructions' => 'Heading above the hobbies list. Leave empty to show: Interests & Hobbies',
            'default_value' => 'Interests & Hobbies',
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


        // ── Education & Languages Tab ───────────────────────────────────────────
        array(
            'key' => 'field_wo_edu_lang_tab',
            'label' => 'Education & Languages',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        ),
        array(
            'key' => 'field_wo_education_items',
            'label' => 'Education Items',
            'name' => 'education_items',
            'type' => 'repeater',
            'instructions' => 'Add each degree or certificate. Replaces the hardcoded education list in Work OS.',
            'collapsed' => 'field_wo_edu_degree',
            'min' => 0,
            'max' => 0,
            'layout' => 'block',
            'button_label' => 'Add Education',
            'sub_fields' => array(
                array(
                    'key' => 'field_wo_edu_degree',
                    'label' => 'Degree / Certificate',
                    'name' => 'degree',
                    'type' => 'text',
                    'required' => 1,
                    'placeholder' => 'e.g. Diploma in Full Stack Web Development',
                    'parent_repeater' => 'field_wo_education_items',
                ),
                array(
                    'key' => 'field_wo_edu_institution',
                    'label' => 'Institution',
                    'name' => 'institution',
                    'type' => 'text',
                    'required' => 1,
                    'placeholder' => 'e.g. Instant Software Solution',
                    'parent_repeater' => 'field_wo_education_items',
                ),
                array(
                    'key' => 'field_wo_edu_period',
                    'label' => 'Period',
                    'name' => 'period',
                    'type' => 'text',
                    'required' => 1,
                    'placeholder' => 'e.g. Oct 2021 – Aug 2022',
                    'parent_repeater' => 'field_wo_education_items',
                ),
            ),
        ),
        array(
            'key' => 'field_wo_language_items',
            'label' => 'Languages',
            'name' => 'language_items',
            'type' => 'repeater',
            'instructions' => 'Add each language you speak. Replaces the hardcoded language list in Work OS.',
            'collapsed' => 'field_wo_lang_name',
            'min' => 0,
            'max' => 0,
            'layout' => 'table',
            'button_label' => 'Add Language',
            'sub_fields' => array(
                array(
                    'key' => 'field_wo_lang_name',
                    'label' => 'Language',
                    'name' => 'language',
                    'type' => 'text',
                    'required' => 1,
                    'placeholder' => 'e.g. Kurdish',
                    'parent_repeater' => 'field_wo_language_items',
                ),
                array(
                    'key' => 'field_wo_lang_level',
                    'label' => 'Level',
                    'name' => 'level',
                    'type' => 'text',
                    'required' => 1,
                    'placeholder' => 'e.g. Native  or  C1 – Fluent',
                    'parent_repeater' => 'field_wo_language_items',
                ),
            ),
        ),
    ),
    'location' => $location_rules,
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
)));
