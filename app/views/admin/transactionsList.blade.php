@extends('admin')
@section('content')
	<!-- BEGIN: NOTIFICATIONS -->
    @include('notifications')
    <!-- END: NOTIFICATIONS -->

    <!--- BEGIN: ERROR INFO --->
	@if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('error_message'); ?>
    @endif
    <!--- END: ERROR INFO --->

    <!--- BEGIN: SUCCESS INFO --->
    @if(Session::has('success_message') && Session::get('success_message') != '')
        <div class="note note-success">{{ Session::get('success_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif
    <!--- END: SUCCESS INFO --->

	<!-- BEGIN: PAGE TITLE -->
	<h1 class="page-title">{{Lang::get('admin/purchaseslist.transaction_history')}}</h1>
    <!-- END: PAGE TITLE -->

    {{ Form::open(array('id'=>'MemberSearchfrm', 'method'=>'get','class' => 'form-horizontal' )) }}
    	<div class="portlet box blue-madison">
            <!--- BEGIN: SEARCH TITLE --->
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-search"></i> {{Lang::get('admin/purchaseslist.search_transaction')}}
                </div>
                <div class="tools">
                    <a class="collapse" href="javascript:;"></a>
                </div>
            </div>
            <!--- END: SEARCH TITLE --->

            <div class="portlet-body form">
                <div class="form-body" id="search_holder">
                    <div id="selSrchBooking" class="row">
						<div class="col-md-6">
							<div class="form-group fn_clsPriceFields {{{ $errors->has('from_date') ? 'error' : '' }}}">
								{{ Form::label('from_date', trans("transaction.transaction_dates"), array('class' => 'col-md-4 control-label')) }}
								<div class="col-md-6">
									<div class="input-group date date-picker input-daterange" data-date-format="dd-mm-yyyy">
										{{ Form::text('from_date', Input::old('from_date', Input::get('from_date')), array('id'=>"from_date", 'class'=>'form-control', 'maxlength'=>'100')) }}
										<label for="date_added_to" class="input-group-addon">{{ trans('common.to') }}</label>
										{{ Form::text('to_date', Input::old('to_date', Input::get('to_date')), array('id'=>"to_date", 'class'=>'form-control', 'maxlength'=>'100')) }}
									</div>
									<label class="error">{{{ $errors->first('from_date') }}}</label>
								</div>
							</div>
							<div class="form-group">
								{{ Form::label('user_code', Lang::get('admin/salesReport.user_code'), array('class' => 'control-label col-md-4')) }}
								<div class="col-md-6">
									{{ Form::text('user_code', Input::get("user_code"), array('class' => 'form-control')) }}
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								{{ Form::label('transaction_id', Lang::get('transaction.paypal_transaction_id'), array('class' => 'control-label col-md-4')) }}
								<div class="col-md-6">
									{{ Form::text('transaction_id', Input::get("transaction_id"), array('class' => 'form-control')) }}
								</div>
							</div>
						</div>
                    </div>
                </div>
                <!-- BEGIN: SEARCH ACTIONS -->
                <div class="form-actions fluid">
                	<div class="col-md-offset-2 col-md-4">
                        <button type="submit" name="search_tax" value="search_tax" class="btn purple-plum">{{ Lang::get('common.search') }} <i class="fa fa-search bigger-110"></i></button>
                        <button type="reset" name="reset_search" value="reset_search" class="btn default" onclick="javascript:location.href='{{ URL::action('AdminTransactionsController@getIndex') }}'"><i class="fa fa-rotate-left bigger-110"></i> {{ Lang::get('common.reset') }}</button>
                    </div>
                </div>
                <!-- END: SEARCH ACTIONS -->
            </div>
         </div>
    {{ Form::close() }}


	<div class="portlet box blue-hoki">
        <!--- START: TABLE TITLE --->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-list"></i> {{ Lang::get('admin/purchaseslist.transactions_list') }}
            </div>
        </div>
        <!--- END: TABLE TITLE --->

        <div class="portlet-body">
           @if(count($transaction_details) <= 0)
           			<!-- BEGIN: ALERT BLOCK -->
					<div class="note note-info mar0">
					   {{ Lang::get('myPurchases.no_transaction') }}
					</div>
					<!-- END: ALERT BLOCK -->
				@else
					@if(count($transaction_details) > 0)
						<!-- BEGIN: TRANSACTION LIST -->
						<div class="table-responsive margin-bottom-30">
							<table class="table table-bordered table-hover">
								<thead>
									<tr>
										<th><div class="wid118">{{ Lang::get('myPurchases.date') }}</div></th>
										<th>{{ Lang::get('myPurchases.user') }}</th>
										<th class="col-md-2">{{ Lang::get('myPurchases.amount_credited') }}</th>
										<th class="col-md-2">{{ Lang::get('myPurchases.amount_debited') }}</th>
										<th>{{ Lang::get('myPurchases.transaction_notes') }}</th>
									</tr>
								</thead>

								<tbody>
									@if(count($transaction_details) > 0)
										@foreach($transaction_details as $transaction)
											<?php
												$curr_user_details = array();
												if(!isset($user_details[$transaction['user_id']]))
													$user_details[$transaction['user_id']] = CUtil::getUserDetails($transaction['user_id']);
												$curr_user_details = $user_details[$transaction['user_id']];

											?>
											<tr>
												<td>{{ CUtil::FMTDate($transaction->date_added, 'Y-m-d H:i:s', ''); }}</td>
												<td>
													@if(!empty($curr_user_details))
														<a target="_blank" href="{{ URL::to('admin/users/user-details').'/'.$transaction['user_id'] }}">{{$curr_user_details['display_name']}}</a>
														(<a class="text-muted" target="_blank" href="{{ URL::to('admin/users/user-details').'/'.$transaction['user_id'] }}">{{$curr_user_details['user_code']}}</a>)
													@else
													{{"-"}}
													@endif
												</td>
												<td>
													<?php
														if(count($transaction) > 0) {
															if($transaction['transaction_type'] == 'credit') {
																$lbl_class = "label-success";
																$transaction_type_credit = Lang::get('transaction.credit');
																?>
																<span class="label {{ $lbl_class }}">{{ $transaction_type_credit }}</span>
																&nbsp;{{ CUtil::convertAmountToCurrency($transaction->amount, Config::get('generalConfig.site_default_currency'), '', true) }}
																<?php
															}
															elseif($transaction['transaction_type'] == 'pending_credit') {
																$lbl_class = "label-warning";
																$transaction_type_pending_credit = Lang::get('transaction.pending_credit');
																?>
																<span class="label {{ $lbl_class }}">{{ $transaction_type_pending_credit }}</span>
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
																$transaction_type_debit = Lang::get('transaction.debit');
																?>
																<span class="label {{ $lbl_class }}">{{ $transaction_type_debit }}</span>
																&nbsp;{{ CUtil::convertAmountToCurrency($transaction->amount, Config::get('generalConfig.site_default_currency'), '', true) }}
																<?php
															}
															elseif($transaction['transaction_type'] == 'pending_debit') {
																$lbl_class = "label-primary";
																$transaction_type_pending_debit = Lang::get('transaction.pending_debit');
																?>
																<span class="label {{ $lbl_class }}">{{ $transaction_type_pending_debit }}</span>
																&nbsp;{{ CUtil::convertAmountToCurrency($transaction->amount, Config::get('generalConfig.site_default_currency'), '', true) }}
																<?php
															}
														}
													?>
												</td>
												<td>{{ $transaction_description[$transaction->id] }}</td>
											</tr>
										@endforeach
									@else
										<tr>
											<td colspan="5"><p class="alert alert-info">{{ Lang::get('myPurchases.no_result') }}</p></td>
										</tr>
									@endif
								</tbody>
							</table>
						</div>

						@if(count($transaction_details) > 0)
							<div class="text-right">
								{{ $transaction_details->appends(array('user_code' => Input::get('user_code'), 'to_date' => Input::get('to_date'), 'from_date' => Input::get('from_date'), 'transaction_id' => Input::get('transaction_id')))->links() }}
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
						<div class="note note-info">
						   {{ Lang::get('product.list_empty') }}
						</div>
					@endif
				@endif
            <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
    	</div>
    </div>

    {{ Form::open(array('id'=>'productsActionfrm', 'method'=>'post', 'url' => URL::action('AdminTaxationsController@postDeleteTaxations'))) }}
    {{ Form::hidden('taxation_id', '', array('id' => 'taxation_id')) }}
    {{ Form::hidden('tax_action', '', array('id' => 'tax_action')) }}
    {{ Form::close() }}

	<div id="dialog-tax-confirm" title="" style="display:none;">
	    <span class="ui-icon ui-icon-alert"></span>
		<span id="dialog-tax-confirm-content" class="show ml15"></span>
	</div>
@stop

@section('script_content')
	<script type="text/javascript">
		function doAction(taxation_id, selected_action)
		{
			if(selected_action == 'delete')
			{
				$('#dialog-tax-confirm-content').html('{{ trans('admin/taxation.confirm_delete') }}');
			}
			$("#dialog-tax-confirm").dialog({ title: '{{ trans('admin/taxation.taxtions_head') }}', modal: true,
				buttons: {
					"{{ trans('common.yes') }}": function() {
						$(this).dialog("close");
						$('#tax_action').val(selected_action);
						$('#taxation_id').val(taxation_id);
						document.getElementById("productsActionfrm").submit();
					}, "{{ trans('common.cancel') }}": function() { $(this).dialog("close"); }
				}
			});
			return false;
		}

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


		var common_ok_label = "{{ Lang::get('common.yes') }}" ;
		var common_no_label = "{{ Lang::get('common.cancel') }}" ;
		var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
		$(window).load(function(){
			  $(".fn_dialog_confirm").click(function(){
					var atag_href = $(this).attr("href");
					var action = $(this).attr("action");
					var cmsg = "";
					var txtDelete = action;

					var txtCancel = common_no_label;
					var buttonText = {};
					buttonText[txtDelete] = function(){
												Redirect2URL(atag_href);
												$( this ).dialog( "close" );
											};
					buttonText[txtCancel] = function(){
												$(this).dialog('close');
											};
					switch(action){
						case "Activate":
							cmsg = "Are you sure you want to activate this Member?";

							break;
						case "De-Activate":
							cmsg = "Are you sure you want to de-activate this Member?";
							break;
						case "Block":
							cmsg = "Are you sure you want to block this Member?";
							break;

						case "Un-Block":
							cmsg = "Are you sure you want to un-block this Member?";
							break;
					}
					$("#fn_dialog_confirm_msg").html(cmsg);
					$("#fn_dialog_confirm_msg").dialog({
						resizable: false,
						height:140,
						width: 320,
						modal: true,
						title: cfg_site_name,
						buttons:buttonText
					});
					return false;
				});
			});
	</script>
@stop