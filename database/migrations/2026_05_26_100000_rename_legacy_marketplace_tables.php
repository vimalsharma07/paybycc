<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Renames old marketplace_* / sub_services tables for databases created before the naming cleanup.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sub_services') && ! Schema::hasTable('subservices')) {
            Schema::rename('sub_services', 'subservices');
        }

        if (Schema::hasTable('freelancer_services') && ! Schema::hasTable('seller_subservices')) {
            if (Schema::hasColumn('freelancer_services', 'sub_service_id')) {
                Schema::table('freelancer_services', function (Blueprint $table) {
                    $table->dropForeign(['sub_service_id']);
                });
                Schema::table('freelancer_services', function (Blueprint $table) {
                    $table->renameColumn('sub_service_id', 'subservice_id');
                });
            }
            Schema::rename('freelancer_services', 'seller_subservices');
            Schema::table('seller_subservices', function (Blueprint $table) {
                $table->foreign('subservice_id')->references('id')->on('subservices')->cascadeOnDelete();
            });
        }

        if (Schema::hasTable('service_submissions') && Schema::hasColumn('service_submissions', 'proposed_sub_service_name')) {
            Schema::table('service_submissions', function (Blueprint $table) {
                $table->renameColumn('proposed_sub_service_name', 'proposed_subservice_name');
            });
        }

        if (Schema::hasTable('marketplace_orders') && ! Schema::hasTable('orders')) {
            if (Schema::hasColumn('marketplace_orders', 'sub_service_id')) {
                Schema::table('marketplace_orders', function (Blueprint $table) {
                    $table->dropForeign(['sub_service_id']);
                });
                Schema::table('marketplace_orders', function (Blueprint $table) {
                    $table->renameColumn('sub_service_id', 'subservice_id');
                });
            }
            Schema::rename('marketplace_orders', 'orders');
            if (Schema::hasColumn('orders', 'subservice_id')) {
                Schema::table('orders', function (Blueprint $table) {
                    $table->foreign('subservice_id')->references('id')->on('subservices')->nullOnDelete();
                });
            }
        }

        if (Schema::hasTable('marketplace_settlements') && ! Schema::hasTable('settlements')) {
            if (Schema::hasColumn('marketplace_settlements', 'marketplace_order_id')) {
                Schema::table('marketplace_settlements', function (Blueprint $table) {
                    $table->dropForeign(['marketplace_order_id']);
                });
                Schema::table('marketplace_settlements', function (Blueprint $table) {
                    $table->renameColumn('marketplace_order_id', 'order_id');
                });
            }
            Schema::rename('marketplace_settlements', 'settlements');
            Schema::table('settlements', function (Blueprint $table) {
                $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
            });
        }

        if (Schema::hasTable('payments') && Schema::hasColumn('payments', 'marketplace_order_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropForeign(['marketplace_order_id']);
            });
            Schema::table('payments', function (Blueprint $table) {
                $table->renameColumn('marketplace_order_id', 'order_id');
            });
            Schema::table('payments', function (Blueprint $table) {
                $table->foreign('order_id')->references('id')->on('orders')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        // Intentionally empty — do not restore legacy names.
    }
};
