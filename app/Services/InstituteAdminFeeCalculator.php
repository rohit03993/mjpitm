<?php

namespace App\Services;

class InstituteAdminFeeCalculator
{
    /**
     * Calculate the fee that Institute Admin needs to pay to main institute
     * based on course duration
     * 
     * Fee Structure:
     * - 3-6 months: ₹2,500 (₹2,000 fees + ₹500 registration)
     * - 1 year (12 months): ₹3,500 (₹3,000 fees + ₹500 registration)
     * - 2 years (24 months): ₹4,500 (₹4,000 fees + ₹500 registration)
     * 
     * @param int $courseDurationMonths Course duration in months
     * @return float
     */
    public static function calculate(int $courseDurationMonths): float
    {
        if ($courseDurationMonths <= 6) {
            // 3-6 months: ₹2,500
            return 2500.00;
        } elseif ($courseDurationMonths <= 12) {
            // 1 year (up to 12 months): ₹3,500
            return 3500.00;
        } elseif ($courseDurationMonths <= 24) {
            // 2 years (up to 24 months): ₹4,500
            return 4500.00;
        } else {
            // For courses longer than 2 years, use 2-year rate
            return 4500.00;
        }
    }

    /**
     * Get fee breakdown description
     * 
     * @param int $courseDurationMonths Course duration in months
     * @return array
     */
    public static function getFeeBreakdown(int $courseDurationMonths): array
    {
        if ($courseDurationMonths <= 6) {
            return [
                'fees' => 2000.00,
                'registration' => 500.00,
                'total' => 2500.00,
                'duration_range' => '3-6 months'
            ];
        } elseif ($courseDurationMonths <= 12) {
            return [
                'fees' => 3000.00,
                'registration' => 500.00,
                'total' => 3500.00,
                'duration_range' => '1 year'
            ];
        } elseif ($courseDurationMonths <= 24) {
            return [
                'fees' => 4000.00,
                'registration' => 500.00,
                'total' => 4500.00,
                'duration_range' => '2 years'
            ];
        } else {
            return [
                'fees' => 4000.00,
                'registration' => 500.00,
                'total' => 4500.00,
                'duration_range' => '2+ years'
            ];
        }
    }
}
