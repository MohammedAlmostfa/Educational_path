<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Department
 *
 * Represents a department entity in the system.
 * A department can belong to many colleges and can be filtered by name.
 */
class Department extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name'];

    /**
     * Relationship: A department belongs to many colleges (many-to-many).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function colleges()
    {
        return $this->belongsToMany(
            College::class,
            'department_college',
            'department_id',
            'college_id'
        );
    }

    /**
     * Scope: Filter departments by given conditions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     *      - name: string (optional, partial match)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterBy($query, $filters)
    {
        if (!empty($filters['name'])) {
            $query->where('name', 'LIKE', "%{$filters['name']}%");
        }

        return $query;
    }
}
