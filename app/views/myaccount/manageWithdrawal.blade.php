@extends('base')
@section('content')
	<!-- BEGIN: PAGE TITLE -->
	<div class="responsive-pull-none">
		<a href="{{ URL::to('users/my-withdrawals/index') }}" class="pull-right btn btn-xs blue-stripe default">
			<i class="fa fa-chevron-left"></i> {{ trans('myaccount/form.back_to_withdrawal') }}
		</a>
		<h1>{{ trans('myaccount/form.my-withdrawals.account_menu_withdrawal') }}</h1>
    </div>
	<!-- END: PAGE TITLE -->

	<!-- BEGIN: ALERT BLOCK -->
	@if (Session::has('success_message') && Session::get('success_message') != "")
        <div class="note note-success">{{ Session::get('success_message') }}</div>
    @endif
    
    @if (Session::has('error_message') && Session::get('error_message') != "")
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
    @endif
	<!-- END: ALERT BLOCK -->

    <?php
		//$logged_user_id = BasicCUtil::getLoggedUserId();
		/*$total_credit_amount = DB::table('common_invoice')->where('user_id',$logged_user_id)
								->where('reference_type','=','Credits')
								->where('status', '=', 'Unpaid')
								->SUM('amount');*/
		$total_credit_amount = 0;
		$bal_amount = $d_arr['user_balance'] - $total_credit_amount;
	 	$amount = ($bal_amount > 0)? $bal_amount : 0 ;
	?>

	<!-- BEGIN: WITHDRAWALS FORM -->
    <div class="well">
		@if(isset($d_arr['allow_withdrawal']) && $d_arr['allow_withdrawal'])
		   <h1 class="title-one">{{ $d_arr['title'] }}</h1>
			<p class="note note-info">{{ $d_arr['note'] }}</p>
			{{ Form::open(array('id'=>'withdrawalReqfrm', 'method'=>'post','class' => 'form-horizontal mb40' )) }}
				<fieldset>
					@if($d_arr['add_form'])
						<div class="form-group {{{ $errors->has('withdraw_amount') ? 'error' : '' }}}">
							{{ Form::label('withdraw_currency', trans('myaccount/form.my-withdrawals.from_balance'), array('class' => 'col-md-3 control-label')) }}
							<div class="col-md-4">
								<p class="margin-top-8"><span class="text-muted">{{ $d_arr['withdraw_currency'] }}</span> <strong>{{ CUtil::formatAmount($amount) }}</strong></p>
								<label class="error">{{{ $errors->first('amount') }}}</label>
								<input type="hidden" name="withdraw_currency" id="withdraw_currency" value="{{ $d_arr['withdraw_currency']}}" class="form-control valid"/>
							</div>
						</div>

						<div class="form-group {{{ $errors->has('request_amount') ? 'error' : '' }}}">
							{{ Form::label('request_amount', trans('myaccount/form.my-withdrawals.request_amount'), array('class' => 'col-md-3 control-label required-icon')) }}
							<div class="col-md-4">
								{{ Form::text('request_amount', Input::get('request_amount'), array('class' => 'form-control', 'id' => 'request_amount')) }}
								<p class="margin-top-5"><small id="min_amount" class="text-muted"></small></p>
								<label for="request_amount" generated="true" class="error">{{{ $errors->first('request_amount') }}}</label>
							</div>
						</div>

						<div class="form-group {{{ $errors->has('withdraw_fee') ? 'error' : '' }}}" id="fee_row">
							{{ Form::label('withdraw_fee', trans('myaccount/form.my-withdrawals.fee_lbl'), array('class' => 'col-md-3 control-label')) }}
							<div class="col-md-4">
								 <p id="fee" class="margin-top-8"><span class="text-muted">{{ $d_arr['withdraw_currency'] }}</span> <strong>{{ $d_arr['withdraw_fee'] }}</strong></p>
							</div>
						</div>

						<div class="form-group {{{ $errors->has('withdraw_amount') ? 'error' : '' }}}">
							{{ Form::label('withdrawal_amount', trans('myaccount/form.my-withdrawals.amount_you_withdraw'), array('class' => 'col-md-3 control-label')) }}
							<div class="col-md-4">
								<p class="margin-top-8"><span class="currency text-muted"></span><strong id="bal_amt"></strong></p>
								<label class="error">{{{ $errors->first('withdrawal_amount') }}}</label>
							</div>
						</div>

						<div class="form-group {{{ $errors->has('pay_to_details') ? 'error' : '' }}}">
							{{ Form::label('pay_to_details', $d_arr['transfer_thru_lbl']." ".trans('common.details'), array('class' => 'col-md-3 control-label required-icon')) }}
							<div class="col-md-7">
								{{ Form::textarea('pay_to_details', Input::get('pay_to_details'), array('placeholder' => trans('myaccount/form.my-withdrawals.payto_help_text'), 'class' => 'form-control', 'id' => 'pay_to_details')) }}
								<label class="error">{{{ $errors->first('pay_to_details') }}}</label>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-offset-3 col-md-10">
								<input type="hidden" name="transfer_thru" id="transfer_thru" value="{{ $d_arr['transfer_thru']}}"/>
								<button type="submit" name="continue" class="btn green" id="continue" value="continue">
								{{trans("common.continue")}} <i class="fa fa-arrow-right"></i></button>
								<button type="reset" name="cancel" class="btn default" onclick="window.location = '{{ URL::to('users/my-withdrawals') }}'">
								<i class="fa fa-times"></i> {{trans("common.cancel")}}</button>
							</div>
						</div>

					@elseif($d_arr['preview_form'])
						<div class="form-group {{{ $errors->has('withdraw_amount') ? 'error' : '' }}}">
							{{ Form::label('user_balance', trans('myaccount/form.my-withdrawals.from_balance'), array('class' => 'col-md-3 control-label')) }}
							<div class="col-md-4">
								<p class="margin-top-8"><span class="text-muted">{{ $d_arr['withdraw_currency'] }}</span> <strong>{{ CUtil::formatAmount($amount) }}</strong></label>
								<input type="hidden" name="withdraw_currency" id="withdraw_currency" value="{{ $d_arr['withdraw_currency']}}"/>
								<input type="hidden" name="user_balance" id="user_balance" value="{{ $d_arr['user_balance']}}"/>
							</div>
						</div>

						<div class="form-group {{{ $errors->has('request_amount') ? 'error' : '' }}}">
							{{ Form::label('request_amount', trans('myaccount/form.my-withdrawals.request_amount'), array('class' => 'col-md-3 control-label required-icon')) }}
							<div class="col-md-4">
								<input type="hidden" name="request_amount" id="request_amount" value="{{ $d_arr['request_amount']}}" class="form-control valid"/>
								<p class="margin-top-8"><span class="text-muted">{{ $d_arr['withdraw_currency'] }}</span> <strong>{{ $d_arr['request_amount'] }}</strong></p>
							</div>
						</div>

						@if($d_arr['withdraw_fee'] > 0)
							<div class="form-group {{{ $errors->has('withdraw_fee') ? 'error' : '' }}}" >
								{{ Form::label('withdraw_fee', trans('myaccount/form.my-withdrawals.fee_lbl'), array('class' => 'col-md-3 control-label')) }}
								<div class="col-md-4">
									 <p class="margin-top-8"><span class="text-muted">{{ $d_arr['withdraw_currency'] }}</span> <strong>{{ $d_arr['withdraw_fee'] }}</strong></p>
								</div>
							</div>
						@endif

						<div class="form-group {{{ $errors->has('withdraw_fee') ? 'error' : '' }}}" >
							{{ Form::label('withdraw_fee', trans('myaccount/form.my-withdrawals.amount_you_withdraw'), array('class' => 'col-md-3 control-label')) }}
							<div class="col-md-4">
								 <p class="margin-top-8"><span class="text-muted">{{ $d_arr['withdraw_currency'] }}</span> <strong>{{ $d_arr['balance_amount'] }}</strong></p>
							</div>
						</div>

						<div class="form-group {{{ $errors->has('pay_to_details') ? 'error' : '' }}}" >
							{{ Form::label('pay_to_details', trans('myaccount/form.my-withdrawals.to_details'), array('class' => 'col-md-3 control-label')) }}
							<div class="col-md-7">
								 <p class="margin-top-8">{{ nl2br($d_arr['pay_to_details']) }}</p>
								 <input type="hidden" name="pay_to_details" id="pay_to_details" value="{{ $d_arr['pay_to_details']}}"/>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-offset-3 col-md-10">
								<input type="hidden" name="transfer_thru" id="transfer_thru" value="{{ $d_arr['transfer_thru']}}"/>
								<button type="submit" name="edit_request" class="btn btn-info" id="edit_request" value="edit_request">
								<i class="fa fa-arrow-left"></i> {{trans("common.back")}}</button>
								<button type="reset" name="cancel_request" class="btn default" onclick="window.location = '{{ URL::to('users/my-withdrawals') }}'">
								<i class="fa fa-times"></i> {{trans("common.cancel")}}</button>
								<button type="submit" name="submit_request" class="btn green" id="submit_request" value="continue">
								<i class="fa fa-check"></i> {{trans("common.submit")}}</button>
							</div>
						</div>
					@endif
				</fieldset>
			{{ Form::close() }}
		@else
			<p class="note note-info margin-0">
				<i class="fa fa-warning text-orange"></i> {{ str_replace("VAR_CURRENCY", $d_arr['withdraw_currency'], trans('myaccount/form.my-withdrawals.insufficient_fund_err_msg'))  }}
				<a href="{{ URL::to('users/my-withdrawals') }}"> <strong>{{trans('common.click_here')}}</strong> </a> {{trans('myaccount/form.my-withdrawals.to_go_back_main_page')}}
			</p>
		@endif
	</div>
	<!-- END: WITHDRAWALS FORM -->

	<script type="text/javascript">
		var page_name = "manage_withdrawal";
		@if(isset($d_arr['allow_withdrawal']) && $d_arr['allow_withdrawal'])
			var allow_withdrawal =  "{{ $d_arr['allow_withdrawal'] }}";
			var mes_required = "{{trans('auth/form.required')}}";
			var invalid_price = "{{ trans('common.invalid_price') }}";
			var minimum_amount = parseFloat('{{ Config::get("payment.minimum_withdrawal_amount") }}');
			var minimum_amount_inr = parseFloat('{{ Config::get("payment.minimum_withdrawal_amount_inr") }}');
			var minimum_allowed = '({{trans('walletAccount.minimum_allowed')}} ';
		@endif
	</script>
@stop