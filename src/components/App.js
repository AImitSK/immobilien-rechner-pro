/**
 * Main App Component
 */

import { useState, useMemo } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { motion, AnimatePresence } from 'framer-motion';

import ModeSelector from './ModeSelector';
import RentalCalculator from './RentalCalculator';
import ComparisonCalculator from './ComparisonCalculator';
import ResultsDisplay from './ResultsDisplay';
import LeadForm from './LeadForm';
import ThankYou from './ThankYou';

const STEPS = {
    MODE_SELECT: 'mode_select',
    CALCULATOR: 'calculator',
    RESULTS: 'results',
    LEAD_FORM: 'lead_form',
    THANK_YOU: 'thank_you',
};

export default function App({ config }) {
    const { initialMode, theme, showBranding, cityId, cityName } = config;

    // State
    const [currentStep, setCurrentStep] = useState(
        initialMode ? STEPS.CALCULATOR : STEPS.MODE_SELECT
    );
    const [mode, setMode] = useState(initialMode || '');
    const [formData, setFormData] = useState({});
    const [results, setResults] = useState(null);

    // Get settings from localized script
    const settings = window.irpSettings?.settings || {};

    // Dynamic styles based on branding
    const brandStyles = useMemo(() => ({
        '--irp-primary': settings.primaryColor || '#2563eb',
        '--irp-secondary': settings.secondaryColor || '#1e40af',
    }), [settings]);

    // Handlers
    const handleModeSelect = (selectedMode) => {
        setMode(selectedMode);
        setCurrentStep(STEPS.CALCULATOR);
    };

    const handleCalculationComplete = (data, calculationResults) => {
        setFormData(data);
        setResults(calculationResults);
        setCurrentStep(STEPS.RESULTS);
    };

    const handleRequestConsultation = () => {
        setCurrentStep(STEPS.LEAD_FORM);
    };

    const handleLeadSubmitted = () => {
        setCurrentStep(STEPS.THANK_YOU);
    };

    const handleStartOver = () => {
        setMode(initialMode || '');
        setFormData({});
        setResults(null);
        setCurrentStep(initialMode ? STEPS.CALCULATOR : STEPS.MODE_SELECT);
    };

    const handleBack = () => {
        switch (currentStep) {
            case STEPS.CALCULATOR:
                if (!initialMode) {
                    setCurrentStep(STEPS.MODE_SELECT);
                }
                break;
            case STEPS.RESULTS:
                setCurrentStep(STEPS.CALCULATOR);
                break;
            case STEPS.LEAD_FORM:
                setCurrentStep(STEPS.RESULTS);
                break;
            default:
                break;
        }
    };

    // Animation variants
    const pageVariants = {
        initial: { opacity: 0, x: 20 },
        animate: { opacity: 1, x: 0 },
        exit: { opacity: 0, x: -20 },
    };

    const pageTransition = {
        duration: 0.3,
        ease: 'easeInOut',
    };

    return (
        <div
            className={`irp-calculator irp-theme-${theme}`}
            style={brandStyles}
        >
            {showBranding && settings.companyLogo && (
                <div className="irp-branding">
                    <img
                        src={settings.companyLogo}
                        alt={settings.companyName || ''}
                        className="irp-logo"
                    />
                </div>
            )}

            <div className="irp-calculator-content">
                <AnimatePresence mode="wait">
                    {currentStep === STEPS.MODE_SELECT && (
                        <motion.div
                            key="mode-select"
                            variants={pageVariants}
                            initial="initial"
                            animate="animate"
                            exit="exit"
                            transition={pageTransition}
                        >
                            <ModeSelector onSelect={handleModeSelect} />
                        </motion.div>
                    )}

                    {currentStep === STEPS.CALCULATOR && (
                        <motion.div
                            key="calculator"
                            variants={pageVariants}
                            initial="initial"
                            animate="animate"
                            exit="exit"
                            transition={pageTransition}
                        >
                            {mode === 'rental' ? (
                                <RentalCalculator
                                    initialData={formData}
                                    onComplete={handleCalculationComplete}
                                    onBack={!initialMode ? handleBack : null}
                                    cityId={cityId}
                                    cityName={cityName}
                                />
                            ) : (
                                <ComparisonCalculator
                                    initialData={formData}
                                    onComplete={handleCalculationComplete}
                                    onBack={!initialMode ? handleBack : null}
                                    cityId={cityId}
                                    cityName={cityName}
                                />
                            )}
                        </motion.div>
                    )}

                    {currentStep === STEPS.RESULTS && (
                        <motion.div
                            key="results"
                            variants={pageVariants}
                            initial="initial"
                            animate="animate"
                            exit="exit"
                            transition={pageTransition}
                        >
                            <ResultsDisplay
                                mode={mode}
                                formData={formData}
                                results={results}
                                onRequestConsultation={handleRequestConsultation}
                                onBack={handleBack}
                                onStartOver={handleStartOver}
                            />
                        </motion.div>
                    )}

                    {currentStep === STEPS.LEAD_FORM && (
                        <motion.div
                            key="lead-form"
                            variants={pageVariants}
                            initial="initial"
                            animate="animate"
                            exit="exit"
                            transition={pageTransition}
                        >
                            <LeadForm
                                mode={mode}
                                calculationData={{ ...formData, results }}
                                onSubmitted={handleLeadSubmitted}
                                onBack={handleBack}
                            />
                        </motion.div>
                    )}

                    {currentStep === STEPS.THANK_YOU && (
                        <motion.div
                            key="thank-you"
                            variants={pageVariants}
                            initial="initial"
                            animate="animate"
                            exit="exit"
                            transition={pageTransition}
                        >
                            <ThankYou
                                companyName={settings.companyName}
                                onStartOver={handleStartOver}
                            />
                        </motion.div>
                    )}
                </AnimatePresence>
            </div>

            {showBranding && settings.companyName && !settings.companyLogo && (
                <div className="irp-footer">
                    <span>{settings.companyName}</span>
                </div>
            )}
        </div>
    );
}
