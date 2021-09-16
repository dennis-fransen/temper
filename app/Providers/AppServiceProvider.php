<?php

namespace App\Providers;

use App\Temper\Statistics\Adapters\ImportFromCsv;
use App\Temper\Statistics\Adapters\ImportFromJson;
use App\Temper\Statistics\Services\OnboardingDataCollector;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ImportFromJson::class, function () {
            return new ImportFromJson();
        });
        $this->app->bind(ImportFromCsv::class, function () {
            return new ImportFromCsv();
        });

        $this->app->bind(OnboardingDataCollector::class, function ($app) {
            return new OnboardingDataCollector($app->make(ImportFromCsv::class), $app->make(ImportFromJson::class));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
