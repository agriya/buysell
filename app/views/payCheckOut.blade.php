@extends('base')
@section('content')
	@include('user_breadcrumb')
    <!-- PAGE TITLE STARTS -->
	<h1>{{ trans('payCheckOut.payment_title') }}</h1>
	<!-- PAGE TITLE END -->
	<?php
		$pay_page = true;
	?>

	<!-- ALERT BLOCK STARTS -->
	@if($error_msg != "")
		<?php
			$pay_page = false;
		?>
		<div class="note note-danger">{{ $error_msg }}</div>
	@endif
	@if(Session::has('success_free_message') && Session::get('success_free_message') != '')
        <div class="note note-success">{{ Session::get('success_free_message') }}</div>
        <?php
			$pay_page = false;
			Session::forget('success_free_message');
		?>
    <!-- ALERT BLOCK ENDS -->
    @else
    	<?php $show_currency = CUtil::getCheckDefaultCurrencyActivate();	?>
		@if(count($order_details) > 0 && $error_msg == "")
			<div class="alert alert-success">
				<p class="pull-right"><a href="{{ URL::action('CheckOutController@populateCheckOutItems', array($order_details->seller_id)) }}" class="btn btn-success btn-xs">
                	<i class="fa fa-reply"></i> {{ trans('payCheckOut.review_order') }}</a>
                </p>
				<span class="fonts18">
                	{{ trans('payCheckOut.total_purchase_amount') }}
					{{ CUtil::convertAmountToCurrency($discounted_amount, Config::get('generalConfig.site_default_currency'), '', true) }}
					@if($show_currency)
						({{ CUtil::convertAmountToCurrency($discounted_amount, Config::get('generalConfig.site_default_currency'), Config::get('generalConfig.site_default_currency'), true) }})
					@endif
                </span>
			</div>
			{{ Form::hidden('total_discounted_amount', $discounted_amount, array('id' => 'total_discounted_amount')) }}
			<div class="alert alert-danger" style="display:none;">
				<span id="buyer_fees_fourmula_disp"></span>
			</div>

            <!-- PAY THROUGH DETAILS STARTS -->
			@if($discounted_amount > 0)
				{{ Form::open(array('id'=>'ProcessPaymentCreditFrm')) }}
				@if(CUtil::chkIsAllowedModule('sudopay') && Config::get('plugin.sudopay_payment') && Config::get('plugin.sudopay_payment_used_product_purchase'))
					<?php if($d_arr['sudopay_brand'] == 'SudoPay Branding') { ?>
						<div class="well">
							<h2 class="title-one">{{ trans('payCheckOut.pay_through_label') }}</h2>
							<?php echo $d_arr['sc']->displayJSBtn($d_arr['sudopay_fields_arr'], false, 'sudopaybtn', true); ?>
						</div>
					<?php } else {  ?>
							<?php if ($gateways_arr = $d_arr['sc']->checkGateways('')) { ?>
								<div class="well">
									<h2 class="title-one">{{ trans('payCheckOut.pay_through_label') }}</h2>
									<?php echo $d_arr['sc']->displayGatewaysNew('', false, 'No', $gateways_arr); ?>
								</div>
							<?php } ?>
					<?php } ?>
					<?php $discounted_amount_cal = CUtil::convertAmountToCurrency($discounted_amount, Config::get('generalConfig.site_default_currency'), '', false, false, true); ?>
					{{ Form::hidden('sudopay_fees_payer', $d_arr['sudopay_fees_payer'], array('id' => 'sudopay_fees_payer')) }}
					{{ Form::hidden('parent_gateway_id', '', array('id' => 'parent_gateway_id')) }}
				@endif
				<?php
					$total_discounted_amount = CUtil::convertAmountToCurrency($discounted_amount, Config::get('generalConfig.site_default_currency'), '', false, false, true);
					$user_balance = CUtil::convertAmountToCurrency($user_account_balance['amount'], Config::get('generalConfig.site_default_currency'), '', false, false, true);
					$remaining_amount = $total_discounted_amount['amt'] - $user_balance['amt'];
				?>
				@if(Config::get('payment.wallet_payment') && Config::get('payment.wallet_payment_used_product_purchase'))
                <div class="row">
					<div class="col-md-12">
						<div class="well">
							<h2 class="title-one">{{ trans('payCheckOut.wallet_payment_label') }} ({{trans('payCheckOut.account_balance')}} {{ $user_balance['currency_symbol']}} <strong>{{ $user_balance['amt'] }}</strong>)</h2>
							{{ Form::hidden('wallet_payment', $user_balance['amt'], array('id' => 'wallet_payment')) }}
							<!-- DUMMY PAYMENT START -->
							<div id="dummy" class="tab-pane">
								<dl class="dl-horizontal-new">
									<dt><div class="btn default payment-btn"><i class="fa fa-money"></i></div></dt>
									<dd class="note note-info">
										<p class="margin-bottom-5"><strong>{{ trans('payCheckOut.paypal_payment_instructions') }}:</strong></p>
										@if($user_account_balance['amount'] > $discounted_amount)
											<p class="no-margin">{{ trans('payCheckOut.dummy_payment_instructions_msg') }}</p>
										@else
											<p class="no-margin">{{ trans('payCheckOut.wallet_payment_instructions_insufficient_amount') }}</p>
										@endif
									</dd>
								</dl>
								<div class="paypal-btn">
									@if($user_account_balance['amount'] > $discounted_amount)
										<button type="button" name="" value="Pay via Wallet" class="btn green" onclick="proceedPayment('USD', 'wallet', 'No');">
											<i class="fa fa-shopping-cart"></i> {{ trans('payCheckOut.buy_now') }}
										</button>
									@elseif(CUtil::chkIsAllowedModule('sudopay') && $user_account_balance['amount'] != 0)
										<button type="button" name="" value="{{ trans('payCheckOut.pay_via_paypal') }}" class="btn green" onclick="proceedPayment('USD', 'sudopay', 'Yes');">
											<i class="fa fa-shopping-cart"></i> {{ Lang::get('common.pay')}} {{ $total_discounted_amount['currency_symbol'] }} <span id="wallet_amount_disp">&nbsp;</span> {{ Lang::get('common.using')}} <span id="wallet_gateway_name_disp">&nbsp;</span>
										</button>
									@else
										<button type="button" name="" value="Pay via Wallet" disabled="disabled" class="btn green">
											<i class="fa fa-shopping-cart"></i> {{ trans('payCheckOut.buy_now') }}
										</button>
									@endif
								</div>
							</div>
							<!-- DUMMY PAYMENT END -->
						</div>
					</div>
                </div>
				@endif
			{{ Form::close() }}
			@else
				<?php
					$pay_url = URL::to('pay-checkout-free/'.$order_details['id']);
				?>
				{{ Form::open(array('id'=>'processfreepayment', 'method'=>'post', 'url' => URL::to('pay-checkout-free/'.$order_details['id']) )) }}
	                {{ Form::hidden('common_invoice_id', '', array('id' => 'common_invoice_id')) }}
	                {{ Form::hidden('card_type', '', array('id' => 'card_type')) }}
					{{ Form::hidden('payment_gateway_chosen', '', array('id' => 'payment_gateway_chosen')) }}
	            {{ Form::close() }}
				<div class="text-right"><button type="button" name="" value="{{ trans('payCheckOut.pay_via_paypal') }}" class="btn green" onclick="proceedFreePayment('{{ $pay_url }}');">
				Proceed <i class="fa fa-check margin-left-5"></i></button></div>
			@endif
            <!-- PAY THROUGH DETAILS ENDS -->
			<input id="reloadValue" type="hidden" name="reloadValue" value="" />
		@endif
	@endif
	<div id="paypal_form" style="display:none;"></div>
	<script language="javascript" type="text/javascript">
		//If back button pressed, then reload page
		var page_name = "pay_checkout";
		var pay_page = "{{ $pay_page }}";
		var pay_via_paypal = "{{ trans('payCheckOut.pay_via_paypal') }}";
		var proceedpayment = "{{ Url::to('proceedpayment')}}";
		@if($pay_page)
			var ajax_proceed = 0;
			var temp_currency_array = new Array();
			@if(isset($payment_curr_options['other_currency']) && count($payment_curr_options['other_currency']) > 0)
				@foreach($payment_curr_options['other_currency'] as $curr_index => $curr)
				temp_currency_array['{{$curr["currency"]}}'] = '{{$curr["text"]}}';
				@endforeach
			@endif
			//For sudo pay
			var payment_gateway_revised_amount_txt = "{{ trans('sudopay::sudopay.payment_gateway_revised_amount') }}";
			var discounted_amount_bf_revise = 0;
			var discounted_currency_bf_revise = "{{ Config::get('generalConfig.site_default_currency') }}";
			@if(isset($discounted_amount_cal))
				@if($discounted_amount > 0)
					var discounted_amount_bf_revise = "{{ $discounted_amount_cal['amt'] }}";
				@endif
				var discounted_currency_bf_revise = "{{ $discounted_amount_cal['currency_symbol'] }}";
			@endif
		@endif
		@if(isset($common_invoice_details) && count($common_invoice_details) > 0 && isset($common_invoice_details["common_invoice_id"]))
			var common_invoice_id_val = "{{ $common_invoice_details["common_invoice_id"] }}";
			var common_invoice_id = common_invoice_id_val;
		@else
			var common_invoice_id = 0;
			var common_invoice_id_val = 0;
		@endif
	</script>
@stop