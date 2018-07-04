@extends('base')
@section('content')
	<div class="row">
		<div class="col-md-2 clearfix">
			<!-- MANAGE ACCOUNT STARTS -->
			@include('myaccount.myAccountMenu')
			<!-- MANAGE ACCOUNT ENDS -->
		</div>

		<div class="col-md-10">
			<!-- PAGE TITLE STARTS -->
			<div class="responsive-pull-none">
				<h1>{{ trans('walletAccount.my_wallet_account') }}</h1>
			</div>
			<!-- PAGE TITLE END -->

			@include('walletAccount')

			<!-- ALERT BLOCK STARTS -->
			@if (Session::has('success_message') && Session::get('success_message') != "")
				<div class="note note-success">{{	Session::get('success_message') }}</div>
			@endif

			@if (Session::has('error_message') && Session::get('error_message') != "")
				<div class="note note-danger">{{	Session::get('error_message') }}</div>
			@endif
			<!-- ALERT BLOCK ENDS -->

			<!-- ALERT sudopay STARTS -->
			<div class="alert alert-danger" style="display:none;">
				<span id="buyer_fees_fourmula_disp"></span>
			</div>
			<!-- ALERT sudopay ENDS -->

			<!-- WALLET AMOUNT STARTS -->
			@if(isset($data_arr) && $data_arr['confirm'] == 'Yes')
				<div class="well">
					<h2 class="title-one">{{trans('walletAccount.add_fund_to_account_balance')}} - {{trans('walletAccount.confirmation')}}</h2>
					{{ Form::open(array('id'=>'ProcessPaymentCreditFrm')) }}
					<!-- {{ Form::open(array('action' => array('WalletAccountController@postAddAmount'), 'id'=>'addAmountToWalletFrm', 'method'=>'post','class' => 'form-horizontal show-bal' )) }}
						<div class="form-group">
							{{ Form::label('acc_balance', trans('walletAccount.amount'), array('class' => 'col-md-2 control-label')) }}
							<div class="col-md-3 margin-top-8">
								<input type="hidden" name="acc_balance" id="acc_balance" value="{{ $data_arr['acc_balance'] }}"/>
								<span class="text-muted">{{ Config::get('generalConfig.site_default_currency') }}</span> <strong>{{ $data_arr['acc_balance'] }}</strong>
							</div>
						</div>

							<div class="form-group">
							{{ Form::label('payment_method', trans('walletAccount.payment_method'), array('class' => 'col-md-2 control-label')) }}
							<div class="col-md-3 margin-top-8">{{ ucfirst($data_arr['payment_method']) }}</div>
						</div>

					{{Form::close()}} -->
					<?php
						if($d_arr['sudopay_brand'] == 'SudoPay Branding') {
							echo $d_arr['sc']->displayJSBtn($d_arr['sudopay_fields_arr'], false, 'sudopaybtn', true);
						}
						else {
							if($gateways_arr = $d_arr['sc']->checkGateways('Capture')) {
								echo $d_arr['sc']->displayGatewaysNew('Capture', false, 'No', $gateways_arr);
							}
						}
						$discounted_amount_cal = CUtil::convertAmountToCurrency($data_arr['acc_balance'], Config::get('generalConfig.site_default_currency'), '', false, false, true);
						$country_iso = CUtil::getCountryISOCode($data_arr['country_id']);
					?>
					{{ Form::hidden('sudopay_fees_payer', $d_arr['sudopay_fees_payer'], array('id' => 'sudopay_fees_payer')) }}
					{{ Form::hidden('address_line1', $data_arr['address_line1'], array('id' => 'address_line1')) }}
					{{ Form::hidden('address_line2', $data_arr['address_line2'], array('id' => 'address_line2')) }}
					{{ Form::hidden('street', $data_arr['street'], array('id' => 'street')) }}
					{{ Form::hidden('city', $data_arr['city'], array('id' => 'city')) }}
					{{ Form::hidden('state', $data_arr['state'], array('id' => 'state')) }}
					{{ Form::hidden('country_id', $data_arr['country_id'], array('id' => 'country_id')) }}
					{{ Form::hidden('country_iso', $country_iso, array('id' => 'country_iso')) }}
					{{ Form::hidden('zip_code', $data_arr['zip_code'], array('id' => 'zip_code')) }}
					{{ Form::hidden('phone_no', $data_arr['phone_no'], array('id' => 'phone_no')) }}
					{{ Form::hidden('parent_gateway_id', '', array('id' => 'parent_gateway_id')) }}
					{{ Form::hidden('card_type', '', array('id' => 'card_type')) }}
					{{ Form::hidden('payment_gateway_chosen', '', array('id' => 'payment_gateway_chosen')) }}
					{{Form::close()}}
					<div class="form-group clearfix">
					{{ Form::open(array('action' => array('WalletAccountController@postAddAmount'), 'id'=>'addAmountToWalletFrm', 'method'=>'post','class' => 'form-horizontal show-bal' )) }}
						<!-- <button type="button" name="" value="{{trans('payCheckout.pay_via_paypal')}}" class="btn green" onclick="proceedPayment('USD', 'paypal');">
						<i class="fa fa-tag margin-right-5"></i> {{trans('payCheckOut.pay_in')}} {{ Config::get('generalConfig.site_default_currency') }} {{ $data_arr['acc_balance'] }}</button> -->
						<button type="submit" name="edit_request" class="btn btn-info pull-right" id="edit_request" value="edit_request">
							<i class="fa fa-arrow-left"></i> {{trans("common.back")}}
                        </button>
						<input type="hidden" name="acc_balance" id="acc_balance" value="{{ $data_arr['acc_balance'] }}"/>
						{{ Form::hidden('address_line1', $data_arr['address_line1'], array('id' => 'address_line1')) }}
						{{ Form::hidden('address_line2', $data_arr['address_line2'], array('id' => 'address_line2')) }}
						{{ Form::hidden('street', $data_arr['street'], array('id' => 'street')) }}
						{{ Form::hidden('city', $data_arr['city'], array('id' => 'city')) }}
						{{ Form::hidden('state', $data_arr['state'], array('id' => 'state')) }}
						{{ Form::hidden('country_id', $data_arr['country_id'], array('id' => 'country_id')) }}
						{{ Form::hidden('zip_code', $data_arr['zip_code'], array('id' => 'zip_code')) }}
						{{ Form::hidden('phone_no', $data_arr['phone_no'], array('id' => 'phone_no')) }}
						{{Form::close()}}
					</div>
				</div>
				<div id="paypal_form" style="display:none;"></div>
			@else
				<div class="well">
					<h2 class="title-one">{{trans('walletAccount.add_fund_to_account_balance')}}</h2>
					{{ Form::open(array('action' => array('WalletAccountController@postAddAmount'), 'id'=>'addAmountToWalletFrm', 'method'=>'post','class' => 'form-horizontal show-bal' )) }}
						<div class="form-group {{{ $errors->has('acc_balance') ? 'error' : '' }}}">
							{{ Form::label('acc_balance', trans('walletAccount.amount'), array('class' => 'col-md-2 control-label required-icon')) }}
							<div class="col-md-3">
								<div class="input-group">
									<span class="input-group-addon">{{ Config::get('generalConfig.site_default_currency') }}</span>
									{{  Form::text('acc_balance', Input::old('acc_balance'), array('class' => 'form-control valid')); }}
								</div>
								<label for="acc_balance" generated="true" class="error">{{{ $errors->first('acc_balance') }}}</label>
								<p class="text-muted ml12"><small>( {{trans('common.note')}}: {{trans('walletAccount.min_amount_to_be_added')}} <strong>{{Config::get('generalConfig.site_default_currency')}} {{Config::get('generalConfig.minimum_amount_added_to_wallet')}} )</strong></small></p>
							</div>
						</div>

						<!-- <div class="form-group {{{ $errors->has('payment_method') ? 'error' : '' }}}">
							{{ Form::label('payment_method', trans('walletAccount.payment_method'), array('class' => 'col-md-2 control-label required-icon')) }}
							<div class="col-md-3">
								{{ Form::select('payment_method', array('paypal' => 'Paypal'), 'paypal', array('class' => 'form-control chosen-select')); }}
								<label class="error">{{{ $errors->first('payment_method') }}}</label>
							</div>
						</div> -->
							<div class="form-group">
								{{ Form::label('address_line1', Lang::get('myAddresses.address_line1'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-3">
									{{ Form::text('address_line1', Input::old('address_line1'), array('class' => 'form-control valid')) }}
									<label class="error">{{{ $errors->first('address_line1') }}}</label>
								</div>
							</div>
							<div class="form-group">
								{{ Form::label('address_line2', Lang::get('myAddresses.address_line2'), array('class' => 'col-md-2 control-label ')) }}
								<div class="col-md-3">
									{{ Form::text('address_line2', Input::old('address_line2'), array('class' => 'form-control valid')) }}
									<label class="error">{{{ $errors->first('address_line2') }}}</label>
								</div>
							</div>
							<div class="form-group">
								{{ Form::label('street', Lang::get('myAddresses.street'), array('class' => 'col-md-2 control-label')) }}
								<div class="col-md-3">
									{{ Form::text('street', Input::old('street'), array('class' => 'form-control valid')) }}
									<label class="error">{{{ $errors->first('street') }}}</label>
								</div>
							</div>
							<div class="form-group">
								{{ Form::label('city', Lang::get('myAddresses.city'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-3">
									{{ Form::text('city', Input::old('city'), array('class' => 'form-control valid')) }}
									<label class="error">{{{ $errors->first('city') }}}</label>
								</div>
							</div>
							<div class="form-group">
								{{ Form::label('state', Lang::get('myAddresses.state'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-3">
									{{ Form::text('state', Input::old('state'), array('class' => 'form-control valid')) }}
									<label class="error">{{{ $errors->first('state') }}}</label>
								</div>
							</div>
							<div class="form-group">
								{{ Form::label('country_id', Lang::get('myAddresses.country'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-3">
									{{ Form::select('country_id', $countries_list, Input::old('country_id') , array("id" => "country_id", "class" => "form-control")) }}
									<label class="error">{{{ $errors->first('country_id') }}}</label>
								</div>
							</div>
							<div class="form-group">
								{{ Form::label('zip_code', Lang::get('myAddresses.zip_code'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-3">
									{{ Form::text('zip_code', Input::old('zip_code'), array('class' => 'form-control valid')) }}
									<label class="error">{{{ $errors->first('zip_code') }}}</label>
								</div>
							</div>
							<div class="form-group">
								{{ Form::label('phone_no', Lang::get('myAddresses.phone_no'), array('class' => 'col-md-2 control-label required-icon')) }}
								<div class="col-md-3">
									{{ Form::text('phone_no', Input::old('phone_no'), array('class' => 'form-control valid')) }}
									<label class="error">{{{ $errors->first('phone_no') }}}</label>
								</div>
							</div>

						<div class="form-group">
							<div class="col-md-offset-2 col-md-9">
								<!--<input type="submit" value="Confirm" class="btn green" id="pay_confirm" name="pay_confirm">-->
								<button type="submit" value="{{trans('common.confirm')}}" class="btn green" id="pay_confirm" name="pay_confirm">{{trans('common.confirm')}} <i class="fa fa-arrow-right"></i></button>
							</div>
						</div>
					{{ Form::hidden('wallet_id', 'wallet', array('id' => 'wallet_id')) }}
					{{Form::close()}}
				</div>
			@endif
			<!-- WALLET AMOUNT END -->
		</div>
	</div>

	@if(isset($data_arr) && $data_arr['confirm'] == 'Yes')
		<script language="javascript" type="text/javascript">
			var ajax_proceed = 0;
			var confirm_data_arr = "{{ $data_arr['confirm'] }}";
			var amount = "{{ $data_arr['acc_balance'] }}";
			var add_users_credits = "{{ Url::to('walletaccount/add-users-credits')}}";
			//For sudo pay
			var payment_gateway_revised_amount_txt = "{{ trans('sudopay::sudopay.payment_gateway_revised_amount') }}";
			var discounted_amount_bf_revise = "{{ $discounted_amount_cal['amt'] }}";
			var discounted_currency_bf_revise = "{{ $discounted_amount_cal['currency_symbol'] }}";
		</script>
	@endif
	<script type="text/javascript">
		var page_name = "add_wallet_amount";
		var mes_required = '{{ Lang::get('auth/form.required') }}';
		var min_amount = '{{Config::get('generalConfig.minimum_amount_added_to_wallet')}}';
	</script>
@stop