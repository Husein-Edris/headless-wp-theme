<?php

/**
 * ACF Fields: Tech Usage Note
 *
 * Optional note per technology for the /tech-stack logo grid: a small line
 * under the case study count on the tool's tile. Hidden when empty.
 * Exposed over REST as acf_fields.usage_note (text).
 *
 * @package HeadlessPro
 */

if (!defined('ABSPATH')) {
    exit;
}

acf_add_local_field_group(edris_with_de_fields(array(
    'key' => 'group_tech_usage_note',
    'title' => 'Tech stack page note',
    'fields' => array(
        array(
            'key' => 'field_tech_usage_note',
            'label' => 'Usage note',
            'name' => 'usage_note',
            'type' => 'textarea',
            'instructions' => 'Small line under the case study count on this tool\'s tile on /tech-stack. Leave empty to show nothing.',
            'rows' => 3,
            'new_lines' => '',
            'show_in_graphql' => 1,
        ),
    ),
    'location' => array(
        array(
            array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'tech',
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
    'graphql_field_name' => 'techUsageNote',
    'map_graphql_types_from_location_rules' => 0,
)));
