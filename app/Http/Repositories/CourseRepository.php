<?php

namespace App\Repositories;

use App\Models\Course;
use App\Interfaces\CourseInterface;
use App\Http\Resources\CourseResource;

class CourseRepository implements CourseInterface
{
    public function getAll()
    {
        return Course::with(['category', 'subCategory'])->get();
    }
    
    public function findById($id)
    {
        return Course::with(['category', 'subCategory'])->findOrFail($id);
    }

    public function create(array $data)
    {
        $course = Course::create($data);

        if (isset($data['tags'])) {
            $course->tags()->attach($data['tags']);
        }

        return $course;
    }

    public function update($id, array $data)
    {
        $course = Course::findOrFail($id);
        $course->update($data);

        if (isset($data['tags'])) {
            $course->tags()->sync($data['tags']);
        }

        return $course;
    }

    public function delete($id)
    {
        $course = Course::findOrFail($id);
        $course->tags()->detach();
        $course->delete();

        return true;
    }
    

}