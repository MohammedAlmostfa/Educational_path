<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'university_id', 'college_type', 'study_duration', 'department_id'];

    // جميع الأقسام التابعة للكلية
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // القسم الافتراضي / الرئيسي للكلية
    public function mainDepartment()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'admissions');
    }
    /**
     * The university this college belongs to.
     */
    public function university()
    {
        return $this->belongsTo(University::class);
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
