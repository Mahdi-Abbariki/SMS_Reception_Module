<?php

namespace App\Library\SMS;

use Exception;

class SmsData
{
    /**
     * either message is present or template id
     * with message we should send a regular api call 
     * with template we must use tokens and send a predefined message in that provider
     */
    private $reception; //string or array
    private $message; //string or array
    private $templateId; //string
    private $tokens; //array of tokens used in template

    public function __construct($reception, $message = "", $templateId = "", $tokens = [])
    {
        if (!is_numeric($reception) && !is_array($reception))
            throw new Exception("reception ($reception) does not have a valid format");

        $this->reception = $reception;
        $this->message = $message;
        $this->templateId = $templateId;
        if (is_array($tokens))
            $this->tokens = $tokens;
        else
            $this->tokens = [];
    }

    public function getReception()
    {
        return $this->reception;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getTemplateId()
    {
        return $this->templateId;
    }

    public function getTokens()
    {
        return $this->tokens;
    }
}
