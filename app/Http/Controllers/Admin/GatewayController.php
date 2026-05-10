<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGatewayRequest;
use App\Http\Requests\Admin\UpdateGatewayRequest;
use App\Models\Gateway;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GatewayController extends Controller
{
    public function index(): View
    {
        $gateways = Gateway::query()
            ->orderByDesc('is_primary')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.gateways.index', compact('gateways'));
    }

    public function create(): View
    {
        return view('admin.gateways.create');
    }

    public function store(StoreGatewayRequest $request): RedirectResponse
    {
        $payload = $request->validatedPayload();
        $primary = $request->boolean('is_primary');

        DB::transaction(function () use ($payload, $primary) {
            if ($primary) {
                Gateway::query()->update(['is_primary' => false]);
            }

            Gateway::create(array_merge($payload, [
                'is_primary' => $primary,
            ]));
        });

        return redirect()
            ->route('admin.gateways.index')
            ->with('status', 'Gateway created.');
    }

    public function edit(Gateway $gateway): View
    {
        return view('admin.gateways.edit', compact('gateway'));
    }

    public function update(UpdateGatewayRequest $request, Gateway $gateway): RedirectResponse
    {
        $payload = $request->validatedPayload();
        $primary = $request->boolean('is_primary');

        DB::transaction(function () use ($gateway, $payload, $primary) {
            if ($primary) {
                Gateway::query()->whereKeyNot($gateway->id)->update(['is_primary' => false]);
            }

            $gateway->fill(array_merge($payload, [
                'is_primary' => $primary,
            ]));
            $gateway->save();
        });

        return redirect()
            ->route('admin.gateways.edit', $gateway)
            ->with('status', 'Gateway updated.');
    }

    public function destroy(Gateway $gateway): RedirectResponse
    {
        $gateway->delete();

        return redirect()
            ->route('admin.gateways.index')
            ->with('status', 'Gateway removed.');
    }
}
