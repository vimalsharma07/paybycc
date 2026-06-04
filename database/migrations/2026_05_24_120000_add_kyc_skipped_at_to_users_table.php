<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'kyc_skipped_at')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('kyc_skipped_at')->nullable()->after('phone_verified_at');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'kyc_skipped_at')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('kyc_skipped_at');
        });
    }
};
