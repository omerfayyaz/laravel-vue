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
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => substr($this->content, 0, 50) . '...',
            'category' => $this->category->name,
            'category_id' => $this->category->id,
            'created_at' => $this->created_at->toDateString()
        ];
    }
}
