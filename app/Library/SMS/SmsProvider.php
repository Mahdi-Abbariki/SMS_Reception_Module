<?php

namespace App\Library\SMS;

use App\Library\SMS\Providers\Kavenegar;
use App\Library\SMS\Providers\Sms20;
use App\Models\SmsConfig;
use Exception;

/**
 * Abstract class for SMS providers.
 *
 * This abstract class provides a blueprint for implementing SMS providers.
 * It includes common methods and properties shared by SMS providers.
 *
 * @package App\Library\SMS
 */
abstract class SmsProvider
{
    /**
     * List of available SMS provider classes.
     *
     * @var array
     */
    private static $providers = [
        "sms20",
        "kavenegar",
        // "newProvider",
    ];

    /**
     * Mapping of provider names to their human-readable labels.
     *
     * @var array
     */
    public static $providerNames = [
        "sms20" => "sms20",
        "kavenegar" => "کاوه نگار",
        // "newProvider" => "name to be shown of newProvider",
    ];

    /**
     * SMS data to be sent.
     *
     * @var SmsData
     */
    protected $smsData;

    /**
     * Get an instance of the appropriate SMS provider based on configuration.
     *
     * @return SmsProvider An instance of the SMS provider.
     *
     * @throws Exception If the configuration is missing or the provider is not supported.
     */
    public static function getProvider()
    {
        $config = SmsConfig::query()
            ->isActive()
            ->first();

        if (!$config)
            throw new Exception("!تنظمیات پیامک را تکمیل کنید", 422);

        $provider = $config->provider;
        if (!in_array($provider, self::$providers))
            throw new Exception("Wrong SMS Provider. This SMS provider ($provider) is not supported");

        switch ($provider) {
            case "sms20": {
                    return new Sms20();
                }
                case "kavenegar": {
                    return new Kavenegar();
                }
                // case "newProvider": {
                // return new newProvider();
                // }
        }
    }

    /**
     * Send an SMS request to the provider.
     *
     * @param SmsData $smsData the data of the sms that wanted to be sent
     *
     * @return void
     */
    abstract public function setData(SmsData $smsData);

    /**
     * Send an SMS request to the provider.
     *
     * @param string $action The action to perform.
     * @param array $data The data to send with the request.
     *
     * @return bool The response from the provider.
     */
    abstract protected function sendRequest($action, $data);

    /**
     * Check the configuration settings for the SMS provider.
     * 
     * @return bool whether the config is completed or not
     */
    abstract protected function checkConfig();

    /**
     * Check if the SMS sending was successful based on the provider's response.
     *
     * @param mixed $responseResult The result from the provider's response.
     *
     * @return bool True if the sending was successful; otherwise, false.
     */
    abstract function isSuccessful($responseResult);

    /**
     * Send a single or group SMS.
     * 
     * if the message and phone in SmsData is string send single sms.
     * if they are arrays send group SMS.
     */
    abstract function sendSms();

    /**
     * Send an SMS based on a template that is defined in providers platform.
     */
    abstract function sendTemplateSms();

    /**
     * this method will get the all sms that the provider has received
     * 
     * @return array the list of received sms
     */
    abstract function getReceivedSms();
}
