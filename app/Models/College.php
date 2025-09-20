<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class College
 *
 * Represents a college entity which belongs to a university,
 * has many admissions, and can be linked to departments, branches,
 * and users who saved it.
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
     * A college can belong to many departments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
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
     * A college belongs to a branch.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * A college belongs to a college type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function collegeType()
    {
        return $this->belongsTo(CollegeType::class, 'college_type_id');
    }

    /**
     * A college has many admissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }

    /**
     * A college belongs to a university.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function university()
    {
        return $this->belongsTo(University::class);
    }

    /**
     * A college can be saved by many users with a priority level.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
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
     * Scope: Filter colleges by various conditions.
     *
     * Supported filters:
     * - governorates: array of governorate IDs (via university relation)
     * - name: college name (LIKE search)
     * - universityName: university name (LIKE search)
     * - min_average_from / min_average_to: filter by admission average range for year 2025
     * - departments: array of department IDs
     * - collegeType: single ID or array of IDs
     * - branches: array of branch IDs
     *
     * Results are sorted by highest admission min_average for year 2025.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
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

        // Filter by admission average range (year 2025 only)
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

        // Filter by college type (can be single ID or array of IDs)
        if (!empty($filters['collegeType'])) {
            $collegeTypeIds = is_array($filters['collegeType'])
                ? $filters['collegeType']
                : [$filters['collegeType']];

            $query->whereIn('college_type_id', $collegeTypeIds);
        }

        // Filter by branches
        if (!empty($filters['branches'])) {
            $query->whereHas('branch', function ($q) use ($filters) {
                $q->whereIn('id', $filters['branches']);
            });
        }

        // Sort by highest min_average in related admissions (year 2025)
        $query->withMax(
            ['admissions as max_min_average' => function ($q) {
                // You could add year filtering here if needed
            }],
            'min_average'
        )->orderByDesc('max_min_average');

        return $query;
    }
    protected static function booted()
    {
        $clearCache = function ($college) {
            $cacheKeys = Cache::get('all_colleges_keys', []);
            foreach ($cacheKeys as $key) {
                Cache::forget($key);
            }
            Cache::forget('all_colleges_keys');
        };

        static::created($clearCache);
        static::updated($clearCache);
        static::deleted($clearCache);
    }
}
