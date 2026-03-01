<?php

namespace TresorKasenda\Numberable;

use Illuminate\Support\ServiceProvider;

class NumberableServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Optionnel
        // $this->publishes([
        //     __DIR__.'/../config/numberable.php' => config_path('numberable.php'),
        // ], 'numberable-config');
    }
}