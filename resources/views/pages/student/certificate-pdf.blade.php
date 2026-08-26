<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <style>
            @page { margin: 0; }
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: Helvetica, Arial, sans-serif; }

            .sheet {
                width: 1122px;
                height: 793px;
                position: relative;
                background: #0d1a5c;
            }

            /* Decorative glow blocks (solid color approximations) */
            .glow-tr { position: absolute; top: -120px; right: -100px; width: 380px; height: 380px; background: #7a3d0a; border-radius: 190px; opacity: .35; }
            .glow-bl { position: absolute; bottom: -140px; left: -110px; width: 420px; height: 420px; background: #1730b6; border-radius: 210px; opacity: .45; }

            /* Frame + tech corner brackets */
            .frame { position: absolute; top: 16px; left: 16px; right: 16px; bottom: 16px; border: 1px solid #b35410; border-radius: 16px; }
            .corner { position: absolute; width: 56px; height: 56px; }
            .corner-tl { top: 16px; left: 16px; border-top: 2px solid #ff7a11; border-left: 2px solid #ff7a11; border-top-left-radius: 16px; }
            .corner-tr { top: 16px; right: 16px; border-top: 2px solid #ff7a11; border-right: 2px solid #ff7a11; border-top-right-radius: 16px; }
            .corner-bl { bottom: 16px; left: 16px; border-bottom: 2px solid #4d6bff; border-left: 2px solid #4d6bff; border-bottom-left-radius: 16px; }
            .corner-br { bottom: 16px; right: 16px; border-bottom: 2px solid #4d6bff; border-right: 2px solid #4d6bff; border-bottom-right-radius: 16px; }

            .content {
                position: absolute; top: 34px; left: 64px; right: 64px; bottom: 34px;
                text-align: center;
            }

            .brand-row { font-size: 24px; font-weight: bold; color: #ffffff; letter-spacing: 2px; }
            .brand-row span { color: #ffb37a; }
            .logo-cell {
                width: 40px; height: 40px;
                background: #ff7a11; color: #ffffff;
                border-radius: 10px;
                text-align: center; vertical-align: middle;
                font-size: 20px; font-weight: bold;
            }
            .heading {
                font-size: 13px; letter-spacing: 9px; color: #ffb37a; font-weight: bold;
                text-transform: uppercase; margin-top: 26px;
            }
            .presented { font-style: italic; color: #f2c9a8; font-size: 14px; margin-top: 28px; }
            .name { font-size: 52px; font-weight: bold; color: #ffffff; margin-top: 6px; letter-spacing: -1px; }
            .divider { width: 340px; height: 1px; background: #ff7a11; margin: 12px auto 0 auto; }
            .completing { color: #f2c9a8; font-size: 13px; margin-top: 14px; }
            .course { font-size: 27px; font-weight: bold; color: #ffffff; margin-top: 6px; }
            .meta-pill-wrap { text-align: center; margin-top: 12px; }
            table.meta-pill {
                margin: 0 auto;
                border: 1px solid #b35410; background: #14226e;
                border-radius: 20px; border-collapse: separate;
            }
            table.meta-pill td { padding: 6px 22px; color: #ffe0c4; font-size: 11px; }

            .signatures { width: 100%; margin-top: 30px; }
            .signatures td { width: 33%; text-align: center; vertical-align: bottom; }
            .sig-line { font-style: italic; font-size: 17px; color: #ffffff; display: inline-block; padding: 0 30px 4px 30px; }
            .sig-orange { border-bottom: 1px solid #ff7a11; }
            .sig-blue { border-bottom: 1px solid #4d6bff; }
            .sig-role { font-size: 9px; letter-spacing: 4px; text-transform: uppercase; color: #f2c9a8; margin-top: 6px; }

            .seal-wrap { text-align: center; vertical-align: bottom; font-size: 0; line-height: 0; }
            .stamp-edge {
                width: 104px; height: 104px;
                border: 2px dashed #ff7a11;
                border-radius: 52px;
                margin: 0 auto;
                padding-top: 4px;
            }
            .stamp-outer {
                width: 92px; height: 92px;
                background: #131f66;
                border: 2px solid #ff7a11;
                border-radius: 46px;
                margin: 0 auto;
                text-align: center;
            }
            .stamp-inner {
                width: 76px; height: 76px;
                margin: 3px auto;
                border: 1px solid #ff7a11;
                border-radius: 38px;
                text-align: center;
            }
            .stamp-top { font-size: 6px; line-height: 9px; font-weight: bold; letter-spacing: 1px; color: #ffb37a; padding-top: 8px; }
            .stamp-check {
                width: 18px; height: 18px; line-height: 18px;
                margin: 3px auto;
                background: #f05e07; color: #ffffff;
                border-radius: 9px;
                font-size: 10px; font-weight: bold;
            }
            .stamp-check span { font-family: zapfdingbats; font-size: 11px; color: #ffffff; }
            .stamp-line { width: 36px; height: 1px; background: #ff7a11; margin: 0 auto; }
            .stamp-certified { font-size: 8px; line-height: 11px; font-weight: bold; letter-spacing: 2px; color: #ffffff; margin-top: 2px; }
            .stamp-year { font-size: 6.5px; line-height: 9px; letter-spacing: 1.5px; color: #ffb37a; margin-top: 1px; }

            .footer {
                position: absolute; left: 64px; right: 64px; bottom: 40px;
                border-top: 1px solid #2a3a8e; padding-top: 10px; text-align: center;
            }
            .footer p { font-size: 11px; color: #f2c9a8; }
            .footer .mono { font-family: Courier, monospace; font-weight: bold; color: #ffb37a; }
            .footer .url { font-family: Courier, monospace; color: #8ebbff; }
            .sep { color: #3a4da0; margin: 0 8px; }
        </style>
    </head>
    <body>
        <div class="sheet">
            <div class="glow-tr"></div>
            <div class="glow-bl"></div>
            <div class="frame"></div>
            <div class="corner corner-tl"></div>
            <div class="corner corner-tr"></div>
            <div class="corner corner-bl"></div>
            <div class="corner corner-br"></div>

            <div class="content">
                <table style="margin: 0 auto; border-collapse: separate;"><tr>
                    <td class="logo-cell">L</td>
                    <td class="brand-row" style="padding-left: 10px; vertical-align: middle;">LMS<span>PORTAL</span></td>
                </tr></table>

                <div class="heading">Certificate of Completion</div>

                <p class="presented">This certificate is proudly presented to</p>
                <h1 class="name">{{ $certificate->user->name }}</h1>
                <div class="divider"></div>

                <p class="completing">for successfully completing all requirements of the course</p>
                <h2 class="course">{{ $certificate->course->title }}</h2>
                <div class="meta-pill-wrap">
                    <table class="meta-pill"><tr><td>{{ $certificate->course->duration_hours }} hours of instruction &middot; {{ $certificate->course->category ?? 'General' }} &middot; {{ $certificate->course->level ?? 'All Levels' }}</td></tr></table>
                </div>

                <table class="signatures">
                    <tr>
                        <td>
                            <span class="sig-line sig-orange">{{ $certificate->course->instructor->name ?? 'LMS Portal' }}</span>
                            <div class="sig-role">Course Instructor</div>
                        </td>
                        <td class="seal-wrap">
                            <div class="stamp-edge">
                                <div class="stamp-outer">
                                    <div class="stamp-inner">
                                        <div class="stamp-top">OFFICIALLY VERIFIED</div>
                                        <div class="stamp-check"><span>3</span></div>
                                        <div class="stamp-line"></div>
                                        <div class="stamp-certified">CERTIFIED</div>
                                        <div class="stamp-year">{{ $certificate->issued_at->format('Y') }}</div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="sig-line sig-blue">{{ $certificate->issued_at->format('F j, Y') }}</span>
                            <div class="sig-role">Date of Issue</div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="footer">
                <table style="width: 100%; border-collapse: collapse;"><tr>
                    <td style="text-align: left; vertical-align: middle;">
                        Certificate ID: <span class="mono">{{ $certificate->code }}</span>
                        <span class="sep">|</span>
                        Verify at: <span class="url">{{ url('/verify-certificate/' . $certificate->code) }}</span>
                    </td>
                    <td style="width: 96px; text-align: right; vertical-align: middle; font-size: 0; line-height: 0;">
                        <span style="display: inline-block; vertical-align: middle; font-size: 8px; color: rgba(255,214,178,.55); padding-right: 6px;">SCAN TO VERIFY</span>
                        <span style="display: inline-block; background: #ffffff; border-radius: 4px; padding: 3px; vertical-align: middle;">{!! qr_svg($certificate->verificationUrl(), 72) !!}</span>
                    </td>
                </tr></table>
            </div>
        </div>
    </body>
</html>
