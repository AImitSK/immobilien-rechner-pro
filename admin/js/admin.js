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
                title: irpAdmin.i18n.mediaTitle || 'Logo auswählen',
                button: {
                    text: irpAdmin.i18n.mediaButton || 'Dieses Bild verwenden'
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

        // Matrix: Update Vervielfältiger example calculations
        $('.irp-factor-input').on('input', function() {
            var region = $(this).data('region');
            var factor = parseFloat($(this).val()) || 0;
            var monthlyRent = 1000;
            var price = monthlyRent * 12 * factor;

            $('.irp-calc-price[data-region="' + region + '"]').text(
                price.toLocaleString('de-DE')
            );
        });

        // Matrix: Update multiplier impact display
        $('.irp-data-table input[type="number"]').on('input', function() {
            var $row = $(this).closest('tr');
            var $impactCell = $row.find('.irp-positive, .irp-negative');

            if ($impactCell.length && $(this).closest('table').find('th').length === 3) {
                var multiplier = parseFloat($(this).val()) || 1;
                var impact = (multiplier - 1) * 100;
                var sign = impact >= 0 ? '+' : '';

                $impactCell
                    .text(sign + Math.round(impact) + '%')
                    .removeClass('irp-positive irp-negative')
                    .addClass(impact >= 0 ? 'irp-positive' : 'irp-negative');
            }
        });

        // Matrix: Update feature premium example (based on 80m²)
        $('#tab-features .irp-data-table input[type="number"]').on('input', function() {
            var premium = parseFloat($(this).val()) || 0;
            var monthlyExtra = premium * 80;
            var $example = $(this).closest('tr').find('.irp-positive');

            if ($example.length) {
                $example.text('+' + Math.round(monthlyExtra).toLocaleString('de-DE') + ' €/Monat');
            }
        });

        // Cities: Add new city row
        $('#irp-add-city').on('click', function() {
            var $tbody = $('#irp-cities-body');
            var $rows = $tbody.find('.irp-city-row');
            var newIndex = 0;

            // Find the highest index and add 1
            $rows.each(function() {
                var idx = parseInt($(this).data('index')) || 0;
                if (idx >= newIndex) {
                    newIndex = idx + 1;
                }
            });

            var newRow = `
                <tr class="irp-city-row" data-index="${newIndex}">
                    <td>
                        <input type="text"
                               name="irp_price_matrix[cities][${newIndex}][id]"
                               value=""
                               class="regular-text irp-city-id"
                               placeholder="z.B. stadt_name"
                               pattern="[a-z0-9_-]+"
                               required>
                    </td>
                    <td>
                        <input type="text"
                               name="irp_price_matrix[cities][${newIndex}][name]"
                               value=""
                               class="regular-text"
                               placeholder="Stadtname"
                               required>
                    </td>
                    <td>
                        <input type="number"
                               name="irp_price_matrix[cities][${newIndex}][base_price]"
                               value="12.00"
                               step="0.10"
                               min="1"
                               max="100"
                               class="small-text"> €/m²
                    </td>
                    <td>
                        <input type="number"
                               name="irp_price_matrix[cities][${newIndex}][sale_factor]"
                               value="25"
                               step="0.5"
                               min="5"
                               max="60"
                               class="small-text">
                    </td>
                    <td>
                        <button type="button" class="button irp-remove-city" title="Stadt entfernen">
                            <span class="dashicons dashicons-trash"></span>
                        </button>
                    </td>
                </tr>
            `;

            $tbody.append(newRow);
        });

        // Cities: Remove city row
        $(document).on('click', '.irp-remove-city', function() {
            var $tbody = $('#irp-cities-body');
            var $rows = $tbody.find('.irp-city-row');

            // Don't allow removing the last row
            if ($rows.length <= 1) {
                alert('Sie müssen mindestens eine Stadt konfiguriert haben.');
                return;
            }

            $(this).closest('tr').remove();
        });

        // Cities: Auto-generate ID from name
        $(document).on('input', '.irp-cities-table input[name*="[name]"]', function() {
            var $row = $(this).closest('tr');
            var $idInput = $row.find('.irp-city-id');

            // Only auto-fill if ID is empty
            if (!$idInput.val()) {
                var name = $(this).val();
                var id = name
                    .toLowerCase()
                    .replace(/ä/g, 'ae')
                    .replace(/ö/g, 'oe')
                    .replace(/ü/g, 'ue')
                    .replace(/ß/g, 'ss')
                    .replace(/[^a-z0-9]/g, '_')
                    .replace(/_+/g, '_')
                    .replace(/^_|_$/g, '');

                $idInput.val(id);
            }
        });
    });

})(jQuery);
