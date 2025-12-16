/**
 * Mortgage Details Step
 */

import { __ } from '@wordpress/i18n';

export default function MortgageStep({ data, onChange }) {
    const handleChange = (e) => {
        const { name, value } = e.target;
        onChange({ [name]: value });
    };
    
    const formatNumber = (value) => {
        const num = String(value).replace(/[^0-9]/g, '');
        return num ? parseInt(num).toLocaleString('de-DE') : '';
    };
    
    const handleMortgageChange = (e) => {
        const rawValue = e.target.value.replace(/[^0-9]/g, '');
        onChange({ remaining_mortgage: rawValue });
    };
    
    return (
        <div className="irp-mortgage-step">
            <h3>{__('Do you have an existing mortgage?', 'immobilien-rechner-pro')}</h3>
            <p className="irp-step-description">
                {__('This helps us calculate your net proceeds and rental returns', 'immobilien-rechner-pro')}
            </p>
            
            <div className="irp-form-group">
                <label htmlFor="irp-mortgage">
                    {__('Remaining Mortgage Balance', 'immobilien-rechner-pro')}
                </label>
                <div className="irp-input-with-unit">
                    <input
                        type="text"
                        id="irp-mortgage"
                        name="remaining_mortgage"
                        value={formatNumber(data.remaining_mortgage || '')}
                        onChange={handleMortgageChange}
                        placeholder="150.000"
                        inputMode="numeric"
                    />
                    <span className="irp-unit">€</span>
                </div>
                <p className="irp-help-text">
                    {__('Leave empty or enter 0 if fully paid off', 'immobilien-rechner-pro')}
                </p>
            </div>
            
            {data.remaining_mortgage && parseInt(data.remaining_mortgage) > 0 && (
                <div className="irp-form-group">
                    <label htmlFor="irp-rate">
                        {__('Current Interest Rate', 'immobilien-rechner-pro')}
                    </label>
                    <div className="irp-input-with-unit">
                        <input
                            type="number"
                            id="irp-rate"
                            name="mortgage_rate"
                            value={data.mortgage_rate}
                            onChange={handleChange}
                            placeholder="3.5"
                            min="0"
                            max="15"
                            step="0.1"
                        />
                        <span className="irp-unit">%</span>
                    </div>
                    <p className="irp-help-text">
                        {__('Your current annual interest rate', 'immobilien-rechner-pro')}
                    </p>
                </div>
            )}
            
            <div className="irp-form-group">
                <label htmlFor="irp-appreciation">
                    {__('Expected Annual Appreciation', 'immobilien-rechner-pro')}
                </label>
                <div className="irp-input-with-unit">
                    <input
                        type="number"
                        id="irp-appreciation"
                        name="expected_appreciation"
                        value={data.expected_appreciation}
                        onChange={handleChange}
                        placeholder="2"
                        min="-10"
                        max="20"
                        step="0.5"
                    />
                    <span className="irp-unit">%</span>
                </div>
                <p className="irp-help-text">
                    {__('Historical average in Germany: 2-4% per year', 'immobilien-rechner-pro')}
                </p>
            </div>
            
            <div className="irp-info-box irp-info-box-muted">
                <p>
                    {__('All fields on this page are optional. We\'ll use sensible defaults if left empty.', 'immobilien-rechner-pro')}
                </p>
            </div>
        </div>
    );
}
