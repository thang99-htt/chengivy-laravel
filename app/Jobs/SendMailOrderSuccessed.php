<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderSuccessedMail;

class SendMailOrderSuccessed implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userName;
    public $userEmail;
    public $orderSuccessed;

    public function __construct($userName, $userEmail, $orderSuccessed)
    {
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->orderSuccessed = $orderSuccessed;
    }

    public function handle()
    {
        Mail::to($this->userEmail)
            ->cc($this->userEmail)
            ->bcc($this->userEmail)
            ->send(new OrderSuccessedMail($this->userName, $this->userEmail, $this->orderSuccessed));
    }
}
