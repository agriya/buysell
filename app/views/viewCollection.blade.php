@extends('base')
@section('content')
	<?php $all_user_details = array(); $all_user_image_details = array(); $logged_user_id = BasicCUtil::getLoggedUserId(); ?>
	<h1>{{$collection_details->collection_name}}</h1>
	<div id="error_msg_div"></div>
	<div class="row">
		<!-- BEGIN: SIDE BAR BLOCK -->
		<div class="col-md-3 blog-sidebar">
        	<div class="well">
                @if(CUtil::isMember())
                    @if($logged_user_id != $collection_details->user_id)
                    	<div class="well-border fav-btn">
							<?php $isFavoriteCollection = $collectionFavoritesService->isFavoriteCollection($collection_details->id, $logged_user_id);  ?>
                            @if($isFavoriteCollection)
                                <a class="btn btn-default js-addto-favorite-collection" data-collectionid="{{$collection_details->id}}" data-userid="{{$collection_details->user_id}}" href="javascript:;"><i class="fa fa-heart text-pink"></i> {{ Lang::get('collection.unfavorite') }}</a>
                            @else
                                <a class="btn btn-default js-addto-favorite-collection" data-collectionid="{{$collection_details->id}}" data-userid="{{$collection_details->user_id}}" href="javascript:;"><i class="fa fa-heart text-muted"></i> {{ Lang::get('collection.favorite') }}</a>
                            @endif
                        </div>
                    @endif
                @else
                    <?php $login_url = URL::to('users/login?form_type=selLogin'); ?>
                    <div class="well-border fav-btn">
                        <a class="fn_signuppop btn btn-default" href="{{ $login_url }}"><i class="fa fa-heart text-muted"></i> {{ Lang::get('collection.favorite') }}</a>
                    </div>
                @endif

                <!-- BEGIN: STATS -->
                <div class="well-border">
                    <h4>{{ Lang::get('collection.stats') }}</h4>
                    <ul class="list-unstyled no-margin">
                        <li>{{$collection_details->total_views}} {{ Lang::choice('collection.views', $collection_details->total_views) }}</li>
                        <li>{{$collection_details->total_clicks}} {{ Lang::choice('collection.clicks', $collection_details->total_clicks) }}</li>
                    </ul>
                </div>
                <!-- END: STATS -->

                <!-- BEGIN: CURATOR TOOLS -->
                <h4>{{ Lang::get('collection.curator_tools') }}</h4>
                <ul class="list-unstyled no-margin">
                    @if(CUtil::isMember())
                        @if($logged_user_id == $collection_details->user_id)
                            <li><a title="Edit Collection" href="{{URL::action('MyCollectionsController@getUpdate', $collection_details->id)}}">{{ Lang::get('common.edit') }}</a></li>
                        @endif
                    @endif
                    <li><a title="Create a list" href="{{URL::action('MyCollectionsController@getAdd')}}">{{ Lang::get('collection.add_collection') }}</a></li>
                    <li><a title="All collections list" href="{{URL::action('CollectionsController@getIndex')}}">{{ Lang::get('collection.all_collections_list') }}</a></li>
                    @if(CUtil::isMember())
                        <li><a title="Your collections list" href="{{URL::action('CollectionsController@getIndex', array('user_id'=>$logged_user_id))}}">{{ Lang::get('collection.your_collections_list') }}</span></a></li>
                    @endif
                </ul>
                <!-- END: CURATOR TOOLS -->
            </div>
		</div>
		<!-- END: SIDE BAR BLOCK -->

		<div class="col-md-9">
			<!-- BEGIN: ALERT BLOCK -->
			@if(Session::has('error_message') && Session::get('error_message') != '')
				<div class="note note-danger">{{ Session::get('error_message') }}</div>
				<?php Session::forget('error_message'); ?>
			@endif
			@if(Session::has('success_message') && Session::get('success_message') != '')
				<div class="note note-success">{{ Session::get('success_message') }}</div>
				<?php Session::forget('success_message'); ?>
			@endif
			<!-- END: ALERT BLOCK -->

			<!-- ENGIN: ITEM LIST PRODUCT -->
			@if(isset($collection_details->collection_products) && !empty($collection_details->collection_products))
				<div id="selViewAllItems">
					<ul class="list-unstyled row">
						@foreach($collection_details->collection_products as $product_id)
							<?php
								$product = Products::initialize($product_id);
								$product->setIncludeDeleted(true);
								$product->setIncludeBlockedUserProducts(true);
								$product_det = $product->getProductDetails();

								$p_img_arr = $product->getProductImage($product_id);
								$p_thumb_img = $productService->getProductDefaultThumbImage($product_id, 'thumb', $p_img_arr);
								$view_url = $productService->getProductViewURL($product_id, $product_det);
								$price = $productService->formatProductPriceNew($product_det);

								$shop_details = array();
								if(!isset($all_shop_details[$product_det['product_user_id']])) {
									$shop_obj->setIncludeBlockedUserShop(true);
									$all_shop_details[$product_det['product_user_id']] = $shop_obj->getShopDetails($product_det['product_user_id']);
								}
								$shop_details = $all_shop_details[$product_det['product_user_id']];
							?>
							<li class="col-md-4">
								<div class="bs-prodet product-gridview">
									{{ CUtil::showFeaturedProductIcon($product_id, $product_det); }}
                                    <ul id="fav_block_{{ $product_id }}" class="list-unstyled favset-icon">
										<?php
											$prod_fav_service = new ProductFavoritesService();
											$is_favorite_prod = $prod_fav_service->isFavoriteProduct($product_id, $logged_user_id);
											$login_url = URL::to('users/login?form_type=selLogin');
										?>
                                        <li>
	                                        @if(CUtil::isMember())
						                        @if($is_favorite_prod)
						                    		<a href="javascript:void(0);" data-productid="{{$product_id}}" class="js-addto-favorite-heart"><i class="fa fa-heart  text-pink"></i></a>
						                    	@else
						                    		<a href="javascript:void(0);" data-productid="{{$product_id}}" class="js-addto-favorite-heart"><i class="fa fa-heart  text-muted"></i></a>
						                    	@endif
						                    @else
						    					<a href="{{ $login_url }}" class="fn_signuppop"><i class="fa fa-heart"></i></a>
						                    @endif
					                    </li>
                                        <li class="dropdown">
                                        	@if(CUtil::isMember())
												<a href="javascript:void(0);" id="fav_list_{{$product_id}}" data-productid="{{$product_id}}" data-toggle="dropdown" class="js-show-list"><span class="fa fa-list"></span> <i class="fa fa-caret-down"></i></a>
												<div id="fav_list_holder_{{$product_id}}" class="dropdown-menu custom-dropdown fnFavListHolder">
												<div>
											@else
						    					<a href="{{ $login_url }}" class="fn_signuppop"><span class="fa fa-list"></span> <i class="fa fa-caret-down"></i></a>
						                    @endif
                                        </li>
                                    </ul>
                                    <div class="prodet-img">
                                        <a href="{{$view_url}}" class="indexlist-img js-product-view">
                                            <img height="135" src="{{$p_thumb_img['image_url']}}" alt="{{{$product_det['product_name']}}}" title="{{{$product_det['product_name']}}}">
                                        </a>
                                    </div>
									<div class="prodet-info">
										<h3><a class="js-product-view" href="{{$view_url}}" title="{{{$product_det['product_name']}}}">{{{$product_det['product_name']}}}</a></h3>
										<!--<p class="pull-right mar0">
											<small>Free !!!</small>
										</p>-->
                                        @if($product_det['is_free_product'] == 'Yes')
                                            <p class="pull-right"><strong class="label label-primary label-free">{{ Lang::get('common.free') }}</strong></p>
                                        @else
                                            @if($price['disp_price'] && $price['disp_discount'])
                                                <p class="pull-right">{{ CUtil::convertAmountToCurrency($price['product']['discount'], Config::get('generalConfig.site_default_currency'), '', true) }}</p>
                                            @elseif($price['disp_price'])
                                                @if($price['product']['price'] > 0)
                                                    <p class="pull-right">{{ CUtil::convertAmountToCurrency($price['product']['price'], Config::get('generalConfig.site_default_currency'), '', true) }}</p>
                                                @else
                                                    <p class="pull-right"><strong class="label label-primary label-free">{{ Lang::get('common.free') }}</strong></p>
                                                @endif
                                            @endif
                                        @endif
										<a title="{{$shop_details['shop_name']}}" href="{{$shop_details['shop_url']}}" class="bs-title">{{$shop_details['shop_name']}}</a>
									</div>
								</div>
							</li>
						@endforeach
					</ul>
				</div>
			@endif
			<!-- END: ITEM LIST PRODUCT -->

			<!-- BEGIN: CURATOR -->
			<h2>{{ Lang::get('collection.curator') }}</h2>
			<?php $owner_profile_image = CUtil::getUserPersonalImage($collection_details->user_id, 'thumb');?>
			<div class="well margin-bottom-30">
				<div class="media">
					<div class="pull-left">
						<a class="imgsize-75X75 comment-imgbg" href="{{$collection_owner_details['profile_url']}}">
							<img title="webmaster" alt="{{$collection_owner_details['display_name']}}" src="{{$owner_profile_image['image_url']}}">
						</a>
					</div>
					<div class="media-body">
						<p>
							<a href="{{Url::action('CollectionsController@getIndex',array('user_code' => $collection_owner_details['user_code']) ) }}" class="pull-right">
								<small><i class="fa fa-eye"></i> {{ Lang::get('collection.view_user_collections', array('user_name' => $collection_owner_details['display_name'])) }} </small>
							</a>
							<small>
								<a href="{{$collection_owner_details['profile_url']}}" class="text-success"><strong>{{$collection_owner_details['display_name']}}</strong></a>
								<span class="text-muted">|</span>
								<span>{{$collection_details->created_at->diffForHumans()}}</span>
							</small>
						</p>
						<p>{{ nl2br($collection_details->collection_description) }}</p>
					</div>
				</div>
			</div>
			<!-- END: CURATOR -->

			<!-- BEGIN: COMMENT POST -->
			<div id="selShowCommentBlock">
				<h2 class="title-five">{{ Lang::get('collection.comments') }}</h2>
				<div class="well margin-bottom-30" id="selCommentBlock">
					<ul id="selCommentTab" summary="Collection Comments" class="list-unstyled margin-0 comment-list">
						@if(isset($collections_comments) && $collections_comments->count() > 0)
							@foreach($collections_comments as $comment)
								<?php
									$user_details = array();
									$user_image_details = array();
									if(!isset($all_user_details[$comment->user_id]))
										$all_user_details[$comment->user_id] = CUtil::getUserDetails($comment->user_id);
									if(!isset($all_user_image_details[$comment->id]))
										$all_user_image_details[$comment->id] = CUtil::getUserPersonalImage($comment->user_id, 'small');
									$user_details = $all_user_details[$comment->user_id];
									$user_image_details = $all_user_image_details[$comment->id];
								?>
								<li id="commentsection_{{$comment->id}}">
									<div class="media">
										<div class="pull-left">
											<a class="imgsize-75X75 comment-imgbg" href="{{$user_details['profile_url']}}">
												<img alt="{{$user_details['display_name']}}" title="user_details['display_name']" src="{{$user_image_details['image_url']}}">
											</a>
										</div>
										<div class="media-body">
											<p>
												<small>
													<a href="{{$user_details['profile_url']}}" class="text-success"><strong>{{$user_details['user_name']}}</strong></a>
													<span class="text-muted">|</span> <span>{{$comment->created_at->diffForHumans()}}</span>
												</small>
											</p>
											<div id="cmd2_1">
												<p id="CommentTxt_{{$comment->id}}">{{ nl2br($comment->comment) }}</p>
												<div style="display:none;" id="EditCommentFrm_{{$comment->id}}" class="js-editCommentFrm">
													<p>{{Form::textarea('comment', $comment->comment, array('id' => 'comment_'.$comment->id, 'rows' => '5', 'cols' => '80', 'class' => 'form-control')) }}</p>
													<p class="pull-left margin-right-5 margin-bottom-5">
														<a onclick="return updateComment('{{$comment->id}}')" class="btn btn-success btn-xs">
														<i class="fa fa-save"></i> {{ Lang::get('common.save') }}</a>
														<a onclick="return cancelEditComment('{{$comment->id}}')" class="btn default btn-xs">
														<i class="fa fa-times"></i> {{ Lang::get('common.cancel') }}</a>
													</p>
												</div>
												@if($comment->user_id == $logged_user_id)
													<span id="ViewEditComment_{{$comment->id}}" class="pull-left margin-right-5">
														<a href="javascript:void(0)" data-id="{{$comment->id}}" class="btn blue btn-xs js-editComment" title="{{ Lang::get('common.edit') }}">
														<i class="fa fa-edit"></i> {{ Lang::get('common.edit') }}</a>
													</span>
												@endif
												@if($comment->user_id == $logged_user_id || $collection_details->user_id == $logged_user_id)
													<span id="DeleteComment_{{$comment->id}}" class="pull-left">
														<a href="javascript:void(0)" onclick="doAction({{$comment->id}}, 'delete_comment')" class="btn btn-danger btn-xs" title="{{ Lang::get('common.delete') }}">
														<i class="fa fa-trash-o"></i> {{ Lang::get('common.delete') }}</a>
													</span>
												@endif
											</div>
										</div>
									</div>
								</li>
							@endforeach
						@else
							<p class="note note-info margin-0">{{ Lang::get('collection.no_comments_found') }}</p>
						@endif
					</ul>
				</div>

				@if(CUtil::isMember())
					<div class="well">
						<h2 class="title-one">{{ Lang::get('collection.post_your_comments') }}:</h2>
						<?php
							$user_details = array();
							$user_image_details = array();
							if(!isset($all_user_details[$logged_user_id]))
								$all_user_details[$logged_user_id] = CUtil::getUserDetails($logged_user_id);
							if(!isset($all_user_image_details[$logged_user_id]))
								$all_user_image_details[$logged_user_id] = CUtil::getUserPersonalImage($logged_user_id, 'small');

							$user_details = $all_user_details[$logged_user_id];
							$user_image_details = $all_user_image_details[$logged_user_id];
						?>
						<div id="selEditMainComments">
							<div id="selEditMainComments">
								{{ Form::open(array('url'=>$current_url, 'id'=>'collectionAddCommmentFrm', 'method'=>'post','class' => 'form-horizontal', 'onsubmit' => false)) }}
									<div class="media">
										<div class="pull-left">
											<a title="{{$user_details['display_name']}}" href="{{$user_details['profile_url']}}" class="imgsize-75X75 comment-imgbg">
												<img alt="{{$user_details['display_name']}}" title="{{$user_details['display_name']}}" src="{{$user_image_details['image_url']}}">
											</a>
										</div>
										<div class="media-body">
											<p>{{Form::textarea('comment','',array('class' => 'form-control', 'id'=>'comment', 'rows' => '5'))}}</p>
											<label class="error">{{{ $errors->first('comment') }}}</label>
											<div style="display:none;" id="selMsgError">
												<p class="text-danger" id="commentSelMsgError"></p>
											</div>
											<input type="submit" value="{{ Lang::get('common.submit') }}" id="post_comment" name="post_comment" class="btn green">
										</div>
									</div>
								{{Form::close()}}
							</div>
						</div>
					</div>
				@endif
			</div>
			<!-- END: COMMENT POST -->
		</div>
	</div>

	<div id="dialog-product-confirm" title="" style="display:none;">
		<span class="ui-icon ui-icon-alert"></span>
		<span id="dialog-product-confirm-content" class="show ml15"></span>
	</div>
@stop

@section('script_content')
	<script type="text/javascript">
		var ajax_proceed = 0;
		var collection_id = '{{$collection_details->id}}';
		var logged_user_id = '{{$logged_user_id}}';
		var user_id = {{$logged_user_id}};
		var collection_click_url = "{{URL::action('CollectionsController@postIncreaseClicks')}}";
		var favorite_product_url = "{{URL::action('FavoritesController@postToggleFavorite')}}";
		var favorite_product_list_url = "{{URL::action('FavoritesController@postToggleFavoriteList')}}";

		function showErrorDialog(err_data)
		{
			var err_msg ='<div class="alert alert-danger">'+err_data.error_message+'</div>';
			$('#error_msg_div').html(err_msg);
		}
		function showSuccessDialog(err_data)
		{
			var err_msg ='<div class="alert alert-success">'+err_data.success_message+'</div>';
			$('#error_msg_div').html(err_msg);
		}

		function removeErrorDialog()
		{
			$('#error_msg_div').html('');
		}
		$('.js-product-view').click(function(evt) {
			evt.preventDefault();
			var link = $(this).attr('href');
			displayLoadingImage(true);
			postData = 'collection_id=' + collection_id,
			$.post(collection_click_url, postData,  function(response)
			{
				window.location = link;
			}).error(function() {
				window.location = link;
			});
		});

		$( ".js-addto-favorite-collection").click(function() {

			$this = $(this);
			collection_id = $(this).data('collectionid');
			collection_owner_id = $(this).data('userid');

			if(typeof collection_id != 'undefined' && collection_id!='' && typeof collection_owner_id != 'undefined' && collection_owner_id!='')
			{
				postData = 'favorites=collection&collection_id=' + collection_id + '&collection_owner_id=' + collection_owner_id + '&user_id=' + logged_user_id,
				displayLoadingImage(true);
				$.post(favorite_product_url, postData,  function(response)
				{
					hideLoadingImage (false);

					data = eval( '(' +  response + ')');

					if(data.result == 'success')
					{
						removeErrorDialog();
		                var act = data.action_to_show;
		                var text_to_disp = '';
		                var favorite_text_msg = '';
		                if(act == "remove")
		                {
		                    text_to_disp = '<i class="fa fa-heart text-pink"></i> {{ Lang::get('collection.unfavorite') }}';
		                    //favorite_text_msg = '<strong>Favorited item!</strong> You can revisit it from your favorites.';
		                }else
		                {
		                    text_to_disp = '<i class="fa fa-heart text-muted"></i> {{ Lang::get('collection.favorite') }}';
		                    //favorite_text_msg = '<strong>Like this item?</strong> Add it to your favorites to revisit it later.';
		                }
		                $this.html(text_to_disp);
		                //$('#js-favorite-msg').html(favorite_text_msg);
						showSuccessDialog({status: 'success', success_message: data.success_msg});
					}
					else
					{
						showErrorDialog({status: 'error', error_message: data.error_msg});
					}
				}).error(function() {
					hideLoadingImage (false);
					showErrorDialog({status: 'error', error_message: '{{ Lang::get('common.some_problem_try_later') }}'});
				});
			}
		});



		$('.js-editComment').click(function() {
			var comment_id = $(this).data('id');
			$('#CommentTxt_'+comment_id).hide();

			$('#EditCommentFrm_'+comment_id).show();
			$('#ViewEditComment_'+comment_id).hide();

		});

	    function cancelEditComment(comment_id)
	    {
			$('#CommentTxt_'+comment_id).show();
        	$('#comment_'+comment_id).val($('#CommentTxt_'+comment_id).text());
			$('#EditCommentFrm_'+comment_id).hide();
        	$('#ViewEditComment_'+comment_id).show();
		}
		function nl2br (str, is_xhtml) {
		    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
		    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
		}

	    function updateComment(comment_id)
	    {
	    	var comment = $('#comment_'+comment_id).val();
	    	if(ajax_proceed)
                ajax_proceed.abort();

			var params = {"comment_id": comment_id, "comment": comment, 'user_id':logged_user_id, 'action':'update_comment' };
			ajax_proceed = $.post("{{ Url::action('CollectionsController@postCollectionAction')}}", params, function(data) {
                if(data) {

                    var data_arr = data.split("|~~|");
                    if(data_arr.length > 1) {
                        if(data_arr[0] == "success") {
                        	$('#CommentTxt_'+comment_id).html(nl2br(comment, true));
                        	$('#CommentTxt_'+comment_id).show();
							$('#EditCommentFrm_'+comment_id).hide();
                        	$('#ViewEditComment_'+comment_id).show();
                        	window.location.reload(true);
						}
                        else if(data_arr[0] == "error")
                        {
                        	$('#CommentTxt_'+comment_id).show();
                        	$('#comment_'+comment_id).val($('#CommentTxt_'+comment_id).text());
							$('#EditCommentFrm_'+comment_id).hide();
                        	$('#ViewEditComment_'+comment_id).show();

                        }
                        else
                        {
                            window.location.reload();
                        }
                    }
                    else {
                        window.location.reload();
                    }
                }
            });
		}

	    function doAction(comment_id, selected_action)
		{

			if(selected_action == 'delete_comment')
			{
				$('#dialog-product-confirm-content').html('{{ Lang::get('collection.confirm_delete_comment') }}');
			}
			$("#dialog-product-confirm").dialog({ title: '{{ Lang::get('collection.collections_list') }}', modal: true,
				buttons: {
					"{{ Lang::get('common.yes') }}": function() {


						if(ajax_proceed)
			                ajax_proceed.abort();
						displayLoadingImage(true);
						var params = {"comment_id": comment_id, 'action':selected_action };
						ajax_proceed = $.post("{{ Url::action('CollectionsController@postCollectionAction')}}", params, function(data) {
			                if(data) {

			                    var data_arr = data.split("|~~|");
			                    if(data_arr.length > 1) {
			                        if(data_arr[0] == "success") {
			                        	$('#commentsection_'+comment_id).remove();
			                        	$("#dialog-product-confirm").dialog("close");
			                        	 window.location.reload(true);
			                        }
			                        else if(data_arr[0] == "error")
			                        {
			                        	$('#CommentTxt_'+comment_id).show();
			                        	$('#comment_'+comment_id).val($('#CommentTxt_'+comment_id).text());
										$('#EditCommentFrm_'+comment_id).hide();
			                        	$('#ViewEditComment_'+comment_id).show();
			                        	$("#dialog-product-confirm").dialog("close");
			                        }
			                        else
			                        {
			                            window.location.reload();
			                        }
			                    }
			                    else {
			                        window.location.reload();
			                    }
			                }
			            });


					}, "{{ Lang::get('common.cancel') }}": function() { $(this).dialog("close"); }
				}
			});

			return false;
		}
		$(".fn_signuppop").fancybox({
            maxWidth    : 800,
            maxHeight   : 630,
            fitToView   : false,
            width       : '70%',
            height      : '430',
            autoSize    : false,
            closeClick  : false,
            type        : 'iframe',
            openEffect  : 'none',
            closeEffect : 'none'
        });
		$(".fn_changeStatus").fancybox({
	        maxWidth    : 800,
	        maxHeight   : 430,
	        fitToView   : false,
	        width       : '70%',
	        height      : '430',
	        autoSize    : false,
	        closeClick  : false,
	        type        : 'iframe',
	        openEffect  : 'none',
	        closeEffect : 'none'
	    });

	    $(document).ready(function() {
			$( ".js-addto-favorite-heart").click(function() {
				$this = $(this);
				if($(this).data('productid')!='')
				{
					postData = 'favorites=product&user_id=' + user_id + '&product_id=' + $(this).data('productid'),
					displayLoadingImage(true);
					$.post(favorite_product_url, postData,  function(response)
					{
						hideLoadingImage (false);

						data = eval( '(' +  response + ')');
						if(data.result == 'success')
						{
							removeErrorDialog();
			                var act = data.action_to_show;
			                var text_to_disp = '';
			                var favorite_text_msg = '';
			                if(act == "remove")
			                {
			                    text_to_disp = '<i class="fa fa-heart text-pink"></i>';
			                }else
			                {
			                    text_to_disp = '<i class="fa fa-heart text-muted"></i>';
			                }
			                $this.html(text_to_disp);
							//showSuccessDialog({status: 'success', success_message: data.success_msg});
						}
						else
						{
							showErrorDialog({status: 'error', error_message: data.error_msg});
						}
					}).error(function() {
						hideLoadingImage (false);
						showErrorDialog({status: 'error', error_message: '{{ Lang::get('viewProduct.some_problem_try_later') }}'});
					});
				}
			});

			$( ".js-show-list").click(function() {
				$this = $(this);
				if($(this).data('productid')!='') {
					var product_id = $(this).data('productid');
					//alert($('#fav_list_holder_'+product_id).css('display'));
					if($('#fav_list_holder_'+product_id).css('display') == 'none')
					{
						//$('#fav_block_'+product_id).css('display', 'block');
						$('#fav_list_holder_'+product_id).show();
						$('#fav_list_holder_'+product_id).html('<img src="{{URL::asset('images/general/loading.gif')}}" alt="loading" />');
						postData = 'user_id=' + user_id + '&product_id=' + product_id,
						//displayLoadingImage(true);
						$.post(favorite_product_list_url, postData,  function(response)
						{
							//hideLoadingImage (false);

							data_arr = response.split('|~~|');

							if(data_arr[0] == 'success')
							{
								//removeErrorDialog();
								$('#fav_list_holder_'+product_id).html(data_arr[1]);
							}
							else
							{
								showErrorDialog({status: 'error', error_message: data_arr[1]});
							}
						}).error(function() {
							//hideLoadingImage (false);
							showErrorDialog({status: 'error', error_message: '{{ Lang::get('viewProduct.some_problem_try_later') }}'});
						});
					}
					else {
						$('#fav_list_holder_'+product_id).html("").hide();
					}
				}
			});

			$(document).mouseup(function (e) {
			    var container = $(".fnFavListHolder");
			    if (!container.is(e.target) // if the target of the click isn't the container...
			        && container.has(e.target).length === 0) {
			        container.hide();
			    }
			});
		});

		$(document).ready(function() {
			$("#collectionAddCommmentFrm").validate({
                rules: {
	                comment: {
						required: true,
						maxlength: "{{Config::get('webshoppack.summary_max_length')}}"
					}
				},
	            messages: {
	                comment: {
						required: mes_required,
						maxlength: jQuery.format("{{trans('product.summary_max_length')}}")
					}
				},
				/* For Contact info violation */
				submitHandler: function(form) {
					form.submit();
				}
            });

	       // setInputLimiterById('comment', {{ Config::get('webshoppack.summary_max_length') }});

	       /*function setInputLimiterById(ident, char_limit)	{
	            if ($('#'+ident).length > 0) {
	                $('#'+ident).inputlimiter({
	                    limit: char_limit,
	                    remText: '{{ trans("common.words_remaining_1")}} %n {{ trans("common.words_remaining_2")}} %s {{ trans("common.words_remaining_3")}}',
	                    limitText: '{{ trans("common.limitted_words_1")}} %n {{ trans("common.limitted_words_2")}}%s'
	                });
	            }
	        }*/
        });
	</script>
@stop