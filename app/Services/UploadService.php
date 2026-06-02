<?php

namespace App\Services;

use Illuminate\Support\Str; 

class UploadService
{
    public static function upload($files, string $folder = 'posts'): array
    {
        $files = is_array($files) ? $files : [$files];

        $uploadedFiles = [];

        $uploadPath = public_path("uploads/{$folder}");

        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        foreach ($files as $file) {
            $mimeType = $file->getMimeType();
            $fileSize = $file->getSize();
            $originalName = $file->getClientOriginalName();

            $extension = $file->getClientOriginalExtension();

            $filename = Str::slug(
                pathinfo($originalName, PATHINFO_FILENAME)
            ) . '-' . uniqid() . '.' . $extension;

            $file->move($uploadPath, $filename);

            $uploadedFiles[] = [
                'file_path' => "uploads/{$folder}/{$filename}",
                'file_name' => $originalName,
                'mime_type' => $mimeType,
                'file_size' => $fileSize,
            ];
        }

        return $uploadedFiles;
    }
}
