<?php

namespace App\Allmails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $setting;
    public $pdf;

    /**
     * Create a new message instance.
     */
    public function __construct($order, $setting, $pdf)
    {
        $this->order = $order;
        $this->setting = $setting;
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('ScrapERP Invoice Mail')
            ->view('emails.invoicemail') // simple text email
            ->attachData(
                $this->pdf->output(),
                'invoice.pdf',
                ['mime' => 'application/pdf']
            );
    }
}
