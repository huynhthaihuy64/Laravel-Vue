<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ThankForBuy extends Mailable
{
    use Queueable, SerializesModels;


    public $data;
    public $param;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $param = [])
    {
        $this->data = $data;
        $this->param = $param;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $send = $this->from(config('mail.from.address'), config('mail.from.name'));
        $send->subject("Thank you for purchasing our products");
        $send->view('email-thanks-for',['user'=>$this->data , 'param' => $this->param]);
    }
}
