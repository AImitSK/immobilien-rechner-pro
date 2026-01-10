# Immobilien Rechner Pro

Professionelles WordPress-Plugin für Mietwertberechnung und Verkaufen-vs-Vermieten-Vergleich. White-Label-Lösung für Immobilienmakler.

## Features

- **Mietwert-Rechner**: Schätzung potenzieller Mieteinnahmen basierend auf Immobiliendaten
- **Verkaufen vs. Vermieten Vergleich**: Visuelle Break-Even-Analyse mit interaktiven Charts
- **Städte-System**: Individuelle Konfiguration pro Stadt mit eigenem Basis-Mietpreis und Vervielfältiger
- **Lage-Bewertung**: 5-stufige Lage-Bewertung mit konfigurierbaren Multiplikatoren und Google Maps Integration
- **Lead-Generierung**: Erfassung und Verwaltung von Leads mit E-Mail-Benachrichtigungen
- **E-Mail mit PDF**: Automatischer Versand einer professionellen Immobilienbewertung als PDF an Leads
- **White-Label-Ready**: Vollständig anpassbares Branding (Farben, Logo, Firmeninfo, mehrzeilige Signatur)
- **reCAPTCHA v3**: Spam-Schutz für Lead-Formulare
- **GitHub Auto-Updater**: Automatische Updates direkt von GitHub Releases
- **Responsives Design**: Funktioniert auf allen Geräten
- **DSGVO-konform**: Integrierte Einwilligungsverwaltung

## Anforderungen

- WordPress 6.0+
- PHP 7.4+
- Node.js 18+ (für Entwicklung)

## Installation

### Für Entwicklung

1. Repository in das WordPress-Plugins-Verzeichnis klonen:
   ```bash
   cd wp-content/plugins/
   git clone https://github.com/AImitSK/immobilien-rechner-pro.git
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

1. Release-ZIP von [GitHub Releases](https://github.com/AImitSK/immobilien-rechner-pro/releases) herunterladen
2. Hochladen über WordPress Admin → Plugins → Installieren → Plugin hochladen
3. Plugin aktivieren

### Updates

Das Plugin prüft automatisch auf neue Versionen bei GitHub. Verfügbare Updates erscheinen unter **Dashboard → Aktualisierungen**.

Manuell prüfen: **Immo Rechner → Settings → Nach Updates suchen**

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

---

## Admin-Bereich

### Dashboard
**WordPress Admin → Immo Rechner → Dashboard**

- Übersicht über Leads und Berechnungen
- Schnellstart-Anleitung mit Shortcode-Beispielen
- Letzte Leads auf einen Blick

### Leads
**WordPress Admin → Immo Rechner → Leads**

- Liste aller erfassten Leads mit Status-Anzeige
- E-Mail-Versand-Status (✓ gesendet / – ausstehend)
- Filterung nach Modus und Status
- Detailansicht mit Berechnungsdaten
- CSV-Export

### Shortcode Generator
**WordPress Admin → Immo Rechner → Shortcode**

Visueller Generator für Shortcodes mit Live-Vorschau.

### Matrix & Daten
**WordPress Admin → Immo Rechner → Matrix & Daten**

Zentrale Konfiguration aller Berechnungsparameter:

- **Städte**: Stadt-ID, Name, Basis-Mietpreis, Degression, Vervielfältiger
- **Multiplikatoren**: Zustand und Objekttyp
- **Ausstattung**: Zuschläge pro m² für Features
- **Lage-Faktoren**: 5-stufige Bewertung mit Multiplikatoren
- **Globale Parameter**: Zinssätze, Wertsteigerung

### Settings
**WordPress Admin → Immo Rechner → Settings**

Einstellungen in 5 Tabs organisiert:

#### Tab: Allgemein
- Darstellung (Breite, Farben)
- Rechner-Standardwerte
- Datenschutz-Einstellungen

#### Tab: Branding & Kontakt
- Logo mit konfigurierbarer Breite
- Firmenname (bis zu 3 Zeilen)
- Adresse und Kontaktdaten
- Wird in E-Mail-Signatur und PDF-Footer verwendet

#### Tab: E-Mail
- E-Mail-Versand aktivieren/deaktivieren
- Absender konfigurieren
- E-Mail-Betreff und Inhalt mit WYSIWYG-Editor
- Verfügbare Variablen: `{name}`, `{city}`, `{property_type}`, `{size}`, `{result_value}`
- Test-E-Mail versenden

#### Tab: reCAPTCHA
- Google reCAPTCHA v3 Keys
- Mindest-Score konfigurieren

#### Tab: Google Maps
- API Key für Karten und Autocomplete
- Karte im Lage-Step anzeigen

---

## E-Mail & PDF Feature

Bei aktivierter E-Mail-Funktion erhält jeder Lead automatisch eine professionelle E-Mail mit:

- **Personalisierter Anrede** aus dem Lead-Namen
- **Anpassbarer Text** über den WYSIWYG-Editor
- **PDF-Anhang** mit:
  - Zentriertes Firmenlogo (Breite konfigurierbar)
  - Berechnungsergebnis mit Mietspanne
  - Objektdaten im Überblick
  - Lage-Bewertung mit Sternen
  - Disclaimer-Hinweis
  - Footer mit Firmenkontaktdaten

Die E-Mail wird nach der Response via `register_shutdown_function` gesendet, sodass der Nutzer nicht warten muss.

---

## Berechnungslogik

### Mietwert-Berechnung

```
Mietpreis/m² = Basis-Mietpreis (Stadt)
             × Größendegression-Faktor
             × Lage-Multiplikator
             × Zustands-Multiplikator
             × Objekttyp-Multiplikator
             + Ausstattungs-Zuschläge
             × Alters-Anpassung

Monatliche Miete = Fläche × Mietpreis/m²
```

**Größendegression:** Der Basis-Mietpreis bezieht sich auf eine 70 m² Referenzwohnung. Größere Wohnungen werden pro m² günstiger, kleinere teurer.

### Verkaufen vs. Vermieten Vergleich

- Brutto- und Nettorendite
- Break-Even-Analyse
- Empfehlung basierend auf Rendite und Haltedauer

---

## REST API Endpoints

| Endpoint | Method | Beschreibung |
|----------|--------|--------------|
| `/irp/v1/calculate/rental` | POST | Mietwert berechnen |
| `/irp/v1/calculate/comparison` | POST | Verkaufen vs. Vermieten berechnen |
| `/irp/v1/leads` | POST | Lead übermitteln (legacy) |
| `/irp/v1/leads/partial` | POST | Partial Lead erstellen |
| `/irp/v1/leads/complete` | POST | Partial Lead vervollständigen |
| `/irp/v1/cities` | GET | Alle konfigurierten Städte abrufen |

---

## Dateistruktur

```
immobilien-rechner-pro/
├── immobilien-rechner-pro.php    # Haupt-Plugin-Datei
├── includes/                      # PHP-Klassen
│   ├── class-activator.php       # Datenbank-Setup
│   ├── class-assets.php          # Script/Style-Loading
│   ├── class-calculator.php      # Berechnungslogik
│   ├── class-email.php           # E-Mail-Versand
│   ├── class-github-updater.php  # Auto-Updates von GitHub
│   ├── class-leads.php           # Lead-Verwaltung
│   ├── class-pdf-generator.php   # PDF-Generierung mit DOMPDF
│   ├── class-recaptcha.php       # reCAPTCHA v3
│   ├── class-rest-api.php        # REST API Endpoints
│   ├── class-shortcode.php       # Shortcode-Handler
│   └── templates/
│       ├── email.php             # E-Mail HTML Template
│       └── pdf.php               # PDF HTML Template
├── admin/                         # Admin-Panel
│   ├── class-admin.php
│   └── views/
├── vendor/                        # Gebündelte Libraries
│   ├── autoload.php              # Custom Autoloader
│   ├── dompdf/                   # DOMPDF 2.0.4
│   ├── php-font-lib/
│   └── php-svg-lib/
├── src/                           # React-Source (Entwicklung)
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
| newsletter_consent | tinyint | Newsletter-Einwilligung |
| status | varchar(20) | 'partial' oder 'complete' |
| recaptcha_score | decimal(3,2) | reCAPTCHA Score |
| ip_address | varchar(45) | IP-Adresse |
| source | varchar(100) | Quelle |
| created_at | datetime | Erstellungsdatum |
| completed_at | datetime | Vervollständigt am |
| email_sent | tinyint | E-Mail versendet |
| email_sent_at | datetime | E-Mail versendet am |

---

## Technologie-Stack

- **Backend**: PHP 7.4+, WordPress REST API
- **Frontend**: React 18, WordPress Element
- **PDF**: DOMPDF 2.0.4 (gebündelt)
- **Charts**: ApexCharts
- **Icons**: Heroicons
- **Animationen**: Framer Motion
- **Styling**: SCSS

---

## Changelog

### Version 1.2.0
- E-Mail mit PDF-Anhang an Leads
- DOMPDF für PDF-Generierung gebündelt
- Erweiterte Branding-Einstellungen (mehrzeilige Firma, Adresse, Logo-Breite)
- Settings-Seite in Tabs reorganisiert
- E-Mail-Status in Lead-Liste
- PHP 7.4 Kompatibilität

### Version 1.1.0
- Lead Magnet Flow mit Partial Leads
- reCAPTCHA v3 Integration
- GitHub Auto-Updater
- Debug-Logging

### Version 1.0.0
- Initiales Release

---

## Lizenz

GPL v2 oder später

## Support

Für Support und Feature-Requests bitte ein [Issue auf GitHub](https://github.com/AImitSK/immobilien-rechner-pro/issues) öffnen.

## Autor

**Stefan Kühne**
[sk-online-marketing.de](https://sk-online-marketing.de)
