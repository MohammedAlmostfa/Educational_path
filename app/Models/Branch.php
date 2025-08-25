<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Branch
 *
 * Represents an academic branch or field of study.
 * Branches can be linked to multiple colleges via admissions.
 */
class Branch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name']; // Branch name

    /**
     * Get all colleges associated with this branch through admissions.
     */
    public function colleges()
    {
        return $this->belongsToMany(College::class, 'admissions');
    }
}
