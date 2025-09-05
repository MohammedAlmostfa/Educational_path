<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class College
 *
 * Represents a college entity in the system.
 * A college belongs to a university, has many departments, admissions,
 * and can be saved by multiple users.
 */
class College extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'university_id',
        'college_type_id',
        'study_duration',
        'gender',
        'branch_id',
    ];

    /**
     * Relationship: A college has many departments (many-to-many).
     */
    public function departments()
    {
        return $this->belongsToMany(
            Department::class,
            'department_college',
            'college_id',
            'department_id'
        );
    }

    /**
     * Relationship: A college belongs to one branch.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Relationship: A college belongs to one type.
     */
    public function collegeType()
    {
        return $this->belongsTo(CollegeType::class, 'college_type_id');
    }

    /**
     * Relationship: A college has many admissions.
     */
    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }

    /**
     * Relationship: A college belongs to one university.
     */
    public function university()
    {
        return $this->belongsTo(University::class);
    }

    /**
     * Relationship: Users who saved this college (many-to-many).
     */
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
        if (!empty($filters['min_average_from']) && !empty($filters['min_average_to'])) {
            $from = number_format((float) $filters['min_average_from'], 2, '.', '');
            $to   = number_format((float) $filters['min_average_to'], 2, '.', '');

            $query->whereHas('admissions', function ($q) use ($from, $to) {
                $q->whereBetween('min_average', [$from, $to]);
            });
        }

        // Filter by departments
        if (!empty($filters['departments'])) {
            $query->whereHas('departments', function ($q) use ($filters) {
                $q->whereIn('departments.id', $filters['departments']);
            });
        }

        // Filter by college type
        if (!empty($filters['collegeType'])) {
            $query->whereIn('college_type_id', $filters['collegeType']);
        }

        // Filter by branches
        if (!empty($filters['branches'])) {
            $query->whereHas('branch', function ($q) use ($filters) {
                $q->whereIn('id', $filters['branches']);
            });
        }

        // Sort by highest min_average in related admissions
        $query->orderByDesc(
            \App\Models\Admission::select('min_average')
                ->whereColumn('admissions.college_id', 'colleges.id')
                ->orderByDesc('min_average')
                ->limit(1)
        );

        return $query;
    }
}
