<?php

namespace App\Interfaces;

interface CourseInterface
{
    public function getAll();
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function searchAndFilter(?string $search, ?int $categoryId, ?string $difficulty);
}
