# Troubleshooting Guide

This guide helps you resolve common issues with the Headless Pro WordPress Theme.

## 🚨 Critical Errors

### Fatal Error: Cannot declare class WPGraphQL

**Error Message:**
```
Fatal error: Cannot declare class WPGraphQL, because the name is already in use
```

**Cause:** Class name conflict between WPGraphQL plugin and theme code.

**Solution:**
1. **Deactivate conflicting plugins temporarily**
2. **Check for duplicate WPGraphQL installations**
3. **Ensure theme is up to date** (v1.0.0+ has fixes)
4. **Clear all caches** (object cache, file cache, etc.)

**Step-by-step fix:**
```bash
# Via WP-CLI
wp plugin deactivate --all
wp plugin activate wp-graphql
wp plugin activate advanced-custom-fields-pro
wp plugin activate wp-graphql-acf
wp cache flush
```

### White Screen of Death (WSOD)

**Cause:** PHP fatal error in theme code.

**Solution:**
1. **Enable WordPress debugging** in `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

2. **Check error logs** in `/wp-content/debug.log`
3. **Switch to default theme temporarily**
4. **Review recent changes**

## 🔧 Plugin Issues

### WPGraphQL Not Working

**Symptoms:**
- `/graphql` endpoint returns 404
- GraphQL queries fail
- GraphiQL IDE not accessible

**Solutions:**

1. **Check plugin activation:**
```bash
wp plugin list | grep graphql
```

2. **Verify permalink structure:**
   - Go to Settings > Permalinks
   - Choose "Post name" structure
   - Click "Save Changes"

3. **Check .htaccess file:**
```apache
# Ensure this is in your .htaccess
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
```

4. **Test GraphQL endpoint:**
```bash
curl -X POST \
  'https://yourdomain.com/graphql' \
  -H 'Content-Type: application/json' \
  -d '{"query":"{ __type(name: \"RootQuery\") { name } }"}'
```

### ACF Fields Not Showing in GraphQL

**Symptoms:**
- ACF fields missing in GraphQL schema
- Field groups not appearing in GraphiQL

**Solutions:**

1. **Install WPGraphQL for ACF:**
```bash
wp plugin install wp-graphql-acf --activate
```

2. **Check field group settings:**
   - Edit field group in ACF
   - Ensure "Show in GraphQL" is enabled
   - Set proper "GraphQL Field Name"

3. **Verify field group location rules:**
   - Check location rules are correctly set
   - Ensure targeting correct post types/pages

4. **Clear GraphQL schema cache:**
```bash
wp graphql clear-schema-cache
```

### Custom Post Types Not Appearing

**Symptoms:**
- Skills, Hobbies, Projects not showing in admin
- CPTs not available in GraphQL/REST API

**Solutions:**

1. **Check theme activation:**
   - Ensure Headless Pro theme is active
   - Verify no PHP errors on activation

2. **Manual CPT registration:**
```php
// Add to functions.php temporarily
function debug_post_types() {
    $post_types = get_post_types(array(), 'objects');
    foreach ($post_types as $post_type) {
        if (in_array($post_type->name, array('skill', 'hobby', 'project'))) {
            error_log('Found CPT: ' . $post_type->name);
        }
    }
}
add_action('init', 'debug_post_types');
```

3. **Flush rewrite rules:**
```bash
wp rewrite flush
```

## 🌐 API Issues

### CORS Errors

**Error Message:**
```
Access to fetch at 'https://cms.yourdomain.com/graphql' from origin 'https://yourdomain.com' has been blocked by CORS policy
```

**Solutions:**

1. **Configure allowed origins:**
```php
// Add to wp-config.php
define('HEADLESS_ALLOWED_ORIGINS', 'http://localhost:3000,https://yourdomain.com,https://www.yourdomain.com');
```

2. **Check server CORS configuration:**
```apache
# .htaccess
Header always set Access-Control-Allow-Origin "https://yourdomain.com"
Header always set Access-Control-Allow-Methods "GET, POST, OPTIONS, PUT, DELETE"
Header always set Access-Control-Allow-Headers "Authorization, Content-Type, X-Requested-With"
```

3. **Nginx configuration:**
```nginx
add_header Access-Control-Allow-Origin "https://yourdomain.com" always;
add_header Access-Control-Allow-Methods "GET, POST, OPTIONS, PUT, DELETE" always;
add_header Access-Control-Allow-Headers "Authorization, Content-Type, X-Requested-With" always;
```

### REST API Returning Errors

**Common errors:**
- 401 Unauthorized
- 403 Forbidden
- 500 Internal Server Error

**Solutions:**

1. **Check authentication:**
```javascript
// For authenticated requests
fetch('/wp-json/wp/v2/posts', {
  headers: {
    'Authorization': 'Bearer ' + token,
    'Content-Type': 'application/json'
  }
})
```

2. **Test basic endpoint:**
```bash
curl -X GET 'https://yourdomain.com/wp-json/wp/v2/posts?per_page=1'
```

3. **Check .htaccess for mod_rewrite:**
```apache
RewriteEngine On
```

## 🔒 Security Issues

### Security Headers Not Working

**Test headers:**
```bash
curl -I https://yourdomain.com
```

**Expected headers:**
```
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
X-XSS-Protection: 1; mode=block
```

**Solutions:**

1. **Check server configuration**
2. **Verify theme is active**
3. **Test with different browser**
4. **Check for conflicting plugins**

### Access Denied Errors

**Symptoms:**
- Cannot access admin areas
- API requests blocked
- Authentication failures

**Solutions:**

1. **Check user permissions:**
```bash
wp user list --fields=user_login,roles
```

2. **Reset user capabilities:**
```bash
wp user set-role admin administrator
```

3. **Check security plugins:**
   - Temporarily deactivate security plugins
   - Review security plugin logs
   - Whitelist your IP address

## ⚡ Performance Issues

### Slow GraphQL Queries

**Solutions:**

1. **Enable query caching:**
```php
// Add to wp-config.php
define('GRAPHQL_QUERY_CACHE_ENABLED', true);
```

2. **Optimize queries:**
   - Avoid deep nesting
   - Use pagination
   - Limit field selection

3. **Use query complexity limits:**
```php
add_filter('graphql_query_max_complexity', function() {
    return 500; // Adjust as needed
});
```

### High Memory Usage

**Solutions:**

1. **Increase PHP memory limit:**
```php
// wp-config.php
ini_set('memory_limit', '512M');
```

2. **Enable object caching:**
```bash
wp plugin install redis-cache --activate
wp redis enable
```

3. **Optimize database:**
```bash
wp db optimize
```

## 🗄️ Database Issues

### Table Not Found Errors

**Error:** `Table 'database.wp_posts' doesn't exist`

**Solutions:**

1. **Check database connection:**
```bash
wp db check
```

2. **Verify table prefix:**
```php
// wp-config.php
$table_prefix = 'wp_'; // Check this matches your database
```

3. **Repair database:**
```bash
wp db repair
```

### Migration Issues

**When moving from another theme:**

1. **Backup database:**
```bash
wp db export backup.sql
```

2. **Clear all caches:**
```bash
wp cache flush
wp rewrite flush
```

3. **Regenerate GraphQL schema:**
```bash
wp graphql clear-schema-cache
```

## 📱 Frontend Integration Issues

### Data Not Loading in Frontend

**Common causes:**
- Incorrect API endpoints
- CORS issues
- Authentication problems
- Field name mismatches

**Debug steps:**

1. **Test API endpoints directly:**
```bash
# REST API
curl https://yourdomain.com/wp-json/wp/v2/posts

# GraphQL
curl -X POST https://yourdomain.com/graphql \
  -H "Content-Type: application/json" \
  -d '{"query": "{ posts { nodes { title } } }"}'
```

2. **Check browser network tab:**
   - Look for failed requests
   - Check response status codes
   - Verify request headers

3. **Validate GraphQL queries:**
   - Use GraphiQL IDE
   - Check for syntax errors
   - Verify field names

### Build Errors in Frontend

**Next.js specific:**

1. **Check environment variables:**
```env
NEXT_PUBLIC_WORDPRESS_API_URL=https://cms.yourdomain.com
```

2. **Verify API responses:**
```javascript
// Test in browser console
fetch(process.env.NEXT_PUBLIC_WORDPRESS_API_URL + '/wp-json/wp/v2/posts')
  .then(res => res.json())
  .then(console.log)
```

## 🛠️ Development Tools

### Enable Debug Mode

```php
// wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', true);
define('GRAPHQL_DEBUG', true);
```

### Useful WP-CLI Commands

```bash
# Theme status
wp theme status headless-pro

# Plugin status
wp plugin status

# Clear caches
wp cache flush
wp rewrite flush

# Database operations
wp db check
wp db optimize

# GraphQL specific
wp graphql clear-schema-cache
```

### Debug Queries

```php
// Add to functions.php for debugging
add_action('pre_get_posts', function($query) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Query: ' . print_r($query->query, true));
    }
});
```

## 📞 Getting Help

### Before Asking for Help

1. **Check this troubleshooting guide**
2. **Enable debug mode and check logs**
3. **Test with default WordPress theme**
4. **Deactivate all plugins except required ones**
5. **Clear all caches**

### Information to Include

When reporting issues:

- WordPress version
- PHP version
- Theme version
- Active plugins list
- Error messages (full text)
- Steps to reproduce
- Browser/environment details

### Support Channels

- **GitHub Issues**: [Report bugs and feature requests](https://github.com/your-repo/headless-pro-theme/issues)
- **WordPress Forums**: [Community support](https://wordpress.org/support/)
- **Documentation**: [Full documentation](README.md)

---

## 🔄 Emergency Recovery

### If Site is Completely Broken

1. **Switch to default theme:**
```bash
wp theme activate twentytwentythree
```

2. **Deactivate all plugins:**
```bash
wp plugin deactivate --all
```

3. **Restore from backup:**
```bash
wp db import backup.sql
```

4. **Check file permissions:**
```bash
find /path/to/wordpress/ -type d -exec chmod 755 {} \;
find /path/to/wordpress/ -type f -exec chmod 644 {} \;
```

Remember: Always backup before making changes!