<?php
/**
 * Admin Leads List View
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap irp-admin-wrap">
    <h1><?php esc_html_e('Leads', 'immobilien-rechner-pro'); ?></h1>

    <div class="irp-leads-toolbar">
        <form method="get" class="irp-filter-form">
            <input type="hidden" name="page" value="irp-leads">

            <select name="mode">
                <option value=""><?php esc_html_e('Alle Modi', 'immobilien-rechner-pro'); ?></option>
                <option value="rental" <?php selected($args['mode'], 'rental'); ?>><?php esc_html_e('Mietwert', 'immobilien-rechner-pro'); ?></option>
                <option value="comparison" <?php selected($args['mode'], 'comparison'); ?>><?php esc_html_e('Vergleich', 'immobilien-rechner-pro'); ?></option>
            </select>

            <input type="search" name="s" value="<?php echo esc_attr($args['search']); ?>"
                   placeholder="<?php esc_attr_e('Suchen...', 'immobilien-rechner-pro'); ?>">

            <button type="submit" class="button"><?php esc_html_e('Filtern', 'immobilien-rechner-pro'); ?></button>
        </form>

        <button type="button" class="button irp-export-btn">
            <span class="dashicons dashicons-download"></span>
            <?php esc_html_e('CSV exportieren', 'immobilien-rechner-pro'); ?>
        </button>
    </div>

    <?php if (empty($leads['items'])) : ?>
        <div class="irp-no-leads">
            <p><?php esc_html_e('Keine Leads gefunden.', 'immobilien-rechner-pro'); ?></p>
        </div>
    <?php else : ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th class="column-name"><?php esc_html_e('Name', 'immobilien-rechner-pro'); ?></th>
                    <th class="column-email"><?php esc_html_e('E-Mail', 'immobilien-rechner-pro'); ?></th>
                    <th class="column-phone"><?php esc_html_e('Telefon', 'immobilien-rechner-pro'); ?></th>
                    <th class="column-mode"><?php esc_html_e('Modus', 'immobilien-rechner-pro'); ?></th>
                    <th class="column-property"><?php esc_html_e('Objekt', 'immobilien-rechner-pro'); ?></th>
                    <th class="column-date"><?php esc_html_e('Datum', 'immobilien-rechner-pro'); ?></th>
                    <th class="column-actions"><?php esc_html_e('Aktionen', 'immobilien-rechner-pro'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($leads['items'] as $lead) : ?>
                    <tr>
                        <td class="column-name">
                            <a href="<?php echo esc_url(admin_url('admin.php?page=irp-leads&lead=' . $lead->id)); ?>">
                                <strong><?php echo esc_html($lead->name ?: '—'); ?></strong>
                            </a>
                        </td>
                        <td class="column-email">
                            <a href="mailto:<?php echo esc_attr($lead->email); ?>">
                                <?php echo esc_html($lead->email); ?>
                            </a>
                        </td>
                        <td class="column-phone">
                            <?php if ($lead->phone) : ?>
                                <a href="tel:<?php echo esc_attr($lead->phone); ?>">
                                    <?php echo esc_html($lead->phone); ?>
                                </a>
                            <?php else : ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td class="column-mode">
                            <span class="irp-badge irp-badge-<?php echo esc_attr($lead->mode); ?>">
                                <?php echo $lead->mode === 'rental' ? esc_html__('Mietwert', 'immobilien-rechner-pro') : esc_html__('Vergleich', 'immobilien-rechner-pro'); ?>
                            </span>
                        </td>
                        <td class="column-property">
                            <?php
                            $type_labels = [
                                'apartment' => __('Wohnung', 'immobilien-rechner-pro'),
                                'house' => __('Haus', 'immobilien-rechner-pro'),
                                'commercial' => __('Gewerbe', 'immobilien-rechner-pro'),
                            ];
                            $property_info = [];
                            if ($lead->property_type) {
                                $property_info[] = $type_labels[$lead->property_type] ?? ucfirst($lead->property_type);
                            }
                            if ($lead->property_size) {
                                $property_info[] = $lead->property_size . ' m²';
                            }
                            if ($lead->zip_code) {
                                $property_info[] = $lead->zip_code;
                            }
                            echo esc_html(implode(', ', $property_info) ?: '—');
                            ?>
                        </td>
                        <td class="column-date">
                            <?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($lead->created_at))); ?>
                        </td>
                        <td class="column-actions">
                            <a href="<?php echo esc_url(admin_url('admin.php?page=irp-leads&lead=' . $lead->id)); ?>"
                               class="button button-small">
                                <?php esc_html_e('Ansehen', 'immobilien-rechner-pro'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($leads['pages'] > 1) : ?>
            <div class="tablenav bottom">
                <div class="tablenav-pages">
                    <span class="displaying-num">
                        <?php printf(
                            esc_html(_n('%s Eintrag', '%s Einträge', $leads['total'], 'immobilien-rechner-pro')),
                            number_format_i18n($leads['total'])
                        ); ?>
                    </span>
                    <span class="pagination-links">
                        <?php
                        echo paginate_links([
                            'base' => add_query_arg('paged', '%#%'),
                            'format' => '',
                            'prev_text' => '&laquo;',
                            'next_text' => '&raquo;',
                            'total' => $leads['pages'],
                            'current' => $leads['current_page'],
                        ]);
                        ?>
                    </span>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
jQuery(function($) {
    $('.irp-export-btn').on('click', function() {
        var params = new URLSearchParams(window.location.search);
        var form = $('<form method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">');
        form.append('<input type="hidden" name="action" value="irp_export_leads">');
        form.append('<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('irp_export_leads'); ?>">');
        form.append('<input type="hidden" name="mode" value="' + (params.get('mode') || '') + '">');
        form.appendTo('body').submit().remove();
    });
});
</script>
