<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(\App\Contracts\Article\ArticleInterface::class, \App\Repositories\Article\ArticleRepository::class);
        $this->app->bind(\App\Contracts\Category\CategoryInterface::class, \App\Repositories\Category\CategoryRepository::class);
        $this->app->bind(\App\Contracts\Source\SourceInterface::class, \App\Repositories\Source\SourceRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });
    }
}
