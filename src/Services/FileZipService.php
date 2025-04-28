<?php

namespace VeiligLanceren\GithubFile\Services;

use ZipArchive;
use RuntimeException;
use Illuminate\Support\Facades\Storage;
use VeiligLanceren\GithubFile\Interfaces\IFileZipService;

class FileZipService implements IFileZipService
{
    /**
     * {@inheritDoc}
     */
    public function createZip(string $filePath, array $files, string $disk = 'local'): string
    {
        $zip = new ZipArchive();
        $zipFilename = basename($filePath) . '.zip';
        $zipPath = "zips/{$zipFilename}";
        $diskPath = Storage::disk($disk)->path('zips');

        if (!is_dir($diskPath)) {
            mkdir($diskPath, 0777, true);
        }

        if ($zip->open(Storage::disk($disk)->path($zipPath), ZipArchive::CREATE) !== true) {
            throw new RuntimeException("Unable to create ZIP file at {$zipPath}");
        }

        foreach ($files as $file) {
            $zip->addFromString($file['name'], $file['content']);
        }

        $zip->close();

        return Storage::disk($disk)->path($zipPath);
    }
}