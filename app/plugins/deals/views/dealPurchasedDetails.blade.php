@extends('popup')
@section('content')
	<h1>{{ Lang::get('deals::deals.deal_purchased_stats_head') }}</h1>
    <div class="pop-content">
    	@if(isset($error_message) && $error_message != "")
        	<div class="note note-danger">{{ $error_message }}</div>
            <a href="javascript://" itemprop="url" onclick="javascript:parent.$.fancybox.close();">
                <button type="reset" class="btn red margin-top-10"><i class="fa fa-times-circle"></i> {{ Lang::get('deals::deals.close_lbl') }}</button>
            </a>
        @else
    	
            @if(count($purchase_details) > 0)
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th>{{ Lang::get('deals::deals.order_id_head_lbl') }}</th>
                            <th>{{ Lang::get('deals::deals.invoice_status_head_lbl') }}</th>
                            <th>{{ Lang::get('deals::deals.purchased_by_head_lbl') }}</th>
                            <th>{{ Lang::get('deals::deals.purchased_qty_head_lbl') }}</th>
                            <th>{{ Lang::get('deals::deals.purchased_date_head_lbl') }}</th>
                            <th class="co-md-2">{{ Lang::get('deals::deals.actions') }}</th>
                        </tr>
                    </thead>
					
                    <tbody>
                     @foreach($purchase_details as $det)
                     	<?php
							$buyer_details = CUtil::getUserDetails($det->buyer_id);	
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
                            	<a href="{{ $buyer_details['profile_url'] }}" title="{{ $buyer_details['display_name'] }}" target="_blank">{{ $buyer_details['display_name'] }}</a>
                            </td>
                            <td>{{ $det->item_qty }}</td>
                            <td>{{ CUtil::FMTDate($det->date_created, 'Y-m-d H:i:s', ''); }}</td>
                            <td>                                
                                <a href="{{ URL::action('PurchasesController@getSalesOrderDetails', $det->id) }}" title="{{ Lang::get('myPurchases.view') }}" class="btn btn-xs btn-info" target="_blank"><i class="fa fa-eye"></i>  {{ Lang::get('deals::deals.view_invoice_link_lbl') }}</a>
                                
                            </td>
                        </tr>
                     @endforeach
                     </tbody>
                </table>  
                @if(count($purchase_details) > 0)
                    <div class="text-right">
                        {{ $purchase_details->links() }}
                    </div>
                @endif               
            @else
                <p class="note note-info">{{ Lang::get('deals::deals.purchased_none_err_msg') }}</p>
            @endif
		@endif            
    </div>
	
@stop    