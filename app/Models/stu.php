<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stu extends Model  // Class names should be capitalized as per PSR standards
{
    use HasFactory;

    // Manually specify the table name
    protected $table = 'students';

    // Define fillable columns for mass assignment
    protected $fillable = [
        'user_id', 'roll_number', 'Batch','section' , 'department'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
