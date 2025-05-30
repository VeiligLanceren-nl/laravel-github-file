<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use VeiligLanceren\GithubFile\Exceptions\GithubFileException;
use VeiligLanceren\GithubFile\Services\FileContentService;

beforeEach(fn () => Http::preventStrayRequests());

it('fetches file content from GitHub', function () {
    Http::fake([
        'https://raw.githubusercontent.com/VeiligLanceren-nl/laravel-github-file/main/tests/README.md' =>
            Http::response('Hello World', 200),
    ]);

    $service = new FileContentService();
    $response = $service->get('VeiligLanceren-nl/laravel-github-file', 'tests/README.md', 'main');

    expect($response)->toBe('Hello World');
});

it('throws exception when GitHub API fails', function () {
    Http::fake([
        'https://raw.githubusercontent.com/VeiligLanceren-nl/laravel-github-file/main/tests/README.md' =>
            Http::response(null, 404),
    ]);

    $service = new FileContentService();

    expect(fn () => $service->get('VeiligLanceren-nl/laravel-github-file', 'tests/README.md', 'main'))
        ->toThrow(
            GithubFileException::class,
            'Failed to fetch file from GitHub: VeiligLanceren-nl/laravel-github-file/tests/README.md on branch main (HTTP 404).'
        );
});

it('fetches a directory listing from GitHub', function () {
    Http::fake([
        'https://api.github.com/repos/VeiligLanceren-nl/laravel-github-file/contents/src?ref=main' => Http::response([
            [
                'type' => 'file',
                'name' => 'GithubFileService.php',
                'path' => 'src/GithubFileService.php',
                'download_url' => 'https://raw.githubusercontent.com/VeiligLanceren-nl/laravel-github-file/main/src/GithubFileService.php',
            ],
            [
                'type' => 'file',
                'name' => 'FileContentService.php',
                'path' => 'src/FileContentService.php',
                'download_url' => 'https://raw.githubusercontent.com/VeiligLanceren-nl/laravel-github-file/main/src/FileContentService.php',
            ],
        ], 200),
    ]);

    $service = new FileContentService();
    $listing = $service->getDirectoryListing('VeiligLanceren-nl/laravel-github-file', 'src', 'main');

    expect($listing)->toBeArray()
        ->and($listing)->toHaveCount(2)
        ->and($listing[0])->toHaveKey('name', 'GithubFileService.php')
        ->and($listing[1])->toHaveKey('path', 'src/FileContentService.php');
});

it('throws exception if directory listing fails', function () {
    Http::fake([
        'https://api.github.com/repos/VeiligLanceren-nl/laravel-github-file/contents/bad-folder?ref=main' =>
            Http::response([], 404),
    ]);

    $service = new FileContentService();

    expect(fn () => $service->getDirectoryListing('VeiligLanceren-nl/laravel-github-file', 'bad-folder', 'main'))
        ->toThrow(
            GithubFileException::class,
            'Failed to fetch file from GitHub: VeiligLanceren-nl/laravel-github-file/bad-folder on branch main (HTTP 404).'
        );
});
