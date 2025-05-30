<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;
use VeiligLanceren\GithubFile\Services\GithubFileService;

beforeEach(function () {
    Storage::fake('local');
});

it('fetches the actual README.md from GitHub', function () {
    $repo = 'VeiligLanceren-nl/laravel-github-file';
    $path = 'README.md';

    $service = new GithubFileService();
    $content = $service->get($repo, $path);

    expect($content)
        ->toBeString()
        ->and($content)->toContain('Laravel GitHub File');
});

it('downloads the actual README.md from GitHub and stores it locally', function () {
    $repo = 'VeiligLanceren-nl/laravel-github-file';
    $path = 'README.md';

    $service = new GithubFileService();
    $downloadPath = $service->download($repo, $path);

    $relative = 'downloads/README.md';

    Storage::disk('local')->assertExists($relative);
    expect($downloadPath)->toBe(Storage::disk('local')->path($relative));
});

it('creates a real zip archive containing the README.md from GitHub', function () {
    $repo = 'VeiligLanceren-nl/laravel-github-file';
    $path = 'README.md';

    $service = new GithubFileService();
    $zipPath = $service->zip($repo, $path);

    $relative = str_replace(Storage::disk('local')->path(''), '', $zipPath);
    Storage::disk('local')->assertExists($relative);

    $zip = new ZipArchive();
    $opened = $zip->open($zipPath);
    expect($opened)->toBeTrue();

    $readmeInZip = $zip->locateName('README.md');
    expect($readmeInZip)->not->toBeFalse();

    $content = $zip->getFromName('README.md');
    expect($content)->toContain('Laravel GitHub File');

    $zip->close();
});