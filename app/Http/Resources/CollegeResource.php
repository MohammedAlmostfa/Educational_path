<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CollegeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'universityName' => $this->university->name ?? null,
            'collegeName'    => $this->name,
            'isSaved'        => $this->is_saved ?? false,

            'department'     => $this->department ? [
                'id'   => $this->department->id,
                'name' => $this->department->name,
            ] : null,

            'gender'         => $this->gender,

            'admissions'     => $this->admissions->map(function ($adm) {
                return [
                    'year'       => $adm->year,
                    'minAverage' => $adm->min_average,
                    'branch'     => [
                        'id'   => $adm->branch->id,
                        'name' => $adm->branch->name,
                    ],
                    'minTotal'   => $adm->min_total !== null ? (int)$adm->min_total : null,
                ];
            }),
        ];
    }
}
