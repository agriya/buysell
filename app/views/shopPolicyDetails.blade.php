@extends('base')
<!-- SHOP SETTINGS STARTS -->
@section('content')
	<div class="row">
    	<div class="col-md-2">
			<!-- MANAGE ACCOUNT STARTS -->
				@include('myaccount.myAccountMenu')
			<!-- MANAGE ACCOUNT ENDS -->
		</div>

        <div class="col-md-10">
			<!-- PAGE TITLE STARTS -->
			<div class="responsive-pull-none">
				<h1>{{trans("shopDetails.shop_policies")}}</h1>
			</div>
			<!-- PAGE TITLE END -->

			<!-- ALERT BLOCK STARTS -->
			@if (Session::has('success_message') && Session::get('success_message') != "")
				<div class="note note-success">{{	Session::get('success_message') }}</div>
				<?php
					Session::forget('success_message');
				?>
			@endif

			@if(!CUtil::isShopOwner(null, $shop_obj))
				<div class="alert alert-info">{{ trans("shopDetails.shop_not_set_error_message") }}</div>
			@endif
        	<!-- ALERT BLOCK ENDS -->

		   <div class="well">
				<!-- PAYPAL DETAILS STARTS -->
				{{ Form::model($shop_details, ['url' => $shop_policy_url, 'method' => 'post', 'id' => 'addProductfrm', 'class' => 'form-horizontal']) }}
					<h2 class="title-one"><i class="fa fa-file-text-o"></i> {{trans("shopDetails.shop_policy_details")}}</h2>
					<div class="form-group {{{ $errors->has('policy_welcome') ? 'error' : '' }}}">
						{{ Form::label('policy_welcome', trans("shopDetails.welcome"), array('class' => 'col-md-3 control-label')) }}
						<div class="col-md-8">
							{{  Form::textarea('policy_welcome', null, array('class' => 'form-control valid fn_editor', 'rows' => '7')); }}
							<label class="error">{{{ $errors->first('policy_welcome') }}}</label>
						</div>
					</div>

					<div class="form-group {{{ $errors->has('policy_payment') ? 'error' : '' }}}">
						{{ Form::label('policy_payment', trans('shopDetails.payment'), array('class' => 'col-md-3 control-label')) }}
						<div class="col-md-8">
							{{  Form::textarea('policy_payment', null, array('class' => 'form-control fn_editor', 'rows' => '7')); }}
							<label class="error">{{{ $errors->first('policy_payment') }}}</label>
						</div>
					</div>

					<div class="form-group {{{ $errors->has('policy_shipping') ? 'error' : '' }}}">
						{{ Form::label('policy_shipping', trans("shopDetails.shipping"), array('class' => 'col-md-3 control-label')) }}
						<div class="col-md-8">
							{{  Form::textarea('policy_shipping', null, array('class' => 'form-control fn_editor', 'rows' => '7')); }}
							<label class="error">{{{ $errors->first('policy_shipping') }}}</label>
						</div>
					</div>

					<div class="form-group {{{ $errors->has('policy_refund_exchange') ? 'error' : '' }}}">
						{{ Form::label('policy_refund_exchange', trans("shopDetails.refund_exchange"), array('class' => 'col-md-3 control-label')) }}
						<div class="col-md-8">
							{{  Form::textarea('policy_refund_exchange', null, array('class' => 'form-control fn_editor', 'rows' => '7')); }}
							<label class="error">{{{ $errors->first('policy_refund_exchange') }}}</label>
						</div>
					</div>

					<div class="form-group {{{ $errors->has('policy_faq') ? 'error' : '' }}}">
						{{ Form::label('policy_faq', trans("shopDetails.faq"), array('class' => 'col-md-3 control-label')) }}
						<div class="col-md-8">
							{{  Form::textarea('policy_faq', null, array('class' => 'form-control fn_editor', 'rows' => '7')); }}
							<label class="error">{{{ $errors->first('policy_faq') }}}</label>
						</div>
					</div>

					<div class="form-group">
						{{ Form::hidden('id', null, array('id' => 'id')) }}
						<div class="col-md-offset-3 col-md-8">
							<button name="update_policy_details" id="update_policy_details" value="update_policy_details" type="submit" class="btn blue-madison">
								<i class="fa fa-cloud-upload"></i> {{ trans("shopDetails.update") }}
							</button>
						</div>
					</div>
				{{ Form::close() }}
				<!-- PAYPAL DETAILS ENDS -->
			</div>

			<div id="dialog-delete-confirm" class="confirm-dialog-delete" title="" style="display:none;">
				  <p><span class="ui-icon ui-icon-alert"></span><small>{{  trans('shopDetails.shopdetails_banner_image_confirm') }}</small></p>
			</div>

			<div id="dialog-cancellation-policy-delete-confirm" class="confirm-dialog-delete" title="" style="display:none;">
				  <p><span class="ui-icon ui-icon-alert"></span><small>{{  trans('shopDetails.shopdetails_cancellation_policy_delete_confirm') }}</small></p>
			</div>
		</div>
    </div>
<!-- SHOP SETTINGS ENDS -->
	<script type="text/javascript">
		var page_name = "shop_policy_details";
		var mes_required = "{{trans('common.required')}}";
		var valid_email = "{{ trans('shopDetails.not_valid_email') }}";
		var alpha_numeric = "{{trans('shopDetails.alpha_numeric_characters')}}";
		var common_no_label = "{{ trans('common.cancel') }}" ;
		var common_yes_label = "{{ trans('common.yes') }}" ;
		var package_name = "{{ Config::get('generalConfig.site_name') }}" ;
		var url_del_image = "{{ Url::action('ShopController@getDeleteShopImage') }}";
		var shopdetails_banner_deleted_success = "{{trans('shopDetails.shopdetails_banner_deleted_success')}}";
        var url_policy= "{{ Url::action('ShopController@getDeleteCancellationPolicy') }}";
        var shopdetails_cancellation_policy_file_deleted_success = "{{trans('shopDetails.shopdetails_cancellation_policy_file_deleted_success')}}";
        var desc_max = "{{ Config::get('webshoppack.fieldlength_shop_description_max') }}";
		var contactinfo_max = "{{ Config::get('webshoppack.fieldlength_shop_contactinfo_max') }}";
		var shopname_min_length = "{{ Config::get('webshoppack.shopname_min_length') }}";
		var shopname_max_length = "{{ Config::get('webshoppack.shopname_max_length') }}";
		var shopslogan_min_length = "{{ Config::get('webshoppack.shopslogan_min_length') }}";
		var shopslogan_max_length = "{{ Config::get('webshoppack.shopslogan_max_length') }}";
		var fieldlength_shop_description_min = "{{ Config::get('webshoppack.fieldlength_shop_description_min') }}";
		var fieldlength_shop_description_max = "{{ Config::get('webshoppack.fieldlength_shop_description_max') }}";
		var fieldlength_shop_contactinfo_min = "{{ Config::get('webshoppack.fieldlength_shop_contactinfo_min') }}";
		var fieldlength_shop_contactinfo_max = "{{ Config::get('webshoppack.fieldlength_shop_contactinfo_max') }}";
	</script>
@stop