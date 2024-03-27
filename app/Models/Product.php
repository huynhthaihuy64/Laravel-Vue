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
        'inventory_number',
        'creator_id',
        'modifier_id'
    ];
    protected $appends = ['price_sale_currency', 'price_currency'];
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

    public function getPriceSaleCurrencyAttribute()
    {
        if (isset($this->attributes['price_sale'])) {
            $jsonFilePath = storage_path('setting.json');
            $jsonData = file_get_contents($jsonFilePath);
            $data = json_decode($jsonData, true);
            $priceCurrency = number_format(round($data[auth()->user()->currency]['value'] * $this->attributes['price_sale'], 4), 4);
            return $priceCurrency;
        }
        return null;
    }

    public function getPriceCurrencyAttribute()
    {
        $jsonFilePath = storage_path('setting.json');
        $jsonData = file_get_contents($jsonFilePath);
        $data = json_decode($jsonData, true);
        $priceCurrency = number_format(round($data[auth()->user()->currency]['value'] * $this->attributes['price'], 4), 4);
        return $priceCurrency;
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
