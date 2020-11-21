<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $images = $this->images;
        return [
            'id'    => $this->id,
            'title'    => $this->title,
            'content'    => $this->content,
            'status'    => $this->status,
            'images'    => $images->count() ? FileCollection::collection($images) : []
        ];
    }
}
