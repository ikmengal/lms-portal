<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Setting;
use Illuminate\Http\{
    UploadedFile,
    Request
};

class SettingController extends Controller
{
    private const IMAGE_FIELDS = ['logo', 'dark_logo', 'favicon', 'banner', 'certificate_badge', 'hero_image'];

    public function edit()
    {
        return view('pages.admin.settings');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            // General
            'site_name' => ['required', 'string', 'max:255'],
            'site_tagline' => ['nullable', 'string', 'max:255'],

            // Branding images
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg,webp', 'max:2048'],
            'dark_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg,webp', 'max:2048'],
            'favicon' => ['nullable', 'file', 'mimes:ico,png,jpg,jpeg,svg', 'max:1024'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'certificate_badge' => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg,webp', 'max:4096'],

            // Hero
            'hero_badge' => ['nullable', 'string', 'max:255'],
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_title_highlight' => ['nullable', 'string', 'max:255'],
            'hero_description' => ['nullable', 'string', 'max:500'],
            'hero_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],

            // About
            'about_title' => ['nullable', 'string', 'max:255'],
            'about_description' => ['nullable', 'string', 'max:5000'],
            'footer_description' => ['nullable', 'string', 'max:1000'],

            // Contact
            'contact_email' => ['nullable', 'email', 'max:255'],
            'support_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_address' => ['nullable', 'string', 'max:500'],
            'office_hours' => ['nullable', 'string', 'max:255'],

            // Social links
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'twitter_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'youtube_url' => ['nullable', 'url', 'max:255'],

            // SEO
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],

            // Misc
            'copyright_text' => ['nullable', 'string', 'max:255'],
            'maintenance_message' => ['nullable', 'string', 'max:500'],
            'maintenance_mode' => ['nullable', 'boolean'],
        ]);

        $textKeys = [
            'site_name',
            'site_tagline',
            'about_title',
            'about_description',
            'footer_description',
            'contact_email',
            'support_email',
            'contact_phone',
            'contact_address',
            'office_hours',
            'facebook_url',
            'twitter_url',
            'instagram_url',
            'linkedin_url',
            'youtube_url',
            'meta_title',
            'meta_description',
            'meta_keywords',
            'copyright_text',
            'maintenance_message',
            'hero_badge',
            'hero_title',
            'hero_title_highlight',
            'hero_description',
        ];

        $payload = [];
        foreach ($textKeys as $key) {
            $payload[$key] = $validated[$key] ?? null;
        }

        $payload['maintenance_mode'] = $request->boolean('maintenance_mode') ? '1' : '0';

        Setting::set($payload);

        foreach (self::IMAGE_FIELDS as $field) {
            if ($request->hasFile($field)) {
                $this->storeImage($request->file($field), $field);
            }
        }

        return redirect()
            ->route('admin.settings')
            ->with('success', 'Website settings updated successfully.');
    }

    public function removeImage(string $key)
    {
        abort_unless(in_array($key, self::IMAGE_FIELDS, true), 404);

        if ($value = Setting::get($key)) {
            Storage::disk('upload')->delete($value);
            Setting::set($key, null);
        }

        return back()->with('success', ucfirst(str_replace('_', ' ', $key)) . ' removed successfully.');
    }

    private function storeImage(UploadedFile $file, string $field): void
    {
        if ($old = Setting::get($field)) {
            Storage::disk('upload')->delete($old);
        }

        Setting::set($field, $file->store('settings/branding', 'upload'));
    }
}
