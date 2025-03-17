<?php

namespace App\Http\Controllers\Api\V2;

use Illuminate\Http\Request;
use OpenAPI\Annotations as OA;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PermissionResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PermissionController extends Controller
{
   /**
     * @OA\Get(
     *     path="/api/v2/permissions",
     *     summary="Get a list of permission",
     *     tags={"Permission"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */

    public function index()
    {
        $permissions = Permission::all();
        return response()->json(PermissionResource::collection($permissions));
    }


    /**
     * @OA\Post(
     *     path="/api/v2/permissions",
     *     summary="Store a new permission",
     *     tags={"Permission"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="see-course")
     *         )
     *     ),
     *     @OA\Response(response=200, description="permission created"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:permissions,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $permission = Permission::create(['name' => $request->name]);

        return response()->json($permission, 201);
    }



    /**
     * @OA\Put(
     *     path="/api/v2/permissions/{id}",
     *     summary="Update a permission",
     *     tags={"Permission"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="permission ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="update-course")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Permission updated"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:permissions,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $permission = Permission::findOrFail($id);

        $permission->name = $request->name;
        $permission->save();

        return response()->json($permission);
    }


    /**
     * @OA\Delete(
     *     path="/api/v2/permissions/{id}",
     *     summary="Delete a Permission",
     *     tags={"Permission"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Permission ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Permission deleted"),
     *     @OA\Response(response=404, description="Permission not found")
     * )
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);

        $permission->delete();

        return response()->json(['message' => 'Permission supprimée avec succès']);
    }


    /**
     * @OA\Get(
     *     path="/api/v2/permissions/{id}",
     *     summary="Get permission details",
     *     tags={"Permission"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Permission ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Category not found")
     * )
     */
    public function show($id)
    {
        try {
            $permission = Permission::find($id);
            if (!$permission) {
                throw new ModelNotFoundException("permission non trouvé avec l'ID : $id");
            }
            return response()->json(["id" => $permission->id, "name" => $permission->name]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur s\'est produite lors de la récupération du cours.'], 500);
        }
    }
}
