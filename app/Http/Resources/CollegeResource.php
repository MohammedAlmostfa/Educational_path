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
                    'year'            => (int) $adm->year,
                    'minAverage'      => (float) $adm->min_average,
                    'minTotal'        => (float) $adm->min_total,
                    'preferenceScore' => (int) $adm->preference_score,
                ];
            }),
        ];
    }
}
