<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $ordersTable = Schema::hasTable('orders') ? 'orders' : 'marketplace_orders';

        Schema::table('payments', function (Blueprint $table) use ($ordersTable) {
            if (! Schema::hasColumn('payments', 'order_id') && ! Schema::hasColumn('payments', 'marketplace_order_id')) {
                $table->foreignId('order_id')
                    ->nullable()
                    ->after('gateway_id')
                    ->constrained($ordersTable)
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('payments', 'order_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropConstrainedForeignId('order_id');
            });
        }
    }
};
