<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users') || Schema::hasColumn('users', 'allow_guest_payment_links')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('allow_guest_payment_links')->default(false)->after('accept_only_kyc_customers');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'allow_guest_payment_links')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('allow_guest_payment_links');
        });
    }
};
