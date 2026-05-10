<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('filename');
            $table->longText('credentials')->nullable();
            $table->string('status')->default('active')->index();
            $table->boolean('is_primary')->default(false)->index();
            $table->decimal('min_txn', 15, 2)->default(1);
            $table->decimal('max_txn', 15, 2)->default(999999.99);
            $table->decimal('daily_limit', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gateways');
    }
};
