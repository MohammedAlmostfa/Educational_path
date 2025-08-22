<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CollegeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'universityName' => $this->university->name,
            'collegeName'    => $this->name,
            // 'isSaved'        => $this->is_saved ?? false, 
            'departments'    => $this->departments->pluck('name'), 
            'admissions'     => $this->admissions->map(function($adm) {
                return [
                    'year'       => $adm->year,
                    'minAverage' => $adm->min_average,
                    'minTotal'   => $adm->min_total ?? null, // إذا موجود
                ];
            }),
        ];
    }
}
