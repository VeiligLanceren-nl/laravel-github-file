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
    public function zip(string $repository, string|array $filePaths, string $disk = 'local', string $branch = 'main'): string
    {
        $filePaths = is_array($filePaths) ? $filePaths : [$filePaths];
        $allFiles = [];

        foreach ($filePaths as $filePath) {
            $cleanPath = rtrim($filePath, '/');
            $url = "https://api.github.com/repos/{$repository}/contents/{$cleanPath}?ref={$branch}";
            $response = Http::get($url);

            if (! $response->successful()) {
                throw new RuntimeException("Failed to fetch file from GitHub: {$url}");
            }

            $data = $response->json();

            if (array_is_list($data)) {
                foreach ($data as $item) {
                    if ($item['type'] === 'file') {
                        $fileContent = Http::get($item['download_url'])->body();
                        $allFiles[] = ['name' => $item['name'], 'content' => $fileContent];
                    }
                }
            } else {
                $content = base64_decode($data['content'] ?? '');
                $allFiles[] = ['name' => basename($filePath), 'content' => $content];
            }
        }

        $zipName = 'github-files';

        return $this->fileZipService->createZip($zipName, $allFiles, $disk);
    }

}