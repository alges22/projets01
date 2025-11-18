<?php

namespace Ntech\NotifierPackage\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Ntech\NotifierPackage\Facades\Notifier as NotifierFacade;
use Ntech\NotifierPackage\Notifier;

class NotifierPackageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('Notifier', NotifierFacade::class);
    }

    public function register()
    {
        $this->app->singleton('notifier', function () {
            return new Notifier;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['notifier'];
    }
}
