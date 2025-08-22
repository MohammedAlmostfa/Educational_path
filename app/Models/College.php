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
    public function favoredByUsers()
{
    return $this->belongsToMany(User::class, 'college_user')->withTimestamps();
}

}
