<?php

/**
 * ACF Fields: Service page
 *
 * Two groups on the service post type (inc/post-types.php):
 * 1. "Service page" (group_service_page): SEO title and description, H1,
 *    hero note and card icon, "What you get" items, steps, short text blocks,
 *    FAQ, linked case studies, articles and one testimonial, schema data and
 *    the closing call to action. Wrapped in edris_with_de_fields(), so every text,
 *    textarea and wysiwyg field has a German twin <name>_de.
 * 2. "Service routing" (group_service_routing): slug_de, the German URL slug
 *    (/de/services/<slug_de>; the post slug is the English one). Not wrapped,
 *    so there is no slug_de_de twin.
 *
 * Post title = short name for cards, nav and breadcrumb (German: title_de),
 * editor = intro, excerpt = card teaser (inc/acf-fields/translation-de.php).
 * Shared template labels (FAQ and projects headings): Site Content > Services pages.
 *
 * REST: /wp-json/wp/v2/service, key acf_fields (inc/api.php always returns
 * slug_de, empty when unset). Empty repeaters and relationships arrive as false.
 *
 * @package HeadlessPro
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('HEADLESS_PRO_SERVICE_SLUG_DE_FIELD')) {
    define('HEADLESS_PRO_SERVICE_SLUG_DE_FIELD', 'field_service_slug_de');
}

if (!function_exists('headless_pro_validate_service_slug_de')) {
    /**
     * ACF validation of slug_de: lowercase letters, digits and hyphens only.
     * An empty value is left to the required check.
     *
     * @param bool|string $valid Result of earlier checks (true or an error message).
     * @param mixed       $value Submitted value.
     * @return bool|string True when valid, otherwise an error message.
     */
    function headless_pro_validate_service_slug_de($valid, $value)
    {
        if ($valid !== true) {
            return $valid;
        }

        $value = is_string($value) ? $value : '';
        if ($value === '' || preg_match('/^[a-z0-9-]+$/', $value)) {
            return $valid;
        }

        return 'Use lowercase letters, digits and hyphens only, e.g. website-erstellen-lassen.';
    }
}

add_filter('acf/validate_value/key=' . HEADLESS_PRO_SERVICE_SLUG_DE_FIELD, 'headless_pro_validate_service_slug_de', 10, 2);

// Keys match SERVICE_ICON_KEYS in the frontend (src/lib/service-icons.ts).
$headless_pro_service_icons = array(
                'code' => 'Code',
                'layout' => 'Layout / design',
                'mobile' => 'Mobile',
                'search' => 'Search / SEO',
                'edit' => 'Edit content',
                'key' => 'Ownership / access',
                'speed' => 'Speed',
                'shield' => 'Security',
                'update' => 'Updates',
                'redirect' => 'Redirects',
                'wrench' => 'Repair',
                'chat' => 'Talk / support',
);

$headless_pro_service_location = array(
    array(
        array(
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'service',
        ),
    ),
);

acf_add_local_field_group(edris_with_de_fields(array(
    'key' => 'group_service_page',
    'title' => 'Service page',
    'fields' => array(

        // --- SEO and heading ---
        array(
            'key' => 'field_service_tab_heading',
            'label' => 'SEO and heading',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        ),
        array(
            'key' => 'field_service_seo_title',
            'label' => 'SEO title',
            'name' => 'seo_title',
            'type' => 'text',
            'instructions' => 'Browser tab and search result title, up to 60 characters. Leave empty to use: post title | Edris Husein',
            'maxlength' => 60,
        ),
        array(
            'key' => 'field_service_seo_description',
            'label' => 'SEO description',
            'name' => 'seo_description',
            'type' => 'textarea',
            'instructions' => 'Search result description, up to 155 characters. Also the description in the Service schema.',
            'maxlength' => 155,
            'rows' => 3,
            'new_lines' => '',
        ),
        array(
            'key' => 'field_service_eyebrow',
            'label' => 'Eyebrow',
            'name' => 'eyebrow',
            'type' => 'text',
            'instructions' => 'Small label above the main heading. Leave empty to hide it.',
        ),
        array(
            'key' => 'field_service_headline',
            'label' => 'Main heading (H1)',
            'name' => 'headline',
            'type' => 'text',
            'instructions' => 'The H1 of the page, e.g. Website development for businesses in Vorarlberg. The post title stays the short name on cards and in the menu.',
            'required' => 1,
        ),
        array(
            'key' => 'field_service_primary_keyword',
            'label' => 'Primary keyword',
            'name' => 'primary_keyword',
            'type' => 'text',
            'instructions' => 'Editorial note, not shown on the site: the search phrase this page targets.',
        ),
        array(
            'key' => 'field_service_hero_note',
            'label' => 'Note under the button',
            'name' => 'hero_note',
            'type' => 'text',
            'instructions' => 'Short line under the hero button, e.g. Replies within one working day. Leave empty to hide it.',
        ),
        array(
            'key' => 'field_service_icon',
            'label' => 'Card icon',
            'name' => 'icon',
            'type' => 'select',
            'instructions' => 'Icon on the service card on /services.',
            'choices' => $headless_pro_service_icons,
            'default_value' => 'code',
            'return_format' => 'value',
        ),

        // --- Sections (H2 blocks) ---
        array(
            'key' => 'field_service_tab_sections',
            'label' => 'Sections',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        ),
        array(
            'key' => 'field_service_sections',
            'label' => 'Sections',
            'name' => 'sections',
            'type' => 'repeater',
            'instructions' => 'Short text blocks after the steps, shown side by side, e.g. Freelancer or agency. One or two work best.',
            'layout' => 'block',
            'min' => 0,
            'max' => 0,
            'collapsed' => 'field_service_sections_heading',
            'button_label' => 'Add section',
            'sub_fields' => array(
                array(
                    'key' => 'field_service_sections_heading',
                    'label' => 'Heading (H2)',
                    'name' => 'heading',
                    'type' => 'text',
                    'parent_repeater' => 'field_service_sections',
                ),
                array(
                    'key' => 'field_service_sections_body',
                    'label' => 'Text',
                    'name' => 'body',
                    'type' => 'wysiwyg',
                    'tabs' => 'all',
                    'toolbar' => 'basic',
                    'media_upload' => 1,
                    'delay' => 0,
                    'parent_repeater' => 'field_service_sections',
                ),
            ),
        ),

        // --- What you get and how it works ---
        array(
            'key' => 'field_service_tab_blocks',
            'label' => 'What you get and steps',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        ),
        array(
            'key' => 'field_service_deliverables',
            'label' => 'What you get',
            'name' => 'deliverables',
            'type' => 'repeater',
            'instructions' => 'Shown under the hero: an icon, a short title and one sentence each. Four works best. Leave empty to hide the block.',
            'layout' => 'table',
            'min' => 0,
            'max' => 6,
            'collapsed' => 'field_service_deliverables_title',
            'button_label' => 'Add item',
            'sub_fields' => array(
                array(
                    'key' => 'field_service_deliverables_icon',
                    'label' => 'Icon',
                    'name' => 'icon',
                    'type' => 'select',
                    'choices' => $headless_pro_service_icons,
                    'default_value' => 'code',
                    'return_format' => 'value',
                    'parent_repeater' => 'field_service_deliverables',
                ),
                array(
                    'key' => 'field_service_deliverables_title',
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => 'text',
                    'parent_repeater' => 'field_service_deliverables',
                ),
                array(
                    'key' => 'field_service_deliverables_text',
                    'label' => 'Text',
                    'name' => 'text',
                    'type' => 'textarea',
                    'rows' => 2,
                    'new_lines' => '',
                    'parent_repeater' => 'field_service_deliverables',
                ),
            ),
        ),
        array(
            'key' => 'field_service_process',
            'label' => 'How it works',
            'name' => 'process',
            'type' => 'repeater',
            'instructions' => 'Numbered steps from first message to launch. Four or five work best. Leave empty to hide the block.',
            'layout' => 'table',
            'min' => 0,
            'max' => 6,
            'collapsed' => 'field_service_process_title',
            'button_label' => 'Add step',
            'sub_fields' => array(
                array(
                    'key' => 'field_service_process_title',
                    'label' => 'Step',
                    'name' => 'title',
                    'type' => 'text',
                    'parent_repeater' => 'field_service_process',
                ),
                array(
                    'key' => 'field_service_process_text',
                    'label' => 'Text',
                    'name' => 'text',
                    'type' => 'textarea',
                    'rows' => 2,
                    'new_lines' => '',
                    'parent_repeater' => 'field_service_process',
                ),
            ),
        ),

        // --- FAQ ---
        array(
            'key' => 'field_service_tab_faq',
            'label' => 'FAQ',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        ),
        array(
            'key' => 'field_service_faq',
            'label' => 'FAQ',
            'name' => 'faq',
            'type' => 'repeater',
            'instructions' => 'Questions shown on the page and in the FAQPage schema. Leave empty to hide the FAQ.',
            'layout' => 'block',
            'min' => 0,
            'max' => 0,
            'collapsed' => 'field_service_faq_question',
            'button_label' => 'Add question',
            'sub_fields' => array(
                array(
                    'key' => 'field_service_faq_question',
                    'label' => 'Question',
                    'name' => 'question',
                    'type' => 'text',
                    'parent_repeater' => 'field_service_faq',
                ),
                array(
                    'key' => 'field_service_faq_answer',
                    'label' => 'Answer',
                    'name' => 'answer',
                    'type' => 'wysiwyg',
                    'tabs' => 'all',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                    'delay' => 0,
                    'parent_repeater' => 'field_service_faq',
                ),
            ),
        ),

        // --- Proof and further reading ---
        array(
            'key' => 'field_service_tab_links',
            'label' => 'Projects and articles',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        ),
        array(
            'key' => 'field_service_related_projects',
            'label' => 'Related projects',
            'name' => 'related_projects',
            'type' => 'relationship',
            'instructions' => 'Case studies shown as proof on this page. Each project page links back to the services that list it.',
            'post_type' => array('project'),
            'return_format' => 'id',
            'min' => 0,
            'max' => 0,
            'filters' => array('search'),
        ),
        array(
            'key' => 'field_service_related_posts',
            'label' => 'Related articles',
            'name' => 'related_posts',
            'type' => 'relationship',
            'instructions' => 'Articles linked as further reading.',
            'post_type' => array('post'),
            'return_format' => 'id',
            'min' => 0,
            'max' => 0,
            'filters' => array('search'),
        ),
        array(
            'key' => 'field_service_showcase_image',
            'label' => 'Showcase image',
            'name' => 'showcase_image',
            'type' => 'image',
            'instructions' => 'Shown on this page and its /services row only when no related project has a screenshot, e.g. a real maintenance report (no client names). Landscape 16:10, shown whole.',
            'return_format' => 'array',
            'preview_size' => 'medium',
            'library' => 'all',
        ),
        array(
            'key' => 'field_service_showcase_caption',
            'label' => 'Showcase caption',
            'name' => 'showcase_caption',
            'type' => 'text',
            'instructions' => 'Line under the showcase image, e.g. Monthly maintenance report, September 2026.',
        ),
        array(
            'key' => 'field_service_testimonial',
            'label' => 'Testimonial',
            'name' => 'testimonial',
            'type' => 'relationship',
            'instructions' => 'One client quote shown on this page. Leave empty to hide it.',
            'post_type' => array('testimonial'),
            'return_format' => 'id',
            'min' => 0,
            'max' => 1,
            'filters' => array('search'),
        ),

        // --- Schema ---
        array(
            'key' => 'field_service_tab_schema',
            'label' => 'Schema',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        ),
        array(
            'key' => 'field_service_service_type',
            'label' => 'Service type',
            'name' => 'service_type',
            'type' => 'text',
            'instructions' => 'serviceType in the Service schema, e.g. Website development. Not shown on the page.',
        ),
        array(
            'key' => 'field_service_area_served',
            'label' => 'Area served',
            'name' => 'area_served',
            'type' => 'checkbox',
            'instructions' => 'areaServed in the Service schema. Not shown on the page.',
            'choices' => array(
                'vorarlberg' => 'Vorarlberg',
                'austria' => 'Austria',
                'switzerland' => 'Switzerland',
                'germany' => 'Germany',
            ),
            'layout' => 'horizontal',
            'return_format' => 'value',
        ),

        // --- Call to action (end of the page, links to /contact) ---
        array(
            'key' => 'field_service_tab_cta',
            'label' => 'Call to action',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        ),
        array(
            'key' => 'field_service_cta_heading',
            'label' => 'Heading',
            'name' => 'cta_heading',
            'type' => 'text',
            'instructions' => 'Heading of the closing block that links to /contact.',
        ),
        array(
            'key' => 'field_service_cta_text',
            'label' => 'Text',
            'name' => 'cta_text',
            'type' => 'textarea',
            'instructions' => 'Short paragraph under the heading. Plain text.',
            'rows' => 3,
            'new_lines' => '',
        ),
        array(
            'key' => 'field_service_contact_message',
            'label' => 'Prefilled message',
            'name' => 'contact_message',
            'type' => 'textarea',
            'instructions' => 'Text already typed in when a visitor taps Email or WhatsApp on this page, e.g. Hi Edris, my website is: … What happened: … Line breaks are kept. The email subject is the post title.',
            'rows' => 3,
            'new_lines' => '',
        ),
        array(
            'key' => 'field_service_cta_label',
            'label' => 'Button label',
            'name' => 'cta_label',
            'type' => 'text',
            'instructions' => 'Button to /contact, shown only when Site Content has no email address. The page otherwise shows the Email and WhatsApp buttons.',
        ),
    ),
    'location' => $headless_pro_service_location,
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'active' => true,
    'description' => 'Service page content, SEO and schema (EN with German twins)',
    'show_in_graphql' => 0,
)));

acf_add_local_field_group(array(
    'key' => 'group_service_routing',
    'title' => 'Service routing',
    'fields' => array(
        array(
            'key' => HEADLESS_PRO_SERVICE_SLUG_DE_FIELD,
            'label' => 'German slug',
            'name' => 'slug_de',
            'type' => 'text',
            'instructions' => 'URL of the German page. Lowercase letters, digits and hyphens only, e.g. website-erstellen-lassen. The English URL uses the post slug.',
            'required' => 1,
            'prepend' => '/de/services/',
        ),
    ),
    'location' => $headless_pro_service_location,
    'menu_order' => -20,
    'position' => 'side',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'active' => true,
    'description' => 'German URL slug of a service page',
    'show_in_graphql' => 0,
));
