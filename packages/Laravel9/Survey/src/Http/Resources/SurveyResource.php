<?php

namespace Laravel9\Survey\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class SurveyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'image_url' => $this->image ? URL::to($this->image) : null,
            'title' => $this->title,
            'slug' => $this->slug,
            'status' => !!$this->status,
            'description' => $this->description,
            'created_at' => (new \DateTime($this->created_at))->format('Y-m-d H:i:s'),
            'updated_at' => (new \DateTime($this->updated_at))->format('Y-m-d H:i:s'),
            'expire_date' => (new \DateTime($this->expire_date))->format('Y-m-d'),
            'questions' => SurveyQuestionResource::collection($this->questions)
        ];
    }
}
