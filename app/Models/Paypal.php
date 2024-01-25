<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paypal extends Model
{
    use HasFactory;

    protected $table = 'paypal';

    protected $fillable = [
        'cart_id',
        'token',
        'user_id'
    ];
}
