<?php

class ViewProductService extends ProductService
{
	private $buyer_details = array();
	public $product_user_id = 0;
	public $logged_user_id = 0;
	public $set_as_private_note = '';

	function __construct()
	{
		parent::__construct();
		//$this->logged_user_id = (\isLoggedin()) ? \getAuthUser()->user_id : 0;
    }

    public function getProductViewBreadcrumbArr($cat_id)
    {
		$cat_arr = array();
		$cache_key = 'PVBA_cache_key_'.$cat_id;
		if (($q = HomeCUtil::cacheGet($cache_key)) === NULL) {
			$q = DB::select('SELECT parent.category_name, parent.seo_category_name FROM product_category AS node, product_category AS parent WHERE node.category_left BETWEEN parent.category_left AND parent.category_right  AND node.id = ? ORDER BY node.category_left;', array($cat_id));
			HomeCUtil::cachePut($cache_key, $q, Config::get('generalConfig.cache_expiry_minutes'));
		}
		if(count($q) > 0)
		{
			foreach($q AS $cat)
			{
				$cat_arr[$cat->seo_category_name] = $cat->category_name;
			}
			$cat_arr = array_slice($cat_arr, 1); //To remove root category
		}
		return $cat_arr;
	}

	public function addProductViewCount($p_id, $prod_obj)
	{
		$increment_view_count = false;
		$cookie_name = Config::get('webshoppack.site_cookie_prefix')."_mp_product_views";
		$cookie_value = $this->getCookie($cookie_name);
		$cookie = '';
		if($cookie_value != '')
		{
			$p_id_arr = explode(',', $cookie_value);
			$p_id_arr = array_unique($p_id_arr);
			if(!in_array($p_id, $p_id_arr))
			{
				$p_ids = $p_id.','.$cookie_value;
				$cookie = Cookie::forever($cookie_name, $p_ids);
				$increment_view_count = true;
			}
		}
		else
		{
			$cookie = Cookie::forever($cookie_name, $p_id);
			$increment_view_count = true;
		}
		if($increment_view_count)
		{
			//To increment the view count.
			$prod_obj->addProductViewCount($p_id);
			//Product::where('id', '=', $p_id)->increment('total_views');
		}
		return $cookie;
	}

	public function populateRecentQuestionsAndAnswers($item_id)
	{
		$recent_replies = array();
		$sql = ' SELECT discussion.discussion_id, discussion.buyer_id, discussion.seller_id, item_id, discussion.notes, discussion_replies.notes AS reply,'.
				' DATE_FORMAT(discussion.date_added, "%b %d, %Y") AS date_added, discussion.is_private FROM '.
				' discussion AS discussion LEFT JOIN discussion_replies '.
				' ON discussion.discussion_id = discussion_replies.discussion_id'.
				' JOIN ( SELECT reply.discussion_id, min( reply.reply_id ) AS first_reply_id'.
				' FROM discussion_replies AS reply  INNER JOIN discussion '.
				' AS discussion ON reply.discussion_id = discussion.discussion_id WHERE is_main_thread =\'0\' '.
				' AND added_by_user_id != discussion.buyer_id GROUP BY discussion_id'.
				' ) AS first_reply ON first_reply.discussion_id = discussion_replies.discussion_id'.
				' AND first_reply.first_reply_id = discussion_replies.reply_id'.
				' WHERE item_id = ? AND discussion.is_replied = \'1\''.
				' AND discussion.item_type = \'product\''.
				' AND discussion.visibility_status = \'1\''.
				' ORDER BY discussion_id DESC LIMIT 0,5';
		$data = DB::select($sql, array($item_id));


		$inc = 0;
		foreach($data AS $row)
		{
			$discussion_id = $row->discussion_id;
			$view_replies_url = '?query=add&id='.$row->discussion_id;
			$buyer_details = CUtil::getUserDetails($row->buyer_id);
			$seller_details = CUtil::getUserDetails($row->seller_id);

			$recent_replies[$inc]['discussion_id'] = $row->discussion_id;
			$recent_replies[$inc]['buyer_name']	= $buyer_details['display_name'];
			$recent_replies[$inc]['seller_name'] = $seller_details['display_name'];
			$recent_replies[$inc]['buyer_profile_url'] = $buyer_details['profile_url'];
			$recent_replies[$inc]['seller_profile_url'] = $seller_details['profile_url'];
			$recent_replies[$inc]['icon'] = CUtil::getUserPersonalImage($row->buyer_id);
			$recent_replies[$inc]['icon_seller'] = CUtil::getUserPersonalImage($row->seller_id);
			$recent_replies[$inc]['view_replies_url'] = $view_replies_url;
			$recent_replies[$inc]['add_report_url'] = '';
			$recent_replies[$inc]['show_report_url'] = false;
			$recent_replies[$inc]['question'] = $row->notes;
			$recent_replies[$inc]['reply_full'] = $row->reply;
			$recent_replies[$inc]['reply'] = $row->reply;
			$recent_replies[$inc]['show_more_notes'] = 0;
			if(strlen($row->reply) > 250)
			{
				$recent_replies[$inc]['reply'] 	= CUtil::wordWrap($row->reply, 250);
				$recent_replies[$inc]['show_more_notes'] = 1;
			}
			$recent_replies[$inc]['date_added'] = ($row->date_added != '')?$row->date_added:'-';
			$recent_replies[$inc]['is_private'] = $row->is_private;
			//Code to show private question and its replies only to buyer, seller and admin
			$recent_replies[$inc]['show_question_and_replies'] = true;
			if($row->is_private == 1)
			{
				$recent_replies[$inc]['show_question_and_replies'] = false;
				if(CUtil::isStaff() || $this->logged_user_id == $row->buyer_id || $this->logged_user_id == $row->seller_id)
				{
					$recent_replies[$inc]['show_question_and_replies'] = true;
				}
			}

			//Code to mark/unmark question as private
			$recent_replies[$inc]['mark_as_private_question_url'] = '';
			$recent_replies[$inc]['unmark_from_private_question_url'] = '';
			if(CUtil::isStaff() || $this->logged_user_id == $row->buyer_id || $this->logged_user_id == $row->seller_id)
			{
				$recent_replies[$inc]['mark_as_private_question_url'] = '?discussion_id='.$discussion_id.'&status=mark_as_private';
				$recent_replies[$inc]['unmark_from_private_question_url'] = '?discussion_id='.$discussion_id.'&status=unmark_from_private';
			}

			$inc++;
		}
		return $recent_replies;
	}

	public function chkDiscussionAccess($discussion_id)
	{
		 if(isLoggedIn())
		 {
			$d_arr = Discussion::whereRaw('discussion_id = ? AND visibility_status = ? ', array($discussion_id, 1))->first( array('buyer_id', 'seller_id'));
			if(count($d_arr) > 0)
			{
				if($d_arr['buyer_id'] == $this->logged_user_id || $d_arr['seller_id'] == $this->logged_user_id)
				{
					return true;
				}
			}
		}
		return false;
	}

	public function chkProductQueryAccess($p_details)
	{
		if(isLoggedIn())
		{
			if(isset($p_details['product_user_id']) && $p_details['product_user_id'] != $this->logged_user_id)
			{
				return true;
			}
		}
		return false;
	}

	public function chkIsProductReply($discussion_id)
    {
    	$reply = 0;
    	$this->view_query_url = $this->item_type = $this->item_name = '';
    	$row = Discussion::where('discussion_id', '=', $discussion_id)->first(array('last_replied_user_id', 'is_replied', 'buyer_id', 'item_id', 'item_type'));
		if(count($row) > 0 && $row['last_replied_user_id'] != '')
		{
			$this->buyer_details = CUtil::getUserDetails($row['buyer_id']);
			$this->item_type = ucfirst($row['item_type']);
			/*if(($row['last_replied_user_id'] && $row['last_replied_user_id'] != $this->logged_user_id) || ($row['is_replied'] == 1 && $row['last_replied_user_id'] == $this->logged_user_id))
			{
				$reply = 1;
			}*/
			$reply = 1;
		}
		return $reply;
	}

	public function updateProductDiscussion($discussion_id)
	{
		$data_arr = array('is_replied' => 1,
							'last_replied_user_id' => $this->logged_user_id,
							'last_replied_date' => date('Y-m-d H:m:s'),
							'status' => 'replied',
						);
		Discussion::where('discussion_id', '=', $discussion_id)->update($data_arr);
	}

	public function replyProductQuery($input_arr)
	{
		$discussion_id = $input_arr['discussion_id'];
		$is_product_reply = $this->chkIsProductReply($discussion_id);
    	if($is_product_reply)
    	{
    		$this->updateProductDiscussion($discussion_id);
    	}

    	$data_arr = array('discussion_id' => $discussion_id,
                            'notes' => $input_arr['addproductquery_notes'],
                            'added_by_user_id' => $this->logged_user_id,
                            'date_added' => date('Y-m-d H:m:s'));

		$obj = new DiscussionReplies();
		$reply_id = $obj->insertGetId($data_arr);

		//Code to update total replies in discussion table
		$this->updateTotalDiscussionReplies($discussion_id);
		//To send mail
		$this->sendProductQueryReplyMail($reply_id, $discussion_id);

		return true;
	}

	public function addProductQuery($input_arr, $p_details)
	{
		$is_private = (isset($input_arr['is_private'])? 1 : 0);
		$data_arr = array('item_id' => $p_details['id'],
                            'item_type' => 'product',
                            'buyer_id' => $this->logged_user_id,
                            'seller_id' => $p_details['product_user_id'],
                            'notes' => $input_arr['addproductquery_notes'],
                            'status' => 'new',
                            'is_replied' => 0,
                            'total_replies' => 0,
                            'last_replied_user_id' => $this->logged_user_id,
                            'date_added' => date('Y-m-d H:m:s'),
                            'is_private' => $is_private);

		$obj = new Discussion();
		$discussion_id = $obj->insertGetId($data_arr);
		$this->insertRepliesAsMainThread($discussion_id, $input_arr);
		$this->updateTotalDiscussionReplies($discussion_id);
		//To send mail
		$this->sendProductAddQueryMail($discussion_id);
		return $discussion_id;
	}


	public function getProductQueryValidationRules()
	{
		return array('rules' => array('addproductquery_notes' => 'Required'), 'messages' => array('required' => trans('common.required')));
	}

	public function insertRepliesAsMainThread($discussion_id, $input_arr)
	{
		$is_private = (isset($input_arr['is_private'])? 1 : 0);
		$data_arr = array('discussion_id' => $discussion_id,
                            'notes' => $input_arr['addproductquery_notes'],
                            'added_by_user_id' => $this->logged_user_id,
                            'is_main_thread' => 1,
                            'date_added' => date('Y-m-d H:m:s'));

			$obj = new DiscussionReplies();
			$discussion_id = $obj->insertGetId($data_arr);
	}

	public function updateTotalDiscussionReplies($discussion_id)
	{
		Discussion::where('discussion_id', '=', $discussion_id)->increment('total_replies');
		return true;
	}

	public function populateDiscussionReplies($item_id, $discussion_id = 0)
	{
		$discussion_replies = array();
		$inc = 0;
		$return_fields = array();
		$q = Discussion::leftJoin('discussion_replies', 'discussion.discussion_id', '=', 'discussion_replies.discussion_id');
		if($discussion_id)
		{
			$q->where('discussion_replies.discussion_id', '=', $discussion_id)
				->orderBy('discussion_replies.date_added', 'ASC');

			$return_fields = array('discussion_replies.notes', 'discussion_replies.is_main_thread', 'discussion_replies.added_by_user_id', DB::raw('DATE_FORMAT( discussion_replies.`DATE_ADDED` , \'%b %d, %Y\' ) AS date_added') );
		}
		else
		{
			$q->where('discussion_replies.added_by_user_id', '=', $this->logged_user_id)
				->where('item_id', '=', $item_id)
				->orderBy('date_added', 'ASC');

			$return_fields = array('discussion_replies.notes', 'discussion_replies.is_main_thread', 'discussion_replies.added_by_user_id', DB::raw('DATE_FORMAT( discussion_replies.`DATE_ADDED` , \'%b %d, %Y\' ) AS date_added', 'discussion.is_private', 'discussion.buyer_id', 'discussion.seller_id') );
		}
		$d_arr = $q->get( $return_fields );
		$buyer_name = '';
		foreach($d_arr AS $row)
		{
			if($row['is_main_thread'])
			{
				$user_details = CUtil::getUserDetails($row['added_by_user_id']);
				$discussion_replies['main_note'] = $row['notes'];
				$discussion_replies['buyer_name'] = $user_details['display_name'];
				$buyer_name = $discussion_replies['buyer_name'];
				$discussion_replies['icon'] = CUtil::getUserPersonalImage($row['added_by_user_id']);
				$discussion_replies['memberProfileUrl'] = $user_details['profile_url'];
				$discussion_replies['date_added']	= $row['date_added'];
				//Code to show private question and its replies only to buyer, seller and admin
				$discussion_replies['show_question_and_replies'] = true;
				if($row['is_private'] == 1)
				{
					$discussion_replies['show_question_and_replies'] = false;
					if(CUtil::isStaff() || $this->logged_user_id == $row['buyer_id'] || $this->logged_user_id == $row['seller_id'])
					{
						$discussion_replies['show_question_and_replies'] = true;
					}
				}
			}
			else
			{
				$user_details = CUtil::getUserDetails($row['added_by_user_id']);
				$discussion_replies['replies'][$inc]['reply_note'] = $row['notes'];
				$discussion_replies['replies'][$inc]['seller_name'] = $user_details['display_name'];
				if($discussion_replies['replies'][$inc]['seller_name'] == $buyer_name)
				{
					$discussion_replies['replies'][$inc]['reply_label'] = trans("mp_product/viewProduct.add_product_query_buyer");
					$discussion_replies['replies'][$inc]['replier_type'] = 'buyer';
				}
				else
				{
					$discussion_replies['replies'][$inc]['reply_label'] = trans("mp_product/viewProduct.add_product_query_seller");
					$discussion_replies['replies'][$inc]['replier_type'] = 'seller';
				}
				$discussion_replies['replies'][$inc]['icon'] = CUtil::getUserPersonalImage($row['added_by_user_id']);
				$discussion_replies['replies'][$inc]['memberProfileUrl'] = $user_details['profile_url'];
				$discussion_replies['replies'][$inc]['date_added']	= $row['date_added'];

				//Code to show private question and its replies only to buyer, seller and admin
				$discussion_replies['replies'][$inc]['show_question_and_replies'] = true;
				if($row['is_private'] == 1)
				{
					$discussion_replies['replies'][$inc]['show_question_and_replies'] = false;
					if(CUtil::isStaff() || $this->logged_user_id == $row['buyer_id'] || $this->logged_user_id == $row['seller_id'])
					{
						$discussion_replies['replies'][$inc]['show_question_and_replies'] = true;
					}
				}
			}
			$inc++;
		}
		return $discussion_replies;
	}

	public function populateQuestionsAndAnswers($item_id)
	{
		$recent_replies = array();
		$sql = ' SELECT discussion.discussion_id, discussion.buyer_id, discussion.seller_id, item_id, discussion.notes, discussion_replies.notes AS reply,'.
				' is_main_thread, discussion_replies.added_by_user_id, DATE_FORMAT(discussion_replies.DATE_ADDED , \'%b %d, %Y\' ) AS date_added, '.
				' discussion.is_private FROM '.
				' discussion AS discussion LEFT JOIN discussion_replies '.
				' ON discussion.discussion_id = discussion_replies.discussion_id'.
				' JOIN ( SELECT reply.discussion_id, min( reply.reply_id ) AS first_reply_id'.
				' FROM discussion_replies AS reply  INNER JOIN discussion '.
				' AS discussion ON reply.discussion_id = discussion.discussion_id WHERE is_main_thread =\'0\' '.
				' AND added_by_user_id != discussion.buyer_id GROUP BY discussion_id'.
				' ) AS first_reply ON first_reply.discussion_id = discussion_replies.discussion_id'.
				' AND first_reply.first_reply_id = discussion_replies.reply_id AND item_id = ? AND discussion.is_replied = \'1\' '.
				' AND discussion.visibility_status = \'1\' AND discussion.item_type = ?  GROUP BY discussion.discussion_id ';

		$data = DB::select($sql, array($item_id, 'product'));


		$inc = 0;
		foreach($data AS $row)
		{
			$discussion_id = $row->discussion_id;
			$view_replies_url = '?query=add&id='.$row->discussion_id;
			$buyer_details = CUtil::getUserDetails($row->buyer_id);
			$seller_details = CUtil::getUserDetails($row->seller_id);

			$recent_replies[$inc]['discussion_id'] = $row->discussion_id;
			$recent_replies[$inc]['buyer_name']	= $buyer_details['display_name'];
			$recent_replies[$inc]['seller_name'] = $seller_details['display_name'];
			$recent_replies[$inc]['buyer_profile_url'] = $buyer_details['profile_url'];
			$recent_replies[$inc]['seller_profile_url'] = $seller_details['profile_url'];
			$recent_replies[$inc]['icon'] = CUtil::getUserPersonalImage($row->buyer_id);
			$recent_replies[$inc]['icon_seller'] = CUtil::getUserPersonalImage($row->seller_id);
			$recent_replies[$inc]['view_replies_url'] = $view_replies_url;
			$recent_replies[$inc]['add_report_url'] = '';
			$recent_replies[$inc]['show_report_url'] = false;
			$recent_replies[$inc]['question'] = $row->notes;
			$recent_replies[$inc]['reply'] = $row->reply;
			$recent_replies[$inc]['show_more_notes'] = 0;
			$recent_replies[$inc]['date_added'] = ($row->date_added != '')?$row->date_added:'-';
			$recent_replies[$inc]['is_private'] = $row->is_private;
			//Code to show private question and its replies only to buyer, seller and admin
			$recent_replies[$inc]['show_question_and_replies'] = true;
			if($row->is_private == 1)
			{
				$recent_replies[$inc]['show_question_and_replies'] = false;
				if(CUtil::isStaff() || $this->logged_user_id == $row->buyer_id || $this->logged_user_id == $row->seller_id)
				{
					$recent_replies[$inc]['show_question_and_replies'] = true;
				}
			}

			//Code to mark/unmark question as private
			$recent_replies[$inc]['mark_as_private_question_url'] = '';
			$recent_replies[$inc]['unmark_from_private_question_url'] = '';
			if(CUtil::isStaff() || $this->logged_user_id == $row->buyer_id || $this->logged_user_id == $row->seller_id)
			{
				$recent_replies[$inc]['mark_as_private_question_url'] = '?discussion_id='.$discussion_id.'&status=mark_as_private';
				$recent_replies[$inc]['unmark_from_private_question_url'] = '?discussion_id='.$discussion_id.'&status=unmark_from_private';
			}

			$inc++;
		}
		return $recent_replies;
	}

	public function sendProductAddQueryMail($discussion_id)
	{
		$discussion_details = Discussion::where('discussion_id', '=', $discussion_id)->first();

		$seller_details = CUtil::getUserDetails($discussion_details->seller_id);
		$buyer_details = CUtil::getUserDetails($discussion_details->buyer_id);
		$p_details = MpProduct::where('id', '=', $discussion_details->item_id)->first(array('product_name', 'product_code', 'url_slug'));
		$view_url = $this->getProductViewURL($discussion_details->item_id, $p_details);

		$data = array(
			'product_name'	=> $p_details['product_name'],
			'notes'	=> $discussion_details->notes,
			'date_added'	=> date('M d, Y', strtotime($discussion_details->date_added)),
			'seller_details'	=> $seller_details,
			'buyer_details'	=> $buyer_details,
			'query_id'	=> $discussion_details->discussion_id,
			'query_view_url' => $view_url.'?query=add&id='.$discussion_details->discussion_id,
			'view_url'		=> $view_url,
			'to_email'		=> $seller_details['email']
		);

		//Mail to User
		try {
			Mail::send('emails.mp_product.queryAdded', $data, function($m) use ($data) {
					$m->to($data['to_email']);
					$subject = str_replace('VAR_QUERY_ID', $data['query_id'], trans('email.mpProductQueryAdded'));
					$m->subject($subject);
				});
		} catch (Exception $e) {
			//return false
			CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
		}

		//Mail to admin
		$mailer = new AgMailer;
		$data['subject'] = str_replace('VAR_QUERY_ID', $data['query_id'], trans('email.mpProductQueryAddedAdmin'));
		$mailer->sendAlertMail('mp_product_query_add', 'emails.mp_product.queryAddedAdmin', $data);
	}

	public function sendProductQueryReplyMail($reply_id, $discussion_id)
	{
		$replies_details = DiscussionReplies::where('reply_id', '=', $reply_id)->first();
		$discussion_details = Discussion::where('discussion_id', '=', $discussion_id)->first();

		$seller_details = CUtil::getUserDetails($discussion_details->seller_id);
		$buyer_details = CUtil::getUserDetails($discussion_details->buyer_id);
		$user_details = CUtil::getUserDetails($replies_details->added_by_user_id); //Note added user details
		$to_email = ($discussion_details->seller_id == $replies_details->added_by_user_id)? $buyer_details['email'] : $seller_details['email'];
		$p_details = MpProduct::where('id', '=', $discussion_details->item_id)->first(array('product_name', 'product_code', 'url_slug'));
		$view_url = $this->getProductViewURL($discussion_details->item_id, $p_details);

		$data = array(
			'product_name'	=> $p_details['product_name'],
			'notes'	=> $replies_details->notes,
			'date_added'	=> date('M d, Y', strtotime($discussion_details->date_added)),
			'seller_details'	=> $seller_details,
			'buyer_details'	=> $buyer_details,
			'user_details'	=> $user_details,
			'query_id'	=> $discussion_details->discussion_id,
			'query_view_url' => $view_url.'?query=add&id='.$discussion_details->discussion_id,
			'view_url'		=> $view_url,
			'to_email'		=> $to_email
		);

		//Mail to User
		try {
			Mail::send('emails.mp_product.queryReplyAdded', $data, function($m) use ($data) {
					$m->to($data['to_email']);
					$subject = str_replace('VAR_QUERY_ID', $data['query_id'], trans('email.mpProductQueryReplyAdded'));
					$m->subject($subject);
				});
		} catch (Exception $e) {
			//return false
			CUtil::sendMailerSettingsErrorToAdmin($e->getMessage());
		}
		//Mail to admin
		$mailer = new AgMailer;
		$data['subject'] = str_replace('VAR_QUERY_ID', $data['query_id'], trans('email.mpProductQueryReplyAddedAdmin'));
		$mailer->sendAlertMail('mp_product_query_reply', 'emails.mp_product.queryReplyAddedAdmin', $data);
	}

	public function chkIsValidDiscussion($discussion_id)
	{
		$d_arr = Discussion::whereRaw('discussion_id = ? AND visibility_status = ?', array($discussion_id, 1))->first();

        if(count($d_arr) > 0)
        {
        	//Condition to show set as private based on logged in user(buyer/seller/admin)
        	if($this->logged_user_id == $d_arr['buyer_id'])
            {
            	$this->set_as_private_note = trans('mp_product/viewProduct.private_question_set_as_private_buyer_note');
            }
            elseif($this->logged_user_id == $d_arr['seller_id'])
            {
            	$this->set_as_private_note = trans('mp_product/viewProduct.private_question_set_as_private_seller_note');
			}
			elseif(CUtil::isStaff())
			{
				$this->set_as_private_note = trans('mp_product/viewProduct.private_question_set_as_private_admin_note');
			}

        	//condition to allow to set/unset question as private only by buyser, seller and staff
        	if(CUtil::isStaff() || $this->logged_user_id == $d_arr['buyer_id'] || $this->logged_user_id == $d_arr['seller_id'])
        	{
            	return $d_arr;
            }

        }
        return false;
	}

	public function updateDiscussionPrivateStatus($data_arr)
	{
		Discussion::where('discussion_id', '=', $data_arr['discussion_id'])->update(array('is_private' => $data_arr['is_private']));
		return true;
	}

	public function populateRecentPurchasesAndReviews($p_id, $u_id)
	{
		$recent_purchases = array();
		$d_arr = MpFeedbackItem::whereRaw('feedback_item_id = ? AND feedback_user_id != ? AND item_type = ?', array($p_id, $u_id, 'product'))
								->get( array('feedback_comment', 'feedback_status', 'feedback_user_id', DB::raw('DATE_FORMAT(date_added, "%b %d, %Y") AS date_added')));

		$inc = 0;
		foreach($d_arr AS $data)
		{
			$posted_user_details = CUtil::getUserDetails($data['feedback_user_id']);
			$recent_purchases[$inc]['posted_by'] = $posted_user_details['display_name'];
			$recent_purchases[$inc]['date_added'] = ($data['date_added'] != '')?$data['date_added']:'-';
			$recent_purchases[$inc]['profile_url'] = $posted_user_details['profile_url'];
			$recent_purchases[$inc]['icon'] = CUtil::getUserPersonalImage($data['feedback_user_id']);
			$recent_purchases[$inc]['feedback_comment'] = $data['feedback_comment'];
			$recent_purchases[$inc]['show_more_notes'] = 0;
			$recent_purchases[$inc]['feedback_comment_full'] = CUtil::makeClickableLinks(nl2br($data['feedback_comment']));
			if(strlen($data['feedback_comment']) > 250)
			{
				$recent_purchases[$inc]['feedback_comment'] =	CUtil::wordWrap($data['feedback_comment'], 250);
				$recent_purchases[$inc]['show_more_notes'] = 1;
			}
			$recent_purchases[$inc]['feedback_status'] = $data['feedback_status'];
			$inc++;
		}
		return $recent_purchases;
	}

	//Added by mohamed_158at11
	public function getMpProductRating($product_id, $user_id)
	{
		$rating_arr = array();
		$productRating = MpProductRating::where('mp_product_id', '=', $product_id)->avg('rating');
		$rating_arr['avg_rating'] = $productRating;
		$rating_arr['user_rating'] = 0;
		$rating_arr['user_purchase'] = false;
		if($user_id != '')
		{
			$user_rated = MpProductRating::whereRaw('mp_product_id = ? and user_id = ?', array($product_id,$user_id))->first();
			if(!empty($user_rated))
			{
				$rating_arr['user_rating'] = $user_rated['rating'];
			}
		}
		$rating_arr['user_purchase'] = $this->isUserBoughtProduct($product_id, $user_id);
		return $rating_arr;
	}
	//Added by mohamed_158at11

	public function addRating($inputs = array())
	{
		if(!empty($inputs))
		{
			$productRating = new MpProductRating();
			$rated_id = $productRating::whereRaw('mp_product_id = ? and user_id = ?', array($inputs['mp_product_id'], $inputs['user_id']))->first();
			if(empty($rated_id))
			{
				$rate_id = $productRating->addNew($inputs);
				return $rate_id;
			}
			else
			{
				$affectedRows = $productRating::where('id', '=', $rated_id['id'])->update(array('rating' => $inputs['rating']));
				return false;
			}
		}
	}
	//Added by mohamed_158at11
	public function isUserBoughtProduct($product_id, $user_id)
	{
		$purchased = MpInvoices::where('item_id','=',$product_id)
								->where('buyer_id', '=', $user_id)
								->where('invoice_status', '!=', 'pending')
								->count();
			//$purchased = DB::table('mp_purchase')->join('mp_invoices', function($join){
			//										$join->on('mp_purchase.invoice_id', '=', 'mp_invoice_item.invoice_id');
			//									})
			//									->where('mp_purchase.status', '=', 'paid')
			//									->where('mp_invoice_item.product_id', '=',  $product_id)
			//									->where('mp_invoice_item.user_id', '=',  $user_id)->get();
			//echo "<pre>";print_r($purchased);echo "</pre>";

		if($purchased > 0)
			return true;
		else
			return false;

	}

	public function getMpProductLikedInfo($product_id, $user_id)
	{
		$liked_arr = array();
		$total_likes = MpProductLikedDetails::where('product_id', '=', $product_id)->where('liked', '=', '1')->count();
		$liked_arr['total_likes'] = $total_likes;
		$liked_arr['user_liked_already'] = 0;
		$liked_arr['user_liked'] = 0;
		if($user_id != '')
		{
			$user_liked = MpProductLikedDetails::where('product_id', '=', $product_id)
							->where('user_id', '=', $user_id)->first();

			if($user_liked && count($user_liked) > 0)
			{
				$liked_arr['user_liked_already'] = 1;
				$liked_arr['user_liked'] = $user_liked['liked'];
			}

		}
		return $liked_arr;

	}
	public function addLike($inputs = array())
	{
		if(!empty($inputs))
		{
			$productRating = new MpProductLikedDetails();
			$rated_id = $productRating::whereRaw('product_id = ? and user_id = ?', array($inputs['product_id'], $inputs['user_id']))->first();
			if(empty($rated_id))
			{
				$rate_id = $productRating->addNew($inputs);
				return $rate_id;
			}
			else
			{
				$affectedRows = $productRating::where('id', '=', $rated_id['id'])->update(array('liked' => $inputs['liked']));
				return false;
			}
		}
	}

	public function isUserLikedAlready($product_id, $user_id)
	{
		$purchased = MpProductLikedDetails::where('product_id','=',$product_id)->where('user_id', '=', $user_id)->count();
		if($purchased > 0)
			return true;
		else
			return false;
	}




	public function sendProductConversationMail($product_details = array(), $thread_id = 0, $cache_key = 'NewComment', $message_id = 0, $reference_id = 0)
	{
		$thread_details_arr = MpProductComments::whereRaw("id = ?", array($thread_id))->first();
		if(count($product_details) == 0 || count($thread_details_arr) == 0)
		{
			return '';
		}
		$email_template = 'emails.mp_product.productCommentAddedNotify';
		/*
		switch($key)
		{
			case 'NewComment':
				$email_template .= 'requestNewMessage';
				break;

			case 'MessageOwnerReply':
				$email_template .= 'requestMessageOwnerReply';
				break;

			case 'MessageUserReply':
				$email_template .= 'requestMessageUserReply';
				break;

			case 'deleteThread':
				$email_template .= 'requestDeleteThread';
				break;

			case 'deleteThreadReply':
				$email_template .= 'requestDeleteThreadReply';
				break;

			default:
				return '';
		}
		*/

		$view_product_link = $this->getProductViewURL($product_details->id, $product_details);
		$thread_link = $view_product_link.'?thread='.$thread_id;

		$message_details_arr = array();
		//To get user information
		$from_id = $this->logged_user_id ;
		$to_id = ($product_details['product_user_id'] == $from_id)?  $thread_details_arr['user_id'] : $thread_details_arr['seller_id'];

		$from_user_details = CUtil::getUserDetails($from_id, 'all');
		$to_user_details = CUtil::getUserDetails($to_id, 'all');

		//To get seller details
		$owner_details = array();
		if($from_id == $product_details['product_user_id'])
			$owner_details = $from_user_details;
		elseif($to_id == $product_details['product_user_id'])
			$owner_details = $to_user_details;
		else
			$owner_details = CUtil::getUserDetails($product_details['product_user_id'], 'all');

		//To get commenter details
		$commenter_details = array();
		if($from_id == $thread_details_arr['user_id'])
			$commenter_details = $from_user_details;
		elseif($to_id == $thread_details_arr['user_id'])
			$commenter_details = $to_user_details;
		else
			$commenter_details = CUtil::getUserDetails($thread_details_arr['user_id'], 'all');
		$visibility_text = ($thread_details_arr['visibility'] == "Public") ? "Public" : "Private";

		//Get replied message details
		$notes = $thread_details_arr['message'];
		if($message_id > 0)
		{
			//To get message details if already not fetched..
			if(count($message_details_arr) == 0)
			{
				$message_details_arr = MpProductCommentReplies::whereRaw("id = ?", array($message_id))->first();

				if(count($message_details_arr) > 0) //To check and proceed with message details
				{
					$notes = $message_details_arr['notes'];
					if($message_details_arr['visibility_status'] == 'Private')
					{
						$visibility_text = trans('mp_product/viewProduct.private_msg_for');
						$visibility_text .= $to_user_details['display_name'];
						$visibility_text .= " By.".$from_user_details['display_name'];
					}
				}
			}
		}

		//To assign mail data's..
		$mail_subject_lang = 'email.product_'.snake_case($key);
		$user_mail_sub = trans($mail_subject_lang.'_notify_user');
		$admin_mail_sub = trans($mail_subject_lang.'_notify_admin');
	//	$ack_mail_sub = trans($mail_subject_lang.'NotifyACK');

		$data_arr = array(
			'product_title'	=> $product_details['product_name'],
			'from_user_name' => $from_user_details['display_name'],
			'to_user_name' => $to_user_details['display_name'],
			'from_profile_url' => $from_user_details['profile_url'],
			'to_profile_url' => $to_user_details['profile_url'],
			'view_product_link' => $view_product_link,
			'thread_link' => $thread_link,
			'notes' => $notes,
			'visibility' => $visibility_text,
			'product_details' => $product_details,
			'owner_details' => $owner_details,
			'commenter_details' => $commenter_details,
			'user_subject' => str_replace('VAR_PRODUCT_TITLE', $product_details['product_name'], $user_mail_sub),
			'to_user_mail' => $to_user_details['email'],
			'admin_subject' => str_replace('VAR_PRODUCT_TITLE', $product_details['product_name'], $admin_mail_sub),
			'admin_email_template' => $email_template.'ForAdmin',
			'user_email_template' => $email_template.'ForUser',
			'key'	=> $key
		);

		//Mail to admin
		$mailer = new AgMailer;
		$data_arr['subject'] = $data_arr['admin_subject'];
		$mailer->sendAlertMail('product_comment', $data_arr['admin_email_template'], $data_arr);


		if(isset($to_user_details['email']) && $to_user_details['email'] != "" && $from_id != $to_id)
		{
			$data_arr['subject'] = $data_arr['user_subject'];
			$data_arr['to_email'] = $to_user_details['email'];
			$mailer->sendUserMail('product_comment', $data_arr['user_email_template'], $data_arr);
		}
	}

}