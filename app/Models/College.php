<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'university_id', 'college_type', 'study_duration'];

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'admissions');
    }
    public function saveddByUsers()
{
    return $this->belongsToMany(User::class, 'savedColleges')->withTimestamps();
}
public function scopeFilterBy($query, $filters)
{

    if (!empty($filters['governorates'])) {
        $query->whereHas('university', function ($q) use ($filters) {
            $q->whereIn('governorate_id', $filters['governorates']);
        });
    }
   if (!empty($filters['min_average_from']) && !empty($filters['min_average_to'])) {
    $query->whereHas('admissions', function ($q) use ($filters) {
        $q->whereBetween('min_average', [$filters['min_average_from'], $filters['min_average_to']]);
    });
}

    // فلترة حسب الأقسام
    if (!empty($filters['departments'])) {
        $query->whereHas('departments', function ($q) use ($filters) {
            $q->whereIn('id', $filters['departments']);
        });
    }

    // فلترة حسب الفروع (اختياري)
    if (!empty($filters['branches'])) {
        $query->whereHas('branches', function ($q) use ($filters) {
            $q->whereIn('branches.id', $filters['branches']);
        });
    }

    return $query;
}


}
