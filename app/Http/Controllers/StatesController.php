<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;

class StatesController extends Controller
{
    public function courseStatistics()
    {
        $totalCourses = Course::count();
        $BeginnerCourses = Course::where('level', 'Beginner')->count();
        $AdvancedCourses = Course::where('level', 'Advanced')->count();
        $IntermediateCourses = Course::where('level', 'Intermediate')->count();

        return response()->json([
            'total_courses' => $totalCourses,
            'Beginner_courses' => $BeginnerCourses,
            'Advanced_courses' => $AdvancedCourses,
            'Intermediate_courses' => $IntermediateCourses,
        ]);
    }

    public function categorieStatistics()
    {
        $totalCategories = Category::count();
        $categoriesWithCourses = Category::has('courses')->count();

        return response()->json([
            'total_categories' => $totalCategories,
            'categories_with_courses' => $categoriesWithCourses,
        ]);
    }

    public function tagStatistics()
    {
        $totalTags = Tag::count();
        $tagsWithCourses = Tag::has('courses')->count();

        return response()->json([
            'total_tags' => $totalTags,
            'tags_with_courses' => $tagsWithCourses,
        ]);
    }
}
