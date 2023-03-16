<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Sortable;

class Slider extends Model
{
    use HasFactory;
    use Sortable;
    
    protected $fillable = [
        'name',
        'url',
        'file',
        'sort_by',
        'active',
    ];

    public $sortables = ['id','name','active'];
}
