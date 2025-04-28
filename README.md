

# Laravel GitHub File

A Laravel package to fetch, download, and zip files from GitHub repositories.

## Installation

Install the package via Composer:

```bash
composer require veiliglanceren/laravel-github-file
```

## Configuration

Publish the configuration file (optional):

```bash
php artisan vendor:publish --provider="VeiligLanceren\GithubFile\GithubFileServiceProvider"
```

This will create a `config/github-file.php` file where you can customize settings like the default disk.

## Usage

### Fetch File Content

Retrieve the content of a file from a GitHub repository:

```php
use VeiligLanceren\GithubFile\Facades\GithubFile;

$content = GithubFile::get('owner/repo', 'path/to/file.txt');
```

### Download a File

Download a file and store it locally:

```php
$path = GithubFile::download('owner/repo', 'path/to/file.txt');
```

This will store the file in the default disk's `downloads` directory.

### Create a ZIP Archive

Create a ZIP archive containing a file from a GitHub repository:

```php
$zipPath = GithubFile::zip('owner/repo', 'path/to/file.txt');
```

The ZIP file will be stored in the default disk's `zips` directory.

## Facade Autocompletion

To enable autocompletion for the `GithubFile` facade in IDEs like PhpStorm, add the following PHPDoc block to the facade class:

```php
/**
 * @method static string get(string $repository, string $filePath, string $branch = 'main')
 * @method static string download(string $repository, string $filePath, string $disk = 'local', string $branch = 'main')
 * @method static string zip(string $repository, string $filePath, string $disk = 'local', string $branch = 'main')
 *
 * @see \VeiligLanceren\GithubFile\Services\GithubFileService
 */
```

This will provide autocompletion for the `get`, `download`, and `zip` methods.

## Testing

Run the tests using Pest:

```bash
./vendor/bin/pest
```

Ensure that your environment is set up correctly and that all dependencies are installed.

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository.
2. Create a new branch (`git checkout -b feature-branch`).
3. Make your changes.
4. Commit your changes (`git commit -am 'Add new feature'`).
5. Push to the branch (`git push origin feature-branch`).
6. Create a new Pull Request.

Please ensure that your code adheres to the project's coding standards and passes all tests.

## License

This package is open-source software licensed under the MIT license.
