@extends('mail.ops.layout')

@section('heading', 'New user registered')

@section('body')
    <p style="margin:0 0 16px;">A new account was created on the platform.</p>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;background:#0f172a;border-radius:8px;border:1px solid #334155;">
        <tr>
            <td style="padding:12px 16px;color:#94a3b8;font-size:12px;width:38%;">Name</td>
            <td style="padding:12px 16px;color:#f1f5f9;font-weight:600;">{{ $user->name }}</td>
        </tr>
        <tr>
            <td style="padding:12px 16px;border-top:1px solid #334155;color:#94a3b8;font-size:12px;">Email</td>
            <td style="padding:12px 16px;border-top:1px solid #334155;color:#f1f5f9;font-family:ui-monospace,monospace;font-size:13px;">{{ $user->email }}</td>
        </tr>
        <tr>
            <td style="padding:12px 16px;border-top:1px solid #334155;color:#94a3b8;font-size:12px;">Phone</td>
            <td style="padding:12px 16px;border-top:1px solid #334155;color:#f1f5f9;font-family:ui-monospace,monospace;font-size:13px;">{{ $user->phone }}</td>
        </tr>
        <tr>
            <td style="padding:12px 16px;border-top:1px solid #334155;color:#94a3b8;font-size:12px;">User code</td>
            <td style="padding:12px 16px;border-top:1px solid #334155;color:#f1f5f9;font-family:ui-monospace,monospace;font-size:13px;">{{ $user->user_code }}</td>
        </tr>
        <tr>
            <td style="padding:12px 16px;border-top:1px solid #334155;color:#94a3b8;font-size:12px;">Registered at</td>
            <td style="padding:12px 16px;border-top:1px solid #334155;color:#f1f5f9;font-size:13px;">{{ $user->created_at?->timezone(config('app.timezone'))->toDayDateTimeString() ?? '—' }}</td>
        </tr>
    </table>
@endsection
