<?php
/**
 * Propstack CRM Integration
 *
 * Handles all communication with the Propstack API
 */

if (!defined('ABSPATH')) {
    exit;
}

class IRP_Propstack {

    /**
     * API Base URL
     */
    private const API_BASE_URL = 'https://api.propstack.de/v1';

    /**
     * Option name for settings
     */
    private const OPTION_NAME = 'irp_propstack_settings';

    /**
     * Get settings
     *
     * @return array
     */
    public static function get_settings(): array {
        return get_option(self::OPTION_NAME, [
            'enabled' => false,
            'api_key' => '',
            'default_broker_id' => null,
            'city_broker_mapping' => [],
            'contact_source_id' => null,
            'newsletter_enabled' => false,
            'newsletter_snippet_id' => null,
            'newsletter_broker_id' => null,
        ]);
    }

    /**
     * Save settings
     *
     * @param array $settings Settings to save
     * @return bool
     */
    public static function save_settings(array $settings): bool {
        return update_option(self::OPTION_NAME, $settings);
    }

    /**
     * Check if integration is enabled
     *
     * @return bool
     */
    public static function is_enabled(): bool {
        $settings = self::get_settings();
        return !empty($settings['enabled']) && !empty($settings['api_key']);
    }

    /**
     * Get API key
     *
     * @return string
     */
    private static function get_api_key(): string {
        $settings = self::get_settings();
        return $settings['api_key'] ?? '';
    }

    /**
     * Make API request
     *
     * @param string $endpoint API endpoint (e.g., '/brokers')
     * @param string $method HTTP method
     * @param array|null $data Request data for POST/PUT
     * @return array|WP_Error Response data or error
     */
    private static function api_request(string $endpoint, string $method = 'GET', ?array $data = null) {
        $api_key = self::get_api_key();

        if (empty($api_key)) {
            return new \WP_Error('no_api_key', __('Kein API-Key konfiguriert.', 'immobilien-rechner-pro'));
        }

        $url = self::API_BASE_URL . $endpoint;

        $args = [
            'method' => $method,
            'timeout' => 30,
            'headers' => [
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ];

        if ($data !== null && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $args['body'] = wp_json_encode($data);
        }

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            error_log('[IRP Propstack] API Error: ' . $response->get_error_message());
            return $response;
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $decoded = json_decode($body, true);

        if ($status_code >= 400) {
            $error_message = $decoded['message'] ?? $decoded['error'] ?? __('Unbekannter API-Fehler', 'immobilien-rechner-pro');
            error_log('[IRP Propstack] API Error ' . $status_code . ': ' . $error_message);
            return new \WP_Error('api_error', $error_message, ['status' => $status_code]);
        }

        return $decoded;
    }

    /**
     * Test API connection
     *
     * @return array ['success' => bool, 'message' => string]
     */
    public static function test_connection(): array {
        $result = self::api_request('/brokers');

        if (is_wp_error($result)) {
            return [
                'success' => false,
                'message' => $result->get_error_message(),
            ];
        }

        return [
            'success' => true,
            'message' => __('Verbindung erfolgreich!', 'immobilien-rechner-pro'),
        ];
    }

    /**
     * Get all brokers from Propstack
     *
     * @return array|WP_Error List of brokers or error
     */
    public static function get_brokers() {
        // Check if we should use mock data (no API key set)
        if (empty(self::get_api_key())) {
            return self::get_mock_brokers();
        }

        $result = self::api_request('/brokers');

        if (is_wp_error($result)) {
            return $result;
        }

        // Normalize the response
        $brokers = [];
        $data = $result['data'] ?? $result;

        if (is_array($data)) {
            foreach ($data as $broker) {
                $brokers[] = [
                    'id' => $broker['id'] ?? 0,
                    'name' => trim(($broker['first_name'] ?? '') . ' ' . ($broker['last_name'] ?? '')),
                    'email' => $broker['email'] ?? '',
                ];
            }
        }

        return $brokers;
    }

    /**
     * Get mock brokers for testing without API key
     *
     * @return array
     */
    private static function get_mock_brokers(): array {
        return [
            ['id' => 1, 'name' => 'Max Mustermann (Demo)', 'email' => 'max@demo.de'],
            ['id' => 2, 'name' => 'Anna Schmidt (Demo)', 'email' => 'anna@demo.de'],
            ['id' => 3, 'name' => 'Tom Weber (Demo)', 'email' => 'tom@demo.de'],
            ['id' => 4, 'name' => 'Lisa Müller (Demo)', 'email' => 'lisa@demo.de'],
        ];
    }

    /**
     * Get contact sources from Propstack
     *
     * @return array|WP_Error List of contact sources or error
     */
    public static function get_contact_sources() {
        if (empty(self::get_api_key())) {
            return [
                ['id' => 1, 'name' => 'Website (Demo)'],
                ['id' => 2, 'name' => 'Immobilien-Rechner (Demo)'],
            ];
        }

        $result = self::api_request('/contact_sources');

        if (is_wp_error($result)) {
            return $result;
        }

        $sources = [];
        $data = $result['data'] ?? $result;

        if (is_array($data)) {
            foreach ($data as $source) {
                $sources[] = [
                    'id' => $source['id'] ?? 0,
                    'name' => $source['name'] ?? '',
                ];
            }
        }

        return $sources;
    }

    /**
     * Create or update contact in Propstack
     *
     * @param array $lead_data Lead data from WordPress
     * @return int|WP_Error Propstack contact ID or error
     */
    public static function create_contact(array $lead_data) {
        $settings = self::get_settings();

        // Determine broker ID based on city mapping
        $broker_id = self::get_broker_for_city($lead_data['city_id'] ?? '');

        // Build contact data
        $contact_data = [
            'client' => [
                'email' => $lead_data['email'] ?? '',
                'phone' => $lead_data['phone'] ?? '',
                'broker_id' => $broker_id,
                'description' => self::build_description($lead_data),
            ],
        ];

        // Parse name into first/last name
        $name_parts = self::parse_name($lead_data['name'] ?? '');
        $contact_data['client']['first_name'] = $name_parts['first_name'];
        $contact_data['client']['last_name'] = $name_parts['last_name'];

        // Add contact source if configured
        if (!empty($settings['contact_source_id'])) {
            $contact_data['client']['client_source_id'] = (int) $settings['contact_source_id'];
        }

        // Make API request
        $result = self::api_request('/contacts', 'POST', $contact_data);

        if (is_wp_error($result)) {
            return $result;
        }

        // Return the contact ID
        return $result['id'] ?? $result['data']['id'] ?? 0;
    }

    /**
     * Get broker ID for a city
     *
     * @param string $city_id City ID
     * @return int|null Broker ID or null
     */
    public static function get_broker_for_city(string $city_id): ?int {
        $settings = self::get_settings();
        $mapping = $settings['city_broker_mapping'] ?? [];

        // Check if city has assigned broker
        if (!empty($mapping[$city_id])) {
            return (int) $mapping[$city_id];
        }

        // Fallback to default broker
        if (!empty($settings['default_broker_id'])) {
            return (int) $settings['default_broker_id'];
        }

        return null;
    }

    /**
     * Parse full name into first and last name
     *
     * @param string $full_name Full name
     * @return array ['first_name' => string, 'last_name' => string]
     */
    private static function parse_name(string $full_name): array {
        $parts = explode(' ', trim($full_name), 2);

        return [
            'first_name' => $parts[0] ?? '',
            'last_name' => $parts[1] ?? '',
        ];
    }

    /**
     * Build description for Propstack contact
     *
     * @param array $lead_data Lead data
     * @return string
     */
    private static function build_description(array $lead_data): string {
        $calc = $lead_data['calculation_data'] ?? [];
        $result = $calc['result'] ?? [];

        $lines = [
            'Anfrage über Immobilien-Rechner Pro',
            '',
            '--- Berechnungsergebnis ---',
        ];

        // Mode
        $mode = $lead_data['mode'] ?? 'rental';
        $lines[] = 'Modus: ' . ($mode === 'rental' ? 'Mietwertberechnung' : 'Verkaufen vs. Vermieten');

        // Property type
        $types = ['apartment' => 'Wohnung', 'house' => 'Haus', 'commercial' => 'Gewerbe'];
        $property_type = $calc['property_type'] ?? '';
        $lines[] = 'Objekttyp: ' . ($types[$property_type] ?? $property_type);

        // Size
        $size = $calc['size'] ?? $lead_data['property_size'] ?? '';
        if ($size) {
            $lines[] = 'Größe: ' . $size . ' m²';
        }

        // City
        $city = $calc['city_name'] ?? $lead_data['property_location'] ?? '';
        if ($city) {
            $lines[] = 'Stadt: ' . $city;
        }

        // Condition
        $conditions = [
            'new' => 'Neubau',
            'renovated' => 'Renoviert',
            'good' => 'Gut',
            'needs_renovation' => 'Renovierungsbedürftig',
        ];
        $condition = $calc['condition'] ?? '';
        if ($condition) {
            $lines[] = 'Zustand: ' . ($conditions[$condition] ?? $condition);
        }

        // Result
        $lines[] = '';
        if (isset($result['monthly_rent'])) {
            $rent = $result['monthly_rent']['estimate'] ?? $result['monthly_rent'];
            if (is_numeric($rent)) {
                $lines[] = 'Geschätzte Miete: ' . number_format($rent, 0, ',', '.') . ' €/Monat';
            }
        }

        if (isset($result['price_per_sqm'])) {
            $lines[] = 'Preis pro m²: ' . number_format($result['price_per_sqm'], 2, ',', '.') . ' €';
        }

        return implode("\n", $lines);
    }

    /**
     * Send newsletter double opt-in email via Propstack
     *
     * @param int $contact_id Propstack contact ID
     * @param string $email Email address
     * @return bool|WP_Error True on success, error otherwise
     */
    public static function send_newsletter_doi(int $contact_id, string $email) {
        $settings = self::get_settings();

        if (empty($settings['newsletter_enabled'])) {
            return new \WP_Error('newsletter_disabled', __('Newsletter-Integration ist nicht aktiviert.', 'immobilien-rechner-pro'));
        }

        if (empty($settings['newsletter_snippet_id'])) {
            return new \WP_Error('no_snippet', __('Kein Textbaustein für Newsletter-DOI konfiguriert.', 'immobilien-rechner-pro'));
        }

        $broker_id = $settings['newsletter_broker_id'] ?? $settings['default_broker_id'];

        if (empty($broker_id)) {
            return new \WP_Error('no_broker', __('Kein Absender für Newsletter-DOI konfiguriert.', 'immobilien-rechner-pro'));
        }

        $message_data = [
            'message' => [
                'broker_id' => (int) $broker_id,
                'contact_id' => $contact_id,
                'snippet_id' => (int) $settings['newsletter_snippet_id'],
                'to' => $email,
            ],
        ];

        $result = self::api_request('/messages', 'POST', $message_data);

        if (is_wp_error($result)) {
            return $result;
        }

        return true;
    }

    /**
     * Sync a lead to Propstack
     *
     * @param int $lead_id WordPress lead ID
     * @param object|array $lead Lead data (optional, will be fetched if not provided)
     * @return bool|WP_Error True on success, error otherwise
     */
    public static function sync_lead(int $lead_id, $lead = null) {
        if (!self::is_enabled()) {
            return new \WP_Error('not_enabled', __('Propstack-Integration ist nicht aktiviert.', 'immobilien-rechner-pro'));
        }

        // Fetch lead if not provided
        if ($lead === null) {
            $leads = new IRP_Leads();
            $lead = $leads->get($lead_id);
        }

        if (!$lead) {
            return new \WP_Error('lead_not_found', __('Lead nicht gefunden.', 'immobilien-rechner-pro'));
        }

        // Skip if already synced
        if (!empty($lead->propstack_id)) {
            return true;
        }

        // Skip partial leads (no email yet)
        if (empty($lead->email)) {
            return new \WP_Error('incomplete_lead', __('Lead hat noch keine E-Mail-Adresse.', 'immobilien-rechner-pro'));
        }

        // Prepare lead data
        $calculation_data = $lead->calculation_data;
        if (is_string($calculation_data)) {
            $calculation_data = json_decode($calculation_data, true);
        }

        $lead_data = [
            'name' => $lead->name ?? '',
            'email' => $lead->email,
            'phone' => $lead->phone ?? '',
            'mode' => $lead->mode ?? 'rental',
            'city_id' => $calculation_data['city_id'] ?? '',
            'property_size' => $lead->property_size ?? $calculation_data['size'] ?? '',
            'property_location' => $lead->property_location ?? $calculation_data['city_name'] ?? '',
            'calculation_data' => $calculation_data,
        ];

        // Create contact in Propstack
        $propstack_id = self::create_contact($lead_data);

        global $wpdb;
        $table = $wpdb->prefix . 'irp_leads';

        if (is_wp_error($propstack_id)) {
            // Save error
            $wpdb->update(
                $table,
                [
                    'propstack_synced' => 0,
                    'propstack_error' => $propstack_id->get_error_message(),
                    'propstack_synced_at' => current_time('mysql'),
                ],
                ['id' => $lead_id],
                ['%d', '%s', '%s'],
                ['%d']
            );

            error_log('[IRP Propstack] Sync failed for lead ' . $lead_id . ': ' . $propstack_id->get_error_message());
            return $propstack_id;
        }

        // Save success
        $wpdb->update(
            $table,
            [
                'propstack_id' => $propstack_id,
                'propstack_synced' => 1,
                'propstack_error' => null,
                'propstack_synced_at' => current_time('mysql'),
            ],
            ['id' => $lead_id],
            ['%d', '%d', '%s', '%s'],
            ['%d']
        );

        error_log('[IRP Propstack] Lead ' . $lead_id . ' synced successfully. Propstack ID: ' . $propstack_id);

        // Send newsletter DOI if consent given
        $settings = self::get_settings();
        if (!empty($settings['newsletter_enabled']) && !empty($lead->newsletter_consent)) {
            $doi_result = self::send_newsletter_doi($propstack_id, $lead->email);
            if (is_wp_error($doi_result)) {
                error_log('[IRP Propstack] Newsletter DOI failed: ' . $doi_result->get_error_message());
            }
        }

        return true;
    }

    /**
     * Retry sync for a failed lead
     *
     * @param int $lead_id Lead ID
     * @return bool|WP_Error
     */
    public static function retry_sync(int $lead_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'irp_leads';

        // Reset sync status
        $wpdb->update(
            $table,
            [
                'propstack_synced' => 0,
                'propstack_error' => null,
            ],
            ['id' => $lead_id],
            ['%d', '%s'],
            ['%d']
        );

        return self::sync_lead($lead_id);
    }

    /**
     * Get sync status for display
     *
     * @param object $lead Lead object
     * @return array ['status' => string, 'label' => string, 'class' => string]
     */
    public static function get_sync_status($lead): array {
        if (!self::is_enabled()) {
            return [
                'status' => 'disabled',
                'label' => __('Nicht aktiv', 'immobilien-rechner-pro'),
                'class' => 'irp-status-disabled',
                'icon' => '⚫',
            ];
        }

        if (!empty($lead->propstack_id)) {
            return [
                'status' => 'synced',
                'label' => sprintf(__('Synchronisiert (ID: %d)', 'immobilien-rechner-pro'), $lead->propstack_id),
                'class' => 'irp-status-success',
                'icon' => '✅',
            ];
        }

        if (!empty($lead->propstack_error)) {
            return [
                'status' => 'error',
                'label' => $lead->propstack_error,
                'class' => 'irp-status-error',
                'icon' => '❌',
            ];
        }

        if ($lead->status === 'partial') {
            return [
                'status' => 'pending',
                'label' => __('Wartet auf Kontaktdaten', 'immobilien-rechner-pro'),
                'class' => 'irp-status-pending',
                'icon' => '⏳',
            ];
        }

        return [
            'status' => 'pending',
            'label' => __('Ausstehend', 'immobilien-rechner-pro'),
            'class' => 'irp-status-pending',
            'icon' => '⏳',
        ];
    }
}
