<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Governorate extends Model
{


    protected $fillable = ['name'];

    public function universities()
    {
        return $this->hasMany(University::class);
    }
}
