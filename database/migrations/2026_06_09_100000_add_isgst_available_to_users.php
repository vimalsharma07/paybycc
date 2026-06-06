<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users') || Schema::hasColumn('users', 'isgst_available')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('isgst_available')->default(false)->after('gstin');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'isgst_available')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('isgst_available');
        });
    }
};
