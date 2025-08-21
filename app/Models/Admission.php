<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    use HasFactory;

    protected $fillable = [
        'college_id',
        'branch_id',
        'year',
        'min_average',
        'min_total',
        'preference_score'
    ];

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
