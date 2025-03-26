<?php

namespace App\Repositories;

use App\Models\Course;
use App\Interfaces\CourseInterface;
use App\Http\Resources\CourseResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class CourseRepository implements CourseInterface
{
    public function getAll()
    {
        try {
            return Course::with(['category', 'subCategory'])->get();
        } catch (QueryException $e) {
            throw new \Exception("Une erreur s'est produite lors de la récupération des cours.");
        }
    }

    public function searchAndFilter(?string $search = null, ?int $categoryId = null, ?string $difficulty = null)
    {
        $query = Course::with(['category', 'subCategory']);
    
        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        }
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        if ($difficulty) {
            $query->where('level', $difficulty);
        }
    
        return $query->get();
    }
    
    public function findById($id)
    {
        try {
            return Course::with(['category', 'subCategory'])->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new \Exception("Cours non trouvé avec l'ID : $id");
        } catch (QueryException $e) {
            throw new \Exception("Une erreur s'est produite lors de la récupération du cours.");
        }
    }

    public function create(array $data)
    {
        try {
            $course = Course::create($data);
    
            if (isset($data['tags'])) {
                $course->tags()->attach($data['tags']);
            }
    
            return $course;
        } catch (QueryException $e) {
            throw new \Exception("Une erreur s'est produite ");
        }
    }

    public function update($id, array $data)
    {
        try {
            $course = Course::findOrFail($id);
            $course->update($data);

            if (isset($data['tags'])) {
                $course->tags()->sync($data['tags']);
            }

            return $course;
        } catch (ModelNotFoundException $e) {
            throw new \Exception("Cours non trouvé avec l'ID : $id");
        } catch (QueryException $e) {
            throw new \Exception("Une erreur s'est produite lors de la mise à jour du cours.");
        }
    }

    public function delete($id)
    {
        try {
            $course = Course::findOrFail($id);
            $course->tags()->detach();
            $course->delete();

            return true;
        } catch (ModelNotFoundException $e) {
            throw new \Exception("Cours non trouvé avec l'ID : $id");
        } catch (QueryException $e) {
            throw new \Exception("Une erreur s'est produite lors de la suppression du cours.");
        }
    }
    

}