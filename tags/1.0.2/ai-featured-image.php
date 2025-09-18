<?php
/**
 * Plugin Name: AI Featured Image Generator
 * Description: Generate AI-powered featured images for your WordPress posts using AI image APIs.
 * Version: 1.0.2
 * Author: Amr AI
 * Author URI: https://www.linkedin.com/in/amr-issa/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ai-featured-image-generator
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 *
 * @package AIFI
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Plugin version
define('AIFI_VERSION', '1.0.2');
define('AIFI_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AIFI_PLUGIN_URL', plugin_dir_url(__FILE__));

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'AIFI\\';
    $base_dir = AIFI_PLUGIN_DIR . 'inc/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . 'class-' . str_replace('\\', '/', strtolower(str_replace('_', '-', $relative_class))) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Initialize the plugin
function aifi_init() {
    $plugin = new AIFI\Plugin();
    $plugin->init();
}
add_action('plugins_loaded', 'aifi_init');

// Activation hook
register_activation_hook(__FILE__, function() {
    // Set default options
    $defaults = array(
        'api_key' => '',
        'default_size' => '1536x1024',
        'default_style' => 'realistic',
        'max_attempts' => 3,
        'auto_generate' => false
    );

    if (is_multisite()) {
        add_network_option(null, 'aifi_settings', $defaults);
    } else {
        add_option('aifi_settings', $defaults);
    }
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    // Clean up transients
    $all_options = wp_load_alloptions();
    foreach ($all_options as $option_name => $value) {
        if (strpos($option_name, '_transient_aifi_lock_') === 0) {
            $transient = str_replace('_transient_', '', $option_name);
            delete_transient($transient);
        }
    }
}); 