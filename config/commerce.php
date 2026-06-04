<?php

use App\Constants\CommerceConstants;

/**
 * Commerce fees & compliance — defaults from CommerceConstants, overridable via .env.
 */
return [

    'processing_fee' => [
        'threshold_inr' => (float) env('COMMERCE_PROCESSING_FEE_THRESHOLD', CommerceConstants::PROCESSING_FEE_THRESHOLD_INR),
        'percent' => (float) env('COMMERCE_PROCESSING_FEE_PERCENT', CommerceConstants::PROCESSING_FEE_PERCENT),
    ],

    'gst_on_processing_fee_percent' => (float) env('COMMERCE_GST_ON_PROCESSING_FEE_PERCENT', CommerceConstants::GST_ON_PROCESSING_FEE_PERCENT),

    'flat_order_fee_percent' => (float) env('COMMERCE_FLAT_ORDER_FEE_PERCENT', CommerceConstants::FLAT_ORDER_FEE_PERCENT),

    'tcs_when_gst_registered_percent' => (float) env('COMMERCE_TCS_PERCENT', CommerceConstants::TCS_PERCENT_WHEN_GST_REGISTERED),

    'tds' => [
        'cumulative_net_threshold_inr' => (float) env('COMMERCE_TDS_THRESHOLD_INR', CommerceConstants::TDS_CUMULATIVE_NET_THRESHOLD_INR),
        'percent_after_threshold' => (float) env('COMMERCE_TDS_PERCENT', CommerceConstants::TDS_PERCENT_AFTER_THRESHOLD),
    ],

    'seller_no_gst_max_net_payout_per_fy_inr' => (float) env(
        'COMMERCE_NO_GST_MAX_NET_FY_INR',
        CommerceConstants::SELLER_NO_GST_MAX_NET_PAYOUT_PER_FY_INR
    ),

    'financial_year_start_month' => (int) env(
        'COMMERCE_FY_START_MONTH',
        CommerceConstants::FINANCIAL_YEAR_START_MONTH
    ),

];
