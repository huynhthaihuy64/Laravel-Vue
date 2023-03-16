<?php

namespace App\Notifications;

use App\Mail\MailCronjob;
use App\Mail\OrderShipped;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class SendNotification extends Notification
{
    use Queueable;
    private $user;
    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['slack', 'mail', 'vonage'];
    }

    public function toMail($notifiable)
    {

        return (new MailMessage)
            ->greeting('Hello!')
            ->line('One of your invoices has been paid!')
            ->action('View Invoice', 'http://127.0.0.1:8000/')
            ->line('Thank ' . $this->user->name . ' for using our application!');
    }
    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->success()
            ->content($this->user->name . ' tài khoản đã đăng nhập thành công');
    }
    // public function toVonage($notifiable)
    // {
    //     return (new VonageMessage())
    //         ->content('Thank ' . $this->user->name . ' for using our application!')
    //         ->unicode();
    // }
}
