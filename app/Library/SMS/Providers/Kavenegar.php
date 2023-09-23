<?php

namespace App\Library\SMS\Providers;

use App\Library\SMS\ReceiveSmsData;
use App\Library\SMS\SmsData;
use App\Library\SMS\SmsProvider;
use App\Models\SmsConfig;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Kavenegar extends SmsProvider
{
    private static $providerName = "kavenegar";
    private static $apiUrl = "https://api.kavenegar.com/v1/";
    private $apiKey;
    private $senderNumber;

    public function __construct()
    {
        if ($this->checkConfig()) {
            $config = SmsConfig::query()
                ->where("provider", self::$providerName)
                ->first();
            $this->apiKey = $config->api_key;
            $this->senderNumber = $config->sender_number;
        } else
            throw new Exception("تنظمیات کاوه‌نگار به درستی انجام نشده است");
    }

    public function setData(SmsData $smsData)
    {
        $this->smsData = $smsData;
    }

    private function checkSmsData()
    {
        if (!$this->smsData)
            throw new Exception("پارامتر های مربوط به پیامک وارد نشده است.");
    }

    protected function sendRequest($action, $data, $method = "post", $returnResponse = false): array|bool
    {
        $url = self::$apiUrl . $this->apiKey . "/" . $action;
        $response = Http::retry(3, 1)
            ->acceptJson()
            ->asForm();
        if ($method == "post")
            $response = $response->post($url, $data);
        else if ($method == "get")
            $response = $response->get($url, $data);

        if ($returnResponse)
            return $response->json();
        else {
            if ($this->isSuccessful($response))
                return true;
            else {
                Log::critical('Kavenegar did not successfully ended the request.', ["response" => $response]);
                return false;
            }
        }
    }

    protected function checkConfig()
    {
        $config = SmsConfig::query()
            ->where("provider", self::$providerName)
            ->first();
        return ($config &&
            $config->api_key &&
            $config->sender_number
        );
    }

    public function isSuccessful($responseResult)
    {
        return is_array($responseResult) &&
            isset($responseResult["return"]) &&
            isset($responseResult["return"]["status"]) &&
            $responseResult["return"]["status"] == 200;
    }

    public function sendTemplateSms()
    {
        $this->checkSmsData();
        $data = [
            "receptor" => $this->smsData->getReception(),
            "template" => $this->smsData->getTemplateId()
        ];
        foreach ($this->smsData->getTokens() as $index => $t)
            if ($index > 0)
                $data["token" . ($index + 1)] = $t;
            else
                $data["token"] = $t;
        $this->sendRequest("verify/lookup.json", $data);
        return true;
    }

    public function sendSms()
    {
        $this->checkSmsData();
        $phones = $this->smsData->getReception();
        $messages = $this->smsData->getMessage();
        if (is_array($phones) && is_array($messages)) {
            if (count($phones) != count($messages))
                throw new Exception("phones and messages array must be the same length");
            return $this->sendGroupMessage();
        }

        $data = [
            "receptor" => $phones,
            "sender" => $this->senderNumber,
            "message" => urlencode($messages),
        ];
        $this->sendRequest("sms/send.json", $data, "get");
        return true;
    }

    /**
     * for group send
     * phones and messages and sender numbers must be arrays of string with same length
     */
    public function sendGroupMessage()
    {
        $this->checkSmsData();
        $phones = $this->smsData->getReception();
        $messages = $this->smsData->getMessage();
        $sender = [];
        for ($i = 0; $i < count($phones); $i++)
            $sender[] = $this->senderNumber;

        $data = [
            "receptor" => json_encode($phones),
            "sender" => json_encode($sender),
            "message" => json_encode($messages), // if error happens in some situations do urlencode on each of messages values
        ];

        $this->sendRequest("sms/sendarray.json", $data, "post");
        return true;
    }

    public function getReceivedSms($isRead = 0): array
    {
        $arrayOfReceiveSmsData = [];

        $data = [
            "linenumber" => $this->senderNumber,
            "isread" => $isRead,
        ];
        $receivedSms = $this->sendRequest('sms/receive.json', $data, "post", true);

        if ($this->isSuccessful($receivedSms)) {
            $receivedSms = $receivedSms["entries"];

            if ($receivedSms)
                foreach ($receivedSms as $data)
                    $arrayOfReceiveSmsData[] = new ReceiveSmsData(
                        $data["messageid"],
                        $data["sender"],
                        $data["message"],
                        Carbon::parse($data['date']),
                        $data["receptor"],
                    );
        } else
            Log::critical('Kavenegar did not successfully ended the request.', ["response" => $receivedSms]);

        return $arrayOfReceiveSmsData;
    }
}
