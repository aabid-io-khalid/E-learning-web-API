<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\TagService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenAPI\Annotations as OS;

class TagController extends Controller
{
    protected $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }
        /**
     * @OA\Get(
     *     path="/api/v1/tags",
     *     summary="Get a list of tags",
     *     tags={"Tag"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function index()
    {
        return response()->json($this->tagService->listTags());
    }
            /**
     * @OA\Get(
     *     path="/api/v1/tags/{id}",
     *     summary="Get tag details",
     *     tags={"Tag"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Tag ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Category not found")
     * )
     */
    public function show($id)
    {
        return response()->json($this->tagService->getTag($id));
    }

            /**
     * @OA\Post(
     *     path="/api/v1/tags",
     *     summary="Store a new tag",
     *     tags={"Tag"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="php")
     *         )
     *     ),
     *     @OA\Response(response=200, description="tag created"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        return response()->json($this->tagService->createTag($validated), 201);
    }


            /**
     * @OA\Put(
     *     path="/api/v1/tags/{id}",
     *     summary="Update a Tag",
     *     tags={"Tag"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Tag ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="laravel")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Tag updated"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
        ]);

        return response()->json($this->tagService->updateTag($id, $validated));
    }


    /**
     * @OA\Delete(
     *     path="/api/v1/tags/{id}",
     *     summary="Delete a Tag",
     *     tags={"Tag"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Tag ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Tag deleted"),
     *     @OA\Response(response=404, description="Tag not found")
     * )
     */
    public function destroy($id)
    {
        return response()->json($this->tagService->deleteTag($id));
    }

}
