@extends('adminPopup')
@section('content')
    <!-- BEGIN: TITLE PAGE -->
	<h1>{{ Lang::get('myPurchases.canellation_action') }}</h1>
	<!-- END: TITLE PAGE -->
	@if(count($invoice_details) > 0)
	   <!-- BEGIN: INVOICE DETAILS -->
	    <div class="pop-content">
	    	<div id="error_msg_div"></div>
	    	<div id="success_block" style="display:none;">
	    		<a href="javascript:;" itemprop="url" onclick="closeFancyBox()">
	                <button type="reset" class="btn danger"><i class="fa fa-times"></i> {{ trans('common.close')}}</button>
	            </a>
			</div>
	    	<div id="form_block">
		        {{ Form::open(array('url' => URL::action('AdminPurchasesController@postResponseCancel'), 'class' => 'form-horizontal',  'id' => 'cancellation_frm', 'name' => 'cancellation_frm')) }}
					{{Form::hidden('order_id', $invoice_details['order_id'], array('id' => 'order_id'))}}
					{{Form::hidden('item_id', $invoice_details['item_id'], array('id' => 'item_id'))}}
					{{Form::hidden('refund_action', $refund_action, array('id' => 'refund_action'))}}
					{{Form::hidden('admin_refund_amount', $invoice_details['item_site_commission'], array('class' => 'form-control', 'id' => 'admin_refund_amount'))}}
					<?php
						$default_currency = Config::get('generalConfig.site_default_currency');
						$user_notes_label = ($refund_action == 'rejected') ? 'User notes' : 'Transaction details/User notes';
					?>
					@if($refund_action == 'rejected')
                        <div>
                            {{ Lang::get('myPurchases.reject_cancel_confirm') }}
                        </div>
		            @endif
                    <div class="admin-dl mb30">
                        <div class="dl-horizontal">
                            <dl><dt>{{ trans('myPurchases.reason_for_cancellation') }}</dt><dd><span>{{ $invoice_details['refund_reason'] }}</span></dd></dl>
                            <dl><dt>{{ Lang::get('myPurchases.invoice_id') }}</dt><dd><span>{{ $invoice_details['id'] }}</span></dd></dl>
                            <dl><dt>{{ Lang::get('myPurchases.product') }}</dt><dd><span>{{ $product_details['product_name'] }}</span></dd></dl>
                            <dl><dt>{{ Lang::get('myPurchases.quantity') }}</dt><dd><span>{{ $invoice_details['item_qty'] }}</span></dd></dl>
                            <dl><dt>{{ Lang::get('myPurchases.item_amount')}}</dt><dd><span>{{ $invoice_details['item_amount'] }}</span></dd></dl>
                            <dl><dt>{{ Lang::get('myPurchases.shipping_fee') }}</dt>
							<dd><span>{{ CUtil::convertAmountToCurrency($invoice_details['shipping_fee'], Config::get('generalConfig.site_default_currency'), '', true) }}</span></dd></dl>
                            @if(isset($invoice_details['tax_ids']) && $invoice_details['tax_ids'])
                                <?php
                                    $order_item_tax_split_arr = array();
                                    $order_item_tax_split_arr = explode(",", $invoice_details['tax_ids']);
                                    $order_item_amounts_split_arr = explode(",", $invoice_details['tax_amounts']);
                                    foreach($order_item_tax_split_arr as $inc => $tax_id) {
                                        $amount = isset($order_item_amounts_split_arr[$inc]) ? $order_item_amounts_split_arr[$inc] : 0;
                                        $tax_info = Webshoptaxation::Taxations()->getTaxations(array('id' => $tax_id), 'first', array('include_deleted' => true));
                                        echo '<dl><dt>'.(isset($tax_info['tax_name']) ? $tax_info['tax_name'] : '').'</dt><dd><span>'.CUtil::convertAmountToCurrency($amount, Config::get('generalConfig.site_default_currency'), '', true).'</span></dd></dl>';
                                    }
                                ?>
                            @endif
                            <dl><dt>{{ Lang::get('myPurchases.total_amount') }}</dt><dd><span>{{ CUtil::convertAmountToCurrency($invoice_details['item_total_amount'], Config::get('generalConfig.site_default_currency'), '', true) }}</span></dd></dl>
                        </div>
                    </div>
	                <!--<div class="note note-info mb30">
		                {{ Lang::get('myPurchases.total_amount') }}: {{ $productService->getBaseAmountToDisplay($invoice_details['item_total_amount'], $default_currency) }}
		            </div>-->
		            <div class="note note-info mb30">
		                {{ Lang::get('myPurchases.site_commission') }}: {{ CUtil::convertAmountToCurrency($invoice_details['item_site_commission'], Config::get('generalConfig.site_default_currency'), '', true) }}
		            </div>
		            <?php /*@if($refund_action == 'accept')
                        <div class="form-group">
                            {{ Form::label('seller_refund_paypal_amount', 'Amount transfered via Paypal', array('class' => 'col-sm-4 control-label')) }}
                            <div class="col-sm-6">
                                {{ Form::text('seller_refund_paypal_amount', Null, array('class' => 'form-control','id' => 'seller_refund_paypal_amount')) }}
                                <label class="error">{{{ $errors->first('seller_refund_paypal_amount') }}}</label>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('seller_refund_amount', 'Amount credited to Wallet', array('class' => 'col-sm-4 control-label')) }}
                            <div class="col-sm-6">
                                {{ Form::text('seller_refund_amount', Null, array('class' => 'form-control','id' => 'seller_refund_amount')) }}
                                <label class="error">{{{ $errors->first('seller_refund_amount') }}}</label>
                            </div>
                        </div>
		            @else */ ?>
                        {{Form::hidden('seller_refund_paypal_amount', '', array('id' => 'seller_refund_paypal_amount'))}}
                        {{Form::hidden('seller_refund_amount', '', array('id' => 'seller_refund_amount'))}}
		            <?php /*@endif*/ ?>
		            <!--<div class="form-group">
		                {{ Form::label('user_notes', $user_notes_label.'', array('class' => 'col-sm-4 control-label required-icon')) }}
		                <div class="col-sm-7">
		                    {{ Form::textarea('user_notes', Null, array('class' => 'form-control', 'id' => 'user_notes', 'rows' => '6')) }}
		                    <label class="error">{{{ $errors->first('user_notes') }}}</label>
		                </div>
		            </div>-->
		            <div class="form-group">
		                {{ Form::label('refund_response', Lang::get('myPurchases.admin_notes') , array('class' => 'col-sm-2 control-label required-icon')) }}
		                <div class="col-sm-7">
		                    {{ Form::textarea('refund_response', Null, array('class' => 'form-control', 'id' => 'refund_response', 'rows' => '6')) }}
		                    <label class="error">{{{ $errors->first('refund_response') }}}</label>
		                </div>
		            </div>
		            <div class="form-group">
		                 <div class="col-sm-8 col-sm-offset-2">
				            <button type="button" onclick="updateResponseCancel('{{ $invoice_details['id'] }}');" class="btn btn-success ">
							<i class="fa fa-check"></i> {{trans('common.submit')}}</button>
				            <a href="javascript:;" itemprop="url" onclick="closeFancyBox()">
		                        <button type="reset" class="btn default"><i class="fa fa-times"></i> {{trans('common.cancel')}}</button>
		                    </a>
		                </div>
		            </div>
			    {{ Form::close() }}
		    </div>
	    </div>
		<!-- END: INVOICE DETAILS -->
	@endif

	<script language="javascript" type="text/javascript">
		var ajax_proceed = 0;
		var refund_action = $('#refund_action').val();

		jQuery.validator.addMethod("decimallimit", function (value, element) {
			return this.optional(element) || /^[0-9]*(\.\d{0,2})?$/i.test(value);
		}, "Only two decimals allowed");

		jQuery.validator.addMethod("chkPaypalAndCreditAmountEmpty", function(value, element) {
				if(refund_action == 'accept') {
					var seller_refund_paypal_amount = $('#seller_refund_paypal_amount').val();
					var seller_refund_amount = $('#seller_refund_amount').val();
					if(seller_refund_paypal_amount == '' && seller_refund_paypal_amount == '')
						return false;
				}
				return true;
		},"Either Paypal amount or Credit amount mandatory");

		$("#cancellation_frm").validate({
				rules: {
						/*seller_refund_paypal_amount: {
							required: {
								depends: function(element) {
									return (refund_action == 'accept') ? true : false;
								}
							},
							chkPaypalAndCreditAmountEmpty: true,
							number: true,
							decimallimit: true
						},
						seller_refund_amount: {
							required: {
								depends: function(element) {
									return (refund_action == 'accept') ? true : false;
								}
							},
							number: true,
							decimallimit: true
						},
						user_notes: {
							required: true
						},*/
						refund_response: {
							required: true
						}
					},
					messages: {
						/*seller_refund_paypal_amount: {
							required: mes_required
						},
						seller_refund_amount: {
							required: mes_required
						},
						user_notes: {
							required: mes_required
						},*/
						refund_response: {
							required: mes_required
						}
					},
				submitHandler: function(form) {
				form.submit();
			}
		});

		function closeFancyBox() {
			parent.$.fancybox.close();
		}

		function updateResponseCancel(invoice_id) {
			if($("#cancellation_frm").valid())
			{
				var refund_action = $('#refund_action').val();
				var refund_amount = $('#admin_refund_amount').val();
				var refund_response = $('#refund_response').val();
				var user_notes = $('#user_notes').val();
				var seller_refund_amount = $('#seller_refund_amount').val();
				var seller_refund_paypal_amount = $('#seller_refund_paypal_amount').val();
				var item_id = $('#item_id').val();

				var div_id = 'error_msg_div';

				if(refund_action == 'accept')
					refund_action = 'Yes';

				parent.displayLoadingImage(true);

				if(ajax_proceed)
					ajax_proceed.abort();

				var params = {"invoice_id": invoice_id, "refund_action":refund_action, "refund_response": refund_response, "user_notes": user_notes, "refund_amount": refund_amount, "seller_refund_amount":seller_refund_amount, "seller_refund_paypal_amount":seller_refund_paypal_amount, "item_id": item_id };
				ajax_proceed = $.post("{{ Url::action('AdminPurchasesController@postResponseCancel')}}", params, function(data) {
					if(data) {
						var data_arr = data.split("|~~|");
						if(data_arr.length > 1) {
							if(data_arr[0] == "success") {
								$('#'+div_id).html('<div class="note note-success mt10">'+data_arr[1]+'</div>');
								$('#form_block').hide();
								$('#success_block').show();
								//window.location.reload();
							}
							else if(data_arr[0] == "error")
							{
								$('#'+div_id).html('<div class="note note-danger mt10">'+data_arr[1]+'</div>');
							}
							else
							{
								window.location.reload();
							}
						}
						else {
							window.location.reload();
						}
						parent.hideLoadingImage(false);
					}
				})
			}
		}
	</script>
@stop