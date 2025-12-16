<?php
/**
 * Lead management functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class IRP_Leads {
    
    private string $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'irp_leads';
    }
    
    /**
     * Create a new lead
     */
    public function create(array $data): int|\WP_Error {
        global $wpdb;
        
        // Validate email
        if (!is_email($data['email'])) {
            return new \WP_Error('invalid_email', __('Bitte geben Sie eine gültige E-Mail-Adresse an.', 'immobilien-rechner-pro'));
        }
        
        $insert_data = [
            'name' => sanitize_text_field($data['name'] ?? ''),
            'email' => sanitize_email($data['email']),
            'phone' => sanitize_text_field($data['phone'] ?? ''),
            'mode' => sanitize_text_field($data['mode']),
            'property_type' => sanitize_text_field($data['calculation_data']['property_type'] ?? ''),
            'property_size' => (float) ($data['calculation_data']['size'] ?? 0),
            'property_location' => sanitize_text_field($data['calculation_data']['location'] ?? ''),
            'zip_code' => sanitize_text_field($data['calculation_data']['zip_code'] ?? ''),
            'calculation_data' => wp_json_encode($data['calculation_data'] ?? []),
            'consent' => (int) $data['consent'],
            'source' => sanitize_text_field($data['source'] ?? 'calculator'),
        ];
        
        $result = $wpdb->insert(
            $this->table_name,
            $insert_data,
            ['%s', '%s', '%s', '%s', '%s', '%f', '%s', '%s', '%s', '%d', '%s']
        );
        
        if ($result === false) {
            return new \WP_Error('db_error', __('Lead-Daten konnten nicht gespeichert werden.', 'immobilien-rechner-pro'));
        }
        
        return $wpdb->insert_id;
    }
    
    /**
     * Get a single lead by ID
     */
    public function get(int $id): ?object {
        global $wpdb;
        
        $lead = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $id)
        );
        
        if ($lead && $lead->calculation_data) {
            $lead->calculation_data = json_decode($lead->calculation_data);
        }
        
        return $lead;
    }
    
    /**
     * Get all leads with optional filtering and pagination
     */
    public function get_all(array $args = []): array {
        global $wpdb;
        
        $defaults = [
            'per_page' => 20,
            'page' => 1,
            'mode' => '',
            'search' => '',
            'date_from' => '',
            'date_to' => '',
            'orderby' => 'created_at',
            'order' => 'DESC',
        ];
        
        $args = wp_parse_args($args, $defaults);
        
        $where = ['1=1'];
        $values = [];
        
        if (!empty($args['mode'])) {
            $where[] = 'mode = %s';
            $values[] = $args['mode'];
        }
        
        if (!empty($args['search'])) {
            $where[] = '(name LIKE %s OR email LIKE %s OR property_location LIKE %s)';
            $search_term = '%' . $wpdb->esc_like($args['search']) . '%';
            $values[] = $search_term;
            $values[] = $search_term;
            $values[] = $search_term;
        }
        
        if (!empty($args['date_from'])) {
            $where[] = 'created_at >= %s';
            $values[] = $args['date_from'] . ' 00:00:00';
        }
        
        if (!empty($args['date_to'])) {
            $where[] = 'created_at <= %s';
            $values[] = $args['date_to'] . ' 23:59:59';
        }
        
        $where_clause = implode(' AND ', $where);
        
        // Sanitize orderby and order
        $allowed_orderby = ['id', 'name', 'email', 'mode', 'created_at'];
        $orderby = in_array($args['orderby'], $allowed_orderby) ? $args['orderby'] : 'created_at';
        $order = strtoupper($args['order']) === 'ASC' ? 'ASC' : 'DESC';
        
        $offset = ($args['page'] - 1) * $args['per_page'];
        
        // Get total count
        $count_sql = "SELECT COUNT(*) FROM {$this->table_name} WHERE {$where_clause}";
        if (!empty($values)) {
            $count_sql = $wpdb->prepare($count_sql, $values);
        }
        $total = (int) $wpdb->get_var($count_sql);
        
        // Get results
        $sql = "SELECT * FROM {$this->table_name} WHERE {$where_clause} ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d";
        $values[] = $args['per_page'];
        $values[] = $offset;
        
        $results = $wpdb->get_results($wpdb->prepare($sql, $values));
        
        // Decode JSON data
        foreach ($results as &$lead) {
            if ($lead->calculation_data) {
                $lead->calculation_data = json_decode($lead->calculation_data);
            }
        }
        
        return [
            'items' => $results,
            'total' => $total,
            'pages' => ceil($total / $args['per_page']),
            'current_page' => $args['page'],
        ];
    }
    
    /**
     * Delete a lead
     */
    public function delete(int $id): bool {
        global $wpdb;
        
        return (bool) $wpdb->delete($this->table_name, ['id' => $id], ['%d']);
    }
    
    /**
     * Send notification email to admin
     */
    public function send_notification(int $lead_id): bool {
        $lead = $this->get($lead_id);
        
        if (!$lead) {
            return false;
        }
        
        $settings = get_option('irp_settings', []);
        $to = $settings['company_email'] ?? get_option('admin_email');
        
        $type_labels = [
            'apartment' => __('Wohnung', 'immobilien-rechner-pro'),
            'house' => __('Haus', 'immobilien-rechner-pro'),
            'commercial' => __('Gewerbe', 'immobilien-rechner-pro'),
        ];

        $mode_label = $lead->mode === 'rental'
            ? __('Mietwert-Berechnung', 'immobilien-rechner-pro')
            : __('Verkauf vs. Vermietung', 'immobilien-rechner-pro');

        $subject = sprintf(
            __('[Neuer Lead] %s - %s', 'immobilien-rechner-pro'),
            $mode_label,
            $lead->property_location ?: $lead->zip_code
        );

        $property_type_label = $type_labels[$lead->property_type] ?? ucfirst($lead->property_type ?: '-');

        $message = sprintf(
            __("Neuer Lead vom Immobilien Rechner Pro:\n\n" .
               "Name: %s\n" .
               "E-Mail: %s\n" .
               "Telefon: %s\n\n" .
               "Modus: %s\n" .
               "Objekttyp: %s\n" .
               "Größe: %s m²\n" .
               "Standort: %s %s\n\n" .
               "Im Admin ansehen: %s",
            'immobilien-rechner-pro'),
            $lead->name ?: '-',
            $lead->email,
            $lead->phone ?: '-',
            $mode_label,
            $property_type_label,
            $lead->property_size ?: '-',
            $lead->zip_code,
            $lead->property_location,
            admin_url('admin.php?page=irp-leads&lead=' . $lead_id)
        );
        
        $headers = ['Content-Type: text/plain; charset=UTF-8'];
        
        if (!empty($settings['company_name'])) {
            $headers[] = 'From: ' . $settings['company_name'] . ' <' . $to . '>';
        }
        
        return wp_mail($to, $subject, $message, $headers);
    }
    
    /**
     * Export leads to CSV
     */
    public function export_csv(array $args = []): string {
        $leads = $this->get_all(array_merge($args, ['per_page' => 9999]));
        
        $output = fopen('php://temp', 'r+');
        
        // Header row
        fputcsv($output, [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Mode',
            'Property Type',
            'Size (m²)',
            'ZIP Code',
            'Location',
            'Created At',
        ]);
        
        // Data rows
        foreach ($leads['items'] as $lead) {
            fputcsv($output, [
                $lead->id,
                $lead->name,
                $lead->email,
                $lead->phone,
                $lead->mode,
                $lead->property_type,
                $lead->property_size,
                $lead->zip_code,
                $lead->property_location,
                $lead->created_at,
            ]);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }
}
