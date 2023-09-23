<?php

namespace App\Library\SMS\Providers;

use App\Library\SMS\SmsData;
use App\Library\SMS\SmsProvider;
use App\Models\SmsConfig;
use Exception;
use Illuminate\Support\Facades\Log;
use SoapClient;
use SoapFault;
use SoapParam;
use SoapVar;

class Sms20 extends SmsProvider
{
    private static $providerName = "sms20";
    private $username;
    private $password;
    private $senderNumber;
    private $soapClient;
    private static $soapUrl = "http://www.5m5.ir/webservice/soap/smsService.php?wsdl";



    public function __construct()
    {
        if ($this->checkConfig()) {
            $config = SmsConfig::query()
                ->where("provider", self::$providerName)
                ->first();
            $this->username = $config->username;
            $this->password = decrypt($config->password);
            $this->senderNumber = $config->sender_number;

            $this->soapClient = new SoapClient(self::$soapUrl, [
                'trace' => 1, // Allows you to trace the request and response for debugging
                'exceptions' => true, // Enables exceptions for SOAP errors
            ]);
        }
    }

    public function setData(SmsData $smsData)
    {
        $this->smsData = $smsData;
    }

    protected function checkConfig()
    {
        $config = SmsConfig::query()
            ->where("provider", self::$providerName)
            ->first();
        return ($config &&
            $config->username &&
            $config->password
        );
    }

    protected function sendRequest($action, $data)
    {
        $requestParams = [
            'username' => $this->username,
            'password' => $this->password,
            ...$data
        ];

        //another way of sending the parameters (not working)
        // $parm = array();
        // $parm[] = new SoapVar($this->username, XSD_STRING, null, null, 'username');
        // $parm[] = new SoapVar($this->password, XSD_STRING, null, null, 'password');
        $response = false;
        try {
            // Make a SOAP request to the service
            $response = $this->soapClient->$action($requestParams);
        } catch (SoapFault $e) {
            // Handle SOAP errors
            Log::critical("SMS20 SoapClient Failed", ["message" => $e->getMessage()]);
        } catch (Exception $e) {
            // Handle other exceptions
            Log::critical("SMS20 failure in sending request", ["message" => $e->getMessage()]);
        }
        return $response;
    }

    public function isSuccessful($responseResult)
    {
        // check if the result is valid with the api documentation (not given)
        return false;
    }

    public function sendTemplateSms()
    {
        //send template sms. not given in api list
        return false;
    }

    public function sendSms()
    {
        $phones = $this->smsData->getReception();
        $messages = $this->smsData->getMessage();

        //check if the phones is array so the messages must be array too vice versa
        if ((is_array($phones) && !is_array($messages)) || (!is_array($phones) && is_array($messages)) || (is_array($phones) && is_array($messages) && count($phones) != count($messages)))
            throw new Exception("both phones and messages must be array with same lentgh");

        if (is_array($phones) && is_array($messages) && count($phones) == count($messages))
            $this->sendGroupMessage();
        else {
            // send a single sms
            /*
            
            $params = [
                "sender_number" => $this->senderNumber,
                "receiver_number" => $phones,
                "note" => [$messages]
                // "note"=>$messages
            ];
            return $this->sendRequest('send_sms', $params);
            
            */
            return false;
        }

        return true;
    }

    /**
     * for group send
     * phones and messages and sender numbers must be arrays of string with same length
     */
    public function sendGroupMessage()
    {
        // send sms for a group of users
        return false;
    }

    public function getReceivedSms()
    {
        /*
        $data = [
            "number" => $this->senderNumber
        ];
        return $this->sendRequest('sms_receive', $data);
        */
        return [];
    }
}
