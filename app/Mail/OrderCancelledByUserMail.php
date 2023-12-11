<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCancelledByUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $userEmail;
    public $order;
    public $orderCancelled;


    public function __construct($userName, $userEmail, $orderCancelled)
    {
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->orderCancelled = $orderCancelled;
    }

    public function build()
    {
        return $this->to($this->userEmail)
            ->subject('Chengivy Store - Hủy đơn thành công')
            ->view('emails.user.cancelbyuser')
            ->with([
                'userName' => $this->userName,
                'userEmail' => $this->userEmail,
                'orderCancelled' => $this->orderCancelled,
            ]);
    }
}
