<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderCancelledByUserMail;

class SendMailOrderCancelledByUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userName;
    public $userEmail;
    public $orderCancelled;

    public function __construct($userName, $userEmail, $orderCancelled)
    {
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->orderCancelled = $orderCancelled;
    }

    public function handle()
    {
        Mail::to($this->userEmail)
            ->cc($this->userEmail)
            ->bcc($this->userEmail)
            ->send(new OrderCancelledByUserMail($this->userName, $this->userEmail, $this->orderCancelled));
    }
}
