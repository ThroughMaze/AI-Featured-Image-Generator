<?php
namespace AIFI;

/**
 * Handles the post editor UI for AI image generation.
 *
 * @package AIFI
 */
class Meta_Box {
    /**
     * Settings instance.
     *
     * @var Settings
     */
    private $settings;

    /**
     * Initialize meta box.
     */
    public function __construct($settings) {
        $this->settings = $settings;
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Add meta box to post editor.
     */
    public function add_meta_box() {
        add_meta_box(
            'aifi_meta_box',
            __('AI Featured Image Generator', 'ai-featured-image-generator'),
            array($this, 'render_meta_box'),
            array('post', 'page'), // Add page support
            'side',
            'high'
        );
    }

    /**
     * Enqueue admin scripts and styles.
     *
     * @param string $hook Current admin page.
     */
    public function enqueue_scripts($hook) {
        if (!in_array($hook, array('post.php', 'post-new.php'))) {
            return;
        }

        wp_enqueue_style(
            'aifi-admin',
            AIFI_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            AIFI_VERSION
        );

        wp_enqueue_script(
            'aifi-admin',
            AIFI_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            AIFI_VERSION,
            true
        );

        wp_localize_script('aifi-admin', 'aifiData', array(
            'nonce' => wp_create_nonce('wp_rest'),
            'restUrl' => rest_url('aifi/v1/generate'),
            'i18n' => array(
                'generating' => __('Generating image...', 'ai-featured-image-generator'),
                'success' => __('Image generated successfully!', 'ai-featured-image-generator'),
                'error' => __('Error generating image. Please try again.', 'ai-featured-image-generator'),
                'noApiKey' => __('Please configure your API key in the settings.', 'ai-featured-image-generator')
            )
        ));
    }

    /**
     * Render meta box content.
     *
     * @param \WP_Post $post Post object.
     */
    public function render_meta_box($post) {
        if (!current_user_can('edit_post', $post->ID)) {
            return;
        }

        $settings_data = $this->settings->get_settings();
        $default_style = isset($settings_data['default_style']) ? $settings_data['default_style'] : 'realistic';
        $allow_text = isset($settings_data['allow_text']) ? (bool)$settings_data['allow_text'] : false;

        if (empty($settings_data['api_key'])) {
            printf(
                '<p class="aifi-error">%s <a href="%s">%s</a></p>',
                esc_html__('API key not configured.', 'ai-featured-image-generator'),
                esc_url(admin_url('options-general.php?page=ai-featured-image-generator')),
                esc_html__('Configure now', 'ai-featured-image-generator')
            );
            return;
        }

        wp_nonce_field('aifi_meta_box', 'aifi_meta_box_nonce');
        ?>
        <div class="aifi-meta-box">
            <?php if (!$allow_text): ?>
                <p style="color:#666;font-size:12px;"><em><?php esc_html_e('Text, captions, or words will be avoided in generated images (see plugin settings).', 'ai-featured-image-generator'); ?></em></p>
            <?php else: ?>
                <p style="color:#666;font-size:12px;"><em><?php esc_html_e('Text, captions, or words may appear in generated images (see plugin settings).', 'ai-featured-image-generator'); ?></em></p>
            <?php endif; ?>

            <p>
                <label for="aifi-prompt"><?php esc_html_e('Custom Prompt (optional):', 'ai-featured-image-generator'); ?></label>
                <textarea id="aifi-prompt" 
                          class="widefat" 
                          rows="3" 
                          placeholder="<?php esc_attr_e('Add details to your image prompt...', 'ai-featured-image-generator'); ?>"></textarea>
            </p>

            <p>
                <label for="aifi-style"><?php esc_html_e('Style:', 'ai-featured-image-generator'); ?></label>
                <select id="aifi-style" class="widefat">
                    <option value="realistic" <?php selected($default_style, 'realistic'); ?>><?php esc_html_e('Realistic', 'ai-featured-image-generator'); ?></option>
                    <option value="artistic" <?php selected($default_style, 'artistic'); ?>><?php esc_html_e('Artistic', 'ai-featured-image-generator'); ?></option>
                    <option value="cartoon" <?php selected($default_style, 'cartoon'); ?>><?php esc_html_e('Cartoon', 'ai-featured-image-generator'); ?></option>
                    <option value="sketch" <?php selected($default_style, 'sketch'); ?>><?php esc_html_e('Sketch', 'ai-featured-image-generator'); ?></option>
                    <option value="watercolor" <?php selected($default_style, 'watercolor'); ?>><?php esc_html_e('Watercolor', 'ai-featured-image-generator'); ?></option>
                    <option value="3d" <?php selected($default_style, '3d'); ?>><?php esc_html_e('3D Render', 'ai-featured-image-generator'); ?></option>
                    <option value="pixel" <?php selected($default_style, 'pixel'); ?>><?php esc_html_e('Pixel Art', 'ai-featured-image-generator'); ?></option>
                    <option value="cyberpunk" <?php selected($default_style, 'cyberpunk'); ?>><?php esc_html_e('Cyberpunk', 'ai-featured-image-generator'); ?></option>
                    <option value="fantasy" <?php selected($default_style, 'fantasy'); ?>><?php esc_html_e('Fantasy', 'ai-featured-image-generator'); ?></option>
                    <option value="anime" <?php selected($default_style, 'anime'); ?>><?php esc_html_e('Anime', 'ai-featured-image-generator'); ?></option>
                    <option value="minimalist" <?php selected($default_style, 'minimalist'); ?>><?php esc_html_e('Minimalist', 'ai-featured-image-generator'); ?></option>
                    <option value="technicolor" <?php selected($default_style, 'technicolor'); ?>><?php esc_html_e('Technicolor', 'ai-featured-image-generator'); ?></option>
                </select>
            </p>

            <div class="aifi-actions">
                <button type="button" 
                        id="aifi-generate" 
                        class="button button-primary button-large">
                    <?php esc_html_e('Generate Featured Image', 'ai-featured-image-generator'); ?>
                </button>
                <span class="spinner"></span>
            </div>

            <div id="aifi-preview" class="aifi-preview" style="display: none;">
                <img src="" alt="<?php esc_attr_e('Generated image preview', 'ai-featured-image-generator'); ?>">
            </div>

            <div id="aifi-message" class="aifi-message" style="display: none;"></div>
        </div>
        <?php
    }
} 