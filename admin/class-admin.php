<?php
/**
 * Admin panel functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class IRP_Admin {
    
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('wp_ajax_irp_export_leads', [$this, 'ajax_export_leads']);
    }
    
    public function add_admin_menu(): void {
        add_menu_page(
            __('Immobilien Rechner', 'immobilien-rechner-pro'),
            __('Immo Rechner', 'immobilien-rechner-pro'),
            'manage_options',
            'immobilien-rechner',
            [$this, 'render_dashboard'],
            'dashicons-calculator',
            30
        );
        
        add_submenu_page(
            'immobilien-rechner',
            __('Dashboard', 'immobilien-rechner-pro'),
            __('Dashboard', 'immobilien-rechner-pro'),
            'manage_options',
            'immobilien-rechner',
            [$this, 'render_dashboard']
        );
        
        add_submenu_page(
            'immobilien-rechner',
            __('Leads', 'immobilien-rechner-pro'),
            __('Leads', 'immobilien-rechner-pro'),
            'manage_options',
            'irp-leads',
            [$this, 'render_leads']
        );
        
        add_submenu_page(
            'immobilien-rechner',
            __('Matrix & Daten', 'immobilien-rechner-pro'),
            __('Matrix & Daten', 'immobilien-rechner-pro'),
            'manage_options',
            'irp-matrix',
            [$this, 'render_matrix']
        );

        add_submenu_page(
            'immobilien-rechner',
            __('Settings', 'immobilien-rechner-pro'),
            __('Settings', 'immobilien-rechner-pro'),
            'manage_options',
            'irp-settings',
            [$this, 'render_settings']
        );
    }
    
    public function register_settings(): void {
        register_setting('irp_settings_group', 'irp_settings', [
            'type' => 'array',
            'sanitize_callback' => [$this, 'sanitize_settings'],
        ]);

        register_setting('irp_matrix_group', 'irp_price_matrix', [
            'type' => 'array',
            'sanitize_callback' => [$this, 'sanitize_price_matrix'],
        ]);
    }

    public function sanitize_price_matrix(array $input): array {
        $sanitized = [];

        // Regional base prices
        if (isset($input['base_prices']) && is_array($input['base_prices'])) {
            foreach ($input['base_prices'] as $region => $price) {
                $sanitized['base_prices'][sanitize_text_field($region)] = (float) $price;
            }
        }

        // Condition multipliers
        if (isset($input['condition_multipliers']) && is_array($input['condition_multipliers'])) {
            foreach ($input['condition_multipliers'] as $condition => $multiplier) {
                $sanitized['condition_multipliers'][sanitize_text_field($condition)] = (float) $multiplier;
            }
        }

        // Property type multipliers
        if (isset($input['type_multipliers']) && is_array($input['type_multipliers'])) {
            foreach ($input['type_multipliers'] as $type => $multiplier) {
                $sanitized['type_multipliers'][sanitize_text_field($type)] = (float) $multiplier;
            }
        }

        // Feature premiums
        if (isset($input['feature_premiums']) && is_array($input['feature_premiums'])) {
            foreach ($input['feature_premiums'] as $feature => $premium) {
                $sanitized['feature_premiums'][sanitize_text_field($feature)] = (float) $premium;
            }
        }

        // Sale factors (Vervielfältiger)
        if (isset($input['sale_factors']) && is_array($input['sale_factors'])) {
            foreach ($input['sale_factors'] as $region => $factor) {
                $sanitized['sale_factors'][sanitize_text_field($region)] = (float) $factor;
            }
        }

        // Global calculation parameters
        $sanitized['interest_rate'] = (float) ($input['interest_rate'] ?? 3.0);
        $sanitized['appreciation_rate'] = (float) ($input['appreciation_rate'] ?? 2.0);
        $sanitized['rent_increase_rate'] = (float) ($input['rent_increase_rate'] ?? 2.0);

        return $sanitized;
    }
    
    public function sanitize_settings(array $input): array {
        $sanitized = [];
        
        $sanitized['primary_color'] = sanitize_hex_color($input['primary_color'] ?? '#2563eb');
        $sanitized['secondary_color'] = sanitize_hex_color($input['secondary_color'] ?? '#1e40af');
        $sanitized['company_name'] = sanitize_text_field($input['company_name'] ?? '');
        $sanitized['company_logo'] = esc_url_raw($input['company_logo'] ?? '');
        $sanitized['company_email'] = sanitize_email($input['company_email'] ?? get_option('admin_email'));
        $sanitized['default_maintenance_rate'] = (float) ($input['default_maintenance_rate'] ?? 1.5);
        $sanitized['default_vacancy_rate'] = (float) ($input['default_vacancy_rate'] ?? 3);
        $sanitized['default_broker_commission'] = (float) ($input['default_broker_commission'] ?? 3.57);
        $sanitized['enable_pdf_export'] = !empty($input['enable_pdf_export']);
        $sanitized['require_consent'] = !empty($input['require_consent']);
        $sanitized['privacy_policy_url'] = esc_url_raw($input['privacy_policy_url'] ?? '');
        
        return $sanitized;
    }
    
    public function enqueue_admin_assets(string $hook): void {
        if (strpos($hook, 'immobilien-rechner') === false && strpos($hook, 'irp-') === false) {
            return;
        }
        
        wp_enqueue_style(
            'irp-admin',
            IRP_PLUGIN_URL . 'admin/css/admin.css',
            [],
            IRP_VERSION
        );
        
        wp_enqueue_script(
            'irp-admin',
            IRP_PLUGIN_URL . 'admin/js/admin.js',
            ['jquery'],
            IRP_VERSION,
            true
        );
        
        // Media uploader for logo
        if (strpos($hook, 'irp-settings') !== false) {
            wp_enqueue_media();
        }
    }
    
    public function render_dashboard(): void {
        global $wpdb;
        
        $leads_table = $wpdb->prefix . 'irp_leads';
        $calculations_table = $wpdb->prefix . 'irp_calculations';
        
        // Get statistics
        $total_leads = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$leads_table}");
        $leads_this_month = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$leads_table} WHERE created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')"
        );
        $total_calculations = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$calculations_table}");
        $rental_calculations = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$calculations_table} WHERE mode = 'rental'"
        );
        
        // Recent leads
        $recent_leads = $wpdb->get_results(
            "SELECT * FROM {$leads_table} ORDER BY created_at DESC LIMIT 5"
        );
        
        include IRP_PLUGIN_DIR . 'admin/views/dashboard.php';
    }
    
    public function render_leads(): void {
        $leads_manager = new IRP_Leads();
        
        // Handle single lead view
        if (isset($_GET['lead'])) {
            $lead = $leads_manager->get((int) $_GET['lead']);
            include IRP_PLUGIN_DIR . 'admin/views/lead-detail.php';
            return;
        }
        
        // Handle delete action
        if (isset($_POST['action']) && $_POST['action'] === 'delete_lead' && wp_verify_nonce($_POST['_wpnonce'], 'irp_delete_lead')) {
            $leads_manager->delete((int) $_POST['lead_id']);
            echo '<div class="notice notice-success"><p>' . esc_html__('Lead deleted.', 'immobilien-rechner-pro') . '</p></div>';
        }
        
        // Get filtered leads
        $args = [
            'page' => (int) ($_GET['paged'] ?? 1),
            'mode' => sanitize_text_field($_GET['mode'] ?? ''),
            'search' => sanitize_text_field($_GET['s'] ?? ''),
        ];
        
        $leads = $leads_manager->get_all($args);
        
        include IRP_PLUGIN_DIR . 'admin/views/leads-list.php';
    }
    
    public function render_matrix(): void {
        $matrix = get_option('irp_price_matrix', $this->get_default_matrix());
        include IRP_PLUGIN_DIR . 'admin/views/matrix.php';
    }

    public function get_default_matrix(): array {
        return [
            'base_prices' => [
                '1' => 18.50,  // Berlin
                '2' => 16.00,  // Hamburg
                '3' => 11.50,  // Hannover
                '4' => 11.00,  // Düsseldorf
                '5' => 11.50,  // Köln/Bonn
                '6' => 13.50,  // Frankfurt
                '7' => 13.00,  // Stuttgart
                '8' => 19.00,  // München
                '9' => 10.00,  // Nürnberg
                '0' => 10.50,  // Leipzig/Dresden
            ],
            'condition_multipliers' => [
                'new' => 1.25,
                'renovated' => 1.10,
                'good' => 1.00,
                'needs_renovation' => 0.80,
            ],
            'type_multipliers' => [
                'apartment' => 1.00,
                'house' => 1.15,
                'commercial' => 0.85,
            ],
            'feature_premiums' => [
                'balcony' => 0.50,
                'terrace' => 0.75,
                'garden' => 1.00,
                'elevator' => 0.30,
                'parking' => 0.40,
                'garage' => 0.60,
                'cellar' => 0.20,
                'fitted_kitchen' => 0.50,
                'floor_heating' => 0.40,
                'guest_toilet' => 0.25,
                'barrier_free' => 0.30,
            ],
            'sale_factors' => [
                '1' => 30,  // Berlin
                '2' => 28,  // Hamburg
                '3' => 22,  // Hannover
                '4' => 23,  // Düsseldorf
                '5' => 24,  // Köln/Bonn
                '6' => 27,  // Frankfurt
                '7' => 26,  // Stuttgart
                '8' => 35,  // München
                '9' => 20,  // Nürnberg
                '0' => 21,  // Leipzig/Dresden
            ],
            'interest_rate' => 3.0,
            'appreciation_rate' => 2.0,
            'rent_increase_rate' => 2.0,
        ];
    }

    public function render_settings(): void {
        $settings = get_option('irp_settings', []);
        include IRP_PLUGIN_DIR . 'admin/views/settings.php';
    }
    
    public function ajax_export_leads(): void {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        check_ajax_referer('irp_export_leads', 'nonce');
        
        $leads_manager = new IRP_Leads();
        $csv = $leads_manager->export_csv([
            'mode' => sanitize_text_field($_POST['mode'] ?? ''),
            'date_from' => sanitize_text_field($_POST['date_from'] ?? ''),
            'date_to' => sanitize_text_field($_POST['date_to'] ?? ''),
        ]);
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="leads-export-' . date('Y-m-d') . '.csv"');
        
        echo $csv;
        wp_die();
    }
}
