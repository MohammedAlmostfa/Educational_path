<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CollegeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'university' => [
                'name' => $this->university->name ?? null,
                'id' => $this->university->id ?? null,
            ],
            'collegeName'    => $this->name,

            'governorate' => $this->university && $this->university->governorate
                ? [
                    'id'   => $this->university->governorate->id,
                    'name' => $this->university->governorate->name,
                ]
                : null,

            'gender'         => $this->gender,
            'collegeType'    => $this->college_type ?? null,
            'studyDuration'  => (int) $this->study_duration,
            'isSaved'        => $this->is_saved ?? false,

            'departments'    => $this->departments->map(function ($dep) {
                return [
                    'id'   => $dep->id,
                    'name' => $dep->name,
                ];
            }),

            'branch' => $this->branch ? [
                'id'   => $this->branch->id,
                'name' => $this->branch->name,
            ] : null,

            'admissions'     => $this->admissions->map(function ($adm) {
                return [
                    'id'            => (int) $adm->id,
                    'year'            => (int) $adm->year,
                    'min_average'      => (float) $adm->min_average,
                    'min_total'        => (float) $adm->min_total,
                    'preference_score' => (int) $adm->preference_score,
                ];
            }),
        ];
    }
}
