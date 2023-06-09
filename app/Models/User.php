<?php

namespace App\Models;

use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Twilio\Rest\Client;
use Exception;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Sortable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'department',
        'gender',
        'birthday',
        'address',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function generateCode()
    {
        $code = rand(1000, 9999);
  
        UserCode::updateOrCreate(
            [ 'user_id' => auth()->user()->id ],
            [ 'code' => $code ]
        );
  
        $receiverNumber = auth()->user()->phone;
        $message = "2FA login code is ". $code;
    
        try {
   
            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = getenv("TWILIO_FROM");
    
            $client = new Client($account_sid, $auth_token);
            $client->messages->create($receiverNumber, [
                'from' => $twilio_number, 
                'body' => $message]);
    
        } catch (Exception $e) {
            info("Error: ". $e->getMessage());
        }
    }

    public function messages()
    {
    return $this->hasMany(Message::class);
    }

    /* 
        Notify Email
    */
    public function routeNotificationForMail($notification)
    {
        // Return email address only...
        // return auth()->user()->email;
        if(auth()->user()){
            return auth()->user()->email;
        }
        return $this->email;
    }
    
    /* 
        Notify Slack
    */
    public function routeNotificationForSlack($notification)
    {
        return 'https://hooks.slack.com/services/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';
    }

    // /**
    //  * Route notifications for the Vonage channel.
    //  *
    //  * @param  \Illuminate\Notifications\Notification  $notification
    //  * @return string
    //  */
    // public function routeNotificationForVonage($notification)
    // {
    //     if(auth()->user()){
    //         return auth()->user()->phone;
    //     }
    //     return $this->phone;
    // }

    public function userAlbums()
    {
        return $this->hasMany(UserAlbum::class);
    }

    public function role()
    {
        return $this->belongsTo(UserRole::class);
    }
}
