<?php

namespace VeiligLanceren\GithubFile;

use Illuminate\Support\ServiceProvider;
use VeiligLanceren\GithubFile\Services\FileZipService;
use VeiligLanceren\GithubFile\Services\GithubFileService;
use VeiligLanceren\GithubFile\Interfaces\IFileZipService;
use VeiligLanceren\GithubFile\Interfaces\IGithubFileService;

class GithubFileServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(IFileZipService::class, FileZipService::class);
        $this->app->singleton(IGithubFileService::class, GithubFileService::class);
    }

    /**
     * @return void
     */
    public function boot(): void
    {}
}