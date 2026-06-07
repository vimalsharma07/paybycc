<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transactions') && Schema::getColumnType('transactions', 'status') === 'string') {
            $transactionMap = [
                'pending' => 0,
                'completed' => 1,
                'failed' => 2,
                'processing' => 3,
                'success' => 1,
                'succeeded' => 1,
                'paid' => 1,
                'error' => 2,
                'declined' => 2,
                'rejected' => 2,
            ];

            foreach ($transactionMap as $from => $to) {
                DB::table('transactions')->where('status', $from)->update(['status' => (string) $to]);
            }

            Schema::table('transactions', function (Blueprint $table) {
                $table->unsignedTinyInteger('status')->default(1)->comment('0=pending,1=completed,2=failed,3=processing')->change();
            });
        }

        if (Schema::hasTable('settlements') && Schema::getColumnType('settlements', 'status') === 'string') {
            $settlementMap = [
                'pending' => 0,
                'eligible' => 1,
                'settled' => 2,
                'failed' => 3,
            ];

            foreach ($settlementMap as $from => $to) {
                DB::table('settlements')->where('status', $from)->update(['status' => (string) $to]);
            }

            Schema::table('settlements', function (Blueprint $table) {
                $table->unsignedTinyInteger('status')->default(0)->comment('0=pending,1=eligible,2=settled,3=failed')->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('transactions') && Schema::getColumnType('transactions', 'status') !== 'string') {
            Schema::table('transactions', function (Blueprint $table) {
                $table->string('status')->default('completed')->change();
            });

            $map = [0 => 'pending', 1 => 'completed', 2 => 'failed', 3 => 'processing'];
            foreach ($map as $from => $to) {
                DB::table('transactions')->where('status', $from)->update(['status' => $to]);
            }
        }

        if (Schema::hasTable('settlements') && Schema::getColumnType('settlements', 'status') !== 'string') {
            Schema::table('settlements', function (Blueprint $table) {
                $table->string('status', 20)->default('pending')->change();
            });

            $map = [0 => 'pending', 1 => 'eligible', 2 => 'settled', 3 => 'failed'];
            foreach ($map as $from => $to) {
                DB::table('settlements')->where('status', $from)->update(['status' => $to]);
            }
        }
    }
};
