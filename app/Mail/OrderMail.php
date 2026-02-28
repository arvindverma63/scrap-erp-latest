<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data, $filePath)
    {
        $this->data = $data;
        $this->filePath = $filePath;
    }

    public function build()
    {
        return $this->subject('Test Mail')
            ->view('emails.order-mail')
            ->attach($this->filePath, [
                'as' => 'order-details.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
