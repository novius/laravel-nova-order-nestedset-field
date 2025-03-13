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
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(static function (ServingNova $event) {
            Nova::script('laravel-nova-order-nestedset-field', __DIR__.'/../dist/js/field.js');
        });

        $this->publishes([__DIR__.'/../config' => config_path()], 'config');

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'nova-order-nestedset-field');
        $this->publishes([__DIR__.'/../lang' => lang_path('vendor/nova-order-nestedset-field')], 'lang');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/nova-order-nestedset-field.php',
            'nova-order-nestedset-field'
        );
    }

    /**
     * Register the tool's routes.
     */
    protected function routes(): void
    {
        /** @phpstan-ignore method.notFound */
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova'])
            ->prefix('nova-vendor/nova-order-nestedset-field')
            ->group(__DIR__.'/../routes/api.php');
    }
}
