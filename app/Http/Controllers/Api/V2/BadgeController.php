<?php

namespace App\Http\Controllers\Api\V2;

use Exception;
use App\Models\User;
use App\Models\Badge;
use App\Services\BadgeService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    protected $badgeService;

    public function __construct(BadgeService $badgeService)
    {
        $this->badgeService = $badgeService;
    }

    /**
     * @OA\Get(
     *     path="/api/v2/badges",
     *     summary="Obtenir les badges de l'utilisateur",
     *     tags={"Badge"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Liste des badges"),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function getUserBadges()
    {
        try {
            $user = Auth::user();
            $badges = $this->badgeService->getUserBadges($user);
            return response()->json($badges);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    

    /**
     * @OA\Post(
     *     path="/api/v2/admin/badges",
     *     summary="Créer un nouveau badge (admin)",
     *     tags={"Badge"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description", "type", "condition_type", "condition_value"},
     *             @OA\Property(property="name", type="string", example="Expert"),
     *             @OA\Property(property="description", type="string", example="A complété 10 cours"),
     *             @OA\Property(property="image_url", type="string", example="https://example.com/badge.png"),
     *             @OA\Property(property="type", type="string", example="student"),
     *             @OA\Property(property="condition_type", type="string", example="courses_completed"),
     *             @OA\Property(property="condition_value", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Badge créé"),
     *     @OA\Response(response=403, description="Non autorisé")
     * )
     */
        // creer un badge par l'admin ( le role est verifié par middlware)
    public function createBadge(Request $request)
    {
        try {

            $data = $request->validate([
                'name' => 'required|string',
                'description' => 'required|string',
                'type' => 'required|in:student,mentor',
                'condition_type' => 'nullable|string',
                'condition_value' => 'nullable|integer',
            ]);

            $badge = Badge::create($data);

            return response()->json($badge, 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

        /**
     * @OA\Put(
     *     path="/api/v2/admin/badges/{id}",
     *     summary="Mettre à jour un badge (Admin seulement)",
     *     tags={"Badges"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du badge",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", nullable=true, example="Super Expert"),
     *             @OA\Property(property="description", type="string", nullable=true, example="A complété 20 cours"),
     *             @OA\Property(property="image_url", type="string", nullable=true, example="https://example.com/new-badge.png"),
     *             @OA\Property(property="type", type="string", nullable=true, enum={"student", "mentor"}, example="student"),
     *             @OA\Property(property="condition_type", type="string", nullable=true, example="courses_completed"),
     *             @OA\Property(property="condition_value", type="integer", nullable=true, example=20)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Badge mis à jour",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Super Expert"),
     *             @OA\Property(property="description", type="string", example="A complété 20 cours"),
     *             @OA\Property(property="type", type="string", example="student"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Le type doit être student ou mentor")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Badge non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Badge not found")
     *         )
     *     )
     * )
     */
    public function updateBadge(Request $request, $id)
    {
        try {

            $badge = Badge::findOrFail($id);

            $data = $request->validate([
                'name' => 'nullable|string',
                'description' => 'nullable|string',
                'type' => 'nullable|in:student,mentor',
                'condition_type' => 'nullable|string',
                'condition_value' => 'nullable|integer',
            ]);

            $badge->update($data);

            return response()->json($badge);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

        /**
     * @OA\Get(
     *     path="/api/v2/badges/user/{id}",
     *     summary="Obtenir les badges d'un utilisateur spécifique",
     *     tags={"Badges"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des badges de l'utilisateur",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Expert"),
     *                 @OA\Property(property="description", type="string", example="A complété 10 cours"),
     *                 @OA\Property(property="image_url", type="string", example="https://example.com/badge.png"),
     *                 @OA\Property(property="type", type="string", example="student"),
     *                 @OA\Property(property="created_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreur de traitement",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="User not found")
     *         )
     *     )
     * )
     */
    public function getaUserBadges($id)
    {
        try {
            
            $badges = $this->badgeService->checkStudentBadges($id);
            return response()->json($badges);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}