<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Branch
 *
 * Represents an academic branch or field of study.
 */
class Branch extends Model
{
    use HasFactory;

    protected $fillable = ['name'];


    public function colleges()
    {
        return $this->hasMany(College::class, 'branch_id');
    }
}
