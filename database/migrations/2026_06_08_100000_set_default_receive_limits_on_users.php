<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->decimal('daily_limit', 15, 2)->default(100000)->change();
            $table->decimal('monthly_limit', 15, 2)->default(300000)->change();
            $table->decimal('yearly_limit', 15, 2)->default(2000000)->change();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->decimal('daily_limit', 15, 2)->default(0)->change();
            $table->decimal('monthly_limit', 15, 2)->default(0)->change();
            $table->decimal('yearly_limit', 15, 2)->default(0)->change();
        });
    }
};
