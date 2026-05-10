<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('gateway_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->char('currency', 3)->default('INR');
            $table->string('status')->default('pending')->index();
            $table->string('gateway_reference')->nullable()->index();
            $table->json('driver_payload')->nullable();
            $table->timestamps();

            $table->index(['gateway_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
