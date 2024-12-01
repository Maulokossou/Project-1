<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
         return [
            'id' => $this->id,
            'question' => $this->question,
            'answer' => $this->answer,
            'category_id' => $this->faq_category_id,
            'order' => $this->order,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}