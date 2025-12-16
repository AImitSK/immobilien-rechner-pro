# Immobilien Rechner Pro

Professional real estate calculator WordPress plugin for rental value estimation and sell vs. rent comparison. White-label solution for real estate agents.

## Features

- **Rental Value Calculator**: Estimate potential rental income based on property details
- **Sell vs. Rent Comparison**: Visual break-even analysis with interactive charts
- **Lead Generation**: Capture and manage leads with email notifications
- **White-Label Ready**: Fully customizable branding (colors, logo, company info)
- **Responsive Design**: Works on all devices
- **GDPR Compliant**: Built-in consent management

## Requirements

- WordPress 6.0+
- PHP 8.0+
- Node.js 18+ (for development)

## Installation

### For Development

1. Clone the repository into your WordPress plugins directory:
   ```bash
   cd wp-content/plugins/
   git clone <your-repo-url> immobilien-rechner-pro
   ```

2. Install dependencies:
   ```bash
   cd immobilien-rechner-pro
   npm install
   ```

3. Build the React frontend:
   ```bash
   npm run build
   ```

4. Activate the plugin in WordPress Admin → Plugins

### For Production

1. Download the release ZIP
2. Upload via WordPress Admin → Plugins → Add New → Upload Plugin
3. Activate the plugin

## Development

Start the development server with hot reloading:

```bash
npm run start
```

Build for production:

```bash
npm run build
```

Lint JavaScript:

```bash
npm run lint:js
```

## Usage

Add the calculator to any page or post using the shortcode:

```
[immobilien_rechner]
```

### Shortcode Options

| Attribute | Values | Description |
|-----------|--------|-------------|
| `mode` | `""`, `"rental"`, `"comparison"` | Lock to specific mode or let user choose |
| `theme` | `"light"`, `"dark"` | Color theme |
| `show_branding` | `"true"`, `"false"` | Show/hide company branding |

Examples:

```
[immobilien_rechner mode="rental"]
[immobilien_rechner theme="dark" show_branding="false"]
```

## Configuration

Go to **WordPress Admin → Immo Rechner → Settings** to configure:

- **Branding**: Company name, logo, primary/secondary colors
- **Notifications**: Email address for lead notifications
- **Calculator Defaults**: Maintenance rate, vacancy rate, broker commission
- **Privacy**: Consent requirement, privacy policy URL

## File Structure

```
immobilien-rechner-pro/
├── immobilien-rechner-pro.php    # Main plugin file
├── includes/                      # PHP classes
│   ├── class-activator.php       # Database setup
│   ├── class-assets.php          # Script/style loading
│   ├── class-calculator.php      # Calculation logic
│   ├── class-leads.php           # Lead management
│   ├── class-rest-api.php        # REST API endpoints
│   └── class-shortcode.php       # Shortcode handler
├── admin/                         # Admin panel
│   ├── class-admin.php
│   ├── views/
│   ├── css/
│   └── js/
├── src/                           # React source (development)
│   ├── components/
│   ├── hooks/
│   └── styles/
├── build/                         # Compiled React (production)
└── languages/                     # Translations
```

## REST API Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/irp/v1/calculate/rental` | POST | Calculate rental value |
| `/irp/v1/calculate/comparison` | POST | Calculate sell vs rent comparison |
| `/irp/v1/leads` | POST | Submit a lead |
| `/irp/v1/locations` | GET | Search locations (autocomplete) |

## Customization

### Extending Calculations

Edit `includes/class-calculator.php` to modify:

- Base prices per region (`$base_prices`)
- Condition multipliers (`$condition_multipliers`)
- Feature premiums (`$feature_premiums`)

### Adding Features

1. Add the feature to `src/components/steps/FeaturesStep.js`
2. Add corresponding premium in `includes/class-calculator.php`

### Translations

The plugin is translation-ready. Generate POT file:

```bash
wp i18n make-pot . languages/immobilien-rechner-pro.pot
```

## License

GPL v2 or later

## Support

For support and feature requests, please open an issue on GitHub.
