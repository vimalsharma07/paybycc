<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->foreignId('order_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->foreignId('transaction_id')->nullable()->after('order_id')->constrained()->nullOnDelete();

            $table->index(['order_id', 'created_at']);
            $table->index(['transaction_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['transaction_id']);
            $table->dropIndex(['order_id', 'created_at']);
            $table->dropIndex(['transaction_id', 'created_at']);
            $table->dropColumn(['order_id', 'transaction_id']);
        });
    }
};
