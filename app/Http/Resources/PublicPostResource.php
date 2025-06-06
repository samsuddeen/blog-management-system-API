<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
   public function toArray($request)
    {
        return [
            'title' => $this->title,
            'author_name' => $this->author->name ?? null,
            'category_name' => $this->category->name ?? null,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
