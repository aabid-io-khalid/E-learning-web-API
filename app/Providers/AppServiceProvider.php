<?php

namespace App\Providers;

use App\Services\TagService;
use App\Services\AuthService;
use App\Services\CourseService;
use App\Interfaces\TagInterface;
use App\Services\CategoryService;
use App\Interfaces\CourseInterface;
use App\Repositories\TagRepository;
use App\Repositories\AuthRepository;
use App\Interfaces\CategoryInterface;
use App\Repositories\CourseRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\CategoryRepository;
use App\Interfaces\AuthRepositoryInterface;

/**
 
*   @OA\Info(
*   title="E-learning-API",
*   version="1.0.0"
*   )
*/

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CategoryInterface::class, CategoryRepository::class);
        $this->app->bind(CategoryService::class);

        $this->app->bind(TagInterface::class, TagRepository::class);
        $this->app->bind(TagService::class);

        $this->app->bind(CourseInterface::class, CourseRepository::class);
        $this->app->bind(CourseService::class);

        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(AuthService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
