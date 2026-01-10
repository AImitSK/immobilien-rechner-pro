<?php
/**
 * Integrations admin view
 */

if (!defined('ABSPATH')) {
    exit;
}

$active_tab = sanitize_key($_GET['tab'] ?? 'connection');

// Get brokers from cache or API
$cached_brokers = get_transient('irp_propstack_brokers');
$brokers = $cached_brokers ? $cached_brokers : [];
$api_key = $propstack_settings['api_key'] ?? '';
$is_connected = !empty($api_key) && !empty($brokers);
?>

<div class="wrap irp-admin-wrap">
    <h1><?php esc_html_e('Integrationen', 'immobilien-rechner-pro'); ?></h1>

    <!-- Tabs Navigation -->
    <nav class="nav-tab-wrapper">
        <a href="<?php echo esc_url(add_query_arg('tab', 'connection')); ?>"
           class="nav-tab <?php echo $active_tab === 'connection' ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e('Propstack Verbindung', 'immobilien-rechner-pro'); ?>
        </a>
        <a href="<?php echo esc_url(add_query_arg('tab', 'broker-mapping')); ?>"
           class="nav-tab <?php echo $active_tab === 'broker-mapping' ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e('Makler-Zuweisung', 'immobilien-rechner-pro'); ?>
        </a>
        <a href="<?php echo esc_url(add_query_arg('tab', 'newsletter')); ?>"
           class="nav-tab <?php echo $active_tab === 'newsletter' ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e('Newsletter', 'immobilien-rechner-pro'); ?>
        </a>
    </nav>

    <div class="irp-tab-content" style="background: #fff; padding: 20px; margin-top: 0; border: 1px solid #c3c4c7; border-top: none;">

        <?php if ($active_tab === 'connection'): ?>
            <!-- Connection Tab -->
            <div id="propstack-connection">
                <h2><?php esc_html_e('Propstack CRM Verbindung', 'immobilien-rechner-pro'); ?></h2>
                <p class="description">
                    <?php esc_html_e('Verbinden Sie Ihr Propstack CRM, um Leads automatisch zu synchronisieren.', 'immobilien-rechner-pro'); ?>
                </p>

                <!-- Connection Status -->
                <div class="irp-connection-status" style="margin: 20px 0; padding: 15px; border-radius: 4px; <?php echo $is_connected ? 'background: #d4edda; border: 1px solid #c3e6cb;' : 'background: #f8f9fa; border: 1px solid #dee2e6;'; ?>">
                    <?php if ($is_connected): ?>
                        <span class="dashicons dashicons-yes-alt" style="color: #28a745;"></span>
                        <strong style="color: #28a745;"><?php esc_html_e('Verbunden', 'immobilien-rechner-pro'); ?></strong>
                        <span style="margin-left: 10px;">
                            <?php printf(
                                esc_html__('%d Makler verfügbar', 'immobilien-rechner-pro'),
                                count($brokers)
                            ); ?>
                        </span>
                    <?php else: ?>
                        <span class="dashicons dashicons-warning" style="color: #6c757d;"></span>
                        <strong style="color: #6c757d;"><?php esc_html_e('Nicht verbunden', 'immobilien-rechner-pro'); ?></strong>
                    <?php endif; ?>
                </div>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="propstack_enabled"><?php esc_html_e('Integration aktivieren', 'immobilien-rechner-pro'); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox"
                                       id="propstack_enabled"
                                       name="propstack_enabled"
                                       value="1"
                                       <?php checked(!empty($propstack_settings['enabled'])); ?>>
                                <?php esc_html_e('Propstack-Synchronisierung aktivieren', 'immobilien-rechner-pro'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="propstack_api_key"><?php esc_html_e('API Key', 'immobilien-rechner-pro'); ?></label>
                        </th>
                        <td>
                            <input type="password"
                                   id="propstack_api_key"
                                   name="propstack_api_key"
                                   value="<?php echo esc_attr($api_key); ?>"
                                   class="regular-text"
                                   autocomplete="off">
                            <button type="button" class="button" id="toggle-api-key">
                                <span class="dashicons dashicons-visibility" style="vertical-align: middle;"></span>
                            </button>
                            <p class="description">
                                <?php esc_html_e('Den API Key finden Sie in Ihrem Propstack Account unter Einstellungen > API.', 'immobilien-rechner-pro'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Verbindung testen', 'immobilien-rechner-pro'); ?></th>
                        <td>
                            <button type="button" class="button button-secondary" id="test-propstack-connection">
                                <span class="dashicons dashicons-update" style="vertical-align: middle;"></span>
                                <?php esc_html_e('Verbindung testen', 'immobilien-rechner-pro'); ?>
                            </button>
                            <span id="connection-test-result" style="margin-left: 10px;"></span>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="button" class="button button-primary" id="save-propstack-connection">
                        <?php esc_html_e('Speichern', 'immobilien-rechner-pro'); ?>
                    </button>
                </p>

                <!-- Demo Mode Notice -->
                <?php if (empty($api_key)): ?>
                <div class="notice notice-info" style="margin-top: 20px;">
                    <p>
                        <strong><?php esc_html_e('Demo-Modus:', 'immobilien-rechner-pro'); ?></strong>
                        <?php esc_html_e('Ohne API Key werden Demo-Daten verwendet. Die Makler-Zuweisung kann trotzdem konfiguriert werden.', 'immobilien-rechner-pro'); ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>

        <?php elseif ($active_tab === 'broker-mapping'): ?>
            <!-- Broker Mapping Tab -->
            <div id="broker-mapping">
                <h2><?php esc_html_e('Makler-Zuweisung nach Stadt', 'immobilien-rechner-pro'); ?></h2>
                <p class="description">
                    <?php esc_html_e('Weisen Sie jedem Standort einen verantwortlichen Makler zu. Neue Leads werden automatisch dem entsprechenden Makler zugeordnet.', 'immobilien-rechner-pro'); ?>
                </p>

                <?php if (empty($cities)): ?>
                    <div class="notice notice-warning">
                        <p>
                            <?php esc_html_e('Keine Städte konfiguriert. Bitte fügen Sie zuerst Städte in der Matrix & Daten Seite hinzu.', 'immobilien-rechner-pro'); ?>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=irp-matrix')); ?>"><?php esc_html_e('Zur Matrix', 'immobilien-rechner-pro'); ?></a>
                        </p>
                    </div>
                <?php else: ?>
                    <?php
                    // Get brokers - use cached or fetch
                    if (empty($brokers)) {
                        $brokers = IRP_Propstack::get_brokers();
                    }
                    $broker_mapping = $propstack_settings['city_broker_mapping'] ?? [];
                    ?>

                    <table class="widefat" style="margin-top: 20px;">
                        <thead>
                            <tr>
                                <th style="width: 40%;"><?php esc_html_e('Stadt / Region', 'immobilien-rechner-pro'); ?></th>
                                <th style="width: 50%;"><?php esc_html_e('Zuständiger Makler', 'immobilien-rechner-pro'); ?></th>
                                <th style="width: 10%;"><?php esc_html_e('Status', 'immobilien-rechner-pro'); ?></th>
                            </tr>
                        </thead>
                        <tbody id="broker-mapping-table">
                            <?php foreach ($cities as $city): ?>
                                <?php $city_id = $city['id'] ?? ''; ?>
                                <tr>
                                    <td>
                                        <strong><?php echo esc_html($city['name'] ?? ''); ?></strong>
                                    </td>
                                    <td>
                                        <select name="broker_mapping[<?php echo esc_attr($city_id); ?>]"
                                                class="broker-select"
                                                style="width: 100%; max-width: 400px;">
                                            <option value=""><?php esc_html_e('-- Kein Makler zugewiesen --', 'immobilien-rechner-pro'); ?></option>
                                            <?php foreach ($brokers as $broker): ?>
                                                <option value="<?php echo esc_attr($broker['id']); ?>"
                                                        <?php selected($broker_mapping[$city_id] ?? '', $broker['id']); ?>>
                                                    <?php echo esc_html($broker['name']); ?>
                                                    <?php if (!empty($broker['email'])): ?>
                                                        (<?php echo esc_html($broker['email']); ?>)
                                                    <?php endif; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <?php if (!empty($broker_mapping[$city_id])): ?>
                                            <span class="dashicons dashicons-yes" style="color: #28a745;" title="<?php esc_attr_e('Zugewiesen', 'immobilien-rechner-pro'); ?>"></span>
                                        <?php else: ?>
                                            <span class="dashicons dashicons-minus" style="color: #6c757d;" title="<?php esc_attr_e('Nicht zugewiesen', 'immobilien-rechner-pro'); ?>"></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <p class="submit">
                        <button type="button" class="button button-primary" id="save-broker-mapping">
                            <?php esc_html_e('Zuweisung speichern', 'immobilien-rechner-pro'); ?>
                        </button>
                    </p>

                    <?php if (empty($api_key)): ?>
                    <div class="notice notice-info" style="margin-top: 20px;">
                        <p>
                            <span class="dashicons dashicons-info"></span>
                            <?php esc_html_e('Demo-Modus: Es werden Test-Makler angezeigt. Verbinden Sie Propstack für echte Daten.', 'immobilien-rechner-pro'); ?>
                        </p>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

        <?php elseif ($active_tab === 'newsletter'): ?>
            <!-- Newsletter Tab -->
            <div id="newsletter-settings">
                <h2><?php esc_html_e('Newsletter-Einstellungen', 'immobilien-rechner-pro'); ?></h2>
                <p class="description">
                    <?php esc_html_e('Konfigurieren Sie, wie Newsletter-Abonnenten in Propstack verarbeitet werden.', 'immobilien-rechner-pro'); ?>
                </p>

                <?php
                // Get brokers
                if (empty($brokers)) {
                    $brokers = IRP_Propstack::get_brokers();
                }
                $newsletter_broker_id = $propstack_settings['newsletter_broker_id'] ?? '';
                $sync_newsletter_only = $propstack_settings['sync_newsletter_only'] ?? false;
                ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="sync_newsletter_only"><?php esc_html_e('Sync-Filter', 'immobilien-rechner-pro'); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox"
                                       id="sync_newsletter_only"
                                       name="sync_newsletter_only"
                                       value="1"
                                       <?php checked($sync_newsletter_only); ?>>
                                <?php esc_html_e('Nur Leads mit Newsletter-Zustimmung synchronisieren', 'immobilien-rechner-pro'); ?>
                            </label>
                            <p class="description">
                                <?php esc_html_e('Wenn aktiviert, werden nur Leads synchronisiert, die dem Newsletter zugestimmt haben.', 'immobilien-rechner-pro'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="newsletter_broker_id"><?php esc_html_e('Newsletter-Makler', 'immobilien-rechner-pro'); ?></label>
                        </th>
                        <td>
                            <select id="newsletter_broker_id" name="newsletter_broker_id" style="width: 100%; max-width: 400px;">
                                <option value=""><?php esc_html_e('-- Gleicher Makler wie Stadt-Zuweisung --', 'immobilien-rechner-pro'); ?></option>
                                <?php foreach ($brokers as $broker): ?>
                                    <option value="<?php echo esc_attr($broker['id']); ?>"
                                            <?php selected($newsletter_broker_id, $broker['id']); ?>>
                                        <?php echo esc_html($broker['name']); ?>
                                        <?php if (!empty($broker['email'])): ?>
                                            (<?php echo esc_html($broker['email']); ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description">
                                <?php esc_html_e('Optional: Separater Makler für Newsletter-Leads. Leer lassen für Standard-Zuweisung nach Stadt.', 'immobilien-rechner-pro'); ?>
                            </p>
                        </td>
                    </tr>
                </table>

                <h3 style="margin-top: 30px;"><?php esc_html_e('Propstack Tags', 'immobilien-rechner-pro'); ?></h3>
                <p class="description">
                    <?php esc_html_e('Diese Tags werden automatisch beim Sync gesetzt:', 'immobilien-rechner-pro'); ?>
                </p>
                <ul style="list-style: disc; margin-left: 20px; margin-top: 10px;">
                    <li><code>immobilien-rechner</code> - <?php esc_html_e('Alle Leads aus diesem Plugin', 'immobilien-rechner-pro'); ?></li>
                    <li><code>newsletter</code> - <?php esc_html_e('Leads mit Newsletter-Zustimmung', 'immobilien-rechner-pro'); ?></li>
                    <li><code>mietwert</code> / <code>verkaufen-vs-vermieten</code> - <?php esc_html_e('Je nach verwendetem Rechner-Modus', 'immobilien-rechner-pro'); ?></li>
                </ul>

                <p class="submit">
                    <button type="button" class="button button-primary" id="save-newsletter-settings">
                        <?php esc_html_e('Speichern', 'immobilien-rechner-pro'); ?>
                    </button>
                </p>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Toggle API key visibility
    $('#toggle-api-key').on('click', function() {
        var input = $('#propstack_api_key');
        var icon = $(this).find('.dashicons');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('dashicons-visibility').addClass('dashicons-hidden');
        } else {
            input.attr('type', 'password');
            icon.removeClass('dashicons-hidden').addClass('dashicons-visibility');
        }
    });

    // Test Propstack connection
    $('#test-propstack-connection').on('click', function() {
        var $button = $(this);
        var $result = $('#connection-test-result');
        var apiKey = $('#propstack_api_key').val();

        $button.prop('disabled', true);
        $result.html('<span class="spinner is-active" style="float: none;"></span>');

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'irp_propstack_test',
                nonce: irpAdmin.nonce,
                api_key: apiKey
            },
            success: function(response) {
                if (response.success) {
                    $result.html('<span style="color: #28a745;"><span class="dashicons dashicons-yes"></span> ' + response.data.message + '</span>');
                    // Cache brokers if returned
                    if (response.data.brokers) {
                        console.log('Brokers loaded:', response.data.brokers.length);
                    }
                } else {
                    $result.html('<span style="color: #dc3545;"><span class="dashicons dashicons-no"></span> ' + response.data.message + '</span>');
                }
            },
            error: function() {
                $result.html('<span style="color: #dc3545;"><span class="dashicons dashicons-no"></span> <?php esc_html_e('Verbindungsfehler', 'immobilien-rechner-pro'); ?></span>');
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });
    });

    // Save Propstack connection settings
    $('#save-propstack-connection').on('click', function() {
        var $button = $(this);
        $button.prop('disabled', true).text('<?php esc_html_e('Speichern...', 'immobilien-rechner-pro'); ?>');

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'irp_propstack_save',
                nonce: irpAdmin.nonce,
                enabled: $('#propstack_enabled').is(':checked') ? 1 : 0,
                api_key: $('#propstack_api_key').val()
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message);
                }
            },
            error: function() {
                alert('<?php esc_html_e('Fehler beim Speichern.', 'immobilien-rechner-pro'); ?>');
            },
            complete: function() {
                $button.prop('disabled', false).text('<?php esc_html_e('Speichern', 'immobilien-rechner-pro'); ?>');
            }
        });
    });

    // Save broker mapping
    $('#save-broker-mapping').on('click', function() {
        var $button = $(this);
        var brokerMapping = {};

        $('select.broker-select').each(function() {
            var name = $(this).attr('name');
            var match = name.match(/broker_mapping\[([^\]]+)\]/);
            if (match) {
                brokerMapping[match[1]] = $(this).val();
            }
        });

        $button.prop('disabled', true).text('<?php esc_html_e('Speichern...', 'immobilien-rechner-pro'); ?>');

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'irp_propstack_save',
                nonce: irpAdmin.nonce,
                enabled: true, // Keep enabled when saving mapping
                api_key: '<?php echo esc_js($api_key); ?>',
                broker_mapping: brokerMapping
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message);
                }
            },
            error: function() {
                alert('<?php esc_html_e('Fehler beim Speichern.', 'immobilien-rechner-pro'); ?>');
            },
            complete: function() {
                $button.prop('disabled', false).text('<?php esc_html_e('Zuweisung speichern', 'immobilien-rechner-pro'); ?>');
            }
        });
    });

    // Save newsletter settings
    $('#save-newsletter-settings').on('click', function() {
        var $button = $(this);
        $button.prop('disabled', true).text('<?php esc_html_e('Speichern...', 'immobilien-rechner-pro'); ?>');

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'irp_propstack_save',
                nonce: irpAdmin.nonce,
                enabled: true,
                api_key: '<?php echo esc_js($api_key); ?>',
                newsletter_broker_id: $('#newsletter_broker_id').val(),
                sync_newsletter_only: $('#sync_newsletter_only').is(':checked') ? 1 : 0
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                } else {
                    alert(response.data.message);
                }
            },
            error: function() {
                alert('<?php esc_html_e('Fehler beim Speichern.', 'immobilien-rechner-pro'); ?>');
            },
            complete: function() {
                $button.prop('disabled', false).text('<?php esc_html_e('Speichern', 'immobilien-rechner-pro'); ?>');
            }
        });
    });
});
</script>
