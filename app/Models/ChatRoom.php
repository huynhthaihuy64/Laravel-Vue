<?php

namespace App\Models;

use App\Events\ChatRoomBroadCast;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'friend_id',
        'chat'
    ];

    protected $dispatchesEvents = [
        'created' => ChatRoomBroadCast::class,
    ];

    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    public function getCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->format('h:i A, F j, Y');
    }
}
