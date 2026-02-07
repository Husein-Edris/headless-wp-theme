<?php

/**
 * ACF Fields: Hobby Fields
 *
 * GraphQL field name: hobbyFields
 * Post type: hobby
 *
 * @package HeadlessPro
 */

if (!defined('ABSPATH')) {
    exit;
}

acf_add_local_field_group(array(
    'key' => 'group_hobbies_fields',
    'title' => 'Hobby Fields',
    'fields' => array(
        array(
            'key' => 'field_hobby_description',
            'label' => 'Description',
            'name' => 'description',
            'type' => 'textarea',
            'instructions' => 'Detailed description of this hobby or interest',
            'required' => 0,
            'wrapper' => array('width' => '', 'class' => '', 'id' => ''),
            'rows' => 4,
            'placeholder' => 'Describe this hobby or interest...',
            'new_lines' => '',
            'maxlength' => '',
            'allow_in_bindings' => 1,
            'show_in_graphql' => 1,
            'graphql_field_name' => 'description',
            'graphql_non_null' => 0,
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
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'active' => true,
    'description' => 'Fields for Hobbies custom post type',
    'show_in_rest' => 1,
    'show_in_graphql' => 1,
    'graphql_field_name' => 'hobbyFields',
    'map_graphql_types_from_location_rules' => 0,
));
