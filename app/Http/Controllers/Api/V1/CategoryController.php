<?php
namespace App\Http\Controllers\Api\V1;

use Exception;
use Illuminate\Http\Request;
use OpenAPI\Annotations as OS;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }
    /**
     * @OA\Get(
     *     path="/api/v1/categories",
     *     summary="Get a list of categories",
     *     tags={"Category"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function index()
    {
        try {
            return response()->json($this->categoryService->listCategories());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur s\'est produite lors de la récupération des catégories.'], 500);
        }
    }
        /**
     * @OA\Get(
     *     path="/api/v1/categories/{id}",
     *     summary="Get category details",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category ID",
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
                return response()->json(['error' => 'L\'ID de la catégorie doit être un nombre.'], 400);
            }

            return response()->json($this->categoryService->getCategory($id));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur s\'est produite lors de la récupération de la catégorie.'], 500);
        }
    }


        /**
     * @OA\Post(
     *     path="/api/v1/categories",
     *     summary="Store a new category",
     *     tags={"Category"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Technology")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Category created"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'parent_id' => 'nullable|exists:categories,id',
            ]);
    
            $category = $this->categoryService->createCategory($validated); 
            return response()->json(['data' => $category], 201);
            
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 400);
        } catch (Exception $e) {
            return response()->json(['error' => 'Une erreur s\'est produite lors de la création de la catégorie.'], 500);
        }
    }
        /**
     * @OA\Put(
     *     path="/api/v1/categories/{id}",
     *     summary="Update a category",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Science")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Category updated"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */

    public function update(Request $request, $id)
    {
        try {
            if (!is_numeric($id)) {
                return response()->json(['error' => 'L\'ID de la catégorie doit être un nombre.'], 400);
            }

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255|unique:categories,name,' . $id,
                'parent_id' => 'nullable|exists:categories,id',
            ]);

            return response()->json($this->categoryService->updateCategory($id, $validated));
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur s\'est produite lors de la mise à jour de la catégorie.'], 500);
        }
    }


        /**
     * @OA\Delete(
     *     path="/api/v1/categories/{id}",
     *     summary="Delete a category",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Category deleted"),
     *     @OA\Response(response=404, description="Category not found")
     * )
     */
    public function destroy($id)
    {
        try {
            if (!is_numeric($id)) {
                return response()->json(['error' => 'L\'ID de la catégorie doit être un nombre.'], 400);
            }

            $this->categoryService->deleteCategory($id);
            return response()->json(['message' => 'Catégorie supprimée avec succès.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur s\'est produite lors de la suppression de la catégorie.'], 500);
        }
    }
}
