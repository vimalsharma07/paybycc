<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payment_links')) {
            return;
        }

        Schema::table('payment_links', function (Blueprint $table) {
            if (! Schema::hasColumn('payment_links', 'max_uses')) {
                $table->unsignedInteger('max_uses')->nullable()->after('description');
            }
            if (! Schema::hasColumn('payment_links', 'uses_count')) {
                $table->unsignedInteger('uses_count')->default(0)->after('max_uses');
            }
        });

        if (Schema::hasColumn('payment_links', 'amount')) {
            Schema::table('payment_links', function (Blueprint $table) {
                $table->decimal('amount', 15, 2)->nullable()->change();
            });
        }

        if (Schema::hasColumn('payment_links', 'max_uses')) {
            DB::table('payment_links')->whereNull('max_uses')->update(['max_uses' => 1]);
            DB::table('payment_links')->where('status', 'paid')->update(['uses_count' => 1]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('payment_links')) {
            return;
        }

        if (Schema::hasColumn('payment_links', 'uses_count')) {
            Schema::table('payment_links', function (Blueprint $table) {
                $table->dropColumn('uses_count');
            });
        }

        if (Schema::hasColumn('payment_links', 'max_uses')) {
            Schema::table('payment_links', function (Blueprint $table) {
                $table->dropColumn('max_uses');
            });
        }
    }
};
