<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $this->from = env('TWILIO_FROM');
        $this->client = new Client($sid, $token);

    }

    public function sendSms($to, $message)
    {
        return true;
//        return $this->client->messages->create($to, [
//            'from' => $this->from,
//            'body' => $message,
////            'body' => "Here's our new product: https://lab5.website.work/scraperp/public/supplier/receipt/61",
////            'provideFeedback' => true,
////            'shortenUrls' => true,
//            'mediaUrl' => ['https://lab5.invoidea.work/scraperp/public/assets/images/cm-logo.png']
//        ]);
    }
}
