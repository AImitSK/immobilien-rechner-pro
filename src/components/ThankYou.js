/**
 * Thank You Component
 * Displayed after successful lead submission
 */

import { __ } from '@wordpress/i18n';
import { motion } from 'framer-motion';

export default function ThankYou({ companyName, onStartOver }) {
    return (
        <div className="irp-thank-you">
            <motion.div
                className="irp-thank-you-icon"
                initial={{ scale: 0 }}
                animate={{ scale: 1 }}
                transition={{ 
                    type: 'spring',
                    stiffness: 200,
                    damping: 15,
                    delay: 0.2 
                }}
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="16 8 10 14 8 12" />
                </svg>
            </motion.div>
            
            <motion.h2
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ delay: 0.4 }}
            >
                {__('Thank You!', 'immobilien-rechner-pro')}
            </motion.h2>
            
            <motion.p
                className="irp-thank-you-message"
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ delay: 0.5 }}
            >
                {companyName ? (
                    <>
                        {__('Your request has been submitted.', 'immobilien-rechner-pro')}{' '}
                        <strong>{companyName}</strong>{' '}
                        {__('will be in touch with you shortly.', 'immobilien-rechner-pro')}
                    </>
                ) : (
                    __('Your request has been submitted. We will be in touch with you shortly.', 'immobilien-rechner-pro')
                )}
            </motion.p>
            
            <motion.div
                className="irp-thank-you-info"
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                transition={{ delay: 0.6 }}
            >
                <div className="irp-info-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" width="24" height="24">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                        <polyline points="22,6 12,13 2,6" />
                    </svg>
                    <div>
                        <strong>{__('Check your email', 'immobilien-rechner-pro')}</strong>
                        <p>{__('We\'ve sent a confirmation with your calculation results.', 'immobilien-rechner-pro')}</p>
                    </div>
                </div>
                
                <div className="irp-info-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" width="24" height="24">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                    <div>
                        <strong>{__('What happens next?', 'immobilien-rechner-pro')}</strong>
                        <p>{__('A local expert will contact you within 24 hours to discuss your property.', 'immobilien-rechner-pro')}</p>
                    </div>
                </div>
            </motion.div>
            
            <motion.div
                className="irp-thank-you-actions"
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                transition={{ delay: 0.8 }}
            >
                <button
                    type="button"
                    className="irp-btn irp-btn-secondary"
                    onClick={onStartOver}
                >
                    {__('Calculate Another Property', 'immobilien-rechner-pro')}
                </button>
            </motion.div>
        </div>
    );
}
