<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use HasFactory, Sortable,SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'content',
        'menu_id',
        'price',
        'price_sale',
        'active',
        'file',
        'images_quick_view',
        'creator_id',
        'modifier_id'
    ];
    public function menus()
    {
        return $this->belongsToMany(Menu::class)->select(['name', 'id']);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function userProducts()
    {
        return $this->hasMay(UserProduct::class, 'product_id', 'id');
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
