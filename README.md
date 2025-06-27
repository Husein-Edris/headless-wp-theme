# Headless Pro WordPress Theme

A modern, secure, and performance-optimized WordPress theme specifically designed for headless/JAMstack applications.

## 🚀 Features

### Core Features
- ⚡ **Performance Optimized** - Minimal frontend, maximum backend efficiency
- 🔒 **Security Hardened** - Built-in security headers and protections
- 🎯 **Headless Ready** - Optimized for GraphQL and REST API consumption
- 📱 **Modern PHP** - Uses PHP 8+ features and best practices
- 🛡️ **CORS Configured** - Proper cross-origin resource sharing setup

### Custom Post Types (Built-in)
- 💡 **Skills** - Technical skills and proficiencies
- 🎨 **Hobbies** - Personal interests and activities  
- 🚀 **Projects** - Portfolio projects with case studies
- 🔧 **Technologies** - Tech stack items with details

### Advanced Custom Fields (Pre-configured)
- 🏠 **Homepage Sections** - Hero, About, Contact
- 👤 **About Page** - Experience, Skills, Personal info
- 📝 **Blog Posts** - Reading time, conclusions, custom tags
- 💼 **Projects** - Case studies, technologies, links

### API Enhancements
- 📊 **GraphQL Extensions** - Custom fields, queries, and types
- 🔍 **Advanced Search** - Multi-type search with filters
- 📈 **Analytics Ready** - Post views tracking, popular posts
- 🔗 **Custom Endpoints** - Site info, menus, contact forms

### Admin Features
- 🎛️ **Custom Dashboard** - Headless-specific admin interface
- 📋 **API Status** - Monitor GraphQL and REST API health
- 🔧 **Content Management** - Streamlined content creation tools
- 📊 **Analytics Dashboard** - Track content performance

## 📋 Requirements

### WordPress
- WordPress 6.0+
- PHP 8.0+
- MySQL 5.7+ or MariaDB 10.3+

### Required Plugins
- **WPGraphQL** (for GraphQL API)
- **Advanced Custom Fields PRO** (for custom fields)
- **WPGraphQL for Advanced Custom Fields** (for GraphQL ACF integration)

### Recommended Plugins
- **Yoast SEO** (for enhanced SEO features)
- **WP Rocket** (for caching)
- **Wordfence Security** (for additional security)

## 🛠️ Installation

### Method 1: Direct Upload
1. Download the theme files
2. Upload to `/wp-content/themes/headless-pro/`
3. Activate in WordPress admin
4. Install required plugins
5. Configure settings

### Method 2: Zip Upload
1. Zip the theme folder
2. Go to **Appearance > Themes > Add New > Upload Theme**
3. Upload the zip file
4. Activate the theme
5. Install required plugins

## ⚙️ Configuration

### 1. Install Required Plugins
```bash
# Via WP-CLI (recommended)
wp plugin install advanced-custom-fields-pro --activate
wp plugin install wp-graphql --activate
wp plugin install wp-graphql-acf --activate
```

### 2. Configure Frontend URL
1. Go to **Appearance > Customize**
2. Navigate to **Headless Settings**
3. Set your frontend application URL
4. Save changes

### 3. Set Up Permalinks
1. Go to **Settings > Permalinks**
2. Choose "Post name" structure
3. Save changes

### 4. Configure CORS (if needed)
Add to `wp-config.php`:
```php
define('HEADLESS_ALLOWED_ORIGINS', 'http://localhost:3000,https://yourdomain.com');
```

## 🎯 Usage

### GraphQL Endpoint
```
https://yourdomain.com/graphql
```

### REST API Endpoints
```
# Standard WordPress REST API
https://yourdomain.com/wp-json/wp/v2/

# Custom Headless Pro endpoints
https://yourdomain.com/wp-json/headless/v1/
```

### Custom Endpoints
- **Site Info**: `/wp-json/headless/v1/site-info`
- **Menus**: `/wp-json/headless/v1/menus`
- **Search**: `/wp-json/headless/v1/search?query=term`
- **Popular Posts**: `/wp-json/headless/v1/posts/popular`
- **Related Posts**: `/wp-json/headless/v1/posts/{id}/related`

### Example GraphQL Queries

#### Get Homepage Data
```graphql
query GetHomepage {
  page(id: "home", idType: URI) {
    homepageSections {
      heroSection {
        title
        heroCopy
        heroImage {
          node {
            sourceUrl
            altText
          }
        }
      }
      aboutSection {
        title
        aboutMeText
      }
      contactSection {
        subTitle
        title
        email
      }
    }
  }
}
```

#### Get About Page with Skills and Hobbies
```graphql
query GetAboutPage {
  page(id: "about", idType: URI) {
    title
    content
    aboutPageFields {
      aboutHeroTitle
      aboutHeroSubtitle
      selectedSkills {
        ... on Skill {
          id
          title
          skillFields {
            shortDescription
            proficiencyLevel
            category
          }
        }
      }
      personalSection {
        selectedHobbies {
          ... on Hobby {
            id
            title
            hobbyFields {
              description
              category
            }
          }
        }
      }
    }
  }
}
```

#### Get Projects with Technologies
```graphql
query GetProjects {
  projects {
    nodes {
      id
      title
      excerpt
      featuredImage {
        node {
          sourceUrl
          altText
        }
      }
      caseStudy {
        projectTitle
        projectDescription
        technologies {
          nodes {
            ... on Tech {
              id
              title
              featuredImage {
                node {
                  sourceUrl
                }
              }
            }
          }
        }
        projectLinks {
          liveSite
          github
        }
      }
    }
  }
}
```

## 🔧 Customization

### Adding Custom Fields
```php
// In your child theme's functions.php
function custom_acf_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }
    
    acf_add_local_field_group(array(
        'key' => 'group_custom_fields',
        'title' => 'Custom Fields',
        'fields' => array(
            // Your fields here
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'your_post_type',
                ),
            ),
        ),
        'show_in_graphql' => 1,
        'graphql_field_name' => 'customFields',
    ));
}
add_action('acf/init', 'custom_acf_fields');
```

### Adding Custom Post Types
```php
// In your child theme's functions.php
function register_custom_post_type() {
    register_post_type('your_type', array(
        'public' => true,
        'show_in_rest' => true,
        'show_in_graphql' => true,
        'graphql_single_name' => 'yourType',
        'graphql_plural_name' => 'yourTypes',
        'supports' => array('title', 'editor', 'thumbnail'),
        // Other args...
    ));
}
add_action('init', 'register_custom_post_type');
```

### Custom GraphQL Fields
```php
// Add custom field to GraphQL
add_action('graphql_register_types', function() {
    register_graphql_field('Post', 'customField', array(
        'type' => 'String',
        'description' => 'Custom field description',
        'resolve' => function($post) {
            return get_post_meta($post->ID, 'custom_field', true);
        }
    ));
});
```

## 🔒 Security Features

### Built-in Security Headers
- X-Content-Type-Options: nosniff
- X-Frame-Options: SAMEORIGIN  
- X-XSS-Protection: 1; mode=block
- Referrer-Policy: strict-origin-when-cross-origin

### Disabled Features
- WordPress version disclosure
- XML-RPC (disabled for security)
- Unnecessary WordPress features for headless use
- File editing from admin

### CORS Configuration
Properly configured CORS headers for your frontend domains.

## 📊 Performance Features

### Optimizations
- Disabled WordPress emojis
- Removed unnecessary scripts and styles
- Optimized database queries
- GraphQL query caching
- Minimized admin interface

### Recommended Setup
- Use a CDN (Cloudflare, AWS CloudFront)
- Enable object caching (Redis, Memcached)
- Use a caching plugin (WP Rocket, W3 Total Cache)
- Optimize images (WebP, lazy loading)

## 🐛 Troubleshooting

### Common Issues

#### GraphQL Not Working
1. Ensure WPGraphQL plugin is installed and activated
2. Check permalink structure is set to "Post name"
3. Verify your GraphQL endpoint: `/graphql`

#### ACF Fields Not Showing in GraphQL
1. Install WPGraphQL for ACF plugin
2. Ensure "Show in GraphQL" is enabled for field groups
3. Check GraphQL field names are valid

#### CORS Errors
1. Configure allowed origins in theme settings
2. Add domains to `HEADLESS_ALLOWED_ORIGINS` constant
3. Check server CORS configuration

#### Custom Post Types Not Appearing
1. Verify post types are registered correctly
2. Check `show_in_graphql` and `show_in_rest` are true
3. Flush permalinks in Settings > Permalinks

### Debug Mode
Add to `wp-config.php` for debugging:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('GRAPHQL_DEBUG', true);
```

## 🔄 Updates

### Updating the Theme
1. Backup your site
2. Download the latest version
3. Replace theme files (keep customizations in child theme)
4. Test functionality
5. Clear caches

### Changelog
See `CHANGELOG.md` for version history and updates.

## 📚 Resources

### Documentation
- [WPGraphQL Documentation](https://www.wpgraphql.com/)
- [ACF Documentation](https://www.advancedcustomfields.com/resources/)
- [WordPress REST API](https://developer.wordpress.org/rest-api/)

### Community
- [GitHub Issues](https://github.com/your-repo/headless-pro-theme/issues)
- [WordPress Slack #headless](https://wordpress.slack.com/channels/headless)
- [JAMstack Community](https://jamstack.org/)

## 📝 License

This theme is licensed under the GPL v2 or later.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## 👨‍💻 Author

**Edris Husein**
- Website: [edrishusein.com](https://edrishusein.com)
- GitHub: [@edrishusein](https://github.com/edrishusein)

## 🙏 Acknowledgments

- WordPress Core Team
- WPGraphQL Team
- Advanced Custom Fields Team
- JAMstack Community

---

**Built with ❤️ for the headless WordPress community**