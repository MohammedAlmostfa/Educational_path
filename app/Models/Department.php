<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Department
 *
 * Represents a department within a college.
 * Each department belongs to a single college.
 */
class Department extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'name',       // Name of the department
        'college_id', // Foreign key to the associated college
    ];

    /**
     * Get the college this department belongs to.
     */
    public function college()
    {
        return $this->belongsTo(College::class);
    }
}
