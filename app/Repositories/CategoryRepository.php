<?php

namespace App\Repositories;

use App\Models\Category;
use App\Interfaces\CategoryInterface;
use App\Http\Resources\CategoryResource;

class CategoryRepository implements CategoryInterface
{

    public function getAll()
    {
        return Category::all();
        // $categories = Category::all();
        // return CategoryResource::collection($categories);
    }

    public function findById(int $id)
    {
        return Category::find($id);
    }

    public function create(array $data)
    {
        return Category::create($data);
    }

    public function update(int $id, array $data)
    {
        $category = Category::find($id);
        return $category ? $category->update($data) : false;
    }

    public function delete(int $id)
    {
        $category = Category::find($id);
        return $category ? $category->delete() : false;
    }

}
