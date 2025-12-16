/**
 * Property Condition Step
 */

import { __ } from '@wordpress/i18n';
import { motion } from 'framer-motion';

const CONDITIONS = [
    {
        id: 'new',
        label: __('New / First Occupancy', 'immobilien-rechner-pro'),
        description: __('Never lived in, brand new construction', 'immobilien-rechner-pro'),
        icon: '✨',
    },
    {
        id: 'renovated',
        label: __('Recently Renovated', 'immobilien-rechner-pro'),
        description: __('Modernized in the last 5 years', 'immobilien-rechner-pro'),
        icon: '🔧',
    },
    {
        id: 'good',
        label: __('Good Condition', 'immobilien-rechner-pro'),
        description: __('Well maintained, move-in ready', 'immobilien-rechner-pro'),
        icon: '👍',
    },
    {
        id: 'needs_renovation',
        label: __('Needs Renovation', 'immobilien-rechner-pro'),
        description: __('Requires updates or repairs', 'immobilien-rechner-pro'),
        icon: '🏗️',
    },
];

export default function ConditionStep({ data, onChange }) {
    const handleSelect = (conditionId) => {
        onChange({ condition: conditionId });
    };
    
    return (
        <div className="irp-condition-step">
            <h3>{__('What condition is your property in?', 'immobilien-rechner-pro')}</h3>
            
            <div className="irp-condition-grid">
                {CONDITIONS.map((condition) => (
                    <motion.button
                        key={condition.id}
                        type="button"
                        className={`irp-condition-card ${data.condition === condition.id ? 'is-selected' : ''}`}
                        onClick={() => handleSelect(condition.id)}
                        whileHover={{ scale: 1.02 }}
                        whileTap={{ scale: 0.98 }}
                    >
                        <span className="irp-condition-icon">{condition.icon}</span>
                        <span className="irp-condition-label">{condition.label}</span>
                        <span className="irp-condition-description">{condition.description}</span>
                    </motion.button>
                ))}
            </div>
        </div>
    );
}
