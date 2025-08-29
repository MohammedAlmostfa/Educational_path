<?php

namespace App\Models;

use App\Models\DeviceToken;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens; // Important for API token authentication

/**
 * Class User
 *
 * Represents a user in the system.
 * Handles authentication, profile data, saved colleges, and device tokens.
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'activation_code',
        'average',
        'gender',
        'branch_id',
        'is_active',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to specific data types.
     *
     * @return array<string,string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'integer',
            'average'           => 'float',
            'is_admin'          => 'integer',
            'activation_code'   => 'string',
            'gender'            => 'integer',
        ];
    }

    /**
     * Many-to-many relationship with College for saved colleges.
     */
    public function savedColleges()
    {
        return $this->belongsToMany(
            College::class,
            'saved_college_user',
            'user_id',
            'college_id'
        )->withPivot('priority')->withTimestamps();
    }

    /**
     * One-to-many relationship with DeviceToken.
     * A user can have multiple device tokens for notifications.
     */
    public function devicetoke()
    {
        return $this->hasMany(DeviceToken::class);
    }

    /**
     * One-to-one relationship with Branch.
     * Each user can have a branch associated.
     */
    public function branch()
    {
        return $this->hasOne(Branch::class);
    }

    /**
     * Scope to filter users by provided criteria.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filteringData
     * @return \Illuminate\Database\Eloquent\Builder
     */
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
