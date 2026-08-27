<?php

namespace App\Support;

use App\Models\Setting;

class Brand
{
    public static function data(): array
    {
        try {
            $settings = Setting::allCached();
        } catch (\Throwable) {
            $settings = [];
        }

        $name = $settings['site_name'] ?? config('app.name', 'LMS Portal');

        return [
            'name' => $name,
            'tagline' => $settings['site_tagline'] ?? null,
            'logoUrl' => ($settings['logo'] ?? null)
                ? asset('assets/upload/' . ltrim($settings['logo'], '/'))
                : null,
            'supportEmail' => $settings['support_email']
                ?? $settings['contact_email']
                ?? config('mail.from.address'),
            'copyright' => trim(($name ? $name . ' ' : '') . ($settings['copyright_text'] ?? '')),
            'url' => rtrim(config('app.url'), '/') ?: url('/'),

            // Theme colors (mirror resources/css/app.css @theme palette)
            'primary' => '#1b4ff5',
            'primaryDark' => '#1730b6',
            'primaryDarker' => '#192e8f',
            'primaryDarkest' => '#141d57',
            'accent' => '#ff7a11',
            'bg' => '#f3f4f6',
            'textDark' => '#111827',
            'textMuted' => '#6b7280',
            'border' => '#e5e7eb',

            // Wordmark split: "LMS Portal" -> ["LMS", "Portal"]
            'wordmarkMain' => self::wordmark($name)['main'],
            'wordmarkAccent' => self::wordmark($name)['accent'],
        ];
    }

    public static function wordmark(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];

        if (count($parts) < 2) {
            return ['main' => $name, 'accent' => ''];
        }

        return [
            'main' => implode(' ', array_slice($parts, 0, -1)),
            'accent' => end($parts),
        ];
    }

    public static function fromAddress(): array
    {
        return [self::data()['supportEmail'], self::data()['name']];
    }
}
