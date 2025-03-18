<?php

namespace App\Repositories;

use App\Models\Category;
use App\Interfaces\CategoryInterface;
use App\Http\Resources\CategoryResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class CategoryRepository implements CategoryInterface
{

    public function getAll()
    {
        // return Category::all();
        try {
            $categories = Category::all();
            return CategoryResource::collection($categories);
        } catch (QueryException $e) {
            throw new \Exception("Une erreur s'est produite lors de la récupération des categories.");
        }
    }

    public function findById(int $id)
    {
        $category = Category::find($id);

        if (!$category) {
            throw new ModelNotFoundException("Catégorie non trouvée avec l'ID : $id");
        }

        return $category;
    }

    public function create(array $data)
    {
        try {
            return Category::create($data);
        } catch (QueryException $e) {
            throw new \Exception("Une erreur s'est produite lors de la création du categorie.");
        }
    }

    public function update(int $id, array $data)
    {
        try {
            if (isset($data['name']) && empty($data['name'])) {
                throw new InvalidArgumentException("Le champ 'name' ne peut pas être vide.");
            }

            $category = Category::find($id);

            if (!$category) {
                throw new ModelNotFoundException("Catégorie non trouvée avec l'ID : $id");
            }

            return $category->update($data);
        } catch (InvalidArgumentException $e) {
            throw $e; 
        } catch (ModelNotFoundException $e) {
            throw $e; 
        } catch (QueryException $e) {
            throw new \Exception("Une erreur s'est produite lors de la mise à jour de la catégorie.");
        }
    }

    public function delete(int $id)
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                throw new ModelNotFoundException("Catégorie non trouvée avec l'ID : $id");
            }
            return $category->delete();
        } catch (ModelNotFoundException $e) {
            throw $e; 
        } catch (QueryException $e) {
            throw new \Exception("Une erreur s'est produite lors de la suppression de la catégorie.");
        }
    }

}
