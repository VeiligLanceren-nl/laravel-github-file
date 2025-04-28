<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use VeiligLanceren\GithubFile\Services\GithubFileService;

beforeEach(function () {
    Http::fake([
        'https://raw.githubusercontent.com/owner/repo/main/path/to/file.txt' => Http::response('file content', 200),
    ]);

    Storage::fake('local');
});

it('fetches file content from GitHub', function () {
    $service = new GithubFileService();
    $content = $service->get('owner/repo', 'path/to/file.txt');

    expect($content)->toBe('file content');
});

it('downloads a file and stores it locally', function () {
    $service = new GithubFileService();
    $path = $service->download('owner/repo', 'path/to/file.txt');

    Storage::disk('local')
        ->assertExists('downloads/file.txt');

    expect($path)
        ->toBe(Storage::disk('local')->path('downloads/file.txt'));
});

it('creates a zip archive containing the file', function () {
    $service = new GithubFileService();
    $zipPath = $service->zip('owner/repo', 'path/to/file.txt');

    Storage::disk('local')->assertExists('zips/file.txt.zip');

    expect($zipPath)
        ->toBe(Storage::disk('local')->path('zips/file.txt.zip'));
});
