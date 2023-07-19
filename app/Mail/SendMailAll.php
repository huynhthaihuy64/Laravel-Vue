<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMailAll extends Mailable
{
    use Queueable, SerializesModels;


    public $user;
    public $param;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $param = [])
    {
        $this->user = $user;
        $this->param = $param;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $body = nl2br($this->user['content']);
        $send = $this->from(config('mail.from.address'), config('mail.from.name'));
        $send->subject($this->user['subject']);
        if (isset($this->param['is_admin_sent']) && $this->param['is_admin_sent'] == true) {
            $send->bcc(config('mail.from.admin_address'));
        }
        $send->subject($this->user['subject']);
        $send->view('email-send-mail-all',['user'=>$this->user , 'body' => $body]);
    }
}