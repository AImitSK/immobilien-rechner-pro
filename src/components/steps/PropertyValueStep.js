/**
 * Property Value Step
 */

import { __ } from '@wordpress/i18n';

export default function PropertyValueStep({ data, onChange }) {
    const handleChange = (e) => {
        const { name, value } = e.target;
        onChange({ [name]: value });
    };
    
    const formatNumber = (value) => {
        const num = value.replace(/[^0-9]/g, '');
        return num ? parseInt(num).toLocaleString('de-DE') : '';
    };
    
    const handleValueChange = (e) => {
        const rawValue = e.target.value.replace(/[^0-9]/g, '');
        onChange({ property_value: rawValue });
    };
    
    return (
        <div className="irp-value-step">
            <h3>{__('What is your property worth?', 'immobilien-rechner-pro')}</h3>
            <p className="irp-step-description">
                {__('Enter your estimated property value or recent appraisal', 'immobilien-rechner-pro')}
            </p>
            
            <div className="irp-form-group">
                <label htmlFor="irp-property-value">
                    {__('Property Value', 'immobilien-rechner-pro')}
                    <span className="irp-required">*</span>
                </label>
                <div className="irp-input-with-unit irp-input-large">
                    <input
                        type="text"
                        id="irp-property-value"
                        name="property_value"
                        value={formatNumber(data.property_value || '')}
                        onChange={handleValueChange}
                        placeholder="350.000"
                        inputMode="numeric"
                        required
                    />
                    <span className="irp-unit">€</span>
                </div>
                <p className="irp-help-text">
                    {__('Estimated market value of your property', 'immobilien-rechner-pro')}
                </p>
            </div>
            
            <div className="irp-form-group">
                <label htmlFor="irp-holding-period">
                    {__('How long have you owned this property?', 'immobilien-rechner-pro')}
                </label>
                <div className="irp-input-with-unit">
                    <input
                        type="number"
                        id="irp-holding-period"
                        name="holding_period_years"
                        value={data.holding_period_years}
                        onChange={handleChange}
                        placeholder="5"
                        min="0"
                        max="50"
                    />
                    <span className="irp-unit">{__('years', 'immobilien-rechner-pro')}</span>
                </div>
                <p className="irp-help-text">
                    {__('Important for speculation tax calculation (10-year rule)', 'immobilien-rechner-pro')}
                </p>
            </div>
            
            <div className="irp-info-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" width="20" height="20">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="16" x2="12" y2="12" />
                    <line x1="12" y1="8" x2="12.01" y2="8" />
                </svg>
                <div>
                    <strong>{__('Tip:', 'immobilien-rechner-pro')}</strong>
                    <p>{__('If you\'ve owned the property for less than 10 years, selling may trigger speculation tax on any gains.', 'immobilien-rechner-pro')}</p>
                </div>
            </div>
        </div>
    );
}
