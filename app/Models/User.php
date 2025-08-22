<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\DeviceToken;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens; // 👈 مهم

class User extends Authenticatable
{


    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'activation_code',
        "average",
        "gender",
        "branch",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

public function favoriteColleges()
{
    return $this->belongsToMany(College::class, 'favorite_college_user', 'user_id', 'college_id')
                ->withTimestamps();
}


    public function devicetoke()
    {
        return $this->hasMany(DeviceToken::class);
    }

 public function savedlCollage()
    {
        return $this->hasMany(SavedCollage::class);
    }

    public function scopeFilterBy($query, $filteringData)
    {
        if (isset($filteringData['email'])) {
            $query->where(function ($q) use ($filteringData) {
                $q->where('email', 'LIKE', "%{$filteringData['email']}%");
            });
        }
        return $query;
    }
}
