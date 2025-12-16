/**
 * Immobilien Rechner Pro - Admin Scripts
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Logo Upload
        var mediaUploader;

        $('.irp-upload-logo').on('click', function(e) {
            e.preventDefault();

            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            mediaUploader = wp.media({
                title: 'Choose Logo',
                button: {
                    text: 'Use this image'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });

            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#company_logo').val(attachment.url);
                
                var preview = $('.irp-logo-preview');
                preview.html('<img src="' + attachment.url + '" alt="Logo">');
                
                $('.irp-remove-logo').show();
            });

            mediaUploader.open();
        });

        $('.irp-remove-logo').on('click', function(e) {
            e.preventDefault();
            $('#company_logo').val('');
            $('.irp-logo-preview').empty();
            $(this).hide();
        });

        // Color picker enhancement
        $('input[type="color"]').each(function() {
            var $input = $(this);
            var $wrapper = $('<div class="irp-color-wrapper"></div>');
            var $preview = $('<span class="irp-color-preview"></span>');
            var $hex = $('<input type="text" class="irp-color-hex small-text" maxlength="7">');
            
            $input.wrap($wrapper);
            $input.after($hex);
            $hex.val($input.val());
            
            $input.on('input', function() {
                $hex.val($input.val());
            });
            
            $hex.on('change', function() {
                var val = $hex.val();
                if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
                    $input.val(val);
                }
            });
        });
    });

})(jQuery);
