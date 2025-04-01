<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/*
Plugin Name: Login Works
Plugin URI: https://wordpressoptimize.com
Description: Customize. Secure. Simplify.
Version: 1.0
Author: Alex Seif
Author URI: https://alexseif.com
License: GPL2
Text Domain: login-works
*/


// Enqueue custom styles for the login page
function login_works_enqueue_styles()
{
    wp_enqueue_style(
        'login-works-custom-login', // Handle
        plugin_dir_url(__FILE__) . 'css/custom-login.css', // File URL
        [], // Dependencies
        '1.0.0' // Version
    );
}
add_action('login_enqueue_scripts', 'login_works_enqueue_styles');

// Replace the WordPress logo with the uploaded logo
function login_works_replace_logo()
{
    $logo_url = get_option('login_works_logo', '');
    if (!empty($logo_url)) {
?>
        <style type="text/css">
            .login h1 a {
                background-image: url('<?php echo esc_url($logo_url); ?>');
                background-size: contain;
                width: 100%;
                height: 80px;
                display: block;
                text-indent: -9999px;
                overflow: hidden;
            }
        </style>
    <?php
    }
}
add_action('login_head', 'login_works_replace_logo');

// Change the logo link URL and title
function login_works_custom_logo_url()
{
    return home_url(); // Redirects the logo link to the site's home page
}
add_filter('login_headerurl', 'login_works_custom_logo_url');

function login_works_custom_logo_title()
{
    return get_bloginfo('name'); // Sets the logo title to the site's name
}
add_filter('login_headertext', 'login_works_custom_logo_title');

// Add a settings page for the plugin
function login_works_add_settings_page()
{
    add_options_page(
        'Login Works Settings', // Page title
        'Login Works',          // Menu title
        'manage_options',       // Capability
        'login-works',          // Menu slug
        'login_works_render_settings_page' // Callback function
    );
}
add_action('admin_menu', 'login_works_add_settings_page');

// Render the settings page
function login_works_render_settings_page()
{
    ?>
    <div class="wrap">
        <h1>Login Works Settings</h1>
        <form method="post" action="options.php">
            <?php
            // Output security fields for the registered setting
            settings_fields('login_works_settings');
            // Output setting sections and their fields
            do_settings_sections('login-works');
            // Output save settings button
            submit_button();
            ?>
        </form>
    </div>
<?php
}

// Register settings and fields
function login_works_register_settings()
{
    // Register the setting to store the logo URL
    register_setting(
        'login_works_settings', // Option group
        'login_works_logo',     // Option name
        'login_works_sanitize_logo_url' // Explicit sanitization callback
    );

    // Add a section to the settings page
    add_settings_section(
        'login_works_main_section', // Section ID
        'Main Settings',            // Section title
        null,                       // Callback (optional)
        'login-works'               // Page slug
    );

    // Add a field to upload the logo
    add_settings_field(
        'login_works_logo',         // Field ID
        'Upload Logo',              // Field title
        'login_works_logo_field_callback', // Callback function
        'login-works',              // Page slug
        'login_works_main_section'  // Section ID
    );
}
add_action('admin_init', 'login_works_register_settings');

// Sanitization callback for the logo URL
function login_works_sanitize_logo_url($input)
{
    // Ensure the input is a valid URL
    return esc_url_raw($input);
}

// Callback function for the logo upload field
function login_works_logo_field_callback()
{
    $logo_url = get_option('login_works_logo', '');
    $attachment_id = attachment_url_to_postid($logo_url); // Get the attachment ID from the URL
?>
    <input type="hidden" id="login_works_logo" name="login_works_logo" value="<?php echo esc_attr($logo_url); ?>" />
    <button type="button" class="button" id="upload_logo_button">Upload Logo</button>
    <button type="button" class="button" id="remove_logo_button" style="<?php echo empty($logo_url) ? 'display: none;' : ''; ?>">Remove Logo</button>
    <p class="description">Upload or select a logo from the Media Library.</p>
    <div id="logo_preview" style="margin-top: 10px;">
        <?php if (!empty($attachment_id)) : ?>
            <?php echo wp_get_attachment_image($attachment_id, 'medium', false, ['alt' => 'Logo Preview', 'style' => 'max-width: 200px; height: auto;']); ?>
        <?php endif; ?>
    </div>
<?php
}

// Enqueue scripts for the Media Library uploader
function login_works_enqueue_admin_scripts($hook)
{
    if ($hook !== 'settings_page_login-works') {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_script(
        'login-works-admin',
        plugin_dir_url(__FILE__) . 'js/admin.js',
        ['jquery'],
        '1.0',
        true
    );
}
add_action('admin_enqueue_scripts', 'login_works_enqueue_admin_scripts');
