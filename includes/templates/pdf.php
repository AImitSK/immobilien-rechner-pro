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
            font-size: 11pt;
            line-height: 1.5;
            color: #1f2937;
            background: #ffffff;
        }

        .page {
            padding: 50px;
            min-height: 100%;
            position: relative;
        }

        /* Header with Logo */
        .header {
            text-align: center;
            padding-bottom: 30px;
            border-bottom: 3px solid <?php echo esc_attr($primary_color); ?>;
            margin-bottom: 40px;
        }

        .logo {
            max-width: <?php echo (int) $logo_width; ?>px;
            max-height: 80px;
            margin-bottom: 15px;
        }

        .document-title {
            font-size: 28pt;
            font-weight: bold;
            color: <?php echo esc_attr($primary_color); ?>;
            margin: 20px 0 10px;
        }

        .document-subtitle {
            font-size: 14pt;
            color: #6b7280;
        }

        /* Lead greeting */
        .greeting {
            font-size: 12pt;
            margin-bottom: 30px;
            color: #374151;
        }

        /* Main result box */
        .result-container {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: 2px solid <?php echo esc_attr($primary_color); ?>;
            border-radius: 12px;
            padding: 35px;
            text-align: center;
            margin: 30px 0;
        }

        .result-label {
            font-size: 13pt;
            color: #6b7280;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .result-value {
            font-size: 42pt;
            font-weight: bold;
            color: <?php echo esc_attr($primary_color); ?>;
            margin: 10px 0;
        }

        .result-suffix {
            font-size: 16pt;
            color: #6b7280;
        }

        .result-range {
            font-size: 11pt;
            color: #6b7280;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #cbd5e1;
        }

        .result-sqm {
            font-size: 12pt;
            color: #374151;
            margin-top: 10px;
        }

        /* Property details */
        .details-section {
            margin: 35px 0;
        }

        .section-title {
            font-size: 14pt;
            font-weight: bold;
            color: <?php echo esc_attr($primary_color); ?>;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e5e7eb;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table tr {
            border-bottom: 1px solid #e5e7eb;
        }

        .details-table tr:last-child {
            border-bottom: none;
        }

        .details-table td {
            padding: 12px 0;
            vertical-align: top;
        }

        .details-table td:first-child {
            color: #6b7280;
            width: 40%;
        }

        .details-table td:last-child {
            font-weight: 500;
            color: #1f2937;
        }

        /* Location rating stars */
        .rating-stars {
            color: #fbbf24;
            font-size: 14pt;
        }

        .rating-stars .empty {
            color: #d1d5db;
        }

        /* Disclaimer box */
        .disclaimer {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            border-radius: 0 8px 8px 0;
            padding: 20px;
            margin: 35px 0;
            font-size: 10pt;
            color: #92400e;
        }

        .disclaimer strong {
            display: block;
            margin-bottom: 5px;
            color: #78350f;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #f8fafc;
            border-top: 1px solid #e5e7eb;
            padding: 20px 50px;
            text-align: center;
            font-size: 9pt;
            color: #6b7280;
            line-height: 1.6;
        }

        .footer-company {
            font-weight: bold;
            color: #374151;
            font-size: 10pt;
        }

        .footer-contact {
            margin-top: 5px;
        }

        .footer-date {
            margin-top: 8px;
            color: #9ca3af;
            font-size: 8pt;
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
                vielen Dank für Ihr Interesse. Nachfolgend finden Sie Ihre persönliche Immobilienbewertung.
            </div>
        <?php endif; ?>

        <!-- Result Box -->
        <div class="result-container">
            <div class="result-label">Geschätzte Monatsmiete</div>
            <div class="result-value"><?php echo $monthly_rent; ?></div>
            <div class="result-range">
                Spanne: <?php echo $rent_min; ?> – <?php echo $rent_max; ?>
            </div>
            <div class="result-sqm">
                Quadratmeterpreis: <?php echo $price_per_sqm; ?>
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
                    <td><?php echo esc_html($city_name); ?></td>
                </tr>
                <tr>
                    <td>Zustand</td>
                    <td><?php echo esc_html($condition); ?></td>
                </tr>
                <tr>
                    <td>Lagebewertung</td>
                    <td>
                        <span class="rating-stars">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= $location_rating ? '★' : '<span class="empty">★</span>';
                            }
                            ?>
                        </span>
                        (<?php echo $location_rating; ?> von 5)
                    </td>
                </tr>
            </table>
        </div>

        <!-- Disclaimer -->
        <div class="disclaimer">
            <strong>Wichtiger Hinweis</strong>
            <?php echo esc_html($disclaimer); ?>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
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
</body>
</html>
