<?php
class CollectionService
{
	public function generateSlug($name = '')
	{
		if(is_null($name) || $name == '')
			return '';
		else
			return \Str::slug($name);
	}
	public function addCollection($inputs){
		$collection = new Collection();
		$collection_id  = $collection->addNew($inputs);
		return $collection_id;
	}
	public function setCollectionFilter($inputs = array()){
		if(isset($inputs['collection_name']) && $inputs['collection_name']!='' && $inputs['collection_name']!=Lang::get('collection.default_search_collection_name'))
			$this->collection_name = $inputs['collection_name'];
		if(isset($inputs['orderby_field']))
			$this->order_by = $inputs['orderby_field'];
		if(isset($inputs['user_code']) && $inputs['user_code']!='')
		{
			$user_id = CUtil::getUserId($inputs['user_code']);
			if($user_id > 0)
				$this->user_id = $user_id;
		}
		if(isset($inputs['user_id']) && $inputs['user_id'] > 0)
		{
			$this->user_id = $inputs['user_id'];
		}
		if(isset($inputs['collection_by']) && $inputs['collection_by'] != '' && $inputs['collection_by']!= Lang::get('collection.default_search_by_member'))
		{
			$this->collection_by = $inputs['collection_by'];
		}
	}
	public function setCollectionFilterIds($collectionids = array()){
		$this->collection_ids = $collectionids;
	}
	public function getCollectionsList($return_type = 'get', $limit = 10){
		$cache_key = 'CL_'.$return_type;
		$collection = Collection::select(DB::raw('collections.*'));
		$collection->join('users', function($join)
			                         {
			                             $join->on('collections.user_id', '=', 'users.id');
			                             $join->where('users.is_banned', '=', 0);
			                             $join->where('users.shop_status', '=', 1);
			                         });

		$collection->where('collections.collection_status', '=', 'Active');

		if(isset($this->collection_name) && $this->collection_name!='')
		{
			$cache_key .= 'CN_'.$this->collection_name;
			$collection->where('collections.collection_name', 'like', '%'.$this->collection_name.'%');
		}

		$logged_user_id = BasicCUtil::getLoggedUserId();
		if($logged_user_id > 0 && !isset($this->user_id))
		{
			$cache_key .= 'UID_'.$logged_user_id;
			$collection->whereRaw('( (collections.user_id != ? AND collections.collection_access = \'Public\') OR (collections.user_id = ?)) ', array($logged_user_id, $logged_user_id));
		}
		elseif(isset($this->user_id) && $this->user_id > 0)
		{
			$cache_key .= 'U_'.$this->user_id;
			$collection->where('collections.user_id', '=', $this->user_id);
			//if logged user id is the filtered user id, then show the collections to him
			if($logged_user_id != $this->user_id)
			{
				$cache_key .= 'NELUID_'.$this->user_id;
				$collection->where('collections.collection_access', '=', 'Public');
			}
		}
		else
		{
			$cache_key .= 'CAP';
			$collection->where('collections.collection_access', '=', 'Public');
		}

		if(isset($this->collection_by) && $this->collection_by != '')
		{
			$cache_key .= '_CB'.$this->collection_by;
			$collection_by = $this->collection_by;
			//$collection->join('users','collections.user_id','=','users.id');
			$condition_string = '(users.first_name LIKE \'%'.addslashes($collection_by).'%\' OR users.last_name LIKE \'%'.addslashes($collection_by).'%\' OR users.user_name LIKE \'%'.addslashes($collection_by).'%\')';
			$collection->whereRaw(DB::raw($condition_string));
		}
		if(isset($this->collection_ids) && $this->collection_ids!='' && !empty($this->collection_ids))
		{
			$cache_key .= '_CID'.serialize($this->collection_ids);
			$collection->whereIn('collections.id',$this->collection_ids);
		}
		if(!isset($this->order_by) || $this->order_by=='')
			$this->order_by = 'collections.id';
		if(isset($this->order_by) && in_array($this->order_by,array('collections.id','collections.total_views','collections.total_comments') ))
		{
			$cache_key .= '_OBID'.$this->order_by;
			$collection->orderby($this->order_by,'desc');
		}
		else
		{
			$cache_key .= '_OBID';
			$collection->orderby('collections.id','desc');
		}

		if($return_type == 'paginate')
		{
			if(!HomeCUtil::cacheAllowed())
				$collection_result = $collection->paginate($limit);
			else{
				$page_name = (!Input::has('page') ? '1' :  Input::get('page'));
				$cache_key .= '_PR'.$return_type.'_'.$page_name.'_'.$limit;
				if( ! Cache::has($cache_key)) {
					$collection_arr = array(
							'total' => $collection->get()->count(),
							'items' => $collection->paginate($limit)->getItems(),
							'perpage' => $limit,
							);
					Cache::put($cache_key, $collection_arr, Config::get('generalConfig.cache_expiry_minutes'));
				}
				$collection_arr = Cache::get($cache_key);
				$collection_result = Paginator::make($collection_arr['items'], $collection_arr['total'], $collection_arr['perpage']);
			}
		}
		else
		{
			$cache_key .= '_GR';
			if (($collection_result = HomeCUtil::cacheGet($cache_key)) === NULL) {
				$collection_result = $collection->get();
				HomeCUtil::cachePut($cache_key, $collection_result, Config::get('generalConfig.cache_expiry_minutes'));
			}
		}
		return $collection_result;
	}
	public function getCollections($user_id, $return_type = 'get', $limit = 10){
		$collection = Collection::where('user_id',$user_id)
						->where('collection_status', '!=', 'InActive');
		if(isset($this->collection_name) && $this->collection_name!='')
			$collection->where('collection_name', 'like', '%'.$this->collection_name.'%');
		if($return_type == 'paginate')
			return $collection->paginate($limit);
		else
			return $collection->get();
	}
	public function addCollectionProducts($inputs = array())
	{
		if(!is_array($inputs) || empty($inputs))
			return false;
		DB::table('collection_products')->insert($inputs);
		return true;
	}
	public function getCollectionDetails($collection_id)
	{
		$collection = Collection::where('id',$collection_id)->first();
		if(count($collection) > 0)
			return $collection;
		else
			return false;
	}
	public function getCollectionDetailsBySlug($collection_slug)
	{
		$cache_key = 'GCDBS_'.$collection_slug;
		if (($collection = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$collection = Collection::where('collection_slug',$collection_slug)->first();
			HomeCUtil::cachePut($cache_key, $collection, Config::get('generalConfig.cache_expiry_minutes'));
		}
		if(count($collection) > 0)
			return $collection;
		else
			return false;
	}
	public function getCollectionProductIds($collection_id, $limit = 0)
	{
		$cache_key = 'GCPIDCK_'.$collection_id;
		$collection_products = CollectionProduct::where('collection_id',$collection_id)->orderby('order','asc')
								->join('product', 'product.id', '=', 'collection_products.product_id')
								->join('users', 'users.id', '=', 'product.product_user_id')
								->where('product.product_status', '!=', 'Deleted')
								->where('users.shop_status', '=', '1')
								->where('users.is_banned', '=', '0');
		if($limit > 0)
		{
			$cache_key .= '_LR'.$limit;
			$collection_products = $collection_products->take($limit);
		}

		if (($collection_products_result = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$collection_products_result = $collection_products->lists('product_id');
			HomeCUtil::cachePut($cache_key, $collection_products_result, Config::get('generalConfig.cache_expiry_minutes'));
		}

		if(count($collection_products_result) > 0)
			return $collection_products_result;
		else
			return false;
	}
	public function getCollectionProductCounts($collction_id = null)
	{
		if(is_null($collction_id) || $collction_id <= 0)
			return 0;
		$cache_key = 'GCPC_'.$collction_id;
		if (($tot_products = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$tot_products = CollectionProduct::where('collection_id',$collction_id)->count();
			HomeCUtil::cachePut($cache_key, $tot_products, Config::get('generalConfig.cache_expiry_minutes'));
		}
		return $tot_products;
	}
	public function updateCollection($collection_id, $inputs = array()){
		if(!is_array($inputs) || empty($inputs))
			return false;
		unset($inputs['listing_id']);
		try{
			$collection = Collection::where('id',$collection_id)->update($inputs);
			return true;
		}
		Catch(Exception $e){
			return false;
		}
	}
	public function removeCollectionProducts($collection_id)
	{
		$collection_products = CollectionProduct::where('collection_id',$collection_id)->delete();
	}
	public function deleteCollection($collection_id = 0)
	{
		if(is_null($collection_id) || $collection_id<=0)
			return false;
		$this->removeCollectionProducts($collection_id);
		$deleted = Collection::where('id',$collection_id)->delete();
		return $deleted;
	}

	public function getCollectionComments($collection_id, $return_type='get', $limit = 10, $allow_cache = true)
	{
		$cache_key = 'GCC_'.$collection_id;
		if(is_null($collection_id) || $collection_id<=0)
			return false;

		$collection_comments = CollectionComments::where('collection_id',$collection_id);
		if($return_type = 'get')
		{
			$cache_key .= '_GRT';
			if (!$allow_cache || (($collection_comments_results = HomeCUtil::cacheGet($cache_key)) === NULL)) {
				$collection_comments_results = $collection_comments->get();
				HomeCUtil::cachePut($cache_key, $collection_comments_results, Config::get('generalConfig.cache_expiry_minutes'));
			}
		}
		elseif($return_type == 'paginate')
		{
			if(!$allow_cache || !HomeCUtil::cacheAllowed())
				$collection_comments_results = $collection_comments->paginate($limit);
			else{
				$page_name = (!Input::has('page') ? '1' :  Input::get('page'));
				$cache_key .= '_GRP'.$limit.'_'.$page_name;
				if( ! Cache::has($cache_key)) {
					$collection_comments_arr = array(
							'total' => $collection_comments->get()->count(),
							'items' => $collection_comments->paginate($limit)->getItems(),
							'perpage' => $limit,
							);
					Cache::put($cache_key, $collection_comments_arr, Config::get('generalConfig.cache_expiry_minutes'));
				}
				$collection_comments_arr = Cache::get($cache_key);
				$collection_comments_results = Paginator::make($collection_comments_arr['items'], $collection_comments_arr['total'], $collection_comments_arr['perpage']);
			}
		}
		return $collection_comments_results;
	}
	public function addCollectionComment($inputs){
		$collectioncomments = new CollectionComments();
		$collection_comment_id  = $collectioncomments->addNew($inputs);

		Event::fire('collectioncomment.updatecount', array($inputs, 'increment'));
		return $collection_comment_id;
	}
	public function getCollectionCommentDetails($comment_id)
	{
		$cache_key = 'GCCDCK_'.$comment_id;
		if (($collection_comments = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$collection_comments = CollectionComments::select('collection_comments.*','collections.user_id as owner_id')
						->leftjoin('collections','collections.id', '=', 'collection_comments.collection_id')
						->where('collection_comments.id',$comment_id)->where('collection_comments.status','Active')->first();
			HomeCUtil::cachePut($cache_key, $collection_comments, Config::get('generalConfig.cache_expiry_minutes'));
		}
		if(count($collection_comments) > 0)
			return $collection_comments;
		else
			return false;
	}
	public function updateCollectionCommentDetails($collection_comment_id, $inputs = array()){
		if(!is_array($inputs) || empty($inputs))
			return false;
		$collection = CollectionComments::where('id',$collection_comment_id)->update($inputs);
	}
	public function deleteCollectionComment($collection_comment_id){
		$inputs = $this->getCollectionCommentDetails($collection_comment_id)->toArray();
		$deleted = CollectionComments::where('id',$collection_comment_id)->delete();
		Event::fire('collectioncomment.updatecount', array($inputs, 'decrement'));
		return $deleted;
	}

	public function getAllCollectionAdmin($inputs = array(), $return_type='paginate', $limit = 10)
	{
		//SELECT *, COUNT(collection_favorites.id) AS tot_count FROM collections LEFT JOIN collection_favorites ON(collection_favorites.collection_id = collections.id) WHERE 1 GROUP BY collections.id;
		$cache_key = 'GACA';
		$all_collections = Collection::select('collections.*', DB::Raw("count(collection_favorites.id) as total_favorites"))
							->leftjoin('collection_favorites', 'collection_favorites.collection_id','=','collections.id')
							->groupby('collections.id')
							->orderby('collections.id','desc');

		if(isset($inputs['collection_id_from']) && $inputs['collection_id_from'] > 0)
		{
			$cache_key .= 'CIF_'.$inputs['collection_id_from'];
			$all_collections->where('collections.id', '>=', $inputs['collection_id_from']);
		}
		if(isset($inputs['collection_id_to']) && $inputs['collection_id_to'] > 0)
		{
			$cache_key .= 'CIT'.$inputs['collection_id_to'];
			$all_collections->where('collections.id', '<=', $inputs['collection_id_to']);
		}

		if(isset($inputs['privacy']) && $inputs['privacy'] != '')
		{
			$cache_key .= 'CA'.$inputs['privacy'];
			$all_collections->where('collections.collection_access', '=', $inputs['privacy']);
		}
		if(isset($inputs['status']) && $inputs['status'] != '')
		{
			$cache_key .= 'CS'.$inputs['status'];
			$all_collections->where('collections.collection_status', '=', $inputs['status']);
		}

		if(isset($inputs['featured']) && $inputs['featured'] != '')
		{
			$cache_key .= 'CF'.$inputs['featured'];
			$all_collections->where('collections.featured_collection', '=', ucfirst($inputs['featured']));
		}

		if($return_type=='get')
		{
			$cache_key .= '_GR';
			if (($all_collections_result = HomeCUtil::cacheGet($cache_key)) === NULL) {
				$all_collections_result = $all_collections->get();
				HomeCUtil::cachePut($cache_key, $all_collections_result, Config::get('generalConfig.cache_expiry_minutes'));
			}
		}
		else
		{
			if(!HomeCUtil::cacheAllowed())
				$all_collections_result = $all_collections->paginate($limit);
			else{
				$page_name = (!Input::has('page') ? '1' :  Input::get('page'));
				$cache_key .= '_PR'.$page_name.'_'.$limit;
				if( ! Cache::has($cache_key)) {
					$all_collection_arr = array(
							'total' => $all_collections->get()->count(),
							'items' => $all_collections->paginate($limit)->getItems(),
							'perpage' => $limit,
							);
					Cache::put($cache_key, $all_collection_arr, Config::get('generalConfig.cache_expiry_minutes'));
				}
				$all_collection_arr = Cache::get($cache_key);
				$all_collections_result = Paginator::make($all_collection_arr['items'], $all_collection_arr['total'], $all_collection_arr['perpage']);
			}
		}
		return $all_collections_result;
	}

	public function bulkUpdateCollections($collection_ids, $data = array()){
		$update = false;
		if(!empty($collection_ids))
		{
			try
			{
				$qry = Collection::whereIn('id', $collection_ids)->update($data);
				$update = true;
			}
			catch(exception $e)
			{
				$update = false;
			}
		}
		return $update;
	}
	public function bulkDeleteCollections($collection_ids = array()){
		if(!empty($collection_ids))
		{
			try{
				Collection::whereIn('id', $collection_ids)->delete();
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
	public function increaseClicks($collction_id){
		if(is_null($collction_id) || $collction_id <=0)
			return false;

		Collection::where('id', $collction_id)->increment('total_clicks');
		return true;
	}
}