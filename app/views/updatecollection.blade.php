@extends('base')
@section('content')
	<!-- PAGE TITLE STARTS -->
	<a href="{{ URL::action('MyCollectionsController@getIndex') }}" class="pull-right btn btn-xs blue-stripe default margin-top-5">
		<i class="fa fa-chevron-left"></i> {{ Lang::get('collection.my_collections') }}
	</a>
	<h1>{{ Lang::get('collection.edit_collection') }}</h1>
	<!-- PAGE TITLE END -->

    <!-- ALERT BLOCK STARTS -->
    @if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('error_message'); ?>
    @endif
    @if(Session::has('success_message') && Session::get('success_message') != '')
        <div class="note note-success">{{ Session::get('success_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif
    <!-- ALERT BLOCK ENDS -->

	<div class="row">
		<!-- ADD TAXATION STARTS -->
		<div class="col-md-9">
			<div class="well">
			{{ Form::open(array('action' => array('MyCollectionsController@postUpdate', $collection_det->id), 'id'=>'collectionFrm', 'method'=>'post','class' => 'form-horizontal' )) }}
				<div id="selSrchProducts">
					<fieldset>
						<div class="form-group">
							{{ Form::label('collection_name', Lang::get('collection.collection_name'), array('class' => 'col-md-3 control-label required-icon')) }}
							<div class="col-md-5">
								{{ Form::text('collection_name', Input::get("collection_name",$collection_det->collection_name), array('class' => 'form-control valid')) }}
								<label class="error">{{$errors->first('collection_name')}}</label>
							</div>
						</div>

						<div class="form-group">
							{{ Form::label('collection_description', Lang::get('collection.description'), array('class' => 'col-md-3 control-label')) }}
							<div class="col-md-8">
								{{ Form::textarea('collection_description', Input::get("collection_description", $collection_det->collection_description), array('class' => 'form-control valid')) }}
								<label class="error">{{$errors->first('collection_description')}}</label>
							</div>
						</div>

						<div class="form-group">
							<?php
								$collection_access = 'Public';
								$collection_access_public = true;
								$collection_access_private = false;
								if(isset($collection_det->collection_access))
								{
									$collection_access_public = ($collection_det->collection_access == "Public")?true:false;
									$collection_access_private = ($collection_det->collection_access == "Private")?true:false;
								}
							 ?>
							{{ Form::label('collection_access', Lang::get('collection.privacy'), array('class' => 'col-md-3 control-label')) }}
							<div class="col-md-5">
								<label class="radio-inline margin-right-10">
									{{Form::radio('collection_access','Public', Input::old('collection_access',$collection_access_public)) }}
									<label>{{ Lang::get('collection.public') }}</label>
								</label>
								<label class="radio-inline">
									{{Form::radio('collection_access','Private', Input::old('collection_access',$collection_access_private)) }}
									<label>{{ Lang::get('collection.private') }}</label>
								</label>
								<label class="error">{{$errors->first('collection_access')}}</label>
							</div>
						</div>

						<ul class="list-unstyled addlist-item row margin-top-20">
							<?php $inc = 0;
								$productService = new ProductService();
							 ?>
							@foreach($collection_det->collection_products as $product_id)
								<?php
									$product = Products::initialize($product_id);
									$product->setIncludeDeleted(true);
									$product->setIncludeBlockedUserProducts(true);
									$product_det = $product->getProductDetails();
									$name = Cutil::getUserDetails($user_id);
									$view_url = Products::getProductViewURL($product_id, $product_det);


									$p_img_arr = $product->getProductImage($product_id);
									$p_thumb_img = $productService->getProductDefaultThumbImage($product_id, 'thumb', $p_img_arr);
									if($product_det['is_free_product'] == 'Yes'){
										$price = 'Free';
									}else{
										$price = $productService->formatProductPriceNew($product_det);
										$price =  (isset($price['product']['discount']) && $price['product']['discount'] >= 0)? $price['product']['discount'] : 0;
										$price = CUtil::convertAmountToCurrency($price, Config::get('generalConfig.site_default_currency'), '', true);
									}
									$p_img_arr = $product->getProductImage($product_id);
									$p_thumb_img = $productService->getProductDefaultThumbImage($product_id, 'thumb', $p_img_arr);
									$view_user_url = Cutil::userProfileUrl($name['user_code']);
									//print_r($product_det);exit;
								?>

								<li class="col-md-3 col-sm-4 col-xs-5">
									<div class="shopitem-list">
										<div class="listing_items">
											<div class="inner-collection">
												<p class="item_image">
													<span class="jsImageContainer"><a target="_blank" href="{{ $view_url }}">
													<img alt="" src="{{ $p_thumb_img['image_url'] }}" title ="{{ $p_thumb_img['title'] }}"></a></span>
												</p>
												<p style="display:none" class="js_product_loading text-center">
													<a class="listing_error" href="javascript:void(0);">{{ Lang::get('common.loading')}}</a>
												</p>
												<p style="display:none" class="js_item_error alert alert-info margin-0"><a href="javascript:void(0);"><!--alert comes here--></a></p>
												<div class="listing_details">
													<h3 class="listing_title margin-0 text-ellipsis">
														<a target="_blank" href="{{ $view_url }}">{{{ nl2br($product_det['product_name']) }}}</a>
													</h3>
													<div class="clearfix fonts13">
														<span class="listing_user pull-left text-ellipsis mxwd-85 text-left">
															<a target="_blank" href="{{ $view_user_url }}">{{ $name['display_name'] }}</a>
														</span>
														<span class="listing_price pull-right text-success">{{ $price }}</span>
													</div>
												</div>
											</div>
											<div class="js_listing_add_product inputnew-group input-group" style="display: none;">
												{{Form::text('listing_url',$view_url, array('class' => 'js_listing_url text form-control', 'id' => 'listing_id_'.$inc))}}
												{{Form::hidden('listing_id[]',$product_id, array('id' => 'listing_id_'.$inc, 'class' => 'js-product_id'))}}
												<a href="javascript:void(0);" class="listing_id_ok input-group-addon" title="Add"><i class="fa fa-plus-circle"></i></a>
											</div>
											<div style="display:none" id="listing_id_{{$inc}}_Help">Enter the listing url (or) the item id</div>
											<p></p>
											<div class="js_listing_overlay favorite-addremove">
												<span class="edit"><a class="listing_edit btn btn-success" href="javascript:void(0);" title="{{ Lang::get('common.edit') }}"><i class="fa fa-pencil"></i></a></span>
												<span class="delete"><a class="listing_remove btn btn-danger" href="javascript:void(0);" title="{{ Lang::get('common.delete') }}"><i class="fa fa-times"></i></a></span>
											</div>
										</div>
									</div>
								</li>
							@endforeach

							@if($collection_det->products_count < $max_product_allowed)
								@for($i=$collection_det->products_count+1;$i<=$max_product_allowed;$i++)
									<li class="col-md-3 col-sm-4 col-xs-5">
										<div class="shopitem-list">
											<div class="listing_items">
												<div class="inner-collection">
													<p class="item_image">
														<span class="jsImageContainer"><img alt="" src="{{Url::asset('images/no_image/bg-collectionempty.gif')}}"></span>
													</p>
													<p style="display:none" class="js_product_loading text-center">
														<a class="listing_error" href="javascript:void(0);">{{ Lang::get('common.loading')}}</a>
													</p>
													<p style="display:none" class="js_item_error alert alert-info margin-0"><a href="javascript:void(0);"><!--alert comes here--></a></p>
													<div class="listing_details">
														<h3 class="listing_title text-ellipsis margin-0"></h3>
														<div class="clearfix fonts13">
															<span class="listing_price pull-right text-success"></span>
															<span class="listing_user pull-left"></span>
														</div>
													</div>
												</div>

												<div class="js_listing_add_product inputnew-group input-group">
													{{Form::text('listing_url',Lang::get('collection.listing_url_text'), array('class' => 'js_listing_url text form-control', 'id' => 'listing_id_'.$i))}}
													{{Form::hidden('listing_id[]','', array('id' => 'listing_id_'.$i, 'class' => 'js-product_id'))}}
													<a href="javascript:void(0);" class="listing_id_ok input-group-addon" title="Add"><i class="fa fa-plus-circle"></i></a>
												</div>

												<div style="display:none" id="listing_id_{{$i}}_Help">Enter the listing url (or) the item id</div>
												<p></p>
												<div style="display: none;" class="js_listing_overlay favorite-addremove">
													<span class="edit"><a class="listing_edit btn btn-success" href="javascript:void(0);"><i class="fa fa-pencil"></i></a></span>
													<span class="delete"><a class="listing_remove btn btn-danger" href="javascript:void(0);"><i class="fa fa-times"></i></a></span>
												</div>
											</div>
										</div>
									</li>
								@endfor
							@endif
						</ul>

						<div class="form-group">
							<div class="col-md-9">
								{{ Form::hidden('user_id', $user_id) }}
								<button type="submit" name="srchproduct_submit" value="srchproduct_submit" class="btn blue-madison">
								<i class="fa fa-arrow-up"></i> {{ Lang::get('collection.update_collection') }}</button>
								<button type="reset" name="srchproduct_reset" value="srchproduct_reset" class="btn default" onclick="javascript:location.href='{{ URL::action('MyCollectionsController@getIndex') }}'"><i class="fa fa-times"></i> {{ Lang::get('collection.collection_cancel') }}</button>
							</div>
						</div>
					</fieldset>
				</div>
			{{ Form::close() }}
			</div>
		</div>
		<!-- ADD TAXATION ENDS -->

		<!-- SIDE BAR STARTS -->
		<div class="col-md-3">
			<div class="well aside-colectnguid">
				<h2 class="title-two">{{ Lang::get('collection.collections_guidelines') }}</h2>
				<p>{{ Lang::get('collection.collections_desc', array('site_name' => Config::get('generalConfig.site_name'))) }}:</p>
				<ul class="list-unstyled">
					<li><i class="fa fa-chevron-right text-muted"></i> <p>{{ Lang::get('collection.tips1') }}</p></li>
					<li><i class="fa fa-chevron-right text-muted"></i> <p>{{ Lang::get('collection.tips2') }}</p></li>
					<li><i class="fa fa-chevron-right text-muted"></i> <p>{{ Lang::get('collection.tips3') }}</p></li>
				</ul>
			</div>
		</div>
		<!-- SIDE BAR END -->
	</div>
@stop

@section('script_content')
	<script type="text/javascript">
		var mes_required = '{{ Lang::get('auth/form.required') }}';
		var listing_url_txt = '{{Lang::get('collection.listing_url_text')}}';
		var empty_image = '<img alt="" src="{{Url::asset('images/no_image/bg-collectionempty.gif')}}">';



		$('.js_listing_url').focusin(
			function ()
			{
				if($(this).val() == listing_url_txt)
				{
					$(this).val('');
					//$(this).removeClass(css_err_class);
				}
			}
		).focusout(
			function ()
			{
				if($(this).val() == '')
				{
					$(this).val(listing_url_txt);
					//$(this).removeClass(css_err_class);
				}
			}
		);

		$(document).ready(function() {
			$("#collectionFrm").validate({
                rules: {
	                collection_name: {
						required: true
					},
					privacy: {
						required: true
					},
				},
	            messages: {
	                collection_name: {
						required: mes_required
					},
					privacy: {
						required: mes_required
					},
				},
				/* For Contact info violation */
				submitHandler: function(form) {
					form.submit();
				}
            });

            $('.listing_id_ok').click(function(){
            	listing_space = $(this).closest('.listing_items');
				var listing_input = $(listing_space).find('.js_listing_url');
				var listing_product_id = $(listing_space).find('.js-product_id');
				var listing_url = $(listing_input).val();
				if(listing_url=='' || listing_url == listing_url_txt)
					return false;
				loading_image = $(listing_space).find('.js_product_loading');
				$(loading_image).show();
				$(listing_space).find('.item_image').hide();
				$(listing_space).find('.listing_details').hide();
				$(listing_space).find('.js_item_error').hide();
				$.post('{{ URL::action('MyCollectionsController@postProductDetails') }}', { listing_url: listing_url},  function(response)
		        {
		        	$(loading_image).hide();
		        	data = eval( '(' +  response + ')');
		            if(data.result == 'success')
		            {
		            	var error_found = false;
		            	$('.js-product_id').each(function(){
							var currelem = $(this);
							if(listing_product_id.is(currelem))
							{

								return;
							}

							if($(this).val() == data.product_id)
							{
								error_div = $(listing_space).find('.js_item_error');
								$(error_div).show();
								$(error_div).find('a').text('Product already exists. Please try again');
								$(listing_input).val(listing_url_txt);
								$(listing_space).find('.listing_title').text('');
								$(listing_space).find('.jsImageContainer').html(empty_image);
								$(listing_space).find('.js_listing_add_product').show();
			            		$(listing_space).find('.js_listing_overlay').hide();
								error_found = true;
								return false;
							}
						});
						if(!error_found)
						{
			            	$(listing_space).find('.jsImageContainer').html(data.view_url_with_image);
			            	//$(listing_input).val(data.product_id);
			            	$(listing_space).find('.js-product_id').val(data.product_id);
			            	$(listing_space).find('.listing_title').html(data.product_name);
			            	$(listing_space).find('.listing_user').html(data.user_id);
			            	$(listing_space).find('.listing_price').html(data.price);
			            	$(listing_space).find('.js_listing_add_product').hide();
			            	$(listing_space).find('.js_listing_overlay').show();
							$(listing_space).find('.item_image').show();
							$(listing_space).find('.listing_details').show();
							$(listing_space).find('.js_item_error').show();
			            	error_div = $(listing_space).find('.js_item_error');
							$(error_div).hide();
							$(error_div).find('a').text('');
			            }

		            }
		            else
		            {
		            	error_div = $(listing_space).find('.js_item_error');
						$(error_div).show();
						if(typeof(data.error_message) == 'undefined')
							$(error_div).find('a').text('Invalid Product Id');
						else
							$(error_div).find('a').text(data.error_message);
						$(listing_input).val(listing_url_txt);
					}
		            hideLoadingImage(false);
		        });
            });
			$('.listing_edit').click(function(){
				listing_space = $(this).closest('.listing_items');
				$(listing_space).find('.js_listing_add_product').show();
		        $(listing_space).find('.js_listing_overlay').hide();
			});
			$('.listing_remove').click(function(){
				listing_space = $(this).closest('.listing_items');
				listing_input = $(listing_space).find('.js_listing_url');
				$(listing_input).val(listing_url_txt);
				$(listing_space).find('.js-product_id').val('');
				$(listing_space).find('.listing_title').text('');
				$(listing_space).find('.jsImageContainer').html(empty_image);
				$(listing_space).find('.listing_user').html('<span class="listing_price pull-right text-success"></span>');
				$(listing_space).find('.listing_price').html('<span class="listing_user pull-left text-ellipsis mxwd-85 text-left"></span>');
				$(listing_space).find('.js_listing_add_product').show();
        		$(listing_space).find('.js_listing_overlay').hide();
			});
		});
    </script>
@stop