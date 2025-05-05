<?php

namespace VeiligLanceren\GithubFile\Facades;

use Illuminate\Support\Facades\Facade;
use VeiligLanceren\GithubFile\Interfaces\IGithubFileService;

/**
 * @method static string get(string $repository, string $filePath, string $branch = 'main')
 * @method static string download(string $repository, string $filePath, string $disk = 'local', string $branch = 'main')
 * @method static string zip(string $repository, string|array $filePath, string $disk = 'local', string $branch = 'main')
 *
 * @see \VeiligLanceren\GithubFile\Services\GithubFileService
 */
class GithubFile extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return IGithubFileService::class;
    }
}