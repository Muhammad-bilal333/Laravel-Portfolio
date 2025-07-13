<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    protected $fillable = [
        'user_id', 'roll_number', 'batch', 'department', 'designation' ,  'duties'
    ];

    protected $casts = [
        'duties' => 'array', // Ensures duties are stored as JSON
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
