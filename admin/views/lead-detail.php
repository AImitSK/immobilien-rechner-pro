<?php
/**
 * Admin Lead Detail View
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!$lead) {
    echo '<div class="wrap"><p>' . esc_html__('Lead nicht gefunden.', 'immobilien-rechner-pro') . '</p></div>';
    return;
}

$type_labels = [
    'apartment' => __('Wohnung', 'immobilien-rechner-pro'),
    'house' => __('Haus', 'immobilien-rechner-pro'),
    'commercial' => __('Gewerbe', 'immobilien-rechner-pro'),
];
?>
<div class="wrap irp-admin-wrap">
    <h1>
        <a href="<?php echo esc_url(admin_url('admin.php?page=irp-leads')); ?>" class="page-title-action">
            &larr; <?php esc_html_e('Zurück zu Leads', 'immobilien-rechner-pro'); ?>
        </a>
        <?php esc_html_e('Lead-Details', 'immobilien-rechner-pro'); ?>
    </h1>

    <div class="irp-lead-detail">
        <div class="irp-lead-header">
            <div class="irp-lead-avatar">
                <?php echo get_avatar($lead->email, 80); ?>
            </div>
            <div class="irp-lead-info">
                <h2><?php echo esc_html($lead->name ?: $lead->email); ?></h2>
                <span class="irp-badge irp-badge-<?php echo esc_attr($lead->mode); ?>">
                    <?php echo $lead->mode === 'rental' ? esc_html__('Mietwert-Berechnung', 'immobilien-rechner-pro') : esc_html__('Verkauf vs. Vermietung', 'immobilien-rechner-pro'); ?>
                </span>
                <p class="irp-lead-date">
                    <?php printf(
                        esc_html__('Eingereicht am %s', 'immobilien-rechner-pro'),
                        date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($lead->created_at))
                    ); ?>
                </p>
            </div>
            <div class="irp-lead-actions">
                <a href="mailto:<?php echo esc_attr($lead->email); ?>" class="button button-primary">
                    <span class="dashicons dashicons-email"></span>
                    <?php esc_html_e('E-Mail senden', 'immobilien-rechner-pro'); ?>
                </a>
                <?php if ($lead->phone) : ?>
                    <a href="tel:<?php echo esc_attr($lead->phone); ?>" class="button">
                        <span class="dashicons dashicons-phone"></span>
                        <?php esc_html_e('Anrufen', 'immobilien-rechner-pro'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="irp-lead-panels">
            <div class="irp-panel">
                <h3><?php esc_html_e('Kontaktinformationen', 'immobilien-rechner-pro'); ?></h3>
                <table class="irp-detail-table">
                    <tr>
                        <th><?php esc_html_e('Name', 'immobilien-rechner-pro'); ?></th>
                        <td><?php echo esc_html($lead->name ?: '—'); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('E-Mail', 'immobilien-rechner-pro'); ?></th>
                        <td><a href="mailto:<?php echo esc_attr($lead->email); ?>"><?php echo esc_html($lead->email); ?></a></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Telefon', 'immobilien-rechner-pro'); ?></th>
                        <td>
                            <?php if ($lead->phone) : ?>
                                <a href="tel:<?php echo esc_attr($lead->phone); ?>"><?php echo esc_html($lead->phone); ?></a>
                            <?php else : ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Einwilligung', 'immobilien-rechner-pro'); ?></th>
                        <td>
                            <?php if ($lead->consent) : ?>
                                <span class="irp-consent-yes">✓ <?php esc_html_e('Datenschutzerklärung akzeptiert', 'immobilien-rechner-pro'); ?></span>
                            <?php else : ?>
                                <span class="irp-consent-no">✗ <?php esc_html_e('Keine Einwilligung', 'immobilien-rechner-pro'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="irp-panel">
                <h3><?php esc_html_e('Objektinformationen', 'immobilien-rechner-pro'); ?></h3>
                <table class="irp-detail-table">
                    <tr>
                        <th><?php esc_html_e('Typ', 'immobilien-rechner-pro'); ?></th>
                        <td><?php echo esc_html($type_labels[$lead->property_type] ?? ucfirst($lead->property_type ?: '—')); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Größe', 'immobilien-rechner-pro'); ?></th>
                        <td><?php echo $lead->property_size ? esc_html($lead->property_size . ' m²') : '—'; ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Standort', 'immobilien-rechner-pro'); ?></th>
                        <td>
                            <?php
                            $location = array_filter([$lead->zip_code, $lead->property_location]);
                            echo esc_html(implode(' ', $location) ?: '—');
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <?php if ($lead->calculation_data) : ?>
            <div class="irp-panel">
                <h3><?php esc_html_e('Berechnungsdaten', 'immobilien-rechner-pro'); ?></h3>
                <pre class="irp-json-display"><?php echo esc_html(json_encode($lead->calculation_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
            </div>
        <?php endif; ?>

        <div class="irp-lead-footer">
            <form method="post" onsubmit="return confirm('<?php esc_attr_e('Sind Sie sicher, dass Sie diesen Lead löschen möchten?', 'immobilien-rechner-pro'); ?>');">
                <?php wp_nonce_field('irp_delete_lead'); ?>
                <input type="hidden" name="action" value="delete_lead">
                <input type="hidden" name="lead_id" value="<?php echo esc_attr($lead->id); ?>">
                <button type="submit" class="button irp-delete-btn">
                    <span class="dashicons dashicons-trash"></span>
                    <?php esc_html_e('Lead löschen', 'immobilien-rechner-pro'); ?>
                </button>
            </form>
        </div>
    </div>
</div>
