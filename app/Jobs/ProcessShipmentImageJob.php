<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProcessShipmentImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tempPath;
    protected $finalPath;
    protected $watermarkText;

    public function __construct(string $tempPath, string $finalPath, string $watermarkText)
    {
        $this->tempPath = $tempPath;
        $this->finalPath = $finalPath;
        $this->watermarkText = $watermarkText;
    }

    public function handle(): void
    {
        if (!Storage::exists($this->tempPath)) {
            return;
        }

        $image = Image::make(storage_path('app/' . $this->tempPath))
            ->orientate(); // FIX ROTATE, APPLY KE PIXEL

        // resize
        $image->resize(1280, null, function ($c) {
            $c->aspectRatio();
            $c->upsize();
        });

        $fontSize = 30;
        $margin   = 40;

        $image->text(
            $this->watermarkText,
            $margin,                         // X dari kiri
            $image->height() - $margin,      // Y dari bawah
            function ($font) use ($fontSize) {
                $font->file(public_path('dist/fonts/roboto/Roboto-Regular.ttf'));
                $font->size($fontSize);
                $font->color('#ffffff');
                $font->align('left');        // KIRI
                $font->valign('bottom');     // BAWAH
            }
        );

        // encode → buang EXIF
        $image->encode('jpg', 75);

        Storage::put('public/' . $this->finalPath, (string) $image);
        Storage::delete($this->tempPath);
    }
}