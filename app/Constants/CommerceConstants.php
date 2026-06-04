<?php

namespace App\Constants;

/**
 * Central business rules for order fees, tax withholdings, and seller limits.
 * Override via config/commerce.php (.env) in production when rates change.
 */
final class CommerceConstants
{
    /** Processing fee applies only when order amount is strictly above this (INR). */
    public const PROCESSING_FEE_THRESHOLD_INR = 1000.0;

    /** Processing fee rate (%) on order amount when above threshold. */
    public const PROCESSING_FEE_PERCENT = 3.0;

    /** GST (%) charged on the processing fee component only. */
    public const GST_ON_PROCESSING_FEE_PERCENT = 18.0;

    /** Flat platform fee (%) on every order, regardless of amount. */
    public const FLAT_ORDER_FEE_PERCENT = 0.5;

    /** TCS (%) on order amount when seller has a valid GSTIN on file. */
    public const TCS_PERCENT_WHEN_GST_REGISTERED = 0.5;

    /** Cumulative seller net settlements in FY (INR) after which TDS applies. */
    public const TDS_CUMULATIVE_NET_THRESHOLD_INR = 500000.0;

    /** TDS rate (%) on order amount once FY net-settlement threshold is reached. */
    public const TDS_PERCENT_AFTER_THRESHOLD = 0.1;

    /**
     * Max net amount a seller without GSTIN may receive in one Indian financial year (INR).
     * FY runs 1 April – 31 March.
     */
    public const SELLER_NO_GST_MAX_NET_PAYOUT_PER_FY_INR = 2000000.0;

    /** Indian financial year starts in April (1-based month). */
    public const FINANCIAL_YEAR_START_MONTH = 4;

    private function __construct() {}
}
