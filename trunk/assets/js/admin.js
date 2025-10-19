(function($) {
    'use strict';

    // Cache DOM elements
    const $generateButton = $('#aifi-generate');
    const $promptInput = $('#aifi-prompt');
    const $customTextInput = $('#aifi-custom-text');
    const $styleSelect = $('#aifi-style');
    const $qualityInput = $('#aifi-quality');
    const $preview = $('#aifi-preview');
    const $message = $('#aifi-message');
    const $spinner = $('.aifi-actions .spinner');
    const $loadingSpinner = $('#aifi-loading-spinner');

    // Handle generate button click
    $generateButton.on('click', function(e) {
        e.preventDefault();

        // Reset UI
        $preview.hide();
        $message.hide().removeClass('success error');
        $generateButton.prop('disabled', true);
        $spinner.addClass('is-active');
        $loadingSpinner.show();

        // Get post ID from the page
        const postId = $('#post_ID').val();
        
        // Get post title - compatible with both Classic Editor and Block Editor
        let postTitle = '';
        
        // Try Classic Editor first (title field)
        const titleField = $('#title');
        if (titleField.length && titleField.val()) {
            postTitle = titleField.val();
        } else {
            // Fallback to Block Editor method
            const titleFromH1 = $('h1:nth(1)').text().replace("· Post","");
            if (titleFromH1 && titleFromH1.trim()) {
                postTitle = titleFromH1;
            } else {
                // Try alternative selectors for different editor versions
                const altTitle = $('.editor-post-title__input').val() || $('.wp-block-post-title input').val() || '';
                postTitle = altTitle;
            }
        }

        // Prepare request data
        const data = {
            post_id: postId,
            title: postTitle,
            prompt: $promptInput.val(),
            custom_text: $customTextInput.val(),
            style: $styleSelect.val(),
            quality: parseInt($qualityInput.val())
        };

        // Make API request
        $.ajax({
            url: aifiData.restUrl,
            method: 'POST',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', aifiData.nonce);
                xhr.setRequestHeader('Content-Type', 'application/json');
            },
            data: JSON.stringify(data),
            processData: false,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    $message
                        .addClass('success')
                        .text(aifiData.i18n.success)
                        .show();

                    // Show preview
                    $preview
                        .find('img')
                        .attr('src', response.url)
                        .end()
                        .show();

                    // Update featured image in the editor
                    if (aifiData.isClassicEditor) {
                        // Classic Editor: Set featured image using traditional method
                        $('#_thumbnail_id').val(response.attachment_id);
                        
                        // Update the featured image display in Classic Editor
                        const featuredImageContainer = $('#postimagediv');
                        if (featuredImageContainer.length) {
                            // Remove existing image if any
                            featuredImageContainer.find('.inside img').remove();
                            
                            // Add new image
                            const imgElement = $('<img>').attr({
                                'src': response.url,
                                'style': 'max-width: 100%; height: auto;'
                            });
                            
                            featuredImageContainer.find('.inside').prepend(imgElement);
                            
                            // Show the "Remove featured image" link if hidden
                            featuredImageContainer.find('.remove-post-thumbnail').show();
                        }
                    } else {
                        // Block Editor: Use wp.media method
                        if (wp.media && wp.media.featuredImage) {
                            wp.media.featuredImage.set(response.attachment_id);
                        }
                    }
                } else {
                    showError(response.message || aifiData.i18n.error);
                }
            },
            error: function(xhr) {
                let message = aifiData.i18n.error;
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                showError(message);
            },
            complete: function() {
                $generateButton.prop('disabled', false);
                $spinner.removeClass('is-active');
                $loadingSpinner.hide();
            }
        });
    });

    /**
     * Show error message.
     *
     * @param {string} message Error message to display.
     */
    function showError(message) {
        $message
            .addClass('error')
            .text(message)
            .show();
    }

    // Handle prompt input changes
    $promptInput.on('input', function() {
        if ($(this).val().length > 0) {
            $generateButton.prop('disabled', false);
        } else {
            $generateButton.prop('disabled', false);
        }
    });

})(jQuery); 