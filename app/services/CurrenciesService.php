<?php
class CurrenciesService
{
	public function getAvailableCurrenciesList($inputs = array()){
		$currency_list = Currencies::where('display_currency', '=', 'Yes');
		if(isset($inputs['currency_code']) && $inputs['currency_code']!='')
			$currency_list->where('currency_code', '=', $inputs['currency_code']);
		$currency_list = $currency_list->get()->toArray();
		return $currency_list;
	}
	public function addCurrency($currency_id = null)
	{
		if(is_null($currency_id) || $currency_id <=0)
			return false;
		try
		{
			Currencies::where('id', $currency_id)->update(array('display_currency' => 'Yes'));
			$array_multi_key = array('allowed_currencies_list_cache_key', 'fetch_currencies_details_cache_key', 'currencies_details_cache_key', 'selected_currency_code_cache_key');
			HomeCUtil::forgotMultiCacheKey($array_multi_key);
			return true;
		}
		catch(Exception $e)
		{
			return false;
		}
	}
	public function getNotAvailableCurrenciesList()
	{
		$currency_list = Currencies::where('display_currency', '=', 'No')->orderby('currency_code','Asc')->lists('currency_code','id');
		return $currency_list;
	}

	public function updateCurrency($currency_id, $inputs = array()){
		if(!is_array($inputs) || empty($inputs))
			return false;
		try{
			$collection = Currencies::where('id',$currency_id)->where('currency_code', '!=', 'USD')->update($inputs);
			$array_multi_key = array('allowed_currencies_list_cache_key', 'fetch_currencies_details_cache_key', 'currencies_details_cache_key', 'selected_currency_code_cache_key');
			HomeCUtil::forgotMultiCacheKey($array_multi_key);
			return true;
		}
		Catch(Exception $e){
			return false;
		}
	}

	public function bulkUpdateCurrencies($currency_ids, $data = array()){
		$update = false;
		if(!empty($currency_ids))
		{
			try
			{
				$qry = Currencies::whereIn('id', $currency_ids)->where('currency_code', '!=', 'USD')->update($data);
				$array_multi_key = array('allowed_currencies_list_cache_key', 'fetch_currencies_details_cache_key', 'currencies_details_cache_key', 'selected_currency_code_cache_key');
				HomeCUtil::forgotMultiCacheKey($array_multi_key);
				$update = true;
			}
			catch(exception $e)
			{
				$update = false;
			}
		}
		return $update;
	}
	public function bulkDeleteCurrencies($currency_ids = array()){
		if(!empty($currency_ids))
		{
			try{
				Currencies::whereIn('id', $currency_ids)->where('currency_code', '!=', 'USD')->update(array('display_currency'=>'No'));
				return true;
			}
			catch(Exception $e)
			{
				return false;
			}
		}
		else
			return false;
	}
}