<?php

// @added manikandan_133at10
class ListShopService extends ShopService
{
	public function setListShopsFilterArr()
	{
		$this->filter_arr['shop_name']= '';
	}

	public function getSrchVal($key)
	{
		return (isset($this->srch_arr[$key])) ? $this->srch_arr[$key] : "";
	}

	public function setListShopsSrchArr($input, $shop_obj)
	{
		$this->srch_arr['shop_name']= '';
		$this->srch_arr['shop_name']= (isset($input['shop_name']) && $input['shop_name'] != '') ? $input['shop_name'] : '';
		$shop_obj->setFilterShopName($this->srch_arr['shop_name']);
	}

	/*public function buildShopsListQuery()
	{
		$shop_obj = Products::initializeShops();
		$shop_list = $shop_obj->getShopList();
		$this->qry = ShopDetails::Select('shop_name', 'shop_details.url_slug', 'shop_details.id', 'shop_city', 'shop_state', 'shop_country', 'users.first_name', 'users.last_name', 'shop_details.user_id', 'users_shop_details.total_products')
									->join('users', function($join)
			                         {
			                             $join->on('users.id', '=', 'shop_details.user_id');
			                         });

		$this->qry->join('users_shop_details', function($join2)
		{
		 $join2->on('users_shop_details.user_id', '=', 'shop_details.user_id');
		});

		if($this->getSrchVal("shop_name") != "")
		{
			$shop_name = $this->getSrchVal("shop_name");//Input::get("shop_name"); //edited by mohamed_158at11
			$s_name_arr = explode(" ", $shop_name);
			if(count($s_name_arr) > 0)
			{
				foreach($s_name_arr AS $names)
				{
					$this->qry->WhereRaw("(shop_name LIKE '%".addslashes($names)."%')");
				}
			}
		}
		//$this->qry->Where('users.shop_status', 1);
		//$this->qry->Where('users.is_shop_owner', 'Yes');
		//$this->qry->groupBy('shop_details.id');
		//$this->qry->orderBy('users_shop_details.total_products', 'DESC');
		return $this->qry;
	}*/
}