<?php

/**
 * ACF Fields: Homepage Content
 *
 * GraphQL field name: homepageSections
 * Location: Front page
 *
 * 7 sections: Hero, Projects, About Me, Bookshelf, Tech Stack, Notebook, Contact
 *
 * @package HeadlessPro
 */

if (!defined('ABSPATH')) {
    exit;
}

acf_add_local_field_group(edris_with_de_fields(array(
    'key' => 'group_homepage_sections',
    'title' => 'Homepage Content',
    'fields' => array(

        // --- Hero Tab ---
        array(
            'key' => 'field_tab_hero',
            'label' => 'Hero',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        ),

        // --- Hero Section ---
        array(
            'key' => 'field_67b9e23fddea0',
            'label' => 'Hero',
            'name' => 'hero_section',
            'type' => 'group',
            'required' => 1,
            'layout' => 'block',
            'show_in_graphql' => 1,
            'graphql_field_name' => 'heroSection',
            'sub_fields' => array(
                array(
                    'key' => 'field_67b9e3547c576',
                    'label' => 'Hero Image',
                    'name' => 'hero_image',
                    'type' => 'image',
                    'instructions' => 'Portrait photo. Not shown on the homepage; the About page uses it when its own About Hero Image is empty.',
                    'return_format' => 'array',
                    'library' => 'all',
                    'preview_size' => 'medium',
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'heroImage',
                ),
                array(
                    'key' => 'field_67b9e25bddea4',
                    'label' => 'Hero Copy',
                    'name' => 'hero_copy',
                    'type' => 'wysiwyg',
                    'instructions' => 'Paragraph under the heading. Links, bold and italic are kept; other formatting is removed. While this text has no links, the names illwerke vkw, Vorarlberg state hospitals and FHE Gastro link to their live sites automatically.',
                    'tabs' => 'all',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'heroCopy',
                ),
                array(
                    'key' => 'field_hero_eyebrow',
                    'label' => 'Eyebrow',
                    'name' => 'hero_eyebrow',
                    'type' => 'text',
                    'instructions' => 'Small line above the main heading. Leave empty to show: Edris Husein, Dornbirn, Austria',
                    'default_value' => 'Edris Husein, Dornbirn, Austria',
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'heroEyebrow',
                ),
                array(
                    'key' => 'field_hero_title',
                    'label' => 'Heading (H1)',
                    'name' => 'hero_title',
                    'type' => 'text',
                    'instructions' => 'Main page heading (the only H1 on the homepage, keep it keyword-led). Leave empty to show: Servus. I build websites, fix them and help you plan what comes next.',
                    'default_value' => 'Servus. I build websites, fix them and help you plan what comes next.',
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'heroTitle',
                ),
                array(
                    'key' => 'field_hero_primary_cta_label',
                    'label' => 'Primary button label',
                    'name' => 'hero_primary_cta_label',
                    'type' => 'text',
                    'instructions' => 'Text of the filled button. Leave empty to show: Email me',
                    'default_value' => 'Email me',
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'heroPrimaryCtaLabel',
                ),
                array(
                    'key' => 'field_hero_primary_cta_url',
                    'label' => 'Primary button link',
                    'name' => 'hero_primary_cta_url',
                    'type' => 'text',
                    'instructions' => 'Where the filled button goes: mailto:address, a site path like /contact, or a full https:// URL. Leave empty for mailto:kontakt@edrishusein.com',
                    'default_value' => 'mailto:kontakt@edrishusein.com',
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'heroPrimaryCtaUrl',
                ),
                array(
                    'key' => 'field_hero_secondary_cta_label',
                    'label' => 'Secondary button label',
                    'name' => 'hero_secondary_cta_label',
                    'type' => 'text',
                    'instructions' => 'Text of the outlined button below the project frames. Leave empty to show: See all projects',
                    'default_value' => 'See all projects',
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'heroSecondaryCtaLabel',
                ),
                array(
                    'key' => 'field_hero_secondary_cta_url',
                    'label' => 'Secondary button link',
                    'name' => 'hero_secondary_cta_url',
                    'type' => 'text',
                    'instructions' => 'Where the outlined button goes: a site path like /projects or a full https:// URL. Leave empty for /projects',
                    'default_value' => '/projects',
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'heroSecondaryCtaUrl',
                ),
            ),
        ),

        // --- Projects Tab ---
        array(
            'key' => 'field_tab_projects',
            'label' => 'Projects',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        ),

        // --- Projects Section ---
        array(
            'key' => 'field_projects_section',
            'label' => 'Projects Section',
            'name' => 'projects_section',
            'type' => 'group',
            'instructions' => 'Projects section on the homepage. Labels for /projects, the project cards and the case study pages live under Site Content in the admin menu.',
            'required' => 1,
            'layout' => 'block',
            'show_in_graphql' => 1,
            'graphql_field_name' => 'projectsSection',
            'sub_fields' => array(
                array(
                    'key' => 'field_projects_title',
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => 'text',
                    'instructions' => 'Enter the section title',
                    'default_value' => 'Projects',
                ),
                array(
                    'key' => 'field_projects_view_all_label',
                    'label' => 'View all link label',
                    'name' => 'view_all_label',
                    'type' => 'text',
                    'instructions' => 'Link next to the section title. Leave empty to show: VIEW ALL PROJECTS',
                    'default_value' => 'VIEW ALL PROJECTS',
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'viewAllLabel',
                ),
                array(
                    'key' => 'field_projects_view_all_url',
                    'label' => 'View all link target',
                    'name' => 'view_all_url',
                    'type' => 'text',
                    'instructions' => 'A site path like /projects or a full https:// URL. Leave empty for /projects',
                    'default_value' => '/projects',
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'viewAllUrl',
                ),
            ),
        ),

        // --- About Me Tab ---
        array(
            'key' => 'field_tab_about_me',
            'label' => 'About Me',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        ),

        // --- About Me Section ---
        array(
            'key' => 'field_67b9e3eb14358',
            'label' => 'About me Section',
            'name' => 'about_me_section',
            'type' => 'group',
            'required' => 1,
            'layout' => 'block',
            'show_in_graphql' => 1,
            'graphql_field_name' => 'aboutSection',
            'sub_fields' => array(
                array(
                    'key' => 'field_67b9e43c1435c',
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => 'text',
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'title',
                ),
                array(
                    'key' => 'field_67b9e3eb1435b',
                    'label' => 'About me Text',
                    'name' => 'about_me_text',
                    'type' => 'wysiwyg',
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                    'delay' => 0,
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'aboutMeText',
                ),
                array(
                    'key' => 'field_about_me_learn_more_label',
                    'label' => 'Button label',
                    'name' => 'learn_more_label',
                    'type' => 'text',
                    'instructions' => 'Button under the About text on the homepage. Leave empty to show: Learn more about me',
                    'default_value' => 'Learn more about me',
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'learnMoreLabel',
                ),
                array(
                    'key' => 'field_about_me_learn_more_url',
                    'label' => 'Button link target',
                    'name' => 'learn_more_url',
                    'type' => 'text',
                    'instructions' => 'A site path like /about or a full https:// URL. Leave empty for /about',
                    'default_value' => '/about',
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'learnMoreUrl',
                ),
            ),
        ),

        // --- Bookshelf Tab ---
        array(
            'key' => 'field_tab_bookshelf',
            'label' => 'Bookshelf',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        ),

        // --- Bookshelf Section ---
        array(
            'key' => 'field_bookshelf_section',
            'label' => 'Bookshelf Section',
            'name' => 'bookshelf_section',
            'type' => 'group',
            'instructions' => 'Configure the Bookshelf section settings',
            'required' => 1,
            'layout' => 'block',
            'show_in_graphql' => 1,
            'graphql_field_name' => 'bookshelfSection',
            'sub_fields' => array(
                array(
                    'key' => 'field_67b9e36d7c577',
                    'label' => 'Featured Image',
                    'name' => 'featured_image',
                    'type' => 'image',
                    'return_format' => 'array',
                    'library' => 'all',
                    'preview_size' => 'medium',
                    'allow_in_bindings' => 1,
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'featuredImage',
                ),
                array(
                    'key' => 'field_bookshelf_title',
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => 'text',
                    'instructions' => 'Enter the section title',
                    'default_value' => 'BOOKSHELF',
                ),
                array(
                    'key' => 'field_bookshelf_description',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                    'instructions' => 'Enter the section description',
                    'default_value' => 'Books and pieces of wisdom I\'ve enjoyed reading',
                ),
            ),
        ),

        // --- Tech Stack Tab ---
        array(
            'key' => 'field_tab_techstack',
            'label' => 'Tech Stack',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        ),

        // --- Tech Stack Section ---
        array(
            'key' => 'field_techstack_section',
            'label' => 'Tech Stack Section',
            'name' => 'techstack_section',
            'type' => 'group',
            'instructions' => 'Configure the Tech Stack section settings',
            'required' => 1,
            'layout' => 'block',
            'show_in_graphql' => 1,
            'graphql_field_name' => 'techstackSection',
            'sub_fields' => array(
                array(
                    'key' => 'field_67b9e38d7c578',
                    'label' => 'Featured Image',
                    'name' => 'featured_image',
                    'type' => 'image',
                    'return_format' => 'array',
                    'library' => 'all',
                    'preview_size' => 'medium',
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'featuredImage',
                ),
                array(
                    'key' => 'field_techstack_title',
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => 'text',
                    'instructions' => 'Enter the section title',
                    'default_value' => 'TECH STACK',
                ),
                array(
                    'key' => 'field_techstack_description',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                    'instructions' => 'Enter the section description',
                    'default_value' => 'The dev tools, apps, devices, and games I use and play with',
                ),
            ),
        ),

        // --- Notebook Tab ---
        array(
            'key' => 'field_tab_notebook',
            'label' => 'Notebook',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        ),

        // --- Notebook Section ---
        array(
            'key' => 'field_67b9e47314360',
            'label' => 'Notebook Section',
            'name' => 'notebook_section',
            'type' => 'group',
            'instructions' => 'Notebook section on the homepage. Text on /notebook and the article pages lives under Site Content in the admin menu.',
            'required' => 1,
            'layout' => 'block',
            'show_in_graphql' => 1,
            'graphql_field_name' => 'notebookSection',
            'sub_fields' => array(
                array(
                    'key' => 'field_67b9e47314361',
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => 'text',
                    'instructions' => 'Enter the section title',
                    'default_value' => 'NOTEBOOK',
                    'allow_in_bindings' => 1,
                    'show_in_graphql' => 1,
                ),
                array(
                    'key' => 'field_notebook_view_all_label',
                    'label' => 'View all link label',
                    'name' => 'view_all_label',
                    'type' => 'text',
                    'instructions' => 'Link next to the section title. Leave empty to show: VIEW ALL ARTICLES',
                    'default_value' => 'VIEW ALL ARTICLES',
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'viewAllLabel',
                ),
                array(
                    'key' => 'field_notebook_view_all_url',
                    'label' => 'View all link target',
                    'name' => 'view_all_url',
                    'type' => 'text',
                    'instructions' => 'A site path like /notebook or a full https:// URL. Leave empty for /notebook',
                    'default_value' => '/notebook',
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'viewAllUrl',
                ),
            ),
        ),

        // --- Contact Tab ---
        array(
            'key' => 'field_tab_contact',
            'label' => 'Contact',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        ),

        // --- Contact Section ---
        array(
            'key' => 'field_67b9e4691435d',
            'label' => 'Contact Section',
            'name' => 'contact_section',
            'type' => 'group',
            'required' => 1,
            'layout' => 'block',
            'show_in_graphql' => 1,
            'graphql_field_name' => 'contactSection',
            'sub_fields' => array(
                array(
                    'key' => 'field_67b9e4fc14365',
                    'label' => 'Sub Title',
                    'name' => 'sub_title',
                    'type' => 'text',
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'subTitle',
                ),
                array(
                    'key' => 'field_67b9e4691435e',
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => 'text',
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'title',
                ),
                array(
                    'key' => 'field_67b9e50e14366',
                    'label' => 'Email',
                    'name' => 'email',
                    'type' => 'email',
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'email',
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
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'active' => true,
    'description' => 'Configure settings for Homepage sections',
    'show_in_graphql' => 1,
    'graphql_field_name' => 'homepageSections',
    'map_graphql_types_from_location_rules' => 1,
    'graphql_types' => array(
        'ContentNode', 'Book', 'GraphqlDocument', 'MediaItem', 'Page',
        'Post', 'Project', 'Tech', 'TermNode', 'Category',
        'GraphqlDocumentGroup', 'PostFormat', 'Tag', 'ContentTemplate',
        'DefaultTemplate', 'Comment', 'Menu', 'MenuItem', 'User',
    ),
)));
