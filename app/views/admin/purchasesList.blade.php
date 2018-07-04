@extends('admin')
@section('content')
    <!--- BEGIN: INFO BLOCK -->
    @if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('error_message'); ?>
    @endif

    @if(Session::has('success_message') && Session::get('success_message') != '')
        <div class="note note-success">{{ Session::get('success_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif
    <!--- END: INFO BLOCK -->

    <!-- BEGIN: PAGE TITLE -->
    <h1 class="page-title">{{Lang::get('admin/purchaseslist.my_purchases')}}</h1>
    <!-- END: PAGE TITLE -->

    {{ Form::open(array('id'=>'MemberSearchfrm', 'method'=>'get','class' => 'form-horizontal' )) }}
        <div class="portlet box blue-madison">
            <!--- BEGIN: SEARCH TITLE -->
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-search"></i> {{Lang::get('admin/purchaseslist.search_sales')}}
                </div>
                <div class="tools">
                    <a class="collapse" href="javascript:;"></a>
                </div>
            </div>
            <!--- END: SEARCH TITLE -->

            <div class="portlet-body form">
                <div class="form-body" id="search_holder">
                    <div id="selSrchBooking" class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('order_id', Lang::get('admin/purchaseslist.order_id'), array('class' => 'control-label col-md-4')) }}
                                <div class="col-md-6">
                                    {{ Form::text('order_id', Input::get("order_id"), array('class' => 'form-control')) }}
                                </div>
                            </div>

                            <div class="form-group fn_clsPriceFields {{{ $errors->has('from_date') ? 'error' : '' }}}">
                                {{ Form::label('from_date', trans("admin/purchaseslist.order_date"), array('class' => 'col-md-4 control-label')) }}
                                <div class="col-md-6">
                                    <div class="input-group date date-picker input-daterange" data-date-format="dd-mm-yyyy">
                                        {{ Form::text('from_date', Input::old('from_date', Input::get('from_date')), array('id'=>"from_date", 'class'=>'form-control', 'maxlength'=>'100')) }}
                                        <label for="date_added_to" class="input-group-addon">{{ trans('admin/productList.product_to') }}</label>
                                        {{ Form::text('to_date', Input::old('to_date', Input::get('to_date')), array('id'=>"to_date", 'class'=>'form-control', 'maxlength'=>'100')) }}
                                    </div>
                                    <label class="error">{{{ $errors->first('from_date') }}}</label>
                                </div>
                            </div>

                            <div class="form-group">
                                {{ Form::label('order_status', Lang::get('admin/purchaseslist.order_status'), array('class' => 'control-label col-md-4')) }}
                                <div class="col-md-6">
                                    {{ Form::select('order_status', $search_order_statuses,  Input::get("order_status"), array('class' => 'form-control bs-select')) }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('buyer_user_code', Lang::get('admin/purchaseslist.user_code_of_buyer'), array('class' => 'control-label col-md-4')) }}
                                <div class="col-md-6">
                                    {{ Form::text('buyer_user_code', Input::old('buyer_user_code', Input::get('buyer_user_code')), array('class' => 'form-control')) }}
                                </div>
                            </div>

                            <div class="form-group">
                                {{ Form::label('seller_user_code', Lang::get('admin/purchaseslist.user_code_of_seller'), array('class' => 'control-label col-md-4')) }}
                                <div class="col-md-6">
                                    {{ Form::text('seller_user_code', Input::old('seller_user_code', Input::get('seller_user_code')), array('class' => 'form-control')) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BEGIN: SEARCH ACTION -->
                <div class="form-actions fluid">
                    <div class="col-md-offset-2 col-md-4">
                        <button type="submit" name="search_tax" value="search_tax" class="btn purple-plum">{{ Lang::get('common.search') }} <i class="fa fa-search bigger-110"></i></button>
                        <button type="reset" name="reset_search" value="reset_search" class="btn default" onclick="javascript:location.href='{{ URL::action('AdminPurchasesController@getIndex') }}'"><i class="fa fa-rotate-left bigger-110"></i> {{ Lang::get('common.reset') }}</button>
                    </div>
                </div>
                <!-- END: SEARCH ACTION -->
            </div>
         </div>
    {{ Form::close() }}

    <div class="portlet box blue-hoki">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-list"></i> {{ Lang::get('admin/purchaseslist.my_purchases') }} List
            </div>
        </div>

        <div class="portlet-body">
            @if(count($order_details) <= 0)
                <!--- BEGIN: PURCHASE INFO -->
                <div class="note note-info mar0">
                   {{ Lang::get('admin/purchaseslist.no_result') }}
                </div>
                <!--- END: PURCHASE INFO -->
            @else
                @if(count($order_details) > 0)
                    {{ Form::open(array('action' => array('PurchasesController@getIndex'), 'id'=>'purchaseFrm', 'method'=>'get','class' => 'form-horizontal' )) }}
                    {{ Form::hidden('page',Input::get('page'),array('id' => 'page')) }}
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ Lang::get('admin/purchaseslist.order') }}</th>
                                        <th>{{ Lang::get('admin/purchaseslist.buyer') }}</th>
                                        <th>{{ Lang::get('admin/purchaseslist.seller') }}</th>
                                        <th>{{ Lang::get('admin/purchaseslist.total_amount') }}</th>
                                        <th>{{ Lang::get('admin/purchaseslist.status') }}</th>
                                        <th><div class="wid50">{{ Lang::get('common.action') }}</div></th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @if(count($order_details) > 0)
                                        @foreach($order_details as $order)
                                            <tr>
                                                <td>
                                                    <div class="dl-horizontal dl-horizontal-new sale-dl">
                                                        <!--- this block span gave for colon -->
                                                        <dl>
                                                            <dt>{{ Lang::get('admin/purchaseslist.order_id') }}</dt>
                                                            <dd><span><strong>{{ CUtil::setOrderCode($order->id); }}</strong></span></dd>
                                                        </dl>
                                                        <dl>
                                                            <dt><span title="{{ Lang::get('admin/purchaseslist.date_ordered') }}">{{ Lang::get('admin/purchaseslist.date_ordered') }}</span></dt>
                                                            <dd><span>{{ CUtil::FMTDate($order->date_created, 'Y-m-d H:i:s', ''); }}</span></dd>
                                                        </dl>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if(isset($order->buyer_id) && $order->buyer_id >0)
														<?php $user_det = CUtil::getUserDetails($order->buyer_id);?>
                                                        <p>
                                                            <strong><a href="{{ URL::to('admin/users/user-details').'/'.$order->buyer_id }}">{{$user_det['display_name']}}</a></strong>
                                                            <span>(<a class="text-muted" href="{{ URL::to('admin/users/user-details').'/'.$order->buyer_id }}">{{ $user_det['user_code'] }}</a>)</span>
                                                        </p>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(isset($order->seller_id) && $order->seller_id >0)
														<?php $user_det = CUtil::getUserDetails($order->seller_id);?>
                                                        <p>
                                                            <strong><a href="{{ URL::to('admin/users/user-details').'/'.$order->seller_id }}">{{$user_det['display_name']}}</a></strong>
                                                            <span>(<a class="text-muted" href="{{ URL::to('admin/users/user-details').'/'.$order->seller_id }}">{{ $user_det['user_code'] }}</a>)</span>
                                                        </p>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <p>
                                                        <span class="text-muted">{{ Lang::get('admin/purchaseslist.amount') }}:</span>
                                                        {{ CUtil::convertAmountToCurrency($order->total_amount, Config::get('generalConfig.site_default_currency'), '', true) }}
                                                    </p>
                                                </td>
                                                <td>
                                                    <?php
                                                        $lbl_class = "";
                                                        if(strtolower ($order['order_status']) == "draft")
                                                            $lbl_class = "label-default";
                                                        elseif(strtolower ($order['order_status']) == "pending_payment")
                                                            $lbl_class = "label-warning";
                                                        elseif(strtolower ($order['order_status']) == "payment_completed")
                                                            $lbl_class = "label-success";
                                                        elseif(strtolower ($order['order_status']) == "refund_requested")
                                                            $lbl_class = "label-warning";
                                                        elseif(strtolower ($order['order_status']) == "refund_completed")
                                                            $lbl_class = "label-info";
                                                        elseif(strtolower ($order['order_status']) == "refund_rejected")
                                                            $lbl_class = "label-danger";
                                                        elseif(strtolower ($order['order_status']) == "not_paid")
                                                            $lbl_class = "label-warning";
                                                        elseif(strtolower ($order['order_status']) == "payment_cancelled")
                                                            $lbl_class = "label-danger";
                                                    ?>
                                                    <div id="container_{{ $order->id }}">
                                                        @if(strtolower ($order['order_status']) == "not_paid")
                                                            <p class="label {{ $lbl_class }}">{{ Lang::get('myPurchases.status_unpaid') }}</p>
                                                        @else
                                                            <p class="label {{ $lbl_class }}">{{ Lang::get('myPurchases.status_txt_'.$order->order_status) }}</p>
                                                        @endif
                                                    </div>
                                                </td>

                                                <td class="status-btn">
                                                    <a href="{{ URL::action('AdminPurchasesController@getOrderDetails', $order->id) }}" class="btn btn-info btn-xs" title="{{ Lang::get('admin/purchaseslist.view') }}"><i class="fa fa-eye"></i></a>
                                                    @if(($order['order_status']) == "pending_payment" || ($order['order_status']) == "not_paid")
                                                        <p id="set_as_paid_info_{{ $order->id }}" class="btn btn-xs green pull-left">
                                                            <a onclick="openSetAsPaidPopup('{{ $order->id }}', '{{ $order->buyer_id }}');" title="{{ Lang::get('admin/purchaseslist.set_as_paid') }}" class=""><i class="fa fa-check-circle text-white"></i></a>
                                                        </p>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5"><p class="alert alert-info">{{ Lang::get('admin/purchaseslist.no_result') }}</p></td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        @if(count($order_details) > 0)
                            <div class="text-right">{{ $order_details->appends(array('order_id' => Input::get('order_id'), 'from_date' => Input::get('from_date') , 'to_date' => Input::get('to_date'), 'order_status' => Input::get('order_status'), 'buyer_user_code' => Input::get('buyer_user_code'), 'seller_user_code' => Input::get('seller_user_code')))->links() }}</div>
                        @endif
                    {{ Form::close() }}

                    {{ Form::open(array('id'=>'productsActionfrm', 'method'=>'post', 'url' => URL::to('myproducts/deleteproduct'))) }}
                        {{ Form::hidden('p_id', '', array('id' => 'p_id')) }}
                        {{ Form::hidden('product_action', '', array('id' => 'product_action')) }}
                    {{ Form::close() }}

                    <div id="dialog-product-confirm" title="" style="display:none;">
                        <span class="ui-icon ui-icon-alert"></span>
                        <span id="dialog-product-confirm-content" class="show ml15"></span>
                    </div>
                @else
                    <div class="note note-info mar0">
                       {{ Lang::get('product.list_empty') }}
                    </div>
                @endif
            @endif
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

	    function openSetAsPaidPopup(order_id, buyer_id) {
                var actions_url = '{{ URL::action('AdminPurchasesController@getSetAsPaidPopup') }}';
                var postData = 'buyer_id='+buyer_id+'&order_id='+order_id;
                fancybox_url = actions_url + '?' + postData;
                $.fancybox({
                    maxWidth    : 800,
                    maxHeight   : 432,
                    fitToView   : false,
                    width       : '70%',
                    height      : '432',
                    autoSize    : false,
                    closeClick  : false,
                    type        : 'iframe',
                    href        : fancybox_url,
                    openEffect  : 'none',
                    closeEffect : 'none',
                    /*afterClose  : function() {
                         window.location.reload();
                    }*/
                });
            };

            function openSetAsShippingPopup(order_id) {
                var actions_url = '{{ URL::action('AdminPurchasesController@getSetAsShippingPopup') }}';
                var postData = 'order_id='+order_id;
                //alert(postData);
                fancybox_url = actions_url + '?' + postData;
                $.fancybox({
                    maxWidth    : 800,
                    maxHeight   : 432,
                    fitToView   : false,
                    autoSize    : false,
                    closeClick  : false,
                    type        : 'iframe',
                    href        : fancybox_url,
                    openEffect  : 'none',
                    closeEffect : 'none',
                    /*afterClose  : function() {
                        window.location.reload();
                    }*/
                });
            };
		/**
		 *
		 * @access public
		 * @return void
		 **/
		/*function updateSetAsPaidValues(data)(){
			$('#set_as_paid_info').html(data);

		}*/
		function openViewShippingPopup(order_id) {
            var actions_url = '{{ URL::action('AdminPurchasesController@getViewShippingPopup') }}';
            var postData = 'order_id='+order_id;
            fancybox_url = actions_url + '?' + postData;
            $.fancybox({
                maxWidth    : 800,
                maxHeight   : 432,
                fitToView   : false,
                width       : '70%',
                height      : '432',
                autoSize    : false,
                closeClick  : false,
                type        : 'iframe',
                href        : fancybox_url,
                openEffect  : 'none',
                closeEffect : 'none',
                /*afterClose  : function() {
                     window.location.reload();
                }*/
            });
        };
		var cfg_site_name = '{{ Config::get('generalConfig.site_name') }}' ;
		$(window).load(function(){
			  $(".fn_dialog_confirm").click(function(){
					var atag_href = $(this).attr("href");
					var action = $(this).attr("action");
					var cmsg = "";
					//alert(action); return false;
					switch(action){
						case "Delivered":
							cmsg = '{{ Lang::get('myProducts.product_confirm_delivered') }}';
							break;
					}
					bootbox.dialog({
						message: cmsg,
					  	title: cfg_site_name,
					  	buttons: {
							danger: {
					      		label: "Ok",
					      		className: "btn-danger",
					      		callback: function() {
					      			Redirect2URL(atag_href);
					      			bootbox.hideAll();
					      		}
					    	},
					    	success: {
					      		label: "Cancel",
					      		className: "btn-default",
					    	}
					  	}
					});
					return false;
				});
			});
	</script>
@stop