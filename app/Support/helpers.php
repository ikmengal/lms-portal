<?php

use App\Models\Setting;

if (! function_exists('supported_languages')) {
    /**
     * All languages a course can be delivered in (code => info array).
     */
    function supported_languages(): array
    {
        return config('portal.supported_languages', []);
    }
}

if (! function_exists('language_info')) {
    /**
     * Metadata for a single language code, or null when unknown.
     */
    function language_info(?string $code): ?array
    {
        if (! $code) {
            return null;
        }

        return config('portal.supported_languages.' . $code);
    }
}

if (! function_exists('content_lang')) {
    /**
     * Resolve the requested content language from the `?lang=` query string,
     * falling back to null (the course's primary language). Unknown codes are ignored.
     */
    function content_lang(): ?string
    {
        $code = (string) request()->query('lang');

        if (! $code || ! language_info($code)) {
            return null;
        }

        return $code;
    }
}

if (! function_exists('setting')) {
    /**
     * Get a site setting value (or update multiple settings at once).
     */
    function setting(string|array $key, mixed $default = null): mixed
    {
        if (is_array($key)) {
            Setting::set($key);

            return true;
        }

        return Setting::get($key, $default);
    }
}

if (! function_exists('setting_image')) {
    /**
     * Get the public URL of an uploaded setting image (null when not set).
     */
    function setting_image(string $key): ?string
    {
        $value = Setting::get($key);

        return $value ? asset('assets/upload/' . ltrim($value, '/')) : null;
    }
}

if (! function_exists('qr_svg')) {
    /**
     * Inline SVG QR code (works in browsers and dompdf PDFs via php-svg-lib).
     */
    function qr_svg(string $data, int $size = 100): string
    {
        return preg_replace(
            '/<\?xml[^>]*\?>/',
            '',
            \SimpleSoftwareIO\QrCode\Facades\QrCode::size($size)->margin(0)->errorCorrection('M')->generate($data)
        );
    }
}

if (! function_exists('seal_png_data_uri')) {
    /**
     * Render the notary-style verification seal as a PNG data URI via GD.
     *
     * Matches the browser SVG seal: serrated edge, concentric rings,
     * "OFFICIALLY VERIFIED" arc, center checkmark, "CERTIFIED" text.
     *
     * @param  string $year    Year to display (e.g. "2026").
     * @param  int    $size    Output image size in pixels (square).
     * @return string          Data URI string (data:image/png;base64,...).
     */
    function seal_png_data_uri(string $year, int $size = 200): string
    {
        // Use palette image for much smaller PNG size
        $img = imagecreate($size, $size);
        $transparent = imagecolorallocate($img, 0, 0, 0);
        imagecolortransparent($img, $transparent);
        imagefill($img, 0, 0, $transparent);

        $c   = $size / 2;
        $s   = $size / 170; // scale from 170-unit viewBox
        $orange  = imagecolorallocate($img, 255, 122, 17);
        $ltOrange = imagecolorallocate($img, 255, 150, 56);
        $deepOrange = imagecolorallocate($img, 240, 94, 7);
        $white   = imagecolorallocate($img, 255, 255, 255);
        $gold    = imagecolorallocate($img, 255, 179, 122);

        // 1. Serrated outer edge (dashed circle r=80, stroke=7)
        $r = 80 * $s;
        $steps = 120;
        for ($i = 0; $i < $steps; $i++) {
            $angle = ($i / $steps) * 360;
            if (($i % 3) < 2) { // dash pattern: 2 on, 1 off
                $rad = deg2rad($angle);
                $x1 = $c + ($r - 3 * $s) * cos($rad);
                $y1 = $c + ($r - 3 * $s) * sin($rad);
                $x2 = $c + ($r + 3 * $s) * cos($rad);
                $y2 = $c + ($r + 3 * $s) * sin($rad);
                imageline($img, (int) $x1, (int) $y1, (int) $x2, (int) $y2, $orange);
            }
        }

        // 2. Main ring (r=71, stroke=2.5) with faint fill
        $r = 71 * $s;
        $lw = max(1, (int) round(2.5 * $s));
        imagearc($img, (int) ($c - $r), (int) ($c - $r), (int) ($r * 2), (int) ($r * 2), 0, 360, $orange);
        // Fill between rings with very faint orange
        for ($i = 0; $i < $lw; $i++) {
            $ri = $r - $i;
            imagearc($img, (int) ($c - $ri), (int) ($c - $ri), (int) ($ri * 2), (int) ($ri * 2), 0, 360, $orange);
        }

        // 3. Inner ring (r=49, stroke=1.5)
        $r = 49 * $s;
        imagearc($img, (int) ($c - $r), (int) ($c - $r), (int) ($r * 2), (int) ($r * 2), 0, 360, $orange);

        // 4. Side separator dots
        $dotR = 2.5 * $s;
        imagefilledellipse($img, (int) (25 * $s), (int) $c, (int) ($dotR * 2), (int) ($dotR * 2), $ltOrange);
        imagefilledellipse($img, (int) (145 * $s), (int) $c, (int) ($dotR * 2), (int) ($dotR * 2), $ltOrange);

        // 5. "OFFICIALLY VERIFIED" — approximate curved text with positioned chars
        $text1 = 'OFFICIALLY VERIFIED';
        $fontW = max(3, (int) (3.2 * $s));
        $fontH = max(4, (int) (4.5 * $s));
        $arcR  = 60 * $s;
        $startAngle = -155;
        $endAngle   = -25;
        $span = $endAngle - $startAngle;
        $charSpacing = $span / max(1, strlen($text1) - 1);
        for ($i = 0; $i < strlen($text1); $i++) {
            $angle = deg2rad($startAngle + $i * $charSpacing);
            $cx = $c + $arcR * cos($angle);
            $cy = $c + $arcR * sin($angle);
            $char = $text1[$i];
            imagestring($img, 5, (int) ($cx - $fontW / 2), (int) ($cy - $fontH / 2), $char, $gold);
        }

        // 6. "★ LMS PORTAL ★" — bottom arc
        $text2 = '* LMS PORTAL *';
        $arcR2  = 60 * $s;
        $startAngle2 = 25;
        $endAngle2   = 155;
        $span2 = $endAngle2 - $startAngle2;
        $charSpacing2 = $span2 / max(1, strlen($text2) - 1);
        for ($i = 0; $i < strlen($text2); $i++) {
            $angle = deg2rad($startAngle2 + $i * $charSpacing2);
            $cx = $c + $arcR2 * cos($angle);
            $cy = $c + $arcR2 * sin($angle);
            $char = $text2[$i];
            imagestring($img, 4, (int) ($cx - $fontW / 2), (int) ($cy - 3 * $s), $char, $gold);
        }

        // 7. Center certification circle (r=15, fill orange)
        $centerY = $c - 12 * $s; // slightly above center like SVG cy=73 vs viewBox 85
        $cr = 15 * $s;
        imagefilledellipse($img, (int) $c, (int) $centerY, (int) ($cr * 2), (int) ($cr * 2), $deepOrange);

        // 8. Checkmark (white stroke)
        // Simplified checkmark: 3 points forming a V
        $ckPts = [
            [(int) ($c - 5 * $s), (int) ($centerY + 1 * $s)],
            [(int) ($c - 1 * $s), (int) ($centerY + 5 * $s)],
            [(int) ($c + 7 * $s), (int) ($centerY - 5 * $s)],
        ];
        for ($i = 0; $i < count($ckPts) - 1; $i++) {
            $lw2 = max(2, (int) round(3 * $s));
            for ($off = -1; $off <= 1; $off++) {
                imageline($img, $ckPts[$i][0] + $off, $ckPts[$i][1], $ckPts[$i + 1][0] + $off, $ckPts[$i + 1][1], $white);
                imageline($img, $ckPts[$i][0], $ckPts[$i][1] + $off, $ckPts[$i + 1][0], $ckPts[$i + 1][1] + $off, $white);
            }
        }

        // 9. Horizontal line below checkmark
        $lineY = (int) ($centerY + 24 * $s);
        $lineX1 = (int) ($c - 19 * $s);
        $lineX2 = (int) ($c + 19 * $s);
        imageline($img, $lineX1, $lineY, $lineX2, $lineY, $orange);

        // 10. "CERTIFIED" text
        $certText = 'CERTIFIED';
        $certFontW = max(4, (int) (4 * $s));
        $certX = (int) ($c - (strlen($certText) * $certFontW) / 2);
        $certY = (int) ($lineY + 4 * $s);
        imagestring($img, 5, $certX, $certY, $certText, $white);

        // 11. Year text
        $yearFontW = max(3, (int) (3.5 * $s));
        $yearX = (int) ($c - (strlen($year) * $yearFontW) / 2);
        $yearY = $certY + max(8, (int) (9 * $s));
        imagestring($img, 4, $yearX, $yearY, $year, $gold);

        // Output
        ob_start();
        imagepng($img);
        $raw = ob_get_clean();
        imagedestroy($img);

        return 'data:image/png;base64,' . base64_encode($raw);
    }
}

if (! function_exists('certificate_badge_data_uri')) {
    /**
     * Return the certificate badge as a base64 data URI.
     * Uses the admin-uploaded badge if present, otherwise falls back to the GD-rendered seal.
     *
     * @param  string $year  Year to display on the fallback seal.
     * @param  int    $size  Size for the fallback seal in pixels.
     * @return string        Data URI string.
     */
    function certificate_badge_data_uri(string $year, int $size = 256): string
    {
        $path = \App\Models\Setting::get('certificate_badge');

        if ($path && \Illuminate\Support\Facades\Storage::disk('upload')->exists($path)) {
            $fullPath = \Illuminate\Support\Facades\Storage::disk('upload')->path($path);
            $raw      = file_get_contents($fullPath);
            $mime     = mime_content_type($fullPath) ?: 'image/png';

            return 'data:' . $mime . ';base64,' . base64_encode($raw);
        }

        return seal_png_data_uri($year, $size);
    }
}

if (! function_exists('qr_table')) {
    /**
     * QR code as an HTML table — 100% compatible with dompdf (no SVG rendering needed).
     *
     * @param  string  $data    Data to encode.
     * @param  int     $modules Number of modules (pixels) per QR cell.
     * @param  string  $fg      Foreground colour (hex).
     * @param  string  $bg      Background colour (hex).
     * @return string           Raw HTML <table>.
     */
    function qr_table(string $data, int $modules = 3, string $fg = '#000000', string $bg = '#ffffff'): string
    {
        $encoder  = new \BaconQrCode\Encoder\Encoder();
        $qrCode   = $encoder->encode(
            $data,
            \BaconQrCode\Common\ErrorCorrectionLevel::M(),
            'ISO-8859-1',
            null
        );
        $matrix   = $qrCode->getMatrix();
        $width    = $matrix->getWidth();
        $height   = $matrix->getHeight();

        $html = '<table cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">';
        for ($y = 0; $y < $height; $y++) {
            $html .= '<tr>';
            for ($x = 0; $x < $width; $x++) {
                $fill = $matrix->get($x, $y) ? $fg : $bg;
                $html .= '<td style="width:'.$modules.'px;height:'.$modules.'px;background-color:'.$fill.';">&nbsp;</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';

        return $html;
    }
}

if (! function_exists('qr_png_data_uri')) {
    /**
     * QR code as a base64-encoded PNG data URI — reliable in dompdf.
     *
     * @param  string $data   Data to encode.
     * @param  int    $size   Image size in pixels.
     * @return string         Data URI string (data:image/png;base64,...).
     */
    function qr_png_data_uri(string $data, int $size = 150): string
    {
        $encoder = new \BaconQrCode\Encoder\Encoder();
        $qrCode  = $encoder->encode(
            $data,
            \BaconQrCode\Common\ErrorCorrectionLevel::M(),
            'ISO-8859-1',
            null
        );
        $matrix  = $qrCode->getMatrix();
        $w       = $matrix->getWidth();
        $h       = $matrix->getHeight();

        $moduleSize = max(1, (int) floor($size / max($w, $h)));
        $imgW       = $w * $moduleSize;
        $imgH       = $h * $moduleSize;

        $img = imagecreatetruecolor($imgW, $imgH);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        imagefill($img, 0, 0, $white);

        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                if ($matrix->get($x, $y)) {
                    imagefilledrectangle(
                        $img,
                        $x * $moduleSize,
                        $y * $moduleSize,
                        ($x + 1) * $moduleSize - 1,
                        ($y + 1) * $moduleSize - 1,
                        $black
                    );
                }
            }
        }

        ob_start();
        imagepng($img);
        $raw = ob_get_clean();
        imagedestroy($img);

        return 'data:image/png;base64,' . base64_encode($raw);
    }
}
