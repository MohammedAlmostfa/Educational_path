<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = [
        'image_url', 
        'title',
        'body',
        'is_new'
    ];
}
