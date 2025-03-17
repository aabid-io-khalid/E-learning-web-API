<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;

class MentorController extends Controller
{
    public function mentorCourses($id)
    {
        try {
            $mentor = User::findOrFail($id);

            // if (!$mentor->isMentor()) {
            //     return response()->json(['error' => 'Cet utilisateur n\'est pas un mentor.'], 403);
            // }

            $courses = $mentor->coursesMentor;

            return response()->json([
                'mentor_id' => $mentor->id,
                'courses' => $courses,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Une erreur s\'est produite.'], 500);
        }
    }
}
