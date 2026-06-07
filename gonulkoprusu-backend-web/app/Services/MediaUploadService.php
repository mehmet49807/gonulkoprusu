<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class MediaUploadService
{
    public const FOLDER_PROFILES = 'profiles';
    public const FOLDER_POSTS = 'posts';
    public const FOLDER_STORIES = 'stories';

    private string $baseUrl;

    /** @var list<string> */
    private array $disks;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('filesystems.disks.media_local.url', env('MEDIA_URL', 'https://gonulkoprusu.com/uploads')), '/');
        $this->disks = $this->resolveDiskOrder();
    }

    /**
     * Dosyayı yükler ve herkese açık URL döner.
     */
    public function upload(UploadedFile $file, string $folder): string
    {
        $filename = $this->generateFilename($file);
        $path = "{$folder}/{$filename}";
        $contents = file_get_contents($file->getRealPath());

        if ($contents === false) {
            throw new RuntimeException('Dosya okunamadı.');
        }

        $lastError = null;

        foreach ($this->disks as $disk) {
            try {
                $this->ensureFolderExists($folder, $disk);

                if ($this->store($disk, $path, $contents)) {
                    return "{$this->baseUrl}/{$path}";
                }
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
                Log::warning('Media disk write failed.', [
                    'disk' => $disk,
                    'path' => $path,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::error('Media upload failed on all disks.', [
            'path' => $path,
            'disks' => $this->disks,
            'last_error' => $lastError,
        ]);

        throw new RuntimeException('Dosya yüklenemedi.');
    }

    public function uploadProfilePhoto(UploadedFile $file): string
    {
        return $this->upload($file, self::FOLDER_PROFILES);
    }

    public function uploadPostImage(UploadedFile $file): string
    {
        return $this->upload($file, self::FOLDER_POSTS);
    }

    public function uploadStoryMedia(UploadedFile $file): string
    {
        return $this->upload($file, self::FOLDER_STORIES);
    }

    public function deleteByUrl(?string $url): void
    {
        if (!$url) {
            return;
        }

        $path = $this->urlToPath($url);
        if (!$path) {
            return;
        }

        foreach ($this->disks as $disk) {
            try {
                if (Storage::disk($disk)->exists($path)) {
                    Storage::disk($disk)->delete($path);
                }
            } catch (\Throwable) {
                // Silme hatası yüklemeyi engellemesin.
            }
        }
    }

    /** @return list<string> */
    private function resolveDiskOrder(): array
    {
        $configured = config('filesystems.media_disks', ['media_local', 'ftp_media']);
        $ordered = [];

        if ($this->localUploadsRootIsUsable()) {
            $ordered[] = 'media_local';
        }

        foreach ($configured as $disk) {
            if (!in_array($disk, $ordered, true)) {
                $ordered[] = $disk;
            }
        }

        return $ordered ?: ['media_local', 'ftp_media'];
    }

    private function localUploadsRootIsUsable(): bool
    {
        $root = config('filesystems.disks.media_local.root', public_path('uploads'));

        if (is_dir($root)) {
            return is_writable($root);
        }

        if (!is_dir(dirname($root)) || !is_writable(dirname($root))) {
            return false;
        }

        return @mkdir($root, 0755, true) || is_dir($root);
    }

    private function store(string $disk, string $path, string $contents): bool
    {
        return Storage::disk($disk)->put($path, $contents) === true;
    }

    private function urlToPath(string $url): ?string
    {
        $prefix = $this->baseUrl.'/';
        if (!str_starts_with($url, $prefix)) {
            return null;
        }

        return substr($url, strlen($prefix));
    }

    private function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg';

        return Str::uuid().'.'.strtolower($extension);
    }

    private function ensureFolderExists(string $folder, string $disk): void
    {
        $storage = Storage::disk($disk);

        foreach ([$folder] as $dir) {
            if (!$storage->exists($dir)) {
                $storage->makeDirectory($dir);
            }
        }

        if ($disk === 'media_local') {
            $profiles = config('filesystems.disks.media_local.root').'/'.self::FOLDER_PROFILES;
            $posts = config('filesystems.disks.media_local.root').'/'.self::FOLDER_POSTS;
            $stories = config('filesystems.disks.media_local.root').'/'.self::FOLDER_STORIES;
            foreach ([$profiles, $posts, $stories] as $dir) {
                if (!is_dir($dir)) {
                    @mkdir($dir, 0755, true);
                }
            }
        }
    }
}
