<?php

namespace App\Enums;

enum LogLevel: string
{
    case Debug = 'debug';
    case Info = 'info';
    case Notice = 'notice';
    case Warning = 'warning';
    case Error = 'error';
    case Critical = 'critical';

    public function badgeClass(): string
    {
        return match ($this) {
            self::Debug => 'bg-slate-100 text-slate-700 ring-slate-300/50',
            self::Info => 'bg-sky-100 text-sky-900 ring-sky-300/50',
            self::Notice => 'bg-indigo-100 text-indigo-900 ring-indigo-300/50',
            self::Warning => 'bg-amber-100 text-amber-900 ring-amber-300/50',
            self::Error => 'bg-rose-100 text-rose-900 ring-rose-300/50',
            self::Critical => 'bg-red-200 text-red-950 ring-red-400/50',
        };
    }
}
