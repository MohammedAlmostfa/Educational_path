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
        'college_type',
        'study_duration',
        'gender',
        'branch_id',
    ];

    /**
     * علاقة: الكلية لها عدة أقسام عبر جدول وسيط
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
     * علاقة: الكلية مرتبطة بفرع واحد
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * علاقة: الكلية لها عدة قبول
     */
    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }

    /**
     * علاقة: الكلية تتبع لجامعة واحدة
     */
    public function university()
    {
        return $this->belongsTo(University::class);
    }

    /**
     * Accessor: تحويل قيمة gender من رقم لنص
     */
    public function getGenderAttribute($value)
    {
        $map = [
            0 => 'أنثى',
            1 => 'ذكر',
            2 => 'كلاهما',
        ];

        return $map[$value] ?? 'كلاهما';
    }

    /**
     * فلترة حسب مجموعة شروط
     */
    public function scopeFilterBy($query, $filters)
    {
        if (!empty($filters['governorates'])) {
            $query->whereHas('university', function ($q) use ($filters) {
                $q->whereIn('governorate_id', $filters['governorates']);
            });
        }

        if (isset($filters['name'])) {
            $query->where('name', 'LIKE', "%{$filters['name']}%");
        }

        if (isset($filters['universityName'])) {
            $query->whereHas('university', function ($q) use ($filters) {
                $q->where('name', 'LIKE', "%{$filters['universityName']}%");
            });
        }

        if (isset($filters['min_average_from'], $filters['min_average_to'])) {
            $query->whereHas('admissions', function ($q) use ($filters) {
                $q->whereBetween('min_average', [
                    $filters['min_average_from'],
                    $filters['min_average_to']
                ]);
            });
        }

        if (!empty($filters['departments'])) {
            $query->whereHas('departments', function ($q) use ($filters) {
                $q->whereIn('department_id', $filters['departments']);
            });
        }

        if (!empty($filters['branches'])) {
            $query->whereHas('branch', function ($q) use ($filters) {
                $q->whereIn('id', $filters['branches']);
            });
        }

        return $query;
    }
}
