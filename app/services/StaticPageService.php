<?php
class StaticPageService
{
	public function getLists($return_type='paginate', $limit=10){
		$lists_details = StaticPages::orderby('id','desc');
		if($return_type=='paginate')
			$lists_details = $lists_details->paginate($limit);
		else
			$lists_details = $lists_details->get();
		return $lists_details;
	}

	public function getPageDetails($page_id){
		$page_details = StaticPages::where('id',$page_id)->first();
		return $page_details;
	}
	public function addStaticPage($data = array())
	{
		$staticPages = new StaticPages();
		$valid_inputs = $staticPages->getTableFields();
		$valid_inputs = array_fill_keys($valid_inputs, '');
		//$valid_inputs = array_combine($valid_inputs,$valid_inputs);

		$subquery = DB::table((new StaticPages)->getTable() . ' as page_alias')->selectRaw('page_display_order + 1 as pdo')
                      ->orderBy('page_display_order', 'desc')
                      ->take(1)->first();

		$page_display_order = 1;
		if(count($subquery) > 0)
			$page_display_order = $subquery->pdo;

		if(isset($data['page_name']) && $data['page_name'] != '') {
			$pg_name_slug = CUtil::createSlug($data['page_name']);
			$data['url_slug'] = $pg_name_slug;
		}
		$final_arr = array_intersect_key($data,$valid_inputs);
		$final_arr['page_display_order'] = $page_display_order;

		$page_id = $staticPages->addNew($final_arr);//exit;
		return $page_id;

		//Old try
		//$max = StaticPages::max('page_display_order');
		//$final_arr['page_display_order'] = $max+1;
		//$page_id = $staticPages->addNew($final_arr);


		//Ole try 2
		//		$subquery = DB::table((new StaticPages)->getTable() . ' as page_alias')->selectRaw('page_display_order + 1 as pdo')
		//                      ->orderBy('page_display_order', 'desc')
		//                      ->take(1)->toSql();
		//		//echo "<br>subquery: ".$subquery;
		//		$page_id = StaticPages::create([
		//		    'page_name' => 'testingfinal',
		//		    'title' => 'testingfinal',
		//		    'page_display_order' => DB::raw("($subquery)"),
		//		]);
		//		echo "<br>page_id: ".$page_id;exit;
	}
	public function updateStaticPage($data = array(), $page_id = null)
	{
		if(is_null($page_id) || empty($data))
			return false;

		$staticPages = new StaticPages();
		$valid_inputs = $staticPages->getTableFields();
		$valid_inputs = array_fill_keys($valid_inputs,'');

		if(isset($data['page_name']) && $data['page_name'] != '') {
			$pg_name_slug = CUtil::createSlug($data['page_name']);
			$data['url_slug'] = $pg_name_slug;
		}
		$final_arr = array_intersect_key($data,$valid_inputs);
		//echo "<pre>";print_r($final_arr);echo "</pre>";
		//echo "<pre>";print_r($page_id);echo "</pre>";

		if(is_array($page_id) && !empty($page_id))
		{
			try{
				StaticPages::whereIn('id',$page_id)->update($final_arr);
				$array_multi_key = array('footer_static_page_links_paginate_cache_key', 'footer_static_page_links_getarray_cache_key', 'footer_static_page_links_cache_key');
				HomeCUtil::forgotMultiCacheKey($array_multi_key);
				$update = true;
			}
			catch(Exception $e)
			{
				Log::info('Error for deleting static pages = '.$e->getMessage());
				$update = false;
			}
		}
		elseif($page_id > 0)
		{
			try{
				StaticPages::where('id',$page_id)->update($final_arr);
				$array_multi_key = array('footer_static_page_links_paginate_cache_key', 'footer_static_page_links_getarray_cache_key', 'footer_static_page_links_cache_key');
				HomeCUtil::forgotMultiCacheKey($array_multi_key);
				$update = true;
			}
			catch(Exception $e)
			{
				Log::info('Error for deleting static pages = '.$e->getMessage());
				$update = false;
			}
		}
		else
		{
			Log::info('No id selected to delete the static pages');
			$update = false;
		}
		return $update;
	}
	public function deleteStaticPage($page_id){
		if(is_array($page_id) && !empty($page_id))
			$deleted = StaticPages::whereIn('id',$page_id)->delete();
		elseif($page_id > 0)
			$deleted = StaticPages::where('id',$page_id)->delete();
		else
			$deleted = false;
		if($deleted)
		{
			$array_multi_key = array('footer_static_page_links_paginate_cache_key', 'footer_static_page_links_getarray_cache_key', 'footer_static_page_links_cache_key');
			HomeCUtil::forgotMultiCacheKey($array_multi_key);
		}		
		return $deleted;
	}

	public function getSellPageStaticContent(){

		$sell_page_content = SellPageStaticContent::First()->toArray();
		return $sell_page_content;
	}
	public function updateSellPageStaticContent($id, $data = array())
	{
		$sellPageStaticContent = new SellPageStaticContent();
		$valid_inputs = $sellPageStaticContent->getTableFields();
		$valid_inputs = array_fill_keys($valid_inputs, '');

		$final_valid_inputs = array_intersect_key($data,$valid_inputs);
		try
		{
			$update = $sellPageStaticContent->where('id',$id)->update($final_valid_inputs);
			return true;
		}
		Catch(Exception $e)
		{
			return false;
		}
	}
	public function addSellPageStaticContent($data = array()){
		$sellPageStaticContent = new SellPageStaticContent();
		$id = $sellPageStaticContent->addNew($data);
	}
	public function replaceSiteName($item)
	{
		$replace_arr = array('VAR_SITE_NAME' => Config::get('generalConfig.site_name'));
		$item = str_replace(array_keys($replace_arr), array_values($replace_arr), $item);
		return $item;
	}

	public function getFooterPages($return_type='paginate', $limit=10){
		if($return_type == 'paginate') {			
			$cache_key = 'footer_static_page_links_paginate_cache_key';
			if (($lists_details = HomeCUtil::cacheGet($cache_key)) === NULL) {
				$lists_details = StaticPages::WhereRaw('display_in_footer = ? AND status = ?', array('Yes', 'Active'))->orderby('page_display_order','ASC')->paginate($limit);
				HomeCUtil::cachePut($cache_key, $lists_details);
			}
		}
		else if($return_type == 'getarray') {
			$cache_key = 'footer_static_page_links_getarray_cache_key';
			if (($lists_details = HomeCUtil::cacheGet($cache_key)) === NULL) {
				$lists_details = StaticPages::WhereRaw('display_in_footer = ? AND status = ?', array('Yes', 'Active'))->orderby('page_display_order','ASC')->get()->toArray();
				HomeCUtil::cachePut($cache_key, $lists_details);
			}
		}
		else {
			$cache_key = 'footer_static_page_links_cache_key';
			if (($lists_details = HomeCUtil::cacheGet($cache_key)) === NULL) {				
				$lists_details = StaticPages::WhereRaw('display_in_footer = ? AND status = ?', array('Yes', 'Active'))->orderby('page_display_order','ASC')->get();
				HomeCUtil::cachePut($cache_key, $lists_details);
			}
		}
		return $lists_details;
	}

	public static function getPageDetailsBySlug($url_slug)
	{
		$page_details = StaticPages::whereRaw('url_slug = ? AND display_in_footer = ? AND status = ?', array($url_slug, 'Yes', 'Active'))->first();
		return $page_details;
	}
}