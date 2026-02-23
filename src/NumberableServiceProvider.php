<?php

namespace TresorKasenda\Numberable;

use Illuminate\Support\ServiceProvider;

class NumberableServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Rien à binder, la classe est stateless + fluent
    }

    public function boot(): void
    {
        // Optionnel : publier une config future
        // $this->publishes([
        //     __DIR__.'/../config/numberable.php' => config_path('numberable.php'),
        // ], 'numberable-config');
    }
}