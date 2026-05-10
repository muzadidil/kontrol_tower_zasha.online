<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploader
{
    /**
     * Convert uploaded image to WebP, optionally resize, save to public disk.
     * Returns the relative path saved (suitable for storing in DB column).
     *
     * @param  UploadedFile $file
     * @param  string       $folder        Subdirectory under storage/app/public (e.g. "profil")
     * @param  int          $maxDimension  Resize so that the longest side is at most this many pixels.
     *                                     Pass 0 to skip resizing.
     * @param  int          $quality       WebP quality 0-100.
     * @param  string|null  $oldPath       Optional: previous file path to delete after successful upload.
     */
    public static function uploadWebp(
        UploadedFile $file,
        string $folder,
        int $maxDimension = 800,
        int $quality = 85,
        ?string $oldPath = null,
    ): string {
        if (! function_exists('imagewebp')) {
            throw new \RuntimeException('GD extension dengan dukungan WebP tidak tersedia di server ini.');
        }

        $img = self::createImageFromFile($file);

        if ($maxDimension > 0) {
            $img = self::resizeIfLarger($img, $maxDimension);
        }

        $filename     = Str::uuid()->toString() . '.webp';
        $relativePath = trim($folder, '/') . '/' . $filename;
        $absolutePath = Storage::disk('public')->path($relativePath);

        if (! is_dir(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0755, true);
        }

        imagewebp($img, $absolutePath, $quality);
        imagedestroy($img);

        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        return $relativePath;
    }

    private static function createImageFromFile(UploadedFile $file): \GdImage
    {
        $mime = $file->getMimeType();
        $path = $file->getRealPath();

        $img = match ($mime) {
            'image/jpeg', 'image/jpg' => imagecreatefromjpeg($path),
            'image/png'               => imagecreatefrompng($path),
            'image/webp'              => imagecreatefromwebp($path),
            'image/gif'               => imagecreatefromgif($path),
            default                   => null,
        };

        if (! $img) {
            throw new \RuntimeException('Format gambar tidak didukung: ' . $mime);
        }

        return $img;
    }

    private static function resizeIfLarger(\GdImage $src, int $maxDimension): \GdImage
    {
        $w = imagesx($src);
        $h = imagesy($src);
        $longest = max($w, $h);

        if ($longest <= $maxDimension) {
            return $src;
        }

        $ratio = $maxDimension / $longest;
        $newW  = (int) round($w * $ratio);
        $newH  = (int) round($h * $ratio);

        $dst = imagecreatetruecolor($newW, $newH);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);
        imagedestroy($src);

        return $dst;
    }
}
