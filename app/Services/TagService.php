<?php

namespace App\Services;

use App\Interfaces\TagInterface;
use App\Http\Resources\TagResource;

class TagService
{
    protected $tagRepository;

    public function __construct(TagInterface $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function listTags()
    {
        $tags =  $this->tagRepository->getAll();
        return TagResource::collection($tags);
    }

    public function getTag(int $id){
        return $this->tagRepository->findById($id);
    }

    public function createTag(array $data)
    {
        return $this->tagRepository->create($data);
    }

    public function updateTag(int $id, array $data)
    {
        return $this->tagRepository->update($id, $data);
    }

    public function deleteTag(int $id)
    {
        return $this->tagRepository->delete($id);
    }
}