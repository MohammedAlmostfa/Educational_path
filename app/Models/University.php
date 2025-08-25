<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class University
 *
 * Represents a university.
 * Each university belongs to a governorate and can have many colleges.
 */
class University extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = ['name', 'governorate_id'];

    /**
     * Get the governorate this university belongs to.
     */
    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    /**
     * Get the colleges associated with this university.
     */
    public function colleges()
    {
        return $this->hasMany(College::class);
    }
}
