@extends('emails.layout')
@section('title', 'Reset Password')
@section('preheader', 'Use this link to reset your '.\App\Support\Brand::data()['name'].' password. Expires in '.$minutes.' minutes.')
@section('content')
    <h1 style="margin:0 0 16px 0; font-size:22px; line-height:30px; font-weight:bold; color:{{ $brand['textDark'] }};">
        Reset Your Password
    </h1>

    <p style="margin:0 0 8px 0; font-size:15px; line-height:24px; color:{{ $brand['textDark'] }};">
        Hi {{ $userName }},
    </p>
    <p style="margin:0 0 24px 0; font-size:14px; line-height:22px; color:{{ $brand['textMuted'] }};">
        We received a request to reset the password for your account on <strong>{{ $brand['name'] }}</strong>.
        Click the button below to choose a new password. This link expires in <strong>{{ $minutes }} minutes</strong>.
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td align="center" bgcolor="{{ $brand['bg'] }}" style="border-radius:12px; padding:20px;">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td align="center" bgcolor="{{ $brand['primary'] }}" style="border-radius:10px;">
                            <a href="{{ $url }}" target="_blank"
                               style="display:inline-block; padding:13px 36px; font-size:15px; font-weight:bold; color:#ffffff; text-decoration:none; border-radius:10px; background-color:{{ $brand['primary'] }};">
                                Reset Password
                            </a>
                        </td>
                    </tr>
                </table>
                <p style="margin:16px 0 0 0; font-size:12px; line-height:18px; color:{{ $brand['textMuted'] }}; word-break:break-all;">
                    Or copy this link into your browser:<br>
                    <a href="{{ $url }}" target="_blank" style="color:{{ $brand['primary'] }}; word-break:break-all;">{{ $url }}</a>
                </p>
            </td>
        </tr>
    </table>

    <p style="margin:24px 0 0 0; font-size:13px; line-height:20px; color:{{ $brand['textMuted'] }};">
        If you didn't request a password reset, you can safely ignore this email &mdash; your password will remain unchanged.
    </p>
@endsection
