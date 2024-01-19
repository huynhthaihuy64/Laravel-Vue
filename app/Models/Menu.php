<?php

namespace App\Models;

use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

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
        'creator_id',
        'modifier_id'
    ];
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    /**
     * The booting method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Auto creator
        static::creating(
            function ($model) {
                $model->creator_id = Auth::id();
            }
        );

        // Auto modifier
        static::updating(
            function ($model) {
                $model->modifier_id = Auth::id();
            }
        );
    }
}
