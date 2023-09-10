<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProductsWithDiscounMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $userEmail;
    public $productsWithDiscount;

    public function __construct($userName, $userEmail, $productsWithDiscount)
    {
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->productsWithDiscount = $productsWithDiscount;
    }

    public function build()
    {
        return $this->to($this->userEmail)
            ->subject('Chengivy Store - Chương trình khuyến mãi sản phẩm yêu thích của bạn')
            ->view('emails.user.sale')
            ->with([
                'userName' => $this->userName,
                'userEmail' => $this->userEmail,
                'productsWithDiscount' => $this->productsWithDiscount,
            ]);
    }
}
