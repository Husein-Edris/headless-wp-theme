<?php

/**
 * ACF Options Page: Site Content
 *
 * Text that belongs to a template or an archive rather than to one page:
 * the /projects, /notebook, /bookshelf, /tech-stack and /contact pages, the
 * project cards, the case study and article templates, and shared chrome
 * (footer, 404 page, cookie banner). None of these has its own WordPress page
 * to hold fields.
 *
 * Admin: "Site Content" menu (capability edit_pages, so editors see it).
 * The page is registered by acf_add_options_page(), which ACF renders through
 * its options-page module; the acf/settings/show_admin filter in
 * inc/acf-fields.php only hides the field-group editor, not this page.
 *
 * REST: GET /wp-json/headless/v1/site-content (inc/api.php).
 * Every field is optional: an empty field makes the frontend show its own
 * built-in text, which is the same as the default_value below.
 *
 * @package HeadlessPro
 */

if (!defined('ABSPATH')) {
    exit;
}

if (function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title' => 'Site Content',
        'menu_title' => 'Site Content',
        'menu_slug' => 'site-content',
        'capability' => 'edit_pages',
        'icon_url' => 'dashicons-edit-page',
        'position' => 21,
        'redirect' => false,
        'post_id' => 'options',
        'autoload' => true,
        'update_button' => 'Save content',
        'updated_message' => 'Site content saved. The live site picks it up within about a minute.',
    ));
}

if (!function_exists('headless_pro_site_content_text_field')) {
    /**
     * Build one optional text field whose default matches the frontend fallback.
     *
     * @param string $name    Field name (also the REST key).
     * @param string $label   Admin label.
     * @param string $where   Where the text appears on the site.
     * @param string $default Text the frontend shows while the field is empty.
     * @param string $type    ACF field type (text or textarea).
     * @return array ACF field definition.
     */
    function headless_pro_site_content_text_field(string $name, string $label, string $where, string $default, string $type = 'text'): array
    {
        $field = array(
            'key' => 'field_site_content_' . $name,
            'label' => $label,
            'name' => $name,
            'type' => $type,
            'instructions' => $where . ' Leave empty to show: ' . $default,
            'default_value' => $default,
            'show_in_graphql' => 1,
        );

        if ($type === 'textarea') {
            $field['rows'] = 5;
            $field['new_lines'] = '';
        }

        return $field;
    }
}

if (!function_exists('headless_pro_site_content_tab')) {
    /**
     * Build a tab separator for the Site Content options page.
     *
     * @param string $key   Unique suffix for the field key.
     * @param string $label Tab label.
     * @return array ACF tab definition.
     */
    function headless_pro_site_content_tab(string $key, string $label): array
    {
        return array(
            'key' => 'field_site_content_tab_' . $key,
            'label' => $label,
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        );
    }
}

$headless_pro_contact_intro = 'Open to freelance projects, collaborations, or just a chat about web development. I build WordPress sites, headless setups, and React or Next.js applications, mostly for agencies and small teams in Austria and Germany, though remote work anywhere is fine. If you are writing about a project, a rough scope and timeline helps me give you a useful answer instead of a vague one. I usually reply within a day.';

$headless_pro_bookshelf_intro = "Most of what I know about building software came from the work itself, but these shaped how I think about it: how to structure a system, and how to write something a colleague can still read six months later.\n\nThe list covers software and design, plus a few books with nothing to do with either. I add to it when something earns a place.";

if (!defined('HEADLESS_PRO_SITE_CONTENT_GROUP')) {
    // Field group key; GET /headless/v1/site-content returns only this group's fields.
    define('HEADLESS_PRO_SITE_CONTENT_GROUP', 'group_site_content');
}

acf_add_local_field_group(array(
    'key' => HEADLESS_PRO_SITE_CONTENT_GROUP,
    'title' => 'Site Content',
    'fields' => array(

        // --- Projects page (/projects) ---
        headless_pro_site_content_tab('projects_archive', 'Projects page'),
        headless_pro_site_content_text_field('projects_archive_title', 'Page heading', 'Large heading at the top of /projects.', 'PROJECTS'),
        headless_pro_site_content_text_field('projects_archive_subtitle', 'Subtitle', 'Line under the heading on /projects.', 'Web development case studies: Next.js, WordPress and more'),
        headless_pro_site_content_text_field('projects_archive_count_label', 'Case study count label', 'Words after the project count on /projects, e.g. 12 case studies.', 'case studies'),
        headless_pro_site_content_text_field('projects_archive_featured_label', 'Featured card label', 'Small label above each highlighted project on /projects.', 'Featured project'),
        headless_pro_site_content_text_field('projects_archive_more_heading', 'More projects heading', 'Heading above the grid that follows the highlighted projects on /projects.', 'More projects'),

        // --- Project cards (homepage, /projects, case study "More projects") ---
        headless_pro_site_content_tab('project_card', 'Project cards'),
        headless_pro_site_content_text_field('project_card_read_case_study_label', 'Case study link label', 'Link text on every project card (homepage, /projects and the More projects block on case studies).', 'Read case study'),
        headless_pro_site_content_text_field('project_card_visit_live_site_label', 'Live site link label', 'Link text for the external live site on every project card.', 'Visit live site'),

        // --- Case study template (/projects/<slug>) ---
        headless_pro_site_content_tab('case_study', 'Case study template'),
        headless_pro_site_content_text_field('case_study_challenge_heading', 'Challenge heading', 'Heading above the Challenge text on every case study.', 'The Challenge'),
        headless_pro_site_content_text_field('case_study_solution_heading', 'Solution heading', 'Heading above the Solution text on every case study.', 'The Solution'),
        headless_pro_site_content_text_field('case_study_facts_heading', 'Facts column heading', 'Heading of the facts column for screen readers (not shown on screen).', 'Project facts'),
        headless_pro_site_content_text_field('case_study_facts_client_label', 'Facts: Client label', 'Label of the Client row in the facts column (value: Project facts > Client).', 'Client'),
        headless_pro_site_content_text_field('case_study_facts_role_label', 'Facts: Role label', 'Label of the Role row in the facts column (value: Project facts > Role).', 'Role'),
        headless_pro_site_content_text_field('case_study_facts_year_label', 'Facts: Year label', 'Label of the Year row in the facts column (value: Project facts > Year).', 'Year'),
        headless_pro_site_content_text_field('case_study_facts_built_at_label', 'Facts: Built at label', 'Label of the Built at row in the facts column (value: Project facts > Built at).', 'Built at'),
        headless_pro_site_content_text_field('case_study_facts_stack_label', 'Facts: Stack label', 'Label of the technology chips in the facts column (value: the project Tech Stack).', 'Stack'),
        headless_pro_site_content_text_field('case_study_facts_live_site_label', 'Facts: Live site label', 'Label of the live site link in the facts column (value: Project Links > Live site).', 'Live site'),
        headless_pro_site_content_text_field('case_study_facts_github_label', 'Facts: GitHub label', 'Label of the source code link in the facts column (value: Project Links > GitHub).', 'GitHub'),
        headless_pro_site_content_text_field('case_study_features_heading', 'Key features heading', 'Heading above the key features list on every case study.', 'Key Features'),
        headless_pro_site_content_text_field('case_study_gallery_heading', 'Gallery heading', 'Heading above the image gallery on every case study.', 'Project Gallery'),
        headless_pro_site_content_text_field('case_study_more_projects_heading', 'More projects heading', 'Heading of the three related projects at the end of every case study.', 'More Projects'),
        headless_pro_site_content_text_field('case_study_lightbox_close_label', 'Gallery lightbox: Close label', 'Screen reader label of the close button in the full-screen gallery view.', 'Close'),
        headless_pro_site_content_text_field('case_study_lightbox_previous_label', 'Gallery lightbox: Previous label', 'Screen reader label of the previous image button in the full-screen gallery view.', 'Previous image'),
        headless_pro_site_content_text_field('case_study_lightbox_next_label', 'Gallery lightbox: Next label', 'Screen reader label of the next image button in the full-screen gallery view.', 'Next image'),
        headless_pro_site_content_text_field('case_study_lightbox_counter_format', 'Gallery lightbox: Counter format', 'Image counter in the full-screen gallery view. {current} is the open image, {total} the image count.', '{current} / {total}'),

        // --- Contact page (/contact) ---
        headless_pro_site_content_tab('contact_page', 'Contact page'),
        headless_pro_site_content_text_field('contact_page_subtitle', 'Small line above the heading', 'Shown above the heading on /contact.', 'Freelance WordPress and Next.js work'),
        headless_pro_site_content_text_field('contact_page_heading', 'Heading', 'Large heading on /contact.', 'Start a project'),
        headless_pro_site_content_text_field('contact_page_intro', 'Intro paragraph', 'Paragraph under the heading on /contact. Plain text.', $headless_pro_contact_intro, 'textarea'),
        headless_pro_site_content_text_field('contact_page_email', 'Email address', 'Address shown and linked on /contact.', 'kontakt@edrishusein.com'),

        // --- Notebook page (/notebook) ---
        headless_pro_site_content_tab('notebook_page', 'Notebook page'),
        headless_pro_site_content_text_field('notebook_page_title', 'Page heading', 'Large heading at the top of /notebook.', 'NOTEBOOK'),
        headless_pro_site_content_text_field('notebook_page_subtitle', 'Subtitle', 'Line under the heading on /notebook.', 'Articles and notes on Next.js, WordPress and the web'),
        headless_pro_site_content_text_field('notebook_page_empty_message', 'No articles message', 'Shown on /notebook when no article can be listed.', 'No posts available at the moment. Please check back later.'),
        headless_pro_site_content_text_field('notebook_page_reading_time_format', 'Reading time', 'Reading time on every article card and in every article header. {minutes} is replaced by the number.', '{minutes} min read'),
        headless_pro_site_content_text_field('notebook_page_language_de_label', 'Language badge: German', 'Badge on German articles (cards and article header).', 'DE'),
        headless_pro_site_content_text_field('notebook_page_language_en_label', 'Language badge: English', 'Badge on English articles (cards and article header).', 'EN'),

        // --- Bookshelf page (/bookshelf) ---
        headless_pro_site_content_tab('bookshelf_page', 'Bookshelf page'),
        headless_pro_site_content_text_field('bookshelf_page_title', 'Page heading', 'Large heading at the top of /bookshelf.', 'BOOKSHELF'),
        headless_pro_site_content_text_field('bookshelf_page_lead', 'Lead line', 'First line under the heading on /bookshelf.', "Books and pieces of wisdom I've enjoyed reading"),
        headless_pro_site_content_text_field('bookshelf_page_intro', 'Intro paragraphs', 'Paragraphs after the lead line on /bookshelf. Plain text; leave a blank line between paragraphs.', $headless_pro_bookshelf_intro, 'textarea'),
        headless_pro_site_content_text_field('bookshelf_page_empty_message', 'No books message', 'Shown on /bookshelf when no book can be listed.', 'Bookshelf is currently being updated. Please check back later.'),

        // --- Tech stack page (/tech-stack) ---
        headless_pro_site_content_tab('tech_stack_page', 'Tech stack page'),
        headless_pro_site_content_text_field('tech_stack_page_title', 'Page heading', 'Large heading at the top of /tech-stack.', 'TECH STACK'),
        headless_pro_site_content_text_field('tech_stack_page_intro', 'Intro line', 'Line under the heading on /tech-stack.', 'The dev tools, apps, devices, and games I use and play with'),
        headless_pro_site_content_text_field('tech_stack_page_empty_message', 'No entries message', 'Shown on /tech-stack when no entry can be listed.', 'Tech stack information is currently being updated. Please check back later.'),
        headless_pro_site_content_text_field('tech_stack_page_filter_label', 'Filter: group name (screen readers)', 'Name screen readers announce for the row of category buttons above the logo grid.', 'Filter tools by category'),
        headless_pro_site_content_text_field('tech_stack_page_all_label', 'Filter: All button', 'First category button, shows every tool. The number of tools is added after it.', 'All'),
        headless_pro_site_content_text_field('tech_stack_page_other_label', 'Filter: Other button', 'Button for tools without a Tech Category (Technologies > Tech Categories).', 'Other'),
        headless_pro_site_content_text_field('tech_stack_page_status_format', 'Filter: tools shown (screen readers)', 'Announced after a category is chosen. {count} is replaced by the number of tools.', '{count} tools shown'),
        headless_pro_site_content_text_field('tech_stack_page_count_none', 'Tile: no case study', 'Line under a tool that no case study lists in its Tech Stack.', 'No case study yet'),
        headless_pro_site_content_text_field('tech_stack_page_count_one', 'Tile: one case study', 'Line under a tool that one case study lists.', '1 case study'),
        headless_pro_site_content_text_field('tech_stack_page_count_many', 'Tile: several case studies', 'Line under a tool that several case studies list. {count} is replaced by the number.', '{count} case studies'),

        // --- Article template (/notebook/<slug>) ---
        headless_pro_site_content_tab('article_template', 'Article template'),
        headless_pro_site_content_text_field('article_author_heading', 'Author box heading', 'Heading of the author box under every article.', 'About the Author'),
        headless_pro_site_content_text_field('article_author_bio_fallback', 'Author bio fallback', 'Shown in the author box when the author profile has no biography.', 'Full-stack developer passionate about modern web technologies.'),
        headless_pro_site_content_text_field('article_tags_heading', 'Tags heading', 'Heading above the tag list on every article.', 'Tags'),
        headless_pro_site_content_text_field('article_more_articles_heading', 'More articles heading', 'Heading of the three related articles at the end of every article.', 'More Articles'),
        headless_pro_site_content_text_field('article_toc_heading', 'Contents heading', 'Heading of the contents list built from the article sections (shown when an article has at least two H2 headings).', 'On this page'),

        // --- Global (footer and 404 page, every route) ---
        headless_pro_site_content_tab('global', 'Global'),
        headless_pro_site_content_text_field('footer_imprint_label', 'Footer: Imprint link', 'Footer link to /imprint on every page.', 'Imprint'),
        headless_pro_site_content_text_field('footer_privacy_policy_label', 'Footer: Privacy policy link', 'Footer link to /privacy-policy on every page.', 'Privacy Policy'),
        headless_pro_site_content_text_field('footer_cookie_settings_label', 'Footer: Cookie settings link', 'Footer link that reopens the cookie settings.', 'Cookie Settings'),
        headless_pro_site_content_text_field('not_found_heading', '404: Heading', 'Heading on the page shown for an unknown address.', 'Not Found'),
        headless_pro_site_content_text_field('not_found_text', '404: Text', 'Line under the heading on the 404 page.', 'Could not find the requested resource'),
        headless_pro_site_content_text_field('not_found_home_link_label', '404: Home link', 'Link back to the homepage on the 404 page.', 'Return Home'),

        // --- Cookie banner (every page until the visitor chooses) ---
        headless_pro_site_content_tab('cookie_banner', 'Cookie banner'),
        headless_pro_site_content_text_field('cookie_banner_text', 'Banner text', 'Sentence in the cookie banner, before the privacy policy link.', 'Analytics cookies only with your consent.'),
        headless_pro_site_content_text_field('cookie_banner_privacy_link_label', 'Privacy policy link', 'Link to /privacy-policy in the cookie banner.', 'Privacy policy'),
        headless_pro_site_content_text_field('cookie_banner_reject_label', 'Reject button', 'Button that declines analytics cookies.', 'Reject all'),
        headless_pro_site_content_text_field('cookie_banner_accept_label', 'Accept button', 'Button that accepts analytics cookies.', 'Accept all'),
        headless_pro_site_content_text_field('cookie_banner_settings_label', 'Settings link', 'Link to /cookie-settings in the cookie banner.', 'Settings'),
    ),
    'location' => array(
        array(
            array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'site-content',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'seamless',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'active' => true,
    'description' => 'Template and archive text for the headless frontend',
    'show_in_graphql' => 1,
    'graphql_field_name' => 'siteContent',
    'map_graphql_types_from_location_rules' => 0,
));
