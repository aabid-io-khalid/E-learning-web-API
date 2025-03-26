<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Services\VideoService;
use OpenAPI\Annotations as OS;
use App\Services\CourseService;
use App\Http\Controllers\Controller;

class CourseController extends Controller
{
    protected $courseService;
    protected $videoService;

    public function __construct(CourseService $courseService, VideoService $videoService)
    {
        $this->courseService = $courseService;
        $this->videoService = $videoService;
    }
    /**
     * @OA\Get(
     *     path="/api/v1/courses",
     *     summary="Get a paginated list of courses with optional filtering",
     *     description="Returns a list of courses that can be filtered by search term, category, or difficulty level",
     *     tags={"Courses"},
     *     operationId="getCoursesList",
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term to filter courses by title or description",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="ID of the category to filter courses",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="difficulty",
     *         in="query",
     *         description="Difficulty level to filter courses (beginner, intermediate, advanced)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"beginner", "intermediate", "advanced"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="title", type="string")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request parameters",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid category ID")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Something went wrong")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $courses = $this->courseService->listCourses([
                'search' => $request->input('search'),
                'category_id' => $request->input('category'),
                'difficulty' => $request->input('difficulty')
            ]);

            return response()->json($courses);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
        try {
            return response()->json($this->courseService->getCourse($id));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur s\'est produite lors de la récupération du cours.'], 500);
        }
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
        try {
            $user = auth()->user();
    
            if (!$user) {
                return response()->json(['error' => 'Utilisateur non authentifié'], 401);
            }
    
            $data = $request->validate([
                'name' => 'required|string',
                'description' => 'required|string',
                'duration' => 'required|integer',
                'level' => 'required|string',
                'status' => 'required|string',
                'price' => 'required|integer',
                'category_id' => 'required|exists:categories,id',
                'sub_category_id' => 'nullable|exists:categories,id',
                'tags' => 'nullable|array',
                'tags.*' => 'exists:tags,id',
            ]);
    
            $data['user_id'] = $user->id;
    
            return response()->json($this->courseService->createCourse($data), 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur s\'est produite lors de la création du cours.'], 500);
        }
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
         try {
             $data = $request->validate([
                 'name' => 'required|string',
                 'description' => 'sometimes|string',
                 'duration' => 'required|integer',
                 'level' => 'required|string',
                 'status' => 'sometimes|string',
                 'price' => 'required|integer',
                 'category_id' => 'required|exists:categories,id',
                 'sub_category_id' => 'nullable|exists:categories,id',
                 'tags' => 'nullable|array',
                 'tags.*' => 'exists:tags,id',
             ]);
 
             return response()->json($this->courseService->updateCourse($id, $data));
         } catch (ValidationException $e) {
             return response()->json(['error' => $e->errors()], 400);
         } catch (\Exception $e) {
             return response()->json(['error' => 'Une erreur s\'est produite lors de la mise à jour du cours.'], 500);
         }
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
         try {
             $this->courseService->deleteCourse($id);
             return response()->json(['message' => 'Course deleted successfully']);
         } catch (\Exception $e) {
             return response()->json(['error' => 'Une erreur s\'est produite lors de la suppression du cours.'], 500);
         }
     }


         /**
     * @OA\Post(
     *     path="/api/v1/courses/{id}/videos",
     *     summary="Add a video to a course",
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
     *             required={"title", "url"},
     *             @OA\Property(property="title", type="string", example="Introduction to PHP"),
     *             @OA\Property(property="url", type="string", example="https://example.com/video.mp4"),
     *             @OA\Property(property="description", type="string", example="Learn the basics of PHP")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Video added successfully"),
     *     @OA\Response(response=400, description="Invalid input"),
     *     @OA\Response(response=404, description="Course not found")
     * )
     */
    public function addVideoToCourse(Request $request, $courseId)
    {
        try {
            $data = $request->validate([
                'title' => 'required|string',
                'video_file' => 'required|url', 
                'description' => 'nullable|string',
            ]);

            $video = $this->videoService->addVideoToCourse($courseId, $data);

            return response()->json($video, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/courses/{id}/videos",
     *     summary="List videos of a course",
     *     tags={"Course"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the course",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Course not found")
     * )
     */
    public function listVideosOfCourse($courseId)
    {
        try {
            $videos = $this->videoService->getVideosByCourseId($courseId);
            return response()->json($videos);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/videos/{id}",
     *     summary="Get details of a video",
     *     tags={"Video"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the video",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Video found"),
     *     @OA\Response(response=404, description="Video not found")
     * )
     */
    public function getVideo($videoId)
    {
        try {
            $video = $this->videoService->getVideoById($videoId);
            return response()->json($video);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/videos/{id}",
     *     summary="Update a video",
     *     tags={"Video"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the video",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "url"},
     *             @OA\Property(property="title", type="string", example="Advanced PHP"),
     *             @OA\Property(property="url", type="string", example="https://example.com/video.mp4"),
     *             @OA\Property(property="description", type="string", example="Learn advanced PHP concepts")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Video updated successfully"),
     *     @OA\Response(response=400, description="Invalid input"),
     *     @OA\Response(response=404, description="Video not found")
     * )
     */
    public function updateVideo(Request $request, $videoId)
    {
        try {
            $data = $request->validate([
                'title' => 'required|string',
                'url' => 'required|string',
                'description' => 'nullable|string',
            ]);

            $video = $this->videoService->updateVideo($videoId, $data);

            return response()->json($video);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/videos/{id}",
     *     summary="Delete a video",
     *     tags={"Video"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the video",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Video deleted successfully"),
     *     @OA\Response(response=404, description="Video not found")
     * )
     */
    public function deleteVideo($videoId)
    {
        try {
            $this->videoService->deleteVideo($videoId);
            return response()->json(['message' => 'Video deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
