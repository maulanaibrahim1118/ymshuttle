<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Image;

class ImageCompressor
{
    /**
     * Compress image from temp path to final public path
     */
    public static function compressFromTemp(
        string $tempPath,
        string $finalPath,
        int $maxWidth = 1280,
        int $quality = 75
    ): void {
        if (!Storage::exists($tempPath)) {
            return;
        }

        $raw = Storage::get($tempPath);

        $image = Image::make($raw)
            ->orientate() // penting untuk kamera HP
            ->resize($maxWidth, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode('jpg', $quality);

        Storage::put('public/' . $finalPath, (string) $image);

        // hapus temp
        Storage::delete($tempPath);
    }
}