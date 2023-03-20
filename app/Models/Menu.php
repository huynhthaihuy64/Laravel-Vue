<?php

namespace App\Models;

use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'name',
        'parent_id',
        'description',
        'content',
        'active',
    ];
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
