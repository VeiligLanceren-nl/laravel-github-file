<?php

namespace VeiligLanceren\GithubFile\Interfaces;

interface IFileZipService
{
    /**
     * Create a zip archive of a single file or multiple files in a directory.
     *
     * @param string $disk The disk to store the zip file.
     * @param string $filePath The file or folder path.
     * @param array $files Array of file content and names (for multiple files).
     * @return string The path to the created zip file.
     */
    public function createZip(string $filePath, array $files, string $disk = 'local'): string;
}