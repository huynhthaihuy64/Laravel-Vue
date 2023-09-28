<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slider extends Model
{
    use HasFactory,Sortable,SoftDeletes;
    
    protected $fillable = [
        'name',
        'url',
        'file',
        'sort_by',
        'active',
    ];

    public $sortables = ['id','name','active'];
}
