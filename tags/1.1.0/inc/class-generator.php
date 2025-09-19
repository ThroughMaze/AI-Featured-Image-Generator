<?php
namespace AIFI;

/**
 * Handles AI image generation and media library integration.
 *
 * @package AIFI
 */
class Generator {
    /**
     * Settings instance.
     *
     * @var Settings
     */
    private $settings;

    /**
     * Initialize generator.
     */
    public function __construct($settings) {
        $this->settings = $settings;
    }

    /**
     * Generate image for a post.
     *
     * @param int    $post_id Post ID.
     * @param string $prompt  Optional custom prompt.
     * @param string $style   Optional style override.
     * @param string $title   Optional custom title.
     * @param string $custom_text Optional custom text for image.
     * @return int|\WP_Error Attachment ID on success, WP_Error on failure.
     */
    public function generate_image($post_id, $prompt = '', $style = '', $title = '', $custom_text = '') {
        $settings = $this->settings->get_settings();

        $post = get_post($post_id);

        if (!$post) {
            return new \WP_Error(
                'invalid_post',
                __('Invalid post ID.', 'ai-featured-image-generator')
            );
        }

        // Build prompt
        $base_prompt = !empty($title) ? $title : $post->post_title;
        if (!empty($prompt)) {
            $base_prompt .= ' - ' . $prompt;
        }

        // Apply style
        $style = !empty($style) ? $style : $settings['default_style'];
        $style_prompt = $this->get_style_prompt($style);
        $final_prompt = $base_prompt;
        
        // Only add style prompt if style is not 'none'
        if ($style !== 'none') {
            $final_prompt .= ' ' . $style_prompt;
        }

        // Handle allow_text setting
        if (empty($settings['allow_text']) || $settings['allow_text'] === false) {
            $final_prompt .= ' no text, no captions, no words, no letters, no writing';
        } else {
            // Use custom text if provided, otherwise use title
            $text_to_add = !empty($custom_text) ? $custom_text : $title;
            $text_to_add = explode('·', $text_to_add)[0]; // Remove any WordPress admin suffixes
            $final_prompt .= 'please add the "' . $text_to_add . '" text on the image ';
        }
    
        // Allow filtering the prompt
        $final_prompt = apply_filters('aifi_build_prompt', $final_prompt, $post, $style);
        
        // Call AI API
        $image_data = $this->call_ai_api($final_prompt, $settings);
        if (is_wp_error($image_data)) {
            return $image_data;
        }

        // Download and save image
        $attachment_id = $this->save_image($image_data, $post_id);
        if (is_wp_error($attachment_id)) {
            return $attachment_id;
        }

        // Set as featured image
        set_post_thumbnail($post_id, $attachment_id);

        // Fire action hook
        do_action('aifi_after_generate', $attachment_id, $post_id);

        return $attachment_id;
    }

    /**
     * Get style-specific prompt.
     *
     * @param string $style Style name.
     * @return string
     */
    private function get_style_prompt($style) {
        $styles = array(
            'realistic' => 'in a realistic, photographic style',
            'artistic' => 'in an artistic, painterly style',
            'cartoon' => 'in a cartoon, animated style',
            'sketch' => 'in a detailed sketch style',
            'watercolor' => 'in a beautiful watercolor painting style',
            '3d' => 'as a high-quality 3D render',
            'pixel' => 'in retro pixel art style',
            'cyberpunk' => 'in a vibrant cyberpunk style',
            'fantasy' => 'in a magical fantasy art style',
            'anime' => 'in detailed anime style',
            'minimalist' => 'in a clean minimalist style',
            'technicolor' => 'in vivid technicolor style'
        );

        return isset($styles[$style]) ? $styles[$style] : $styles['realistic'];
    }

    /**
     * Handle API errors with human-readable messages.
     *
     * @param int    $response_code HTTP response code.
     * @param string $error_message Original error message from API.
     * @param string $body          Response body.
     * @return \WP_Error
     */
    private function handle_api_error($response_code, $error_message, $body = '') {
        $error_data = json_decode($body, true);
        $api_error_message = isset($error_data['error']['message']) ? $error_data['error']['message'] : $error_message;
        
        // Common API error patterns and their human-readable equivalents
        $error_mappings = array(
            // Authentication errors
            'invalid_api_key' => __('Your API key is invalid or expired. Please check your API key in the plugin settings.', 'ai-featured-image-generator'),
            'insufficient_quota' => __('You have exceeded your API usage quota. Please check your OpenAI account billing.', 'ai-featured-image-generator'),
            'billing_not_active' => __('Your OpenAI account billing is not active. Please add a payment method to your account.', 'ai-featured-image-generator'),
            
            // Content policy errors
            'content_policy' => __('Your prompt contains content that violates OpenAI\'s usage policies. Please modify your prompt and try again.', 'ai-featured-image-generator'),
            'safety_system' => __('Your prompt was rejected by the safety system. Please modify your prompt to avoid potentially harmful content.', 'ai-featured-image-generator'),
            
            // Rate limiting
            'rate_limit' => __('You are making requests too quickly. Please wait a moment before trying again.', 'ai-featured-image-generator'),
            
            // Server errors
            'server_error' => __('OpenAI servers are experiencing issues. Please try again in a few minutes.', 'ai-featured-image-generator'),
            'service_unavailable' => __('The image generation service is temporarily unavailable. Please try again later.', 'ai-featured-image-generator'),
        );
        
        // Check for specific error patterns
        $lower_error = strtolower($api_error_message);
        
        // Authentication and billing errors
        if (strpos($lower_error, 'invalid api key') !== false || strpos($lower_error, 'incorrect api key') !== false) {
            return new \WP_Error('api_auth_error', $error_mappings['invalid_api_key'], array('status' => $response_code));
        }
        
        if (strpos($lower_error, 'quota') !== false || strpos($lower_error, 'billing') !== false) {
            return new \WP_Error('api_quota_error', $error_mappings['insufficient_quota'], array('status' => $response_code));
        }
        
        if (strpos($lower_error, 'billing_not_active') !== false) {
            return new \WP_Error('api_billing_error', $error_mappings['billing_not_active'], array('status' => $response_code));
        }
        
        // Content policy errors
        if (strpos($lower_error, 'content policy') !== false || strpos($lower_error, 'usage policy') !== false) {
            return new \WP_Error('api_content_error', $error_mappings['content_policy'], array('status' => $response_code));
        }
        
        if (strpos($lower_error, 'safety system') !== false || strpos($lower_error, 'safety') !== false) {
            return new \WP_Error('api_safety_error', $error_mappings['safety_system'], array('status' => $response_code));
        }
        
        // Rate limiting
        if (strpos($lower_error, 'rate limit') !== false || strpos($lower_error, 'too many requests') !== false) {
            return new \WP_Error('api_rate_limit', $error_mappings['rate_limit'], array('status' => $response_code));
        }
        
        // Server errors
        if ($response_code >= 500) {
            return new \WP_Error('api_server_error', $error_mappings['server_error'], array('status' => $response_code));
        }
        
        if ($response_code === 503) {
            return new \WP_Error('api_unavailable', $error_mappings['service_unavailable'], array('status' => $response_code));
        }
        
        // Generic error handling based on HTTP status codes
        switch ($response_code) {
            case 400:
                return new \WP_Error('api_bad_request', 
                    __('Invalid request. Please check your prompt and try again.', 'ai-featured-image-generator'), 
                    array('status' => $response_code));
            case 401:
                return new \WP_Error('api_unauthorized', 
                    __('Authentication failed. Please check your API key.', 'ai-featured-image-generator'), 
                    array('status' => $response_code));
            case 403:
                return new \WP_Error('api_forbidden', 
                    __('Access denied. Please check your API key permissions.', 'ai-featured-image-generator'), 
                    array('status' => $response_code));
            case 404:
                return new \WP_Error('api_not_found', 
                    __('The requested service was not found. Please try again later.', 'ai-featured-image-generator'), 
                    array('status' => $response_code));
            case 429:
                return new \WP_Error('api_rate_limit', 
                    __('Too many requests. Please wait a moment before trying again.', 'ai-featured-image-generator'), 
                    array('status' => $response_code));
            default:
                // Fallback to original error message if no specific mapping found
                return new \WP_Error('api_error', 
                    sprintf(__('API error (HTTP %d): %s', 'ai-featured-image-generator'), $response_code, $api_error_message), 
                    array('status' => $response_code));
        }
    }

    /**
     * Call AI image generation API.
     *
     * @param string $prompt   Image prompt.
     * @param array  $settings Plugin settings.
     * @return string|\WP_Error Image data on success, WP_Error on failure.
     */
    private function call_ai_api($prompt, $settings) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        
        $api_url = 'https://api.openai.com/v1/images/generations';
        $size = isset($settings['default_size']) ? $settings['default_size'] : '1024x1024';

        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $settings['api_key'],
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode(array(
                'model' => 'gpt-image-1',
                'prompt' => $prompt,
                'n' => 1,
                'size' => $size
            )),
            'timeout' => 240,
            'httpversion' => '1.1',
            'sslverify' => true,
            'blocking' => true
        );

        // Allow filtering API request arguments
        $args = apply_filters('aifi_api_request_args', $args);

        $response = wp_remote_post($api_url, $args);

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            
            // Handle timeout specifically
            if (strpos($error_message, 'timed out') !== false) {
                return new \WP_Error(
                    'api_timeout',
                    __('The request timed out. The image generation is taking longer than expected. Please try again.', 'ai-featured-image-generator')
                );
            }
            
            // Handle connection errors
            if (strpos($error_message, 'could not resolve host') !== false) {
                return new \WP_Error(
                    'api_connection_error',
                    __('Unable to connect to OpenAI servers. Please check your internet connection and try again.', 'ai-featured-image-generator')
                );
            }
            
            // Handle SSL errors
            if (strpos($error_message, 'ssl') !== false || strpos($error_message, 'certificate') !== false) {
                return new \WP_Error(
                    'api_ssl_error',
                    __('SSL connection error. Please try again or contact your hosting provider.', 'ai-featured-image-generator')
                );
            }
            
            // Generic connection error
            return new \WP_Error(
                'api_connection_error',
                sprintf(
                    __('Connection failed: %s', 'ai-featured-image-generator'),
                    $error_message
                )
            );
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);

        if ($response_code !== 200) {
            $error_message = __('Unknown API error occurred.', 'ai-featured-image-generator');
            return $this->handle_api_error($response_code, $error_message, $body);
        }

        // Parse the JSON response
        $response_data = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new \WP_Error(
                'api_parse_error',
                __('Invalid response from OpenAI API. Please try again.', 'ai-featured-image-generator')
            );
        }
        
        if (!isset($response_data['data'][0]['b64_json'])) {
            return new \WP_Error(
                'api_data_error',
                __('No image data found in API response. The image generation may have failed.', 'ai-featured-image-generator')
            );
        }

        // Decode the base64 image data
        $image_data = base64_decode($response_data['data'][0]['b64_json']);
        if ($image_data === false) {
            return new \WP_Error(
                'api_decode_error',
                __('Failed to process the generated image data. Please try again.', 'ai-featured-image-generator')
            );
        }

        // Save the image data to a temporary file
        $temp_file = wp_tempnam('aifi-');
        if (!$temp_file) {
            return new \WP_Error(
                'api_temp_file_error',
                __('Unable to create temporary file for the image. Please check your server permissions.', 'ai-featured-image-generator')
            );
        }

        file_put_contents($temp_file, $image_data);

        return $temp_file;
    }

    /**
     * Save image to media library.
     *
     * @param string $image_data Image data or temporary file path.
     * @param int    $post_id   Post ID.
     * @return int|\WP_Error Attachment ID on success, WP_Error on failure.
     */
    private function save_image($image_data, $post_id) {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // If we received a temporary file path, use it directly
        if (file_exists($image_data)) {
            $temp_file = $image_data;
        } else {
            // Download file to temp dir if we received a URL
            $temp_file = download_url($image_data);
            if (is_wp_error($temp_file)) {
                return $temp_file;
            }
        }

        $file_array = array(
            'name' => 'ai-generated-' . $post_id . '.png',
            'tmp_name' => $temp_file
        );

        // Move the temporary file into the uploads directory
        $attachment_id = media_handle_sideload($file_array, $post_id);

        // Clean up the temporary file
        if (file_exists($temp_file)) {
            wp_delete_file($temp_file);
        }

        if (is_wp_error($attachment_id)) {
            return $attachment_id;
        }

        return $attachment_id;
    }
} 