/**
 * Results Display Component
 */

import { __ } from '@wordpress/i18n';
import { motion } from 'framer-motion';
import {
    AreaChart,
    Area,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip,
    ResponsiveContainer,
    ReferenceLine,
} from 'recharts';

import RentalGauge from './RentalGauge';

export default function ResultsDisplay({
    mode,
    formData,
    results,
    onRequestConsultation,
    onBack,
    onStartOver,
}) {
    const formatCurrency = (value) => {
        return new Intl.NumberFormat('de-DE', {
            style: 'currency',
            currency: 'EUR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(value);
    };

    const formatCurrencyShort = (value) => {
        if (value >= 1000000) {
            return `${(value / 1000000).toFixed(1)}M €`;
        }
        if (value >= 1000) {
            return `${(value / 1000).toFixed(0)}k €`;
        }
        return `${value} €`;
    };

    if (mode === 'rental') {
        return (
            <RentalResults
                formData={formData}
                results={results}
                formatCurrency={formatCurrency}
                onRequestConsultation={onRequestConsultation}
                onBack={onBack}
                onStartOver={onStartOver}
            />
        );
    }

    return (
        <ComparisonResults
            formData={formData}
            results={results}
            formatCurrency={formatCurrency}
            formatCurrencyShort={formatCurrencyShort}
            onRequestConsultation={onRequestConsultation}
            onBack={onBack}
            onStartOver={onStartOver}
        />
    );
}

function RentalResults({
    formData,
    results,
    formatCurrency,
    onRequestConsultation,
    onBack,
    onStartOver,
}) {
    const { monthly_rent, price_per_sqm, market_position } = results;

    return (
        <div className="irp-results irp-results-rental">
            <motion.div
                className="irp-results-header"
                initial={{ opacity: 0, y: -20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5 }}
            >
                <h2>{__('Ihre Mietwert-Schätzung', 'immobilien-rechner-pro')}</h2>
                <p>
                    {__('Basierend auf Ihren Immobiliendaten und dem Standort', 'immobilien-rechner-pro')}
                </p>
            </motion.div>

            <div className="irp-results-main">
                <motion.div
                    className="irp-result-card irp-result-primary"
                    initial={{ opacity: 0, scale: 0.9 }}
                    animate={{ opacity: 1, scale: 1 }}
                    transition={{ duration: 0.5, delay: 0.2 }}
                >
                    <span className="irp-result-label">
                        {__('Geschätzte Monatsmiete', 'immobilien-rechner-pro')}
                    </span>
                    <span className="irp-result-value">
                        {formatCurrency(monthly_rent.estimate)}
                    </span>
                    <span className="irp-result-range">
                        {__('Spanne:', 'immobilien-rechner-pro')} {formatCurrency(monthly_rent.low)} – {formatCurrency(monthly_rent.high)}
                    </span>
                </motion.div>

                <div className="irp-result-secondary-grid">
                    <motion.div
                        className="irp-result-card"
                        initial={{ opacity: 0, x: -20 }}
                        animate={{ opacity: 1, x: 0 }}
                        transition={{ duration: 0.5, delay: 0.4 }}
                    >
                        <span className="irp-result-label">
                            {__('Preis pro m²', 'immobilien-rechner-pro')}
                        </span>
                        <span className="irp-result-value-small">
                            {formatCurrency(price_per_sqm)}
                        </span>
                    </motion.div>

                    <motion.div
                        className="irp-result-card"
                        initial={{ opacity: 0, x: 20 }}
                        animate={{ opacity: 1, x: 0 }}
                        transition={{ duration: 0.5, delay: 0.4 }}
                    >
                        <span className="irp-result-label">
                            {__('Jährliche Mieteinnahmen', 'immobilien-rechner-pro')}
                        </span>
                        <span className="irp-result-value-small">
                            {formatCurrency(results.annual_rent)}
                        </span>
                    </motion.div>
                </div>
            </div>

            <motion.div
                className="irp-market-position"
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                transition={{ duration: 0.5, delay: 0.6 }}
            >
                <h3>{__('Marktposition', 'immobilien-rechner-pro')}</h3>
                <RentalGauge percentile={market_position.percentile} />
                <p className="irp-market-label">{market_position.label}</p>
            </motion.div>

            <motion.div
                className="irp-results-cta"
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, delay: 0.8 }}
            >
                <p>{__('Möchten Sie eine professionelle Bewertung Ihrer Immobilie?', 'immobilien-rechner-pro')}</p>
                <button
                    type="button"
                    className="irp-btn irp-btn-primary irp-btn-large"
                    onClick={onRequestConsultation}
                >
                    {__('Kostenlose Beratung anfordern', 'immobilien-rechner-pro')}
                </button>

                <div className="irp-results-actions">
                    <button
                        type="button"
                        className="irp-btn irp-btn-text"
                        onClick={onBack}
                    >
                        {__('Details bearbeiten', 'immobilien-rechner-pro')}
                    </button>
                    <button
                        type="button"
                        className="irp-btn irp-btn-text"
                        onClick={onStartOver}
                    >
                        {__('Neu starten', 'immobilien-rechner-pro')}
                    </button>
                </div>
            </motion.div>
        </div>
    );
}

function ComparisonResults({
    formData,
    results,
    formatCurrency,
    formatCurrencyShort,
    onRequestConsultation,
    onBack,
    onStartOver,
}) {
    const { rental, sale, rental_scenario, yields, break_even_year, projection, recommendation } = results;

    // Prepare chart data
    const chartData = projection.map((p) => ({
        year: p.year,
        rental: p.cumulative_rental_income,
        sale: p.net_sale_proceeds,
        keep: p.keep_total_value,
    }));

    const getRecommendationColor = () => {
        switch (recommendation.direction) {
            case 'rent':
                return 'var(--irp-success)';
            case 'sell':
                return 'var(--irp-warning)';
            default:
                return 'var(--irp-primary)';
        }
    };

    return (
        <div className="irp-results irp-results-comparison">
            <motion.div
                className="irp-results-header"
                initial={{ opacity: 0, y: -20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5 }}
            >
                <h2>{__('Verkaufen vs. Vermieten Vergleich', 'immobilien-rechner-pro')}</h2>
            </motion.div>

            <div className="irp-comparison-cards">
                <motion.div
                    className="irp-comparison-card irp-card-sell"
                    initial={{ opacity: 0, x: -30 }}
                    animate={{ opacity: 1, x: 0 }}
                    transition={{ duration: 0.5, delay: 0.2 }}
                >
                    <h3>{__('Wenn Sie jetzt verkaufen', 'immobilien-rechner-pro')}</h3>
                    <div className="irp-comparison-value">
                        {formatCurrency(sale.net_proceeds)}
                    </div>
                    <span className="irp-comparison-label">{__('Nettoerlös', 'immobilien-rechner-pro')}</span>
                    <ul className="irp-comparison-details">
                        <li>
                            <span>{__('Immobilienwert', 'immobilien-rechner-pro')}</span>
                            <span>{formatCurrency(sale.property_value)}</span>
                        </li>
                        <li>
                            <span>{__('Verkaufskosten', 'immobilien-rechner-pro')}</span>
                            <span>-{formatCurrency(sale.sale_costs)}</span>
                        </li>
                        {sale.remaining_mortgage > 0 && (
                            <li>
                                <span>{__('Hypothekenablösung', 'immobilien-rechner-pro')}</span>
                                <span>-{formatCurrency(sale.remaining_mortgage)}</span>
                            </li>
                        )}
                    </ul>
                </motion.div>

                <motion.div
                    className="irp-comparison-card irp-card-rent"
                    initial={{ opacity: 0, x: 30 }}
                    animate={{ opacity: 1, x: 0 }}
                    transition={{ duration: 0.5, delay: 0.2 }}
                >
                    <h3>{__('Wenn Sie vermieten', 'immobilien-rechner-pro')}</h3>
                    <div className="irp-comparison-value">
                        {formatCurrency(rental_scenario.net_annual_income)}
                        <span className="irp-per-year">/{__('Jahr', 'immobilien-rechner-pro')}</span>
                    </div>
                    <span className="irp-comparison-label">{__('Netto-Mieteinnahmen', 'immobilien-rechner-pro')}</span>
                    <ul className="irp-comparison-details">
                        <li>
                            <span>{__('Bruttomiete', 'immobilien-rechner-pro')}</span>
                            <span>{formatCurrency(rental_scenario.gross_annual_rent)}</span>
                        </li>
                        <li>
                            <span>{__('Leerstandsverlust', 'immobilien-rechner-pro')}</span>
                            <span>-{formatCurrency(rental_scenario.vacancy_loss)}</span>
                        </li>
                        <li>
                            <span>{__('Instandhaltung', 'immobilien-rechner-pro')}</span>
                            <span>-{formatCurrency(rental_scenario.maintenance_cost)}</span>
                        </li>
                    </ul>
                    <div className="irp-yield-badges">
                        <span className="irp-yield-badge">
                            {__('Bruttorendite:', 'immobilien-rechner-pro')} {yields.gross.toFixed(1)}%
                        </span>
                        <span className="irp-yield-badge">
                            {__('Nettorendite:', 'immobilien-rechner-pro')} {yields.net.toFixed(1)}%
                        </span>
                    </div>
                </motion.div>
            </div>

            <motion.div
                className="irp-chart-section"
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                transition={{ duration: 0.5, delay: 0.4 }}
            >
                <h3>{__('15-Jahres-Prognose', 'immobilien-rechner-pro')}</h3>
                <div className="irp-chart-container">
                    <ResponsiveContainer width="100%" height={300}>
                        <AreaChart data={chartData}>
                            <defs>
                                <linearGradient id="colorRental" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="5%" stopColor="var(--irp-primary)" stopOpacity={0.3} />
                                    <stop offset="95%" stopColor="var(--irp-primary)" stopOpacity={0} />
                                </linearGradient>
                                <linearGradient id="colorSale" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="5%" stopColor="var(--irp-warning)" stopOpacity={0.3} />
                                    <stop offset="95%" stopColor="var(--irp-warning)" stopOpacity={0} />
                                </linearGradient>
                            </defs>
                            <CartesianGrid strokeDasharray="3 3" stroke="#e5e7eb" />
                            <XAxis
                                dataKey="year"
                                tickFormatter={(value) => `${value}J`}
                                stroke="#9ca3af"
                            />
                            <YAxis
                                tickFormatter={formatCurrencyShort}
                                stroke="#9ca3af"
                            />
                            <Tooltip
                                formatter={(value) => formatCurrency(value)}
                                labelFormatter={(label) => `${__('Jahr', 'immobilien-rechner-pro')} ${label}`}
                            />
                            <Area
                                type="monotone"
                                dataKey="rental"
                                name={__('Kumulierte Mieteinnahmen', 'immobilien-rechner-pro')}
                                stroke="var(--irp-primary)"
                                fillOpacity={1}
                                fill="url(#colorRental)"
                            />
                            <Area
                                type="monotone"
                                dataKey="sale"
                                name={__('Verkaufserlös (wenn in dem Jahr verkauft)', 'immobilien-rechner-pro')}
                                stroke="var(--irp-warning)"
                                fillOpacity={1}
                                fill="url(#colorSale)"
                            />
                            {break_even_year && (
                                <ReferenceLine
                                    x={break_even_year}
                                    stroke="var(--irp-success)"
                                    strokeDasharray="5 5"
                                    label={{
                                        value: __('Break-even', 'immobilien-rechner-pro'),
                                        position: 'top',
                                        fill: 'var(--irp-success)',
                                    }}
                                />
                            )}
                        </AreaChart>
                    </ResponsiveContainer>
                </div>

                {break_even_year && (
                    <p className="irp-breakeven-info">
                        {__('Break-even-Punkt:', 'immobilien-rechner-pro')} <strong>{break_even_year} {__('Jahre', 'immobilien-rechner-pro')}</strong>
                    </p>
                )}
            </motion.div>

            <motion.div
                className="irp-recommendation"
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, delay: 0.6 }}
                style={{ borderColor: getRecommendationColor() }}
            >
                <h3>{__('Unsere Einschätzung', 'immobilien-rechner-pro')}</h3>
                <p className="irp-recommendation-summary">{recommendation.summary}</p>
                <ul className="irp-recommendation-factors">
                    {recommendation.factors.map((factor, index) => (
                        <li key={index}>{factor}</li>
                    ))}
                </ul>

                {results.speculation_tax_note && (
                    <p className="irp-tax-notice">
                        ⚠️ {results.speculation_tax_note}
                    </p>
                )}
            </motion.div>

            <motion.div
                className="irp-results-cta"
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, delay: 0.8 }}
            >
                <p>{__('Erhalten Sie persönliche Beratung von einem lokalen Experten', 'immobilien-rechner-pro')}</p>
                <button
                    type="button"
                    className="irp-btn irp-btn-primary irp-btn-large"
                    onClick={onRequestConsultation}
                >
                    {__('Kostenlose Beratung anfordern', 'immobilien-rechner-pro')}
                </button>

                <div className="irp-results-actions">
                    <button
                        type="button"
                        className="irp-btn irp-btn-text"
                        onClick={onBack}
                    >
                        {__('Details bearbeiten', 'immobilien-rechner-pro')}
                    </button>
                    <button
                        type="button"
                        className="irp-btn irp-btn-text"
                        onClick={onStartOver}
                    >
                        {__('Neu starten', 'immobilien-rechner-pro')}
                    </button>
                </div>
            </motion.div>
        </div>
    );
}
