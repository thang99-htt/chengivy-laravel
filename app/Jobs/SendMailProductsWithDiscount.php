<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProductsWithDiscounMail;

class SendMailProductsWithDiscount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userName;
    public $userEmail;
    public $productsWithDiscount;

    public function __construct($userName, $userEmail, $productsWithDiscount)
    {
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->productsWithDiscount = $productsWithDiscount;
    }

    public function handle()
    {
        Mail::to($this->userEmail)
            ->cc($this->userEmail)
            ->bcc($this->userEmail)
            ->send(new ProductsWithDiscounMail($this->userName, $this->userEmail, $this->productsWithDiscount));
    }
}
