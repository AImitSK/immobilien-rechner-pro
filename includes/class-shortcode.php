<?php
/**
 * Shortcode handler for embedding the calculator
 */

if (!defined('ABSPATH')) {
    exit;
}

class IRP_Shortcode {
    
    public function __construct() {
        add_shortcode('immobilien_rechner', [$this, 'render_calculator']);
    }
    
    /**
     * Render the calculator shortcode
     * 
     * Usage: [immobilien_rechner mode="rental"] or [immobilien_rechner mode="comparison"]
     * 
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function render_calculator(array $atts = []): string {
        $atts = shortcode_atts([
            'mode' => '', // Empty means user can choose, 'rental' or 'comparison' locks to that mode
            'theme' => 'light',
            'show_branding' => 'true',
        ], $atts, 'immobilien_rechner');
        
        // Generate unique ID for multiple instances on same page
        $instance_id = 'irp-' . wp_generate_uuid4();
        
        // Build data attributes for React
        $data_attrs = [
            'data-instance-id' => $instance_id,
            'data-initial-mode' => esc_attr($atts['mode']),
            'data-theme' => esc_attr($atts['theme']),
            'data-show-branding' => esc_attr($atts['show_branding']),
        ];
        
        $data_string = '';
        foreach ($data_attrs as $key => $value) {
            $data_string .= sprintf(' %s="%s"', $key, $value);
        }
        
        // Ensure assets are loaded
        $assets = new IRP_Assets();
        $assets->enqueue_frontend_assets();
        
        return sprintf(
            '<div id="%s" class="irp-calculator-root"%s>
                <div class="irp-loading">
                    <div class="irp-loading-spinner"></div>
                    <p>%s</p>
                </div>
            </div>',
            esc_attr($instance_id),
            $data_string,
            esc_html__('Loading calculator...', 'immobilien-rechner-pro')
        );
    }
}
