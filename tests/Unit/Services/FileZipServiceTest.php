<?php

use Illuminate\Support\Facades\Storage;
use VeiligLanceren\GithubFile\Services\FileZipService;

beforeEach(function () {
    Storage::fake('local');
});

it('can create a zip file with a single file', function () {
    $fileZipService = new FileZipService();
    $filePath = 'folder/sample.txt';
    $files = [
        ['name' => 'sample.txt', 'content' => 'This is a test file content']
    ];
    $fileZipService->createZip($filePath, $files);

    Storage::disk('local')->assertExists('zips/sample.txt.zip');

    $zip = new ZipArchive();
    $zip->open(Storage::disk('local')->path('zips/sample.txt.zip'));

    expect($zip->numFiles)->toBe(1);
    expect($zip->getNameIndex(0))->toBe('sample.txt');

    $zip->close();
});

it('throws an exception if the zip file cannot be created', function () {
    Storage::shouldReceive('disk')
        ->with('local')
        ->andThrow(new RuntimeException("Unable to create ZIP file"));

    $fileZipService = new FileZipService();
    $filePath = 'folder/sample.txt';
    $files = [
        ['name' => 'sample.txt', 'content' => 'This is a test file content']
    ];

    expect(fn() => $fileZipService->createZip($filePath, $files))
        ->toThrow(RuntimeException::class, 'Unable to create ZIP file');
});

it('can create a zip file with multiple files', function () {
    $fileZipService = new FileZipService();
    $filePath = 'folder/';
    $files = [
        ['name' => 'file1.txt', 'content' => 'Content of file 1'],
        ['name' => 'file2.txt', 'content' => 'Content of file 2']
    ];

    $fileZipService->createZip($filePath, $files);

    Storage::disk('local')->assertExists('zips/folder.zip');

    $zip = new ZipArchive();
    $zip->open(Storage::disk('local')->path('zips/folder.zip'));

    expect($zip->numFiles)->toBe(2);
    expect($zip->getNameIndex(0))->toBe('file1.txt');
    expect($zip->getNameIndex(1))->toBe('file2.txt');

    $zip->close();
});
