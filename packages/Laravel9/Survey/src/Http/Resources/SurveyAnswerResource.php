<?php

namespace Laravel9\Survey\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SurveyAnswerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'survey' => new SurveyResource($this->survey),
            'end_date' => $this->end_date
        ];
    }
}
