<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $subject ?? config('app.name') }}</title>
</head>
<body style="margin:0;padding:0;background-color:#0f172a;font-family:ui-sans-serif,system-ui,-apple-system,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#0f172a;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background-color:#1e293b;border-radius:12px;border:1px solid #334155;overflow:hidden;">
                    <tr>
                        <td style="padding:20px 24px;border-bottom:1px solid #334155;">
                            <p style="margin:0;font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:#818cf8;">{{ config('app.name') }}</p>
                            <p style="margin:6px 0 0;font-size:18px;font-weight:700;color:#f8fafc;">@yield('heading')</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;color:#cbd5e1;font-size:14px;line-height:1.6;">
                            @yield('body')
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:12px 24px 20px;border-top:1px solid #334155;">
                            <p style="margin:0;font-size:11px;color:#64748b;">This is an automated operations message. Sent {{ now()->timezone(config('app.timezone'))->toDayDateTimeString() }}.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
