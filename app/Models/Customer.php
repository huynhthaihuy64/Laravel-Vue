<?php

namespace App\Models;

use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory,Sortable;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'address',
        'email',
        'content',
        'total_tax',
    ];

    public function carts(){
        return $this->hasMany(Cart::class,'customer_id','id');
    }
}
