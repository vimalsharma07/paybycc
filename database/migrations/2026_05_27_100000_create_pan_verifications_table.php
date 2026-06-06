<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pan_verifications')) {
            Schema::create('pan_verifications', function (Blueprint $table) {
                $table->id();
                $table->string('pan', 10)->unique();
                $table->text('response');
                $table->string('status', 20)->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasColumn('users', 'pan_type')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('pan_type', 30)->nullable()->after('pan');
            });
        }

        if (! Schema::hasColumn('users', 'dob')) {
            Schema::table('users', function (Blueprint $table) {
                $table->date('dob')->nullable()->after('pan_name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'dob')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('dob');
            });
        }

        if (Schema::hasColumn('users', 'pan_type')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('pan_type');
            });
        }

        Schema::dropIfExists('pan_verifications');
    }
};
