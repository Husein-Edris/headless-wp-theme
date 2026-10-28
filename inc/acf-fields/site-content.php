<?php

/**
 * ACF Options Page: Site Content
 *
 * Text that belongs to a template or an archive rather than to one page:
 * the /projects, /services, /notebook, /bookshelf, /tech-stack and /contact
 * pages, the project cards, the case study, service and article templates, and shared chrome
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
 * Every field also has a German twin <name>_de with no default
 * (edris_with_de_fields(), inc/acf-fields/i18n.php); the endpoint returns both
 * as flat keys.
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
     * @param string $default_de German default for the twin, matching the frontend's German fallback.
     * @return array ACF field definition.
     */
    function headless_pro_site_content_text_field(string $name, string $label, string $where, string $default, string $type = 'text', string $default_de = ''): array
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
        if ($default_de !== '') {
            $field['default_value_de'] = $default_de;
        }

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

acf_add_local_field_group(edris_with_de_fields(array(
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
        headless_pro_site_content_text_field('case_study_service_label', 'Service link label', 'Label before the link to the service page that lists this case study, e.g. Service: Website relaunch.', 'Service', 'text', 'Leistung'),
        headless_pro_site_content_text_field('case_study_lightbox_counter_format', 'Gallery lightbox: Counter format', 'Image counter in the full-screen gallery view. {current} is the open image, {total} the image count.', '{current} / {total}'),

        // --- Services index (/services) and service template (/services/<slug>) ---
        // The closing call to action is per page (inc/acf-fields/service-page.php).
        headless_pro_site_content_tab('services_page', 'Services pages'),
        headless_pro_site_content_text_field('services_page_title', 'Page heading', 'Large heading at the top of /services.', 'SERVICES', 'text', 'LEISTUNGEN'),
        headless_pro_site_content_text_field('services_page_subtitle', 'Subtitle', 'Line under the heading on /services.', 'New websites, relaunches and WordPress maintenance', 'text', 'Neue Websites, Relaunches und WordPress-Wartung'),
        headless_pro_site_content_text_field('service_faq_heading', 'FAQ heading', 'Heading above the questions on every service page.', 'FAQ', 'text', 'Häufige Fragen'),
        headless_pro_site_content_text_field('service_projects_heading', 'Projects heading', 'Heading above the linked case studies on every service page.', 'Case studies', 'text', 'Fallstudien'),
        headless_pro_site_content_text_field('service_posts_heading', 'Articles heading', 'Heading above the linked articles on every service page.', 'Further reading', 'text', 'Weiterlesen'),

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

        // --- Navigation and SEO (menu labels, breadcrumbs, page titles and descriptions) ---
        headless_pro_site_content_tab('navigation_seo', 'Navigation & SEO'),
        headless_pro_site_content_text_field('nav_projects', 'Navigation: Projects', 'Menu label of the link to /projects.', 'Projects'),
        headless_pro_site_content_text_field('nav_about', 'Navigation: About', 'Menu label of the link to /about.', 'About'),
        headless_pro_site_content_text_field('nav_bookshelf', 'Navigation: Bookshelf', 'Menu label of the link to /bookshelf.', 'Bookshelf'),
        headless_pro_site_content_text_field('nav_notebook', 'Navigation: Notebook', 'Menu label of the link to /notebook.', 'Notebook'),
        headless_pro_site_content_text_field('nav_tech_stack', 'Navigation: Tech stack', 'Menu label of the link to /tech-stack.', 'Tech Stack'),
        headless_pro_site_content_text_field('nav_contact', 'Navigation: Contact', 'Menu label of the link to /contact.', 'Contact'),
        headless_pro_site_content_text_field('nav_services', 'Navigation: Services', 'Menu label of the link to /services, also the breadcrumb item on service pages.', 'Services', 'text', 'Leistungen'),
        headless_pro_site_content_text_field('breadcrumb_home', 'Breadcrumb: Home', 'First breadcrumb item on case studies and articles.', 'Home'),
        headless_pro_site_content_text_field('skip_to_content', 'Skip link', 'Keyboard and screen reader link to jump past the menu, on every page.', 'Skip to main content'),
        headless_pro_site_content_text_field('meta_home_title', 'Homepage: SEO title', 'Browser tab and search result title of the homepage.', 'Edris Husein - Web Developer & WordPress, Vorarlberg'),
        headless_pro_site_content_text_field('meta_home_description', 'Homepage: SEO description', 'Search result description of the homepage.', 'Web developer in Dornbirn, Vorarlberg. I build, fix and maintain WordPress sites and web apps for businesses and agencies in Austria, Switzerland and Germany.', 'textarea'),
        headless_pro_site_content_text_field('meta_about_title', 'About: SEO title', 'Browser tab and search result title of /about.', 'About Edris Husein - Web Developer in Vorarlberg, Austria'),
        headless_pro_site_content_text_field('meta_about_description', 'About: SEO description', 'Search result description of /about.', 'About Edris Husein, a web developer from Dornbirn, Vorarlberg, building and maintaining WordPress sites and web apps for businesses and agencies.', 'textarea'),
        headless_pro_site_content_text_field('meta_projects_title', 'Projects: SEO title', 'Browser tab and search result title of /projects.', 'Web Development Projects & Case Studies - Edris Husein'),
        headless_pro_site_content_text_field('meta_projects_description', 'Projects: SEO description', 'Search result description of /projects.', 'Web development projects and case studies by Edris Husein: client websites and web apps built with Next.js, React and WordPress in Austria and Germany.', 'textarea'),
        headless_pro_site_content_text_field('meta_notebook_title', 'Notebook: SEO title', 'Browser tab and search result title of /notebook.', 'Notebook: Web Development Articles - Edris Husein'),
        headless_pro_site_content_text_field('meta_notebook_description', 'Notebook: SEO description', 'Search result description of /notebook.', 'Web development articles and notes by Edris Husein: Next.js, React, WordPress and SEO, written from real client projects in Austria and Germany.', 'textarea'),
        headless_pro_site_content_text_field('meta_bookshelf_title', 'Bookshelf: SEO title', 'Browser tab and search result title of /bookshelf.', 'Bookshelf: Books That Shaped How I Build - Edris Husein'),
        headless_pro_site_content_text_field('meta_bookshelf_description', 'Bookshelf: SEO description', 'Search result description of /bookshelf.', 'The bookshelf of Edris Husein: books on software craft, design and thinking that shaped how he structures systems and writes readable code.', 'textarea'),
        headless_pro_site_content_text_field('meta_tech_stack_title', 'Tech stack: SEO title', 'Browser tab and search result title of /tech-stack.', 'Tech Stack: Next.js, React & WordPress - Edris Husein'),
        headless_pro_site_content_text_field('meta_tech_stack_description', 'Tech stack: SEO description', 'Search result description of /tech-stack.', 'The tech stack Edris Husein builds with: Next.js, React, TypeScript and WordPress, plus the tools, apps and devices behind his web development work.', 'textarea'),
        headless_pro_site_content_text_field('meta_contact_title', 'Contact: SEO title', 'Browser tab and search result title of /contact.', 'Contact Edris Husein - Web Developer in Vorarlberg'),
        headless_pro_site_content_text_field('meta_contact_description', 'Contact: SEO description', 'Search result description of /contact.', 'Contact Edris Husein, a freelance web developer in Vorarlberg, Austria, for WordPress sites, maintenance, fixes and Next.js projects. Replies within a day.', 'textarea'),
        headless_pro_site_content_text_field('meta_services_title', 'Services: SEO title', 'Browser tab and search result title of /services.', 'Web Development Services | Edris Husein', 'text', 'Leistungen: Websites, Relaunch, WordPress | Edris Husein'),
        headless_pro_site_content_text_field('meta_services_description', 'Services: SEO description', 'Search result description of /services.', 'Services by Edris Husein, web developer in Dornbirn: new websites, relaunches, WordPress maintenance, custom WordPress development and headless builds.', 'textarea', 'Leistungen von Edris Husein, Webentwickler in Dornbirn: Websites erstellen, Relaunch, WordPress-Wartung, WordPress-Entwicklung und Headless WordPress.'),
        headless_pro_site_content_text_field('meta_cookie_settings_title', 'Cookie settings: SEO title', 'Browser tab title of /cookie-settings.', 'Cookie Settings - Edris Husein'),
        headless_pro_site_content_text_field('meta_cookie_settings_description', 'Cookie settings: SEO description', 'Search result description of /cookie-settings.', 'Manage your cookie preferences and privacy settings.', 'textarea'),
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
)));
