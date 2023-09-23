<?php
ini_set("soap.wsdl_cache_enabled", "0"); // Disable WSDL cache for development

// Specify the WSDL URL
$wsdl_url = 'http://www.5m5.ir/webservice/soap/smsService.php?wsdl';

// Create a SOAP client
$client = new SoapClient($wsdl_url, array('trace' => 1));

// Prepare parameters as an associative array
$params = array(
    'date' => '1695443731',
);

// Invoke the SOAP function with the parameters array
try {
    $result = $client->__soapCall('convert_unixtime_to_jalali', array($params));

    // Handle the response here
    if ($client->fault) {
        echo "SOAP Fault: " . $client->faultstring;
        echo "\nLast Request:\n" . htmlentities($client->__getLastRequest()) . "\n";
        echo "\nLast Response:\n" . htmlentities($client->__getLastResponse()) . "\n";
    } else {
        // Process the result
        $smsCredit = (int) $result;
        echo "SMS Credit: " . $smsCredit;
    }

} catch (SoapFault $e) {
    // Handle any SOAP exceptions
    echo "SOAP Exception: " . $e->getMessage();
}
?>