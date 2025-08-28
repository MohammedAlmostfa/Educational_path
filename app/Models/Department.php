<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * علاقة: القسم مرتبط بعدة كليات عبر جدول وسيط
     */
    public function colleges()
    {
        return $this->belongsToMany(College::class, 'department_college', 'department_id', 'college_id');
    }

    /**
     * فلترة الأقسام حسب الاسم
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
   public function scopeFilterBy($query, $filters)
{
    if (!empty($filters['name'])) {
        $query->where('name', 'LIKE', "%{$filters['name']}%");
    }
}

}
