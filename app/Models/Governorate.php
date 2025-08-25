<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Governorate
 *
 * Represents a governorate (administrative region).
 * A governorate can have many universities.
 */
class Governorate extends Model
{
    /**
     * Mass assignable attributes.
     */
    protected $fillable = ['name'];

    /**
     * Get the universities that belong to this governorate.
     */
    public function universities()
    {
        return $this->hasMany(University::class);
    }
}
