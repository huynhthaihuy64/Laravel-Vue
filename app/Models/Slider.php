<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Slider extends Model
{
    use HasFactory,Sortable,SoftDeletes;
    
    protected $fillable = [
        'name',
        'url',
        'file',
        'sort_by',
        'active',
        'creator_id',
        'modifier_id'
    ];

    public $sortables = ['id','name','active'];

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
