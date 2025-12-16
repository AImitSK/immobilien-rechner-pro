<?php
/**
 * Handles loading of frontend and admin assets
 */

if (!defined('ABSPATH')) {
    exit;
}

class IRP_Assets {
    
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'register_frontend_assets']);
    }
    
    public function register_frontend_assets(): void {
        // Only load if shortcode is present (optimization)
        global $post;
        if (!is_a($post, 'WP_Post') || !has_shortcode($post->post_content, 'immobilien_rechner')) {
            return;
        }
        
        $this->enqueue_frontend_assets();
    }
    
    public function enqueue_frontend_assets(): void {
        $asset_file = IRP_PLUGIN_DIR . 'build/index.asset.php';
        
        if (file_exists($asset_file)) {
            $assets = include $asset_file;
            $dependencies = $assets['dependencies'] ?? [];
            $version = $assets['version'] ?? IRP_VERSION;
        } else {
            $dependencies = ['wp-element', 'wp-components', 'wp-i18n'];
            $version = IRP_VERSION;
        }
        
        wp_enqueue_script(
            'irp-calculator',
            IRP_PLUGIN_URL . 'build/index.js',
            $dependencies,
            $version,
            true
        );
        
        wp_enqueue_style(
            'irp-calculator',
            IRP_PLUGIN_URL . 'build/index.css',
            [],
            $version
        );
        
        // Pass settings to JavaScript
        $settings = get_option('irp_settings', []);
        
        wp_localize_script('irp-calculator', 'irpSettings', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'restUrl' => rest_url('irp/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'pluginUrl' => IRP_PLUGIN_URL,
            'settings' => [
                'primaryColor' => $settings['primary_color'] ?? '#2563eb',
                'secondaryColor' => $settings['secondary_color'] ?? '#1e40af',
                'companyName' => $settings['company_name'] ?? '',
                'companyLogo' => $settings['company_logo'] ?? '',
                'requireConsent' => $settings['require_consent'] ?? true,
                'privacyPolicyUrl' => $settings['privacy_policy_url'] ?? '',
                'defaultMaintenanceRate' => $settings['default_maintenance_rate'] ?? 1.5,
                'defaultVacancyRate' => $settings['default_vacancy_rate'] ?? 3,
            ],
            'i18n' => [
                'currency' => '€',
                'locale' => get_locale(),
            ]
        ]);
    }
}
