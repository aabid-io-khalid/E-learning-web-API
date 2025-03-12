<?php

namespace App\Services;

use App\Interfaces\CourseInterface;
use App\Http\Resources\CourseResource;

class CourseService
{
    protected $courseRepository;

    public function __construct(CourseInterface $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    public function listCourses()
    {
        $courses = $this->courseRepository->getAll();
        return CourseResource::collection($courses);
    }

    public function getCourse($id)
    {
        $course = $this->courseRepository->findById($id);
        return new CourseResource($course);
    }

    public function createCourse(array $data)
    {
        $course = $this->courseRepository->create($data);
        return new CourseResource($course);
    }

    public function updateCourse($id, array $data)
    {
        $course = $this->courseRepository->update($id, $data);
        return new CourseResource($course);
    }

    public function deleteCourse($id)
    {
        return $this->courseRepository->delete($id);
    }

}