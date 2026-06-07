<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GatewayReqRes;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GatewayReqResController extends Controller
{
    public function index(Request $request): View
    {
        $status = trim((string) $request->query('status', ''));
        $transactionId = (int) $request->query('transaction_id', 0);
        $dateFrom = trim((string) $request->query('date_from', ''));
        $dateTo = trim((string) $request->query('date_to', ''));
        $q = trim((string) $request->query('q', ''));

        $entries = GatewayReqRes::query()
            ->with(['transaction:id,payment_id,amount,type,transaction_id'])
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($transactionId > 0, fn ($query) => $query->where('transaction_id', $transactionId))
            ->when($dateFrom !== '', fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo !== '', fn ($query) => $query->whereDate('created_at', '<=', $dateTo))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($query) use ($q) {
                    $query
                        ->where('status', 'like', '%'.$q.'%')
                        ->orWhere('req', 'like', '%'.$q.'%')
                        ->orWhere('response', 'like', '%'.$q.'%')
                        ->orWhere('webhook', 'like', '%'.$q.'%');
                });
            })
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        $statuses = GatewayReqRes::query()
            ->whereNotNull('status')
            ->select('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        return view('admin.gateway-req-res.index', [
            'entries' => $entries,
            'statuses' => $statuses,
            'status' => $status,
            'transactionId' => $transactionId > 0 ? $transactionId : '',
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'q' => $q,
        ]);
    }

    public function show(GatewayReqRes $gateway_req_res): View
    {
        $gateway_req_res->load(['transaction:id,payment_id,amount,type,status']);

        return view('admin.gateway-req-res.show', [
            'entry' => $gateway_req_res,
        ]);
    }
}
