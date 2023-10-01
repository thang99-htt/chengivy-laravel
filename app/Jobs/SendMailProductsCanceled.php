<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProductsCanceledMail;

class SendMailProductsCanceled implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userName;
    public $userEmail;
    public $order;
    public $productsCanceled;

    public function __construct($userName, $userEmail, $order, $productsCanceled)
    {
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->order = $order;
        $this->productsCanceled = $productsCanceled;
    }

    public function handle()
    {
        Mail::to($this->userEmail)
            ->cc($this->userEmail)
            ->bcc($this->userEmail)
            ->send(new ProductsCanceledMail($this->userName, $this->userEmail, $this->order, $this->productsCanceled));
    }
}
