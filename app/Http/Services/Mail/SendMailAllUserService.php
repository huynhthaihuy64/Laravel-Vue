<?php

namespace App\Http\Services\Mail;

use App\Classes\Response;
use App\Jobs\SendMailAllJob;

class SendMailAllUserService
{
   public function sendAll($param)
   {
        try {
            $job = new SendMailAllJob($param);
            dispatch($job);
            return Response::success("", __('messages.mail.send.success'));
        } catch (\Exception $e) {
            throw $e;
        }
   }
}
