<?php

namespace Ntech\RequiredDocumentPackage\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RequiredDocumentPackageServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerResources();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerResources();
    }

    /**
     * Register all the resources of the package.
     */
    protected function registerResources()
    {
        $this->registerRoutes();
        $this->registerConfig();
        $this->registerMigrations();
        $this->publishesSeeders();
        $this->registerViews();
        $this->registerLanguageFiles();
    }

    /**
     * Register all the routes of the package.
     */
    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function (){
            $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
        });
    }

    /**
     * Register the Views
     */
    protected function registerViews()
    {
        //
    }

    protected function registerConfig()
    {
        //
    }

    /**
     * Register all the migrations of the package.
     */
    protected function registerMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }
    /**
     * Publish the seeders
     */
    protected function publishesSeeders()
    {
        $this->publishes([
            __DIR__ . '/../../database/seeders/' => database_path('seeders')
        ], 'ntech-seeders');
    }

    /**
     * Routes configurations
     */
    protected function routeConfiguration()
    {
        return [
            'namespace' => 'Ntech\RequiredDocumentPackage\Http\Controllers',
            'middleware' => ['api', 'auth:api'],
            'prefix' => 'api'
        ];
    }

    public function registerLanguageFiles()
    {
        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'RequiredDocumentPackage');
    }
}
