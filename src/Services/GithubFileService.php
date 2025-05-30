<?php

namespace VeiligLanceren\GithubFile\Services;

use RuntimeException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use VeiligLanceren\GithubFile\Interfaces\IFileZipService;
use VeiligLanceren\GithubFile\Interfaces\IGithubFileService;
use VeiligLanceren\GithubFile\Exceptions\GithubFileException;
use VeiligLanceren\GithubFile\Interfaces\IFileContentService;

class GithubFileService implements IGithubFileService
{
    /**
     * @var IFileZipService
     */
    private IFileZipService $fileZipService;

    /**
     * @var IFileContentService
     */
    private IFileContentService $fileContentService;

    public function __construct()
    {
        $this->fileZipService = app()->make(IFileZipService::class);
        $this->fileContentService = app()->make(IFileContentService::class);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $repository, string $filePath, string $branch = 'main'): string
    {
        $url = "https://raw.githubusercontent.com/{$repository}/{$branch}/{$filePath}";
        $response = Http::get($url);

        if ($response->successful()) {
            return $response->body();
        }

        throw new RuntimeException("Failed to fetch file from GitHub: {$url}");
    }

    /**
     * {@inheritDoc}
     */
    public function download(
        string $repository,
        string $filePath,
        string $disk = 'local',
        string $branch = 'main'
    ): string
    {
        $content = $this->get($repository, $filePath, $branch);
        $filename = basename($filePath);
        $path = "downloads/{$filename}";

        Storage::disk($disk)
            ->put($path, $content);

        return Storage::disk($disk)
            ->path($path);
    }

    /**
     * {@inheritDoc}
     */
    public function zip(string $repository, string|array $filePaths, string $disk = 'local', string $branch = 'main'): string
    {
        $filePaths = is_array($filePaths) ? $filePaths : [$filePaths];
        $filePaths = array_filter(
            is_array($filePaths) ? $filePaths : [$filePaths],
            fn ($path) => !empty($path)
        );
        $allFiles = [];

        foreach ($filePaths as $filePath) {
            $allFiles = array_merge($allFiles, $this->collectFilesRecursively($repository, $filePath, $branch));
        }

        $zipName = 'github-files';

        return $this->fileZipService->createZip($zipName, $allFiles, $disk);
    }

    /**
     * Recursively collect all files from a GitHub directory path.
     *
     * @param string $repository
     * @param string $path
     * @param string $branch
     * @return array<array{name: string, content: string}>
     * @throws GithubFileException
     */
    private function collectFilesRecursively(string $repository, string $path, string $branch): array
    {
        try {
            $content = $this->fileContentService->get($repository, $path, $branch);
            return [[
                'name' => $path,
                'content' => $content,
            ]];
        } catch (GithubFileException) {
        }

        try {
            $items = $this->fileContentService->getDirectoryListing($repository, $path, $branch);
        } catch (GithubFileException) {
            return [];
        }

        $files = [];

        foreach ($items as $item) {
            if (($item['type'] ?? '') === 'file' && isset($item['download_url'])) {
                $fileContent = Http::get($item['download_url'])->body();
                $files[] = ['name' => $item['path'], 'content' => $fileContent];
            } elseif (($item['type'] ?? '') === 'dir' && isset($item['path'])) {
                $files = array_merge($files, $this->collectFilesRecursively($repository, $item['path'], $branch));
            }
        }

        return $files;
    }
}