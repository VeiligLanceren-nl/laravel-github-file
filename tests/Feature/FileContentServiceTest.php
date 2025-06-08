<?php

declare(strict_types=1);

use VeiligLanceren\GithubFile\Services\FileContentService;

it('actually fetches a directory listing from GitHub', function () {
    $repo = 'VeiligLanceren-nl/laravel-github-file';
    $folder = 'src';

    $service = new FileContentService();
    $listing = $service->getDirectoryListing($repo, $folder);

    expect($listing)
        ->toBeArray()
        ->not->toBeEmpty()
        ->each->toHaveKeys(['name', 'path', 'type']);

    foreach ($listing as $item) {
        expect(in_array($item['type'], ['file', 'dir']))->toBeTrue();
    }
});
