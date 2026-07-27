<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Goat extends Model
{
    use HasFactory,SoftDeletes;


    protected $fillable = [
        'product_id',
        'time',
        'user_id',
        'cal_value',
        'real_value',
        'type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
