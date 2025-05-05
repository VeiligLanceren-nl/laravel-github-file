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
    Http::fake([
        'https://api.github.com/repos/owner/repo/contents/path/to/file.txt?ref=main' => Http::response([
            'content' => base64_encode('file content'),
        ], 200),
    ]);

    $service = new GithubFileService();
    $zipPath = $service->zip('owner/repo', 'path/to/file.txt');
    $relativePath = str_replace(Storage::disk('local')->path(''), '', $zipPath);

    Storage::disk('local')->assertExists($relativePath);
    expect($zipPath)->toBe(Storage::disk('local')->path($relativePath));
});

it('creates a zip archive from multiple GitHub file and folder paths', function () {
    Http::fake([
        'https://api.github.com/repos/owner/repo/contents/folder?ref=main' => Http::response([
            [
                'type' => 'file',
                'name' => 'file1.txt',
                'download_url' => 'https://raw.githubusercontent.com/owner/repo/main/folder/file1.txt',
            ],
            [
                'type' => 'file',
                'name' => 'file2.txt',
                'download_url' => 'https://raw.githubusercontent.com/owner/repo/main/folder/file2.txt',
            ],
        ]),

        'https://raw.githubusercontent.com/owner/repo/main/folder/file1.txt' => Http::response('content 1', 200),
        'https://raw.githubusercontent.com/owner/repo/main/folder/file2.txt' => Http::response('content 2', 200),
        'https://api.github.com/repos/owner/repo/contents/README.md?ref=main' => Http::response([
            'content' => base64_encode('readme content'),
        ]),
    ]);

    $service = new GithubFileService();
    $zipPath = $service->zip('owner/repo', ['folder/', 'README.md']);

    expect($zipPath)->toBe(Storage::disk('local')->path('zips/github-files.zip'));

    Storage::disk('local')->assertExists('zips/github-files.zip');
});



