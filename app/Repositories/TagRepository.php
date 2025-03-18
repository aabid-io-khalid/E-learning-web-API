<?php

namespace App\Repositories;

use Exception;
use App\Models\Tag;
use App\Interfaces\TagInterface;
use App\Http\Resources\TagResource;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TagRepository implements TagInterface{

    public function getAll()
    {
        try {
            return Tag::all();
        } catch (QueryException $e) {
            throw new \Exception("Une erreur s'est produite lors de la récupération des tags.");
        }
    }

    public function findById(int $id)
    {
        try {
            $tag = Tag::find($id);
            if (!$tag) {
                throw new ModelNotFoundException("Tag non trouvé avec l'ID : $id");
            }
            return $tag;
        } catch (ModelNotFoundException $e) {
            throw new \Exception("Tag non trouvé avec l'ID : $id", 404);
        } 
    }

    public function create(array $data)
    {
        try {
            if (isset($data['name']) && !empty($data['name'])) {
                $tags = explode(',', $data['name']);
                $tags = array_map('trim', $tags);

                foreach ($tags as $tagName) {
                    $createdTags[] = Tag::create(['name' => $tagName]);
                }
            }
        } catch (QueryException $e) {
            throw new \Exception("Une erreur s'est produite lors de la création des tags.");
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $tag = Tag::find($id);
            if (!$tag) {
                throw new ModelNotFoundException("Tag non trouvé avec l'ID : $id");
            }
            return $tag->update($data);
        } catch (ModelNotFoundException $e) {
            throw new \Exception("Tag non trouvé avec l'ID : $id", 404);
        } catch (QueryException $e) {
            throw new \Exception("Une erreur s'est produite lors de la mise à jour du tag.");
        }
    }

    public function delete(int $id)
    {
        try {
            $tag = Tag::find($id);
            if (!$tag) {
                throw new ModelNotFoundException("Tag non trouvé avec l'ID : $id");
            }
            return $tag->delete();
        } catch (ModelNotFoundException $e) {
            throw new \Exception("Tag non trouvé avec l'ID : $id", 404);
        } catch (QueryException $e) {
            throw new \Exception("Une erreur s'est produite lors de la suppression du tag.");
        }
    }
}