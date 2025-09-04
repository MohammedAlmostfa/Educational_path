<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Admission
 *
 * Represents an admission record for a college and branch.
 * Contains information about minimum requirements and preference score.
 */
class Admission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'college_id',       // Related college
        'year',             // Admission year
        'min_average',      // Minimum average required
        'min_total',        // Minimum total score required
        'preference_score', // Score for preference ranking
    ];
protected $casts= [
'min_average' => 'decimal:2',
];

    /**
     * Get the college associated with the admission.
     */
    public function college()
    {
        return $this->belongsTo(College::class);
    }


}
