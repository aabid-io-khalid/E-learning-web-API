<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
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


    public function index(Course $course)
    {
        $enrollments = $course->enrollments()->with('user')->get();
        return response()->json(['enrollments' => $enrollments]);
    }

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
