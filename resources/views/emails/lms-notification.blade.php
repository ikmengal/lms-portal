@extends('emails.layout')
@section('title', $title)
@section('preheader', $body)
@section('content')
    <h1 style="margin:0 0 16px 0; font-size:22px; line-height:30px; font-weight:bold; color:{{ $brand['textDark'] }};">
        {{ $title }}
    </h1>

    <p style="margin:0 0 8px 0; font-size:15px; line-height:24px; color:{{ $brand['textDark'] }};">
        Hi {{ $user->name }},
    </p>
    <p style="margin:0 0 24px 0; font-size:14px; line-height:22px; color:{{ $brand['textMuted'] }};">
        {{ $body }}
    </p>

    @if($url)
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
            <tr>
                <td align="center" bgcolor="{{ $brand['bg'] }}" style="border-radius:12px; padding:20px;">
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td align="center" bgcolor="{{ $brand['primary'] }}" style="border-radius:10px;">
                                <a href="{{ $url }}" target="_blank"
                                   style="display:inline-block; padding:13px 36px; font-size:15px; font-weight:bold; color:#ffffff; text-decoration:none; border-radius:10px; background-color:{{ $brand['primary'] }};">
                                    View Details
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
    @endif
@endsection
