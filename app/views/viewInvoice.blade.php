@extends('base')
@section('content')
	<?php $status = $s; ?>
	<div class="row">
		<div class="col-md-2 clearfix">
			<!-- MANAGE ACCOUNT STARTS -->
			@include('myaccount.myAccountMenu')
			<!-- MANAGE ACCOUNT ENDS -->
		</div>

		<div class="col-md-10">
			<div class="responsive-pull-none">
				<a href="{{ URL::to('invoice').'?status='.$status }}" class="pull-right btn btn-xs blue-stripe default">
					<i class="fa fa-chevron-left"></i> {{ trans('myInvoice.my_invoice_list') }}
				</a>
				{{--<title>{{ Lang::get('myInvoice.invoice_details') }}</title>--}}
				<h1>{{ Lang::get('myInvoice.invoice_details') }}</h1>
			</div>

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

			<!-- ALERT sudopay STARTS -->
			<div class="alert alert-danger" style="display:none;">
				<span id="buyer_fees_fourmula_disp"></span>
			</div>
			<!-- ALERT sudopay ENDS -->

			<div class="well">
				@if(count($invoice_details) <= 0)
					<div class="alert alert-info margin-0">
					   {{ Lang::get('myInvoice.invalid_id') }}
					</div>
				@else
					@if(count($invoice_details) > 0)
						{{ Form::open(array('action' => array('InvoiceController@getIndex'), 'id'=>'invoicefrm', 'method'=>'get','class' => 'form-horizontal view-invcdet' )) }}
						<?php
							$user_details = CUtil::getUserDetails($invoice_details->user_id);
							$user_name = $user_details['display_name'];
						?>
							<div class="row">
								<div class="col-md-6">
                                    <div class="portlet bg-form min-hig180">
                                    	<h2 class="title-one">Invoice Details</h2>
                                        <div class="dl-horizontal dl-horizontal-new">
                                            <dl>
                                                <dt>{{ trans('myInvoice.invoice_from') }}</dt>
                                                <dd>
                                                    @if($invoice_details->reference_type == 'Usercredits')
                                                        <span>{{ $user_name }}</span>
                                                    @else
                                                        <span>{{ trans('myInvoice.site_name') }}</span>
                                                    @endif
                                                </dd>
                                            </dl>
                                            <dl>
                                                <dt>{{ trans('myInvoice.invoice_to') }}</dt>
                                                <dd><span>{{ $user_name }}</span></dd>
                                            </dl>
                                        </div>
									</div>
								</div>

								<div class="col-md-6">
									<div class="portlet bg-form min-hig180">
										<h2 class="title-one">Payment Status</h2>
										<div class="dl-horizontal dl-horizontal-new">
											<?php
												$lbl_class = '';
												if($invoice_details->status == 'Paid') {
													$lbl_class = "label-success";
												}
												elseif($invoice_details->status == 'Unpaid') {
													$lbl_class = "label-warning";
												}
											?>
											<dl>
                                                <dt>Status</dt>
                                                <dd><span><span class="label {{ $lbl_class }}">{{ $invoice_details->status }}</span></span></dd>
                                            </dl>
											@if($invoice_details->date_added != '0000-00-00 00:00:00')
												<dl>
                                                    <dt>{{ trans('myInvoice.invoice_added') }}</dt>
                                                    <dd><span>{{ CUtil::FMTDate($invoice_details->date_added, "Y-m-d H:i:s", "") }}</span></dd>
                                                </dl>
											@else
												<dl><dt>{{ trans('myInvoice.invoice_added') }}</dt> <dd><span> - </span></dd></dl>
											@endif
											@if($invoice_details->status == 'Paid')
												@if($invoice_details->date_added != '0000-00-00 00:00:00')
													<dl>
                                                        <dt>{{ trans('myInvoice.paid_date') }}</dt>
                                                        <dd><span>{{ CUtil::FMTDate($invoice_details->date_paid, "Y-m-d H:i:s", "") }}</span></dd>
                                                    </dl>
												@else
													<dl>
                                                        <dt>{{ trans('myInvoice.paid_date') }}</dt>
                                                        <dd><span> - </span></dd>
                                                    </dl>
												@endif
											@endif
											@if($invoice_details->status == 'Unpaid' && $invoice_details->reference_type == 'Products')
												<?php
													$currency = $invoice_details->invoice_currency;
													$amount = $invoice_details->amount;
												?>
												<!-- PAY PAL PAYMANT START -->
												<div class="well">
													<h2 class="title-one">{{ trans('payCheckOut.pay_through_label') }}</h2>
													<?php
														if($d_arr['sudopay_brand'] == 'SudoPay Branding') {
															echo $d_arr['sc']->displayJSBtn($d_arr['sudopay_fields_arr'], false, 'sudopaybtn', true);
														}
														else {
															if($gateways_arr = $d_arr['sc']->checkGateways('Marketplace-Capture')) {
																echo $d_arr['sc']->displayGatewaysNew('Marketplace-Capture', false, 'No', $gateways_arr);
															}
														}
														$discounted_amount_cal = CUtil::convertAmountToCurrency($amount, Config::get('generalConfig.site_default_currency'), '', false, false, true);
													?>
													{{ Form::hidden('sudopay_fees_payer', $d_arr['sudopay_fees_payer'], array('id' => 'sudopay_fees_payer')) }}
													{{ Form::hidden('parent_gateway_id', '', array('id' => 'parent_gateway_id')) }}
												</div>
												<!-- PAY PAL PAYMANT END -->
											@endif
										</div>
									</div>
								</div>
							</div>

							<!-- INVOICE DETAILS LIST STARTS -->
							<div class="table-responsive margin-bottom-30">
								<table class="table table-hover">
									<thead>
										<tr>
											<th class="col-md-10">{{ Lang::get('myInvoice.description') }}</th>
											<th class="text-right">{{ Lang::get('myInvoice.amount') }}</th>
										</tr>
									</thead>

									<tbody>
										@if(count($invoice_details) > 0)
											<tr>
												<td><div class="wid-400">{{ $invoice_details->user_notes }}</div></td>
												<td class="text-right">
													<span class="text-muted">{{ $invoice_details->invoice_currency }}</span> <strong>{{ $invoice_details->amount }}</strong>
												</td>
											</tr>
											<tr>
												<td class="text-right fonts18">{{ Lang::get('myInvoice.total_amount') }}:</td>
												<td class="fonts18 text-right">
													<span class="text-muted">{{ $invoice_details->invoice_currency }}</span> <strong>{{ $invoice_details->amount }}</strong>
												</td>
											</tr>
										@else
											<tr>
												<td colspan="4"><p class="alert alert-info">{{ Lang::get('myInvoice.invalid_id') }}</p></td>
											</tr>
										@endif
									</tbody>
								</table>
							</div>
							<!-- INVOICE DETAILS LIST END -->
						{{ Form::close() }}
					@endif
				@endif
			</div>
		</div>
	</div>
    <div id="paypal_form" style="display:none;"></div>
@stop

@section('script_content')
	<script language="javascript" type="text/javascript">

		var pay_via_paypal = "{{ trans('payCheckOut.pay_via_paypal') }}";
		var common_invoice_id_val = "{{ $invoice_details->common_invoice_id }}";
		var proceedpayment = "{{ Url::to('proceedpayment')}}";
		var payment_gateway_revised_amount_txt = "{{ trans('sudopay::sudopay.payment_gateway_revised_amount') }}";
		var discounted_amount_bf_revise = 0;
		var discounted_currency_bf_revise = '';
		@if($invoice_details->status == 'Unpaid' && $invoice_details->reference_type == 'Products')
			var discounted_amount_bf_revise = "{{ $discounted_amount_cal['amt'] }}";
			var discounted_currency_bf_revise = "{{ $discounted_amount_cal['currency_symbol'] }}";
		@endif
		var amount = "{{ $invoice_details->amount }}";

		$(document).ready(function() {
			$("#invoicefrm").validate({
				rules: {
					credit_card_number: {
						required: true
					},
					credit_card_expire: {
						required: true
					},
					credit_card_name_on_card: {
						required: true
					},
					credit_card_code: {
						required: true
					},
				},
				messages: {
					credit_card_number: {
						required: mes_required,
					},
					credit_card_expire: {
						required: mes_required,
					},
					credit_card_name_on_card: {
						required: mes_required,
					},
					credit_card_code: {
						required: mes_required,
					},
				},
				/* For Contact info violation */
				submitHandler: function(form) {
					form.submit();
				}
			});
		});

		function proceedPayment(payment_mode, payment_gateway_chosen)
		{
			var parent_gateway_id = $('#parent_gateway_id').val();
			var response = true;
			//var validate = $("#addressValidatioinFrm").validate({ });

			if(parent_gateway_id == ''){
				parent_gateway_id = 4922;
			}

		//	if($("#addressValidatioinFrm").valid()) {
			var valid = true;
			if(parent_gateway_id == 4922){
				var valid = $('#invoicefrm').valid();
			}

			if(valid == false) {
				return false;
			} else {
				if(parent_gateway_id == 4922) {
					var response = cardValidation();
				}
				if(response == false) {
					return false;
				} else {
					var gateway_id = 0;
					if(parent_gateway_id == 4922 || parent_gateway_id == 5333) {
						if($('input[name=gateway_id_'+parent_gateway_id+']:checked').length > 0)
							gateway_id = $('input[name=gateway_id_'+parent_gateway_id+']:checked').val();
					}

					if(ajax_proceed)
					{
						ajax_proceed.abort();
						displayLoadingImage(false);
					}

					var common_invoice_id = 0;
					var currency_code = payment_mode;

					var d_arr = [];
			        sudopay_arr = {};
			        sudopay_arr['sudopay_fees_payer'] = $("#sudopay_fees_payer").val();
			        sudopay_arr['credit_card_number'] = $("#credit_card_number").val();
			        sudopay_arr['credit_card_expire'] = $("#credit_card_expire").val();
			        sudopay_arr['parent_gateway_id'] = parent_gateway_id;
			        sudopay_arr['credit_card_name_on_card'] = $("#credit_card_name_on_card").val();
			        sudopay_arr['credit_card_code'] = $("#credit_card_code").val();
			        sudopay_arr['gateway_id'] = gateway_id;
			        d_arr.push(JSON.stringify(sudopay_arr));
					//console.log(d_arr);

					var params = {"common_invoice_id": common_invoice_id, "payment_gateway_chosen": payment_gateway_chosen, "currency_code": currency_code, "amount": amount, "d_arr[]": d_arr };

					displayLoadingImage(true);
					ajax_proceed = $.post(proceedpayment, params, function(data) {
						if(data) {
							var data_arr = data.split("|~~|");

							if(data_arr.length > 1) {
								window.location.href = data_arr[0];
							}
							else {
								displayLoadingImage(false);
								$("#paypal_form").html(data);
								document.getElementById("frmTransaction").submit();
							}
						}
					});
				}
			}
		}
	//}

	$(document).ready(function () {
		$("#credit_card_number").attr('maxlength','16');
	    $("#credit_card_expire").attr('maxlength','7');
	    $("#credit_card_code").attr('maxlength','4');

		$("#credit_card_number").keypress(function (e) {
			if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
				$("#card_error").html("Digits Only").show().fadeOut("slow");
				return false;
			}
		});

		$("#credit_card_code").keypress(function (e) {
			if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
				$("#card_error").html("Numbers Only").show().fadeOut("slow");
				return false;
			}
		});

		defaultGatewaySetter();
		calcBuyerFeesFormula();
	});

	$("#card_error").hide();

	function defaultGatewaySetter()
	{
		$('.fnGatwayFinder').each(function(){
			if($(this).hasClass("active")) {
				var element_id = $(this).attr("id");
				var data_arr = element_id.split("-");
				if(data_arr.length > 1) {
					$('#parent_gateway_id').val(data_arr[1]);
				}
			}
		});
	}

	function hideCreditCard(id){
		if(id == 5333){
			$('.js-form-tpl-credit_card').hide();
			$('#parent_gateway_id').val(id);

		}else{
			$('.js-form-tpl-credit_card').show();
			$('#parent_gateway_id').val(id);
		}
		calcBuyerFeesFormula();
	}

	function calcBuyerFeesFormula() {
		//4922 = credit card, 5333 = electronics gateways
		var buyer_fees_formula = '';
		var gateway_id = 0;
		var parent_gateway_id = $('#parent_gateway_id').val();
		if(parent_gateway_id == '') {
			parent_gateway_id = 4922;
		}
		if(parent_gateway_id == 4922 || parent_gateway_id == 5333) {
			if($('input[name=gateway_id_'+parent_gateway_id+']:checked').length > 0) {
				gateway_id = $('input[name=gateway_id_'+parent_gateway_id+']:checked').val();
			}
		}

		if(gateway_id > 0) {
			if($('#buyer_fees_formula_' + parent_gateway_id + '_' + gateway_id).length > 0) {
				buyer_fees_formula = $('#buyer_fees_formula_' + parent_gateway_id + '_' + gateway_id).val();
			}
			if($('#wallet_gateway_name_disp').length > 0) {
				if($('#sudopay_gateway_' + parent_gateway_id + '_' +gateway_id).length > 0)
					gateway_name = $('#sudopay_gateway_' + parent_gateway_id + '_' +gateway_id).data("gateway-name");
				$('#wallet_gateway_name_disp').html(gateway_name);
			}
		}
		if(buyer_fees_formula != '') {
			var amount = eval(discounted_amount_bf_revise).toFixed(2);
			var revised_amount = eval(buyer_fees_formula).toFixed(2);
			//var formula = buyer_fees_formula;
			//$('#buyer_fees_fourmula_disp').html(discounted_currency_bf_revise + ' ' + eval(buyer_fees_formula));
			if(amount != revised_amount) {
				var revised_txt = payment_gateway_revised_amount_txt.replace(/VAR_CURRENCY/g, discounted_currency_bf_revise);
				var revised_txt = revised_txt.replace(/VAR_AMOUNT/g, amount);
				var revised_txt = revised_txt.replace(/VAR_REVISED_AMOUNT/g, eval(buyer_fees_formula).toFixed(2));
				$('#buyer_fees_fourmula_disp').text(revised_txt);
				$('#buyer_fees_fourmula_disp').parent('.alert').show();
			}
			else {
				$('#buyer_fees_fourmula_disp').parent('.alert').hide();
			}
		}
	}
	//Sudo pay script end
	</script>
@stop