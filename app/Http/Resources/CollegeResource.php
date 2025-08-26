<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CollegeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'universityName' => $this->university->name,
            'collegeName'    => $this->name,
            'isSaved'        => $this->is_saved ?? false,
            'departments'    => $this->department->name,
            "gender"         => $this->gender,
            'admissions'     => $this->admissions->map(function ($adm) {

                return [

                    'year'       => $adm->year,
                    'minAverage' => $adm->min_average,
                    'branch' => $adm->branch->name,
                    'minTotal'   => (int)$adm->min_total ?? null,
                ];
            }),
        ];
    }
}
