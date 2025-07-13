<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvisorClassAssignment extends Model
{
    protected $fillable = ['advisor_id', 'batch', 'section'];

    public function advisor()
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }
}
