<?php
require_once('fedex-common.php');
class ShippingCalculator  {
	// Defaults
	var $weight = 1.5;
	var $weight_unit = "kg";
	var $size_length = 25;
	var $size_width = 15;
	var $size_height = 10;
	var $size_unit = "in";
	var $debug = false; // Change to true to see XML sent and recieved

	// Batch (get all rates in one go, saves lots of time)
	var $batch_ups = false; // Currently Unavailable
	var $batch_usps = true;
	var $batch_fedex = false; // Currently Unavailable

	// Config (you can either set these here or send them in a config array when creating an instance of the class)
	var $services;
	var $from_zip;
	var $from_state;
	var $from_country;
	var $to_zip;
	var $to_stat;
	var $to_country;
	var $ups_access;
	var $ups_user;
	var $ups_pass;
	var $ups_account;
	var $usps_user;
	var $fedex_account;
	var $fedex_meter;

	// Results
	var $rates;

	// Setup Class with Config Options
	function ShippingCalculator($config) {
		if($config) {
			foreach($config as $k => $v) $this->$k = $v;
		}
	}
	function getShippingFromAddress(){
		$address = array();
		$address['StreetLines'] = '';
		$address['City'] = $this->from_city;
		$address['StateOrProvinceCode'] = $this->from_state;;
		$address['PostalCode'] = $this->from_zip;;
		$address['CountryCode'] = $this->from_country;;

		return $address;
	}

	// Calculate
	function calculate($company = NULL,$code = NULL) {

		$this->rates = NULL;
		$services = $this->services;
		if($company and $code) $services[$company][$code] = 1;
		$result = array();
		foreach($services as $company => $codes) {
			foreach($codes as $code => $name) {
				switch($company) {
					case "ems":
						$result = $this->calculate_ems($code);
						break;
					case "china_post_air_mail":
						$result = $this->calculate_china_post($code);
						break;
					case "ups":
						/*if($this->batch_ups == true) $batch[] = $code; // Batch calculation currently unavaiable
						else*/
						$result = $this->calculate_ups($code);
						break;
					/*case "usps":
						if($this->batch_usps == true) $batch[] = $code;
						else $this->rates[$company][$code] = $this->calculate_usps($code);
						break;*/
					case "fedex":
						/*if($this->batch_fedex == true) $batch[] = $code; // Batch calculation currently unavaiable
						else*/ $result = $this->calculate_fedex($code);
						break;

					case "DHL":
						/*if($this->batch_fedex == true) $batch[] = $code; // Batch calculation currently unavaiable
						else*/ $result = $this->calculate_dhl($code);
						//echo "<br>Final result <pre>";print_r($result);echo "</pre>";
						//Log::info('calculate_dhl result( '.print_r($result,1));
						break;


				}
			}
			// Batch Rates
			//if($company == "ups" and $this->batch_ups == true and count($batch) > 0) $this->rates[$company] = $this->calculate_ups($batch);
			//if($company == "usps" and $this->batch_usps == true and count($batch) > 0) $this->rates[$company] = $this->calculate_usps($batch);
			//if($company == "fedex" and $this->batch_fedex == true and count($batch) > 0) $this->rates[$company] = $this->calculate_fedex($batch);
		}
		//echo "<pre>"; print_r($this->rates); echo "</pre>";
		return $result;
	}

	// Calculate UPS
	function calculate_ups($code) {
		$url = "https://www.ups.com/ups.app/xml/Rate";
    	$data = '<?xml version="1.0"?>
<AccessRequest xml:lang="en-US">
	<AccessLicenseNumber>'.$this->ups_access.'</AccessLicenseNumber>
	<UserId>'.$this->ups_user.'</UserId>
	<Password>'.$this->ups_pass.'</Password>
</AccessRequest>
<?xml version="1.0"?>
<RatingServiceSelectionRequest xml:lang="en-US">
	<Request>
		<TransactionReference>
			<CustomerContext>Bare Bones Rate Request</CustomerContext>
			<XpciVersion>1.0001</XpciVersion>
		</TransactionReference>
		<RequestAction>Rate</RequestAction>
		<RequestOption>Rate</RequestOption>
	</Request>
	<PickupType>
		<Code>01</Code>
	</PickupType>
	<Shipment>
		<Shipper>
			<Address>
				<PostalCode>'.$this->from_zip.'</PostalCode>
				<CountryCode>'.$this->from_country.'</CountryCode>
			</Address>
		<ShipperNumber>'.$this->ups_account.'</ShipperNumber>
		</Shipper>
		<ShipTo>
			<Address>
				<PostalCode>'.$this->to_zip.'</PostalCode>
				<CountryCode>'.$this->to_country.'</CountryCode>
			<ResidentialAddressIndicator/>
			</Address>
		</ShipTo>
		<ShipFrom>
			<Address>
				<PostalCode>'.$this->from_zip.'</PostalCode>
				<CountryCode>'.$this->from_country.'</CountryCode>
			</Address>
		</ShipFrom>
		<Service>
			<Code>'.$code.'</Code>
		</Service>
		<Package>
			<PackagingType>
				<Code>02</Code>
			</PackagingType>
			<Dimensions>
				<UnitOfMeasurement>
					<Code>IN</Code>
				</UnitOfMeasurement>
				<Length>'.($this->size_unit != "in" ? $this->convert_sze($this->size_length,$this->size_unit,"in") : $this->size_length).'</Length>
				<Width>'.($this->size_unit != "in" ? $this->convert_sze($this->size_width,$this->size_unit,"in") : $this->size_width).'</Width>
				<Height>'.($this->size_unit != "in" ? $this->convert_sze($this->size_height,$this->size_unit,"in") : $this->size_height).'</Height>
			</Dimensions>
			<PackageWeight>
				<UnitOfMeasurement>
					<Code>LBS</Code>
				</UnitOfMeasurement>
				<Weight>'.($this->weight_unit != "lb" ? $this->convert_weight($this->weight,$this->weight_unit,"lb") : $this->weight).'</Weight>
			</PackageWeight>
		</Package>
	</Shipment>
</RatingServiceSelectionRequest>';

		// Curl
		$results = $this->curl($url,$data);

		// Debug
		if($this->debug == true) {
			print "<xmp>".$data."</xmp><br />";
			print "<xmp>".$results."</xmp><br />";
		}

		// Match Rate
		preg_match('/<MonetaryValue>(.*?)<\/MonetaryValue>/',$results,$rate);

		$rate = isset($rate[1])?$rate[1]:'';
		$ret_arr = array('return' => true, 'amount' => $rate);
		return $ret_arr;
	}

	// Calculate USPS
	function calculate_usps($code) {
		// Weight (in lbs)
		if($this->weight_unit != 'lb') $weight = $this->convert_weight($weight,$this->weight_unit,'lb');
		else $weight = $this->weight;
		// Split into Lbs and Ozs
		$lbs = floor($weight);
		$ozs = ($weight - $lbs)  * 16;
		if($lbs == 0 and $ozs < 1) $ozs = 1;
		// Code(s)
		$array = true;
		if(!is_array($code)) {
			$array = false;
			$code = array($code);
		}

		$url = "http://Production.ShippingAPIs.com/ShippingAPI.dll";
		$data = 'API=RateV2&XML=<RateV2Request USERID="'.$this->usps_user.'">';
		foreach($code as $x => $c) $data .= '<Package ID="'.$x.'"><Service>'.$c.'</Service><ZipOrigination>'.$this->from_zip.'</ZipOrigination><ZipDestination>'.$this->to_zip.'</ZipDestination><Pounds>'.$lbs.'</Pounds><Ounces>'.$ozs.'</Ounces><Size>REGULAR</Size><Machinable>TRUE</Machinable></Package>';
		$data .= '</RateV2Request>';

		// Curl
		$results = $this->curl($url,$data);

		// Debug
		if($this->debug == true) {
			print "<xmp>".$data."</xmp><br />";
			print "<xmp>".$results."</xmp><br />";
		}

		// Match Rate(s)
		preg_match_all('/<Package ID="([0-9]{1,3})">(.+?)<\/Package>/',$results,$packages);
		foreach($packages[1] as $x => $package) {
			preg_match('/<Rate>(.+?)<\/Rate>/',$packages[2][$x],$rate);
			$rates[$code[$package]] = $rate[1];
		}
		if($array == true) return $rates;
		else return $rate[1];
	}

	// Calculate FedEX
	function calculate_fedex($code)
	{

		$newline = "<br />";
		//The WSDL is not included with the sample code.
		//Please include and reference in $path_to_wsdl variable.
		$path_to_wsdl = "RateService_v9.wsdl";

		ini_set("soap.wsdl_cache_enabled", "0");

		$client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information
		$request['WebAuthenticationDetail'] = array('UserCredential' =>
		                                      array('Key' => getProperty('key'), 'Password' => getProperty('password')));
		$request['ClientDetail'] = array('AccountNumber' => getProperty('shipaccount'), 'MeterNumber' => getProperty('meter'));
		$request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Rate Request v9 using PHP ***');
		$request['Version'] = array('ServiceId' => 'crs', 'Major' => '9', 'Intermediate' => '0', 'Minor' => '0');
		$request['ReturnTransitAndCommit'] = true;
		$request['RequestedShipment']['DropoffType'] = 'REGULAR_PICKUP'; // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
		$request['RequestedShipment']['ShipTimestamp'] = date('c');
		$request['RequestedShipment']['ServiceType'] = $code;//'INTERNATIONAL_PRIORITY_FREIGHT'; // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
		$request['RequestedShipment']['PackagingType'] = 'YOUR_PACKAGING'; // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
		$request['RequestedShipment']['TotalInsuredValue']=array('Ammount'=>100,'Currency'=>'USD');
		//if($this->from_country == 'CN')
		//	$request['RequestedShipment']['Shipper'] = array('Address' => getProperty('addressCN'));
		//else
		//	$request['RequestedShipment']['Shipper'] = array('Address' => getProperty('addressPK'));
		$request['RequestedShipment']['Shipper'] = $shipper =  array('Address' => $this->getShippingFromAddress());
		//$request['RequestedShipment']['Recipient'] = array('Address' => getProperty('addressIN'));

		$request['RequestedShipment']['Recipient'] = $recipient =  array('Address' => array(
					'StreetLines' => array(''),
		            'City' => $this->to_city,
		            'StateOrProvinceCode' => '',
		            'PostalCode' => $this->to_zip,
		            'CountryCode' => $this->to_country,
		            'Residential' => false)
				);

		//$request['RequestedShipment']['Recipient'] = array(
		//		'Address' => array(
		//			'StreetLines' => array('Address Line 1'),
		//            'City' => 'Chennai',
		//            'StateOrProvinceCode' => '',
		//            'PostalCode' => '600032',
		//            'CountryCode' => 'IN',
		//            'Residential' => false)
		//		);
		//$request['RequestedShipment']['Recipient'] = array(
		//		'Address' => array(
		//			'StreetLines' => array('Address Line 1'),
		//            'City' => 'Richmond',
		//            'StateOrProvinceCode' => 'BC',
		//            'PostalCode' => 'V7C4V4',
		//            'CountryCode' => 'CA',
		//            'Residential' => false)
		//		);

		$request['RequestedShipment']['ShippingChargesPayment'] = array('PaymentType' => 'SENDER',
		                                                        'Payor' => array('AccountNumber' => getProperty('billaccount'),
		                                                                     'CountryCode' => 'US'));
		$request['RequestedShipment']['RateRequestTypes'] = 'ACCOUNT';
		$request['RequestedShipment']['RateRequestTypes'] = 'LIST';
		$request['RequestedShipment']['PackageCount'] = '1';
		$request['RequestedShipment']['PackageDetail'] = 'INDIVIDUAL_PACKAGES';  //  Or PACKAGE_SUMMARY
		$request['RequestedShipment']['RequestedPackageLineItems'] = array('0' => array('Weight' => array('Value' => $this->weight,//69.0,
		                                                                                    'Units' => 'KG'),
		                                                                                    'Dimensions' => array('Length' => $this->size_length,//10,
		                                                                                        'Width' => $this->size_width,
		                                                                                        'Height' => $this->size_height,
		                                                                                        'Units' => $this->size_units //'CM'
																								)),
		                                                                   		);

//		Log::info("============== fedex request ===================");
//		Log::info(print_r($request, 1));
//		Log::info("============== fedex request ===================");
		//if from country and to country are same then return the error
		//This validation is done with consideration of all service are internation service.
		//If any domestinc service included then use this section inside else loop of result
		//mohamed_158at11
		if(isset($request['RequestedShipment']['Shipper']['Address']['CountryCode']) && $request['RequestedShipment']['Shipper']['Address']['CountryCode']!=''
			&& isset($request['RequestedShipment']['Recipient']['Address']['CountryCode']) && $request['RequestedShipment']['Recipient']['Address']['CountryCode']!=''
			&& ($request['RequestedShipment']['Shipper']['Address']['CountryCode'] ==$request['RequestedShipment']['Recipient']['Address']['CountryCode'])
		)
		{
			$ret_arr = array('return' => false, 'message' => 'Service allowed only for international countries');
			return $ret_arr;
		}

		try
		{
			if(setEndpoint('changeEndpoint'))
			{
				$newLocation = $client->__setLocation(setEndpoint('endpoint'));
			}

			$response = $client->getRates($request);

			//echo "<pre>";print_r($response);echo "</pre>";
			$ret_arr = array();
		    if ($response->HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR')
		    {
		    	$rateReply = $response->RateReplyDetails;
		    	$amount = number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",");
		    	$amount = CUtil::convertAmountToCurrency($amount, 'USD', '', false);
		    	$ret_arr = array('return' => true, 'amount' => $amount);
			}
		    else
		    {
		    	$shipper['Address']['country_name'] = 	Products::getCountryNameByIso2CountryCode($shipper['Address']['CountryCode']);
		    	$recipient['Address']['country_name'] = 	Products::getCountryNameByIso2CountryCode($recipient['Address']['CountryCode']);
		    	$message = getErrorMessage($response->Notifications, $shipper, $recipient);//printError($client, $response);
				$ret_arr = array('return' => false, 'message' => $message);

		    }

			return $ret_arr;

		} catch (SoapFault $exception) {
		   //printFault($exception, $client);
		   $ret_arr = array('return' => false, 'message' => $exception->getMessage());
		   return $ret_arr;
		}/*


		$url = "https://gatewaybeta.fedex.com/GatewayDC";
		$data = '<?xml version="1.0" encoding="UTF-8" ?>
<FDXRateRequest xmlns:api="http://www.fedex.com/fsmapi" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="FDXRateRequest.xsd">
	<RequestHeader>
		<CustomerTransactionIdentifier>Express Rate</CustomerTransactionIdentifier>
		<AccountNumber>'.$this->fedex_account.'</AccountNumber>
		<MeterNumber>'.$this->fedex_meter.'</MeterNumber>
		<CarrierCode>'.(in_array($code,array('FEDEXGROUND','GROUNDHOMEDELIVERY')) ? 'FDXG' : 'FDXE').'</CarrierCode>
	</RequestHeader>
	<DropoffType>REGULARPICKUP</DropoffType>
	<Service>'.$code.'</Service>
	<Packaging>YOURPACKAGING</Packaging>
	<WeightUnits>LBS</WeightUnits>
	<Weight>'.number_format(($this->weight_unit != 'lb' ? $this->convert_weight($this->weight,$this->weight_unit,'lb') : $this->weight), 1, '.', '').'</Weight>
	<OriginAddress>
		<StateOrProvinceCode>'.$this->from_state.'</StateOrProvinceCode>
		<PostalCode>'.$this->from_zip.'</PostalCode>
		<CountryCode>'.$this->from_country.'</CountryCode>
	</OriginAddress>
	<DestinationAddress>
		<StateOrProvinceCode>'.$this->to_state.'</StateOrProvinceCode>
		<PostalCode>'.$this->to_zip.'</PostalCode>
		<CountryCode>'.$this->to_country.'</CountryCode>
	</DestinationAddress>
	<Payment>
		<PayorType>SENDER</PayorType>
	</Payment>
	<PackageCount>1</PackageCount>
</FDXRateRequest>';

		// Curl
		$results = $this->curl($url,$data);

		// Debug
		if($this->debug == true) {
			print "<xmp>".$data."</xmp><br />";
			print "<xmp>".$results."</xmp><br />";
		}

		// Match Rate
		preg_match('/<NetCharge>(.*?)<\/NetCharge>/',$results,$rate);

		return isset($rate[1])?$rate[1]:'';*/
	}

	// Calculate EMS
	function calculate_ems($code) {
		// Code(s)
		$rate = '';
		$ret_arr = array();
		$to_country_det = Products::getCountryDetailsByCountryId($this->to_country_id);
		$country_name_chinese = (isset($to_country_det['country_name_chinese'])) ? $to_country_det['country_name_chinese'] : '';

		if($country_name_chinese == '') {
			$ret_arr = array('return' => false, 'message' => Lang::get('shippingTemplates.error_service_not_allowed'));
			return $ret_arr;
		}

		$url = 'http://www.ems.com.cn/ems/tool/internet/postageen';
		if($code == 'CHINA_EXPRESS_EMS_RATE')
			$url = 'http://www.ems.com.cn/ems/tool/internet/postageenzs';
		$fields = array(
				'addressto' => urlencode($country_name_chinese),
				'weight' => urlencode($this->weight),
				'goodsType' => urlencode('goods')
				);

		//url-ify the data for the POST
		$fields_string = '';
		foreach($fields as $key=>$value) {
			$fields_string .= $key.'='.$value.'&';
		}
		rtrim($fields_string, '&');
		//open connection
		$ch = curl_init();

		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,CURLOPT_POST, count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

		//execute post
		$result = curl_exec($ch);
		$json_data = json_decode($result, true);
		if(isset($json_data['model']['message'])) {
			$rate = $json_data['model']['message'];
		}
		if (is_numeric($rate)) {
			//Convert CYN to USD
			$rate = CUtil::convertBaseCurrencyToUSD($rate, 'CNY');
			$ret_arr = array('return' => true, 'amount' => $rate);
		}
		else {
			$ret_arr = array('return' => false, 'message' => 'No related charges.');
		}
		return $ret_arr;

		//close connection
		curl_close($ch);
	}

	//China post
	public function calculate_china_post($service) {
		$amount = 0;
		$message = '';
		$weight = $this->weight;
		$china_post_group = $this->china_post_group;
		if($china_post_group > 0) {
			$category = ($service == 'SMALL_PACKET_RATE') ? 'small_packet' : 'm_bag';
			$amount_det = ChinaPostGroupPrice::whereRaw('china_post_category = ? AND china_post_group = ?', array($category, $china_post_group))->first();
			if(count($amount_det) > 0)
			{
				$weight_in_grams = $weight * 1000; //Convert product weight from kg to grams
				if($weight_in_grams <= $amount_det->upto_weight) {
					$amount = $amount_det->upto_weight_price;
				}
				else {
					$amount = $amount_det->upto_weight_price;
					$additional_weight = $weight_in_grams - $amount_det->upto_weight;
					$additional_price = ($additional_weight / $amount_det->additional_weight) * $amount_det->additional_weight_price;
					$amount = $amount + $additional_price;
				}
			}
			else {
				$message = 'Message: Serivice not available';
			}
		}
		else {
			$message = 'Message: Serivice not available';
		}

		if($amount > 0) {
			//Convert CYN to USD
			$amount = CUtil::convertBaseCurrencyToUSD($amount, 'CNY');
			$ret_arr = array('return' => true, 'amount' => $amount);
		}
		else
		{
			$ret_arr = array('return' => false, 'message' => $message);
		}
		return $ret_arr;
	}

	//DHL
	public function calculate_dhl($service)
	{

		$ret_arr = array();
		//get the from and to country. and check whether the service available for the country. if available return the zone
		$pref = 'dhl_china_';
		$stypepref = 'china_';
		if($this->from_country == 'PK')
		{
			$pref = 'dhl_pak_';
			$stypepref = 'pak_';
		}
		if($this->from_country != 'PK' && $service == 'express_easy')
		{
			return array('return' => false, 'message' => 'Service not available');
		}

		$service_name = $pref.$service; //dhl_china_express_9
		$country_id = $this->to_country_id;
		$zone_id_name = $pref.'zone_id';
		//echo "<br>country_id: ".$country_id;
		$zone_service = DhlZoneService::where('country_id',$country_id)->first();
		$zone_id = $zone_service->$zone_id_name;
		$service_available = $zone_service->$service_name;
		$zone_name = 'zone'.$zone_id;
		$amount = 0;
		$message = '';

		if($zone_id > 0 && $service_available > 0)
		{
			$service_type = $stypepref.$service;
			$weight = $this->weight;
			$amount_det = DhlZonePrice::where('service_type',$service_type)->where('goods_weight', '>=', $weight)->first();
			if(count($amount_det) > 0)
			{
				$amount = $amount_det->$zone_name;
			}
			else
			{

				$amount_add_det = DhlZoneAdditionalPrice::where('service_type',$service_type)->whereRaw('goods_weight_from <= ? and (goods_weight_to >= ? OR goods_weight_to = ?)',array($weight,$weight,0))->first();
				if(count($amount_add_det) > 0)
				{
					$amount = $amount_add_det->$zone_name;

					if($this->from_country == 'CN')
						$amount = $weight * $amount;
					else
					{
						$max_allowed_weight_det = DhlZonePrice::where('service_type',$service_type)->orderBy('goods_weight', 'desc')->first();
						$max_allowed_weight = $max_allowed_weight_det->goods_weight;
						$additional_weight = $weight-$max_allowed_weight;
						$price_units = ($additional_weight * 1000)/500;
						$additional_amount = $price_units * $amount;
						$amount = $additional_amount+$max_allowed_weight_det->$zone_name;
					}
				}
				else
				{
					$max_allowed_weight_det = DhlZoneAdditionalPrice::where('service_type',$service_type)->orderBy('goods_weight_from', 'desc')->first();
					if(count($max_allowed_weight_det) > 0)
					{
						$max_weight = $max_allowed_weight_det->goods_weight_to;
						if($max_weight > 0)
						{
							$message = 'Maximum weight allowed is '.$max_weight. ' kg';

						}
						else
						{
							$message = 'Weight specified is not allowed';
						}
					}
					else
					{
						$max_allowed_weight_det = DhlZonePrice::where('service_type',$service_type)->orderBy('goods_weight', 'desc')->first();
						if(count($max_allowed_weight_det) > 0)
						{
							$max_weight = $max_allowed_weight_det->goods_weight;
							$message = 'Maximum weight allowed is '.$max_weight.' kg';
						}
						else
							$message = 'Weight specified is not allowed';
					}

				}
			}
		}
		else
		{
			$message = 'Serivice not available';
		}
		if($amount > 0)
		{
			if($this->from_country == 'CN')
				$amount = CUtil::convertBaseCurrencyToUSD($amount, 'CNY');
			$ret_arr = array('return' => true, 'amount' => $amount);
		}
		else
		{
			$ret_arr = array('return' => false, 'message' => $message);
		}
		//echo "<pre>";print_r($zone_service);echo "</pre>";exit;
		//echo "service_name: ".$service_name;exit;
		/*
		$url = 'http://dct.dhl.com/data/quotation/?wgtUom=kg&dimUom='.$this->size_units.'&noPce=1&wgt0='.$this->weight.'&w0='.$this->size_width.'&l0='.$this->size_length.'&h0='.$this->size_height.'&shpDate='.date('Y-m-d').'&orgCtry='.$this->from_country.'&orgCity='.$this->from_city.'&orgSub=&orgZip='.$this->from_zip.'&dstCtry='.$this->to_country.'&dstCity='.$this->to_city.'&dstSub=&dstZip='.$this->to_zip;
		//$contents = file_get_contents($url);
		//Log::info('calculate_dhl( time url==>'.$url);
		//Log::info('calculate_dhl( time before==>'.time());

		$curl_handle=curl_init();
		curl_setopt($curl_handle, CURLOPT_URL,$url);
		//curl_setopt($curl_handle, CURLOPT_HEADER, 1);
		//curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($curl_handle, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($curl_handle, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($curl_handle, CURLOPT_FRESH_CONNECT, TRUE);
		$contents = curl_exec($curl_handle);
		curl_close($curl_handle);
		Log::info('calculate_dhl( time after==>'.time());
		Log::info('calculate_dhl( time content==>'.print_r($contents,1));

//		$curl_handle=curl_init();
//		curl_setopt($curl_handle, CURLOPT_URL,$url);
//		//curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
//		curl_setopt($curl_handle, CURLOPT_TIMEOUT, 10);
//		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
//		curl_setopt($curl_handle, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
//		$contents = curl_exec($curl_handle);
//		curl_close($curl_handle);
		//exit;



		//Log::info('calculate_dhl( url==>'.$url);
		if($contents && $contents!='')
		{
			//$xml = simplexml_load_string($contents);
			$xml = new SimpleXMLElement($contents);
			Log::info('calculate_dhl( xml ==>'.print_r($xml,1));
			/*if(isset($xml->errorMessage))
			{
				$ret_arr['return'] = false;
				$ret_arr['message'] = $xml->errorMessage;
			}
			else
			{

				$res_arr = array();
				$ret_arr['return'] = true;
				$ret_arr['result'] = array();
				$i=0;
				foreach($xml->quotationList->quotation as $quotation)
				{
					//Log::info('calculate_dhl( xml coming inside ==>'.print_r($quotation,1));
					$res_arr[$i]['service_name'] = $quotation->prodNm;
					$amount = $quotation->quotationServiceList->quotationService->totAmt;
					if($this->from_country == 'CN')
					{
						$amount = str_replace('CNY','',$amount);
						$amount = str_replace(',','',$amount);
						$dollor_amount = CUtil::convertBaseCurrencyToUSD($amount, 'CNY');
					}
					else
					{
						$amount = str_replace('PKR','',$amount);
						$amount = str_replace(',','',$amount);
						$dollor_amount = CUtil::convertBaseCurrencyToUSD($amount, 'PKR');
					}
					$res_arr[$i]['org_amount'] = $amount;
					$res_arr[$i]['amount'] = $dollor_amount;
					$i++;
				}
				$ret_arr['result'] = $res_arr;
			//}
		}
		else
		{
			$ret_arr['return'] = false;
			$ret_arr['message'] = 'Problem in connecting host';
		}*/
		return $ret_arr;
	}

	// Curl
	function curl($url,$data = NULL) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		if($data) {
			curl_setopt($ch, CURLOPT_POST,1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
		}
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		$contents = curl_exec ($ch);

		return $contents;

		curl_close ($ch);
	}

	// Convert Weight
	function convert_weight($weight,$old_unit,$new_unit) {
		$units['oz'] = 1;
		$units['lb'] = 0.0625;
		$units['gram'] = 28.3495231;
		$units['kg'] = 0.0283495231;

		// Convert to Ounces (if not already)
		if($old_unit != "oz") $weight = $weight / $units[$old_unit];

		// Convert to New Unit
		$weight = $weight * $units[$new_unit];

		// Minimum Weight
		if($weight < .1) $weight = .1;

		// Return New Weight
		return round($weight,2);
	}

	// Convert Size
	function convert_size($size,$old_unit,$new_unit) {
		$units['in'] = 1;
		$units['cm'] = 2.54;
		$units['feet'] = 0.083333;

		// Convert to Inches (if not already)
		if($old_unit != "in") $size = $size / $units[$old_unit];

		// Convert to New Unit
		$size = $size * $units[$new_unit];

		// Minimum Size
		if($size < .1) $size = .1;

		// Return New Size
		return round($size,2);
	}

	// Set Value
	function set_value($k,$v) {
		$this->$k = $v;
	}
}
?>