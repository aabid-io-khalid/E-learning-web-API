<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Video;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Resources\VideoResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;


/**
 * @OA\Tag(
 *     name="Videos",
 *     description="Gestion des vidéos de cours"
 * )
 */
class VideoController extends Controller
{

        /**
     * @OA\Post(
     *     path="/api/courses/{courseId}/videos",
     *     summary="Ajouter une vidéo à un cours",
     *     tags={"Videos"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="courseId",
     *         in="path",
     *         required=true,
     *         description="ID du cours",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "video_file"},
     *             @OA\Property(property="title", type="string", maxLength=255, example="Introduction au cours"),
     *             @OA\Property(property="description", type="string", nullable=true, example="Vidéo d'introduction"),
     *             @OA\Property(property="video_file", type="string", format="url", example="https://example.com/video.mp4")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Vidéo ajoutée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Vidéo ajoutée avec succès."),
     *             @OA\Property(
     *                 property="video",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Introduction au cours"),
     *                 @OA\Property(property="description", type="string", example="Vidéo d'introduction"),
     *                 @OA\Property(property="file_path", type="string", example="https://example.com/video.mp4"),
     *                 @OA\Property(property="course_id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Le champ title est obligatoire")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cours non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Cours non trouvé.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Erreur serveur")
     *         )
     *     )
     * )
     */
    public function addVideoToCourse(Request $request, $courseId)
    {
        try {
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'video_file' => 'required|url', 
            ]);

            $course = Course::findOrFail($courseId);

            // $videoPath = $request->file('video_file')->store('videos', 'public');

            $video = Video::create([
                'course_id' => $course->id,
                'title' => $data['title'],
                'description' => $data['description'],
                'file_path' => $data['video_file'],
            ]);

            return response()->json([
                'message' => 'Vidéo ajoutée avec succès.',
                'video' => new VideoResource($video),
            ], 201);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Cours non trouvé.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


        /**
     * @OA\Get(
     *     path="/api/courses/{courseId}/videos",
     *     summary="Lister les vidéos d'un cours",
     *     tags={"Videos"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="courseId",
     *         in="path",
     *         required=true,
     *         description="ID du cours",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des vidéos",
     *         @OA\JsonContent(
     *             @OA\Property(property="course_id", type="integer", example=1),
     *             @OA\Property(
     *                 property="videos",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Introduction au cours"),
     *                     @OA\Property(property="description", type="string", example="Vidéo d'introduction"),
     *                     @OA\Property(property="file_path", type="string", example="https://example.com/video.mp4"),
     *                     @OA\Property(property="course_id", type="integer", example=1)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Authentification requise.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Vous devez être inscrit à ce cours.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cours non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Cours non trouvé.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Erreur serveur")
     *         )
     *     )
     * )
     */
    public function listVideosOfCourse($courseId)
    {
        try {

            if (!auth()->check()) {
                return response()->json(['error' => 'Authentification requise.'], 401);
            }

            $user = auth()->user();
            $course = Course::findOrFail($courseId);

            if ($user->role === 'student') {
                $isEnrolled = $user->courses()->where('course_id', $courseId)->exists();
                
                if (!$isEnrolled) {
                    return response()->json(['error' => 'Vous devez être inscrit à ce cours pour accéder à son contenu.'], 403);
                }
            }
    
            $videos = $course->videos;
    
            return response()->json([
                'course_id' => $course->id,
                'videos' => VideoResource::collection($videos),
            ]);
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Cours non trouvé.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * @OA\Delete(
     *     path="/api/videos/{id}",
     *     summary="Supprimer une vidéo",
     *     tags={"Videos"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la vidéo",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vidéo supprimée",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Vidéo supprimée avec succès.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission refusée",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Action non autorisée.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Vidéo non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Vidéo non trouvée.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Erreur serveur")
     *         )
     *     )
     * )
     */
    public function deleteVideo($id)
    {
        try {
            $video = Video::findOrFail($id);

            Storage::disk('public')->delete($video->file_path);

            $video->delete();

            return response()->json([
                'message' => 'Vidéo supprimée avec succès.',
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Vidéo non trouvée.'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Une erreur s\'est produite.'], 500);
        }
    }
}
