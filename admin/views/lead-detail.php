<?php
/**
 * Admin Lead Detail View
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!$lead) {
    echo '<div class="wrap"><p>' . esc_html__('Lead not found.', 'immobilien-rechner-pro') . '</p></div>';
    return;
}
?>
<div class="wrap irp-admin-wrap">
    <h1>
        <a href="<?php echo esc_url(admin_url('admin.php?page=irp-leads')); ?>" class="page-title-action">
            &larr; <?php esc_html_e('Back to Leads', 'immobilien-rechner-pro'); ?>
        </a>
        <?php esc_html_e('Lead Details', 'immobilien-rechner-pro'); ?>
    </h1>
    
    <div class="irp-lead-detail">
        <div class="irp-lead-header">
            <div class="irp-lead-avatar">
                <?php echo get_avatar($lead->email, 80); ?>
            </div>
            <div class="irp-lead-info">
                <h2><?php echo esc_html($lead->name ?: $lead->email); ?></h2>
                <span class="irp-badge irp-badge-<?php echo esc_attr($lead->mode); ?>">
                    <?php echo $lead->mode === 'rental' ? esc_html__('Rental Valuation', 'immobilien-rechner-pro') : esc_html__('Sell vs. Rent Comparison', 'immobilien-rechner-pro'); ?>
                </span>
                <p class="irp-lead-date">
                    <?php printf(
                        esc_html__('Submitted on %s', 'immobilien-rechner-pro'),
                        date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($lead->created_at))
                    ); ?>
                </p>
            </div>
            <div class="irp-lead-actions">
                <a href="mailto:<?php echo esc_attr($lead->email); ?>" class="button button-primary">
                    <span class="dashicons dashicons-email"></span>
                    <?php esc_html_e('Send Email', 'immobilien-rechner-pro'); ?>
                </a>
                <?php if ($lead->phone) : ?>
                    <a href="tel:<?php echo esc_attr($lead->phone); ?>" class="button">
                        <span class="dashicons dashicons-phone"></span>
                        <?php esc_html_e('Call', 'immobilien-rechner-pro'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="irp-lead-panels">
            <div class="irp-panel">
                <h3><?php esc_html_e('Contact Information', 'immobilien-rechner-pro'); ?></h3>
                <table class="irp-detail-table">
                    <tr>
                        <th><?php esc_html_e('Name', 'immobilien-rechner-pro'); ?></th>
                        <td><?php echo esc_html($lead->name ?: '—'); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Email', 'immobilien-rechner-pro'); ?></th>
                        <td><a href="mailto:<?php echo esc_attr($lead->email); ?>"><?php echo esc_html($lead->email); ?></a></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Phone', 'immobilien-rechner-pro'); ?></th>
                        <td>
                            <?php if ($lead->phone) : ?>
                                <a href="tel:<?php echo esc_attr($lead->phone); ?>"><?php echo esc_html($lead->phone); ?></a>
                            <?php else : ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Consent', 'immobilien-rechner-pro'); ?></th>
                        <td>
                            <?php if ($lead->consent) : ?>
                                <span class="irp-consent-yes">✓ <?php esc_html_e('Agreed to privacy policy', 'immobilien-rechner-pro'); ?></span>
                            <?php else : ?>
                                <span class="irp-consent-no">✗ <?php esc_html_e('No consent', 'immobilien-rechner-pro'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="irp-panel">
                <h3><?php esc_html_e('Property Information', 'immobilien-rechner-pro'); ?></h3>
                <table class="irp-detail-table">
                    <tr>
                        <th><?php esc_html_e('Type', 'immobilien-rechner-pro'); ?></th>
                        <td><?php echo esc_html(ucfirst($lead->property_type ?: '—')); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Size', 'immobilien-rechner-pro'); ?></th>
                        <td><?php echo $lead->property_size ? esc_html($lead->property_size . ' m²') : '—'; ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Location', 'immobilien-rechner-pro'); ?></th>
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
                <h3><?php esc_html_e('Calculation Data', 'immobilien-rechner-pro'); ?></h3>
                <pre class="irp-json-display"><?php echo esc_html(json_encode($lead->calculation_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
            </div>
        <?php endif; ?>
        
        <div class="irp-lead-footer">
            <form method="post" onsubmit="return confirm('<?php esc_attr_e('Are you sure you want to delete this lead?', 'immobilien-rechner-pro'); ?>');">
                <?php wp_nonce_field('irp_delete_lead'); ?>
                <input type="hidden" name="action" value="delete_lead">
                <input type="hidden" name="lead_id" value="<?php echo esc_attr($lead->id); ?>">
                <button type="submit" class="button irp-delete-btn">
                    <span class="dashicons dashicons-trash"></span>
                    <?php esc_html_e('Delete Lead', 'immobilien-rechner-pro'); ?>
                </button>
            </form>
        </div>
    </div>
</div>
