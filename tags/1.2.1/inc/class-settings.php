<?php
namespace AIFI;

/**
 * Handles plugin settings and settings page.
 *
 * @package AIFI
 */
class Settings {
    /**
     * Settings page slug.
     *
     * @var string
     */
    private $page = 'ai-featured-image-generator';

    /**
     * Settings option name.
     *
     * @var string
     */
    private $option = 'aifi_settings';

    /**
     * Initialize settings.
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Add settings page to WordPress admin.
     */
    public function add_settings_page() {
        add_options_page(
            __('AI Featured Image Settings', 'ai-featured-image-generator'),
            __('AI Featured Image', 'ai-featured-image-generator'),
            'manage_options',
            $this->page,
            array($this, 'render_settings_page')
        );
    }

    /**
     * Register settings.
     */
    public function register_settings() {
        register_setting($this->page, $this->option, array(
            'sanitize_callback' => array($this, 'sanitize_settings')
        ));

        add_settings_section(
            'aifi_main_section',
            __('API Settings', 'ai-featured-image-generator'),
            array($this, 'render_section'),
            $this->page
        );

        // API Key
        add_settings_field(
            'api_key',
            __('API Key', 'ai-featured-image-generator'),
            array($this, 'render_api_key_field'),
            $this->page,
            'aifi_main_section'
        );

        // Default Size
        add_settings_field(
            'default_size',
            __('Default Image Size', 'ai-featured-image-generator'),
            array($this, 'render_size_field'),
            $this->page,
            'aifi_main_section'
        );

        // Default Style
        add_settings_field(
            'default_style',
            __('Default Style', 'ai-featured-image-generator'),
            array($this, 'render_style_field'),
            $this->page,
            'aifi_main_section'
        );

        // Allow Text on Image
        add_settings_field(
            'allow_text',
            __('Allow text on generated image', 'ai-featured-image-generator'),
            array($this, 'render_allow_text_field'),
            $this->page,
            'aifi_main_section'
        );

        // Output Format
        add_settings_field(
            'output_format',
            __('Output Format', 'ai-featured-image-generator'),
            array($this, 'render_output_format_field'),
            $this->page,
            'aifi_main_section'
        );

        // Image Quality
        add_settings_field(
            'image_quality',
            __('Image Quality', 'ai-featured-image-generator'),
            array($this, 'render_quality_field'),
            $this->page,
            'aifi_main_section'
        );

        // AI Model
        add_settings_field(
            'ai_model',
            __('AI Model', 'ai-featured-image-generator'),
            array($this, 'render_model_field'),
            $this->page,
            'aifi_main_section'
        );
    }

    /**
     * Render settings page.
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields($this->page);
                do_settings_sections($this->page);
                submit_button();
                ?>
            </form>
            
            <!-- Buy Me a Coffee Widget -->
            <div class="aifi-coffee-widget">
                <div class="aifi-coffee-content">
                    <div class="aifi-coffee-text">
                        <h3><?php esc_html_e('Enjoying the plugin?', 'ai-featured-image-generator'); ?></h3>
                        <p><?php esc_html_e('Every cup of coffee fuels the passion behind this plugin. Your support means the world to me and helps me continue creating amazing features!', 'ai-featured-image-generator'); ?></p>
                    </div>
                    <div class="aifi-coffee-button">
                        <a href="https://buymeacoffee.com/unclegold" target="_blank" rel="noopener" class="aifi-buy-coffee-btn">
                            <span class="aifi-coffee-icon">☕</span>
                            <?php esc_html_e('Buy me a coffee', 'ai-featured-image-generator'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render section description.
     */
    public function render_section() {
        echo '<p>' . esc_html__('Configure your AI image generation settings below.', 'ai-featured-image-generator') . '</p>';
    }

    /**
     * Render API key field.
     */
    public function render_api_key_field() {
        $settings = $this->get_settings();
        $api_key = isset($settings['api_key']) ? $settings['api_key'] : '';
        ?>
        <input type="password" 
               name="<?php echo esc_attr($this->option); ?>[api_key]" 
               value="<?php echo esc_attr($api_key); ?>" 
               class="regular-text"
               aria-describedby="aifi-api-key-help">
        <p id="aifi-api-key-help" class="description">
            <?php echo sprintf(
                /* translators: %s: OpenAI API keys page URL */
                esc_html__('To get your API key, sign in at %s. verify your organization if needed, click Create new secret key, copy it, then paste it into the API Key field above and click Save Changes.', 'ai-featured-image-generator'),
                '<a href="https://platform.openai.com/api-keys" target="_blank" rel="noopener">platform.openai.com/api-keys</a>'
            ); ?>
        </p>
        <?php
    }

    /**
     * Render size field.
     */
    public function render_size_field() {
        $settings = $this->get_settings();
        $size = isset($settings['default_size']) ? $settings['default_size'] : '1024x1024';
        
        // Sizes for GPT-4 Vision
        $sizes = array(
            '1024x1024' => '1024x1024 (Square)',
            '1024x1536' => '1024x1536 (Portrait)',
            '1536x1024' => '1536x1024 (Landscape)'
        );
        ?>
        <select name="<?php echo esc_attr($this->option); ?>[default_size]">
            <?php foreach ($sizes as $value => $label) : ?>
                <option value="<?php echo esc_attr($value); ?>" <?php selected($size, $value); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }

    /**
     * Render style field.
     */
    public function render_style_field() {
        $settings = $this->get_settings();
        $style = isset($settings['default_style']) ? $settings['default_style'] : 'realistic';
        $styles = array(
            'none' => __('None (No Style)', 'ai-featured-image-generator'),
            'realistic' => __('Realistic', 'ai-featured-image-generator'),
            'artistic' => __('Artistic', 'ai-featured-image-generator'),
            'cartoon' => __('Cartoon', 'ai-featured-image-generator'),
            'sketch' => __('Sketch', 'ai-featured-image-generator'),
            'watercolor' => __('Watercolor', 'ai-featured-image-generator'),
            '3d' => __('3D Render', 'ai-featured-image-generator'),
            'pixel' => __('Pixel Art', 'ai-featured-image-generator'),
            'cyberpunk' => __('Cyberpunk', 'ai-featured-image-generator'),
            'fantasy' => __('Fantasy', 'ai-featured-image-generator'),
            'anime' => __('Anime', 'ai-featured-image-generator'),
            'minimalist' => __('Minimalist', 'ai-featured-image-generator'),
            'technicolor' => __('Technicolor', 'ai-featured-image-generator')
        );
        ?>
        <select name="<?php echo esc_attr($this->option); ?>[default_style]">
            <?php foreach ($styles as $value => $label) : ?>
                <option value="<?php echo esc_attr($value); ?>" <?php selected($style, $value); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }

    /**
     * Render allow text field.
     */
    public function render_allow_text_field() {
        $settings = $this->get_settings();
        $allow_text = isset($settings['allow_text']) ? (bool)$settings['allow_text'] : false;
        ?>
        <label>
            <input type="checkbox" name="<?php echo esc_attr($this->option); ?>[allow_text]" value="1" <?php checked($allow_text, true); ?>>
            <?php esc_html_e('Allow text, captions, or words to appear in generated images', 'ai-featured-image-generator'); ?>
        </label>
        <p class="description"><?php esc_html_e('If checked, the AI will be instructed to add the specified text to the image. If unchecked, the AI will avoid adding any text, captions, or words.', 'ai-featured-image-generator'); ?></p>
        <?php
    }

    /**
     * Render output format field.
     */
    public function render_output_format_field() {
        $settings = $this->get_settings();
        $output_format = isset($settings['output_format']) ? $settings['output_format'] : 'webp';
        
        $formats = array(
            'webp' => 'WebP (Recommended)',
            'png' => 'PNG',
            'jpeg' => 'JPEG'
        );
        ?>
        <select name="<?php echo esc_attr($this->option); ?>[output_format]">
            <?php foreach ($formats as $value => $label) : ?>
                <option value="<?php echo esc_attr($value); ?>" <?php selected($output_format, $value); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description"><?php esc_html_e('Choose the output format for generated images. WebP provides the best compression and quality.', 'ai-featured-image-generator'); ?></p>
        <?php
    }

    /**
     * Render quality field.
     */
    public function render_quality_field() {
        $settings = $this->get_settings();
        $quality = isset($settings['image_quality']) ? intval($settings['image_quality']) : 90;
        ?>
        <input type="range" 
               name="<?php echo esc_attr($this->option); ?>[image_quality]" 
               value="<?php echo esc_attr($quality); ?>" 
               min="1" 
               max="100" 
               class="regular-text"
               id="aifi-quality-slider"
               oninput="document.getElementById('aifi-quality-value').textContent = this.value">
        <span id="aifi-quality-value"><?php echo esc_html($quality); ?></span>
        <p class="description"><?php esc_html_e('Choose image quality from 1 to 100. Higher quality means larger files, while lower quality creates smaller files and reduces costs.', 'ai-featured-image-generator'); ?></p>
        <?php
    }

    /**
     * Render model field.
     */
    public function render_model_field() {
        $settings = $this->get_settings();
        $model = isset($settings['ai_model']) ? $settings['ai_model'] : 'gpt-image-1';
        
        $models = array(
            'gpt-image-1' => 'GPT Image 1 (Standard)',
            'gpt-image-1-mini' => 'GPT Image 1 Mini (Cheaper)'
        );
        ?>
        <select name="<?php echo esc_attr($this->option); ?>[ai_model]">
            <?php foreach ($models as $value => $label) : ?>
                <option value="<?php echo esc_attr($value); ?>" <?php selected($model, $value); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description"><?php esc_html_e('Choose the AI model for image generation. GPT Image 1 Mini is faster but may produce slightly different results.', 'ai-featured-image-generator'); ?></p>
        <?php
    }

    /**
     * Sanitize settings.
     *
     * @param array $input Settings input.
     * @return array
     */
    public function sanitize_settings($input) {
        $sanitized = array();

        if (isset($input['api_key'])) {
            $sanitized['api_key'] = sanitize_text_field($input['api_key']);
        }

        if (isset($input['default_size'])) {
            $sanitized['default_size'] = sanitize_text_field($input['default_size']);
        }

        if (isset($input['default_style'])) {
            $sanitized['default_style'] = sanitize_text_field($input['default_style']);
        }

        if (isset($input['allow_text'])) {
            $sanitized['allow_text'] = $input['allow_text'] ? 1 : 0;
        }

        if (isset($input['output_format'])) {
            $allowed_formats = array('webp', 'png', 'jpeg');
            $sanitized['output_format'] = in_array($input['output_format'], $allowed_formats) ? $input['output_format'] : 'webp';
        }

        if (isset($input['image_quality'])) {
            $quality = intval($input['image_quality']);
            $sanitized['image_quality'] = max(1, min(100, $quality)); // Ensure quality is between 1 and 100
        }

        if (isset($input['ai_model'])) {
            $allowed_models = array('gpt-image-1', 'gpt-image-1-mini');
            $sanitized['ai_model'] = in_array($input['ai_model'], $allowed_models) ? $input['ai_model'] : 'gpt-image-1';
        }

        return $sanitized;
    }

    /**
     * Get plugin settings.
     *
     * @return array
     */
    public function get_settings() {
        if (is_multisite()) {
            $settings = get_network_option(null, $this->option);
        } else {
            $settings = get_option($this->option);
        }

        if (isset($settings['auto_generate'])) {
            unset($settings['auto_generate']);
        }
        return is_array($settings) ? $settings : array();
    }
} 