<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        if (Schema::getColumnType('orders', 'order_status') !== 'string') {
            return;
        }

        $maps = [
            'order_status' => [
                'created' => 0,
                'pending' => 1,
                'accepted' => 2,
                'in_progress' => 3,
                'completed' => 4,
                'cancelled' => 5,
                'disputed' => 6,
            ],
            'payment_status' => [
                'pending' => 0,
                'authorized' => 1,
                'paid' => 2,
                'failed' => 3,
                'refunded' => 4,
                'partially_refunded' => 5,
            ],
            'settlement_status' => [
                'pending' => 0,
                'eligible' => 1,
                'settled' => 2,
                'failed' => 3,
            ],
            'safe_status' => [
                'pending_review' => 0,
                'safe' => 1,
                'hold' => 2,
                'rejected' => 3,
            ],
        ];

        foreach ($maps as $column => $map) {
            foreach ($map as $from => $to) {
                DB::table('orders')->where($column, $from)->update([$column => (string) $to]);
            }
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedTinyInteger('order_status')->default(0)->comment('0=created,1=pending,2=accepted,3=in_progress,4=completed,5=cancelled,6=disputed')->change();
            $table->unsignedTinyInteger('payment_status')->default(0)->comment('0=pending,1=authorized,2=paid,3=failed,4=refunded,5=partially_refunded')->change();
            $table->unsignedTinyInteger('settlement_status')->default(0)->comment('0=pending,1=eligible,2=settled,3=failed')->change();
            $table->unsignedTinyInteger('safe_status')->default(0)->comment('0=pending_review,1=safe,2=hold,3=rejected')->change();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        if (Schema::getColumnType('orders', 'order_status') === 'string') {
            return;
        }

        $maps = [
            'order_status' => [
                0 => 'created',
                1 => 'pending',
                2 => 'accepted',
                3 => 'in_progress',
                4 => 'completed',
                5 => 'cancelled',
                6 => 'disputed',
            ],
            'payment_status' => [
                0 => 'pending',
                1 => 'authorized',
                2 => 'paid',
                3 => 'failed',
                4 => 'refunded',
                5 => 'partially_refunded',
            ],
            'settlement_status' => [
                0 => 'pending',
                1 => 'eligible',
                2 => 'settled',
                3 => 'failed',
            ],
            'safe_status' => [
                0 => 'pending_review',
                1 => 'safe',
                2 => 'hold',
                3 => 'rejected',
            ],
        ];

        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_status', 20)->default('created')->change();
            $table->string('payment_status', 24)->default('pending')->change();
            $table->string('settlement_status', 20)->default('pending')->change();
            $table->string('safe_status', 20)->default('pending_review')->change();
        });

        foreach ($maps as $column => $map) {
            foreach ($map as $from => $to) {
                DB::table('orders')->where($column, $from)->update([$column => $to]);
            }
        }
    }
};
