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
class ViewProductController extends BaseController
{
	public $shop_items_limit = 3;
	private $logged_user_id = 0;

	function __construct()
	{
		parent::__construct();
		$this->ViewProductService = new ViewProductService();
		$this->logged_user_id = BasicCUtil::getLoggedUserId();

		if(CUtil::chkIsAllowedModule('deals'))
			 $this->deal_service = new DealsService();
		if(CUtil::chkIsAllowedModule('variations'))
			 $this->variation_service = new VariationsService();
        //$this->beforeFilter('auth', array('except' => array('getIndex', 'getDemo', 'getProductComments')));
    }

	public function getIndex($slug_url)
	{
		$productService = new ProductService();
		$shipping_template_service = new ShippingTemplateService();
		$feedback_service = new ProductFeedbackService;
		$product_report_service = new ReportedProductService;
		//$productFavoritesService = new ProductFavoritesService();

		$productFavoritesService = App::make('FavoriteInterface', array('favorites' => 'product'));
		$shopFavoritesService = App::make('FavoriteInterface', array('favorites' => 'shop'));

		$prod_obj = Products::initialize();
		$shop_obj = Products::initializeShops();
		$alert_msg = $error_msg = $product_code = $product_title = '';
		$d_arr = $breadcrumb_arr = array();
		$preview_mode = false;
		$product_code = $this->ViewProductService->getProductCode($slug_url);
		$stock_count = 0;

		$prod_obj->setFilterProductCode($product_code);

		try
		{
			$prod_obj->setIncludeDeleted(true);
			$prod_obj->setIncludeBlockedUserProducts(true);
			$p_details = $prod_obj->getProductDetails();
			//echo "<pre>";print_r($p_details);echo "</pre>";//exit;


			$product_service_details = array();
			$logged_user_id = (Sentry::getUser())? Sentry::getUser()->id : '';
			if(count($p_details) > 0)
			{
				$cookie_stock_country_id = CUtil::getSiteCountry();
				$stock_count = $prod_obj->countProductStocks($p_details['id'], $cookie_stock_country_id);
				//$product_service_details = $this->ViewProductService->getProductServiceDetails($p_details['id']);
				$product_report = $product_report_service->getProductReportDetails($p_details['id'], $logged_user_id);
				if($p_details['date_expires'] == '0000-00-00 00:00:00' || ($p_details['date_expires'] != '9999-12-31 00:00:00' && (strtotime($p_details['date_expires']) < strtotime(date('Y-m-d')))) )
				{
					if(CUtil::isAdmin() OR ($this->logged_user_id == $p_details['product_user_id']))
					{
						$alert_msg = trans("viewProduct.watching_expired");
						$preview_mode = true;
					}
					else
					{
						$error_msg = trans("viewProduct.product_drafted_choose_another_product");
					}

				}

				$product_user_details = CUtil::getUserDetails($p_details['product_user_id']);
				if(count($product_user_details) > 0) {
					if($product_user_details['is_banned'] == '1')
						$error_msg = trans('shop.shopowner_blocked');
					else if($product_user_details['shop_status'] == '0')
						$error_msg = trans('shop.shop_blocked');
					//if($error_msg != '')
					//	return Redirect::to('product')->with('error_message', $error_msg);
				}

				if(!CUtil::isShopOwner($p_details['product_user_id'])) {
					$error_msg = trans('product.invalid_seller_usercode');
					return Redirect::to('product')->with('error_message', $error_msg);
				}

				//echo "<pre>";print_r($product_service_details);echo "</pre>";
				$this->ViewProductService->product_user_id = $p_details['product_user_id'];
				//echo $this->logged_user_id." == ".$p_details['product_user_id'];

				//echo "<br>p_details: ".$p_details['date_expires'];
				//echo "<br>".strtotime($p_details['date_expires'])."<=". strtotime(date('Y-m-d H:i:s'));


				if($p_details['product_status'] != 'Ok')
				{
					if(CUtil::isAdmin() OR ($this->logged_user_id == $p_details['product_user_id']))
					{
						if($p_details['product_status'] == 'Deleted')
							$alert_msg = trans("viewProduct.watching_deleted");
						else
							$alert_msg = trans("viewProduct.draft_or_not_approval");
						$preview_mode = true;
					}
					else
					{
						if($p_details['product_status'] == 'Deleted')
							$error_msg = trans("viewProduct.product_deleted_choose_another_product");
						else
							$error_msg = trans("viewProduct.product_drafted_choose_another_product");
					}
				}

				if($p_details['use_cancellation_policy'] == 'Yes' && $p_details['use_default_cancellation'] == 'Yes')
				{
					$cancellation_policy = Products::initializeCancellationPolicy();
    				$cancellation_details = $cancellation_policy->getCancellationPolicyDetails($p_details['product_user_id']);

					//echo "<pre>";print_r($cancellation_details);echo "</pre>";exit;
					$p_details['cancellation_policy_text'] = isset($cancellation_details['cancellation_policy_text'])?$cancellation_details['cancellation_policy_text']:'';
					$p_details['cancellation_policy_filename'] = isset($cancellation_details['cancellation_policy_filename'])?$cancellation_details['cancellation_policy_filename']:'';
					$p_details['cancellation_policy_filetype'] = isset($cancellation_details['cancellation_policy_filetype'])?$cancellation_details['cancellation_policy_filetype']:'';
					$p_details['cancellation_policy_server_url'] = isset($cancellation_details['cancellation_policy_server_url'])?$cancellation_details['cancellation_policy_server_url']:'';

				}
				if($error_msg == '')
				{
					//To check add OR view query
					if(Input::has('query') && Input::get('query') == 'add')
					{
						return $this->addQuery($p_details);
					}
					else if(Input::has('query') && Input::get('query') == 'view')
					{
						return $this->viewQuery($p_details);
					}
					else if(Input::has('status'))
					{
						return $this->changeQueryStatus($p_details);
					}

					//To add view count
					if($p_details['product_status'] == 'Ok')
					{
						$view_cookie = $this->ViewProductService->addProductViewCount($p_details['id'], $prod_obj);
						/*if($view_cookie != '')
						{
							$url = $this->ViewProductService->getProductViewURL($p_details['id'], $p_details);
							return Redirect::to($url)->withCookie($view_cookie);
						}*/
					}
					$product_title = $p_details['product_name'];

					$d_arr['product_attr'] = $product_attr_label = $prod_obj->getProductCategoryAttributeValueLabels($p_details['id'], $p_details['product_category_id']);

					//$d_arr['product_attr'] = $this->ViewProductService->getCategories($p_details['product_category_id']);
					//$d_arr['shop_item_details'] = $this->ViewProductService->fetchShopItems($p_details['product_user_id'], $p_details['id'], $this->shop_items_limit);
					//if(Config::get('generalConfig.user_allow_to_add_product')) {
						//To get shop information
						$d_arr['shop_details'] = $shop_obj->getShopDetails($p_details['product_user_id']);

						$prod_obj->setFilterProductCode('');
						$prod_obj->setFilterProductStatus('Ok');
						$prod_obj->setProductPagination(4);
						$prod_obj->setFilterProductExpiry(true);
						$d_arr['shop_item_details'] = $prod_obj->getProductsList($p_details['product_user_id']);
					//}
					$d_arr['slider_default_img'] = $this->ViewProductService->fetchSliderDefaultImage($p_details['id'], $prod_obj);
					$d_arr['slider_preview_img'] = $this->ViewProductService->fetchSliderPreviewImage($p_details['id'], $prod_obj);
					//$d_arr['recent_replies'] = $this->ViewProductService->populateRecentQuestionsAndAnswers($p_details['id']);
					//$d_arr['recent_reviews'] = $this->ViewProductService->populateRecentPurchasesAndReviews($p_details['id'], $p_details['product_user_id']);
					//echo "<pre>";print_r($d_arr);echo "</pre>";

					//Product tag list
					$d_arr['tag_arr'] = array();
					if($p_details['product_tags'] != '')
					{
						$tags_arr = explode(',', $p_details['product_tags']);
						$d_arr['tag_arr'] = array_filter($tags_arr, function($val){ return (trim($val) == '')? false:true; });
					}
					$shipping_country_id = CUtil::getShippingCountry();
					$shipping_companies_list = $shipping_template_service->getShippingTemplatesCompaniesListWithDetails($p_details['shipping_template'],0,$p_details['id'],1, array('country_id' => $shipping_country_id));

					//Log::info(print_r($shipping_companies_list, 1));
					$shipping_company_id = 0;
					$shipping_company = '';
					$shipping_fee = 0;
					$shipping_company_err_msg = '';
					if(count($shipping_companies_list) > 0) {
						//echo "<pre>";print_r($shipping_companies_list);echo "</pre>";
						$min_index = $shipping_template_service->min_with_key($shipping_companies_list, 'shipping_fee');
						//echo "<br>min_index: ".$min_index;
						if($min_index!='')
						{

							$shipping_company_id = isset($shipping_companies_list[$min_index]['company_id']) ? $shipping_companies_list[$min_index]['company_id'] : 0;
							//echo "<pre>";print_r($shipping_company_id);echo "</pre>";
							$shipping_company = isset($shipping_companies_list[$min_index]['company_name']) ? $shipping_companies_list[$min_index]['company_name'] : '';
							$shipping_fee = isset($shipping_companies_list[$min_index]['shipping_fee']) ? $shipping_companies_list[$min_index]['shipping_fee'] : 0;
							$shipping_company_err_msg = isset($shipping_companies_list[$min_index]['error_message']) ? $shipping_companies_list[$min_index]['error_message'] : '';
						}
						else
						{
							$shipping_company_id = isset($shipping_companies_list[0]['company_id']) ? $shipping_companies_list[0]['company_id'] : 0;
							$shipping_company = isset($shipping_companies_list[0]['company_name']) ? $shipping_companies_list[0]['company_name'] : '';
							$shipping_fee = isset($shipping_companies_list[0]['shipping_fee']) ? $shipping_companies_list[0]['shipping_fee'] : 0;
							$shipping_company_err_msg = isset($shipping_companies_list[0]['error_message']) ? $shipping_companies_list[0]['error_message'] : '';
						}
						/*
						foreach($shipping_companies_list as $shipping) {
							if(!isset($shipping['error_message']) || $shipping['error_message'] == '') {
								if($shipping['shipping_fee'] > 0 && $shipping['shipping_fee'] < $shipping_fee) {
									$shipping_company_id = $shipping['company_id'];
									$shipping_company = $shipping['company_name'];
									$shipping_fee = $shipping['shipping_fee'];
									$shipping_company_err_msg = '';
								}
							}
						}*/
					}
					$d_arr['default_shipping_company_id'] = $shipping_company_id;
					$d_arr['default_shipping_company'] = $shipping_company;
					$d_arr['shipping_company_err_msg'] = $shipping_company_err_msg;


	    			$shipping_country_name = Products::getCountryNameByCountryId($shipping_country_id);
	    			$d_arr['default_shipping_country_id'] = $shipping_country_id;
	    			$d_arr['default_shipping_country'] = $shipping_country_name;
	    			$d_arr['default_shipping_fee'] = $shipping_fee;

	    			$feed_back = $feedback_service->getFeedbackCountBySellerId($p_details['product_user_id']);
	    			$feed_back_cnt = array_sum($feed_back);
					$feed_back_rate = 0;
					$positive_feed = $feed_back['Positive'] + $feed_back['Neutral'];
					if($positive_feed > 0) {
						$feed_back_rate = round( ($feed_back_cnt / $positive_feed ) * 100 );
					}
					$d_arr['feed_back_cnt'] = $feed_back_cnt;
					$d_arr['feed_back_rate'] = $feed_back_rate;
					$d_arr['rating_avg'] = $rating_avg = $feedback_service->getAvgRatingForSeller($p_details['product_user_id']);
					$d_arr['prod_fav_cnt'] = $productFavoritesService->totalFavoritesByProduct($p_details['id']);
					$d_arr['feed_back_list'] = $feedback_service->getFeedbackListBySellerId($p_details['product_user_id'], Config::get('generalConfig.prod_view_review_list_count'));
					$d_arr['is_favorite_product'] = $productFavoritesService->isFavoriteProduct($p_details['id'], $logged_user_id);
				}
				//$d_arr['rating_info'] = $this->ViewProductService->getMpProductRating($p_details['id'], $logged_user_id);
				//$d_arr['liked_info'] = $this->ViewProductService->getMpProductLikedInfo($p_details['id'], $logged_user_id);
				//$breadcrumb_arr = $this->ViewProductService->getProductViewBreadcrumbArr($p_details['product_category_id']);
				//Set Meta details
				$get_common_meta_values = Cutil::getCommonMetaValues('customer-product-view');
				if($get_common_meta_values)
				{
					$meta_title = (trim($p_details['meta_title']) != '')? $p_details['meta_title'].' - VAR_SITE_NAME': ((trim($p_details['product_name'])!='')?str_replace('PRODUCT_NAME', $p_details['product_name'], $get_common_meta_values['meta_title']):str_replace('PRODUCT_NAME', '', $get_common_meta_values['meta_title']));

					$meta_keyword = (trim($p_details['meta_keyword']) != '')? $p_details['meta_keyword']: ((trim($p_details['product_name'])!='')?str_replace('PRODUCT_NAME', $p_details['product_name'], $get_common_meta_values['meta_keyword']):str_replace('PRODUCT_NAME', '', $get_common_meta_values['meta_keyword']));
					$meta_description = (trim($p_details['meta_description']) != '')? $p_details['meta_description']: ((trim($p_details['product_name'])!='')?str_replace('PRODUCT_NAME', $p_details['product_name'], $get_common_meta_values['meta_description']):str_replace('PRODUCT_NAME', '', $get_common_meta_values['meta_description']));
					$this->header->setMetaTitle($meta_title);
					$this->header->setMetaKeyword($meta_keyword);
					$this->header->setMetaDescription($meta_description);
				}

		    	// Set canonical URL
		    	//$url_slug = $p_details['product_code'].'-'.$p_details['url_slug'];
				//$canonicalUrl = URL('item/'.$url_slug);
				//$this->header->setViewCanonicalUrl($canonicalUrl);

				$view_url = $this->ViewProductService->getProductViewURLNew($p_details['id'], $p_details);
				$comments_url = $view_url."/comments";
				$support_url = $view_url."/support";
				$d_arr['view_url'] = $view_url;
				$d_arr['comments_url'] = $comments_url;
				$d_arr['support_url'] = $support_url;
			}
			else
			{
				$error_msg = trans("viewProduct.invalid_url_slug");
			}
		}
		catch(Exception $e)
		{
			$error_msg = $e->getMessage();
		}

		//echo "<pre>";print_r($d_arr);echo "</pre>";
		$d_arr['error_msg'] = $error_msg;
		$d_arr['alert_msg'] = $alert_msg;
		$service_obj = $this->ViewProductService;
		//$mpListProductService = new MpListProductService();
		//$disp_category = $mpListProductService->fetchDisplayCategorys();
		$product_this_obj = $this;
		$d_arr['show_variation'] = 0;
		if(isset($p_details['use_variation']) && $p_details['use_variation'] > 0 && $p_details['is_downloadable_product'] == 'No')
		{
			$d_arr['allow_variation'] = (Config::has('plugin.variations') && Config::get('plugin.variations')) ? 1 : 0;
			if($d_arr['allow_variation'])
			{
				$this->variation_service->populateAttributeLabelsList($p_details['id']);
				$variation_det = $this->variation_service->populateVariationAttributes($p_details['id'], 0,$p_details['product_user_id']);
				$d_arr['variation_det'] = $variation_det;
				$d_arr['show_variation'] = (isset($variation_det['show_variation']) && $variation_det['show_variation'] > 0 ) ? 1 : 0;
			}
		}
		return View::make('viewProduct', compact('d_arr', 'product_code', 'breadcrumb_arr', 'product_title', 'preview_mode', 'p_details', 'service_obj', 'prod_obj', 'stock_count','productService', 'productFavoritesService', 'shopFavoritesService', 'product_this_obj', 'logged_user_id', 'product_report'));
    }

	public function addReplyComment()
	{
		$error_message = $success_message = "";
		$url = Url::to('products');
		$thread_id = Input::get('thread_id', 0);
		$product_code = Input::get('prd', 0);
		if($product_code)
		{
			$rules = $messages = array();
			$rules = array('reply_message_'.$thread_id  => 'required');
			$validator = Validator::make(Input::all(), $rules, $messages);
			if ( $validator->passes())
			{
				$reply_id = 0;
				$p_details = Product::whereRaw('product_code = ? AND product_status != ?', array($product_code, 'Deleted'))->first();
				if(count($p_details) > 0)
				{
					$return_arr = $this->chkIsAllowConversation($p_details['id'], $p_details, $thread_id);
					if($return_arr['allow_to_reply'])
					{
						$input_arr = Input::All();
						$logged_user_id = (getAuthUser())? getAuthUser()->user_id : 0;
						$date_now = date('Y-m-d H:i:s');

						if($thread_id > 0)
						{
							$message_thread  = MpProductComments::whereRaw('id = ? AND is_deleted = ?', array($thread_id, 0))->first();
							if(count($message_thread) > 0)
							{
								$visibility = ($message_thread['visibility'] == 'Private')? 'Private' : Input::get('visibility_'.$thread_id, 'Public');
								$mesage_data = array(	'date_added' => $date_now,
				                            'product_id' => $message_thread['product_id'],
				                            'thread_id'=> $thread_id,
				                            'notes' => Input::get('reply_message_'.$thread_id),
				                            'added_by_user_id' => $logged_user_id,
				                            'visibility_status' => $visibility,
				                            'is_deleted' => 0);
								$message_comment_replies = new MpProductCommentReplies();
								$reply_id = $message_comment_replies->insertGetId($mesage_data);

								$reply_count = $message_thread['total_replies'];
								//To update reply count and reply status
								MpProductComments::where('id', $thread_id)->update(array("last_replied_user_id" => $logged_user_id, "last_updated" => $date_now,
															 'total_replies' => (int)$reply_count + 1));
							}
							$success_message =  trans("viewProduct.reply_message_added_success");
						    $key = "NewReply";
						}
						else
						{
							$visibility = Input::get('visibility_'.$thread_id, 'Public');
							$mesage_data = array(	'date_added' => $date_now,
				                            'product_id' => $p_details['id'],
				                            'user_id' => $logged_user_id,
				                            'seller_id' => $p_details['product_user_id'],
				                            'message' => Input::get('reply_message_'.$thread_id),
											'last_updated' => $date_now,
											'visibility' => $visibility,
				                            'is_deleted' => 0);
							$message_comments = new MpProductComments();
							$thread_id = $message_comments->insertGetId($mesage_data);
							$success_message =  trans("viewProduct.comment_added_success");
							$key = "NewComment";
						}
						// Send user notification mail details for admin and Thread owner / Purchased user
						$this->ViewProductService->sendProductConversationMail($p_details, $thread_id, $key, $reply_id);
					}
					$url = $this->ViewProductService->getProductViewURL($p_details['id'], $p_details);
					$url = $url."/comments";
				}
				else
				{
					$error_message = trans("viewProduct.invalid_url_slug");
				}
			}
			else
			{
				$error_message = trans("viewProduct.invalid_url_slug");
			}
		}
		return Redirect::to($url)->with('success_message', $success_message)->with('error_message', $error_message);
	}

	public function listReplyComment()
	{
		$thread_id = Input::get('thread_id', 0);
		$thread_details_arr = MpProductCommentReplies::whereRaw("id = ? AND is_deleted = ?", array($thread_id, 0))->first();
		if(count($thread_details_arr) > 0)
		{
			$d_arr = array();
			$message_replies = $this->ViewProductService->getProductReplies($thread_id);
			$thread_added_by = $thread_details_arr['seller_id'];
			$product_added_by = $thread_details_arr['user_id'];
			$thread_id = $thread_details_arr['id'];
			$return_arr = $this->chkIsAllowConversation($thread_details_arr['request_id'], array(), $thread_id);
			$d_arr['allow_to_suggestion'] = $return_arr['allow_to_suggestion'];
			$d_arr['allow_to_message'] = $return_arr['allow_to_message'];
			$d_arr['allow_to_conversation'] = $return_arr['allow_to_conversation'];
			$d_arr['allow_to_reply'] = $return_arr['allow_to_reply'];
			$total_replies = $thread_details_arr['total_replies'];
			return View::make('mp_product/productThreadReply', compact('message_replies', 'thread_added_by', 'product_added_by', 'thread_id', 'd_arr', 'total_replies'));
		}
		return ;
	}


	public function chkIsAllowConversation($product_id = 0, $p_details = array(), $thread_id = 0)
	{
		$allow_to_suggestion = $allow_to_message = $allow_to_conversation = $allow_to_reply = $allow_to_communicate = false;
		$conversation_alert_msg = '';
		$message_thread_id = 0;
		$reply_suggestion_arr = array();
		if(isLoggedin())
			$allow_to_reply = true;

//		echo "<br>Product ID",$product_id;
//		echo "<br>thread_id",$thread_id;

		/*
		if(isLoggedin())
		{
			$logged_user_id = (Sentry::getUser())? Sentry::getUser()->id : 0;
			//If request details not found, then get the request details
			if(count($req_details) == 0)
			{
				$req_details = Requests::whereRaw('id = ?', array($request_id))->first();
			}
			if(count($req_details) > 0)
			{
				//$allow_to_suggestion = $allow_to_message = $allow_to_conversation = true;
				$allow_to_conversation =  true;
				$request_message_arr = array();
				$request_thread_arr = array();
				if($req_details['request_status'] != 'active')
				{
					//Don't allow any conversation
					$allow_to_suggestion = $allow_to_message = $allow_to_conversation =  false;
					$conversation_alert_msg = trans('request/form.view-request.recommend_own_request');
					if($logged_user_id != $req_details['user_id'])
					{
						if($req_details['request_status'] == 'closed')
						{
							$conversation_alert_msg = trans('request/form.view-request.not_allow_conversation_closed');
						}
						else if($req_details['request_status'] == 'booked_closed')
						{
							$conversation_alert_msg = trans('request/form.view-request.not_allow_conversation_booked');
						}
					}
				}
				else if($logged_user_id == $req_details['user_id'])
				{
					$conversation_alert_msg = trans('request/form.view-request.recommend_own_request');
					$allow_to_message = $allow_to_suggestion = false;
					$allow_to_reply =  true;
				}
				else
				{
					$allow_to_communicate = $this->viewRequestService->checkIsUserAllowedToSuggest($req_details['id'], $logged_user_id);
					//To operator validation...
					//To check user has submit quote
					//$allow_to_reply =  ($allow_to_communicate == 1) ? true : false;
					$allow_to_reply =  true;
					$request_thread_arr = RequestMessageThread::whereRaw('operator_id = ? AND request_id = ? AND is_quote = ? AND is_deleted = ?', array($logged_user_id, $req_details['id'], 'Yes', 0))->first();
					if(count($request_thread_arr) > 0)
					{
						$allow_to_suggestion = false;
						//To render quote details..
						$quote_details =  RequestQuotes::whereRaw('id = ? ', array($request_thread_arr['quote_id']))->first();
						$allow_to_edit_quote = ($quote_details['status'] == 'paid') ? 0 : 1;
						$reply_suggestion_arr = array(
												'adult_count_'.$request_thread_arr['id'] => $quote_details['adult_count'],
												'child_count_'.$request_thread_arr['id'] => $quote_details['child_count'],
												'tour_duration_'.$request_thread_arr['id'] => $quote_details['tour_duration'],
												'tour_date_'.$request_thread_arr['id'] => date('m-d-Y', strtotime($quote_details['tour_date'])),
												'quote_amount_'.$request_thread_arr['id'] => $quote_details['quote_amount'],
												'format_tour_date_'.$request_thread_arr['id'] => date('M d, Y', strtotime($quote_details['tour_date'])),
												'quote_currency_'.$request_thread_arr['id'] => $quote_details['quote_currency']
												);
					}
					else
					{
						$allow_to_suggestion = true;
					}
					//To check user has submit message
					$request_message_arr = RequestMessageThread::whereRaw('operator_id = ? AND request_id = ? AND is_quote = ? AND is_deleted = ?', array($logged_user_id, $req_details['id'], 'No', 0))->first();
					if(count($request_message_arr) > 0)
					{
						$allow_to_message = false;
					}
					else
					{
						$allow_to_message = true;
					}
				}
				//To get operator thread id to add server side validation
				$operator_quote_thread_id = ((count($request_thread_arr) > 0)) ? $request_thread_arr['id'] : 0;
				$message_thread_id = ((count($request_message_arr) > 0)) ? $request_message_arr['id'] : 0;
			}
		}
		*/
		$return_arr = array('allow_to_suggestion' => $allow_to_suggestion,
									'allow_to_message' => $allow_to_message,
									'allow_to_conversation' => $allow_to_conversation,
									'conversation_alert_msg' => $conversation_alert_msg,
									'allow_to_reply' => $allow_to_reply,
									'message_thread_id' => $message_thread_id,
									'allow_to_communicate' => $allow_to_communicate);
		return $return_arr;
	}


	public function populateProductComments($product_id, $thread_id = 0, $sort_by = 'recent')
	{
		//To get user request thread message/suggestion
		$query  = MpProductComments::whereRaw('product_id = ? AND is_deleted = ? AND status = ? ',array($product_id, 0, 'Active'));
		if($thread_id != 0)
		{
			$query->where('id', '=', $thread_id);
		}
		if($sort_by == 'active')
		{
			$query->orderBy('total_replies', 'DESC');
		}
		else
		{
			$query->orderBy('last_updated', 'DESC');
		}
		return $query->paginate(Config::get("generalConfig.product_comment_per_page_list"));
	}

    public function addQuery($p_details)
    {
    	if(!isLoggedIn())
    	{
    		return Redirect::to('users/login');
    	}
		$alert_msg = $error_msg = '';
		$action = 'add';
		$discussion_id = (Session::has('discussion_id'))? Session::get('discussion_id') : 0;
		$d_arr = $breadcrumb_arr = array();
		$page_title = trans("viewProduct.page_title");
		$reply = 0;
		if(Input::has('id'))
		{
			$discussion_id = Input::get('id');
			$reply = $this->ViewProductService->chkIsProductReply($discussion_id);
			if(!$reply)
			{
				$error_msg = trans("viewProduct.invalid_url_slug");
			}
		}
		else
		{
			if($this->logged_user_id == $p_details['product_user_id'])
			{
				$error_msg = trans("viewProduct.own_product_add_query");
			}
		}


		if($error_msg == '')
		{

		}

		$item_url = $this->ViewProductService->getProductViewURL($p_details['id'], $p_details);
		$bc_name = (strlen($p_details['product_name']) > 25 ) ?  substr($p_details['product_name'], 0, 25).'...' : $p_details['product_name'];
		$breadcrumb_arr = array($bc_name => $item_url, $page_title => '');
		$d_arr['error_msg'] = $error_msg;
		$d_arr['alert_msg'] = $alert_msg;
		$d_arr['discussion_id'] = $discussion_id;
		$d_arr['reply'] = $reply;
		if($d_arr['discussion_id'] > 0)
		{
			$d_arr['discussion_replies'] = $this->ViewProductService->populateDiscussionReplies($p_details['id'], $d_arr['discussion_id']);
		}
		$d_arr['p_id'] = $p_details['id'];
		$d_arr['shop_details'] = $this->ViewProductService->getShopDetails($p_details['product_user_id']);
		$service_obj = $this->ViewProductService;
		return View::make('mp_product/productQuery', compact('d_arr', 'breadcrumb_arr', 'page_title', 'p_details', 'service_obj', 'item_url', 'action'));

	}

	public function viewQuery($p_details)
    {
    	$alert_msg = $error_msg = '';
    	$action = 'view';
		$d_arr = $breadcrumb_arr = array();
		$page_title = trans("viewProduct.page_title");


		$item_url = $this->ViewProductService->getProductViewURL($p_details['id'], $p_details);
		$breadcrumb_arr = array($p_details['product_name'] => $item_url, $page_title => '');
		$d_arr['error_msg'] = $error_msg;
		$d_arr['alert_msg'] = $alert_msg;
		$d_arr['discussion_id'] = 0;
		$d_arr['p_id'] = $p_details['id'];
		$service_obj = $this->ViewProductService;
		$d_arr['shop_details'] = $this->ViewProductService->getShopDetails($p_details['product_user_id']);
		$d_arr['recent_replies'] = $this->ViewProductService->populateQuestionsAndAnswers($p_details['id']);

		return View::make('mp_product/productQuery', compact('d_arr', 'breadcrumb_arr', 'page_title', 'p_details', 'service_obj', 'item_url', 'action'));
	}

	public function changeQueryStatus($p_details)
    {
    	$alert_msg = $error_msg = '';
    	$action = 'view';
		$d_arr = array();
		$discussion_id = Input::get('discussion_id');
		$status = Input::get('status');
		$discussion_details = $this->ViewProductService->chkIsValidDiscussion($discussion_id);
		$d_arr['blocks'] = array('block_mark_as_private_question' => false, 'block_unmark_from_private_question' => false);
		if($discussion_details)
		{
			if($status == 'mark_as_private')
			{
				if(isset($discussion_details['is_private']) && $discussion_details['is_private'] == 1)
				{
					$error_msg = trans("viewProduct.private_question_already_marked_as_private_msg");
				}
				else
				{
					$d_arr['blocks']['block_mark_as_private_question'] = true;
				}
			}
			elseif($status == 'unmark_from_private')
			{
				if(isset($discussion_details['is_private']) && $discussion_details['is_private'] == 0)
				{
					$error_msg = trans("viewProduct.private_question_already_unmarked_from_private_msg");
				}
				else
				{
					$d_arr['blocks']['block_unmark_from_private_question'] = true;
				}
			}
			else
			{
				$error_msg = trans("viewProduct.private_question_invalid_action");
			}
			$d_arr['p_id'] = $discussion_details['item_id'];
		}

		$d_arr['error_msg'] = $error_msg;
		$d_arr['alert_msg'] = $alert_msg;
		$d_arr['discussion_id'] = $discussion_id;

		$service_obj = $this->ViewProductService;
		return View::make('mp_product/privateQuestions', compact('d_arr', 'service_obj'));
	}

	public function postIndex()
	{
		$discussion_id = Input::get('discussion_id', 0);
		$p_id = Input::get('p_id', 0);
		if(Input::has('unmark_from_private_submit'))
		{
			$p_details = Product::whereRaw('id = ? AND product_status = ?', array($p_id, 'Ok') )->first();
			$item_url = $this->ViewProductService->getProductViewURL($p_details['id'], $p_details);
			if(count($p_details) > 0)
			{
				$data_arr = array('discussion_id' => $discussion_id, 'is_private' => 0);
				$this->ViewProductService->updateDiscussionPrivateStatus($data_arr);
				return Redirect::to($item_url.'?discussion_id='.$discussion_id.'&status=unmark_from_private')
								->with('unmark_from_private_success', true)
								->with('success_message', trans('viewProduct.private_question_unmarked_from_private_success_msg'));
			}


		}
		elseif(Input::has('mark_as_private_submit'))
		{
			$p_details = Product::whereRaw('id = ? AND product_status = ?', array($p_id, 'Ok') )->first();
			$item_url = $this->ViewProductService->getProductViewURL($p_details['id'], $p_details);
			if(count($p_details) > 0)
			{
				$data_arr = array('discussion_id' => $discussion_id, 'is_private' => 1);
				$this->ViewProductService->updateDiscussionPrivateStatus($data_arr);
				return Redirect::to($item_url.'?discussion_id='.$discussion_id.'&status=mark_as_private')
								->with('mark_as_private_success', true)
								->with('success_message', trans('viewProduct.private_question_marked_as_private_success_msg'));
			}
		}
		else
		{
			//To validate product id
			$input_arr = Input::All();
			if(is_numeric($p_id))
			{
				$p_details = Product::whereRaw('id = ? AND product_status = ?', array($p_id, 'Ok') )->first();
				$item_url = $this->ViewProductService->getProductViewURL($p_details['id'], $p_details);
				if(count($p_details) > 0)
				{
					$is_allowed_access = ($discussion_id) ? $this->ViewProductService->chkDiscussionAccess($discussion_id) : $this->ViewProductService->chkProductQueryAccess($p_details);
					if($is_allowed_access)
			    	{
						//To add new Or reply to existing questions..
						$validator_arr = $this->ViewProductService->getProductQueryValidationRules();
						$validator = Validator::make($input_arr, $validator_arr['rules'], $validator_arr['messages']);
						if($validator->passes())
						{
							$suc_message = trans('viewProduct.reply_post_msg');
							$params = '?query=add';
							if($discussion_id)
							{
								//To add reply for notes
								$params .= '&id='.$discussion_id ;
								$this->ViewProductService->replyProductQuery($input_arr);
							}
							else
							{
								//To add notes
								$discussion_id = $this->ViewProductService->addProductQuery($input_arr, $p_details);
								$suc_message = trans('viewProduct.buyer_note_success');
							}
							return Redirect::to($item_url.$params)
													->with('discussion_id', $discussion_id)
													->with('success_message', $suc_message);
						}
						else
						{
							return Redirect::to($item_url.'?query=add')->with('error_message', trans('common.correct_errors'))->withInput()->withErrors($validator);
						}
					}
			    	return Redirect::to($item_url.'?query=add')->with('error_message', trans('viewProduct.invalid_url_slug'));
				}
			}
		}

	}
	//Added by mohamed_158at11
	public function postRating()
	{
		$inputs = Input::all();
		if(empty($inputs['product_code']) || empty($inputs['rating']))
		{
			echo "error|".trans('viewProduct.issue_in_rating');exit;
		}
		$p_arr = Product::whereRaw('product_code = ?', array($inputs['product_code']))->first();
		if(empty($p_arr))
		{
			echo "error|".trans('viewProduct.invalid_product');exit;
		}
		if(!isLoggedIn())
    	{
    		Session::put('login_redirect_url', URL::to('script/'.$inputs['product_code'].'-'.$p_arr['url_slug']));
    		echo "redirect|".Url::to('users/login');exit;
    	}
    	$is_bought = $this->ViewProductService->isUserBoughtProduct($p_arr['id'], $inputs['user_id']);
    	if(!$is_bought)
    	{
			echo "error|".trans('viewProduct.rate_after_purchase');exit;
		}
		$inputs['mp_product_id'] = $p_arr['id'];
		$rate_id = $this->ViewProductService->addRating($inputs);
		if($rate_id)
		{
			echo "success|".trans('viewProduct.thanks_voting');exit;
		}
		else
		{
			echo "reload|".trans('viewProduct.rating_updated');exit;
		}
	//	echo "<pre>";print_r($inputs);echo "</pre>";
		exit;

	}

	public function postLike()
	{
		$inputs = Input::all();
		if(empty($inputs['product_code']) || empty($inputs['liked']))
		{
			echo "error|".trans('viewProduct.issue_in_like');exit;
		}
		$p_arr = Product::whereRaw('product_code = ?', array($inputs['product_code']))->first();
		if(empty($p_arr))
		{
			echo "error|".trans('viewProduct.invalid_product');exit;
		}
		if(!isLoggedIn())
    	{
    		Session::put('login_redirect_url', URL::to('script/'.$inputs['product_code'].'-'.$p_arr['url_slug']));
    		echo "redirect|".Url::to('users/login');exit;
    	}
    	$is_liked = $this->ViewProductService->isUserLikedAlready($p_arr['id'], $inputs['user_id']);
    	if($is_liked)
    	{
			echo "error|".trans('viewProduct.already_liked');exit;
		}
		$inputs['product_id'] = $p_arr['id'];
		$inputs['date_added'] = date('Y-m-d H:i:s');
		$like_id = $this->ViewProductService->addLike($inputs);
		if($like_id)
		{
			echo "success|".trans('viewProduct.thanks_voting');exit;
		}
		else
		{
			echo "reload|".trans('viewProduct.thanks_like');exit;
		}
		exit;
	}

	public function getDemo($url_slug)
	{
		$product_code = $this->ViewProductService->getProductCode($url_slug);
		$page_title = '';
		$d_arr = array();
		$p_details = array();
		$prod_obj = Products::initialize();
		$prod_obj->setFilterProductCode($product_code);
		try
		{
			$p_details = $prod_obj->getProductDetails();
		}
		catch(Exception $e){
			echo $e->getMessage(); exit;
			$p_details = array();
		}

		if(count($p_details) > 0)
		{
			$page_title = str_replace('VAR_SCRIPT_NAME', $p_details['product_name'], trans('viewProductURL.demo_page_title'));
			//$category_info_arr = $this->ViewProductService->getProductViewBreadcrumbArr($p_arr['product_category_id']);
			//$d_arr['category_info'] = $category_info_arr;
			$d_arr['demo_details'] = $p_details['demo_details'];
			$d_arr['page_type'] = 'demo';
			$d_arr['iframe_url'] = $p_details['demo_url'];
			return View::make('viewProductURL', compact('p_details', 'd_arr', 'page_title'));
		}
		else
		{
			return Redirect::action('ViewProductController@getIndex',$url_slug);
		}
	}
}