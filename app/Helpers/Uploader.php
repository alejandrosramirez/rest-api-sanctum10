<?php

namespace App\Helpers;

use App\Enums\DiskDriver;
use App\Exceptions\GenericException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class Uploader
{
    /**
     * Load image from request and save it to storage.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  \App\Enums\DiskDriver  $disk
     * @param  ?string  $lastFile
     * @param  int  $width
     * @param  int  $height
     */
    public static function saveImage(UploadedFile $file, DiskDriver $disk = DiskDriver::LOCAL, string $lastFile = null, int $width = 0, int $height = 0): array
    {
        if (null !== $lastFile) {
            self::deleteFile($lastFile, $disk);
        }

        if (0 == $width) {
            $width = Image::make($file)->width();
        }

        if (0 == $height) {
            $height = Image::make($file)->height();
        }

        $extension = $file->extension();

        $image = Image::make($file)->fit($width, $height)->encode($extension);

        $filename = 'image_' . md5($image->__toString() . rand()) . '.' . $extension;

        $imageSaved = Storage::disk($disk->value)->put($filename, $image);

        if (!$imageSaved) {
            throw new GenericException(__('An error occurred uploading the image.'));
        }

        $imageUrl = Storage::disk($disk->value)->url($filename);

        if (!$imageUrl) {
            throw new GenericException(__('File :filename not found', ['filename' => $filename]));
        }

        return [
            'url' => $imageUrl,
            'name' => $file->getClientOriginalName(),
        ];
    }

    /**
     * Load file/document from request and save it to storage.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  \App\Enums\DiskDriver  $disk
     */
    public static function saveFile(UploadedFile $file, DiskDriver $disk = DiskDriver::LOCAL): array
    {
        $extension = $file->extension();

        $filename = 'doc_' . md5($file->__toString() . rand()) . '.' . $extension;

        $fileSaved = Storage::disk($disk->value)->put($filename, $file);

        if (!$fileSaved) {
            throw new GenericException(__('An error occurred uploading the file.'));
        }

        $fileUrl = Storage::disk($disk->value)->url($filename);

        if (!$fileUrl) {
            throw new GenericException(__('File :filename not found', ['filename' => $filename]));
        }

        return [
            'url' => $fileUrl,
            'name' => $file->getClientOriginalName(),
        ];
    }

    /**
     * Delete image/file from request and save it to storage.
     */
    public static function deleteFile(string $file, DiskDriver $disk = DiskDriver::LOCAL): void
    {
        $url = explode('/', $file);
        $filename = $url[count($url) - 1];

        $exists = Storage::disk($disk->value)->exists($filename);

        if ($exists) {
            Storage::disk($disk->value)->delete($filename);
        }
    }
}
