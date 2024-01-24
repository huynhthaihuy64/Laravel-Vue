<?php
  
namespace App\Models;

use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->select('id','name','email','phone','address');
    }

    public function getTotalSumAttribute()
    {
        return $this->newQuery()->where('user_id', $this->user_id)->sum('total');
    }
}