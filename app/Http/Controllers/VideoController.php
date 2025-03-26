<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Video;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Resources\VideoResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VideoController extends Controller
{
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

    public function listVideosOfCourse($courseId)
    {
        try {
            $course = Course::findOrFail($courseId);

            $videos = $course->videos;

            return response()->json([
                'course_id' => $course->id,
                'videos' => VideoResource::collection($videos),
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Cours non trouvé.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur s\'est produite.'], 500);
        }
    }

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
