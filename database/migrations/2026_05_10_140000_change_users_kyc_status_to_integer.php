<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $type = Schema::getColumnType('users', 'kyc_status');

        if ($type === 'varchar') {
            DB::table('users')->where('kyc_status', 'pending')->update(['kyc_status' => -1]);
            DB::table('users')->where('kyc_status', 'verified')->update(['kyc_status' => 1]);
            DB::table('users')->whereNotIn('kyc_status', [-1, 1])->update(['kyc_status' => 0]);

            Schema::table('users', function (Blueprint $table) {
                $table->smallInteger('kyc_status')->default(-1)->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('kyc_status')->default('pending')->change();
        });

        DB::table('users')->where('kyc_status', -1)->update(['kyc_status' => 'pending']);
        DB::table('users')->where('kyc_status', 1)->update(['kyc_status' => 'verified']);
        DB::table('users')->where('kyc_status', 0)->update(['kyc_status' => 'pending']);
    }
};
