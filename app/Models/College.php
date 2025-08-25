<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class College
 *
 * Represents a college.
 * Colleges belong to a university, have multiple departments, admissions, and branches.
 * Also supports saving by users and filtering.
 */
class College extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = ['name', 'university_id', 'college_type', 'study_duration'];

    /**
     * The university this college belongs to.
     */
    public function university()
    {
        return $this->belongsTo(University::class);
    }

    /**
     * Departments within this college.
     */
    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Admissions related to this college.
     */
    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }

    /**
     * Branches available in this college through admissions.
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'admissions');
    }

    /**
     * Users who have saved this college.
     */
    public function saveddByUsers()
    {
        return $this->belongsToMany(User::class, 'savedColleges')->withTimestamps();
    }

    /**
     * Scope a query to filter colleges based on various criteria.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterBy($query, $filters)
    {
        // Filter by governorates via the university
        if (!empty($filters['governorates'])) {
            $query->whereHas('university', function ($q) use ($filters) {
                $q->whereIn('governorate_id', $filters['governorates']);
            });
        }

        // Filter by min_average range from admissions
        if (!empty($filters['min_average_from']) && !empty($filters['min_average_to'])) {
            $query->whereHas('admissions', function ($q) use ($filters) {
                $q->whereBetween('min_average', [$filters['min_average_from'], $filters['min_average_to']]);
            });
        }

        // Filter by departments
        if (!empty($filters['departments'])) {
            $query->whereHas('departments', function ($q) use ($filters) {
                $q->whereIn('id', $filters['departments']);
            });
        }

        // Optional: filter by branches
        if (!empty($filters['branches'])) {
            $query->whereHas('branches', function ($q) use ($filters) {
                $q->whereIn('branches.id', $filters['branches']);
            });
        }

        return $query;
    }
}
