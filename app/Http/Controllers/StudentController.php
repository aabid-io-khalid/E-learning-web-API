<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function enrolledCourses($id)
    {
        
        $student = User::findOrFail($id);

        if(!$student->isStudent()){
            response()->json(["this is not a student!"]);
        }
        
        $courses = $student->courses;

        return response()->json([
            'student_id' => $student->id,
            'enrolled_courses' => $courses,
        ]);
    }
}



