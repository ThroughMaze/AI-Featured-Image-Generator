<?php
namespace AIFI;

/**
 * Main plugin class that handles initialization and hook registration.
 *
 * @package AIFI
 */
class Plugin {
    /**
     * Plugin instance.
     *
     * @var Plugin
     */
    private static $instance = null;

    /**
     * Settings instance.
     *
     * @var Settings
     */
    private $settings;

    /**
     * Meta_Box instance.
     *
     * @var Meta_Box
     */
    private $meta_box;

    /**
     * REST instance.
     *
     * @var REST
     */
    private $rest;

    /**
     * Generator instance.
     *
     * @var Generator
     */
    private $generator;

    /**
     * Get plugin instance.
     *
     * @return Plugin
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initialize the plugin.
     */
    public function init() {
        // Initialize components
        $this->settings = new Settings();
        $this->meta_box = new Meta_Box($this->settings);
        $this->rest = new REST();
        $this->generator = new Generator($this->settings);

        // Register hooks
        $this->register_hooks();
    }

    /**
     * Register plugin hooks.
     */
    private function register_hooks() {
        // Add settings link to plugins page
        add_filter('plugin_action_links_' . plugin_basename(AIFI_PLUGIN_DIR . 'ai-featured-image.php'), 
            array($this, 'add_settings_link')
        );
    }

    /**
     * Add settings link to plugin listing.
     *
     * @param array $links Plugin action links.
     * @return array
     */
    public function add_settings_link($links) {
        $settings_link = sprintf(
            '<a href="%s">%s</a>',
            admin_url('options-general.php?page=ai-featured-image-generator'),
            __('Settings', 'ai-featured-image-generator')
        );
        array_unshift($links, $settings_link);
        return $links;
    }
} 