<?php

/**
 * ACF Fields: Project Facts
 *
 * Sidebar box on projects. Each filled field is one row of the facts column on
 * the case study page (client, role, year, built at). An empty field hides its
 * row; stack and links come from the Project Case Study group.
 * Exposed over REST as acf_fields.client, role, year, built_at (text) and
 * showcase_video (file URL, homepage hero strip only).
 * Row labels: Site Content > Case study template.
 *
 * @package HeadlessPro
 */

if (!defined('ABSPATH')) {
    exit;
}

acf_add_local_field_group(edris_with_de_fields(array(
    'key' => 'group_project_facts',
    'title' => 'Project facts',
    'fields' => array(
        array(
            'key' => 'field_project_facts_client',
            'label' => 'Client',
            'name' => 'client',
            'type' => 'text',
            'instructions' => 'Who the project was for, e.g. illwerke vkw. Leave empty to hide the row.',
            'show_in_graphql' => 1,
        ),
        array(
            'key' => 'field_project_facts_role',
            'label' => 'Role',
            'name' => 'role',
            'type' => 'text',
            'instructions' => 'Your role, e.g. WordPress developer. Leave empty to hide the row.',
            'show_in_graphql' => 1,
        ),
        array(
            'key' => 'field_project_facts_year',
            'label' => 'Year',
            'name' => 'year',
            'type' => 'text',
            'instructions' => 'Year or range, e.g. 2025 or 2023 to 2024. Leave empty to hide the row.',
            'maxlength' => 20,
            'show_in_graphql' => 1,
        ),
        array(
            'key' => 'field_project_facts_built_at',
            'label' => 'Built at',
            'name' => 'built_at',
            'type' => 'text',
            'instructions' => 'Where the work happened, e.g. Agency work, Freelance or Own project. Leave empty to hide the row.',
            'show_in_graphql' => 1,
        ),
        array(
            'key' => 'field_project_facts_showcase_video',
            'label' => 'Showcase video',
            'name' => 'showcase_video',
            'type' => 'file',
            'instructions' => 'Short muted screen recording (5-15s) shown in the homepage hero',
            'return_format' => 'url',
            'library' => 'all',
            'mime_types' => 'mp4,webm',
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
    'menu_order' => 1,
    'position' => 'side',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'active' => true,
    'show_in_graphql' => 1,
    'graphql_field_name' => 'projectFacts',
    'map_graphql_types_from_location_rules' => 0,
)));
