<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use SoftDeletes;

    protected $fillable = ['key', 'value'];

    public static function cacheKey(): string
    {
        return 'site.settings';
    }

    public static function allCached(): array
    {
        return Cache::rememberForever(self::cacheKey(), function () {
            return self::query()->pluck('value', 'key')->all();
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = self::allCached();

        $value = $settings[$key] ?? null;

        if ($value === null || $value === '') {
            return $default;
        }

        return $value;
    }

    public static function set(string|array $key, mixed $value = null): void
    {
        $items = is_array($key) ? $key : [$key => $value];

        foreach ($items as $k => $v) {
            self::updateOrCreate(['key' => $k], ['value' => $v]);
        }

        self::flushCache();
    }

    public static function flushCache(): void
    {
        Cache::forget(self::cacheKey());
    }
}
