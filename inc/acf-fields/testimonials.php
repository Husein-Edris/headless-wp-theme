<?php

/**
 * ACF Fields: Testimonials
 *
 * Two groups:
 * 1. "Testimonial" on the testimonial post type (inc/post-types.php): the quote,
 *    client name, company and role, logo, website and language of one review.
 *    REST: /wp-json/wp/v2/testimonial, key acf_fields.
 * 2. "Testimonials" on the front page: section heading, button labels and the
 *    relationship that picks and orders the quotes shown on the homepage.
 *    REST: /wp-json/wp/v2/pages?slug=home, key acf_fields.testimonials_section
 *
 * Frontend: src/lib/transform/transformTestimonials.ts (edrishusein.com).
 * Every field is optional for the frontend: an empty title, button label or
 * selection makes it show its built-in defaults (four quotes). A testimonial
 * named like a built-in client without its own company, logo or website reuses
 * the bundled one.
 *
 * @package HeadlessPro
 */

if (!defined('ABSPATH')) {
    exit;
}

acf_add_local_field_group(array(
    'key' => 'group_testimonial_post',
    'title' => 'Testimonial',
    'fields' => array(
        array(
            'key' => 'field_testimonial_quote',
            'label' => 'Quote',
            'name' => 'quote',
            'type' => 'textarea',
            'required' => 1,
            'rows' => 4,
            'new_lines' => '',
            'instructions' => 'Plain text, no quotation marks (the design adds them). No dates, no platform names.',
        ),
        array(
            'key' => 'field_testimonial_name',
            'label' => 'Client name',
            'name' => 'name',
            'type' => 'text',
            'instructions' => 'As the client shows it publicly, e.g. "Leon Heuser". Shown in white under the quote. Empty uses the title above.',
            'placeholder' => 'Leon Heuser',
            'wrapper' => array('width' => '50', 'class' => '', 'id' => ''),
        ),
        array(
            'key' => 'field_testimonial_company',
            'label' => 'Company and role (optional)',
            'name' => 'company',
            'type' => 'text',
            'instructions' => 'Shown in grey under the client name, e.g. "Managing Director, ReviewForest". Empty hides it.',
            'placeholder' => 'Managing Director, ReviewForest',
            'wrapper' => array('width' => '50', 'class' => '', 'id' => ''),
        ),
        array(
            'key' => 'field_testimonial_logo',
            'label' => 'Client logo (optional)',
            'name' => 'logo',
            'type' => 'image',
            'instructions' => 'Shown small before the name and turned light grey, whatever its colours. Needs a transparent background: SVG or PNG with transparency (a white background would show as a grey block). Empty uses the bundled logo for ReviewForest, Campzilla and Stars Media.',
            'return_format' => 'array',
            'preview_size' => 'thumbnail',
            'library' => 'all',
            'mime_types' => 'png,jpg,jpeg,webp,svg',
            'wrapper' => array('width' => '50', 'class' => '', 'id' => ''),
        ),
        array(
            'key' => 'field_testimonial_website_url',
            'label' => 'Client website (optional)',
            'name' => 'website_url',
            'type' => 'url',
            'instructions' => 'Full https:// address. When set, the logo, name and company link to it (new tab). The address itself is not shown.',
            'wrapper' => array('width' => '30', 'class' => '', 'id' => ''),
        ),
        array(
            'key' => 'field_testimonial_lang',
            'label' => 'Language',
            'name' => 'lang',
            'type' => 'select',
            'choices' => array(
                'en' => 'English',
                'de' => 'German',
            ),
            'default_value' => 'en',
            'return_format' => 'value',
            'wrapper' => array('width' => '20', 'class' => '', 'id' => ''),
        ),
    ),
    'location' => array(
        array(
            array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'testimonial',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'acf_after_title',
    'style' => 'seamless',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'active' => true,
    'show_in_rest' => 0,
));

acf_add_local_field_group(array(
    'key' => 'group_homepage_testimonials',
    'title' => 'Testimonials',
    'fields' => array(
        array(
            'key' => 'field_testimonials_section',
            'label' => 'Testimonials',
            'name' => 'testimonials_section',
            'type' => 'group',
            'layout' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_testimonials_section_title',
                    'label' => 'Section title',
                    'name' => 'section_title',
                    'type' => 'text',
                    'instructions' => 'Heading above the quotes. Empty shows "What clients say".',
                    'default_value' => 'What clients say',
                    'placeholder' => 'What clients say',
                ),
                array(
                    'key' => 'field_testimonials_prev_label',
                    'label' => 'Previous button text',
                    'name' => 'prev_label',
                    'type' => 'text',
                    'instructions' => 'Empty shows "Prev". Only visible when more than one quote is shown.',
                    'default_value' => 'Prev',
                    'placeholder' => 'Prev',
                    'wrapper' => array('width' => '50', 'class' => '', 'id' => ''),
                ),
                array(
                    'key' => 'field_testimonials_next_label',
                    'label' => 'Next button text',
                    'name' => 'next_label',
                    'type' => 'text',
                    'instructions' => 'Empty shows "Next".',
                    'default_value' => 'Next',
                    'placeholder' => 'Next',
                    'wrapper' => array('width' => '50', 'class' => '', 'id' => ''),
                ),
                array(
                    'key' => 'field_testimonials_section_testimonials',
                    'label' => 'Testimonials',
                    'name' => 'testimonials',
                    'type' => 'relationship',
                    'instructions' => 'Pick and order the testimonials shown on the homepage. Empty shows the built-in defaults. One quote shows at a time; visitors step through them with Prev/Next (no autoplay), starting with the first one here. Add or edit quotes under Testimonials in the admin menu.',
                    'post_type' => array('testimonial'),
                    'post_status' => array('publish'),
                    'taxonomy' => '',
                    'filters' => array('search'),
                    'elements' => '',
                    'min' => '',
                    'max' => '',
                    'return_format' => 'id',
                ),
            ),
        ),
    ),
    'location' => array(
        array(
            array(
                'param' => 'page_type',
                'operator' => '==',
                'value' => 'front_page',
            ),
        ),
    ),
    'menu_order' => 5,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'active' => true,
    'show_in_rest' => 0,
));
