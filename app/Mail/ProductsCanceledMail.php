<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProductsCanceledMail extends Mailable
{
    use Queueable, SerializesModels;

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

    public function build()
    {
        return $this->to($this->userEmail)
            ->subject('Chengivy Store - Hủy đơn hàng của bạn')
            ->view('emails.user.canceled')
            ->with([
                'userName' => $this->userName,
                'userEmail' => $this->userEmail,
                'order' => $this->order,
                'productsCanceled' => $this->productsCanceled,
            ]);
    }
}
