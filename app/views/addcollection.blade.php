@extends('base')
@section('content')
	<!-- BEGIN: PAGE TITLE -->
	<div class="responsive-pull-none">
		<a href="{{ URL::action('MyCollectionsController@getIndex') }}" class="pull-right btn btn-xs blue-stripe default">
			<i class="fa fa-chevron-left"></i> {{ Lang::get('collection.my_collections') }}
		</a>
		<h1>{{ Lang::get('collection.add_collection') }}</h1>
	</div>
    <!-- END: PAGE TITLE -->

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

    <div class="row">
		<!-- BEGIN: ADD TAXATION -->
		<div class="col-md-9">
			<div class="well">
				{{ Form::open(array('action' => array('MyCollectionsController@postAdd'), 'id'=>'collectionFrm', 'method'=>'post','class' => 'form-horizontal' )) }}
					<div id="selSrchProducts">
						<fieldset>
							<div class="form-group">
								{{ Form::label('collection_name', Lang::get('collection.collection_name'), array('class' => 'col-md-3 control-label required-icon' )) }}
								<div class="col-md-5">
									{{ Form::text('collection_name', Input::get("collection_name"), array('class' => 'form-control valid')) }}
									<label class="error">{{$errors->first('collection_name')}}</label>
								</div>
							</div>

							<div class="form-group">
								{{ Form::label('collection_description', Lang::get('collection.description'), array('class' => 'col-md-3 control-label')) }}
								<div class="col-md-8">
									{{ Form::textarea('collection_description', Input::get("collection_description"), array('class' => 'form-control valid')) }}
									<label class="error">{{$errors->first('collection_description')}}</label>
								</div>
							</div>

							<div class="form-group">
								{{ Form::label('collection_access', Lang::get('collection.privacy'), array('class' => 'col-md-3 control-label')) }}
								<div class="col-md-5">
									<label class="radio-inline margin-right-10">
										{{Form::radio('collection_access','Public', Input::old('collection_access',true)) }}
										<label>{{ Lang::get('collection.public') }}</label>
									</label>
									<label class="radio-inline">
										{{Form::radio('collection_access','Private', Input::old('collection_access',false)) }}
										<label>{{ Lang::get('collection.private') }}</label>
									</label>
									<label class="error">{{$errors->first('collection_access')}}</label>
								</div>
							</div>

							<ul class="list-unstyled addlist-item row margin-top-20">
								@for($i=1;$i<=$max_product_allowed;$i++)
									<li class="col-md-3 col-sm-4 col-xs-5">
										<div class="shopitem-list">
											<div class="listing_items">
												<div class="inner-collection">
													<p class="item_image">
														<span class="jsImageContainer">
														<img alt="" src="{{Url::asset('images/no_image/bg-collectionempty.gif')}}"></span>
													</p>
													<p style="display:none" class="js_product_loading text-center"><a class="listing_error" href="javascript:void(0);">{{ Lang::get('common.loading')}}</a></p>
													<p style="display:none" class="js_item_error alert alert-info margin-0"><a href="javascript:void(0);"><!--alert comes here--></a></p>
													<div class="listing_details">
														<h3 class="listing_title text-ellipsis margin-0"></h3>
														<div class="clearfix fonts13">
															<span class="listing_price pull-right text-success"></span>
															<span class="listing_user pull-left text-ellipsis mxwd-85 text-left"></span>
														</div>
													</div>
												</div>
												<div class="js_listing_add_product inputnew-group input-group">
													{{Form::text('listing_url',Lang::get('collection.listing_url_text'),array('class' => 'js_listing_url text form-control'))}}
													{{Form::hidden('listing_id[]','', array('id' => 'listing_id_'.$i, 'class' => 'js-product_id'))}}
													<a href="javascript:void(0);" class="listing_id_ok input-group-addon" title="{{Lang::get('common.add')}}">
													<i class="fa fa-plus-circle"></i></a>
												</div>
												<div style="display:none" id="listing_id_{{$i}}_Help">{{Lang::get('collection.listing_url_text')}}</div>
												<p></p>
												<div style="display:none;" class="js_listing_overlay favorite-addremove">
													<span class="edit"><a class="listing_edit btn btn-success" href="javascript:void(0);" title="{{ Lang::get('common.edit') }}"><i class="fa fa-pencil"></i></a></span>
													<span class="delete"><a class="listing_remove btn btn-danger" href="javascript:void(0);" title="{{ Lang::get('common.delete') }}"><i class="fa fa-times"></i></a></span>
												</div>
											</div>
										</div>
									</li>
								@endfor
							</ul>

							<div class="form-group">
								<div class="col-md-9">
									{{ Form::hidden('user_id', $user_id) }}
									<button type="submit" name="srchproduct_submit" value="srchproduct_submit" class="btn green">
									<i class="fa fa-plus"></i> {{ Lang::get('collection.add_collection') }}</button>
									<button type="reset" name="srchproduct_reset" value="srchproduct_reset" class="btn default" onclick="javascript:location.href='{{ URL::action('MyCollectionsController@getIndex') }}'"><i class="fa fa-times"></i> {{ Lang::get('common.cancel') }}</button>
								</div>
							</div>
						</fieldset>
					</div>
				{{ Form::close() }}
			</div>
		</div>
		<!-- END: ADD TAXATION ->

		<!-- BEGIN: SIDE BAR -->
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
		<!-- END: SIDE BAR -->
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

		 function generateSlugUrl() {
                var title = $("#collection_name").val();
                    if(title.trim() == "")
                    $("#collection_slug").val('');
                    else if($("#url_slug").val().trim() == ''){
                    var slug_url = title.replace(/[^a-z0-9]/gi, '-');
                    slug_url = slug_url.replace(/(-)+/gi, '-');
                    slug_url = slug_url.replace(/^(-)+|(-)+$/g, '');
                    $("#collection_slug").val(slug_url.toLowerCase());
                }
            }

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