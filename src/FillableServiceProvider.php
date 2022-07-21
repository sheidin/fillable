<?php

namespace Sheidin\Fillable;

use Illuminate\Support\ServiceProvider;
use Sheidin\Fillable\Commands\FillableCommand;

class FillableServiceProvider extends ServiceProvider
{
    protected $commands = [
        FillableCommand::class,
    ];

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        // Register commands for Artisan interface.
        $this->commands($this->commands);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['Fillable'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/../config/fillable.php' => config_path('fillable.php'),
        ], 'fillable');

        // Registering package commands.
        $this->commands($this->commands);
    }
}
