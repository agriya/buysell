<?php

// Copyright 2009, FedEx Corporation. All rights reserved.

define('TRANSACTIONS_LOG_FILE', 'fedextransactions.log');  // Transactions log file

/**
 *  Print SOAP request and response
 */
define('Newline',"<br />");

function printSuccess($client, $response) {
    echo '<h2>Transaction Successful</h2>';
    echo "\n";
    printRequestResponse($client);
}
function printRequestResponse($client){
	echo '<h2>Request</h2>' . "\n";
	echo '<pre>' . htmlspecialchars($client->__getLastRequest()). '</pre>';
	echo "\n";

	echo '<h2>Response</h2>'. "\n";
	echo '<pre>' . htmlspecialchars($client->__getLastResponse()). '</pre>';
	echo "\n";
}

/**
 *  Print SOAP Fault
 */
function printFault($exception, $client) {
    echo '<h2>Fault</h2>' . "<br>\n";
    echo "<b>Code:</b>{$exception->faultcode}<br>\n";
    echo "<b>String:</b>{$exception->faultstring}<br>\n";
    //writeToLog($client);
}

/**
 * SOAP request/response logging to a file
 */
function writeToLog($client){
if (!$logfile = fopen(TRANSACTIONS_LOG_FILE, "a"))
{
   error_func("Cannot open " . TRANSACTIONS_LOG_FILE . " file.\n", 0);
   #exit(1);
}

fwrite($logfile, sprintf("\r%s:- %s",date("D M j G:i:s T Y"), $client->__getLastRequest(). "\n\n" . $client->__getLastResponse()));
}

/**
 * This section provides a convenient place to setup many commonly used variables
 * needed for the php sample code to function.
 */
function getProperty($var){
	if($var == 'check') return true;
	if($var == 'shipaccount') return '510087925';
	if($var == 'billaccount') return '510087925';
	if($var == 'dutyaccount') return '510087925';
	if($var == 'accounttovalidate') return '510087925';
	if($var == 'meter') return '100235037';
	if($var == 'key') return 'rDf2M9RDgrEGUEeY';
	if($var == 'password') return '5S9To6Hj8Pk5RhQzXYcHIzXJj';


	if($var == 'shippingChargesPayment') return 'SENDER';
	if($var == 'internationalPaymentType') return 'SENDER';
	if($var == 'readydate') return '2014-07-30T08:44:07';
	if($var == 'readytime') return '12:00:00-05:00';
	if($var == 'closetime') return '20:00:00-05:00';
	if($var == 'closedate') return date("Y-m-d");
	if($var == 'dispatchdate') return date("Y-m-d", mktime(8, 0, 0, date("m")  , date("d")+1, date("Y")));
	if($var == 'dispatchtimestamp') return mktime(8, 0, 0, date("m")  , date("d")+1, date("Y"));
	if($var == 'shiptimestamp') return mktime(10, 0, 0, date("m"), date("d")+4, date("Y"));
	if($var == 'tag_readytimestamp') return mktime(10, 0, 0, date("m"), date("d")+4, date("Y"));
	if($var == 'tag_latesttimestamp') return mktime(15, 0, 0, date("m"), date("d")+4, date("Y"));
	if($var == 'dispatchlocationid') return 'XXX';
	if($var == 'dispatchconfirmationnumber') return 'XXX';
	if($var == 'trackingnumber') return 'XXX';
	if($var == 'trackaccount') return 'XXX';
	if($var == 'shipdate') return '2014-07-31';
	if($var == 'account') return 'XXX';
	if($var == 'phonenumber') return '9015551212';
	if($var == 'rth_trackingnumber') return 'XXX';
	if($var == 'rth_shipdate') return '2014-07-31';
	if($var == 'closedate') return '2014-08-03';
	if($var == 'hubid') return '5531';
	if($var == 'address1') return array('StreetLines' => array('10 Fed Ex Pkwy'),
                                          'City' => 'Washington',
                                          'StateOrProvinceCode' => '',
                                          'PostalCode' => '78701',
                                          'CountryCode' => 'US');
	if($var == 'address2') return array('StreetLines' => array('Alhydri North Nazimabad'),
                                          'City' => 'Karachi',
                                          'StateOrProvinceCode' => '',
                                          'PostalCode' => '74700',
                                          'CountryCode' => 'PK');
	if($var == 'addressCN') return array('StreetLines' => array('Address Line 1'),
                                          'City' => 'Chong Qing',
                                          'StateOrProvinceCode' => '',
                                          'PostalCode' => '100015',
                                          'CountryCode' => 'CN');
	if($var == 'addressPK') return array('StreetLines' => array('Kodambakkam'),
                                          'City' => 'Chennai',
                                          'StateOrProvinceCode' => '',
                                          'PostalCode' => '600001',
                                          'CountryCode' => 'IN');
	if($var == 'locatoraddress') return array(array('StreetLines'=>'240 Central Park S'),
										  'City'=>'Austin',
										  'StateOrProvinceCode'=>'TX',
										  'PostalCode'=>'78701',
										  'CountryCode'=>'US');
	if($var == 'holdcontactandlocation') return array('Contact'=>array('ContactId' => 'arnet',
										'PersonName' => 'Hold Contact',
										'Title' => 'Manager',
										'CompanyName' => 'FedEx Office Print & Ship Center',
										'PhoneNumber' => '7036890004'),
										'Address'=>array('StreetLines'=>array('13085 Worldgate Dr '),
										'City' =>'Herndon',
										'StateOrProvinceCode' => 'VA',
										'PostalCode' => '20170',
										'CountryCode' => 'US'));
	if($var == 'recipientcontact') return array('ContactId' => 'arnet',
										'PersonName' => 'Recipient Contact',
										'PhoneNumber' => '1234567890');
}
function setEndpoint($var){
	if($var == 'changeEndpoint') return false;
	if($var == 'endpoint') return 'XXX';
}

function printNotifications($notes){
	foreach($notes as $noteKey => $note){
		if(is_string($note)){
            echo $noteKey . ': ' . $note . Newline;
        }
        else{
        	printNotifications($note);
        }
	}
	echo Newline;
}

function getErrorMessage($notes,  $shipper, $recipient, $err_msg = ''){
	Log::info("============ note  =============");
			Log::info(	print_r($notes,1));
			Log::info(	print_r($shipper,1));
			Log::info(	print_r($recipient,1));
	foreach($notes as $noteKey => $note){
		if(is_string($note)){
			$err_msg = '';
			if($noteKey == 'Message')
			{
				Log::info(	print_r($note,1));
				if(stripos($note, 'Remote EJB') !== false)
					return $err_msg  .= $noteKey . ': Service not available. Please try again after some time.';
				elseif(stripos($note, 'Origin postal code missing or invalid.') !== false)
					return $err_msg  .= $noteKey.': Service not available from ('.$shipper['Address']['country_name'].'/'.$shipper['Address']['PostalCode'].') to ('.$recipient['Address']['country_name'].'/'.$recipient['Address']['PostalCode'].')';
				elseif(stripos($note, 'Destination postal code missing or invalid.') !== false)
					return $err_msg  .= $noteKey.': Service not available from ('.$shipper['Address']['country_name'].'/'.$shipper['Address']['PostalCode'].') to ('.$recipient['Address']['country_name'].'/'.$recipient['Address']['PostalCode'].')';
				else
					return $err_msg  .= $noteKey . ': ' . $note;
            }
        }
        else{
        	$err_msg .= getErrorMessage($note, $shipper, $recipient);
        }
	}
	$val = explode('Message:', $err_msg);
	$err_msg = array_unique($val);
	$err_msg = array_filter($err_msg);
	$err_msg = implode(',', $err_msg);
	$err_msg = trim($err_msg);

	Log::info("============ note  =============");

	return $err_msg;
}
function printError($client, $response){
    echo '<h2>Error returned in processing transaction</h2>';
	echo "\n";
	printNotifications($response -> Notifications);
    printRequestResponse($client, $response);
}
function trackDetails($details, $spacer){
	foreach($details as $key => $value){
    	echo '<tr>';
		if(is_array($value) || is_object($value)){
        	$newSpacer = $spacer. '&nbsp;&nbsp;&nbsp;&nbsp;';
    		echo '<td>'. $spacer . $key.'</td>';
    		trackDetails($value, $newSpacer);
    	}
        else echo '<td>'.$spacer. $key .'</td><td>'.$value.'</td>';
        echo '</tr>';
    }
}

?>