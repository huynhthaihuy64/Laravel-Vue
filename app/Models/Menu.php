<?php

namespace App\Models;

use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory, Sortable, SoftDeletes;

    protected $fillable = [
        'name',
        'parent_id',
        'sub_id',
        'description',
        'content',
        'active',
    ];
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
