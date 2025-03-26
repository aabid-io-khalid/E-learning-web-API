<?php

namespace App\Interfaces;

interface VideoInterface
{
    public function getVideosByCourseId(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function findById(int $id);
    public function delete(int $id);
}
