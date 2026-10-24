<?php

/**
 * ACF German twins: generic field cloner
 *
 * edris_with_de_fields() takes a field group definition and inserts a German
 * twin right after every text, textarea and wysiwyg field, also inside the
 * sub_fields of group and repeater fields:
 *
 *   name  <name>_de       key  <key>_de       label  <label> (DE)
 *
 * The twin keeps the type and settings of its source, except: never required,
 * no default_value, no conditional_logic, and a graphql_field_name (when the
 * source has one) gets a "De" suffix so WPGraphQL does not see a duplicate.
 *
 * Every group in inc/acf-fields/ is wrapped with it, except testimonials.php
 * (client quotes stay in their original language) and translation-de.php
 * (already German). REST picks the twins up with no extra code: acf_fields
 * (inc/api.php) and /headless/v1/site-content both walk the registered fields.
 * WP-CLI import/export: inc/cli-i18n.php (wp edris-i18n).
 *
 * Loaded first by inc/acf-fields.php, before the group files.
 *
 * @package HeadlessPro
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('EDRIS_I18N_SUFFIX')) {
    // Suffix of every German field name and key.
    define('EDRIS_I18N_SUFFIX', '_de');
}

if (!function_exists('edris_i18n_translatable_types')) {
    /**
     * ACF field types that hold editable text and get a German twin.
     *
     * @return string[]
     */
    function edris_i18n_translatable_types(): array
    {
        return array('text', 'textarea', 'wysiwyg');
    }
}

if (!function_exists('edris_i18n_is_translatable')) {
    /**
     * Whether a field definition gets a German twin.
     *
     * @param mixed $field ACF field definition.
     * @return bool
     */
    function edris_i18n_is_translatable($field): bool
    {
        return is_array($field)
            && !empty($field['name'])
            && !empty($field['key'])
            && in_array($field['type'] ?? '', edris_i18n_translatable_types(), true);
    }
}

if (!function_exists('edris_i18n_make_de_field')) {
    /**
     * Build the German twin of one text, textarea or wysiwyg field.
     *
     * @param array $field Source field definition.
     * @return array Twin field definition.
     */
    function edris_i18n_make_de_field(array $field): array
    {
        $twin = $field;
        $twin['key'] = $field['key'] . EDRIS_I18N_SUFFIX;
        $twin['name'] = $field['name'] . EDRIS_I18N_SUFFIX;
        $twin['label'] = ($field['label'] ?? $field['name']) . ' (DE)';
        $twin['required'] = 0;
        $twin['default_value'] = '';
        $twin['conditional_logic'] = 0;

        if (!empty($field['graphql_field_name'])) {
            $twin['graphql_field_name'] = $field['graphql_field_name'] . 'De';
        }

        return $twin;
    }
}

if (!function_exists('edris_i18n_clone_fields')) {
    /**
     * Insert a German twin after every translatable field of a field list,
     * recursing into the sub_fields of group and repeater fields.
     *
     * @param array $fields ACF field definitions.
     * @return array New field list with the twins inserted.
     */
    function edris_i18n_clone_fields(array $fields): array
    {
        $result = array();

        foreach ($fields as $field) {
            $is_container = is_array($field)
                && in_array($field['type'] ?? '', array('group', 'repeater'), true)
                && !empty($field['sub_fields'])
                && is_array($field['sub_fields']);

            if ($is_container) {
                $field['sub_fields'] = edris_i18n_clone_fields($field['sub_fields']);
            }

            $result[] = $field;

            if (edris_i18n_is_translatable($field)) {
                $result[] = edris_i18n_make_de_field($field);
            }
        }

        return $result;
    }
}

if (!function_exists('edris_with_de_fields')) {
    /**
     * Add German twins to a field group definition.
     *
     * Usage: acf_add_local_field_group(edris_with_de_fields(array(...)));
     *
     * @param array $group ACF field group definition.
     * @return array The same group with German twins in its field list.
     */
    function edris_with_de_fields(array $group): array
    {
        if (!empty($group['fields']) && is_array($group['fields'])) {
            $group['fields'] = edris_i18n_clone_fields($group['fields']);
        }

        return $group;
    }
}
