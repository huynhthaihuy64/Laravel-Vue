<?php
  
namespace App\Models;

use App\Traits\Sortable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class UserProduct extends Model
{
    use HasFactory, Sortable,SoftDeletes;
  
    public $table = "user_products";
  
    protected $fillable = [
        'user_id',
        'product_id',
        'qty',
        'price',
        'total'
    ];
    protected $appends = ['price_currency', 'total_currency'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id')->select('id', 'name', 'price');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->select('id','name','email','phone','address');
    }

    public function getTotalSumAttribute()
    {
        return $this->newQuery()->where('user_id', $this->user_id)->sum('total');
    }

    public function getCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->format('Y/m/d');
    }

    public function getPriceCurrencyAttribute()
    {
        if (isset($this->attributes['price'])) {
            $jsonFilePath = storage_path('setting.json');
            $jsonData = file_get_contents($jsonFilePath);
            $data = json_decode($jsonData, true);
            $priceCurrency = (float)(round($data[Auth::user()->currency]['value'] * $this->attributes['price'], 4));
            return $priceCurrency;
        }
        return null;
    }

    public function getTotalCurrencyAttribute()
    {
        if (isset($this->attributes['total_all'])) {
            $jsonFilePath = storage_path('setting.json');
            $jsonData = file_get_contents($jsonFilePath);
            $data = json_decode($jsonData, true);
            $priceCurrency = (float)(round($data[Auth::user()->currency]['value'] * $this->attributes['total_all'], 4));
            return $priceCurrency;
        }
        return null;
    }
}