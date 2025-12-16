<?php
/**
 * Plugin activation handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class IRP_Activator {
    
    public static function activate(): void {
        self::create_tables();
        self::set_default_options();
        flush_rewrite_rules();
    }
    
    private static function create_tables(): void {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        $leads_table = $wpdb->prefix . 'irp_leads';
        $calculations_table = $wpdb->prefix . 'irp_calculations';
        
        $sql_leads = "CREATE TABLE $leads_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(255) DEFAULT NULL,
            email varchar(255) NOT NULL,
            phone varchar(50) DEFAULT NULL,
            mode varchar(20) NOT NULL DEFAULT 'rental',
            property_type varchar(50) DEFAULT NULL,
            property_size decimal(10,2) DEFAULT NULL,
            property_location varchar(255) DEFAULT NULL,
            zip_code varchar(10) DEFAULT NULL,
            calculation_data longtext DEFAULT NULL,
            consent tinyint(1) NOT NULL DEFAULT 0,
            source varchar(100) DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY email (email),
            KEY mode (mode),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        $sql_calculations = "CREATE TABLE $calculations_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            lead_id bigint(20) unsigned DEFAULT NULL,
            session_id varchar(64) NOT NULL,
            mode varchar(20) NOT NULL,
            input_data longtext NOT NULL,
            result_data longtext NOT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY lead_id (lead_id),
            KEY session_id (session_id),
            KEY mode (mode)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_leads);
        dbDelta($sql_calculations);
        
        update_option('irp_db_version', IRP_VERSION);
    }
    
    private static function set_default_options(): void {
        $defaults = [
            'irp_settings' => [
                'primary_color' => '#2563eb',
                'secondary_color' => '#1e40af',
                'company_name' => '',
                'company_logo' => '',
                'company_email' => get_option('admin_email'),
                'default_maintenance_rate' => 1.5,
                'default_vacancy_rate' => 3,
                'default_broker_commission' => 3.57,
                'enable_pdf_export' => false,
                'require_consent' => true,
                'privacy_policy_url' => get_privacy_policy_url(),
            ]
        ];
        
        foreach ($defaults as $option => $value) {
            if (get_option($option) === false) {
                add_option($option, $value);
            }
        }
    }
}
