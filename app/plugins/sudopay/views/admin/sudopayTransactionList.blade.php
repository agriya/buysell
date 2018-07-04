@extends('admin')
@section('content')
	<!-- BEGIN: INCLUDE NOTIFICATIONS -->
    @include('notifications')
    <!-- END: INCLUDE NOTIFICATIONS -->

    <!--- BEGIN: INFO BLOCK --->
	@if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('error_message'); ?>
    @endif
    
    @if(Session::has('success_message') && Session::get('success_message') != '')
        <div class="note note-success">{{ Session::get('success_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif
    <!--- END: INFO BLOCK --->

	<!-- BEGIN: PAGE TITLE -->
	<h1 class="page-title">{{ trans('sudopay::sudopay.sudopay_transaction') }}</h1>
    <!-- END: PAGE TITLE -->

	<div class="portlet box blue-hoki">
        <!--- BEGIN: TABLE TITLE --->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-retweet sudo-icon"></i> {{ trans('sudopay::sudopay.sudopay_transaction_list') }}
            </div>
        </div>
        <!--- END: TABLE TITLE --->

        <div class="portlet-body">
            @if(count($display_transactions) <= 0 )
                <div class="note note-info mar0">
                	{{ trans('sudopay::sudopay.no_sudopay_transaction_list') }}
                </div>
            @else
                @if(count($display_transactions) > 0)
                    <div class="table-responsive margin-bottom-30">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>{{ trans('sudopay::sudopay.created') }}</th>
                                    <th>{{ trans('sudopay::sudopay.class') }}</th>
                                    <th>{{ trans('sudopay::sudopay.payment_id') }}</th>
                                    <th>{{ trans('sudopay::sudopay.amount') }}</th>
                                    <th>{{ trans('sudopay::sudopay.sudopay_pay_key') }}</th>
                                    <th>{{ trans('sudopay::sudopay.merchant_id') }}</th>
                                    <th>{{ trans('sudopay::sudopay.gateway_name') }}</th>
                                    <th>{{ trans('sudopay::sudopay.status') }}</th>
                                    <th>{{ trans('sudopay::sudopay.payment_type') }}</th>
                                    <th>{{ trans('sudopay::sudopay.buyer_email') }}</th>
                                    <th>{{ trans('sudopay::sudopay.buyer_address') }}</th>
                                </tr>
                            </thead>
                        
                            <tbody>
                                @if(count($display_transactions) > 0)
                                    @foreach($display_transactions as $display_trans)
                                    <tr>
                                        <td class="text-muted">{{ $display_trans->created }}</td>
                                        <td>{{ "class" }}</td>
                                        <td>{{ $display_trans->payment_id }}</td>
                                        <td>{{ $display_trans->amount }}</td>
                                        <td>{{ $display_trans->sudopay_pay_key }}</td>
                                        <td>{{ $display_trans->merchant_id }}</td>
                                        <td>{{ $display_trans->gateway_name }}</td>
                                        <td>{{ $display_trans->status }}</td>
                                        <td>{{ $display_trans->payment_type }}</td>
                                        <td>{{ $display_trans->buyer_email }}</td>
                                        <td>{{ $display_trans->buyer_address }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5"><p class="alert alert-info">{{ trans('sudopay::sudopay.no_sudopay_transaction_list') }}</p></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                
                    @if(count($display_transactions) > 0)
                        <div class="text-right">
                            {{ $display_transactions->links() }}
                        </div>
                    @endif
                    
                    {{ Form::open(array('id'=>'productsActionfrm', 'method'=>'post', 'url' => URL::to('myproducts/deleteproduct'))) }}
                    {{ Form::hidden('p_id', '', array('id' => 'p_id')) }}
                    {{ Form::hidden('product_action', '', array('id' => 'product_action')) }}
                    {{ Form::close() }}
                    
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