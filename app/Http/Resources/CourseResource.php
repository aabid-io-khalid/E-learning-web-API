<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'duration' => $this->duration,
            'level' => $this->level,
            'status' => $this->status,
            'category_name' => $this->category ? $this->category->name : null,
            'sub_category_name' => $this->subCategory ? $this->subCategory->name : null,
        ];
    }
}
