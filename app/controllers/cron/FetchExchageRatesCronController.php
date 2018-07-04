<?php
/**
 * Buy Sell
 *
 * PHP version 5
 *
 * @category   PHP
 * @package    buysell
 * @subpackage Core
 * @author     Agriya <info@agriya.com>
 * @copyright  2018 Agriya Infoway Private Ltd
 * @license    http://www.agriya.com/ Agriya Infoway Licence
 * @link       http://www.agriya.com
 */
class FetchExchageRatesCronController extends BaseController
{
	public function __construct()
	{
	}
	public function getIndex()
	{
		$exchange_rates = Products::setCurrencyDetails();
		if(count($exchange_rates) > 0)
		{
			$url = "http://openexchangerates.org/api/latest.json?app_id=13818df7f5b6418795509ca0f58df579";
			$result = CUtil::getContents($url);

   			$geocode = json_decode($result);
   			$rates_obj = $geocode->rates;

			foreach($exchange_rates as $exRate)
			{
				if($exRate['currency_code'] != "" && isset($rates_obj->$exRate['currency_code']))
				{
					$update_arr['currency_code'] = $exRate['currency_code'];
					$update_arr['exchange_rate'] = $rates_obj->$exRate['currency_code'];
					Products::updateCurrencyExchangeRate($update_arr);
				}
			}
		}
	}
}