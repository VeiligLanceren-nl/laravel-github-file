<?php

namespace VeiligLanceren\GithubFile\Services;

use Illuminate\Support\Facades\Log;
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
        $relativePath = "zips/{$zipFilename}";
        $tmpZip = tempnam(sys_get_temp_dir(), 'zip_');

        if ($tmpZip === false || !is_string($tmpZip)) {
            throw new RuntimeException("Could not create temporary file for zip");
        }

        if ($zip->open($tmpZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException("Unable to open zip archive: $tmpZip");
        }

        $fileCount = 0;
        foreach ($files as $file) {
            if (!isset($file['name'], $file['content'])) {
                continue;
            }

            if (!$zip->addFromString($file['name'], $file['content'])) {
                throw new RuntimeException("Failed to add file to zip: " . $file['name']);
            }

            $fileCount++;
        }

        $zip->close();

        if ($fileCount === 0) {
            throw new RuntimeException("No files were added to the zip archive.");
        }

        if (!file_exists($tmpZip)) {
            throw new RuntimeException("Temporary ZIP file was not created: $tmpZip");
        }

        $zipContents = file_get_contents($tmpZip);
        if ($zipContents === false) {
            throw new RuntimeException("Failed to read temporary ZIP file: $tmpZip");
        }

        Storage::disk($disk)->put($relativePath, $zipContents);
        unlink($tmpZip);

        return Storage::disk($disk)->path($relativePath);
    }
}