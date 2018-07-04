<?php
class TransactionService
{
	public function getSiteTransactions($user_id=null, $return_type='paginate', $limit = 10, $inputs =array()){
		if(is_null($user_id) || $user_id =='')
			return false;
		$transaction_details = SiteTransactionDetails::orderby('id','desc');

		if(!CUtil::chkIsAllowedModule('deals'))
		{
			$transaction_details = $transaction_details->whereRaw('transaction_key <> \'deal_listfee_paid\' AND transaction_key <> \'deal_tipping_success\' AND transaction_key <> \'deal_tipping_failure\' ');
		}

		if(!CUtil::chkIsAllowedModule('featuredproducts'))
		{
			$transaction_details = $transaction_details->whereRaw('transaction_key <> \'product_featured_fee\'');
		}

		if(!CUtil::chkIsAllowedModule('featuredsellers'))
		{
			$transaction_details = $transaction_details->whereRaw('transaction_key <> \'seller_featured_fee\'');
		}

		if($user_id > 0)
			$transaction_details->where('user_id',$user_id);

		if(!empty($inputs))
		{
			if(!empty($inputs['from_date']) && !empty($inputs['to_date'])){
				$transaction_details = $transaction_details->whereRaw('DATE(date_added)  >= ?', array($inputs['from_date']))->whereRaw('DATE(date_added)  <= ?', array($inputs['to_date'].'%'));
			} else if(!empty($inputs['from_date'])){
				$transaction_details = $transaction_details->whereRaw('DATE(date_added)  >= ?', array($inputs['from_date']));
			} else if(!empty($inputs['to_date'])){
				$transaction_details = $transaction_details->whereRaw('DATE(date_added)  <= ?', array($inputs['to_date']));
			}

			if(isset($inputs['transaction_id']) && $inputs['transaction_id']!=''){
				$transaction_details->where('transaction_id', 'like', '%'.$inputs['transaction_id'].'%');
			}
		}

		if($return_type=='paginate')
			$transaction_details = $transaction_details->paginate($limit);
		else
			$transaction_details = $transaction_details->get();
		return $transaction_details;
	}
	public function getAdminSiteTransactions($return_type='paginate', $limit = 10, $inputs = array()){
		$transaction_details = new SiteTransactionDetails;
		//if($user_id > 0)$transaction_details->where('user_id',$user_id);
		if(!CUtil::chkIsAllowedModule('deals'))
		{
			$transaction_details = $transaction_details->whereRaw('transaction_key <> \'deal_listfee_paid\' AND transaction_key <> \'deal_tipping_success\' AND transaction_key <> \'deal_tipping_failure\' ');
		}

		if(!CUtil::chkIsAllowedModule('featuredproducts'))
		{
			$transaction_details = $transaction_details->whereRaw('transaction_key <> \'product_featured_fee\'');
		}

		if(!CUtil::chkIsAllowedModule('featuredsellers'))
		{
			$transaction_details = $transaction_details->whereRaw('transaction_key <> \'seller_featured_fee\'');
		}

		if(!empty($inputs))
		{
			if(!empty($inputs['from_date']) && !empty($inputs['to_date'])){
				$transaction_details = $transaction_details->whereRaw('DATE(date_added)  >= ?', array($inputs['from_date']))->whereRaw('DATE(date_added)  <= ?', array($inputs['to_date'].'%'));
			} else if(!empty($inputs['from_date'])){
				$transaction_details = $transaction_details->whereRaw('DATE(date_added)  >= ?', array($inputs['from_date']));
			} else if(!empty($inputs['to_date'])){
				$transaction_details = $transaction_details->whereRaw('DATE(date_added)  <= ?', array($inputs['to_date']));
			}
			if(!empty($inputs['user_code'])){
				$user_id = BasicCUtil::getUserIDFromCode($inputs['user_code']);
				$transaction_details = $transaction_details->whereRaw('user_id = ?', array($user_id));
				//$transaction_details = $transaction_details->leftjoin('users', 'site_transaction_details.user_id', '=', 'users.id');
				//$or_str = ' (users.first_name LIKE \'%'.addslashes($inputs['name']).'%\' OR users.last_name LIKE \'%'.addslashes($inputs['name']).'%\' )';
				//$transaction_details->whereRaw(DB::raw($or_str));
			}
			if(isset($inputs['transaction_id']) && $inputs['transaction_id']!=''){
				$transaction_details = $transaction_details->where('transaction_id', 'like', '%'.$inputs['transaction_id'].'%');
			}
		}
		$page = (isset($inputs['page']) && $inputs['page'] > 0)?$inputs['page']:1;
		Paginator::setCurrentPage($page);
		$transaction_details = $transaction_details->orderby('site_transaction_details.id','desc');
		if($return_type == 'paginate')
			$transaction_details = $transaction_details->paginate($limit);
		else
			$transaction_details = $transaction_details->get();
		return $transaction_details;
	}
}