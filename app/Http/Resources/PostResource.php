<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */


    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'slug' => $this->slug,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'author' => $this->author->name ?? null,
            'created_at' => $this->created_at->toDateTimeString(),
            'status' => $this->status,

        ];
    }
}
