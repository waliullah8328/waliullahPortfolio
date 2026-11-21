<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Stores uploads directly under public/uploads so no storage symlink is needed
 * (symlinks require admin rights on Windows).
 */
class Media
{
    public static function uploadsEnabled(): bool
    {
        return (bool) config('filesystems.uploads_enabled', true);
    }

    public static function store(UploadedFile $file, string $folder): string
    {
        if (! static::uploadsEnabled()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'upload' => 'File uploads are turned off on this host. Paste a link (Google Drive, Dropbox, ...) instead.',
            ]);
        }

        $dir = public_path("uploads/{$folder}");
        File::ensureDirectoryExists($dir);

        $base = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'file';
        $ext = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());
        $name = Str::limit($base, 40, '').'-'.Str::random(8).'.'.$ext;
        $target = $dir.DIRECTORY_SEPARATOR.$name;

        // Write the file directly instead of UploadedFile::move(): on OneDrive folders Windows
        // reports a "read-only" flag that makes is_writable() fail even though writing works.
        $ok = @move_uploaded_file($file->getPathname(), $target)
            || (@copy($file->getPathname(), $target) && (@unlink($file->getPathname()) || true));

        if (! $ok) {
            throw new \RuntimeException("Could not save the uploaded file to {$dir}.");
        }

        return "uploads/{$folder}/{$name}";
    }

    /**
     * Resolve a media field: an uploaded file wins, then a pasted link (Google Drive, Dropbox
     * or any https URL), otherwise the old value is kept. Replaced local files are deleted.
     */
    public static function replace(?UploadedFile $file, ?string $old, string $folder, ?string $link = null): ?string
    {
        if ($file) {
            static::delete($old);

            return static::store($file, $folder);
        }

        $link = trim((string) $link);
        if ($link !== '' && $link !== $old) {
            static::delete($old);

            return $link;
        }

        return $old;
    }

    public static function isExternal(?string $path): bool
    {
        return (bool) $path && Str::startsWith($path, ['http://', 'https://']);
    }

    /** The file id inside a Google Drive / Docs share link, if it is one. */
    public static function driveId(string $url): ?string
    {
        if (! preg_match('~^https?://(drive|docs)\.google\.com/~i', $url)) {
            return null;
        }
        if (preg_match('~/d/([A-Za-z0-9_-]{10,})~', $url, $m)) {
            return $m[1];
        }
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        return isset($query['id']) && preg_match('~^[A-Za-z0-9_-]{10,}$~', $query['id']) ? $query['id'] : null;
    }

    /** Turn a share link into one a browser can use directly as an <img> or a download. */
    public static function externalUrl(string $url, bool $file = false): string
    {
        if ($id = static::driveId($url)) {
            return $file
                ? "https://drive.google.com/file/d/{$id}/view"        // opens Google's PDF viewer with a download button
                : "https://lh3.googleusercontent.com/d/{$id}=w2000";   // direct image
        }

        if (preg_match('~^https?://(www\.)?dropbox\.com/~i', $url)) {
            $url = preg_replace('~([?&])(dl|raw)=\d~', '$1', $url);
            $url = rtrim($url, '?&');

            return $url.(str_contains($url, '?') ? '&' : '?').($file ? 'dl=1' : 'raw=1');
        }

        return $url;
    }

    public static function delete(?string $path): void
    {
        if ($path && str_starts_with($path, 'uploads/') && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }

    /** Public URL for a stored path or a pasted link. Pass $file = true for documents (CV). */
    public static function url(?string $path, bool $file = false): ?string
    {
        if (! $path) {
            return null;
        }

        return static::isExternal($path) ? static::externalUrl($path, $file) : asset($path);
    }
}
