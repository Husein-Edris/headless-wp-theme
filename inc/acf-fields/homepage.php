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

acf_add_local_field_group(array(
    'key' => 'group_homepage_sections',
    'title' => 'Homepage Content',
    'fields' => array(

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
                    'type' => 'textarea',
                    'show_in_graphql' => 1,
                    'graphql_field_name' => 'heroCopy',
                ),
            ),
        ),

        // --- Projects Section ---
        array(
            'key' => 'field_projects_section',
            'label' => 'Projects Section',
            'name' => 'projects_section',
            'type' => 'group',
            'instructions' => 'Configure the Projects section settings',
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
            ),
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
            ),
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

        // --- Notebook Section ---
        array(
            'key' => 'field_67b9e47314360',
            'label' => 'Notebook Section',
            'name' => 'notebook_section',
            'type' => 'group',
            'instructions' => 'Configure the Projects section settings',
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
                    'default_value' => 'Projects',
                    'allow_in_bindings' => 1,
                    'show_in_graphql' => 1,
                ),
            ),
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
));
