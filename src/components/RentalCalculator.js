/**
 * Rental Calculator Component
 * Multi-step wizard for rental value estimation
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

const STEPS = [
    { id: 'type', component: PropertyTypeStep, title: __('Immobilienart', 'immobilien-rechner-pro') },
    { id: 'details', component: PropertyDetailsStep, title: __('Details', 'immobilien-rechner-pro') },
    { id: 'location', component: LocationStep, title: __('Standort', 'immobilien-rechner-pro') },
    { id: 'condition', component: ConditionStep, title: __('Zustand', 'immobilien-rechner-pro') },
    { id: 'features', component: FeaturesStep, title: __('Ausstattung', 'immobilien-rechner-pro') },
];

export default function RentalCalculator({ initialData, onComplete, onBack }) {
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
        ...initialData,
    });
    const [isCalculating, setIsCalculating] = useState(false);
    const [error, setError] = useState(null);
    
    // Update form data
    const updateFormData = useCallback((updates) => {
        setFormData((prev) => ({ ...prev, ...updates }));
    }, []);
    
    // Navigate to next step
    const handleNext = useCallback(() => {
        if (currentStep < STEPS.length - 1) {
            setCurrentStep((prev) => prev + 1);
        } else {
            // Final step - submit calculation
            submitCalculation();
        }
    }, [currentStep, formData]);
    
    // Navigate to previous step
    const handlePrev = useCallback(() => {
        if (currentStep > 0) {
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
                path: '/irp/v1/calculate/rental',
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
                },
            });
            
            if (response.success) {
                onComplete(formData, response.data);
            } else {
                setError(response.message || __('Berechnung fehlgeschlagen', 'immobilien-rechner-pro'));
            }
        } catch (err) {
            setError(err.message || __('Ein Fehler ist aufgetreten', 'immobilien-rechner-pro'));
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
                return true; // Features are optional
            default:
                return true;
        }
    };
    
    // Animation variants
    const stepVariants = {
        initial: (direction) => ({
            x: direction > 0 ? 50 : -50,
            opacity: 0,
        }),
        animate: {
            x: 0,
            opacity: 1,
        },
        exit: (direction) => ({
            x: direction < 0 ? 50 : -50,
            opacity: 0,
        }),
    };
    
    const [direction, setDirection] = useState(1);
    
    const goNext = () => {
        setDirection(1);
        handleNext();
    };
    
    const goPrev = () => {
        setDirection(-1);
        handlePrev();
    };
    
    return (
        <div className="irp-rental-calculator">
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
                    onClick={goPrev}
                    disabled={isCalculating}
                >
                    {currentStep === 0 && onBack
                        ? __('Zurück', 'immobilien-rechner-pro')
                        : __('Zurück', 'immobilien-rechner-pro')
                    }
                </button>

                <button
                    type="button"
                    className="irp-btn irp-btn-primary"
                    onClick={goNext}
                    disabled={!isStepValid() || isCalculating}
                >
                    {isCalculating ? (
                        <span className="irp-loading-spinner-small" />
                    ) : currentStep === STEPS.length - 1 ? (
                        __('Berechnen', 'immobilien-rechner-pro')
                    ) : (
                        __('Weiter', 'immobilien-rechner-pro')
                    )}
                </button>
            </div>
        </div>
    );
}
