# Planung: Verkaufswert-Rechner (mode="sale_value")

## Übersicht

Erweiterung des Immobilien-Rechner-Pro um einen neuen Modus zur Berechnung des Immobilien-Verkaufswertes.

**Neuer Shortcode:**
```
[immobilien_rechner mode="sale_value" city="bad_oeynhausen"]
```

**Bestehende Modi bleiben unverändert:**
- `mode="rental"` - Mietwertberechnung
- `mode="comparison"` - Verkaufen vs. Vermieten

---

## 1. Frontend: Steps (React)

### Step-Reihenfolge

| # | Step | Komponente | Beschreibung |
|---|------|------------|--------------|
| 1 | Immobilientyp | `SalePropertyTypeStep.js` | Grundstück / Wohnung / Haus |
| 2 | Haustyp | `HouseTypeStep.js` | Nur bei "Haus": EFH, MFH, DHH, Reihenhaus, Bungalow |
| 3 | Größe | `SaleSizeStep.js` | Grundstücksgröße + Wohnfläche |
| 4 | Baujahr | `BuildYearStep.js` | Baujahr + letzte Modernisierung |
| 5 | Ausstattung Außen | `ExteriorFeaturesStep.js` | Balkon, Garage, Garten, Solar, Stellplatz, Terrasse |
| 6 | Ausstattung Innen | `InteriorFeaturesStep.js` | Aufzug, Dachboden, EBK, Kamin, Parkett, Keller |
| 7 | Qualität | `QualityStep.js` | Einfach / Normal / Gehoben / Luxuriös |
| 8 | Lage | `LocationRatingStep.js` | **Besteht bereits** - wiederverwenden |
| 9 | Nutzung | `UsageStep.js` | Eigennutzung / Leerstand / Vermietung |
| 10 | Verkaufsziel | `SalePurposeStep.js` | Verkauf/Kauf + Zeitrahmen |
| 11 | Adresse | `AddressStep.js` | PLZ, Stadt, Straße (für Genauigkeit) |
| 12 | Kontakt | `ContactStep.js` | **Besteht bereits** - wiederverwenden |
| 13 | Ergebnis | `SaleResultsDisplay.js` | Verkaufspreis-Anzeige |

### Bedingte Steps

```
Immobilientyp = "Grundstück" → Überspringe: Haustyp, Wohnfläche, Baujahr, Innenausstattung, Qualität
Immobilientyp = "Wohnung"    → Überspringe: Haustyp, Grundstücksgröße
Immobilientyp = "Haus"       → Alle Steps
```

---

## 2. Ergebnisausgabe (SaleResultsDisplay.js)

### Anzuzeigende Werte

```
┌─────────────────────────────────────────────────────────┐
│  Geschätzter Verkaufswert Ihrer Immobilie               │
│                                                         │
│  ████████████████████████████████████████               │
│           385.000 € - 425.000 €                         │
│  ████████████████████████████████████████               │
│                                                         │
│  Mittelwert: 405.000 €                                  │
│                                                         │
│  ─────────────────────────────────────────              │
│                                                         │
│  Preisberechnung basiert auf:                           │
│  • Grundstück: 500 m² × 180 €/m² = 90.000 €            │
│  • Gebäudewert: 120 m² × 2.800 €/m² = 336.000 €        │
│  • Ausstattung: +12.000 €                               │
│  • Lagefaktor: ×1.05                                    │
│  • Zustandsfaktor: ×0.95                                │
│                                                         │
│  ─────────────────────────────────────────              │
│                                                         │
│  Vergleichswerte in der Region:                         │
│  • Ø Verkaufspreis EFH: 380.000 €                       │
│  • Ø Preis/m² Wohnfläche: 3.200 €                       │
│  • Ø Preis/m² Grundstück: 175 €                         │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

### Berechnete Felder

| Feld | Beschreibung |
|------|--------------|
| `price_min` | Untere Preisspanne (-5%) |
| `price_max` | Obere Preisspanne (+5%) |
| `price_estimate` | Mittelwert/Schätzpreis |
| `price_per_sqm_living` | Preis pro m² Wohnfläche |
| `price_per_sqm_land` | Preis pro m² Grundstück |
| `land_value` | Anteil Grundstückswert |
| `building_value` | Anteil Gebäudewert |
| `features_value` | Anteil Ausstattung |
| `location_factor` | Angewandter Lagefaktor |
| `condition_factor` | Angewandter Zustandsfaktor |

---

## 3. PDF-Anpassung

### Neues PDF-Template: `pdf-sale-value.php`

**Seite 1: Zusammenfassung**
```
┌─────────────────────────────────────────┐
│  [LOGO]        IMMOBILIENBEWERTUNG      │
│                                         │
│  Objekt: Einfamilienhaus                │
│  Adresse: Musterstraße 123              │
│           32547 Bad Oeynhausen          │
│                                         │
│  ─────────────────────────────────────  │
│                                         │
│  GESCHÄTZTER VERKAUFSWERT               │
│                                         │
│      385.000 € - 425.000 €              │
│                                         │
│  ─────────────────────────────────────  │
│                                         │
│  Objektdaten:                           │
│  • Grundstück: 500 m²                   │
│  • Wohnfläche: 120 m²                   │
│  • Baujahr: 1985                        │
│  • Modernisierung: vor 4-9 Jahren       │
│  • Haustyp: Einfamilienhaus             │
│  • Qualität: Gehoben                    │
│  • Lage: Sehr gute Lage                 │
│                                         │
└─────────────────────────────────────────┘
```

**Seite 2: Details**
```
┌─────────────────────────────────────────┐
│  WERTERMITTLUNG IM DETAIL               │
│                                         │
│  Grundstückswert:                       │
│  500 m² × 180 €/m² = 90.000 €          │
│                                         │
│  Gebäudewert:                           │
│  120 m² × 2.800 €/m² = 336.000 €       │
│                                         │
│  Ausstattungszuschläge:                 │
│  • Garage: +8.000 €                     │
│  • Einbauküche: +5.000 €                │
│  • Garten: +4.000 €                     │
│                                         │
│  Faktoren:                              │
│  • Lagefaktor: ×1.05                    │
│  • Baujahr/Zustand: ×0.95               │
│  • Qualität: ×1.10                      │
│                                         │
│  ─────────────────────────────────────  │
│                                         │
│  HINWEIS                                │
│  Diese Bewertung dient als Orientierung │
│  und ersetzt keine professionelle       │
│  Wertermittlung durch einen Gutachter.  │
│                                         │
└─────────────────────────────────────────┘
```

### PDF-Logik

```php
// In class-pdf-generator.php
public function generate($lead) {
    $mode = $lead->mode;

    if ($mode === 'sale_value') {
        $template = 'pdf-sale-value.php';
    } elseif ($mode === 'comparison') {
        $template = 'pdf-comparison.php';
    } else {
        $template = 'pdf-rental.php';  // Default
    }

    include IRP_PLUGIN_DIR . 'includes/templates/' . $template;
}
```

---

## 4. Backend: Matrix & Daten

### Prinzip: Einfach halten

**Strategie:** Bestehende Struktur erweitern, keine komplett neue Matrix.

### Erweiterung der Stadt-Konfiguration

```php
// Bestehend (bleibt)
'cities' => [
    [
        'id' => 'bad_oeynhausen',
        'name' => 'Bad Oeynhausen',
        'base_price' => 8.50,           // Mietpreis/m²
        'size_degression' => 0.20,
        'sale_factor' => 25,            // Für Vergleichsrechner

        // NEU für Verkaufswert
        'land_price_per_sqm' => 180,    // Grundstückspreis/m²
        'building_price_per_sqm' => 2800, // Gebäudepreis/m² (Neubau-Basis)
    ],
]
```

### Neue Faktoren-Tabellen (eigener Tab im Admin)

**Tab: "Verkaufswert-Faktoren"**

```php
'sale_value_settings' => [

    // Haustyp-Faktoren
    'house_type_multipliers' => [
        'single_family' => ['name' => 'Einfamilienhaus', 'multiplier' => 1.00],
        'multi_family' => ['name' => 'Mehrfamilienhaus', 'multiplier' => 1.15],
        'semi_detached' => ['name' => 'Doppelhaushälfte', 'multiplier' => 0.95],
        'townhouse_middle' => ['name' => 'Mittelreihenhaus', 'multiplier' => 0.88],
        'townhouse_end' => ['name' => 'Endreihenhaus', 'multiplier' => 0.92],
        'bungalow' => ['name' => 'Bungalow', 'multiplier' => 1.05],
    ],

    // Qualitäts-Faktoren
    'quality_multipliers' => [
        'simple' => ['name' => 'Einfach', 'multiplier' => 0.85],
        'normal' => ['name' => 'Normal', 'multiplier' => 1.00],
        'upscale' => ['name' => 'Gehoben', 'multiplier' => 1.15],
        'luxury' => ['name' => 'Luxuriös', 'multiplier' => 1.35],
    ],

    // Modernisierungs-Faktoren
    'modernization_multipliers' => [
        '1-3_years' => ['name' => 'Vor 1-3 Jahren', 'multiplier' => 1.10],
        '4-9_years' => ['name' => 'Vor 4-9 Jahren', 'multiplier' => 1.05],
        '10-15_years' => ['name' => 'Vor 10-15 Jahren', 'multiplier' => 1.00],
        'over_15_years' => ['name' => 'Vor mehr als 15 Jahren', 'multiplier' => 0.90],
        'never' => ['name' => 'Noch nie', 'multiplier' => 0.85],
    ],

    // Altersabschlag (pro Jahr ab Baujahr)
    'age_depreciation' => [
        'rate_per_year' => 0.01,        // 1% pro Jahr
        'max_depreciation' => 0.40,     // Maximal 40% Abschlag
        'base_year' => 2025,            // Bezugsjahr
    ],

    // Ausstattungs-Zuschläge (absolute Werte in €)
    'exterior_features' => [
        'balcony' => ['name' => 'Balkon', 'value' => 5000],
        'garage' => ['name' => 'Garage', 'value' => 15000],
        'garden' => ['name' => 'Garten', 'value' => 8000],
        'solar' => ['name' => 'Solaranlage', 'value' => 12000],
        'parking' => ['name' => 'Stellplatz', 'value' => 8000],
        'terrace' => ['name' => 'Terrasse', 'value' => 6000],
    ],

    'interior_features' => [
        'elevator' => ['name' => 'Aufzug', 'value' => 20000],
        'attic' => ['name' => 'Dachboden', 'value' => 5000],
        'fitted_kitchen' => ['name' => 'Einbauküche', 'value' => 8000],
        'fireplace' => ['name' => 'Kamin', 'value' => 6000],
        'parquet' => ['name' => 'Parkettboden', 'value' => 4000],
        'cellar' => ['name' => 'Keller', 'value' => 10000],
    ],
]
```

### Admin-UI Struktur

```
Immo Rechner
├── Dashboard
├── Leads
├── Matrix & Daten
│   ├── Tab: Städte (bestehend + neue Felder)
│   ├── Tab: Mietwert-Faktoren (bestehend)
│   ├── Tab: Verkaufswert-Faktoren (NEU)
│   └── Tab: Lagefaktoren (bestehend)
├── Shortcode
├── Settings
└── Integrationen
```

---

## 5. Berechnungslogik

### Neue Klasse: `class-sale-calculator.php`

```php
class IRP_Sale_Calculator {

    public function calculate(array $data): array {
        $city_data = $this->get_city_data($data['city_id']);
        $settings = $this->get_sale_settings();

        // 1. Basiswerte
        $land_value = $data['land_size'] * $city_data['land_price_per_sqm'];
        $building_value = $data['living_space'] * $city_data['building_price_per_sqm'];

        // 2. Faktoren anwenden
        $house_type_factor = $settings['house_type_multipliers'][$data['house_type']]['multiplier'] ?? 1.0;
        $quality_factor = $settings['quality_multipliers'][$data['quality']]['multiplier'] ?? 1.0;
        $modernization_factor = $settings['modernization_multipliers'][$data['modernization']]['multiplier'] ?? 1.0;
        $location_factor = $this->get_location_factor($data['location_rating']);
        $age_factor = $this->calculate_age_factor($data['build_year'], $settings);

        // 3. Ausstattungswert
        $features_value = $this->calculate_features_value($data['exterior_features'], $data['interior_features'], $settings);

        // 4. Gesamtberechnung
        $building_adjusted = $building_value
            * $house_type_factor
            * $quality_factor
            * $modernization_factor
            * $age_factor
            * $location_factor;

        $total = $land_value + $building_adjusted + $features_value;

        // 5. Preisspanne (±5%)
        return [
            'price_estimate' => round($total, -3),  // Auf 1000er runden
            'price_min' => round($total * 0.95, -3),
            'price_max' => round($total * 1.05, -3),
            'land_value' => round($land_value, -2),
            'building_value' => round($building_adjusted, -2),
            'features_value' => round($features_value, -2),
            'price_per_sqm_living' => round($total / $data['living_space'], 0),
            'price_per_sqm_land' => round($land_value / $data['land_size'], 0),
            'factors' => [
                'house_type' => $house_type_factor,
                'quality' => $quality_factor,
                'modernization' => $modernization_factor,
                'location' => $location_factor,
                'age' => $age_factor,
            ],
        ];
    }

    private function calculate_age_factor(int $build_year, array $settings): float {
        $age = $settings['age_depreciation']['base_year'] - $build_year;
        $depreciation = min(
            $age * $settings['age_depreciation']['rate_per_year'],
            $settings['age_depreciation']['max_depreciation']
        );
        return max(0.6, 1 - $depreciation);  // Mindestens 60%
    }
}
```

---

## 6. Datenbank-Erweiterung

### Neue Felder in `irp_leads`

```sql
ALTER TABLE {prefix}irp_leads ADD COLUMN house_type VARCHAR(50) NULL;
ALTER TABLE {prefix}irp_leads ADD COLUMN land_size INT NULL;
ALTER TABLE {prefix}irp_leads ADD COLUMN build_year INT NULL;
ALTER TABLE {prefix}irp_leads ADD COLUMN modernization VARCHAR(50) NULL;
ALTER TABLE {prefix}irp_leads ADD COLUMN quality_level VARCHAR(50) NULL;
ALTER TABLE {prefix}irp_leads ADD COLUMN usage_type VARCHAR(50) NULL;
ALTER TABLE {prefix}irp_leads ADD COLUMN sale_purpose VARCHAR(50) NULL;
ALTER TABLE {prefix}irp_leads ADD COLUMN sale_timeframe VARCHAR(50) NULL;
ALTER TABLE {prefix}irp_leads ADD COLUMN address_street VARCHAR(255) NULL;
ALTER TABLE {prefix}irp_leads ADD COLUMN address_zip VARCHAR(20) NULL;
```

### Migration

```php
// In Aktivierungs-Hook oder Update-Check
public function maybe_upgrade_database() {
    $current_version = get_option('irp_db_version', '1.0.0');

    if (version_compare($current_version, '1.6.0', '<')) {
        $this->upgrade_to_160();
        update_option('irp_db_version', '1.6.0');
    }
}
```

---

## 7. Propstack-Integration

### Erweiterte Aktivität für Verkaufswert

```php
// Titel
"Anfrage über Immobilien-Rechner Pro - Verkaufswert-Ermittlung"

// Body
"Objekttyp: Einfamilienhaus | Grundstück: 500 m² | Wohnfläche: 120 m² |
Baujahr: 1985 | Qualität: Gehoben | Lage: Sehr gut |
Geschätzter Wert: 385.000 - 425.000 Euro | Verkaufsziel: 1-3 Monate"
```

---

## 8. Implementierungs-Reihenfolge

### Phase 1: Backend-Grundlagen
1. [ ] Datenbank-Migration erstellen
2. [ ] `class-sale-calculator.php` erstellen
3. [ ] Matrix um Verkaufswert-Felder erweitern
4. [ ] Admin-Tab "Verkaufswert-Faktoren" erstellen

### Phase 2: Frontend-Steps
5. [ ] `SalePropertyTypeStep.js` erstellen
6. [ ] `HouseTypeStep.js` erstellen
7. [ ] `SaleSizeStep.js` erstellen
8. [ ] `BuildYearStep.js` erstellen
9. [ ] `ExteriorFeaturesStep.js` erstellen
10. [ ] `InteriorFeaturesStep.js` erstellen
11. [ ] `QualityStep.js` erstellen
12. [ ] `UsageStep.js` erstellen
13. [ ] `SalePurposeStep.js` erstellen
14. [ ] `AddressStep.js` erstellen

### Phase 3: Ergebnis & Ausgabe
15. [ ] `SaleResultsDisplay.js` erstellen
16. [ ] PDF-Template `pdf-sale-value.php` erstellen
17. [ ] E-Mail-Template für Verkaufswert erstellen

### Phase 4: Integration
18. [ ] Shortcode-Handler für `mode="sale_value"` erweitern
19. [ ] Propstack-Integration anpassen
20. [ ] Lead-Verwaltung für neue Felder erweitern

### Phase 5: Testing & Feinschliff
21. [ ] Alle Steps testen
22. [ ] Berechnung validieren
23. [ ] PDF prüfen
24. [ ] Responsive Design testen

---

## 9. Offene Fragen

- [ ] Soll die Adresse (Straße) für Google Maps Validierung genutzt werden?
- [ ] Sollen Vergleichswerte aus der Region angezeigt werden?
- [ ] Wie detailliert soll die Preisaufschlüsselung im Ergebnis sein?
- [ ] Brauchen wir unterschiedliche Gebäudepreise für Wohnung vs. Haus?
- [ ] Soll es einen "Schnellrechner" ohne alle Steps geben?

---

## 10. Dateistruktur (Neu)

```
src/
├── components/
│   ├── App.js                      (erweitern)
│   ├── steps/
│   │   ├── PropertyTypeStep.js     (bestehend)
│   │   ├── SalePropertyTypeStep.js (NEU)
│   │   ├── HouseTypeStep.js        (NEU)
│   │   ├── SaleSizeStep.js         (NEU)
│   │   ├── BuildYearStep.js        (NEU)
│   │   ├── ExteriorFeaturesStep.js (NEU)
│   │   ├── InteriorFeaturesStep.js (NEU)
│   │   ├── QualityStep.js          (NEU)
│   │   ├── UsageStep.js            (NEU)
│   │   ├── SalePurposeStep.js      (NEU)
│   │   ├── AddressStep.js          (NEU)
│   │   └── ...
│   └── SaleResultsDisplay.js       (NEU)
│
includes/
├── class-calculator.php            (bestehend)
├── class-sale-calculator.php       (NEU)
├── class-pdf-generator.php         (erweitern)
└── templates/
    ├── pdf.php                     (bestehend)
    └── pdf-sale-value.php          (NEU)

admin/
└── views/
    └── matrix.php                  (erweitern: neuer Tab)
```

---

**Erstellt:** 2026-01-17
**Version:** 1.0
**Status:** Entwurf
