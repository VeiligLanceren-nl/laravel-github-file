<?php

namespace VeiligLanceren\GithubFile\Interfaces;

use VeiligLanceren\GithubFile\Exceptions\GithubFileException;

interface IFileContentService
{
    /**
     * @param string $repository
     * @param string $filePath
     * @param string|null $branch
     * @return string
     * @throws GithubFileException
     */
    public function get(string $repository, string $filePath, ?string $branch): string;

    /**
     * @param string $repository
     * @param string $path
     * @param string|null $branch
     * @return array
     */
    public function getDirectoryListing(string $repository, string $path, ?string $branch = null): array;
}