<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\StatesController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Api\V1\TagController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\Api\V2\RoleController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V2\Auth\AuthController;
use App\Http\Controllers\Api\V2\Auth\UserController;
use App\Http\Controllers\Api\V2\PermissionController;


Route::prefix('v2')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->name("logout");

    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('/', [RoleController::class, 'store']);
        Route::put('/{id}', [RoleController::class, 'update']);
        Route::delete('/{id}', [RoleController::class, 'destroy']);
    }); 

    // routes for permissions [Spatie Pack]
    Route::prefix('permissions')->group(function () {
        Route::get('/', [PermissionController::class, 'index']);
        Route::post('/', [PermissionController::class, 'store']); 
        Route::put('/{id}', [PermissionController::class, 'update']); 
        Route::get('/{id}', [PermissionController::class, 'show']); 
        Route::delete('/{id}', [PermissionController::class, 'destroy']);
    }); 


    // routes for checking the auth [working with sanctume this time]
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/edit', [UserController::class, 'update']);
        Route::post('/users/{user}', [UserController::class, 'updateUser'])->middleware('role:admin');
        Route::post('/logout', [AuthController::class, 'logout'])->name("logout");
    });
    
    Route::get('/students/{id}/courses', [StudentController::class, 'enrolledCourses']);
    
    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/mentors/{id}/courses', [MentorController::class, 'mentorCourses']);
        Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'enroll'])->middleware('role:student');
        Route::get('/courses/{course}/enrollments', [EnrollmentController::class, 'index']);
        Route::get('/stats/courses', [StatesController::class, 'courseStatistics'])->middleware('role:admin');
        Route::get('/stats/categories', [StatesController::class, 'categorieStatistics'])->middleware('role:admin');
        Route::get('/stats/tags', [StatesController::class, 'tagStatistics'])->middleware('role:admin');
    
    });
});

Route::prefix('v1')->group(function () {

    Route::prefix('tags')->group(function () {
        Route::get('/', [TagController::class, 'index']); 
        Route::get('/{id}', [TagController::class, 'show']); 
        Route::post('/', [TagController::class, 'store']); 
        Route::put('/{id}', [TagController::class, 'update']); 
        Route::delete('/{id}', [TagController::class, 'destroy']); 
    });

    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']); 
        Route::get('/{id}', [CategoryController::class, 'show']); 
        Route::post('/', [CategoryController::class, 'store']); 
        Route::put('/{id}', [CategoryController::class, 'update']); 
        Route::delete('/{id}', [CategoryController::class, 'destroy']); 
    });

    Route::prefix('courses')->group(function () {
        Route::get('/', [CourseController::class, 'index']); 
        Route::get('/{id}', [CourseController::class, 'show']); 

        Route::middleware('auth:sanctum')->group(function () 
        {
            Route::post('/', [CourseController::class, 'store'])->middleware('role:mentor'); 
        });

        Route::put('/{id}', [CourseController::class, 'update']); 
        Route::delete('/{id}', [CourseController::class, 'destroy']); 
    });


});




Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/test', function () {
    return response()->json(['message' => 'Death Can Have Me, When It Earns Me!']);
});