<!-- ALERT BLOCK STARTS -->
@if (isset($success_message) && $success_message != "")
    <div class="note note-success">{{	$success_message }}</div>
@elseif (isset($error_message) && $error_message != "")
    <div class="note note-danger">{{	$error_message }}</div>
@endif
<!-- ALERT BLOCK ENDS -->

<!-- SHOP ADDRESS DETAILS STARTS -->
{{ Form::model($shop_details, ['url' => URL::to('shop/users/shop-details'),'method' => 'post','id' => 'shopaddress_frm', 'class' => 'form-horizontal', 'onsubmit' => "retun doSubmit('shopaddress_frm', 'address_details')"]) }}
	{{ Form::hidden('submit_form', "update_address", array("name" => "submit_form", "id" => "submit_form"))}}
	<fieldset>
		<div class="form-group {{{ $errors->has('shop_country') ? 'error' : '' }}}">
			{{ Form::label('shop_country', trans("shopDetails.shop_country"), array('class' => 'col-md-4 control-label required-icon')) }}
			<div class="col-md-5">
				{{ Form::select('shop_country', $country_arr, Input::get('shop_country'), array('class' => 'form-control')) }}
				<label class="error">{{{ $errors->first('shop_country') }}}</label>
			</div>
		</div>

		<div class="form-group {{{ $errors->has('shop_address1') ? 'error' : '' }}}">
			{{ Form::label('shop_address1', trans("shopDetails.shop_address1"), array('class' => 'col-md-4 control-label required-icon')) }}
			<div class="col-md-5">
				{{ Form::text('shop_address1', Input::get('shop_address1'), array('class' => 'form-control')); }}
				<label class="error">{{{ $errors->first('shop_address1') }}}</label>
			</div>
		</div>

		<div class="form-group {{{ $errors->has('shop_address2') ? 'error' : '' }}}">
			{{ Form::label('shop_address2', trans("shopDetails.shop_address2"), array('class' => 'col-md-4 control-label')) }}
			<div class="col-md-5">
				{{ Form::text('shop_address2', Input::get('shop_address2'), array('class' => 'form-control')); }}
				<label class="error">{{{ $errors->first('shop_address2') }}}</label>
			</div>
		</div>

		<div class="form-group {{{ $errors->has('shop_city') ? 'error' : '' }}}">
			{{ Form::label('shop_city', trans("shopDetails.shop_city"), array('class' => 'col-md-4 control-label required-icon')) }}
			<div class="col-md-5">
				{{ Form::text('shop_city', Input::get('shop_city'), array('class' => 'form-control')); }}
				<label class="error">{{{ $errors->first('shop_city') }}}</label>
			</div>
		</div>

		<div class="form-group {{{ $errors->has('shop_state') ? 'error' : '' }}}">
			{{ Form::label('shop_state', trans("shopDetails.shop_state"), array('class' => 'col-md-4 control-label required-icon')) }}
			<div class="col-md-5">
				{{ Form::text('shop_state', Input::get('shop_state'), array('class' => 'form-control')); }}
				<label class="error">{{{ $errors->first('shop_state') }}}</label>
			</div>
		</div>

		<div class="form-group {{{ $errors->has('shop_zipcode') ? 'error' : '' }}}">
			{{ Form::label('shop_zipcode', trans("shopDetails.shop_postalzip"), array('class' => 'col-md-4 control-label required-icon')) }}
			<div class="col-md-5">
				{{ Form::text('shop_zipcode', Input::get('shop_zipcode'), array('class' => 'form-control')); }}
				<label class="error">{{{ $errors->first('shop_zipcode') }}}</label>
			</div>
		</div>

		<div class="form-group">
			<div class="col-md-offset-4 col-md-6">
				<button type="button" name="update_address" class="btn blue-madison" id="update_address" value="update_address" onclick="javascript:doSubmit('shopaddress_frm', 'address_details');"><i class="fa fa-cloud-upload"></i> {{trans("common.update")}}</button>
			</div>
		</div>
	</fieldset>
{{ Form::close() }}
<!-- SHOP ADDRESS DETAILS ENDS -->