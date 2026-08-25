<?php $brand = \App\Support\Brand::data(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>@yield('title', 'Email') - {{ $brand['name'] }}</title>
</head>
<body style="margin:0; padding:0; background-color:{{ $brand['bg'] }}; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif; color:{{ $brand['textDark'] }};">

    {{-- Hidden preheader --}}
    <div style="display:none; max-height:0; overflow:hidden; mso-hide:all;">@yield('preheader', '')</div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:{{ $brand['bg'] }};">
        <tr>
            <td align="center" style="padding:32px 16px;">

                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px; width:100%; background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 4px 12px rgba(20,29,87,0.08);">

                    {{-- Header / Logo --}}
                    <tr>
                        <td align="center" style="padding:28px 40px 24px 40px; border-bottom:3px solid {{ $brand['primary'] }};">
                            @if($brand['logoUrl'])
                                <a href="{{ $brand['url'] }}" target="_blank"><img src="{{ $brand['logoUrl'] }}" alt="{{ $brand['name'] }}" height="44" style="height:44px; width:auto; max-width:220px; display:inline-block; vertical-align:middle; border:0;"></a>
                            @else
                                <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td valign="middle" style="vertical-align:middle;">
                                            <a href="{{ $brand['url'] }}" target="_blank" style="text-decoration:none;">
                                                <span style="font-size:22px; font-weight:bold; color:{{ $brand['primaryDarker'] }};">{{ $brand['wordmarkMain'] }}</span><span style="font-size:22px; font-weight:bold; color:{{ $brand['accent'] }};">{{ $brand['wordmarkAccent'] ? ' '.$brand['wordmarkAccent'] : '' }}</span>
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            @endif
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:36px 40px 8px 40px;">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:28px 40px 32px 40px; border-top:1px solid {{ $brand['border'] }}; margin-top:28px;">
                            <p style="margin:0 0 6px 0; font-size:12px; line-height:18px; color:{{ $brand['textMuted'] }};">
                                This email was sent to you because you have an account at {{ $brand['name'] }}.
                            </p>
                            <p style="margin:0 0 10px 0; font-size:12px; line-height:18px; color:{{ $brand['textMuted'] }};">
                                Need help? Contact us at
                                <a href="mailto:{{ $brand['supportEmail'] }}" style="color:{{ $brand['primary'] }}; text-decoration:none; font-weight:600;">{{ $brand['supportEmail'] }}</a>
                            </p>
                            <p style="margin:0; font-size:11px; line-height:17px; color:#9ca3af;">
                                &copy; {{ date('Y') }} {{ $brand['copyright'] ?: ($brand['name'] . '. All rights reserved.') }}
                            </p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</body>
</html>
