<?php
/**
 * Admin Settings View
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap irp-admin-wrap">
    <h1><?php esc_html_e('Einstellungen', 'immobilien-rechner-pro'); ?></h1>

    <form method="post" action="options.php">
        <?php settings_fields('irp_settings_group'); ?>

        <div class="irp-settings-section">
            <h2><?php esc_html_e('Darstellung', 'immobilien-rechner-pro'); ?></h2>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="calculator_max_width"><?php esc_html_e('Maximale Breite', 'immobilien-rechner-pro'); ?></label>
                    </th>
                    <td>
                        <?php $max_width = $settings['calculator_max_width'] ?? 680; ?>
                        <div class="irp-range-slider-container">
                            <input type="range" id="calculator_max_width" name="irp_settings[calculator_max_width]"
                                   value="<?php echo esc_attr($max_width); ?>"
                                   min="680" max="1200" step="20" class="irp-range-slider">
                            <output for="calculator_max_width" class="irp-range-value"><?php echo esc_html($max_width); ?>px</output>
                        </div>
                        <p class="description"><?php esc_html_e('Maximale Breite des Rechners (680px - 1200px).', 'immobilien-rechner-pro'); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="irp-settings-section">
            <h2><?php esc_html_e('Branding', 'immobilien-rechner-pro'); ?></h2>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="company_name"><?php esc_html_e('Firmenname', 'immobilien-rechner-pro'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="company_name" name="irp_settings[company_name]"
                               value="<?php echo esc_attr($settings['company_name'] ?? ''); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="company_logo"><?php esc_html_e('Firmenlogo', 'immobilien-rechner-pro'); ?></label>
                    </th>
                    <td>
                        <input type="hidden" id="company_logo" name="irp_settings[company_logo]"
                               value="<?php echo esc_url($settings['company_logo'] ?? ''); ?>">
                        <div class="irp-logo-preview">
                            <?php if (!empty($settings['company_logo'])) : ?>
                                <img src="<?php echo esc_url($settings['company_logo']); ?>" alt="Logo">
                            <?php endif; ?>
                        </div>
                        <button type="button" class="button irp-upload-logo">
                            <?php esc_html_e('Logo hochladen', 'immobilien-rechner-pro'); ?>
                        </button>
                        <button type="button" class="button irp-remove-logo" <?php echo empty($settings['company_logo']) ? 'style="display:none"' : ''; ?>>
                            <?php esc_html_e('Entfernen', 'immobilien-rechner-pro'); ?>
                        </button>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="primary_color"><?php esc_html_e('Primärfarbe', 'immobilien-rechner-pro'); ?></label>
                    </th>
                    <td>
                        <input type="color" id="primary_color" name="irp_settings[primary_color]"
                               value="<?php echo esc_attr($settings['primary_color'] ?? '#2563eb'); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="secondary_color"><?php esc_html_e('Sekundärfarbe', 'immobilien-rechner-pro'); ?></label>
                    </th>
                    <td>
                        <input type="color" id="secondary_color" name="irp_settings[secondary_color]"
                               value="<?php echo esc_attr($settings['secondary_color'] ?? '#1e40af'); ?>">
                    </td>
                </tr>
            </table>
        </div>

        <div class="irp-settings-section">
            <h2><?php esc_html_e('Kontakt & Benachrichtigungen', 'immobilien-rechner-pro'); ?></h2>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="company_email"><?php esc_html_e('Benachrichtigungs-E-Mail', 'immobilien-rechner-pro'); ?></label>
                    </th>
                    <td>
                        <input type="email" id="company_email" name="irp_settings[company_email]"
                               value="<?php echo esc_attr($settings['company_email'] ?? get_option('admin_email')); ?>" class="regular-text">
                        <p class="description"><?php esc_html_e('Neue Lead-Benachrichtigungen werden an diese E-Mail-Adresse gesendet.', 'immobilien-rechner-pro'); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="irp-settings-section">
            <h2><?php esc_html_e('Rechner-Standardwerte', 'immobilien-rechner-pro'); ?></h2>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="default_maintenance_rate"><?php esc_html_e('Instandhaltungsrate (%)', 'immobilien-rechner-pro'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="default_maintenance_rate" name="irp_settings[default_maintenance_rate]"
                               value="<?php echo esc_attr($settings['default_maintenance_rate'] ?? 1.5); ?>"
                               step="0.1" min="0" max="10" class="small-text"> %
                        <p class="description"><?php esc_html_e('Jährliche Instandhaltungskosten als Prozentsatz des Immobilienwerts.', 'immobilien-rechner-pro'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="default_vacancy_rate"><?php esc_html_e('Leerstandsrate (%)', 'immobilien-rechner-pro'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="default_vacancy_rate" name="irp_settings[default_vacancy_rate]"
                               value="<?php echo esc_attr($settings['default_vacancy_rate'] ?? 3); ?>"
                               step="0.5" min="0" max="20" class="small-text"> %
                        <p class="description"><?php esc_html_e('Erwartete Leerstandsrate für die Mieteinnahmen-Berechnung.', 'immobilien-rechner-pro'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="default_broker_commission"><?php esc_html_e('Maklerprovision (%)', 'immobilien-rechner-pro'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="default_broker_commission" name="irp_settings[default_broker_commission]"
                               value="<?php echo esc_attr($settings['default_broker_commission'] ?? 3.57); ?>"
                               step="0.01" min="0" max="10" class="small-text"> %
                        <p class="description"><?php esc_html_e('Standard-Maklerprovision für Verkaufsberechnungen.', 'immobilien-rechner-pro'); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="irp-settings-section">
            <h2><?php esc_html_e('Google Maps Integration', 'immobilien-rechner-pro'); ?></h2>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="google_maps_api_key"><?php esc_html_e('Google Maps API Key', 'immobilien-rechner-pro'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="google_maps_api_key" name="irp_settings[google_maps_api_key]"
                               value="<?php echo esc_attr($settings['google_maps_api_key'] ?? ''); ?>" class="regular-text"
                               placeholder="AIzaSy...">
                        <p class="description"><?php esc_html_e('Wird für die Kartenanzeige und Adress-Autocomplete im Lage-Step benötigt.', 'immobilien-rechner-pro'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Karte anzeigen', 'immobilien-rechner-pro'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="irp_settings[show_map_in_location_step]" value="1"
                                   <?php checked(!empty($settings['show_map_in_location_step']), true); ?>>
                            <?php esc_html_e('Karte im Lage-Bewertungs-Step anzeigen', 'immobilien-rechner-pro'); ?>
                        </label>
                    </td>
                </tr>
            </table>

            <div class="irp-info-box">
                <h4><?php esc_html_e('So erhalten Sie einen API-Key:', 'immobilien-rechner-pro'); ?></h4>
                <ol>
                    <li><?php esc_html_e('Google Cloud Console öffnen', 'immobilien-rechner-pro'); ?></li>
                    <li><?php esc_html_e('Neues Projekt erstellen oder vorhandenes auswählen', 'immobilien-rechner-pro'); ?></li>
                    <li><?php esc_html_e('APIs aktivieren:', 'immobilien-rechner-pro'); ?>
                        <ul>
                            <li>Maps JavaScript API</li>
                            <li>Places API</li>
                        </ul>
                    </li>
                    <li><?php esc_html_e('API-Key erstellen und hier einfügen', 'immobilien-rechner-pro'); ?></li>
                </ol>
                <p><a href="https://console.cloud.google.com/apis" target="_blank" rel="noopener">console.cloud.google.com</a></p>
            </div>
        </div>

        <div class="irp-settings-section">
            <h2><?php esc_html_e('Datenschutz & Einwilligung', 'immobilien-rechner-pro'); ?></h2>

            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e('Einwilligung erforderlich', 'immobilien-rechner-pro'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="irp_settings[require_consent]" value="1"
                                   <?php checked(!empty($settings['require_consent']), true); ?>>
                            <?php esc_html_e('Nutzer müssen der Datenschutzerklärung zustimmen, bevor sie Daten absenden können', 'immobilien-rechner-pro'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="privacy_policy_url"><?php esc_html_e('Datenschutzerklärung URL', 'immobilien-rechner-pro'); ?></label>
                    </th>
                    <td>
                        <input type="url" id="privacy_policy_url" name="irp_settings[privacy_policy_url]"
                               value="<?php echo esc_url($settings['privacy_policy_url'] ?? get_privacy_policy_url()); ?>" class="regular-text">
                    </td>
                </tr>
            </table>
        </div>

        <?php submit_button(__('Änderungen speichern', 'immobilien-rechner-pro')); ?>
    </form>
</div>
