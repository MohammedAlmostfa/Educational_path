<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'university_id',
        'college_type_id',
        'study_duration',
        'gender',
        'branch_id',
    ];

    public function departments()
    {
        return $this->belongsToMany(
            Department::class,
            'department_college',
            'college_id',
            'department_id'
        );
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function collegeType()
    {
        return $this->belongsTo(CollegeType::class, 'college_type_id');
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function savedByUsers()
    {
        return $this->belongsToMany(
            User::class,
            'saved_college_user',
            'college_id',
            'user_id'
        )->withPivot('priority')->withTimestamps();
    }

    /**
     * Scope: Filter colleges by multiple conditions.
     */
    public function scopeFilterBy($query, $filters)
    {
        // Filter by governorates through related university
        if (!empty($filters['governorates'])) {
            $query->whereHas('university', function ($q) use ($filters) {
                $q->whereIn('governorate_id', $filters['governorates']);
            });
        }

        // Filter by college name
        if (!empty($filters['name'])) {
            $query->where('name', 'LIKE', "%{$filters['name']}%");
        }

        // Filter by university name
        if (!empty($filters['universityName'])) {
            $query->whereHas('university', function ($q) use ($filters) {
                $q->where('name', 'LIKE', "%{$filters['universityName']}%");
            });
        }

        // Filter by admission average range
        // Filter by admission average range (سنة 2025 فقط)
if (!empty($filters['min_average_from']) && !empty($filters['min_average_to'])) {
    $from = number_format((float) $filters['min_average_from'], 2, '.', '');
    $to   = number_format((float) $filters['min_average_to'], 2, '.', '');

    $query->whereHas('admissions', function ($q) use ($from, $to) {
        $q->where('year', 2025)
          ->whereBetween('min_average', [$from, $to]);
    });
}


        // Filter by departments
        if (!empty($filters['departments'])) {
            $query->whereHas('departments', function ($q) use ($filters) {
                $q->whereIn('departments.id', $filters['departments']);
            });
        }

        // Filter by college type (ID)
        if (!empty($filters['collegeType'])) {
            $collegeTypeIds = is_array($filters['collegeType'])
                ? $filters['collegeType']
                : [$filters['collegeType']]; // إذا كانت مفردة، حولها لمصفوفة

            $query->whereIn('college_type_id', $collegeTypeIds);
        }

        // Filter by branches
        if (!empty($filters['branches'])) {
            $query->whereHas('branch', function ($q) use ($filters) {
                $q->whereIn('id', $filters['branches']);
            });
        }

        // Sort by highest min_average in related admissions (year 2025)
        $query->withMax(['admissions as max_min_average' => function ($q) {
            $q->where('year', 2025);
        }], 'min_average')->orderByDesc('max_min_average');

        return $query;
    }
}
