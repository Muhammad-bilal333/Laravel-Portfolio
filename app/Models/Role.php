<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public function getRouteKeyName()
    {
        return 'name';  // This tells Laravel to find the Role by 'name' instead of 'id'
    }
}
