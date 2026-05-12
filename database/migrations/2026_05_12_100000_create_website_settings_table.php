<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name', 160);
            $table->string('tagline', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('support_email', 255)->nullable();
            $table->string('phone', 40)->nullable();
            $table->text('address')->nullable();
            $table->string('instagram_url', 500)->nullable();
            $table->string('linkedin_url', 500)->nullable();
            $table->string('facebook_url', 500)->nullable();
            $table->string('twitter_url', 500)->nullable();
            $table->string('logo_path', 500)->nullable()->comment('Public URL or path under public/, e.g. uploads/site/logo.png');
            $table->string('status', 20)->default('active')->index();
            $table->timestamps();
        });

        DB::table('website_settings')->insert([
            'site_name' => config('app.name', 'PayByCc'),
            'tagline' => 'Secure card payments & wallet settlements.',
            'email' => null,
            'support_email' => null,
            'phone' => null,
            'address' => null,
            'instagram_url' => null,
            'linkedin_url' => null,
            'facebook_url' => null,
            'twitter_url' => null,
            'logo_path' => null,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('website_settings');
    }
};
