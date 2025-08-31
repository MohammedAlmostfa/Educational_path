<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollegeType extends Model
{
    protected $fillable = ['name'];
      public function colleges()
    {
        return $this->hasMany(College::class, 'college_type_id');
    }
}
