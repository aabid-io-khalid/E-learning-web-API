<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Statistics",
 *     description="Statistiques sur les cours, catégories et tags"
 * )
 */

class StatesController extends Controller
{

    /**
     * Obtenir les statistiques des cours
     *
     * @OA\Get(
     *     path="/api/statistics/courses",
     *     summary="Statistiques des cours",
     *     tags={"Statistics"},
     *     @OA\Response(
     *         response=200,
     *         description="Statistiques des cours",
     *         @OA\JsonContent(
     *             @OA\Property(property="total_courses", type="integer", example=100),
     *             @OA\Property(property="Beginner_courses", type="integer", example=30),
     *             @OA\Property(property="Advanced_courses", type="integer", example=40),
     *             @OA\Property(property="Intermediate_courses", type="integer", example=30)
     *         )
     *     )
     * )
     */
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


     /**
     * Obtenir les statistiques des catégories
     *
     * @OA\Get(
     *     path="/api/statistics/categories",
     *     summary="Statistiques des catégories",
     *     tags={"Statistics"},
     *     @OA\Response(
     *         response=200,
     *         description="Statistiques des catégories",
     *         @OA\JsonContent(
     *             @OA\Property(property="total_categories", type="integer", example=10),
     *             @OA\Property(property="categories_with_courses", type="integer", example=8)
     *         )
     *     )
     * )
     */
    public function categorieStatistics()
    {
        $totalCategories = Category::count();
        $categoriesWithCourses = Category::has('courses')->count();

        return response()->json([
            'total_categories' => $totalCategories,
            'categories_with_courses' => $categoriesWithCourses,
        ]);
    }


    /**
     * Obtenir les statistiques des tags
     *
     * @OA\Get(
     *     path="/api/statistics/tags",
     *     summary="Statistiques des tags",
     *     tags={"Statistics"},
     *     @OA\Response(
     *         response=200,
     *         description="Statistiques des tags",
     *         @OA\JsonContent(
     *             @OA\Property(property="total_tags", type="integer", example=50),
     *             @OA\Property(property="tags_with_courses", type="integer", example=40)
     *         )
     *     )
     * )
     */
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
