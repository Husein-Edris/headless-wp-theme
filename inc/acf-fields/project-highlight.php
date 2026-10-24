<?php

/**
 * ACF Fields: Project Highlight
 *
 * Sidebar toggle on projects. Highlighted projects are shown in the large
 * featured layout at the top of /projects, in menu_order.
 * Exposed over REST as acf_fields.highlighted (true/false).
 *
 * @package HeadlessPro
 */

if (!defined('ABSPATH')) {
    exit;
}

acf_add_local_field_group(edris_with_de_fields(array(
    'key' => 'group_project_highlight',
    'title' => 'Highlight',
    'fields' => array(
        array(
            'key' => 'field_project_highlighted',
            'label' => 'Highlighted project',
            'name' => 'highlighted',
            'type' => 'true_false',
            'instructions' => 'Show this project in the large featured layout at the top of the projects page and, for the first three, in the homepage hero frames. Order follows the project order (drag and drop).',
            'ui' => 1,
            'default_value' => 0,
            'show_in_graphql' => 1,
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
    'position' => 'side',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'active' => true,
    'show_in_graphql' => 1,
    'graphql_field_name' => 'projectHighlight',
    'map_graphql_types_from_location_rules' => 0,
)));
