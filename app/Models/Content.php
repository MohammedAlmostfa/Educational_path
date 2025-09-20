<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Content extends Model
{
    protected $fillable = [
        'image_url',
        'title',
        'body',
        'is_new',
        'viewers',
    ];

    protected $casts = [
        'is_new'  => 'integer',
        'viewers' => 'integer',
    ];

    /**
     * Scope to filter by provided data
     */
    public function scopeFilterBy($query, $filteringData)
    {
        if (isset($filteringData['is_new'])) {
            $query->where('is_new', $filteringData['is_new']);
        }

        if (isset($filteringData['title'])) {
            $query->where('title', 'LIKE', "%{$filteringData['title']}%");
        }

        return $query;
    }

    /**
     * Model events: clear content cache automatically
     */
    protected static function booted()
    {
        $clearContentCache = function () {
            $cacheKeys = Cache::get('all_contents_keys', []);
            foreach ($cacheKeys as $key) {
                Cache::forget($key);
            }
            Cache::forget('all_contents_keys');
        };

        static::created($clearContentCache);
        static::updated($clearContentCache);
        static::deleted($clearContentCache);
    }

    /**
     * Helper to store each cache key
     */
    public static function rememberCacheKey(string $key)
    {
        $cacheKeys = Cache::get('all_contents_keys', []);
        if (!in_array($key, $cacheKeys)) {
            $cacheKeys[] = $key;
            Cache::forever('all_contents_keys', $cacheKeys);
        }
    }
}
