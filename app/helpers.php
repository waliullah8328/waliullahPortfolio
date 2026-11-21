<?php

use App\Models\Setting;
use App\Support\Media;

if (! function_exists('setting')) {
    /** Read a site setting managed from Admin > Settings. */
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('setting_on')) {
    /** Boolean toggle setting (stored as "1" / "0"). Defaults to on. */
    function setting_on(string $key, bool $default = true): bool
    {
        $value = Setting::get($key);

        return $value === null ? $default : (bool) (int) $value;
    }
}

if (! function_exists('media')) {
    function media(?string $path, bool $file = false): ?string
    {
        return Media::url($path, $file);
    }
}
