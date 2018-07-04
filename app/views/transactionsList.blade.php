@extends('base')
@section('content')
	<div class="row">
		<div class="col-md-2 clearfix">
			<!-- BEGIN: MANAGE ACCOUNT -->
			@include('myaccount.myAccountMenu')
			<!-- END: MANAGE ACCOUNT -->
		</div>

		<div class="col-md-10">
			<!-- BEGIN: PAGE TITLE -->
			<div class="responsive-pull-none">
				<h1>{{ Lang::get('myPurchases.transaction_history') }}</h1>
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

			<div class="well">
				{{ Form::open(array('action' => array('TransactionsController@getIndex'), 'id'=>'productFrm', 'method'=>'get','class' => 'form-horizontal' )) }}
				<!-- END: SEARCH BLOCK -->
				<div id="search_holder" class="portlet bg-form">
					<div class="portlet-title">
						<div class="caption">
							{{ Lang::get('myPurchases.search_transactions') }}
						</div>
						<div class="tools">
							<a class="collapse" href="javascript:;"></a>
						</div>
					</div>

					<div id="selSrchProducts" class="portlet-body">
						<fieldset>
							<div class="form-group fn_clsPriceFields {{{ $errors->has('from_date') ? 'error' : '' }}}">
	                            {{ Form::label('from_date', trans("transaction.transaction_dates"), array('class' => 'col-md-2 control-label')) }}
	                            <div class="col-md-3">
	                                <div class="input-group date date-picker input-daterange col-xs-12" data-date-format="dd-mm-yyyy">
	                                    {{ Form::text('from_date', Input::old('from_date', Input::get('from_date')), array('id'=>"from_date", 'class'=>'form-control', 'maxlength'=>'100')) }}
										<label for="date_added_to" class="input-group-addon">{{ trans('common.to') }}</label>
	                                    {{ Form::text('to_date', Input::old('to_date', Input::get('to_date')), array('id'=>"to_date", 'class'=>'form-control', 'maxlength'=>'100')) }}
	                                </div>
	                                <label class="error">{{{ $errors->first('from_date') }}}</label>
	                            </div>
	                        </div>

							<div class="form-group">
	                            {{ Form::label('transaction_id', Lang::get('transaction.paypal_transaction_id'), array('class' => 'control-label col-md-2')) }}
	                            <div class="col-md-3">
	                                {{ Form::text('transaction_id', Input::get("transaction_id"), array('class' => 'form-control')) }}
	                            </div>
	                        </div>

	                        <div class="form-group">
								<div class="col-md-offset-2 col-md-8">
									<button type="submit" name="srchproduct_submit" value="srchproduct_submit" class="btn purple-plum">
									<i class="fa fa-search"></i> {{ Lang::get('taxation.search') }}</button>
									<button type="reset" name="srchproduct_reset" value="srchproduct_reset" class="btn default" onclick="javascript:location.href='{{ Request::url() }}'">
									<i class="fa fa-rotate-left"></i> {{ Lang::get('taxation.reset') }}</button>
								</div>
							</div>
						</fieldset>
					</div>
				</div>
				<!-- END: SEARCH BLOCK -->
				{{ Form::close() }}

				@if(count($transaction_details) > 0)
					<!-- BEGIN: TRANSACTION LIST -->
					<div class="table-responsive margin-bottom-30">
						<table class="table table-bordered table-hover table-striped">
							<thead>
								<tr>
									<th class="col-md-2">{{ Lang::get('myPurchases.date') }}</th>
									<th class="col-md-2">{{ Lang::get('myPurchases.amount_credited') }}</th>
									<th class="col-md-2">{{ Lang::get('myPurchases.amount_debited') }}</th>
									<th class="col-md-7">{{ Lang::get('myPurchases.transaction_notes') }}</th>
								</tr>
							</thead>

							<tbody>
								@if(count($transaction_details) > 0)
									@foreach($transaction_details as $transaction)
										<tr>
											<td><div class="wid-100 text-muted">{{ CUtil::FMTDate($transaction->date_added, 'Y-m-d H:i:s', ''); }}</div></td>
											<td>
												<?php
													if(count($transaction) > 0) {
														if($transaction['transaction_type'] == 'credit') {
															$lbl_class = "label-success";
															?>
															<span class="label {{ $lbl_class }}">{{ ucwords($transaction->transaction_type) }}</span>
															&nbsp;{{ CUtil::convertAmountToCurrency($transaction->amount, Config::get('generalConfig.site_default_currency'), '', true) }}
															<?php
														}
														elseif($transaction['transaction_type'] == 'pending_credit') {
															$lbl_class = "label-warning";
															?>
															<span class="label {{ $lbl_class }}">{{ ucwords($transaction->transaction_type) }}</span>
															&nbsp;{{ CUtil::convertAmountToCurrency($transaction->amount, Config::get('generalConfig.site_default_currency'), '', true) }}
															<?php
														}
													}
												?>
											</td>
											<td>
												<?php
													if(count($transaction) > 0) {
														if($transaction['transaction_type'] == 'debit') {
															$lbl_class = " label-info";
															?>
															<span class="label {{ $lbl_class }}">{{ ucwords($transaction->transaction_type) }}</span>
															&nbsp;{{ CUtil::convertAmountToCurrency($transaction->amount, Config::get('generalConfig.site_default_currency'), '', true) }}
															<?php
														}
														elseif($transaction['transaction_type'] == 'pending_debit') {
															$lbl_class = "label-primary";
															?>
															<span class="label {{ $lbl_class }}">{{ ucwords($transaction->transaction_type) }}</span>
															&nbsp;{{ CUtil::convertAmountToCurrency($transaction->amount, Config::get('generalConfig.site_default_currency'), '', true) }}
															<?php
														}
													}
												?>
											</td>
											<td><div class="wid-400">{{ $transaction_description[$transaction->id] }}</div></td>
										</tr>
									@endforeach
								@else
									<tr>
										<td colspan="4"><p class="alert alert-info">{{ Lang::get('myPurchases.no_result') }}</p></td>
									</tr>
								@endif
							</tbody>
						</table>
					</div>
					@if(count($transaction_details) > 0)
						<div class="text-right">
							{{ $transaction_details->appends(array('to_date' => Input::get('to_date'), 'from_date' => Input::get('from_date'), 'transaction_id' => Input::get('transaction_id')))->links() }}
						</div>
					@endif
					{{ Form::open(array('id'=>'productsActionfrm', 'method'=>'post', 'url' => URL::to('myproducts/deleteproduct'))) }}
						{{ Form::hidden('p_id', '', array('id' => 'p_id')) }}
						{{ Form::hidden('product_action', '', array('id' => 'product_action')) }}
					{{ Form::close() }}
					<!-- END: TRANSACTION LIST -->
					<div id="dialog-product-confirm" class="confirm-dialog-delete" title="" style="display:none;">
						<span class="ui-icon ui-icon-alert"></span>
						<span id="dialog-product-confirm-content" class="show"></span>
					</div>
				@else
					<div class="note note-info margin-0">
					   {{ Lang::get('myPurchases.no_transaction') }}
					</div>
				@endif
			</div>
		</div>
	</div>
@stop

@section('script_content')
	<script type="text/javascript">
	      $('.fn_clsDropSearch').click(function() {
	        $('#search_holder').slideToggle(500);
	        // toggle open/close symbol
	        var span_elm = $('.fn_clsDropSearch i');
	        if(span_elm.hasClass('fa fa-caret-up')) {
	            $('.fn_clsDropSearch').html('{{ Lang::get('product.show_search_filters') }} <i class="fa fa-caret-down"></i>');
	        } else {
	            $('.fn_clsDropSearch').html('{{ Lang::get('product.hide_search_filters') }} <i class="fa fa-caret-up"></i>');
	        }
	        return false;
	    });

		$(function() {
            $('#from_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });
            $('#to_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });
        });

	    function doAction(p_id, selected_action)
		{
			if(selected_action == 'delete')
			{
				$('#dialog-product-confirm-content').html('{{ Lang::get('myProducts.product_confirm_delete') }}');
			}
			else if(selected_action == 'feature')
			{
				$('#dialog-product-confirm-content').html('{{ Lang::get('myProducts.product_confirm_featured') }}');
			}
			else if(selected_action == 'unfeature')
			{
				$('#dialog-product-confirm-content').html('{{ Lang::get('myProducts.product_confirm_unfeatured') }}');
			}
			$("#dialog-product-confirm").dialog({ title: '{{ Lang::get('myProducts.my_products_title') }}', modal: true,
				buttons: {
					"{{ Lang::get('common.yes') }}": function() {
						$(this).dialog("close");
						$('#product_action').val(selected_action);
						$('#p_id').val(p_id);
						document.getElementById("productsActionfrm").submit();
					}, "{{ Lang::get('common.cancel') }}": function() { $(this).dialog("close"); }
				}
			});

			return false;
		}

		$(".fn_changeStatus").fancybox({
	        maxWidth    : 800,
	        maxHeight   : 430,
	        fitToView   : false,
	        width       : '70%',
	        height      : '430',
	        autoSize    : false,
	        closeClick  : false,
	        type        : 'iframe',
	        openEffect  : 'none',
	        closeEffect : 'none'
	    });
	</script>
@stop