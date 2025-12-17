/**
 * Property Features Step
 */

import { __ } from '@wordpress/i18n';
import { motion } from 'framer-motion';

const FEATURES = [
    { id: 'balcony', label: __('Balkon', 'immobilien-rechner-pro'), icon: '🌿' },
    { id: 'terrace', label: __('Terrasse', 'immobilien-rechner-pro'), icon: '☀️' },
    { id: 'garden', label: __('Garten', 'immobilien-rechner-pro'), icon: '🌳' },
    { id: 'elevator', label: __('Aufzug', 'immobilien-rechner-pro'), icon: '🛗' },
    { id: 'parking', label: __('Stellplatz', 'immobilien-rechner-pro'), icon: '🅿️' },
    { id: 'garage', label: __('Garage', 'immobilien-rechner-pro'), icon: '🚗' },
    { id: 'cellar', label: __('Keller', 'immobilien-rechner-pro'), icon: '📦' },
    { id: 'fitted_kitchen', label: __('Einbauküche', 'immobilien-rechner-pro'), icon: '🍳' },
    { id: 'floor_heating', label: __('Fußbodenheizung', 'immobilien-rechner-pro'), icon: '🔥' },
    { id: 'guest_toilet', label: __('Gäste-WC', 'immobilien-rechner-pro'), icon: '🚽' },
    { id: 'barrier_free', label: __('Barrierefrei', 'immobilien-rechner-pro'), icon: '♿' },
];

export default function FeaturesStep({ data, onChange }) {
    const toggleFeature = (featureId) => {
        const currentFeatures = data.features || [];
        const newFeatures = currentFeatures.includes(featureId)
            ? currentFeatures.filter((f) => f !== featureId)
            : [...currentFeatures, featureId];

        onChange({ features: newFeatures });
    };

    const selectedFeatures = data.features || [];

    return (
        <div className="irp-features-step">
            <h3>{__('Welche Ausstattungsmerkmale hat Ihre Immobilie?', 'immobilien-rechner-pro')}</h3>
            <p className="irp-step-description">
                {__('Wählen Sie alle zutreffenden aus. Diese können den Mietwert erhöhen.', 'immobilien-rechner-pro')}
            </p>

            <div className="irp-features-grid">
                {FEATURES.map((feature) => {
                    const isSelected = selectedFeatures.includes(feature.id);

                    return (
                        <motion.button
                            key={feature.id}
                            type="button"
                            className={`irp-feature-chip ${isSelected ? 'is-selected' : ''}`}
                            onClick={() => toggleFeature(feature.id)}
                            whileHover={{ scale: 1.05 }}
                            whileTap={{ scale: 0.95 }}
                        >
                            <span className="irp-feature-icon">{feature.icon}</span>
                            <span className="irp-feature-label">{feature.label}</span>
                            {isSelected && (
                                <span className="irp-feature-check">✓</span>
                            )}
                        </motion.button>
                    );
                })}
            </div>

            <div className="irp-features-summary">
                {selectedFeatures.length === 0 ? (
                    <p className="irp-no-features">
                        {__('Keine Ausstattung ausgewählt', 'immobilien-rechner-pro')}
                    </p>
                ) : (
                    <p className="irp-selected-count">
                        {selectedFeatures.length} {selectedFeatures.length === 1
                            ? __('Merkmal ausgewählt', 'immobilien-rechner-pro')
                            : __('Merkmale ausgewählt', 'immobilien-rechner-pro')
                        }
                    </p>
                )}
            </div>
        </div>
    );
}
