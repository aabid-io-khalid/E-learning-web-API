<?php

namespace App\Services;

use App\Interfaces\CategoryInterface;
use App\Http\Resources\CategoryResource;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function listCategories()
    {
        $categories = $this->categoryRepository->getAll();
        return CategoryResource::collection($categories);
    }

    public function getCategory(int $id)
    {
        return $this->categoryRepository->findById($id);
    }

    public function createCategory(array $data)
    {
        return $this->categoryRepository->create($data);
    }

    public function updateCategory(int $id, array $data)
    {
        return $this->categoryRepository->update($id, $data);
    }

    public function deleteCategory(int $id)
    {
        return $this->categoryRepository->delete($id);
    }
}
