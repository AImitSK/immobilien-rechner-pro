# Immobilien Rechner Pro

Professionelles WordPress-Plugin für Mietwertberechnung und Verkaufen-vs-Vermieten-Vergleich. White-Label-Lösung für Immobilienmakler.

## Features

- **Mietwert-Rechner**: Schätzung potenzieller Mieteinnahmen basierend auf Immobiliendaten
- **Verkaufen vs. Vermieten Vergleich**: Visuelle Break-Even-Analyse mit interaktiven Charts
- **Städte-System**: Individuelle Konfiguration pro Stadt mit eigenem Basis-Mietpreis und Vervielfältiger
- **Lead-Generierung**: Erfassung und Verwaltung von Leads mit E-Mail-Benachrichtigungen
- **White-Label-Ready**: Vollständig anpassbares Branding (Farben, Logo, Firmeninfo)
- **Responsives Design**: Funktioniert auf allen Geräten
- **DSGVO-konform**: Integrierte Einwilligungsverwaltung

## Anforderungen

- WordPress 6.0+
- PHP 8.0+
- Node.js 18+ (für Entwicklung)

## Installation

### Für Entwicklung

1. Repository in das WordPress-Plugins-Verzeichnis klonen:
   ```bash
   cd wp-content/plugins/
   git clone <your-repo-url> immobilien-rechner-pro
   ```

2. Abhängigkeiten installieren:
   ```bash
   cd immobilien-rechner-pro
   npm install
   ```

3. React-Frontend bauen:
   ```bash
   npm run build
   ```

4. Plugin aktivieren unter WordPress Admin → Plugins

### Für Produktion

1. Release-ZIP herunterladen
2. Hochladen über WordPress Admin → Plugins → Installieren → Plugin hochladen
3. Plugin aktivieren

## Entwicklung

Entwicklungsserver mit Hot Reloading starten:

```bash
npm run start
```

Für Produktion bauen:

```bash
npm run build
```

JavaScript linten:

```bash
npm run lint:js
```

---

## Shortcode-Verwendung

### Basis-Shortcode

```
[immobilien_rechner]
```

Zeigt den kompletten Rechner mit Modus-Auswahl und Städte-Dropdown.

### Shortcode-Parameter

| Parameter | Werte | Beschreibung |
|-----------|-------|--------------|
| `mode` | `""`, `"rental"`, `"comparison"` | Modus festlegen oder Benutzer wählen lassen |
| `city_id` | z.B. `"muenchen"`, `"berlin"` | Stadt festlegen (überspringt Standort-Auswahl) |
| `theme` | `"light"`, `"dark"` | Farbschema |
| `show_branding` | `"true"`, `"false"` | Firmenbranding anzeigen/ausblenden |

### Beispiele

**Nur Mietwert-Rechner:**
```
[immobilien_rechner mode="rental"]
```

**Nur Verkaufen vs. Vermieten Vergleich:**
```
[immobilien_rechner mode="comparison"]
```

**Für eine bestimmte Stadt (z.B. München):**
```
[immobilien_rechner city_id="muenchen"]
```

**Kombination: Mietwert-Rechner für Berlin mit Dark-Theme:**
```
[immobilien_rechner mode="rental" city_id="berlin" theme="dark"]
```

**Vollständig konfiguriert:**
```
[immobilien_rechner mode="comparison" city_id="frankfurt" theme="light" show_branding="true"]
```

### Verhalten der Parameter

| Konfiguration | Step-Ablauf |
|---------------|-------------|
| Kein `mode` | Benutzer wählt erst Modus (Mietwert oder Vergleich) |
| `mode="rental"` oder `mode="comparison"` | Modus-Auswahl wird übersprungen |
| Kein `city_id` | Benutzer wählt Stadt aus Dropdown |
| `city_id="xxx"` | Standort-Auswahl wird übersprungen, Stadt ist fest |

---

## Admin-Bereich

### Dashboard
**WordPress Admin → Immo Rechner → Dashboard**

- Übersicht über Leads und Berechnungen
- Schnellstart-Anleitung mit Shortcode-Beispielen
- Letzte Leads auf einen Blick

### Leads
**WordPress Admin → Immo Rechner → Leads**

- Liste aller erfassten Leads
- Filterung nach Modus (Mietwert/Vergleich)
- Detailansicht mit Berechnungsdaten
- CSV-Export

### Shortcode Generator
**WordPress Admin → Immo Rechner → Shortcode**

Komfortabler visueller Generator für Shortcodes:

- **Modus auswählen**: Per Klick zwischen "Benutzer wählt", "Nur Mietwert" oder "Nur Vergleich"
- **Stadt auswählen**: Aus konfigurierten Städten wählen oder Benutzer wählen lassen
- **Design anpassen**: Theme (hell/dunkel) und Branding-Optionen
- **Live-Vorschau**: Der Shortcode aktualisiert sich sofort bei Änderungen
- **Ein-Klick-Kopieren**: Shortcode direkt in die Zwischenablage kopieren
- **Step-Vorschau**: Zeigt welche Schritte der Benutzer durchlaufen wird

### Matrix & Daten
**WordPress Admin → Immo Rechner → Matrix & Daten**

Zentrale Konfiguration aller Berechnungsparameter in 4 Tabs:

#### Tab: Städte
Verwaltung der Städte für den Rechner:

| Feld | Beschreibung |
|------|--------------|
| Stadt-ID | Eindeutige ID für Shortcode (z.B. `muenchen`, `berlin`) |
| Name | Anzeigename (z.B. "München", "Berlin") |
| Basis-Mietpreis | €/m² Ausgangswert für 70 m² Referenzwohnung |
| Degression | Größendegression (0.00-0.50), Standard: 0.20 |
| Vervielfältiger | Faktor für Kaufpreisberechnung (Jahresnettokaltmieten) |

**Beispiel Vervielfältiger:** Bei 1.000 € Monatsmiete und Vervielfältiger 25 → Kaufpreis = 300.000 €

#### Tab: Multiplikatoren

**Zustands-Multiplikatoren:**
| Zustand | Standard | Auswirkung |
|---------|----------|------------|
| Neubau | 1,25 | +25% |
| Renoviert | 1,10 | +10% |
| Guter Zustand | 1,00 | ±0% |
| Renovierungsbedürftig | 0,80 | -20% |

**Objekttyp-Multiplikatoren:**
| Typ | Standard | Auswirkung |
|-----|----------|------------|
| Wohnung | 1,00 | ±0% |
| Haus | 1,15 | +15% |
| Gewerbe | 0,85 | -15% |

#### Tab: Ausstattung
Zuschläge pro m² für Ausstattungsmerkmale:

| Merkmal | Standard €/m² | Bei 70m² Wohnung |
|---------|---------------|------------------|
| Balkon | 0,50 | +35 €/Monat |
| Terrasse | 0,75 | +53 €/Monat |
| Garten | 1,00 | +70 €/Monat |
| Aufzug | 0,30 | +21 €/Monat |
| Stellplatz | 0,40 | +28 €/Monat |
| Garage | 0,60 | +42 €/Monat |
| Keller | 0,20 | +14 €/Monat |
| Einbauküche | 0,50 | +35 €/Monat |
| Fußbodenheizung | 0,40 | +28 €/Monat |
| Gäste-WC | 0,25 | +18 €/Monat |
| Barrierefrei | 0,30 | +21 €/Monat |

#### Tab: Globale Parameter
| Parameter | Beschreibung | Standard |
|-----------|--------------|----------|
| Kapitalanlage-Zinssatz | Angenommener Zinssatz für alternative Anlage | 3,0% |
| Wertsteigerung Immobilie | Jährliche Wertsteigerung | 2,0% |
| Jährliche Mietsteigerung | Für Prognoseberechnung | 2,0% |

### Settings
**WordPress Admin → Immo Rechner → Settings**

- **Branding**: Firmenname, Logo, Primär-/Sekundärfarbe
- **Benachrichtigungen**: E-Mail für Lead-Benachrichtigungen
- **Rechner-Defaults**: Instandhaltungsrate, Leerstandsquote, Maklerkosten
- **Datenschutz**: Einwilligungspflicht, Datenschutz-URL

---

## Berechnungslogik

### Mietwert-Berechnung

```
Mietpreis/m² = Basis-Mietpreis (Stadt)
             × Größendegression-Faktor
             × Zustands-Multiplikator
             × Objekttyp-Multiplikator
             + Ausstattungs-Zuschläge
             × Alters-Anpassung

Monatliche Miete = Fläche × Mietpreis/m²
```

**Größendegression (degressive Preisbildung):**

Der Basis-Mietpreis bezieht sich auf eine 70 m² Referenzwohnung. Größere Wohnungen werden pro m² günstiger, kleinere teurer.

**Formel:**
```
m²-Preis = Basis-Preis × (70 / Fläche)^Degression
```

**Beispielrechnung mit Degression = 0.20:**

| Fläche | Berechnung | Faktor | Bei 10€/m² Basis |
|--------|------------|--------|------------------|
| 35 m² | (70/35)^0.20 | ×1,15 | **11,50 €/m²** |
| 50 m² | (70/50)^0.20 | ×1,07 | **10,70 €/m²** |
| **70 m²** | (70/70)^0.20 | ×1,00 | **10,00 €/m²** |
| 100 m² | (70/100)^0.20 | ×0,93 | **9,30 €/m²** |
| 140 m² | (70/140)^0.20 | ×0,87 | **8,70 €/m²** |
| 200 m² | (70/200)^0.20 | ×0,80 | **8,00 €/m²** |

> Die Degression ist pro Stadt konfigurierbar (Standard: 0.20). Höhere Werte = stärkere Preisabnahme bei großen Wohnungen. 0 = keine Größenanpassung.

**Alters-Anpassung:**
| Baujahr | Faktor |
|---------|--------|
| ≥ 2015 | ×1,10 |
| ≥ 2000 | ×1,05 |
| ≥ 1990 | ×1,00 |
| ≥ 1970 | ×0,95 |
| ≥ 1950 | ×0,90 |
| < 1950 (Altbau) | ×1,05 |

### Verkaufen vs. Vermieten Vergleich

**Miet-Szenario (jährlich):**
```
Bruttojahresmiete = Monatsmiete × 12
Leerstandsverlust = Bruttojahresmiete × Leerstandsquote
Instandhaltung = Immobilienwert × Instandhaltungsrate
Hypothekenzinsen = Restschuld × Hypothekenzins
Nettojahreseinkommen = Bruttojahresmiete - Leerstand - Instandhaltung - Zinsen
```

**Verkaufs-Szenario:**
```
Verkaufskosten = Immobilienwert × Maklerprovision
Nettoerlös = Immobilienwert - Restschuld - Verkaufskosten
```

**Renditeberechnung:**
```
Bruttorendite = (Bruttojahresmiete / Immobilienwert) × 100%
Nettorendite = (Nettojahreseinkommen / Immobilienwert) × 100%
```

**Empfehlungslogik:**
- Nettorendite ≥ 5%: Vermietung empfohlen
- Break-Even ≤ 5 Jahre: Vermietung empfohlen
- Spekulationssteuer (< 10 Jahre Haltedauer): Warnung

---

## REST API Endpoints

| Endpoint | Method | Beschreibung |
|----------|--------|--------------|
| `/irp/v1/calculate/rental` | POST | Mietwert berechnen |
| `/irp/v1/calculate/comparison` | POST | Verkaufen vs. Vermieten berechnen |
| `/irp/v1/leads` | POST | Lead übermitteln |
| `/irp/v1/cities` | GET | Alle konfigurierten Städte abrufen |
| `/irp/v1/locations` | GET | Ortssuche (Autocomplete) |

---

## Dateistruktur

```
immobilien-rechner-pro/
├── immobilien-rechner-pro.php    # Haupt-Plugin-Datei
├── includes/                      # PHP-Klassen
│   ├── class-activator.php       # Datenbank-Setup
│   ├── class-assets.php          # Script/Style-Loading
│   ├── class-calculator.php      # Berechnungslogik
│   ├── class-leads.php           # Lead-Verwaltung
│   ├── class-rest-api.php        # REST API Endpoints
│   └── class-shortcode.php       # Shortcode-Handler
├── admin/                         # Admin-Panel
│   ├── class-admin.php
│   ├── views/
│   │   ├── dashboard.php
│   │   ├── leads-list.php
│   │   ├── lead-detail.php
│   │   ├── matrix.php            # Städte & Multiplikatoren
│   │   ├── shortcode-generator.php # Shortcode Generator
│   │   └── settings.php
│   ├── css/
│   └── js/
├── src/                           # React-Source (Entwicklung)
│   ├── index.js                  # Einstiegspunkt
│   ├── components/
│   │   ├── App.js                # Haupt-Komponente
│   │   ├── ModeSelector.js       # Modus-Auswahl
│   │   ├── RentalCalculator.js   # Mietwert-Wizard
│   │   ├── ComparisonCalculator.js # Vergleichs-Wizard
│   │   ├── ResultsDisplay.js     # Ergebnis-Anzeige
│   │   ├── LeadForm.js           # Kontaktformular
│   │   ├── ThankYou.js           # Danke-Seite
│   │   ├── ProgressBar.js        # Fortschrittsanzeige
│   │   ├── RentalGauge.js        # Marktposition-Gauge
│   │   └── steps/
│   │       ├── CityStep.js       # Stadt-Auswahl
│   │       ├── PropertyTypeStep.js
│   │       ├── PropertyDetailsStep.js
│   │       ├── LocationStep.js
│   │       ├── ConditionStep.js
│   │       ├── FeaturesStep.js
│   │       └── FinancialStep.js
│   ├── hooks/
│   └── styles/
├── build/                         # Kompiliertes React (Produktion)
└── languages/                     # Übersetzungen
```

---

## Datenbank-Tabellen

### wp_irp_leads
| Spalte | Typ | Beschreibung |
|--------|-----|--------------|
| id | bigint | Primary Key |
| name | varchar(255) | Name des Leads |
| email | varchar(255) | E-Mail (Pflicht) |
| phone | varchar(50) | Telefon |
| mode | varchar(20) | 'rental' oder 'comparison' |
| property_type | varchar(50) | Immobilientyp |
| property_size | decimal | Fläche in m² |
| property_location | varchar(255) | Standort/Stadt |
| zip_code | varchar(10) | PLZ |
| calculation_data | longtext | JSON mit Berechnungsergebnissen |
| consent | tinyint | Einwilligung gegeben |
| source | varchar(100) | Quelle |
| created_at | datetime | Erstellungsdatum |

### wp_irp_calculations
| Spalte | Typ | Beschreibung |
|--------|-----|--------------|
| id | bigint | Primary Key |
| lead_id | bigint | Verknüpfung zu Lead (optional) |
| session_id | varchar(64) | Session für anonyme Nutzung |
| mode | varchar(20) | 'rental' oder 'comparison' |
| input_data | longtext | JSON mit Eingabedaten |
| result_data | longtext | JSON mit Ergebnissen |
| created_at | datetime | Erstellungsdatum |

---

## Anpassungen

### Neue Stadt hinzufügen

1. Admin → Immo Rechner → Matrix & Daten → Städte
2. "Stadt hinzufügen" klicken
3. Stadt-ID (z.B. `hannover`), Name, Basis-Mietpreis und Vervielfältiger eingeben
4. Speichern

Die Stadt ist sofort im Dropdown verfügbar und kann per Shortcode angesprochen werden:
```
[immobilien_rechner city_id="hannover"]
```

### Multiplikatoren anpassen

Admin → Matrix & Daten → Multiplikatoren

### Feature-Zuschläge anpassen

Admin → Matrix & Daten → Ausstattung

### Übersetzungen

Das Plugin ist übersetzungsbereit. POT-Datei generieren:

```bash
wp i18n make-pot . languages/immobilien-rechner-pro.pot
```

---

## Technologie-Stack

- **Backend**: PHP 8.0+, WordPress REST API
- **Frontend**: React 18, WordPress Element
- **Charts**: ApexCharts
- **Icons**: Heroicons
- **Animationen**: Framer Motion
- **Styling**: SCSS

---

## Lizenz

GPL v2 oder später

## Support

Für Support und Feature-Requests bitte ein Issue auf GitHub öffnen.
