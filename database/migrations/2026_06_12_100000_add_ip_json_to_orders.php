<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('orders') || Schema::hasColumn('orders', 'ip_json')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->json('ip_json')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('orders') || ! Schema::hasColumn('orders', 'ip_json')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('ip_json');
        });
    }
};
