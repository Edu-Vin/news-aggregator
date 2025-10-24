<?php

namespace App\Providers;

use App\Contracts\Article\ArticleInterface;
use App\Contracts\Category\CategoryInterface;
use App\Contracts\Source\SourceInterface;
use App\Contracts\User\UserPreferenceInterface;
use App\Repositories\Article\ArticleRepository;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\Source\SourceRepository;
use App\Repositories\User\UserPreferenceRepository;
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
        $this->app->bind(ArticleInterface::class, ArticleRepository::class);
        $this->app->bind(CategoryInterface::class, CategoryRepository::class);
        $this->app->bind(SourceInterface::class, SourceRepository::class);
        $this->app->bind(UserPreferenceInterface::class, UserPreferenceRepository::class);
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
