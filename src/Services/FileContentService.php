<?php

namespace VeiligLanceren\GithubFile\Services;

use Illuminate\Support\Facades\Http;
use VeiligLanceren\GithubFile\Exceptions\GithubFileException;
use VeiligLanceren\GithubFile\Interfaces\IFileContentService;

class FileContentService implements IFileContentService
{
    /**
     * {@inheritDoc}
     */
    public function get(string $repository, string $filePath, ?string $branch = null): string
    {
        $cleanPath = rtrim($filePath, '/');
        $branch = $branch ?? 'main';

        $url = "https://raw.githubusercontent.com/{$repository}/{$branch}/{$cleanPath}";

        $response = Http::get($url);

        if (!$response->successful()) {
            throw new GithubFileException(
                repository: $repository,
                filePath: $filePath,
                branch: $branch,
                statusCode: $response->status()
            );
        }

        return $response->body();
    }

    /**
     * {@inheritDoc}
     */
    public function getDirectoryListing(string $repository, string $path, ?string $branch = null): array
    {
        $branch = $branch ?? 'main';
        $url = "https://api.github.com/repos/{$repository}/contents/{$path}?ref={$branch}";

        $response = Http::get($url);

        if (!$response->successful()) {
            throw new GithubFileException(
                repository: $repository,
                filePath: $path,
                branch: $branch,
                statusCode: $response->status()
            );
        }

        return $response->json();
    }
}