<?php

use App\Models\Setting;

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

        return $value ? asset('storage/' . ltrim($value, '/')) : null;
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
