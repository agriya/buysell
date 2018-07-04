<!-- BEGIN: ALERT BLOCK -->
@if (isset($success_message) && $success_message != "")
    <div class="note note-success">{{	$success_message }}</div>
@elseif (isset($error_message) && $error_message != "")
    <div class="note note-danger">{{	$error_message }}</div>
@endif
<!-- END: ALERT BLOCK -->

<!-- BEGIN: PAYPAL DETAILS -->
{{ Form::model($shop_paypal_details, ['url' => URL::to('admin/shop/edit/'.$user_id),'method' => 'post','id' => 'shoppaypal_frm', 'class' => 'form-horizontal', 'onsubmit' => "return doSubmit('shoppaypal_frm', 'paypal_details')"]) }}
    <p class="note note-info"><strong>{{ trans("common.note") }}: </strong>{{ trans("shopDetails.shop_paypal_info") }}</p>
	<?php
		$paypal_email_label = trans("shopDetails.paypal_email_label");
	?>
	{{ Form::hidden('submit_form', "update_shop_paypal", array("name" => "submit_form", "id" => "submit_form"))}}
	<fieldset>
		<div class="form-group {{{ $errors->has('shop_name') ? 'error' : '' }}}">
			{{ Form::label('paypal_email', $paypal_email_label, array('class' => 'col-md-4 control-label required-icon')) }}
			<div class="col-md-5">
				{{ Form::text('paypal_id', Input::get('paypal_id'), array('class' => 'form-control')); }}
				<label class="error">{{{ $errors->first('paypal_email') }}}</label>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-offset-4 col-md-8">
				<button type="button" name="update_policy" class="btn blue-madison" id="update_policy" value="update_policy" onclick="javascript:doSubmit('shoppaypal_frm', 'paypal_details');"><i class="fa fa-cloud-upload"></i> {{trans("common.update")}}</button>
			</div>
		</div>
	</fieldset>
{{ Form::close() }}
<!-- END: PAYPAL DETAILS -->