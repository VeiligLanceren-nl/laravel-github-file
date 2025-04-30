<?php

namespace VeiligLanceren\GithubFile\Services;

use RuntimeException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use VeiligLanceren\GithubFile\Interfaces\IFileZipService;
use VeiligLanceren\GithubFile\Interfaces\IGithubFileService;

class GithubFileService implements IGithubFileService
{
    /**
     * @var IFileZipService
     */
    private IFileZipService $fileZipService;

    public function __construct()
    {
        $this->fileZipService = app()->make(IFileZipService::class);
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
    public function zip(string $repository, string $filePath, string $disk = 'local', string $branch = 'main'): string
    {
        $files = [];
        $filename = basename($filePath);

        if (substr($filePath, -1) === '/') {
            $filePath = rtrim($filePath, '/');
            $url = "https://api.github.com/repos/{$repository}/contents/{$filePath}?ref={$branch}";
            $response = Http::get($url);

            if ($response->successful()) {
                $filesData = $response->json();

                foreach ($filesData as $file) {
                    if ($file['type'] === 'file') {
                        $fileContent = Http::get($file['download_url'])->body();
                        $files[] = ['name' => $file['name'], 'content' => $fileContent];
                    }
                }
            } else {
                throw new RuntimeException("Failed to fetch directory contents from GitHub: {$url}");
            }
        } else {
            $fileContent = $this->get($repository, $filePath, $branch);
            $files[] = ['name' => $filename, 'content' => $fileContent];
        }

        return $this->fileZipService->createZip($filePath, $files, $disk);
    }

}