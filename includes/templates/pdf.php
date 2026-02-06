<?php
/**
 * PDF template for property valuation results
 *
 * Available variables:
 * - $logo_base64: Base64 encoded logo image
 * - $logo_width: Logo width in pixels
 * - $primary_color: Brand primary color
 * - $company_name, $company_name_2, $company_name_3: Company name lines
 * - $company_address, $company_phone, $company_email: Contact info
 * - $lead_name: Name of the lead
 * - $property_type: Translated property type
 * - $property_size: Size in m²
 * - $city_name: City name
 * - $condition: Translated condition
 * - $location_rating: 1-5 rating
 * - $features: Array of translated feature names
 * - $monthly_rent: Formatted rent estimate
 * - $rent_min, $rent_max: Formatted rent range
 * - $price_per_sqm: Price per square meter
 * - $date: Current date
 * - $disclaimer: Disclaimer text
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #1f2937;
            background: #ffffff;
        }

        .page {
            padding: 15px 40px 140px 40px;
        }

        /* Header with Logo */
        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo {
            max-width: <?php echo (int) $logo_width; ?>px;
            max-height: 60px;
            margin-bottom: 5px;
        }

        .document-title {
            font-size: 16pt;
            font-weight: bold;
            color: <?php echo esc_attr($primary_color); ?>;
            margin: 5px 0 2px;
        }

        .document-subtitle {
            font-size: 11pt;
            color: #6b7280;
        }

        /* Lead greeting */
        .greeting {
            font-size: 9pt;
            margin: 10px 0;
            color: #4b5563;
            line-height: 1.4;
        }

        /* Main result box - COMPACT */
        .result-container {
            background: #f8fafc;
            border: 2px solid <?php echo esc_attr($primary_color); ?>;
            border-radius: 8px;
            padding: 12px 20px;
            text-align: center;
            margin: 10px 0;
        }

        .result-label {
            font-size: 9pt;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .result-value {
            font-size: 28pt;
            font-weight: bold;
            color: <?php echo esc_attr($primary_color); ?>;
            margin: 3px 0;
        }

        .result-details {
            font-size: 9pt;
            color: #6b7280;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
        }

        .result-details span {
            margin: 0 10px;
        }

        /* Property details */
        .details-section {
            margin: 10px 0 8px;
        }

        .section-title {
            font-size: 10pt;
            font-weight: bold;
            color: <?php echo esc_attr($primary_color); ?>;
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 2px solid #e5e7eb;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table tr {
            border-bottom: 1px solid #f3f4f6;
        }

        .details-table tr:last-child {
            border-bottom: none;
        }

        .details-table td {
            padding: 4px 0;
            vertical-align: top;
            font-size: 9pt;
        }

        .details-table td:first-child {
            color: #6b7280;
            width: 35%;
        }

        .details-table td:last-child {
            font-weight: 500;
            color: #1f2937;
        }

        /* Location rating stars */
        .rating-stars {
            color: #fbbf24;
            font-size: 11pt;
        }

        .rating-stars .empty {
            color: #d1d5db;
        }

        .rating-text {
            font-size: 9pt;
            color: #6b7280;
            margin-left: 5px;
        }

        /* Features list */
        .features-list {
            display: inline;
        }

        .feature-tag {
            display: inline-block;
            background: #f0f9ff;
            color: <?php echo esc_attr($primary_color); ?>;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8pt;
            margin: 1px 2px 1px 0;
        }

        /* Disclaimer box */
        .disclaimer {
            background: #fef9e7;
            border-left: 3px solid #f59e0b;
            border-radius: 0 6px 6px 0;
            padding: 8px 12px;
            margin: 10px 0 0;
            font-size: 8pt;
            color: #78350f;
            line-height: 1.3;
        }

        .disclaimer strong {
            display: inline;
            margin-right: 5px;
            font-size: 8pt;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #f8fafc;
            border-top: 1px solid #e5e7eb;
            padding: 10px 40px 8px;
            font-size: 8pt;
            color: #6b7280;
            line-height: 1.4;
        }

        /* Agent Cards */
        .agent-cards {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .agent-card {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 0 10px;
        }

        .agent-card:first-child {
            padding-left: 0;
            border-right: 1px solid #e5e7eb;
        }

        .agent-card:last-child {
            padding-right: 0;
        }

        .agent-inner {
            display: table;
            width: 100%;
        }

        .agent-photo {
            display: table-cell;
            width: 50px;
            vertical-align: top;
            padding-right: 10px;
        }

        .agent-photo img {
            width: 50px;
            height: 50px;
            border-radius: 25px;
            object-fit: cover;
        }

        .agent-info {
            display: table-cell;
            vertical-align: top;
        }

        .agent-name {
            font-weight: bold;
            font-size: 9pt;
            color: #374151;
            margin-bottom: 2px;
        }

        .agent-role {
            font-size: 7pt;
            color: #6b7280;
            margin-bottom: 3px;
        }

        .agent-contact {
            font-size: 7pt;
            color: #6b7280;
        }

        .agent-contact a {
            color: <?php echo esc_attr($primary_color); ?>;
            text-decoration: none;
        }

        /* Company Footer */
        .footer-company-section {
            text-align: center;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
        }

        .footer-company {
            font-weight: bold;
            color: #374151;
            font-size: 8pt;
        }

        .footer-contact {
            margin-top: 2px;
            font-size: 7pt;
        }

        .footer-date {
            margin-top: 3px;
            color: #9ca3af;
            font-size: 7pt;
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <?php if (!empty($logo_base64)) : ?>
                <img src="<?php echo $logo_base64; ?>" class="logo" alt="Logo">
            <?php endif; ?>
            <div class="document-title">Immobilienbewertung</div>
            <div class="document-subtitle"><?php echo esc_html($property_type); ?> in <?php echo esc_html($city_name); ?></div>
        </div>

        <!-- Greeting -->
        <?php if (!empty($lead_name)) : ?>
            <div class="greeting">
                Sehr geehrte/r <?php echo esc_html($lead_name); ?>,<br>
                vielen Dank für Ihr Interesse an unserer Immobilienbewertung. Basierend auf Ihren Angaben und aktuellen Marktdaten haben wir den voraussichtlichen Mietwert Ihrer Immobilie ermittelt. Diese Einschätzung dient als erste Orientierung und kann Ihnen bei der Einordnung des Marktpotenzials helfen.
            </div>
        <?php endif; ?>

        <!-- Result Box - COMPACT -->
        <div class="result-container">
            <div class="result-label">Geschätzte Monatsmiete</div>
            <div class="result-value"><?php echo $monthly_rent; ?></div>
            <div class="result-details">
                <span>Spanne: <?php echo $rent_min; ?> – <?php echo $rent_max; ?></span>
                <span>·</span>
                <span>Quadratmeterpreis: <?php echo $price_per_sqm; ?></span>
            </div>
        </div>

        <!-- Property Details -->
        <div class="details-section">
            <div class="section-title">Ihre Angaben im Überblick</div>
            <table class="details-table">
                <tr>
                    <td>Objekttyp</td>
                    <td><?php echo esc_html($property_type); ?></td>
                </tr>
                <tr>
                    <td>Wohnfläche</td>
                    <td><?php echo esc_html($property_size); ?> m²</td>
                </tr>
                <tr>
                    <td>Standort</td>
                    <td><?php echo esc_html($city_name); ?><?php if (!empty($address)) : ?>, <?php echo esc_html($address); ?><?php endif; ?></td>
                </tr>
                <tr>
                    <td>Zustand</td>
                    <td><?php echo esc_html($condition); ?></td>
                </tr>
                <tr>
                    <td>Lagebewertung</td>
                    <td>
                        <span class="rating-stars"><?php
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= $location_rating ? '★' : '<span class="empty">★</span>';
                            }
                        ?></span>
                        <span class="rating-text">(<?php echo $location_rating; ?> von 5)</span>
                    </td>
                </tr>
                <?php if (!empty($features)) : ?>
                <tr>
                    <td>Ausstattung</td>
                    <td>
                        <?php foreach ($features as $feature) : ?>
                            <span class="feature-tag"><?php echo esc_html($feature); ?></span>
                        <?php endforeach; ?>
                    </td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- Disclaimer -->
        <div class="disclaimer">
            <strong>Wichtiger Hinweis</strong>
            <?php echo esc_html($disclaimer); ?>
        </div>
    </div>

    <!-- Footer (outside .page for fixed positioning) -->
    <div class="footer">
        <!-- Agent Cards -->
        <?php
        // Load agent images as base64
        $agent1_img = IRP_PLUGIN_DIR . 'assets/images/Brand_Nuener_Kontakt_200x200.jpg';
        $agent2_img = IRP_PLUGIN_DIR . 'assets/images/Brand_Schormann_Kontakt_200x200.jpg';
        $agent1_base64 = file_exists($agent1_img) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($agent1_img)) : '';
        $agent2_base64 = file_exists($agent2_img) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($agent2_img)) : '';
        ?>
        <div class="agent-cards">
            <div class="agent-card">
                <div class="agent-inner">
                    <?php if ($agent1_base64) : ?>
                    <div class="agent-photo">
                        <img src="<?php echo $agent1_base64; ?>" alt="Matthes Nuener">
                    </div>
                    <?php endif; ?>
                    <div class="agent-info">
                        <div class="agent-name">Matthes Nuener</div>
                        <div class="agent-role">Vermittelt Ihre Immobilie in Löhne und Vlotho</div>
                        <div class="agent-contact">
                            <a href="mailto:m.nuener@brand-partner.de">m.nuener@brand-partner.de</a><br>
                            Tel.: 05731 177555
                        </div>
                    </div>
                </div>
            </div>
            <div class="agent-card">
                <div class="agent-inner">
                    <?php if ($agent2_base64) : ?>
                    <div class="agent-photo">
                        <img src="<?php echo $agent2_base64; ?>" alt="Uwe Schormann">
                    </div>
                    <?php endif; ?>
                    <div class="agent-info">
                        <div class="agent-name">Uwe Schormann</div>
                        <div class="agent-role">Vermittelt Ihre Immobilie in Bad Oeynhausen</div>
                        <div class="agent-contact">
                            <a href="mailto:u.schormann@brand-partner.de">u.schormann@brand-partner.de</a><br>
                            Tel.: 05731 177560
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Company Info -->
        <div class="footer-company-section">
            <div class="footer-company">
                <?php echo esc_html($company_name); ?>
                <?php if (!empty($company_name_2)) : ?>
                    · <?php echo esc_html($company_name_2); ?>
                <?php endif; ?>
                <?php if (!empty($company_name_3)) : ?>
                    · <?php echo esc_html($company_name_3); ?>
                <?php endif; ?>
            </div>
            <div class="footer-contact">
                <?php
                $contact_parts = [];
                if (!empty($company_address)) {
                    $contact_parts[] = $company_address;
                }
                if (!empty($company_phone)) {
                    $contact_parts[] = 'Tel.: ' . $company_phone;
                }
                if (!empty($company_email)) {
                    $contact_parts[] = $company_email;
                }
                echo esc_html(implode(' · ', $contact_parts));
                ?>
            </div>
            <div class="footer-date">
                Erstellt am <?php echo esc_html($date); ?>
            </div>
        </div>
    </div>
</body>
</html>
