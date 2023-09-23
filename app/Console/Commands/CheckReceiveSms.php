<?php

namespace App\Console\Commands;

use App\Library\SMS\SmsData;
use App\Library\SMS\SmsProvider;
use Illuminate\Console\Command;

class CheckReceiveSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:updateReceive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'call the api and check for new received SMS';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $provider = SmsProvider::getProvider();
        $receivedSms = $provider->getReceivedSms(); // array of App\Library\SMS\ReceiveSmsData
        $unreadSms = 0;
        if ($receivedSms) {
            //there is some unread sms
            foreach ($receivedSms as $data) {
                if ($data->text == $data->senderNumber) {
                    $smsData = new SmsData($data->senderNumber, "باتشکر از شما بزودی با شما تماس خواهیم گرفت. \n لغو11");
                    $provider->setData($smsData);
                    $provider->sendSms();
                    $unreadSms++;
                }
            }
        }
        return $unreadSms;
    }
}
