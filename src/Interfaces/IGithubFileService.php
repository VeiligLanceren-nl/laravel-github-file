<?php

namespace VeiligLanceren\GithubFile\Interfaces;

interface IGithubFileService
{
    /**
     * Get the content of a file from a GitHub repository.
     *
     * @param string $repository The GitHub repository in the format 'owner/repo'.
     * @param string $filePath   The path to the file within the repository.
     * @param string $branch     The branch name. Defaults to 'main'.
     *
     * @return string The content of the file.
     */
    public function get(string $repository, string $filePath, string $branch = 'main'): string;

    /**
     * Download a file from a GitHub repository and store it locally.
     *
     * @param string $repository The GitHub repository in the format 'owner/repo'.
     * @param string $filePath   The path to the file within the repository.
     * @param string $disk       The disk where the file should be stored.
     * @param string $branch     The branch name. Defaults to 'main'.
     *
     * @return string The path where the file was stored.
     */
    public function download(string $repository, string $filePath, string $disk = 'local', string $branch = 'main'): string;

    /**
     * Create a ZIP archive containing a file from a GitHub repository.
     *
     * @param string $repository The GitHub repository in the format 'owner/repo'.
     * @param string|array $filePaths
     * @param string $disk The disk where the ZIP file should be stored.
     * @param string $branch The branch name. Defaults to 'main'.
     *
     * @return string The path where the ZIP file was stored.
     */
    public function zip(string $repository, string|array $filePaths, string $disk = 'local', string $branch = 'main'): string;
}