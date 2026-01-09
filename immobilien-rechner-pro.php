<?php
/**
 * Plugin Name: Immobilien Rechner Pro
 * Plugin URI: https://example.com/immobilien-rechner-pro
 * Description: Professional real estate calculator for rental value estimation and sell vs. rent comparison. White-label solution for real estate agents.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: immobilien-rechner-pro
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('IRP_VERSION', '1.0.0');
define('IRP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('IRP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('IRP_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main plugin class
 */
final class Immobilien_Rechner_Pro {
    
    private static ?Immobilien_Rechner_Pro $instance = null;
    
    public static function instance(): Immobilien_Rechner_Pro {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    private function load_dependencies(): void {
        require_once IRP_PLUGIN_DIR . 'includes/class-activator.php';
        require_once IRP_PLUGIN_DIR . 'includes/class-deactivator.php';
        require_once IRP_PLUGIN_DIR . 'includes/class-assets.php';
        require_once IRP_PLUGIN_DIR . 'includes/class-shortcode.php';
        require_once IRP_PLUGIN_DIR . 'includes/class-rest-api.php';
        require_once IRP_PLUGIN_DIR . 'includes/class-calculator.php';
        require_once IRP_PLUGIN_DIR . 'includes/class-leads.php';
        require_once IRP_PLUGIN_DIR . 'admin/class-admin.php';
    }
    
    private function init_hooks(): void {
        register_activation_hook(__FILE__, ['IRP_Activator', 'activate']);
        register_deactivation_hook(__FILE__, ['IRP_Deactivator', 'deactivate']);
        
        add_action('init', [$this, 'load_textdomain']);
        add_action('init', [$this, 'init_classes']);
    }
    
    public function load_textdomain(): void {
        load_plugin_textdomain(
            'immobilien-rechner-pro',
            false,
            dirname(IRP_PLUGIN_BASENAME) . '/languages'
        );
    }
    
    public function init_classes(): void {
        new IRP_Assets();
        new IRP_Shortcode();
        new IRP_Rest_API();
        
        if (is_admin()) {
            new IRP_Admin();
        }
    }
}

/**
 * Initialize plugin
 */
function immobilien_rechner_pro(): Immobilien_Rechner_Pro {
    return Immobilien_Rechner_Pro::instance();
}

// Start the plugin
immobilien_rechner_pro();
