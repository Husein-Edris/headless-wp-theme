<?php

/**
 * WP-CLI: wp edris-i18n
 *
 * Export the English source text of the site as JSON for translation, and
 * import German translations into the German twin fields.
 *
 *   wp edris-i18n export [--file=<path>]
 *   wp edris-i18n import <file> [--dry-run]
 *
 * Field addressing ("paths"):
 *   core:title, core:excerpt, core:content   post_title, post_excerpt, post_content;
 *                                            German goes to title_de, excerpt_de, content_de
 *                                            (inc/acf-fields/translation-de.php)
 *   about_hero_title                         top-level ACF field
 *   hero_section/hero_title                  sub field of a group field
 *   experience_items/2/position              sub field of repeater row 2 (0-based)
 * Every ACF path names an English field; import writes its <name>_de twin
 * (inc/acf-fields/i18n.php). Import never writes English post fields; the
 * only English writes it allows are Site Content option names.
 *
 * Loaded by headless_pro_load_modules() only when WP_CLI is defined.
 *
 * @package HeadlessPro
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('WP_CLI') || !WP_CLI) {
    return;
}

class HeadlessProI18nCLI
{
    /**
     * Core post fields: export path => source post property and German target field key.
     */
    private const CORE_FIELDS = array(
        'core:title' => array('source' => 'post_title', 'target' => 'field_translation_de_title'),
        'core:excerpt' => array('source' => 'post_excerpt', 'target' => 'field_translation_de_excerpt'),
        'core:content' => array('source' => 'post_content', 'target' => 'field_translation_de_content'),
    );

    private const OPTIONS_POST_ID = 'option';

    /**
     * Export all translatable English text as JSON.
     *
     * Published posts of the types post, project, page, tech, book, hobby and
     * skill: core title, excerpt and content plus every ACF text, textarea and
     * wysiwyg field that has a German twin. Empty source strings are left out.
     * Current German values are listed under "de". "options" holds every
     * Site Content field (English names); "options_de" the German values set so far.
     *
     * ## OPTIONS
     *
     * [--file=<path>]
     * : Write the JSON to this file instead of printing it.
     *
     * ## EXAMPLES
     *
     *     wp edris-i18n export --file=translations-en.json
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     */
    public function export($args, $assoc_args)
    {
        $this->require_acf();

        $posts = get_posts(array(
            'post_type' => edris_i18n_translation_post_types(),
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => array('type' => 'ASC', 'ID' => 'ASC'),
            'suppress_filters' => true,
            'no_found_rows' => true,
        ));

        $export_posts = array();
        $string_count = 0;
        foreach ($posts as $post) {
            $entry = $this->export_post($post);
            $string_count += count($entry['fields']);
            $export_posts[] = $entry;
        }

        $options = $this->export_options();
        $data = array(
            'exported_at' => gmdate('c'),
            'site' => home_url(),
            'posts' => $export_posts,
            'options' => $options['en'] ? $options['en'] : new stdClass(),
            'options_de' => $options['de'] ? $options['de'] : new stdClass(),
        );

        $json = wp_json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            WP_CLI::error('Could not encode the export as JSON.');
        }

        $file = WP_CLI\Utils\get_flag_value($assoc_args, 'file', '');
        if ($file === '' || $file === true) {
            WP_CLI::line($json);
            return;
        }

        if (file_put_contents($file, $json . "\n") === false) {
            WP_CLI::error(sprintf('Could not write %s.', $file));
        }

        WP_CLI::success(sprintf(
            'Exported %d posts (%d strings) and %d options to %s.',
            count($export_posts),
            $string_count,
            count($options['en']),
            $file
        ));
    }

    /**
     * Import German translations from a JSON file.
     *
     * Input: {"posts": {"<id>": {"core:title": "...", "<acf path>": "..."}},
     *         "options": {"<name>": "...", "<name>_de": "..."}}
     * core:* go to title_de, excerpt_de, content_de; ACF paths go to the _de
     * twin of the named field; option names are written as given. A value equal
     * to the stored one is skipped; null is skipped; every write is read back.
     *
     * ## OPTIONS
     *
     * <file>
     * : JSON file to import.
     *
     * [--dry-run]
     * : Validate and report without writing anything.
     *
     * ## EXAMPLES
     *
     *     wp edris-i18n import translations-de.json --dry-run
     *     wp edris-i18n import translations-de.json
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     */
    public function import($args, $assoc_args)
    {
        $this->require_acf();

        $file = $args[0];
        if (!is_readable($file)) {
            WP_CLI::error(sprintf('Cannot read %s.', $file));
        }

        $data = json_decode((string) file_get_contents($file), true);
        if (!is_array($data)) {
            WP_CLI::error(sprintf('%s is not a JSON object: %s', $file, json_last_error_msg()));
        }

        $posts = $data['posts'] ?? array();
        $options = $data['options'] ?? array();
        if (!is_array($posts) || !is_array($options)) {
            WP_CLI::error('"posts" and "options" must be JSON objects.');
        }

        $dry_run = (bool) WP_CLI\Utils\get_flag_value($assoc_args, 'dry-run', false);
        $totals = array('written' => 0, 'skipped' => 0, 'errors' => 0);

        foreach ($posts as $post_id => $entries) {
            $result = $this->import_post((int) $post_id, $entries, $dry_run);
            $this->report($result, $dry_run);
            $totals = $this->add_totals($totals, $result);
        }

        if ($options) {
            $result = $this->import_options($options, $dry_run);
            $this->report($result, $dry_run);
            $totals = $this->add_totals($totals, $result);
        }

        $summary = sprintf(
            '%s%d %s, %d skipped, %d errors.',
            $dry_run ? 'Dry run, nothing written. ' : '',
            $totals['written'],
            $dry_run ? 'would be written' : 'written',
            $totals['skipped'],
            $totals['errors']
        );

        if ($totals['errors'] > 0) {
            WP_CLI::error($summary);
        }

        WP_CLI::success($summary);
    }

    // --- Export helpers ---

    private function export_post(WP_Post $post): array
    {
        $fields = array();
        $de = array();

        foreach (self::CORE_FIELDS as $path => $core) {
            $source = (string) $post->{$core['source']};
            if (trim($source) === '') {
                continue;
            }
            $fields[$path] = $source;
            $current = get_field($core['target'], $post->ID, false);
            if (is_string($current) && $current !== '') {
                $de[$path] = $current;
            }
        }

        $top_fields = $this->get_top_fields($post);
        $values = array();
        foreach ($top_fields as $field) {
            $values[$field['key']] = get_field($field['key'], $post->ID, false);
        }

        $collected = $this->collect_fields($top_fields, $values, '');

        return array(
            'id' => $post->ID,
            'type' => $post->post_type,
            'slug' => $post->post_name,
            'fields' => array_merge($fields, $collected['en']) ?: new stdClass(),
            'de' => array_merge($de, $collected['de']) ?: new stdClass(),
        );
    }

    /**
     * Walk a field list with its raw values and collect every translatable
     * English value (and its current German twin value) by path.
     *
     * @param array  $field_list ACF field definitions on one level.
     * @param array  $values     Raw values of that level, keyed by field key.
     * @param string $prefix     Path of the level, '' or ending in '/'.
     * @return array{en: array, de: array}
     */
    private function collect_fields(array $field_list, array $values, string $prefix): array
    {
        $en = array();
        $de = array();
        $by_name = $this->index_by_name($field_list);

        foreach ($field_list as $field) {
            if (empty($field['name'])) {
                continue;
            }

            $path = $prefix . $field['name'];
            $value = $values[$field['key']] ?? null;

            if ($this->is_group($field)) {
                $nested = $this->collect_fields($field['sub_fields'], is_array($value) ? $value : array(), $path . '/');
                $en = array_merge($en, $nested['en']);
                $de = array_merge($de, $nested['de']);
                continue;
            }

            if ($this->is_repeater($field)) {
                $rows = is_array($value) ? array_values($value) : array();
                foreach ($rows as $index => $row) {
                    $nested = $this->collect_fields($field['sub_fields'], is_array($row) ? $row : array(), $path . '/' . $index . '/');
                    $en = array_merge($en, $nested['en']);
                    $de = array_merge($de, $nested['de']);
                }
                continue;
            }

            $twin = $this->find_twin($field, $by_name);
            if ($twin === null || !is_string($value) || trim($value) === '') {
                continue;
            }

            $en[$path] = $value;
            $twin_value = $values[$twin['key']] ?? null;
            if (is_string($twin_value) && $twin_value !== '') {
                $de[$path] = $twin_value;
            }
        }

        return array('en' => $en, 'de' => $de);
    }

    /**
     * Site Content options: every English field with its current value, and
     * every German twin that has a value.
     *
     * @return array{en: array, de: array}
     */
    private function export_options(): array
    {
        $en = array();
        $de = array();
        $fields = $this->get_site_content_fields();

        foreach ($fields as $name => $field) {
            $value = get_field($field['key'], self::OPTIONS_POST_ID);
            $value = is_string($value) ? $value : '';

            if ($this->is_twin_name($name, $fields)) {
                if ($value !== '') {
                    $de[$name] = $value;
                }
                continue;
            }

            $en[$name] = $value;
        }

        return array('en' => $en, 'de' => $de);
    }

    // --- Import helpers ---

    /**
     * Import the German values of one post.
     *
     * @param int   $post_id Post ID.
     * @param mixed $entries Path => German value.
     * @return array Result with label, written, skipped and error messages.
     */
    private function import_post(int $post_id, $entries, bool $dry_run): array
    {
        $result = array('label' => sprintf('Post %d', $post_id), 'written' => 0, 'skipped' => 0, 'errors' => array());

        $post = get_post($post_id);
        if (!$post || !in_array($post->post_type, edris_i18n_translation_post_types(), true)) {
            $result['errors'][] = 'no post with a German translation group has this ID';
            return $result;
        }
        $result['label'] = sprintf('Post %d (%s/%s)', $post_id, $post->post_type, $post->post_name);

        if (!is_array($entries)) {
            $result['errors'][] = 'entry must be a JSON object of path => German text';
            return $result;
        }

        $top_fields = $this->get_top_fields($post);
        $direct = array();
        $patches = array();
        $to_verify = array();

        foreach ($entries as $path => $value) {
            $path = (string) $path;
            if ($value === null) {
                ++$result['skipped'];
                continue;
            }
            if (!is_string($value)) {
                $result['errors'][] = sprintf('%s: value must be a string', $path);
                continue;
            }

            $target = $this->resolve_import_path($path, $top_fields, $post_id);
            if (is_wp_error($target)) {
                $result['errors'][] = sprintf('%s: %s', $path, $target->get_error_message());
                continue;
            }

            if ($target['current'] === $value) {
                ++$result['skipped'];
                continue;
            }

            if ($target['top'] === null) {
                $direct[$target['twin']['key']] = $value;
            } else {
                $top_key = $target['top']['key'];
                $patches[$top_key] = $this->patch_set($patches[$top_key] ?? array(), $target['steps'], $value);
            }
            $to_verify[$path] = $value;
        }

        if ($dry_run) {
            $result['written'] = count($to_verify);
            return $result;
        }

        foreach ($direct as $key => $value) {
            update_field($key, wp_slash($value), $post_id);
        }
        foreach ($patches as $top_key => $patch) {
            update_field($top_key, wp_slash($patch), $post_id);
        }

        return $this->verify_post_writes($result, $to_verify, $top_fields, $post_id);
    }

    /**
     * Read every written path back and count it as written only if it matches.
     */
    private function verify_post_writes(array $result, array $to_verify, array $top_fields, int $post_id): array
    {
        foreach ($to_verify as $path => $value) {
            $target = $this->resolve_import_path($path, $top_fields, $post_id);
            if (!is_wp_error($target) && $target['current'] === $value) {
                ++$result['written'];
                continue;
            }
            $result['errors'][] = sprintf('%s: value read back after the write does not match', $path);
        }

        return $result;
    }

    /**
     * Resolve an import path to the German field it writes.
     *
     * @return array|WP_Error {top: ?array, steps: array, twin: array, current: string}
     *                        top is null when the twin is a top-level field
     *                        (written directly); otherwise steps lead from the
     *                        top-level field value to the twin inside it.
     */
    private function resolve_import_path(string $path, array $top_fields, int $post_id)
    {
        if (isset(self::CORE_FIELDS[$path])) {
            $twin = acf_get_field(self::CORE_FIELDS[$path]['target']);
            if (!$twin) {
                return new WP_Error('no_field', 'German translation group is not registered');
            }
            return array(
                'top' => null,
                'steps' => array(),
                'twin' => $twin,
                'current' => $this->as_string(get_field($twin['key'], $post_id, false)),
            );
        }

        $segments = explode('/', $path);
        $field_list = $top_fields;
        $values = null;
        $top = null;
        $steps = array();
        $count = count($segments);

        for ($i = 0; $i < $count; $i++) {
            $name = $segments[$i];
            $by_name = $this->index_by_name($field_list);
            if (!isset($by_name[$name])) {
                return new WP_Error('unknown', sprintf('no field "%s" on this post', $name));
            }
            $field = $by_name[$name];

            if ($top === null) {
                $value = get_field($field['key'], $post_id, false);
                $values = array($field['key'] => $value);
            } else {
                $value = $values[$field['key']] ?? null;
            }

            if ($this->is_group($field) || $this->is_repeater($field)) {
                if ($top === null) {
                    $top = $field;
                } else {
                    $steps[] = array('key' => $field['key']);
                }

                if ($this->is_group($field)) {
                    $field_list = $field['sub_fields'];
                    $values = is_array($value) ? $value : array();
                    continue;
                }

                $rows = is_array($value) ? array_values($value) : array();
                $row = $segments[$i + 1] ?? '';
                if (!ctype_digit($row)) {
                    return new WP_Error('row', sprintf('"%s" is a repeater, a row number must follow it', $name));
                }
                $row = (int) $row;
                if ($row >= count($rows)) {
                    return new WP_Error('row', sprintf('row %d of "%s" does not exist (%d rows)', $row, $name, count($rows)));
                }
                $steps[] = array('row' => $row, 'count' => count($rows));
                $field_list = $field['sub_fields'];
                $values = is_array($rows[$row]) ? $rows[$row] : array();
                ++$i;
                continue;
            }

            if ($i !== $count - 1) {
                return new WP_Error('leaf', sprintf('"%s" has no sub fields', $name));
            }

            $twin = $this->find_twin($field, $by_name);
            if ($twin === null) {
                return new WP_Error('not_translatable', sprintf('"%s" has no German twin', $name));
            }

            if ($top === null) {
                return array(
                    'top' => null,
                    'steps' => array(),
                    'twin' => $twin,
                    'current' => $this->as_string(get_field($twin['key'], $post_id, false)),
                );
            }

            $steps[] = array('key' => $twin['key']);
            return array(
                'top' => $top,
                'steps' => $steps,
                'twin' => $twin,
                'current' => $this->as_string($values[$twin['key']] ?? null),
            );
        }

        return new WP_Error('incomplete', 'path ends on a group or repeater, not on a text field');
    }

    /**
     * Set a value inside a group/repeater update patch. Repeater levels are
     * filled with empty rows up to their current row count, so the update keeps
     * every row; ACF skips sub fields missing from a row or group, so only the
     * German twins are written.
     *
     * @param array $patch Patch so far.
     * @param array $steps Remaining steps: {key} or {row, count}.
     * @param mixed $value Value to set at the end of the steps.
     * @return array New patch.
     */
    private function patch_set(array $patch, array $steps, $value): array
    {
        $step = array_shift($steps);

        if (isset($step['row'])) {
            for ($r = 0; $r < $step['count']; $r++) {
                if (!isset($patch[$r])) {
                    $patch[$r] = array();
                }
            }
            $index = $step['row'];
        } else {
            $index = $step['key'];
        }

        if (!$steps) {
            $patch[$index] = $value;
            return $patch;
        }

        $child = isset($patch[$index]) && is_array($patch[$index]) ? $patch[$index] : array();
        $patch[$index] = $this->patch_set($child, $steps, $value);

        return $patch;
    }

    /**
     * Import Site Content option values. Names are written as given (English
     * or _de twin); only fields of the Site Content group are accepted.
     */
    private function import_options(array $options, bool $dry_run): array
    {
        $result = array('label' => 'Options (Site Content)', 'written' => 0, 'skipped' => 0, 'errors' => array());
        $fields = $this->get_site_content_fields();

        foreach ($options as $name => $value) {
            $name = (string) $name;
            if (!isset($fields[$name])) {
                $result['errors'][] = sprintf('%s: not a Site Content field', $name);
                continue;
            }
            if ($value === null) {
                ++$result['skipped'];
                continue;
            }
            if (!is_string($value)) {
                $result['errors'][] = sprintf('%s: value must be a string', $name);
                continue;
            }

            $key = $fields[$name]['key'];
            if ($this->as_string(get_field($key, self::OPTIONS_POST_ID, false)) === $value) {
                ++$result['skipped'];
                continue;
            }

            if ($dry_run) {
                ++$result['written'];
                continue;
            }

            update_field($key, wp_slash($value), self::OPTIONS_POST_ID);

            if ($this->as_string(get_field($key, self::OPTIONS_POST_ID, false)) === $value) {
                ++$result['written'];
            } else {
                $result['errors'][] = sprintf('%s: value read back after the write does not match', $name);
            }
        }

        return $result;
    }

    // --- Shared helpers ---

    private function require_acf(): void
    {
        if (!function_exists('acf_get_field_groups') || !function_exists('edris_i18n_translation_post_types')) {
            WP_CLI::error('ACF Pro and the theme field groups must be loaded.');
        }
    }

    /**
     * Top-level fields of every field group shown on a post, except the German
     * translation group (core twins are handled as core:* paths).
     */
    private function get_top_fields(WP_Post $post): array
    {
        $groups = acf_get_field_groups(array(
            'post_id' => $post->ID,
            'post_type' => $post->post_type,
        ));

        $fields = array();
        foreach ($groups as $group) {
            if ($group['key'] === EDRIS_I18N_TRANSLATION_GROUP) {
                continue;
            }
            $group_fields = acf_get_fields($group);
            if (is_array($group_fields)) {
                $fields = array_merge($fields, $group_fields);
            }
        }

        return $fields;
    }

    /**
     * Site Content group fields with a name, keyed by name.
     */
    private function get_site_content_fields(): array
    {
        if (!defined('HEADLESS_PRO_SITE_CONTENT_GROUP')) {
            return array();
        }

        $fields = acf_get_fields(HEADLESS_PRO_SITE_CONTENT_GROUP);

        return is_array($fields) ? $this->index_by_name($fields) : array();
    }

    private function index_by_name(array $field_list): array
    {
        $by_name = array();
        foreach ($field_list as $field) {
            if (!empty($field['name'])) {
                $by_name[$field['name']] = $field;
            }
        }

        return $by_name;
    }

    /**
     * The German twin of an English field on the same level, or null when the
     * field has none (other types, testimonials, or a twin itself).
     */
    private function find_twin(array $field, array $by_name): ?array
    {
        if (!edris_i18n_is_translatable($field)) {
            return null;
        }

        return $by_name[$field['name'] . EDRIS_I18N_SUFFIX] ?? null;
    }

    private function is_twin_name(string $name, array $by_name): bool
    {
        $suffix_length = strlen(EDRIS_I18N_SUFFIX);
        if (substr($name, -$suffix_length) !== EDRIS_I18N_SUFFIX) {
            return false;
        }

        return isset($by_name[substr($name, 0, -$suffix_length)]);
    }

    private function is_group(array $field): bool
    {
        return ($field['type'] ?? '') === 'group' && !empty($field['sub_fields']) && is_array($field['sub_fields']);
    }

    private function is_repeater(array $field): bool
    {
        return ($field['type'] ?? '') === 'repeater' && !empty($field['sub_fields']) && is_array($field['sub_fields']);
    }

    private function as_string($value): string
    {
        return is_string($value) ? $value : '';
    }

    private function report(array $result, bool $dry_run): void
    {
        WP_CLI::log(sprintf(
            '%s: %d %s, %d skipped, %d errors',
            $result['label'],
            $result['written'],
            $dry_run ? 'would be written' : 'written',
            $result['skipped'],
            count($result['errors'])
        ));

        foreach ($result['errors'] as $message) {
            WP_CLI::warning(sprintf('  %s: %s', $result['label'], $message));
        }
    }

    private function add_totals(array $totals, array $result): array
    {
        return array(
            'written' => $totals['written'] + $result['written'],
            'skipped' => $totals['skipped'] + $result['skipped'],
            'errors' => $totals['errors'] + count($result['errors']),
        );
    }
}

WP_CLI::add_command('edris-i18n', 'HeadlessProI18nCLI');
