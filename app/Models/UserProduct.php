<?php
  
namespace App\Models;
  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProduct extends Model
{
    use HasFactory, SoftDeletes;
  
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
}