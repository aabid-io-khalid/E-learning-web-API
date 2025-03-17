<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\TagService;
use Illuminate\Http\Request;
use OpenAPI\Annotations as OS;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

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
        try {
            return response()->json($this->tagService->listTags());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur s\'est produite lors de la récupération des tags.'], 500);
        }
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
        try {
                if (!is_numeric($id)) {
                throw new \Exception("L'ID du tag doit être un nombre.", 400);
            }
          return response()->json($this->tagService->getTag($id));  
        } catch(\Exception $e){
            return response()->json(['error' => 'Une erreur s\'est produite lors de la récupération de cet tag.'], 500);
        }
        
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
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:tags,name',
            ]);

            $tags = $this->tagService->createTag($validated); 
            return response()->json(['data' => $tags], 201); 

        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur s\'est produite lors de la création du tag.'], 500);
        }
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
        try {
            if (!is_numeric($id)) {
                throw new \Exception("L'ID du tag doit être un nombre.", 400);
            }

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255|unique:tags,name,' . $id,
            ]);

            return response()->json($this->tagService->updateTag($id, $validated));
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
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
        try {
            if (!is_numeric($id)) {
                throw new \Exception("L'ID du tag doit être un nombre.", 400);
            }

            $this->tagService->deleteTag($id);
            return response()->json(['message' => 'Tag supprimé avec succès.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

}
