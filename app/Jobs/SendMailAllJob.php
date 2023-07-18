<?php

namespace App\Jobs;

use App\Mail\SendMailAll;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMailAllJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public array $param
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $emails = User::select('email')->get()->toArray();
        $send = new SendMailAll($this->param);
        Mail::to($emails)
            ->queue($send);
    }
}
