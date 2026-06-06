<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        if (! Schema::hasColumn('users', 'created_at') || ! Schema::hasColumn('users', 'updated_at')) {
            return;
        }

        $anchor = $this->lastDataColumn();

        if ($anchor === null) {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY created_at TIMESTAMP NULL DEFAULT NULL AFTER `{$anchor}`");
        DB::statement('ALTER TABLE users MODIFY updated_at TIMESTAMP NULL DEFAULT NULL AFTER created_at');
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        if (! Schema::hasColumn('users', 'created_at') || ! Schema::hasColumn('users', 'updated_at')) {
            return;
        }

        if (! Schema::hasColumn('users', 'remember_token')) {
            return;
        }

        DB::statement('ALTER TABLE users MODIFY created_at TIMESTAMP NULL DEFAULT NULL AFTER remember_token');
        DB::statement('ALTER TABLE users MODIFY updated_at TIMESTAMP NULL DEFAULT NULL AFTER created_at');
    }

    protected function lastDataColumn(): ?string
    {
        $candidates = [
            'accept_only_kyc_customers',
            'pincode',
            'state',
            'city',
            'address_line2',
            'address_line1',
            'remember_token',
        ];

        foreach ($candidates as $column) {
            if (Schema::hasColumn('users', $column)) {
                return $column;
            }
        }

        return null;
    }
};
