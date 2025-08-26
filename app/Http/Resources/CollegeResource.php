<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use League\CommonMark\Extension\CommonMark\Node\Block\ListData;
use PhpParser\Node\Expr\List_;

class CollegeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'universityName' => $this->university->name ?? null,
            'collegeName'    => $this->name,
            'governorate'    => $this->governorate ?? null,
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



            'admissions'     => $this->admissions->map(function ($adm) {
                return [
                    'year'            => (int) $adm->year,
                    'minAverage'      => (float) $adm->min_average,
                    'minTotal'        => (float) $adm->min_total,
                    'preferenceScore' => (int) $adm->preference_score,
                    'branch' => [
                        [
                            'id'   => $adm->branch->id,
                            'name' => $adm->branch->name,
                        ]
                    ],

                ];
            }),
        ];
    }
}
