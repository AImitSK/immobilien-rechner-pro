<?php
/**
 * Calculator logic for rental value and comparison calculations
 */

if (!defined('ABSPATH')) {
    exit;
}

class IRP_Calculator {
    
    // Base prices per sqm by region (simplified - in production use real data)
    private array $base_prices = [
        '1' => 18.50,  // Berlin (starts with 1)
        '2' => 16.00,  // Hamburg (starts with 2)
        '3' => 11.50,  // Hannover region
        '4' => 11.00,  // Düsseldorf region
        '5' => 11.50,  // Köln/Bonn region
        '6' => 13.50,  // Frankfurt region
        '7' => 13.00,  // Stuttgart region
        '8' => 19.00,  // München region
        '9' => 10.00,  // Nürnberg region
        '0' => 10.50,  // Leipzig/Dresden region
    ];
    
    // Condition multipliers
    private array $condition_multipliers = [
        'new' => 1.25,
        'renovated' => 1.10,
        'good' => 1.00,
        'needs_renovation' => 0.80,
    ];
    
    // Property type multipliers
    private array $type_multipliers = [
        'apartment' => 1.00,
        'house' => 1.15,
        'commercial' => 0.85,
    ];
    
    // Feature premiums (€ per sqm)
    private array $feature_premiums = [
        'balcony' => 0.50,
        'terrace' => 0.75,
        'garden' => 1.00,
        'elevator' => 0.30,
        'parking' => 0.40,
        'garage' => 0.60,
        'cellar' => 0.20,
        'fitted_kitchen' => 0.50,
        'floor_heating' => 0.40,
        'guest_toilet' => 0.25,
        'barrier_free' => 0.30,
    ];
    
    /**
     * Calculate rental value estimate
     */
    public function calculate_rental_value(array $params): array {
        $size = (float) $params['size'];
        $zip_code = $params['zip_code'];
        $condition = $params['condition'];
        $property_type = $params['property_type'];
        $features = $params['features'] ?? [];
        $year_built = $params['year_built'] ?? null;
        $rooms = $params['rooms'] ?? null;
        
        // Get base price for region
        $region_code = substr($zip_code, 0, 1);
        $base_price = $this->base_prices[$region_code] ?? 11.00;
        
        // Apply multipliers
        $price_per_sqm = $base_price;
        $price_per_sqm *= $this->condition_multipliers[$condition] ?? 1.00;
        $price_per_sqm *= $this->type_multipliers[$property_type] ?? 1.00;
        
        // Apply feature premiums
        foreach ($features as $feature) {
            if (isset($this->feature_premiums[$feature])) {
                $price_per_sqm += $this->feature_premiums[$feature];
            }
        }
        
        // Age adjustment (buildings from 1960-1980 often less desirable)
        if ($year_built) {
            if ($year_built >= 2015) {
                $price_per_sqm *= 1.10;
            } elseif ($year_built >= 2000) {
                $price_per_sqm *= 1.05;
            } elseif ($year_built >= 1990) {
                $price_per_sqm *= 1.00;
            } elseif ($year_built >= 1970) {
                $price_per_sqm *= 0.95;
            } elseif ($year_built >= 1950) {
                $price_per_sqm *= 0.90;
            } else {
                // Pre-war buildings can be desirable (Altbau)
                $price_per_sqm *= 1.05;
            }
        }
        
        // Size adjustment (smaller apartments have higher price per sqm)
        if ($size < 40) {
            $price_per_sqm *= 1.15;
        } elseif ($size < 60) {
            $price_per_sqm *= 1.08;
        } elseif ($size > 120) {
            $price_per_sqm *= 0.95;
        } elseif ($size > 150) {
            $price_per_sqm *= 0.90;
        }
        
        // Calculate monthly rent
        $monthly_rent = $size * $price_per_sqm;
        
        // Calculate range (±15%)
        $rent_low = $monthly_rent * 0.85;
        $rent_high = $monthly_rent * 1.15;
        
        // Annual calculations
        $annual_rent = $monthly_rent * 12;
        
        // Market comparison (simplified)
        $market_position = $this->calculate_market_position($price_per_sqm, $region_code);
        
        return [
            'monthly_rent' => [
                'estimate' => round($monthly_rent, 2),
                'low' => round($rent_low, 2),
                'high' => round($rent_high, 2),
            ],
            'annual_rent' => round($annual_rent, 2),
            'price_per_sqm' => round($price_per_sqm, 2),
            'market_position' => $market_position,
            'factors' => [
                'base_price' => $base_price,
                'condition_impact' => $this->condition_multipliers[$condition] ?? 1.00,
                'type_impact' => $this->type_multipliers[$property_type] ?? 1.00,
                'features_count' => count($features),
            ],
            'calculation_date' => current_time('mysql'),
        ];
    }
    
    /**
     * Calculate sell vs rent comparison
     */
    public function calculate_comparison(array $params): array {
        // First, get the rental calculation
        $rental = $this->calculate_rental_value($params);
        
        $property_value = (float) $params['property_value'];
        $remaining_mortgage = (float) ($params['remaining_mortgage'] ?? 0);
        $mortgage_rate = (float) ($params['mortgage_rate'] ?? 3.5) / 100;
        $holding_period = (int) ($params['holding_period_years'] ?? 0);
        $appreciation_rate = (float) ($params['expected_appreciation'] ?? 2) / 100;
        
        $settings = get_option('irp_settings', []);
        $maintenance_rate = (float) ($settings['default_maintenance_rate'] ?? 1.5) / 100;
        $vacancy_rate = (float) ($settings['default_vacancy_rate'] ?? 3) / 100;
        $broker_commission = (float) ($settings['default_broker_commission'] ?? 3.57) / 100;
        
        // Calculate annual rental income (after costs)
        $gross_annual_rent = $rental['annual_rent'];
        $vacancy_loss = $gross_annual_rent * $vacancy_rate;
        $maintenance_cost = $property_value * $maintenance_rate;
        $net_annual_rent = $gross_annual_rent - $vacancy_loss - $maintenance_cost;
        
        // Mortgage costs (if applicable)
        $annual_mortgage_interest = $remaining_mortgage * $mortgage_rate;
        $net_annual_income = $net_annual_rent - $annual_mortgage_interest;
        
        // Calculate rental yield
        $gross_yield = ($gross_annual_rent / $property_value) * 100;
        $net_yield = ($net_annual_income / $property_value) * 100;
        
        // Sale scenario
        $sale_costs = $property_value * $broker_commission;
        $net_sale_proceeds = $property_value - $remaining_mortgage - $sale_costs;
        
        // Speculation tax consideration (simplified)
        $speculation_tax_applies = $holding_period < 10;
        $speculation_tax_note = $speculation_tax_applies 
            ? __('Note: Speculation tax may apply for properties held less than 10 years.', 'immobilien-rechner-pro')
            : null;
        
        // Break-even calculation (years until rental income exceeds sale proceeds)
        $years_projection = [];
        $cumulative_rental = 0;
        $break_even_year = null;
        
        for ($year = 1; $year <= 30; $year++) {
            // Property appreciates
            $future_value = $property_value * pow(1 + $appreciation_rate, $year);
            
            // Rental income (assuming 2% annual increase)
            $year_rental = $net_annual_income * pow(1.02, $year - 1);
            $cumulative_rental += $year_rental;
            
            // Future sale scenario
            $future_sale_costs = $future_value * $broker_commission;
            $future_mortgage = max(0, $remaining_mortgage - ($year * $remaining_mortgage / 25)); // Simplified paydown
            $future_net_sale = $future_value - $future_mortgage - $future_sale_costs;
            
            // Total value if keeping (cumulative rent + current value - mortgage)
            $keep_value = $cumulative_rental + $future_value - $future_mortgage;
            
            $years_projection[] = [
                'year' => $year,
                'property_value' => round($future_value, 2),
                'cumulative_rental_income' => round($cumulative_rental, 2),
                'net_sale_proceeds' => round($future_net_sale, 2),
                'keep_total_value' => round($keep_value, 2),
            ];
            
            // Find break-even point
            if ($break_even_year === null && $cumulative_rental >= $net_sale_proceeds) {
                $break_even_year = $year;
            }
        }
        
        // Recommendation logic
        $recommendation = $this->generate_recommendation(
            $net_yield,
            $break_even_year,
            $speculation_tax_applies,
            $net_sale_proceeds
        );
        
        return [
            'rental' => $rental,
            'sale' => [
                'property_value' => $property_value,
                'sale_costs' => round($sale_costs, 2),
                'remaining_mortgage' => $remaining_mortgage,
                'net_proceeds' => round($net_sale_proceeds, 2),
            ],
            'rental_scenario' => [
                'gross_annual_rent' => round($gross_annual_rent, 2),
                'vacancy_loss' => round($vacancy_loss, 2),
                'maintenance_cost' => round($maintenance_cost, 2),
                'mortgage_interest' => round($annual_mortgage_interest, 2),
                'net_annual_income' => round($net_annual_income, 2),
            ],
            'yields' => [
                'gross' => round($gross_yield, 2),
                'net' => round($net_yield, 2),
            ],
            'break_even_year' => $break_even_year,
            'speculation_tax_note' => $speculation_tax_note,
            'projection' => array_slice($years_projection, 0, 15), // First 15 years for chart
            'recommendation' => $recommendation,
            'calculation_date' => current_time('mysql'),
        ];
    }
    
    /**
     * Calculate where the rental price falls in the market
     */
    private function calculate_market_position(float $price_per_sqm, string $region_code): array {
        $base = $this->base_prices[$region_code] ?? 11.00;
        
        // Simplified percentile calculation
        $ratio = $price_per_sqm / $base;
        
        if ($ratio < 0.85) {
            $percentile = 20;
            $label = __('Below average', 'immobilien-rechner-pro');
        } elseif ($ratio < 0.95) {
            $percentile = 35;
            $label = __('Slightly below average', 'immobilien-rechner-pro');
        } elseif ($ratio < 1.05) {
            $percentile = 50;
            $label = __('Average', 'immobilien-rechner-pro');
        } elseif ($ratio < 1.15) {
            $percentile = 65;
            $label = __('Above average', 'immobilien-rechner-pro');
        } elseif ($ratio < 1.25) {
            $percentile = 80;
            $label = __('Well above average', 'immobilien-rechner-pro');
        } else {
            $percentile = 90;
            $label = __('Premium segment', 'immobilien-rechner-pro');
        }
        
        return [
            'percentile' => $percentile,
            'label' => $label,
        ];
    }
    
    /**
     * Generate a recommendation based on the analysis
     */
    private function generate_recommendation(
        float $net_yield,
        ?int $break_even_year,
        bool $speculation_tax,
        float $net_sale_proceeds
    ): array {
        $factors = [];
        $score = 0; // Positive = favor rent, Negative = favor sell
        
        // Yield analysis
        if ($net_yield >= 5) {
            $factors[] = __('Strong rental yield suggests renting could be profitable.', 'immobilien-rechner-pro');
            $score += 2;
        } elseif ($net_yield >= 3) {
            $factors[] = __('Moderate rental yield - consider your long-term goals.', 'immobilien-rechner-pro');
            $score += 1;
        } else {
            $factors[] = __('Low rental yield may make selling more attractive.', 'immobilien-rechner-pro');
            $score -= 1;
        }
        
        // Break-even analysis
        if ($break_even_year !== null) {
            if ($break_even_year <= 5) {
                $factors[] = sprintf(
                    __('Quick break-even in %d years supports rental strategy.', 'immobilien-rechner-pro'),
                    $break_even_year
                );
                $score += 2;
            } elseif ($break_even_year <= 10) {
                $factors[] = sprintf(
                    __('Moderate break-even period of %d years.', 'immobilien-rechner-pro'),
                    $break_even_year
                );
                $score += 1;
            } else {
                $factors[] = sprintf(
                    __('Long break-even period of %d years may favor selling.', 'immobilien-rechner-pro'),
                    $break_even_year
                );
                $score -= 1;
            }
        }
        
        // Tax consideration
        if ($speculation_tax) {
            $factors[] = __('Selling now may incur speculation tax - consider waiting or renting.', 'immobilien-rechner-pro');
            $score += 1;
        }
        
        // Generate summary
        if ($score >= 2) {
            $summary = __('Based on our analysis, renting appears to be the more favorable option.', 'immobilien-rechner-pro');
            $direction = 'rent';
        } elseif ($score <= -1) {
            $summary = __('Based on our analysis, selling may be the better choice for your situation.', 'immobilien-rechner-pro');
            $direction = 'sell';
        } else {
            $summary = __('Both options have merit. A consultation can help clarify the best path.', 'immobilien-rechner-pro');
            $direction = 'neutral';
        }
        
        return [
            'direction' => $direction,
            'summary' => $summary,
            'factors' => $factors,
        ];
    }
}
