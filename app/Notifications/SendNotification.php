<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

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
            ->action('View Invoice', 'http://localhost:8083/')
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
