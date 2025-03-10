<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CourseService;
use OpenAPI\Annotations as OS;

class CourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/courses",
     *     summary="Get a list of courses",
     *     tags={"Course"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function index()
    {
        return response()->json($this->courseService->listCourses());
    }


    /**
     * @OA\Get(
     *     path="/api/v1/courses/{id}",
     *     summary="Get a specific course",
     *     tags={"Course"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the course",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Course found"),
     *     @OA\Response(response=404, description="Course not found")
     * )
     */
    public function show($id)
    {
        return $this->courseService->getCourse($id);
    }


        /**
     * @OA\Post(
     *     path="/api/v1/courses",
     *     summary="Create a new course",
     *     tags={"Course"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description", "duration", "level", "status", "category_id"},
     *             @OA\Property(property="name", type="string", example="Introduction to PHP"),
     *             @OA\Property(property="description", type="string", example="Learn the basics of PHP"),
     *             @OA\Property(property="duration", type="integer", example=120),
     *             @OA\Property(property="level", type="string", example="Beginner"),
     *             @OA\Property(property="status", type="string", example="open"),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="sub_category_id", type="integer", example=2),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="integer"), example={1, 2})
     *         )
     *     ),
     *     @OA\Response(response=201, description="Course created successfully"),
     *     @OA\Response(response=400, description="Invalid input")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'duration' => 'required|integer',
            'level' => 'required|string',
            'status' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        return $this->courseService->createCourse($data);
    }


        /**
     * @OA\Put(
     *     path="/api/v1/courses/{id}",
     *     summary="Update an existing course",
     *     tags={"Course"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the course",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description", "duration", "level", "status", "category_id"},
     *             @OA\Property(property="name", type="string", example="Advanced PHP"),
     *             @OA\Property(property="description", type="string", example="Learn advanced PHP concepts"),
     *             @OA\Property(property="duration", type="integer", example=180),
     *             @OA\Property(property="level", type="string", example="Intermediate"),
     *             @OA\Property(property="status", type="string", example="closed"),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="sub_category_id", type="integer", example=2),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="integer"), example={1, 2})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Course updated successfully"),
     *     @OA\Response(response=400, description="Invalid input"),
     *     @OA\Response(response=404, description="Course not found")
     * )
     */

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'sometimes|string',
            'duration' => 'required|integer',
            'level' => 'required|string',
            'status' => 'sometimes|string',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        return $this->courseService->updateCourse($id, $data);
    }


        /**
     * @OA\Delete(
     *     path="/api/v1/courses/{id}",
     *     summary="Delete a course",
     *     tags={"Course"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the course",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Course deleted successfully"),
     *     @OA\Response(response=404, description="Course not found")
     * )
     */
    
    public function destroy($id)
    {
        $this->courseService->deleteCourse($id);
        return response()->json(['message' => 'Course deleted successfully']);
    }
}
