<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogController extends Controller
{
    public function index(Request $request): View
    {
        $channel = trim((string) $request->query('channel', ''));
        $level = trim((string) $request->query('level', ''));
        $event = trim((string) $request->query('event', ''));
        $q = trim((string) $request->query('q', ''));
        $dateFrom = trim((string) $request->query('date_from', ''));
        $dateTo = trim((string) $request->query('date_to', ''));

        $logs = ApplicationLog::query()
            ->with(['user:id,name,email'])
            ->when($channel !== '', fn ($query) => $query->where('channel', $channel))
            ->when($level !== '', fn ($query) => $query->where('level', $level))
            ->when($event !== '', fn ($query) => $query->where('event', 'like', '%'.$event.'%'))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($query) use ($q) {
                    $query
                        ->where('message', 'like', '%'.$q.'%')
                        ->orWhere('event', 'like', '%'.$q.'%')
                        ->orWhere('ip_address', 'like', '%'.$q.'%')
                        ->orWhere('context', 'like', '%'.$q.'%');
                });
            })
            ->when($dateFrom !== '', fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo !== '', fn ($query) => $query->whereDate('created_at', '<=', $dateTo))
            ->orderByDesc('id')
            ->paginate(40)
            ->withQueryString();

        $channels = ApplicationLog::query()
            ->select('channel')
            ->distinct()
            ->orderBy('channel')
            ->pluck('channel');

        if ($channels->isEmpty()) {
            $channels = collect(config('app_log.channels', []));
        }

        $events = ApplicationLog::query()
            ->when($channel !== '', fn ($query) => $query->where('channel', $channel))
            ->select('event')
            ->distinct()
            ->orderByDesc('event')
            ->limit(30)
            ->pluck('event');

        return view('admin.logs.index', [
            'logs' => $logs,
            'channels' => $channels,
            'events' => $events,
            'channel' => $channel,
            'level' => $level,
            'event' => $event,
            'q' => $q,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    public function show(ApplicationLog $log): View
    {
        $log->load(['user:id,name,email', 'subject']);

        return view('admin.logs.show', ['log' => $log]);
    }
}
