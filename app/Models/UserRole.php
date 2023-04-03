<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class UserRole extends Model
{
    
    protected $fillable = [
        'name'
    ];
}
