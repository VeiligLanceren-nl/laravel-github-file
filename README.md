![Veilig Lanceren](/veilig-lanceren-logo.png)

This package is maintained by [VeiligLanceren.nl](https://veiliglanceren.nl), your partner in website development and everything else to power up your online company.

# Laravel Github File

[![Latest Version on Packagist](https://img.shields.io/packagist/v/veiliglanceren/laravel-github-file.svg?style=flat-square)](https://packagist.org/packages/veiliglanceren/laravel-remote-documentation)
[![Total Downloads](https://img.shields.io/packagist/dt/veiliglanceren/laravel-github-file.svg?style=flat-square)](https://packagist.org/packages/veiliglanceren/laravel-remote-documentation)
[![License](https://img.shields.io/packagist/l/veiliglanceren/laravel-github-file.svg?style=flat-square)](LICENSE)

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

The ZIP file will be stored in the default disk's `zips` directory. Or zip multiple files at the same times.

```php
$zipPath = GithubFile::zip('owner/repo', ['path/to/file.txt', 'path/to/file2.txt']);
```

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
