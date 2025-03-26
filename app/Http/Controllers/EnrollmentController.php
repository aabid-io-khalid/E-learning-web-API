<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenAPI\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Enrollment",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="course_id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-01T12:00:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-01T12:00:00.000000Z"),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="John Doe"),
 *         @OA\Property(property="email", type="string", example="john@example.com")
 *     )
 * )
 */

class EnrollmentController extends Controller
{

    /**
     * Inscrire un utilisateur à un cours
     *
     * @OA\Post(
     *     path="/api/v2/courses/{course}/enroll",
     *     summary="Inscrire un utilisateur à un cours",
     *     tags={"Enrollments"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course",
     *         in="path",
     *         required=true,
     *         description="ID du cours",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Inscription réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Inscription réussie."),
     *             @OA\Property(property="enrollment", type="object", ref="#/components/schemas/Enrollment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Déjà inscrit",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Vous êtes déjà inscrit à ce cours.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     )
     * )
     */
    public function enroll(Course $course)
    {
        $user = Auth::user();

        if ($course->enrollments()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Vous êtes déjà inscrit à ce cours.'], 400);
        }

        $enrollment = $course->enrollments()->create([
            'course_id' => $course->id,
            'user_id' => $user->id,
            'status' => 'pending', 
        ]);

        return response()->json(['message' => 'Inscription réussie.', 'enrollment' => $enrollment], 201);
    }


    /**
     * Lister les inscriptions d'un cours
     *
     * @OA\Get(
     *     path="/api/v2/courses/{course}/enrollments",
     *     summary="Lister les inscriptions d'un cours",
     *     tags={"Enrollments"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course",
     *         in="path",
     *         required=true,
     *         description="ID du cours",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des inscriptions",
     *         @OA\JsonContent(
     *             @OA\Property(property="enrollments", type="array", @OA\Items(ref="#/components/schemas/Enrollment"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     )
     * )
     */

    public function index(Course $course)
    {
        $enrollments = $course->enrollments()->with('user')->get();
        return response()->json(['enrollments' => $enrollments]);
    }



    /**
     * Mettre à jour le statut d'une inscription
     *
     * @OA\Put(
     *     path="/api/v2/enrollments/{enrollment}",
     *     summary="Mettre à jour le statut d'une inscription",
     *     tags={"Enrollments"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="enrollment",
     *         in="path",
     *         required=true,
     *         description="ID de l'inscription",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="accepted", enum={"pending", "accepted", "rejected"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Statut mis à jour",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Statut mis à jour."),
     *             @OA\Property(property="enrollment", type="object", ref="#/components/schemas/Enrollment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Action non autorisée",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Action non autorisée.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     )
     * )
     */
    public function update(Request $request, Enrollment $enrollment)
    {
        if (!Auth::user()->isMentor() && !Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,accepted,rejected',
        ]);

        $enrollment->update(['status' => $request->status]);

        return response()->json(['message' => 'Statut mis à jour.', 'enrollment' => $enrollment]);
    }
}
