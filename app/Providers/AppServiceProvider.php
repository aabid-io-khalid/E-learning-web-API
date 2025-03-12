<?php

namespace App\Providers;

use App\Services\TagService;
use App\Services\CourseService;
use App\Interfaces\TagInterface;
use App\Services\CategoryService;
use App\Interfaces\CourseInterface;
use App\Repositories\TagRepository;
use App\Interfaces\CategoryInterface;
use App\Repositories\CourseRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\CategoryRepository;

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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
