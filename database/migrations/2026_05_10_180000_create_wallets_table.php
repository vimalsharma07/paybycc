<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('balance', 15, 2)->default(0);
            $table->boolean('auto_settle_to_bank')->default(true);
            $table->foreignId('default_bank_id')->nullable()->constrained('banks')->nullOnDelete();
            $table->timestamps();

            $table->unique('user_id');
        });

        foreach (DB::table('users')->pluck('id') as $userId) {
            DB::table('wallets')->insert([
                'user_id' => $userId,
                'balance' => 0,
                'auto_settle_to_bank' => true,
                'default_bank_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
