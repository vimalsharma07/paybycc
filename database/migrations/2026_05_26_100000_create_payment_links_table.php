<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payment_links')) {
            Schema::create('payment_links', function (Blueprint $table) {
                $table->id();
                $table->string('link_token', 64)->unique();
                $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
                $table->decimal('amount', 15, 2);
                $table->char('currency', 3)->default('INR');
                $table->string('description', 500)->nullable();
                $table->string('status', 20)->default('open');
                $table->timestamp('expires_at')->nullable();
                $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();

                $table->index(['seller_id', 'status']);
            });
        }

        if (Schema::hasTable('payments') && ! Schema::hasColumn('payments', 'payment_link_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->foreignId('payment_link_id')->nullable()->after('order_id')->constrained('payment_links')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('payments') && Schema::hasColumn('payments', 'payment_link_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropConstrainedForeignId('payment_link_id');
            });
        }

        Schema::dropIfExists('payment_links');
    }
};
