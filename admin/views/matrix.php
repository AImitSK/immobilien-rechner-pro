<?php
/**
 * Admin Matrix & Data View
 */

if (!defined('ABSPATH')) {
    exit;
}

$region_labels = [
    '0' => __('Leipzig/Dresden (0xxxx)', 'immobilien-rechner-pro'),
    '1' => __('Berlin (1xxxx)', 'immobilien-rechner-pro'),
    '2' => __('Hamburg (2xxxx)', 'immobilien-rechner-pro'),
    '3' => __('Hannover (3xxxx)', 'immobilien-rechner-pro'),
    '4' => __('Düsseldorf (4xxxx)', 'immobilien-rechner-pro'),
    '5' => __('Köln/Bonn (5xxxx)', 'immobilien-rechner-pro'),
    '6' => __('Frankfurt (6xxxx)', 'immobilien-rechner-pro'),
    '7' => __('Stuttgart (7xxxx)', 'immobilien-rechner-pro'),
    '8' => __('München (8xxxx)', 'immobilien-rechner-pro'),
    '9' => __('Nürnberg (9xxxx)', 'immobilien-rechner-pro'),
];

$condition_labels = [
    'new' => __('Neubau', 'immobilien-rechner-pro'),
    'renovated' => __('Renoviert', 'immobilien-rechner-pro'),
    'good' => __('Guter Zustand', 'immobilien-rechner-pro'),
    'needs_renovation' => __('Renovierungsbedürftig', 'immobilien-rechner-pro'),
];

$type_labels = [
    'apartment' => __('Wohnung', 'immobilien-rechner-pro'),
    'house' => __('Haus', 'immobilien-rechner-pro'),
    'commercial' => __('Gewerbe', 'immobilien-rechner-pro'),
];

$feature_labels = [
    'balcony' => __('Balkon', 'immobilien-rechner-pro'),
    'terrace' => __('Terrasse', 'immobilien-rechner-pro'),
    'garden' => __('Garten', 'immobilien-rechner-pro'),
    'elevator' => __('Aufzug', 'immobilien-rechner-pro'),
    'parking' => __('Stellplatz', 'immobilien-rechner-pro'),
    'garage' => __('Garage', 'immobilien-rechner-pro'),
    'cellar' => __('Keller', 'immobilien-rechner-pro'),
    'fitted_kitchen' => __('Einbauküche', 'immobilien-rechner-pro'),
    'floor_heating' => __('Fußbodenheizung', 'immobilien-rechner-pro'),
    'guest_toilet' => __('Gäste-WC', 'immobilien-rechner-pro'),
    'barrier_free' => __('Barrierefrei', 'immobilien-rechner-pro'),
];

$active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'prices';
?>

<div class="wrap irp-admin-wrap irp-matrix-wrap">
    <h1><?php esc_html_e('Matrix & Daten', 'immobilien-rechner-pro'); ?></h1>

    <p class="description">
        <?php esc_html_e('Hier können Sie die Berechnungsgrundlagen für den Immobilien-Rechner anpassen.', 'immobilien-rechner-pro'); ?>
    </p>

    <nav class="nav-tab-wrapper irp-tabs">
        <a href="?page=irp-matrix&tab=prices" class="nav-tab <?php echo $active_tab === 'prices' ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e('Mietpreise', 'immobilien-rechner-pro'); ?>
        </a>
        <a href="?page=irp-matrix&tab=factors" class="nav-tab <?php echo $active_tab === 'factors' ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e('Vervielfältiger', 'immobilien-rechner-pro'); ?>
        </a>
        <a href="?page=irp-matrix&tab=multipliers" class="nav-tab <?php echo $active_tab === 'multipliers' ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e('Multiplikatoren', 'immobilien-rechner-pro'); ?>
        </a>
        <a href="?page=irp-matrix&tab=features" class="nav-tab <?php echo $active_tab === 'features' ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e('Ausstattung', 'immobilien-rechner-pro'); ?>
        </a>
        <a href="?page=irp-matrix&tab=global" class="nav-tab <?php echo $active_tab === 'global' ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e('Globale Parameter', 'immobilien-rechner-pro'); ?>
        </a>
    </nav>

    <form method="post" action="options.php" class="irp-matrix-form">
        <?php settings_fields('irp_matrix_group'); ?>

        <!-- Tab: Mietpreise nach Region -->
        <div class="irp-tab-content <?php echo $active_tab === 'prices' ? 'active' : ''; ?>" id="tab-prices">
            <div class="irp-settings-section">
                <h2><?php esc_html_e('Basis-Mietpreise nach Region', 'immobilien-rechner-pro'); ?></h2>
                <p class="description">
                    <?php esc_html_e('Geben Sie die durchschnittlichen Kaltmieten pro m² für jede Region ein. Die Region wird anhand der ersten Ziffer der Postleitzahl bestimmt.', 'immobilien-rechner-pro'); ?>
                </p>

                <table class="widefat irp-data-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Region', 'immobilien-rechner-pro'); ?></th>
                            <th><?php esc_html_e('Basis-Preis (€/m²)', 'immobilien-rechner-pro'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($region_labels as $code => $label) : ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html($label); ?></strong>
                                </td>
                                <td>
                                    <input type="number"
                                           name="irp_price_matrix[base_prices][<?php echo esc_attr($code); ?>]"
                                           value="<?php echo esc_attr($matrix['base_prices'][$code] ?? 10.00); ?>"
                                           step="0.10"
                                           min="0"
                                           max="100"
                                           class="small-text"> €/m²
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab: Vervielfältiger (Verkaufsfaktoren) -->
        <div class="irp-tab-content <?php echo $active_tab === 'factors' ? 'active' : ''; ?>" id="tab-factors">
            <div class="irp-settings-section">
                <h2><?php esc_html_e('Verkaufs-Vervielfältiger nach Region', 'immobilien-rechner-pro'); ?></h2>
                <p class="description">
                    <?php esc_html_e('Der Vervielfältiger gibt an, wie viele Jahresnettokaltmieten dem Kaufpreis entsprechen. Ein Vervielfältiger von 25 bedeutet: Kaufpreis = 25 × Jahresnettokaltmiete.', 'immobilien-rechner-pro'); ?>
                </p>

                <table class="widefat irp-data-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Region', 'immobilien-rechner-pro'); ?></th>
                            <th><?php esc_html_e('Vervielfältiger', 'immobilien-rechner-pro'); ?></th>
                            <th><?php esc_html_e('Beispiel', 'immobilien-rechner-pro'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($region_labels as $code => $label) :
                            $factor = $matrix['sale_factors'][$code] ?? 25;
                            $example_rent = 1000;
                            $example_price = $example_rent * 12 * $factor;
                        ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html($label); ?></strong>
                                </td>
                                <td>
                                    <input type="number"
                                           name="irp_price_matrix[sale_factors][<?php echo esc_attr($code); ?>]"
                                           value="<?php echo esc_attr($factor); ?>"
                                           step="0.5"
                                           min="5"
                                           max="60"
                                           class="small-text irp-factor-input"
                                           data-region="<?php echo esc_attr($code); ?>">
                                </td>
                                <td class="irp-example">
                                    <span class="irp-example-text">
                                        <?php
                                        printf(
                                            esc_html__('Bei %s € Monatsmiete = %s € Kaufpreis', 'immobilien-rechner-pro'),
                                            number_format($example_rent, 0, ',', '.'),
                                            '<strong class="irp-calc-price" data-region="' . esc_attr($code) . '">' . number_format($example_price, 0, ',', '.') . '</strong>'
                                        );
                                        ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab: Multiplikatoren -->
        <div class="irp-tab-content <?php echo $active_tab === 'multipliers' ? 'active' : ''; ?>" id="tab-multipliers">
            <div class="irp-settings-section">
                <h2><?php esc_html_e('Zustands-Multiplikatoren', 'immobilien-rechner-pro'); ?></h2>
                <p class="description">
                    <?php esc_html_e('Diese Faktoren werden auf den Basis-Mietpreis angewendet. Ein Wert von 1.0 bedeutet keine Änderung, 1.25 bedeutet +25%.', 'immobilien-rechner-pro'); ?>
                </p>

                <table class="widefat irp-data-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Zustand', 'immobilien-rechner-pro'); ?></th>
                            <th><?php esc_html_e('Multiplikator', 'immobilien-rechner-pro'); ?></th>
                            <th><?php esc_html_e('Auswirkung', 'immobilien-rechner-pro'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($condition_labels as $key => $label) :
                            $multiplier = $matrix['condition_multipliers'][$key] ?? 1.00;
                            $impact = ($multiplier - 1) * 100;
                        ?>
                            <tr>
                                <td><strong><?php echo esc_html($label); ?></strong></td>
                                <td>
                                    <input type="number"
                                           name="irp_price_matrix[condition_multipliers][<?php echo esc_attr($key); ?>]"
                                           value="<?php echo esc_attr($multiplier); ?>"
                                           step="0.01"
                                           min="0.5"
                                           max="2"
                                           class="small-text">
                                </td>
                                <td>
                                    <span class="<?php echo $impact >= 0 ? 'irp-positive' : 'irp-negative'; ?>">
                                        <?php echo ($impact >= 0 ? '+' : '') . number_format($impact, 0) . '%'; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="irp-settings-section">
                <h2><?php esc_html_e('Objekttyp-Multiplikatoren', 'immobilien-rechner-pro'); ?></h2>

                <table class="widefat irp-data-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Objekttyp', 'immobilien-rechner-pro'); ?></th>
                            <th><?php esc_html_e('Multiplikator', 'immobilien-rechner-pro'); ?></th>
                            <th><?php esc_html_e('Auswirkung', 'immobilien-rechner-pro'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($type_labels as $key => $label) :
                            $multiplier = $matrix['type_multipliers'][$key] ?? 1.00;
                            $impact = ($multiplier - 1) * 100;
                        ?>
                            <tr>
                                <td><strong><?php echo esc_html($label); ?></strong></td>
                                <td>
                                    <input type="number"
                                           name="irp_price_matrix[type_multipliers][<?php echo esc_attr($key); ?>]"
                                           value="<?php echo esc_attr($multiplier); ?>"
                                           step="0.01"
                                           min="0.5"
                                           max="2"
                                           class="small-text">
                                </td>
                                <td>
                                    <span class="<?php echo $impact >= 0 ? 'irp-positive' : 'irp-negative'; ?>">
                                        <?php echo ($impact >= 0 ? '+' : '') . number_format($impact, 0) . '%'; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab: Ausstattung -->
        <div class="irp-tab-content <?php echo $active_tab === 'features' ? 'active' : ''; ?>" id="tab-features">
            <div class="irp-settings-section">
                <h2><?php esc_html_e('Ausstattungs-Zuschläge', 'immobilien-rechner-pro'); ?></h2>
                <p class="description">
                    <?php esc_html_e('Diese Beträge werden pro m² zum Basis-Mietpreis addiert, wenn das Merkmal vorhanden ist.', 'immobilien-rechner-pro'); ?>
                </p>

                <table class="widefat irp-data-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Ausstattungsmerkmal', 'immobilien-rechner-pro'); ?></th>
                            <th><?php esc_html_e('Zuschlag (€/m²)', 'immobilien-rechner-pro'); ?></th>
                            <th><?php esc_html_e('Bei 80m²', 'immobilien-rechner-pro'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($feature_labels as $key => $label) :
                            $premium = $matrix['feature_premiums'][$key] ?? 0.00;
                        ?>
                            <tr>
                                <td><strong><?php echo esc_html($label); ?></strong></td>
                                <td>
                                    <input type="number"
                                           name="irp_price_matrix[feature_premiums][<?php echo esc_attr($key); ?>]"
                                           value="<?php echo esc_attr($premium); ?>"
                                           step="0.05"
                                           min="0"
                                           max="10"
                                           class="small-text"> €/m²
                                </td>
                                <td>
                                    <span class="irp-positive">
                                        +<?php echo number_format($premium * 80, 0, ',', '.'); ?> €/Monat
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab: Globale Parameter -->
        <div class="irp-tab-content <?php echo $active_tab === 'global' ? 'active' : ''; ?>" id="tab-global">
            <div class="irp-settings-section">
                <h2><?php esc_html_e('Globale Berechnungsparameter', 'immobilien-rechner-pro'); ?></h2>
                <p class="description">
                    <?php esc_html_e('Diese Parameter werden für die Vergleichsberechnung (Verkaufen vs. Vermieten) verwendet.', 'immobilien-rechner-pro'); ?>
                </p>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="interest_rate"><?php esc_html_e('Kapitalanlage-Zinssatz', 'immobilien-rechner-pro'); ?></label>
                        </th>
                        <td>
                            <input type="number"
                                   id="interest_rate"
                                   name="irp_price_matrix[interest_rate]"
                                   value="<?php echo esc_attr($matrix['interest_rate'] ?? 3.0); ?>"
                                   step="0.1"
                                   min="0"
                                   max="15"
                                   class="small-text"> %
                            <p class="description">
                                <?php esc_html_e('Angenommener Zinssatz für alternative Kapitalanlage des Verkaufserlöses.', 'immobilien-rechner-pro'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="appreciation_rate"><?php esc_html_e('Wertsteigerung Immobilie', 'immobilien-rechner-pro'); ?></label>
                        </th>
                        <td>
                            <input type="number"
                                   id="appreciation_rate"
                                   name="irp_price_matrix[appreciation_rate]"
                                   value="<?php echo esc_attr($matrix['appreciation_rate'] ?? 2.0); ?>"
                                   step="0.1"
                                   min="-5"
                                   max="15"
                                   class="small-text"> %
                            <p class="description">
                                <?php esc_html_e('Angenommene jährliche Wertsteigerung der Immobilie.', 'immobilien-rechner-pro'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rent_increase_rate"><?php esc_html_e('Jährliche Mietsteigerung', 'immobilien-rechner-pro'); ?></label>
                        </th>
                        <td>
                            <input type="number"
                                   id="rent_increase_rate"
                                   name="irp_price_matrix[rent_increase_rate]"
                                   value="<?php echo esc_attr($matrix['rent_increase_rate'] ?? 2.0); ?>"
                                   step="0.1"
                                   min="0"
                                   max="10"
                                   class="small-text"> %
                            <p class="description">
                                <?php esc_html_e('Angenommene jährliche Mietsteigerung für die Prognose.', 'immobilien-rechner-pro'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="irp-settings-section irp-info-box">
                <h3><?php esc_html_e('Hinweis zur Berechnung', 'immobilien-rechner-pro'); ?></h3>
                <p>
                    <?php esc_html_e('Die Vergleichsberechnung "Verkaufen vs. Vermieten" verwendet diese Parameter, um zwei Szenarien zu modellieren:', 'immobilien-rechner-pro'); ?>
                </p>
                <ul>
                    <li><strong><?php esc_html_e('Verkaufsszenario:', 'immobilien-rechner-pro'); ?></strong>
                        <?php esc_html_e('Erlös wird zum Kapitalanlage-Zinssatz angelegt.', 'immobilien-rechner-pro'); ?>
                    </li>
                    <li><strong><?php esc_html_e('Vermietungsszenario:', 'immobilien-rechner-pro'); ?></strong>
                        <?php esc_html_e('Kumulierte Mieteinnahmen + Wertsteigerung der Immobilie.', 'immobilien-rechner-pro'); ?>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Hidden fields for preserving data across tabs -->
        <?php if ($active_tab !== 'prices') : ?>
            <?php foreach ($matrix['base_prices'] ?? [] as $code => $price) : ?>
                <input type="hidden" name="irp_price_matrix[base_prices][<?php echo esc_attr($code); ?>]" value="<?php echo esc_attr($price); ?>">
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($active_tab !== 'factors') : ?>
            <?php foreach ($matrix['sale_factors'] ?? [] as $code => $factor) : ?>
                <input type="hidden" name="irp_price_matrix[sale_factors][<?php echo esc_attr($code); ?>]" value="<?php echo esc_attr($factor); ?>">
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($active_tab !== 'multipliers') : ?>
            <?php foreach ($matrix['condition_multipliers'] ?? [] as $key => $mult) : ?>
                <input type="hidden" name="irp_price_matrix[condition_multipliers][<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($mult); ?>">
            <?php endforeach; ?>
            <?php foreach ($matrix['type_multipliers'] ?? [] as $key => $mult) : ?>
                <input type="hidden" name="irp_price_matrix[type_multipliers][<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($mult); ?>">
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($active_tab !== 'features') : ?>
            <?php foreach ($matrix['feature_premiums'] ?? [] as $key => $premium) : ?>
                <input type="hidden" name="irp_price_matrix[feature_premiums][<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($premium); ?>">
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($active_tab !== 'global') : ?>
            <input type="hidden" name="irp_price_matrix[interest_rate]" value="<?php echo esc_attr($matrix['interest_rate'] ?? 3.0); ?>">
            <input type="hidden" name="irp_price_matrix[appreciation_rate]" value="<?php echo esc_attr($matrix['appreciation_rate'] ?? 2.0); ?>">
            <input type="hidden" name="irp_price_matrix[rent_increase_rate]" value="<?php echo esc_attr($matrix['rent_increase_rate'] ?? 2.0); ?>">
        <?php endif; ?>

        <?php submit_button(__('Änderungen speichern', 'immobilien-rechner-pro')); ?>
    </form>
</div>
