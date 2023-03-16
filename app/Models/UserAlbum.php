<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAlbum extends Model
{
    protected $fillable = ['user_id','name', 'path'];
}
