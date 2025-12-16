/**
 * Property Details Step
 */

import { __ } from '@wordpress/i18n';

export default function PropertyDetailsStep({ data, onChange }) {
    const handleChange = (e) => {
        const { name, value } = e.target;
        onChange({ [name]: value });
    };
    
    const currentYear = new Date().getFullYear();
    
    return (
        <div className="irp-details-step">
            <h3>{__('Tell us about your property', 'immobilien-rechner-pro')}</h3>
            
            <div className="irp-form-group">
                <label htmlFor="irp-size">
                    {__('Living Space', 'immobilien-rechner-pro')}
                    <span className="irp-required">*</span>
                </label>
                <div className="irp-input-with-unit">
                    <input
                        type="number"
                        id="irp-size"
                        name="size"
                        value={data.size}
                        onChange={handleChange}
                        placeholder="80"
                        min="10"
                        max="10000"
                        step="0.5"
                        required
                    />
                    <span className="irp-unit">m²</span>
                </div>
                <p className="irp-help-text">
                    {__('Total living area in square meters', 'immobilien-rechner-pro')}
                </p>
            </div>
            
            <div className="irp-form-row">
                <div className="irp-form-group">
                    <label htmlFor="irp-rooms">
                        {__('Number of Rooms', 'immobilien-rechner-pro')}
                    </label>
                    <select
                        id="irp-rooms"
                        name="rooms"
                        value={data.rooms}
                        onChange={handleChange}
                    >
                        <option value="">{__('Select...', 'immobilien-rechner-pro')}</option>
                        <option value="1">1</option>
                        <option value="1.5">1.5</option>
                        <option value="2">2</option>
                        <option value="2.5">2.5</option>
                        <option value="3">3</option>
                        <option value="3.5">3.5</option>
                        <option value="4">4</option>
                        <option value="4.5">4.5</option>
                        <option value="5">5</option>
                        <option value="6">6+</option>
                    </select>
                    <p className="irp-help-text">
                        {__('Excluding kitchen and bathroom', 'immobilien-rechner-pro')}
                    </p>
                </div>
                
                <div className="irp-form-group">
                    <label htmlFor="irp-year">
                        {__('Year Built', 'immobilien-rechner-pro')}
                    </label>
                    <input
                        type="number"
                        id="irp-year"
                        name="year_built"
                        value={data.year_built}
                        onChange={handleChange}
                        placeholder={currentYear.toString()}
                        min="1800"
                        max={currentYear + 5}
                    />
                    <p className="irp-help-text">
                        {__('Original construction year', 'immobilien-rechner-pro')}
                    </p>
                </div>
            </div>
        </div>
    );
}
