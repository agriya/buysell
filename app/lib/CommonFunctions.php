<?php

/**
 * populateConfigValues()
 *
 * @return
 */
function populateConfigValues()
{
	$cache_key = 'config_data_key';	
	if(Cache::has($cache_key) && Config::get('generalConfig.cache') == '1'){
		$data = HomeCUtil::cacheGet($cache_key);
	}else{
		$data = DB::table('config_data')->get();
		if(Config::get('generalConfig.cache') == '1')
			Cache::forever($cache_key, $data);
	}	
	foreach($data as $row)
	{
		$value = trim($row->config_value);
		$var   = trim($row->config_var);
		$file_name = '';
		if(trim($row->file_name) != '')
		{
			$file_name = $row->file_name.'.';
		}
		Config::set($file_name.$var, trim($value));
	}
}

/**
 * isValidLicense()
 *
 * @return
 */
function isValidLicense()
{
	$host = Request::server('HTTP_HOST');
	//To check whether the domain is agriya.com
	if(strstr($host, 'agriya.com')) {
	} else {
		if(strcasecmp('www.', substr($host ,0, 4)) == 0) {
			$host = substr($host, 4);
		}
		$str = Config::get('license/credentials.key').$host.'buysell';
		$valid_license = (Config::get('license/credentials.verified') == md5($str));
		if (!$valid_license) {  //Display error if Invalid license
			return true;
			//Send mail to team if visited following url.
			if (Request::is('product/add') || Request::is('purchases*') || Request::is('invoice*') || Request::is('my-sales*')) {

				$user['ip'] = Request::getClientIp();

				// Data to be used on the email view
				$data = array(
					'content'  => 'IP ('.$user['ip'].')<br /> URL: '.Request::root().' viewing site with invalid license key',
				);
				try {
					// Intimate support team
					Mail::send('emails.commonEmail', $data, function($m) use ($user)
					{
						$m->to('buysell@agriya.in', 'Buysell Agriya');
						//$m->cc('s.sridharan@agriya.in', 'Sridharan');
						$m->subject('['.Config::get('generalConfig.site_name').' - Invalid License] IP ('.$user['ip'].') viewing site with invalid license key');
					});
				} catch (Exception $e) {
					//return false
					CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
				}
			}
		}
	}
}

$______ADVERTISEMENT_ID = array();
function getAdvertisement($block)
{
	global $______ADVERTISEMENT_ID;
	
	$cache_key = 'banner_details_key';	
	if(Cache::has($cache_key) && Config::get('generalConfig.cache') == '1'){
		$banner_details = HomeCUtil::cacheGet($cache_key);
	}else{
		$banner_details =  Advertisement::whereRaw('block = ? AND status = ?', array($block, 'Active'))->where(DB::Raw("CURDATE()"), '>=', DB::Raw("date(start_date)"))
						->whereRaw('(((allowed_impressions != \'\' AND allowed_impressions != 0) AND (completed_impressions < allowed_impressions))
						OR ((allowed_impressions = \'\' OR allowed_impressions = 0) AND (end_date != \'0000-00-00 00:00:00\' AND date(end_date) >= CURDATE())))')->get();
		if(Config::get('generalConfig.cache') == '1')
			Cache::forever($cache_key, $banner_details);
	}
	$total_count = count($banner_details);
	if($total_count > 0)
	{
		$add_array = array();
		$need = rand(1, $total_count);
		$i = 1;
		foreach($banner_details AS $banner)
		{
			if($need == $i)
			{
				$class_name = 'topbanner-gadds';
				if($block == 'side-banner')
				{
					$class_name = 'sidebanner-gadds';
				}
				echo '<div class="'.$class_name.'">'.html_entity_decode($banner['source']).'</div>';
				$______ADVERTISEMENT_ID[$banner['add_id']] = $banner['add_id'];
				break;
			}
		 	$i++;
		}
	}
	return false;
}

function updateAdvertisementCount()
{
	global $______ADVERTISEMENT_ID;

	if(sizeof($______ADVERTISEMENT_ID))
	{
		Advertisement::whereIn('add_id', $______ADVERTISEMENT_ID)->increment('completed_impressions');

		$______ADVERTISEMENT_ID = array();
	}
}

//Call function to populate config values from database
populateConfigValues();

//Check for valid license key
isValidLicense();

?>