/**
 * Comparison Calculator Component
 * Extended wizard for sell vs rent comparison
 */

import { useState, useCallback } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { motion, AnimatePresence } from 'framer-motion';
import apiFetch from '@wordpress/api-fetch';

import ProgressBar from './ProgressBar';
import PropertyTypeStep from './steps/PropertyTypeStep';
import PropertyDetailsStep from './steps/PropertyDetailsStep';
import LocationStep from './steps/LocationStep';
import ConditionStep from './steps/ConditionStep';
import FeaturesStep from './steps/FeaturesStep';
import FinancialStep from './steps/FinancialStep';

const STEPS = [
    { id: 'type', component: PropertyTypeStep, title: __('Property', 'immobilien-rechner-pro') },
    { id: 'details', component: PropertyDetailsStep, title: __('Details', 'immobilien-rechner-pro') },
    { id: 'location', component: LocationStep, title: __('Location', 'immobilien-rechner-pro') },
    { id: 'condition', component: ConditionStep, title: __('Condition', 'immobilien-rechner-pro') },
    { id: 'features', component: FeaturesStep, title: __('Features', 'immobilien-rechner-pro') },
    { id: 'financial', component: FinancialStep, title: __('Finances', 'immobilien-rechner-pro') },
];

export default function ComparisonCalculator({ initialData, onComplete, onBack }) {
    const [currentStep, setCurrentStep] = useState(0);
    const [formData, setFormData] = useState({
        property_type: '',
        size: '',
        rooms: '',
        zip_code: '',
        location: '',
        condition: '',
        features: [],
        year_built: '',
        property_value: '',
        remaining_mortgage: '',
        mortgage_rate: '3.5',
        holding_period_years: '',
        expected_appreciation: '2',
        ...initialData,
    });
    const [isCalculating, setIsCalculating] = useState(false);
    const [error, setError] = useState(null);
    const [direction, setDirection] = useState(1);
    
    // Update form data
    const updateFormData = useCallback((updates) => {
        setFormData((prev) => ({ ...prev, ...updates }));
    }, []);
    
    // Navigate to next step
    const handleNext = useCallback(() => {
        if (currentStep < STEPS.length - 1) {
            setDirection(1);
            setCurrentStep((prev) => prev + 1);
        } else {
            submitCalculation();
        }
    }, [currentStep, formData]);
    
    // Navigate to previous step
    const handlePrev = useCallback(() => {
        if (currentStep > 0) {
            setDirection(-1);
            setCurrentStep((prev) => prev - 1);
        } else if (onBack) {
            onBack();
        }
    }, [currentStep, onBack]);
    
    // Submit calculation to API
    const submitCalculation = async () => {
        setIsCalculating(true);
        setError(null);
        
        try {
            const response = await apiFetch({
                path: '/irp/v1/calculate/comparison',
                method: 'POST',
                data: {
                    property_type: formData.property_type,
                    size: parseFloat(formData.size),
                    rooms: formData.rooms ? parseInt(formData.rooms) : null,
                    zip_code: formData.zip_code,
                    location: formData.location,
                    condition: formData.condition,
                    features: formData.features,
                    year_built: formData.year_built ? parseInt(formData.year_built) : null,
                    property_value: parseFloat(formData.property_value),
                    remaining_mortgage: formData.remaining_mortgage ? parseFloat(formData.remaining_mortgage) : 0,
                    mortgage_rate: parseFloat(formData.mortgage_rate),
                    holding_period_years: formData.holding_period_years ? parseInt(formData.holding_period_years) : 0,
                    expected_appreciation: parseFloat(formData.expected_appreciation),
                },
            });
            
            if (response.success) {
                onComplete(formData, response.data);
            } else {
                setError(response.message || __('Calculation failed', 'immobilien-rechner-pro'));
            }
        } catch (err) {
            setError(err.message || __('An error occurred', 'immobilien-rechner-pro'));
        } finally {
            setIsCalculating(false);
        }
    };
    
    // Get current step component
    const CurrentStepComponent = STEPS[currentStep].component;
    
    // Check if current step is valid
    const isStepValid = () => {
        switch (STEPS[currentStep].id) {
            case 'type':
                return !!formData.property_type;
            case 'details':
                return formData.size && parseFloat(formData.size) > 0;
            case 'location':
                return formData.zip_code && formData.zip_code.length >= 4;
            case 'condition':
                return !!formData.condition;
            case 'features':
                return true;
            case 'financial':
                return formData.property_value && parseFloat(formData.property_value) > 0;
            default:
                return true;
        }
    };
    
    // Animation variants
    const stepVariants = {
        initial: (dir) => ({
            x: dir > 0 ? 50 : -50,
            opacity: 0,
        }),
        animate: {
            x: 0,
            opacity: 1,
        },
        exit: (dir) => ({
            x: dir < 0 ? 50 : -50,
            opacity: 0,
        }),
    };
    
    return (
        <div className="irp-comparison-calculator">
            <ProgressBar 
                steps={STEPS.map((s) => s.title)} 
                currentStep={currentStep} 
            />
            
            <div className="irp-step-container">
                <AnimatePresence mode="wait" custom={direction}>
                    <motion.div
                        key={currentStep}
                        custom={direction}
                        variants={stepVariants}
                        initial="initial"
                        animate="animate"
                        exit="exit"
                        transition={{ duration: 0.25 }}
                        className="irp-step"
                    >
                        <CurrentStepComponent
                            data={formData}
                            onChange={updateFormData}
                        />
                    </motion.div>
                </AnimatePresence>
            </div>
            
            {error && (
                <div className="irp-error">
                    <p>{error}</p>
                </div>
            )}
            
            <div className="irp-navigation">
                <button
                    type="button"
                    className="irp-btn irp-btn-secondary"
                    onClick={handlePrev}
                    disabled={isCalculating}
                >
                    {currentStep === 0 && onBack
                        ? __('Back', 'immobilien-rechner-pro')
                        : __('Previous', 'immobilien-rechner-pro')
                    }
                </button>
                
                <button
                    type="button"
                    className="irp-btn irp-btn-primary"
                    onClick={handleNext}
                    disabled={!isStepValid() || isCalculating}
                >
                    {isCalculating ? (
                        <span className="irp-loading-spinner-small" />
                    ) : currentStep === STEPS.length - 1 ? (
                        __('Calculate', 'immobilien-rechner-pro')
                    ) : (
                        __('Next', 'immobilien-rechner-pro')
                    )}
                </button>
            </div>
        </div>
    );
}
