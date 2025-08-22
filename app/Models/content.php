<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = [
        'image_url', // fixed the key from "image-url" to valid snake_case
        'title',
        'body',
        'status'
    ];
}
