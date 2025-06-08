<?php

declare(strict_types=1);

namespace VeiligLanceren\GithubFile\Exceptions;

use Exception;

/**
 * Exception thrown when a file cannot be fetched from GitHub.
 *
 * This exception provides detailed context about the failed request,
 * including the repository, file path, branch, and HTTP status code.
 */
class GithubFileException extends Exception
{
    /**
     * The GitHub repository in the format 'owner/repo'.
     *
     * @type string
     */
    protected string $repository;

    /**
     * The path to the file within the repository.
     *
     * @type string
     */
    protected string $filePath;

    /**
     * The branch from which the file was attempted to be fetched.
     *
     * @type string|null
     */
    protected ?string $branch = 'main';

    /**
     * The HTTP status code returned from the GitHub API.
     *
     * @type int
     */
    protected int $statusCode;

    /**
     * Construct a new GithubFileFetchException.
     *
     * @param string $repository The GitHub repository.
     * @param string $filePath The path to the file.
     * @param string|null $branch The branch name.
     * @param int $statusCode The HTTP status code.
     * @param string|null $message Optional custom error message.
     */
    public function __construct(
        string $repository,
        string $filePath,
        ?string $branch,
        int $statusCode,
        ?string $message = null
    ) {
        $this->repository = $repository;
        $this->filePath = $filePath;
        $this->branch = $branch;
        $this->statusCode = $statusCode;

        $errorMessage = $message ?? "Failed to fetch file from GitHub: {$repository}/{$filePath} on branch {$branch} (HTTP {$statusCode}).";

        parent::__construct($errorMessage, $statusCode);
    }

    /**
     * Get the GitHub repository.
     *
     * @return string
     */
    public function getRepository(): string
    {
        return $this->repository;
    }

    /**
     * Get the file path within the repository.
     *
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * Get the branch name.
     *
     * @return string|null
     */
    public function getBranch(): ?string
    {
        return $this->branch;
    }

    /**
     * Get the HTTP status code returned from the GitHub API.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Provide context information for logging purposes.
     *
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return [
            'repository' => $this->repository,
            'file_path' => $this->filePath,
            'branch' => $this->branch,
            'status_code' => $this->statusCode,
        ];
    }
}
