<?php
/**
 * IonoLicenseHandler
 *
 * @package
 * @author Ahsan
 * @copyright Copyright (c) 2014
 * @version $Id$
 * @access public
 */
class IonoLicenseHandler
	{
		public $home_url_site =  'http://stemp.agriya.in';
		public $home_url_port = 80;
		public $home_url_iono = '/upload/remote.php';
        public $user_defined_string = '3a08f7d2675c';
        public $comm_terminate = true;
		public $license_terminate = true;
        public $product_license_id = 0;
        public $product_id = 0;
        public $lang = array();

		/**
		 * IonoLicenseHandler::setCFGAppLicenseValues()
		 *
		 * @param mixed $CFG
		 * @return
		 */
		public function setConfigLicenseValues($appLicense = array())
			{
				if (!$appLicense) {
					$appLicense['home_url_site'] = Config::get('license/appLicense.home_url_site');//'http://stemp.agriya.in';
					$appLicense['home_url_port'] = Config::get('license/appLicense.home_url_port'); //80;
					$appLicense['home_url_iono'] = Config::get('license/appLicense.home_url_iono'); //'/upload/remote.php';
					$appLicense['user_defined_string'] = Config::get('license/appLicense.user_defined_string');//'3a08f7d2675c';
					$appLicense['product_license_id'] = Config::get('license/appLicense.product_license_id');
					$appLicense['product_id'] = Config::get('license/appLicense.product_id');
				}
				$this->home_url_site =  $appLicense['home_url_site'];
				$this->home_url_port = $appLicense['home_url_port'];
				$this->home_url_iono = $appLicense['home_url_iono'];
        		$this->user_defined_string = $appLicense['user_defined_string'];
        		$this->product_license_id = $appLicense['product_license_id'];
        		$this->product_id = $appLicense['product_id'];
			}

        /**
         * IonoLicenseHandler::setLangLicenseValues()
         *
         * @return
         */
        public function setLangLicenseValues($appLang = array())
	        {
	        	if (!$appLang) {
	        		$appLang['disabled'] = Lang::get('license/error.disabled');
	        		$appLang['suspended'] = Lang::get('license/error.suspended');
	        		$appLang['expired'] = Lang::get('license/error.expired');
	        		$appLang['exceeded'] = Lang::get('license/error.exceeded');
	        		$appLang['invalid_user'] = Lang::get('license/error.invalid_user');
	        		$appLang['invalid_code'] = Lang::get('license/error.invalid_code');
	        		$appLang['invalid_hash'] = Lang::get('license/error.invalid_hash');
	        		$appLang['wrong_product'] = Lang::get('license/error.wrong_product');
	        		$appLang['integrated_product_license_missing'] = Lang::get('license/error.integrated_product_license_missing');
	        		$appLang['integrated_product_id_missing'] = Lang::get('license/error.integrated_product_id_missing');
				}
				$this->lang = $appLang;
	        }

		/**
         * IonoLicenseHandler::ionLicenseHandler()
         *
         * @param mixed $license_key
         * @param mixed $request_type
         * @return
         */
        public function ionLicenseHandler($license_key, $request_type)
        	{
        		// Check that the $license_key provided is for this product
				if (!empty($this->product_id)) {
					$key_parts = explode('-', $license_key);
					if ((!isset($key_parts[2]) OR $key_parts[2] != $this->product_id)) {
						return $this->lang['wrong_product']; // 'wrong_product';
					}
				}
				// Check that the $license_key provided is for this license type of the product
				if (!empty($this->product_license_id)) {
					$key_parts = explode('-', $license_key);
					$product_license_id = array(substr(md5($this->product_license_id), 0, 8));

					if (!in_array($key_parts[4], $product_license_id)) {
						return $this->lang['wrong_product']; // 'wrong_product';
					}
				}
				$host =  $_SERVER['HTTP_HOST'];
				if(strcasecmp('www.', substr($_SERVER['HTTP_HOST'],0,4)) == 0) {
					$host = substr($_SERVER['HTTP_HOST'],4);
				}

				// Build request
				$request = 'remote=licensenew&type='.$request_type.'&license_key='.urlencode(base64_encode($license_key));
				$request .= '&host_ip='.urlencode(base64_encode($_SERVER['SERVER_ADDR'])).'&host_name='.urlencode(base64_encode($host));
				$request .= '&hash='.urlencode(base64_encode(md5($request)));

				$request = $this->home_url_site.$this->home_url_iono.'?'.$request;

				// New cURL resource
				$ch = curl_init();

				// Set options
				curl_setopt($ch, CURLOPT_URL, $request);
				curl_setopt($ch, CURLOPT_PORT, $this->home_url_port);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_USERAGENT, 'iono (www.olate.co.uk/iono)');

				// Execute
				$content = curl_exec($ch);

				// Close
				curl_close($ch);

				if (!$content) {
					return 'Unable to communicate with Iono';
				}

				// Split up the content
				$content = explode('-', $content);
				$status = $content[0];
				$hash = $content[1];

				if ($hash == md5($this->user_defined_string.$host)) {
					//return $status;
					switch ($status)
						{
							case 0: // Disabled
								$err_msg = $this->lang['disabled'];
								unset($home_url_site, $home_url_iono, $user_defined_string, $request, $header, $return, $fpointer, $content, $status, $hash);

								break;
							case 1: // Ok
								$err_msg = '';
								break;
							case 2: // Suspended
								$err_msg = $this->lang['suspended'];
								unset($home_url_site, $home_url_iono, $user_defined_string, $request, $header, $return, $fpointer, $content, $status, $hash);

								break;
							case 3: // Expired
								$err_msg = $this->lang['expired'];
								unset($home_url_site, $home_url_iono, $user_defined_string, $request, $header, $return, $fpointer, $content, $status, $hash);

								break;
							case 4: // Exceeded allowed installs
								$err_msg =  $this->lang['exceeded'];
								unset($home_url_site, $home_url_iono, $user_defined_string, $request, $header, $return, $fpointer, $content, $status, $hash);

								break;
							case 10: // Invalid user ID or license key
								$err_msg = $this->lang['invalid_user'];
								unset($home_url_site, $home_url_iono, $user_defined_string, $request, $header, $return, $fpointer, $content, $status, $hash);

								break;
							default: // Invalid status code
								$err_msg =  $this->lang['invalid_code'];
								unset($home_url_site, $home_url_iono, $user_defined_string, $request, $header, $return, $fpointer, $content, $status, $hash);

								break;
						}
					return $err_msg;

				} else {
					return $this->lang['invalid_hash'];
				}
			}
	}
?>