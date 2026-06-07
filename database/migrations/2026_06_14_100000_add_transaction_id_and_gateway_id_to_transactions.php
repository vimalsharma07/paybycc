<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('transactions', 'transaction_id')) {
                $table->string('transaction_id', 64)->nullable()->after('payment_id');
            }
            if (! Schema::hasColumn('transactions', 'gateway_id')) {
                $table->string('gateway_id', 128)->nullable()->after('transaction_id');
                $table->index('gateway_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'gateway_id')) {
                $table->dropIndex(['gateway_id']);
                $table->dropColumn('gateway_id');
            }
            if (Schema::hasColumn('transactions', 'transaction_id')) {
                $table->dropColumn('transaction_id');
            }
        });
    }
};
