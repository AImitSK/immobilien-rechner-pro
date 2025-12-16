/**
 * Rental Gauge Component
 * Visual gauge showing market position percentile
 */

import { useEffect, useState } from '@wordpress/element';
import { motion } from 'framer-motion';

export default function RentalGauge({ percentile }) {
    const [animatedPercentile, setAnimatedPercentile] = useState(0);
    
    useEffect(() => {
        // Animate the percentile value
        const timer = setTimeout(() => {
            setAnimatedPercentile(percentile);
        }, 100);
        
        return () => clearTimeout(timer);
    }, [percentile]);
    
    // Calculate rotation for the needle (-90 to 90 degrees)
    const rotation = (animatedPercentile / 100) * 180 - 90;
    
    // Color based on percentile
    const getColor = () => {
        if (percentile < 30) return 'var(--irp-info)';
        if (percentile < 50) return 'var(--irp-primary)';
        if (percentile < 70) return 'var(--irp-success)';
        if (percentile < 85) return 'var(--irp-warning)';
        return 'var(--irp-danger)';
    };
    
    return (
        <div className="irp-gauge">
            <svg viewBox="0 0 200 120" className="irp-gauge-svg">
                {/* Background arc */}
                <path
                    d="M 20 100 A 80 80 0 0 1 180 100"
                    fill="none"
                    stroke="#e5e7eb"
                    strokeWidth="12"
                    strokeLinecap="round"
                />
                
                {/* Colored segments */}
                <path
                    d="M 20 100 A 80 80 0 0 1 56 40"
                    fill="none"
                    stroke="var(--irp-info)"
                    strokeWidth="12"
                    strokeLinecap="round"
                    opacity="0.3"
                />
                <path
                    d="M 56 40 A 80 80 0 0 1 100 20"
                    fill="none"
                    stroke="var(--irp-primary)"
                    strokeWidth="12"
                    opacity="0.3"
                />
                <path
                    d="M 100 20 A 80 80 0 0 1 144 40"
                    fill="none"
                    stroke="var(--irp-success)"
                    strokeWidth="12"
                    opacity="0.3"
                />
                <path
                    d="M 144 40 A 80 80 0 0 1 180 100"
                    fill="none"
                    stroke="var(--irp-warning)"
                    strokeWidth="12"
                    strokeLinecap="round"
                    opacity="0.3"
                />
                
                {/* Needle */}
                <motion.g
                    initial={{ rotate: -90 }}
                    animate={{ rotate: rotation }}
                    transition={{ duration: 1, ease: 'easeOut', delay: 0.3 }}
                    style={{ transformOrigin: '100px 100px' }}
                >
                    <line
                        x1="100"
                        y1="100"
                        x2="100"
                        y2="35"
                        stroke={getColor()}
                        strokeWidth="3"
                        strokeLinecap="round"
                    />
                    <circle
                        cx="100"
                        cy="100"
                        r="8"
                        fill={getColor()}
                    />
                </motion.g>
                
                {/* Center cover */}
                <circle
                    cx="100"
                    cy="100"
                    r="5"
                    fill="white"
                />
                
                {/* Labels */}
                <text x="20" y="115" className="irp-gauge-label" textAnchor="middle">0%</text>
                <text x="100" y="10" className="irp-gauge-label" textAnchor="middle">50%</text>
                <text x="180" y="115" className="irp-gauge-label" textAnchor="middle">100%</text>
            </svg>
            
            <motion.div
                className="irp-gauge-value"
                initial={{ opacity: 0, scale: 0.5 }}
                animate={{ opacity: 1, scale: 1 }}
                transition={{ duration: 0.5, delay: 0.8 }}
            >
                <span className="irp-gauge-number">{percentile}</span>
                <span className="irp-gauge-percent">%</span>
            </motion.div>
        </div>
    );
}
