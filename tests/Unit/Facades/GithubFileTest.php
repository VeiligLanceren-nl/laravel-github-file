<?php

use VeiligLanceren\GithubFile\Facades\GithubFile;
use VeiligLanceren\GithubFile\Interfaces\IGithubFileService;

beforeEach(function () {
    $this->githubFileServiceMock = Mockery::mock(IGithubFileService::class);

    app()->instance(IGithubFileService::class, $this->githubFileServiceMock);
});

it('fetches file content from GitHub', function () {
    $repository = 'owner/repo';
    $filePath = 'path/to/file.txt';
    $branch = 'main';
    $content = 'File content';

    $this->githubFileServiceMock
        ->shouldReceive('get')
        ->with($repository, $filePath, $branch)
        ->once()
        ->andReturn($content);

    $result = GithubFile::get($repository, $filePath, $branch);

    expect($result)->toBe($content);
});

it('downloads a file and stores it locally', function () {
    $repository = 'owner/repo';
    $filePath = 'path/to/file.txt';
    $branch = 'main';
    $disk = 'local';
    $storedPath = 'downloads/file.txt';

    $this->githubFileServiceMock
        ->shouldReceive('download')
        ->with($repository, $filePath, $disk, $branch)
        ->once()
        ->andReturn($storedPath);

    $result = GithubFile::download($repository, $filePath, $disk, $branch);

    expect($result)->toBe($storedPath);
});

it('creates a zip archive containing the file', function () {
    $repository = 'owner/repo';
    $filePath = 'path/to/file.txt';
    $branch = 'main';
    $disk = 'local';
    $zipPath = 'zips/file.zip';

    $this->githubFileServiceMock
        ->shouldReceive('zip')
        ->with($repository, $filePath, $disk, $branch)
        ->once()
        ->andReturn($zipPath);

    $result = GithubFile::zip($repository, $filePath, $disk, $branch);

    expect($result)->toBe($zipPath);
});
