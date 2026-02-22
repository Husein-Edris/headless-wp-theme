# Headless Pro WordPress Theme

A WordPress theme designed for headless/JAMstack use. All frontend visitors are redirected to the Next.js frontend at [edrishusein.com](https://edrishusein.com). WordPress serves only as a content API via REST (`/wp-json/`) and GraphQL (`/graphql`).

## Requirements

- WordPress 6.0+
- PHP 8.0+
- MySQL 5.7+ or MariaDB 10.3+

### Required Plugins

- [WPGraphQL](https://www.wpgraphql.com/) — GraphQL API at `/graphql`
- [Advanced Custom Fields PRO](https://www.advancedcustomfields.com/) — Custom field management
- [WPGraphQL for ACF](https://acf.wpgraphql.com/) — Exposes ACF fields in GraphQL

## Installation

1. Upload to `/wp-content/themes/headless-wp-theme/`
2. Activate in WordPress admin
3. Install and activate the required plugins listed above
4. Set permalinks to "Post name" under **Settings > Permalinks**

## Configuration

### Frontend URL and CORS

Add to `wp-config.php`:

```php
define('HEADLESS_FRONTEND_URL', 'https://edrishusein.com');
define('HEADLESS_ALLOWED_ORIGINS', 'http://localhost:3000,https://edrishusein.com');
```

Both constants have sensible defaults and are optional.

### Debug Mode

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('GRAPHQL_DEBUG', true);
```

## Architecture

### Module Structure

`functions.php` defines four inline classes (`HeadlessProConfig`, `HeadlessProSecurity`, `HeadlessProPerformance`, `HeadlessProCORS`) and loads modules from `inc/`:

| Module | Purpose |
|--------|---------|
| `inc/post-types.php` | Registers 5 custom post types + `tech_category` taxonomy |
| `inc/acf-fields.php` | ACF field group loader + environment-aware settings |
| `inc/api.php` | REST endpoints + post view tracking |
| `inc/admin.php` | Admin UI customization |
| `inc/frontend-redirect.php` | 301 redirect for frontend visitors |

### Custom Post Types

All have `show_in_rest` and `show_in_graphql` enabled:

| Type | Slug | GraphQL Name |
|------|------|-------------|
| Skills | `skill` | `skill` / `skills` |
| Hobbies | `hobby` | `hobby` / `hobbies` |
| Projects | `project` | `project` / `projects` |
| Technologies | `tech` | `tech` / `techs` |
| Bookshelf | `book` | `book` / `books` |

### ACF Field Groups

Defined in PHP under `inc/acf-fields/`. PHP code is the source of truth.

| File | GraphQL Name | Description |
|------|-------------|-------------|
| `skills.php` | `skillFields` | short_description |
| `hobbies.php` | `hobbyFields` | description |
| `homepage.php` | `homepageSections` | 7 sections: hero, projects, about, bookshelf, techstack, notebook, contact |
| `about-page.php` | `aboutPageFields` | Hero, experience items, skills, personal content, hobbies |
| `project-case-study.php` | `caseStudy` | Overview with tech stack, content, links, gallery |
| `blog-post.php` | `blogPostFields` | Reading time, conclusion, custom tags, author bio |

ACF admin UI is automatically hidden in production and staging environments.

## API Reference

### REST Endpoints

Standard WordPress REST API at `/wp-json/wp/v2/` with ACF fields included in responses.

Custom endpoints at `/wp-json/headless/v1/`:

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/site-info` | GET | Site metadata |
| `/menus` | GET | Navigation menus with hierarchy |
| `/search` | GET | Multi-type search (`query`, `post_type`, `limit`) |
| `/posts/{id}/related` | GET | Related posts by shared categories/tags |
| `/posts/popular` | GET | Posts ordered by view count |
| `/contact` | POST | Contact form submission (nonce-verified) |
| `/nonce` | GET | Returns a `contact_nonce` |

### GraphQL Queries

```graphql
# Homepage sections
{
  page(id: 29, idType: DATABASE_ID) {
    homepageSections {
      heroSection { heroCopy heroImage { node { sourceUrl } } }
      aboutSection { title aboutMeText }
      contactSection { subTitle title email }
    }
  }
}

# Projects with tech stack
{
  projects {
    nodes {
      title
      caseStudy {
        projectOverview {
          technologies { nodes { ... on Tech { title } } }
        }
        projectContent { challenge solution }
        projectLinks { liveSite github }
      }
    }
  }
}

# Blog posts
{
  posts {
    nodes {
      title
      blogPostFields {
        readingTime
        conclusionSection { conclusionTitle conclusionPoints { pointText } }
        customTags { tagName tagColor }
        authorBioOverride
      }
    }
  }
}
```

## Security

- Security headers: `X-Content-Type-Options`, `X-Frame-Options`, `X-XSS-Protection`, `Referrer-Policy`
- XML-RPC disabled
- WordPress version disclosure removed
- File editing from admin disabled
- CORS configured via `HeadlessProCORS` with the `headless_pro_allowed_origins` filter

## Development

### Adding a New ACF Field Group

1. Create a file in `inc/acf-fields/` that calls `acf_add_local_field_group()`
2. Add the filename to the `$field_files` array in `HeadlessProACFFields::register_field_groups()`
3. Set `show_in_graphql => 1` and `graphql_field_name` in the group definition

### Running Tests

```bash
/Applications/Local.app/Contents/Resources/extraResources/lightning-services/php-8.2.27+1/bin/darwin/bin/php vendor/phpunit/phpunit/phpunit
```

Requires Local by Flywheel running with MySQL accessible.

## License

GPL v2 or later.

## Author

**Edris Husein** — [edrishusein.com](https://edrishusein.com)
