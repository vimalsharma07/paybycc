@extends('mail.ops.layout')

@section('heading', 'New transaction')

@section('body')
    <p style="margin:0 0 16px;">A new transaction record was created.</p>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;background:#0f172a;border-radius:8px;border:1px solid #334155;">
        <tr>
            <td style="padding:12px 16px;color:#94a3b8;font-size:12px;width:38%;">Type</td>
            <td style="padding:12px 16px;color:#f1f5f9;font-weight:600;">{{ $transaction->type_label }}</td>
        </tr>
        <tr>
            <td style="padding:12px 16px;border-top:1px solid #334155;color:#94a3b8;font-size:12px;">Amount</td>
            <td style="padding:12px 16px;border-top:1px solid #334155;color:#f1f5f9;font-weight:600;">{{ $transaction->amount }} {{ $transaction->currency }}</td>
        </tr>
        <tr>
            <td style="padding:12px 16px;border-top:1px solid #334155;color:#94a3b8;font-size:12px;">Status</td>
            <td style="padding:12px 16px;border-top:1px solid #334155;color:#f1f5f9;">{{ $transaction->status }}</td>
        </tr>
        <tr>
            <td style="padding:12px 16px;border-top:1px solid #334155;color:#94a3b8;font-size:12px;">User</td>
            <td style="padding:12px 16px;border-top:1px solid #334155;color:#f1f5f9;">
                @if ($transaction->user)
                    {{ $transaction->user->name }}<br>
                    <span style="font-family:ui-monospace,monospace;font-size:12px;color:#94a3b8;">{{ $transaction->user->email }} · {{ $transaction->user->user_code }}</span>
                @else
                    —
                @endif
            </td>
        </tr>
        @if ($transaction->payment)
            <tr>
                <td style="padding:12px 16px;border-top:1px solid #334155;color:#94a3b8;font-size:12px;">Payment</td>
                <td style="padding:12px 16px;border-top:1px solid #334155;color:#f1f5f9;font-family:ui-monospace,monospace;font-size:12px;">#{{ $transaction->payment->id }} · {{ $transaction->payment->status }}</td>
            </tr>
        @endif
        @if ($transaction->bank)
            <tr>
                <td style="padding:12px 16px;border-top:1px solid #334155;color:#94a3b8;font-size:12px;">Bank</td>
                <td style="padding:12px 16px;border-top:1px solid #334155;color:#f1f5f9;font-size:13px;">{{ $transaction->bank->bank_name ?? 'Account #'.$transaction->bank_id }}</td>
            </tr>
        @endif
        <tr>
            <td style="padding:12px 16px;border-top:1px solid #334155;color:#94a3b8;font-size:12px;vertical-align:top;">Note</td>
            <td style="padding:12px 16px;border-top:1px solid #334155;color:#cbd5e1;font-size:13px;">{{ $transaction->note ?: '—' }}</td>
        </tr>
        <tr>
            <td style="padding:12px 16px;border-top:1px solid #334155;color:#94a3b8;font-size:12px;">Transaction ID</td>
            <td style="padding:12px 16px;border-top:1px solid #334155;color:#f1f5f9;font-family:ui-monospace,monospace;font-size:12px;">#{{ $transaction->id }}</td>
        </tr>
    </table>
@endsection
