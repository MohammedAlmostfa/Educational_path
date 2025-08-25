<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Content
 *
 * Represents a content post or article.
 * Supports optional image, title, body text, and a flag to mark it as new.
 */
class Content extends Model
{
    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'image_url', // URL/path to the content image
        'title',     // Title of the content
        'body',      // Main body text of the content
        'is_new'     // Flag indicating if this content is new (1 = new, 0 = old)
    ];
}
