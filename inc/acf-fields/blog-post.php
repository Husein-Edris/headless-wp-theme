<?php

/**
 * ACF Fields: Blog Post Content
 *
 * GraphQL field name: blogPostFields
 * Post type: post
 *
 * Fields: reading_time, conclusion_section (group with repeater), custom_tags (repeater), author_bio_override
 *
 * @package HeadlessPro
 */

if (!defined('ABSPATH')) {
    exit;
}

acf_add_local_field_group(array(
    'key' => 'group_blog_post_fields',
    'title' => 'Blog Post Content',
    'fields' => array(

        // --- Reading Time ---
        array(
            'key' => 'field_blog_reading_time',
            'label' => 'Reading Time',
            'name' => 'reading_time',
            'type' => 'text',
            'instructions' => 'Estimated reading time (e.g., \'5 min read\'). Leave empty for auto-calculation.',
            'wrapper' => array('width' => '50', 'class' => '', 'id' => ''),
            'placeholder' => '5 min read',
            'show_in_graphql' => 1,
            'graphql_field_name' => 'readingTime',
        ),

        // --- Conclusion Section (group with repeater) ---
        array(
            'key' => 'field_blog_conclusion',
            'label' => 'Conclusion Section',
            'name' => 'conclusion_section',
            'type' => 'group',
            'instructions' => 'Optional conclusion section to highlight key takeaways',
            'layout' => 'block',
            'show_in_graphql' => 1,
            'graphql_field_name' => 'conclusionSection',
            'sub_fields' => array(
                array(
                    'key' => 'field_conclusion_title',
                    'label' => 'Conclusion Title',
                    'name' => 'conclusion_title',
                    'type' => 'text',
                    'default_value' => 'Key Takeaways',
                    'placeholder' => 'Key Takeaways',
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'conclusionTitle',
                ),
                array(
                    'key' => 'field_conclusion_points',
                    'label' => 'Key Points',
                    'name' => 'conclusion_points',
                    'type' => 'repeater',
                    'instructions' => 'Add key takeaways or conclusion points',
                    'min' => 0,
                    'max' => 5,
                    'layout' => 'table',
                    'button_label' => 'Add Key Point',
                    'rows_per_page' => 20,
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'conclusionPoints',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_conclusion_point_text',
                            'label' => 'Point',
                            'name' => 'point_text',
                            'type' => 'textarea',
                            'required' => 1,
                            'rows' => 3,
                            'placeholder' => 'Enter a key takeaway or important point',
                            'show_in_graphql' => 1,
                            'graphql_field_name' => 'pointText',
                        ),
                    ),
                ),
            ),
        ),

        // --- Custom Tags (repeater) ---
        array(
            'key' => 'field_blog_tags_custom',
            'label' => 'Custom Tags',
            'name' => 'custom_tags',
            'type' => 'repeater',
            'instructions' => 'Add custom tags for this post (in addition to WordPress tags)',
            'min' => 0,
            'max' => 10,
            'layout' => 'table',
            'button_label' => 'Add Tag',
            'rows_per_page' => 20,
            'show_in_graphql' => 1,
            'graphql_field_name' => 'customTags',
            'sub_fields' => array(
                array(
                    'key' => 'field_custom_tag_name',
                    'label' => 'Tag Name',
                    'name' => 'tag_name',
                    'type' => 'text',
                    'required' => 1,
                    'wrapper' => array('width' => '70', 'class' => '', 'id' => ''),
                    'placeholder' => 'React',
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'tagName',
                ),
                array(
                    'key' => 'field_custom_tag_color',
                    'label' => 'Tag Color',
                    'name' => 'tag_color',
                    'type' => 'select',
                    'wrapper' => array('width' => '30', 'class' => '', 'id' => ''),
                    'choices' => array(
                        'blue' => 'Blue',
                        'green' => 'Green',
                        'purple' => 'Purple',
                        'orange' => 'Orange',
                        'red' => 'Red',
                        'gray' => 'Gray',
                    ),
                    'default_value' => 'blue',
                    'allow_null' => 0,
                    'ui' => 1,
                    'return_format' => 'value',
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'tagColor',
                ),
            ),
        ),

        // --- Author Bio Override ---
        array(
            'key' => 'field_blog_author_bio',
            'label' => 'Author Bio Override',
            'name' => 'author_bio_override',
            'type' => 'textarea',
            'instructions' => 'Optional custom author bio for this post (overrides default user bio)',
            'rows' => 3,
            'placeholder' => 'Custom author bio for this specific post...',
            'show_in_graphql' => 1,
            'graphql_field_name' => 'authorBioOverride',
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
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'active' => true,
    'show_in_rest' => 1,
    'show_in_graphql' => 1,
    'graphql_field_name' => 'blogPostFields',
    'map_graphql_types_from_location_rules' => 0,
));
