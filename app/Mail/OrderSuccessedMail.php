<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderSuccessedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $userEmail;
    public $order;
    public $orderSuccessed;


    public function __construct($userName, $userEmail, $orderSuccessed)
    {
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->orderSuccessed = $orderSuccessed;
    }

    public function build()
    {
        return $this->to($this->userEmail)
            ->subject('Chengivy Store - Đặt hàng thành công')
            ->view('emails.user.successed')
            ->with([
                'userName' => $this->userName,
                'userEmail' => $this->userEmail,
                'orderSuccessed' => $this->orderSuccessed,
            ]);
    }
}
