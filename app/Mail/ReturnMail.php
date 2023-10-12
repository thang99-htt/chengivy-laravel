<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReturnMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $userEmail;
    public $returnProduct;

    public function __construct($userName, $userEmail, $returnProduct)
    {
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->returnProduct = $returnProduct;
    }

    public function build()
    {
        return $this->to($this->userEmail)
            ->subject('Hoàn trả đơn hàng - Chengivy Store')
            ->view('emails.user.return')
            ->with([
                'userName' => $this->userName,
                'userEmail' => $this->userEmail,
                'returnProduct' => $this->returnProduct,
            ]);
    }
}
