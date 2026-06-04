<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;

/**
 * Token-protected deploy helpers (migrate, fresh DB, cache clear).
 * Disable secrets in .env after use.
 */
class DeployController extends Controller
{
    public function cacheClear(Request $request): Response
    {
        $this->authorizeToken($request, config('app.cache_clear_secret'), 'CACHE_CLEAR_SECRET');

        Artisan::call('optimize:clear');

        return $this->plainResponse('optimize:clear completed.');
    }

    public function migrate(Request $request): Response
    {
        $this->authorizeToken($request, config('app.migrate_secret'), 'MIGRATE_SECRET');

        Artisan::call('migrate', ['--force' => true]);

        return $this->plainResponse(trim(Artisan::output()) ?: 'migrate --force completed.');
    }

    /**
     * Drops all tables and re-runs migrations (like db:wipe + migrate).
     * Destroys all data. Requires ALLOW_DB_WIPE=true and DB_WIPE_SECRET.
     */
    public function dbFresh(Request $request): Response
    {
        if (! config('app.allow_db_wipe')) {
            abort(403, 'DB fresh route disabled. Set ALLOW_DB_WIPE=true in .env only when you intend to wipe data.');
        }

        $this->authorizeToken($request, config('app.db_wipe_secret'), 'DB_WIPE_SECRET');

        $seed = $request->boolean('seed');

        Artisan::call('migrate:fresh', [
            '--force' => true,
            '--seed' => $seed,
        ]);

        $lines = [
            'WARNING: All database tables were dropped and recreated.',
            'migrate:fresh --force'.($seed ? ' --seed' : '').' completed.',
            '',
            trim(Artisan::output()),
        ];

        return $this->plainResponse(implode("\n", array_filter($lines)));
    }

    protected function authorizeToken(Request $request, mixed $secret, string $envKey): void
    {
        if (! is_string($secret) || $secret === '') {
            abort(403, "Route disabled. Set {$envKey} in .env.");
        }

        $token = (string) $request->query('token', '');
        if (! hash_equals($secret, $token)) {
            abort(403);
        }
    }

    protected function plainResponse(string $body): Response
    {
        return response($body, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
