<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'university_id',
        'college_type',
        'study_duration',
        'department_id',
        'gender'
    ];

    /**
     * Relationship: All departments that belong to this college.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Relationship: The main/default department for the college.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mainDepartment()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Relationship: All admissions for this college.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }

    /**
     * Relationship: All branches related to the college via admissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'admissions');
    }

    /**
     * Relationship: The university this college belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function university()
    {
        return $this->belongsTo(University::class);
    }

    /**
     * Accessor to convert the stored numeric gender value to human-readable text.
     *
     * @param int $value
     * @return string
     */
    public function getGenderAttribute($value)
    {
        $map = [
            0 => 'أنثى',    // Female
            1 => 'ذكر',      // Male
            2 => 'كلاهما',   // Both
        ];

        return $map[$value] ?? 'كلاهما';
    }

    /**
     * Scope to filter colleges based on various criteria.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterBy($query, $filters)
    {
        // Filter by governorates via the university relationship
        if (!empty($filters['governorates'])) {
            $query->whereHas('university', function ($q) use ($filters) {
                $q->whereIn('governorate_id', $filters['governorates']);
            });
        }

        // Filter by minimum average score range via admissions
        if (!empty($filters['min_average_from']) && !empty($filters['min_average_to'])) {
            $query->whereHas('admissions', function ($q) use ($filters) {
                $q->whereBetween('min_average', [
                    $filters['min_average_from'],
                    $filters['min_average_to']
                ]);
            });
        }

        // Optional: filter by all departments
        if (!empty($filters['departments'])) {
            $query->whereHas('department', function ($q) use ($filters) {
                $q->whereIn('id', $filters['departments']);
            });
        }

        // Optional: filter by branches via admissions
        if (!empty($filters['branches'])) {
            $query->whereHas('branches', function ($q) use ($filters) {
                $q->whereIn('branches.id', $filters['branches']);
            });
        }

        return $query;
    }
}
