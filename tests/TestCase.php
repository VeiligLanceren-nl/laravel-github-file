<?php

namespace Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use VeiligLanceren\GithubFile\GithubFileServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * @param $app
     * @return class-string[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            GithubFileSErviceProvider::class,
        ];
    }
}