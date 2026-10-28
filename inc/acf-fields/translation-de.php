<?php

/**
 * ACF Fields: German translation (DE)
 *
 * German twins of the WordPress core fields, which ACF cannot clone:
 * title_de (post title), excerpt_de (excerpt) and content_de (editor content).
 * The German twins of ACF text fields are added by edris_with_de_fields()
 * (inc/acf-fields/i18n.php); this group is not wrapped with it.
 *
 * Post types: post, project, page, tech, book, hobby, skill, service.
 * Exposed over REST as acf_fields.title_de, excerpt_de, content_de
 * (inc/api.php always returns the three keys, empty when unset).
 *
 * @package HeadlessPro
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('edris_i18n_translation_post_types')) {
    /**
     * Post types that carry the German translation group.
     *
     * @return string[]
     */
    function edris_i18n_translation_post_types(): array
    {
        return array('post', 'project', 'page', 'tech', 'book', 'hobby', 'skill', 'service');
    }
}

if (!defined('EDRIS_I18N_TRANSLATION_GROUP')) {
    define('EDRIS_I18N_TRANSLATION_GROUP', 'group_translation_de');
}

$edris_i18n_translation_location = array();
foreach (edris_i18n_translation_post_types() as $edris_i18n_post_type) {
    $edris_i18n_translation_location[] = array(
        array(
            'param' => 'post_type',
            'operator' => '==',
            'value' => $edris_i18n_post_type,
        ),
    );
}

acf_add_local_field_group(array(
    'key' => EDRIS_I18N_TRANSLATION_GROUP,
    'title' => 'German translation (DE)',
    'fields' => array(
        array(
            'key' => 'field_translation_de_title',
            'label' => 'Title (DE)',
            'name' => 'title_de',
            'type' => 'text',
            'instructions' => 'German version of the title.',
            'show_in_graphql' => 1,
            'graphql_field_name' => 'titleDe',
        ),
        array(
            'key' => 'field_translation_de_excerpt',
            'label' => 'Excerpt (DE)',
            'name' => 'excerpt_de',
            'type' => 'textarea',
            'instructions' => 'German version of the excerpt.',
            'rows' => 3,
            'new_lines' => '',
            'show_in_graphql' => 1,
            'graphql_field_name' => 'excerptDe',
        ),
        array(
            'key' => 'field_translation_de_content',
            'label' => 'Content (DE)',
            'name' => 'content_de',
            'type' => 'wysiwyg',
            'instructions' => 'German version of the main content.',
            'tabs' => 'all',
            'toolbar' => 'full',
            'media_upload' => 1,
            'delay' => 0,
            'show_in_graphql' => 1,
            'graphql_field_name' => 'contentDe',
        ),
    ),
    'location' => $edris_i18n_translation_location,
    'menu_order' => -10,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'active' => true,
    'description' => 'German title, excerpt and content',
    'show_in_graphql' => 1,
    'graphql_field_name' => 'translationDe',
    'map_graphql_types_from_location_rules' => 0,
));
