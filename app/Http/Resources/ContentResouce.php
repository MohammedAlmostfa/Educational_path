<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentResouce extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id'         => $this->id,
            'image_url'  => $this->image_url,
            'title'      => $this->title,
            'body'       => $this->body,
            'viewers'=>$this->viewers,
            'is_new'     => (bool) $this->is_new, // يتحول لبوليني بدل سترينغ
            'created_at' => $this->created_at?->toDateTimeString(),

        ];
    }
}
