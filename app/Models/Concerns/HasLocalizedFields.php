<?php

namespace App\Models\Concerns;

trait HasLocalizedFields
{
    /**
     * The translation payload for a locale (or the requested content_lang()).
     */
    public function translation(?string $locale = null): ?array
    {
        $locale = $locale ?: content_lang();

        if (! $locale || empty($this->translations[$locale])) {
            return null;
        }

        return $this->translations[$locale];
    }

    /**
     * Whether a translation payload exists for the given locale.
     */
    public function hasTranslation(string $locale): bool
    {
        return ! empty($this->translations[$locale]);
    }

    /**
     * Return the localized value of a field (e.g. "title") for a locale,
     * transparently falling back to the primary-language column value.
     */
    public function localize(string $label, ?string $locale = null): mixed
    {
        $locale = $locale ?: content_lang();

        if ($locale && ! empty($this->translations[$locale][$label])) {
            return $this->translations[$locale][$label];
        }

        return $this->{$label};
    }
}