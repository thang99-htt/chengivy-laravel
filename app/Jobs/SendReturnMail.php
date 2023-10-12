<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReturnMail;

class SendReturnMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userName;
    public $userEmail;
    public $returnProduct;

    public function __construct($userName, $userEmail, $returnProduct)
    {
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->returnProduct = $returnProduct;
    }

    public function handle()
    {
        Mail::to($this->userEmail)
            ->cc($this->userEmail)
            ->bcc($this->userEmail)
            ->send(new ReturnMail($this->userName, $this->userEmail, $this->returnProduct));
    }
}
