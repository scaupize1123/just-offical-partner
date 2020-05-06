<?php

namespace Scaupize1123\JustOfficalPartner;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory as EloquentFactory;

class PartnerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->registerEloquentFactoriesFrom(__DIR__.'/../database/factories');
        $this->app->bind(
            'Scaupize1123\JustOfficalPartner\Interfaces\PartnerRepositoryInterface',
            'Scaupize1123\JustOfficalPartner\Repositories\PartnerRepository'
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    protected function registerEloquentFactoriesFrom($path)
    {
        $this->app->make(EloquentFactory::class)->load($path);
    }
}
