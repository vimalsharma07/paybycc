<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payment_links')) {
            return;
        }

        if (! Schema::hasColumn('payment_links', 'is_default')) {
            Schema::table('payment_links', function (Blueprint $table) {
                $table->boolean('is_default')->default(false)->after('description');
                $table->index(['seller_id', 'is_default']);
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('payment_links') || ! Schema::hasColumn('payment_links', 'is_default')) {
            return;
        }

        Schema::table('payment_links', function (Blueprint $table) {
            $table->dropIndex(['seller_id', 'is_default']);
            $table->dropColumn('is_default');
        });
    }
};
