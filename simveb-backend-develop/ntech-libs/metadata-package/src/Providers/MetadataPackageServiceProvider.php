<?php

namespace Ntech\MetadataPackage\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/**
 * Class MetadataPackageServiceProvider
 * @package Ntech\ServicePackage\Providers
 */
class MetadataPackageServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        //User::class => UserPolicy::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        config(['app.timezone' => 'Africa/Porto-Novo']);

        //$this->app->register(EventServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerResources();
        //$this->registerCommands();
    }



    /**
     * Register all the resources of the package.
     */
    protected function registerResources()
    {
        $this->publishesConfigs();
        $this->publishesSeeders();
        $this->publishesFactories();

        $this->registerFacades();
        $this->registerMigrations();
        $this->registerObservers();
        $this->registerViews();
        $this->registerTranslations();
        $this->registerMiddlewares();
        $this->registerRoutes();
        $this->registerPolicies();
        $this->registerFactories();

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
     * Publish the factories
     */
    protected function publishesFactories()
    {
        $this->publishes([
            __DIR__ . '/../../database/factories/' => database_path('factories')
        ], 'ntech-factories');
    }

    /**
     * Publish the configs
     */
    protected function publishesConfigs()
    {
        $this->publishes([
            __DIR__ . '/../../config/' => config_path(''),
        ], 'ntech-config');
    }


    /**
     * Register all the facades of the package.
     */
    protected function registerFacades()
    {


    }

    /**
     * Register the Observers for the Models
     */
    protected function registerObservers()
    {

    }


    /**
     * Register all the migrations of the package.
     */
    protected function registerMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    /**
     * Register the Views
     */
    protected function registerViews()
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'Ntech');
    }

    /**
     * Register the Views
     */
    protected function registerTranslations()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'Ntech');
    }

    /**
     * Register the middlewares
     */
    protected function registerMiddlewares()
    {
        //$router->aliasMiddleware('auth', \Ntech\ServicePackage\Http\Middleware\Authenticate::class);

    }

    /**
     * get the list of scopes of the application
     * @return array
     */
    protected function getScopes()
    {
        return [];
    }

    /**
     * Register the application's policies.
     *
     * @return void
     */
    public function registerPolicies()
    {
        foreach ($this->policies() as $key => $value) {
            Gate::policy($key, $value);
        }
    }

    /**
     * Register all the routes of the package.
     */
    protected function registerFactories()
    {
        $this->loadFactoriesFrom(__DIR__ . '/../../database/factories');
    }

    /**
     * Get the policies defined on the provider.
     *
     * @return array
     */
    public function policies()
    {
        return $this->policies;
    }

    /**
     * Register all the routes of the package.
     */
    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
        });
    }


    /**
     * Routes configurations
     * @return array
     */
    protected function routeConfiguration()
    {
        return [
            'middleware' => ['api'],
            'prefix' => 'api'
        ];
    }


}
