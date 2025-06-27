<?php

/**
 * Headless Pro Theme - Main Template
 * 
 * This theme is designed for headless WordPress setups.
 * The frontend is handled by your JAMstack application.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header(); ?>

<div class="headless-notice">
    <h1>🚀 Headless WordPress Backend</h1>
    <p>This site is running in headless mode. The frontend is powered by your JAMstack application.</p>
    <p><strong>Admin Panel:</strong> <a href="<?php echo admin_url(); ?>" style="color: #fff; text-decoration: underline;">Access WordPress Admin</a></p>
    <p><strong>REST API:</strong> <code><?php echo rest_url(); ?></code></p>
</div>

<div class="api-status">
    <h2>🔧 API Status</h2>
    <ul>
        <li>✅ WordPress REST API: Active</li>
    </ul>
</div>

<div class="theme-info">
    <h2>📋 Theme Features</h2>
    <ul>
        <li>🔒 Security hardened</li>
        <li>⚡ Performance optimized</li>
        <li>🎯 Custom Post Types included</li>
        <li>🔧 ACF fields pre-configured</li>
        <li>🌐 CORS configured</li>
        <li>🛡️ Headers security</li>
    </ul>
</div>

<?php get_footer(); ?>