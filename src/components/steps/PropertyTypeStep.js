/**
 * Property Type Selection Step
 */

import { __ } from '@wordpress/i18n';
import { motion } from 'framer-motion';

const PROPERTY_TYPES = [
    {
        id: 'apartment',
        icon: (
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <rect x="3" y="3" width="18" height="18" rx="2" />
                <line x1="9" y1="3" x2="9" y2="21" />
                <line x1="15" y1="3" x2="15" y2="21" />
                <line x1="3" y1="9" x2="21" y2="9" />
                <line x1="3" y1="15" x2="21" y2="15" />
            </svg>
        ),
        label: __('Wohnung', 'immobilien-rechner-pro'),
        description: __('Wohnung in einem Mehrfamilienhaus', 'immobilien-rechner-pro'),
    },
    {
        id: 'house',
        icon: (
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                <polyline points="9 22 9 12 15 12 15 22" />
            </svg>
        ),
        label: __('Haus', 'immobilien-rechner-pro'),
        description: __('Einfamilienhaus oder Doppelhaushälfte', 'immobilien-rechner-pro'),
    },
    {
        id: 'commercial',
        icon: (
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <rect x="2" y="7" width="20" height="14" rx="2" />
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
            </svg>
        ),
        label: __('Gewerbe', 'immobilien-rechner-pro'),
        description: __('Büro, Einzelhandel oder Mischnutzung', 'immobilien-rechner-pro'),
    },
];

export default function PropertyTypeStep({ data, onChange }) {
    const handleSelect = (typeId) => {
        onChange({ property_type: typeId });
    };
    
    return (
        <div className="irp-property-type-step">
            <h3>{__('Welche Art von Immobilie haben Sie?', 'immobilien-rechner-pro')}</h3>
            
            <div className="irp-type-grid">
                {PROPERTY_TYPES.map((type) => (
                    <motion.button
                        key={type.id}
                        type="button"
                        className={`irp-type-card ${data.property_type === type.id ? 'is-selected' : ''}`}
                        onClick={() => handleSelect(type.id)}
                        whileHover={{ scale: 1.02 }}
                        whileTap={{ scale: 0.98 }}
                    >
                        <div className="irp-type-icon">
                            {type.icon}
                        </div>
                        <span className="irp-type-label">{type.label}</span>
                        <span className="irp-type-description">{type.description}</span>
                    </motion.button>
                ))}
            </div>
        </div>
    );
}
