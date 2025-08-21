<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'governorate_id'];

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function colleges()
    {
        return $this->hasMany(College::class);
    }
}
