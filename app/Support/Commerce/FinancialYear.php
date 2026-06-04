<?php

namespace App\Support\Commerce;

use Carbon\Carbon;
use Carbon\CarbonInterface;

final class FinancialYear
{
    public static function start(?CarbonInterface $on = null): Carbon
    {
        $on = $on ? Carbon::instance($on) : now();
        $startMonth = max(1, min(12, (int) config('commerce.financial_year_start_month', 4)));

        $year = (int) $on->year;
        if ((int) $on->month < $startMonth) {
            $year--;
        }

        return Carbon::create($year, $startMonth, 1)->startOfDay();
    }

    public static function end(?CarbonInterface $on = null): Carbon
    {
        return self::start($on)->addYear()->subSecond();
    }
}
