@extends('popup')
@section('content')
	<!-- BEGIN: PAGE TITLE -->
	<h1>{{ Lang::get('deals::deals.deal_purchased_stats_head') }}</h1>
    <!-- END: PAGE TITLE -->
	
	<div class="pop-content">
    	
        <!--- BEGIN: SUCCESS INFO --->
        @if(Session::has('success_message') && Session::get('success_message') != '')
            <div class="note note-success">{{ Session::get('success_message') }}</div>
            <?php Session::forget('success_message'); ?>
            <a href="javascript://" itemprop="url" onclick="javascript:parent.$.fancybox.close();">
                <button type="reset" class="btn red margin-top-10"><i class="fa fa-times-circle"></i> {{ Lang::get('deals::deals.close_lbl') }}</button>
            </a>
        @endif
        <!--- END: SUCCESS INFO --->
    
    	@if(isset($error_message) && $error_message != "")
        	<div class="note note-danger">{{ $error_message }}</div>
            <a href="javascript://" itemprop="url" onclick="javascript:parent.$.fancybox.close();">
                <button type="reset" class="btn red margin-top-10"><i class="fa fa-times-circle"></i> {{ Lang::get('deals::deals.close_lbl') }}</button>
            </a>
        @else
            <!-- BEGIN: DEAL PURCHASES LIST -->
			@if(count($purchase_details) > 0)
            	{{ Form::open(array('id'=>'listFrm', 'method'=>'post','class' => 'form-horizontal' )) }}
                	{{ Form::hidden('list_action', '', array('id' => 'list_action')) }}
                    {{ Form::hidden('order_id', '', array('id' => 'order_id')) }}
                    {{ Form::hidden('item_id', '', array('id' => 'item_id')) }}
                    {{ Form::hidden('deal_id', $d_arr['deal_id'], array('id' => 'deal_id')) }}
                    
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>{{ Lang::get('deals::deals.order_id_head_lbl') }}</th>
                                <th>{{ Lang::get('deals::deals.invoice_status_head_lbl') }}</th>
                                <th>{{ Lang::get('deals::deals.purchased_by_head_lbl') }}</th>
                                <th>{{ Lang::get('deals::deals.purchased_qty_head_lbl') }}</th>
                                <th>{{ Lang::get('deals::deals.purchased_date_head_lbl') }}</th>
                                <th class="co-sm-2">{{ Lang::get('deals::deals.actions') }}</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            @foreach($purchase_details as $det)
                                <?php 
                                    $buyer_details = CUtil::getUserDetails($det->buyer_id); 
                                    $refund_flag = $deal_serviceobj->getDealItemPurchasedRefundStatus($det->order_id, $det->item_id);
                                ?>
                                <tr>
                                    <td>{{ CUtil::setOrderCode($det->id) }}</td>
                                    <td>
                                        <?php
                                            $lbl_class = "";
                                            switch(strtolower ($det->order_status))
                                            {
                                                case "draft": 
                                                    $lbl_class = "label-default";
                                                    break;
                                                    
                                                case "pending_payment": 
                                                case "not_paid": 
                                                    $lbl_class = "label-warning";
                                                    break;	
                                                
                                                case "payment_completed": 
                                                case "refund_completed": 
                                                    $lbl_class = "label-success";
                                                    break;
                                                    
                                                case "refund_requested": 
                                                    $lbl_class = "label-primary";
                                                    break;	
                                                
                                                case "refund_rejected": 
                                                case "payment_cancelled": 
                                                    $lbl_class = "label-danger";
                                                    break;																						
                                            }									
                                        ?>
                                        @if(strtolower ($det->order_status) == "not_paid")
                                            <p class="label {{ $lbl_class }}">{{ Lang::get('myPurchases.status_unpaid') }}</p>
                                        @else
                                            <p class="label {{ $lbl_class }}">{{ Lang::get('myPurchases.status_txt_'.$det->order_status) }}</p>
                                        @endif                            
                                    </td>
                                    <td>
                                        <a href="{{ URL::to('admin/users/user-details').'/'.$det->buyer_id }}" title="{{ $buyer_details['display_name'] }}" target="_blank">
                                            {{ $buyer_details['display_name'] }}
                                        </a>
                                    </td>
                                    <td>{{ $det->item_qty }}</td>
                                    <td>{{ CUtil::FMTDate($det->date_created, 'Y-m-d H:i:s', ''); }}</td>
                                    <td>                                
                                        <a href="{{ URL::action('AdminPurchasesController@getOrderDetails', $det->id) }}" title="{{ Lang::get('myPurchases.view') }}" class="btn btn-xs btn-info" target="_blank"><i class="fa fa-eye"></i>  {{ Lang::get('deals::deals.view_invoice_link_lbl') }}</a>
                                        
                                        @if(isset($refund_flag) && $refund_flag > 0)
                                            @if($refund_flag == 1)
                                                <a href="javascript:void(0)" onclick="setAsRefund('{{$det->order_id}}', '{{$det->item_id}}')">{{ Lang::get('deals::deals.set_as_refunded_lbl') }}</a>
                                            @elseif($refund_flag == 2)
                                                {{ Lang::get('deals::deals.refunded_lbl') }}
                                            @endif
                                        @endif
                                        
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>  
                    @if(count($purchase_details) > 0)
                        <div class="text-right">{{ $purchase_details->links() }}</div>
                    @endif               
                {{Form::close()}}
            @else
                <p class="note note-info">{{ Lang::get('deals::deals.purchased_none_err_msg') }}</p>
            @endif
			<!-- END: DEAL PURCHASES LIST -->
            <div id="dialog-product-confirm" title="" style="display:none;">
                <span class="ui-icon ui-icon-alert"></span>
                <span id="dialog-product-confirm-content" class="show ml15"></span>
            </div>
            
		@endif            
    </div>
    <script type="text/javascript">
		function setAsRefund(order_id, item_id)
		{
			alert_msg = "{{ Lang::get('deals::deals.confirm_set_refund_msg') }}";
			$('#dialog-product-confirm-content').html(alert_msg);
				$("#dialog-product-confirm").dialog({ title: '{{ trans('featuredproducts::featuredproducts.product_featured_head') }}', modal: true,
					buttons: {
						"{{ trans('common.yes') }}": function() {
							$('#order_id').val(order_id);
							$('#item_id').val(item_id);
							$('#list_action').val('refund');
							document.getElementById("listFrm").submit();							
						}, "{{ trans('common.cancel') }}": function() { $(this).dialog("close"); }
					}
				});
				return false;
		}		
	</script>
@stop    