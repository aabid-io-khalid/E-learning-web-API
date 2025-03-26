<?php

namespace App\Http\Controllers\Api\V3;

use Illuminate\Http\Request;
use App\Services\VideoService;
use App\Services\CourseService;
use App\Http\Controllers\Controller;

class CourseController extends Controller
{
    public function SearchCourse(Request $request, $name)
    {
        $courses = Course::where('name', 'like', '%'.$name.'%')->get();

        if ($courses->isEmpty()) {
        return response()->json(['message' => 'No courses found'], 404);
        }

        return response()->json($courses);

    }

    
}
