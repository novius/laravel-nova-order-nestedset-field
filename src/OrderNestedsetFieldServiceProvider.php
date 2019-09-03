<?php

namespace Novius\LaravelNovaOrderNestedsetField;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class OrderNestedsetFieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            Nova::script('laravel-nova-order-nestedset-field', __DIR__.'/../dist/js/field.js');
            Nova::style('laravel-nova-order-nestedset-field', __DIR__.'/../dist/css/field.css');
        });

        $this->publishes([__DIR__.'/../config' => config_path()], 'config');

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'nova-order-nestedset-field');
        $this->publishes([__DIR__.'/../resources/lang' => resource_path('lang/vendor/nova-order-nestedset-field')], 'lang');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/nova-order-nestedset-field.php',
            'nova-order-nestedset-field'
        );
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova'])
            ->prefix('nova-vendor/nova-order-nestedset-field')
            ->group(__DIR__.'/../routes/api.php');
    }
}
