<?php

namespace App\Http\Controllers\Admin;

use App\Constants\OrderStatuses;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Gateway;
use App\Models\Order;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'userCount' => User::count(),
            'bankCount' => Bank::count(),
            'gatewayCount' => Gateway::count(),
            'orderCount' => Order::count(),
            'paidOrderCount' => Order::query()->where('payment_status', OrderStatuses::PAYMENT_PAID)->count(),
        ]);
    }
}
