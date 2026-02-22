<?php

/**
 * ACF Fields: Skill Fields
 *
 * GraphQL field name: skillFields
 * Post type: skill
 *
 * @package HeadlessPro
 */

if (!defined('ABSPATH')) {
    exit;
}

acf_add_local_field_group(array(
    'key' => 'group_skills_fields',
    'title' => 'Skill Fields',
    'fields' => array(
        array(
            'key' => 'field_skill_short_description',
            'label' => 'Short Description',
            'name' => 'short_description',
            'type' => 'textarea',
            'instructions' => 'Brief description of this skill or technology',
            'required' => 0,
            'wrapper' => array('width' => '', 'class' => '', 'id' => ''),
            'rows' => 3,
            'placeholder' => 'Enter a brief description of this skill...',
            'new_lines' => '',
            'maxlength' => '',
            'allow_in_bindings' => 1,
            'show_in_graphql' => 1,
            'graphql_field_name' => 'shortDescription',
            'graphql_non_null' => 0,
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
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'active' => true,
    'description' => 'Fields for Skills custom post type',
    'show_in_rest' => 1,
    'show_in_graphql' => 1,
    'graphql_field_name' => 'skillFields',
    'map_graphql_types_from_location_rules' => 0,
));
