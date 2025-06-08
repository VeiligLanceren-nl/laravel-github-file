<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use VeiligLanceren\GithubFile\Services\GithubFileService;

beforeEach(function () {
    Storage::fake('local');
    Http::preventStrayRequests();
});

function githubContentFake(string $repo, string $path, string $content): array {
    $urlRaw = "https://raw.githubusercontent.com/{$repo}/main/{$path}";

    return [
        $urlRaw => Http::response($content, 200),
    ];
}

function githubFolderFake(string $repo, string $folder, array $files): array {
    $cleanFolder = rtrim($folder, '/');
    $folderUrl1 = "https://api.github.com/repos/{$repo}/contents/{$cleanFolder}?ref=main";
    $folderUrl2 = "https://api.github.com/repos/{$repo}/contents/{$cleanFolder}/?ref=main";

    $responses = [
        $folderUrl1 => Http::response(
            collect($files)->map(fn($file) => [
                'type' => 'file',
                'name' => $file,
                'download_url' => "https://raw.githubusercontent.com/{$repo}/main/{$cleanFolder}/{$file}",
            ])->toArray(),
            200
        ),
        $folderUrl2 => Http::response(
            collect($files)->map(fn($file) => [
                'type' => 'file',
                'name' => $file,
                'download_url' => "https://raw.githubusercontent.com/{$repo}/main/{$cleanFolder}/{$file}",
            ])->toArray(),
            200
        ),
    ];

    foreach ($files as $file) {
        $responses["https://raw.githubusercontent.com/{$repo}/main/{$cleanFolder}/{$file}"] = Http::response("{$file} content", 200);
    }

    return $responses;
}

it('fetches file content from GitHub', function () {
    $repo = 'VeiligLanceren-nl/laravel-github-file';
    $path = 'tests/README.md';

    Http::fake([
        "https://raw.githubusercontent.com/{$repo}/main/{$path}" => Http::response('file content', 200),
    ]);

    $service = new GithubFileService();
    $content = $service->get($repo, $path);

    expect($content)->toBe('file content');
});

it('downloads a file and stores it locally', function () {
    $repo = 'VeiligLanceren-nl/laravel-github-file';
    $path = 'tests/README.md';

    Http::fake([
        "https://raw.githubusercontent.com/{$repo}/main/{$path}" => Http::response('file content', 200),
    ]);

    $service = new GithubFileService();
    $downloadPath = $service->download($repo, $path);

    Storage::disk('local')->assertExists('downloads/README.md');
    expect($downloadPath)->toBe(Storage::disk('local')->path('downloads/README.md'));
});

it('creates a zip archive containing a single file', function () {
    $repo = 'VeiligLanceren-nl/laravel-github-file';
    $path = 'path/to/file.txt';

    Http::fake([
        "https://api.github.com/repos/VeiligLanceren-nl/laravel-github-file/contents/path/to/file.txt?ref=main" =>
            Http::response([], 404),
        "https://raw.githubusercontent.com/VeiligLanceren-nl/laravel-github-file/main/path/to/file.txt" =>
            Http::response('zip me', 200),
        "https://api.github.com/repos/VeiligLanceren-nl/laravel-github-file/contents/README.md?ref=main" =>
            Http::response([], 404),
        "https://raw.githubusercontent.com/VeiligLanceren-nl/laravel-github-file/main/README.md" =>
            Http::response('readme content', 200),
    ]);

    $service = new GithubFileService();
    $zipPath = $service->zip($repo, $path);

    Storage::disk('local')->assertExists('zips/github-files.zip');
    expect($zipPath)->toBe(Storage::disk('local')->path('zips/github-files.zip'));

    $zip = new ZipArchive();
    $zip->open($zipPath);
    expect($zip->locateName('path/to/file.txt'))->not->toBeFalse();
    expect($zip->getFromName('path/to/file.txt'))->toBe('zip me');
    $zip->close();
});

it('creates a zip archive from a folder and a file', function () {
    $repo = 'VeiligLanceren-nl/laravel-github-file';

    Http::fake([
        ...githubFolderFake($repo, 'folder', ['file1.txt', 'file2.txt']),
        ...githubContentFake($repo, 'README.md', 'readme content'),
        "https://api.github.com/repos/{$repo}/contents/README.md?ref=main" =>
            Http::response([], 404),
        "https://api.github.com/repos/{$repo}/contents/folder/file1.txt?ref=main" => Http::response([], 404),
        "https://api.github.com/repos/{$repo}/contents/folder/file2.txt?ref=main" => Http::response([], 404),
    ]);

    $service = new GithubFileService();
    $zipPath = $service->zip($repo, ['README.md', 'folder/file1.txt', 'folder/file2.txt']);

    Storage::disk('local')->assertExists('zips/github-files.zip');

    $zip = new ZipArchive();
    $zip->open($zipPath);

    expect($zip->locateName('folder/file1.txt'))->not->toBeFalse();
    expect($zip->locateName('folder/file2.txt'))->not->toBeFalse();
    expect($zip->locateName('README.md'))->not->toBeFalse();

    expect($zip->getFromName('folder/file1.txt'))->toBe('file1.txt content');
    expect($zip->getFromName('folder/file2.txt'))->toBe('file2.txt content');
    expect($zip->getFromName('README.md'))->toBe('readme content');

    $zip->close();
});
