<?php

namespace Sheidin\Fillable;

use Sheidin\Fillable\Commands\FillableCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FillableServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('fillable')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_fillable_table')
            ->hasCommand(FillableCommand::class);
    }
}
