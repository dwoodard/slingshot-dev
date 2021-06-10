<?php

namespace Dwoodard\Slingshot;

use Illuminate\Support\ServiceProvider;

class SlingshotServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'dwoodard');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'dwoodard');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

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
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/slingshot.php', 'slingshot');

        // Register the service the package provides.
        $this->app->singleton('slingshot', function ($app) {
            return new Slingshot;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['slingshot'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/slingshot.php' => config_path('slingshot.php'),
        ], 'slingshot.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/dwoodard'),
        ], 'slingshot.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/dwoodard'),
        ], 'slingshot.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/dwoodard'),
        ], 'slingshot.views');*/

        // Registering package commands.
        $this->commands([

            \Dwoodard\Slingshot\Console\Commands\slingshot::class,
            \Dwoodard\Slingshot\Console\Commands\slingshotProduction::class,


        ]);
    }
}
