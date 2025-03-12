<?php

namespace App\Repositories;

use App\Models\Tag;
use App\Interfaces\TagInterface;
use App\Http\Resources\TagResource;

class TagRepository implements TagInterface{

    public function getAll()
    {
        return Tag::all();
    }

    public function findById(int $id)
    {
        return Tag::find($id);
    }

    public function create(array $data)
    {
        if (isset($data['name']) && !empty($data['name'])) {
            $tags = explode(',', $data['name']);

            $tags = array_map('trim', $tags);

            foreach ($tags as $tagName) {
                Tag::create(['name' => $tagName]);
            }
        }
    }

    public function update(int $id, array $data)
    {
        $tag = Tag::find($id);
        return $tag ? $tag->update($data) : false;
    }

    public function delete(int $id)
    {
        $tag = Tag::find($id);
        return $tag ? $tag->delete() : false;
    }
}