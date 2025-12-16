<?php
/**
 * Admin Settings View
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap irp-admin-wrap">
    <h1><?php esc_html_e('Settings', 'immobilien-rechner-pro'); ?></h1>
    
    <form method="post" action="options.php">
        <?php settings_fields('irp_settings_group'); ?>
        
        <div class="irp-settings-section">
            <h2><?php esc_html_e('Branding', 'immobilien-rechner-pro'); ?></h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="company_name"><?php esc_html_e('Company Name', 'immobilien-rechner-pro'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="company_name" name="irp_settings[company_name]" 
                               value="<?php echo esc_attr($settings['company_name'] ?? ''); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="company_logo"><?php esc_html_e('Company Logo', 'immobilien-rechner-pro'); ?></label>
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
                            <?php esc_html_e('Upload Logo', 'immobilien-rechner-pro'); ?>
                        </button>
                        <button type="button" class="button irp-remove-logo" <?php echo empty($settings['company_logo']) ? 'style="display:none"' : ''; ?>>
                            <?php esc_html_e('Remove', 'immobilien-rechner-pro'); ?>
                        </button>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="primary_color"><?php esc_html_e('Primary Color', 'immobilien-rechner-pro'); ?></label>
                    </th>
                    <td>
                        <input type="color" id="primary_color" name="irp_settings[primary_color]" 
                               value="<?php echo esc_attr($settings['primary_color'] ?? '#2563eb'); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="secondary_color"><?php esc_html_e('Secondary Color', 'immobilien-rechner-pro'); ?></label>
                    </th>
                    <td>
                        <input type="color" id="secondary_color" name="irp_settings[secondary_color]" 
                               value="<?php echo esc_attr($settings['secondary_color'] ?? '#1e40af'); ?>">
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="irp-settings-section">
            <h2><?php esc_html_e('Contact & Notifications', 'immobilien-rechner-pro'); ?></h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="company_email"><?php esc_html_e('Notification Email', 'immobilien-rechner-pro'); ?></label>
                    </th>
                    <td>
                        <input type="email" id="company_email" name="irp_settings[company_email]" 
                               value="<?php echo esc_attr($settings['company_email'] ?? get_option('admin_email')); ?>" class="regular-text">
                        <p class="description"><?php esc_html_e('New lead notifications will be sent to this email.', 'immobilien-rechner-pro'); ?></p>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="irp-settings-section">
            <h2><?php esc_html_e('Calculator Defaults', 'immobilien-rechner-pro'); ?></h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="default_maintenance_rate"><?php esc_html_e('Maintenance Rate (%)', 'immobilien-rechner-pro'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="default_maintenance_rate" name="irp_settings[default_maintenance_rate]" 
                               value="<?php echo esc_attr($settings['default_maintenance_rate'] ?? 1.5); ?>" 
                               step="0.1" min="0" max="10" class="small-text"> %
                        <p class="description"><?php esc_html_e('Annual maintenance cost as percentage of property value.', 'immobilien-rechner-pro'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="default_vacancy_rate"><?php esc_html_e('Vacancy Rate (%)', 'immobilien-rechner-pro'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="default_vacancy_rate" name="irp_settings[default_vacancy_rate]" 
                               value="<?php echo esc_attr($settings['default_vacancy_rate'] ?? 3); ?>" 
                               step="0.5" min="0" max="20" class="small-text"> %
                        <p class="description"><?php esc_html_e('Expected vacancy rate for rental income calculation.', 'immobilien-rechner-pro'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="default_broker_commission"><?php esc_html_e('Broker Commission (%)', 'immobilien-rechner-pro'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="default_broker_commission" name="irp_settings[default_broker_commission]" 
                               value="<?php echo esc_attr($settings['default_broker_commission'] ?? 3.57); ?>" 
                               step="0.01" min="0" max="10" class="small-text"> %
                        <p class="description"><?php esc_html_e('Default broker commission for sale calculations.', 'immobilien-rechner-pro'); ?></p>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="irp-settings-section">
            <h2><?php esc_html_e('Privacy & Consent', 'immobilien-rechner-pro'); ?></h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e('Require Consent', 'immobilien-rechner-pro'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="irp_settings[require_consent]" value="1" 
                                   <?php checked(!empty($settings['require_consent']), true); ?>>
                            <?php esc_html_e('Require users to agree to privacy policy before submitting', 'immobilien-rechner-pro'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="privacy_policy_url"><?php esc_html_e('Privacy Policy URL', 'immobilien-rechner-pro'); ?></label>
                    </th>
                    <td>
                        <input type="url" id="privacy_policy_url" name="irp_settings[privacy_policy_url]" 
                               value="<?php echo esc_url($settings['privacy_policy_url'] ?? get_privacy_policy_url()); ?>" class="regular-text">
                    </td>
                </tr>
            </table>
        </div>
        
        <?php submit_button(); ?>
    </form>
</div>
