<?php

namespace App\Providers;

use App\Repositories\ILeilaoRepository;
use App\Repositories\IUserRepository;
use App\Repositories\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\EloquentLanceRepository;
use App\Repositories\ILanceRepository;
use App\Repositories\EloquentLeilaoRepository;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            ILanceRepository::class,
            EloquentLanceRepository::class,
        );

        $this->app->bind(
            ILeilaoRepository::class,
            EloquentLeilaoRepository::class,
        );

        $this->app->bind(
            IUserRepository::class,
            EloquentUserRepository::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
