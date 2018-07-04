@extends('adminPopup')
@section('content')
    <h1>Shipping Details</h1>
	<div id="error_msg_div"></div>
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
    
    <div class="pop-content view-shippingdet">
        {{ Form::open(array('url' => URL::action('AdminPurchasesController@postSetAsShippingPopup'), 'class' => 'form-horizontal',  'id' => 'set_as_shipping_frm', 'name' => 'form_checkout')) }}
        {{ Form::hidden('order_id', $order_id, array('id' => 'order_id')) }}
        	<div class="form-group">
                {{ Form::label('invoice_id', Lang::get('myPurchases.invoice_id'), array('class' => 'col-sm-4 control-label')) }}
                <div class="col-sm-7">
                    {{ Form::label('invoice_id', $invoice_details->common_invoice_id, array('class' => 'control-label text-bold')) }}
                </div>
	        </div>
            
	        <div class="form-group">
                {{ Form::label('order_code', Lang::get('myPurchases.order_code'), array('class' => 'col-sm-4 control-label')) }}
                <div class="col-sm-7">
                    {{ Form::label('order_code', $order_code, array('class' => 'control-label text-bold')) }}
                </div>
	        </div>
            
	        <?php
                $is_all_not_shipped = $is_all_delivered = true;
                foreach($shop_order as $key => $s_order){
					if($s_order->shipping_status != 'delivered')
						$is_all_delivered = false;
					if($s_order->shipping_status != 'not_shipping')
						$is_all_not_shipped = false;
				}
				$status = Lang::get('myPurchases.partial');
				if($is_all_delivered){
					$status = Lang::get('myPurchases.shipped');
				}
				elseif($is_all_not_shipped){
					$status = Lang::get('myPurchases.not_shipped');
				}
			?>
            
			<div class="form-group">
                {{ Form::label('order_shipping_status', Lang::get('myPurchases.shipping_status'), array('class' => 'col-sm-4 control-label')) }}
                <div class="col-sm-7">
                    {{ Form::label('order_shipping_status', $status, array('class' => 'control-label text-bold')) }}
                </div>
	        </div>
            
	        <?php $count = 1; ?>
            <div class="mt30">
                @foreach($shop_order as $key => $s_order)
                    <?php
                        $product_details = DB::table('product')->where('id',$s_order->item_id)->first();
                        $from_country_id = isset($product_details->shipping_from_country)?$product_details->shipping_from_country:0;
                        $from_country_det = Products::getCountryDetailsByCountryId($from_country_id);
                        //$serial_number_details = DB::table('product_stocks')->where('product_id', $s_order->item_id)->select('serial_numbers','quantity')->first();
                        /*if($s_order->shipping_status == 'delivered')
                            $class_arr = array('class' => 'control-label text-bold js_product_name', 'id' => $s_order->item_id);
                        else
                            $class_arr = array('class' => 'control-label text-bold', 'id' => $s_order->item_id);*/
                    ?>
                	
                	@if($product_details->is_downloadable_product ==  'No')
                        <div class="well">
                            <h3>
                                <strong>{{ $key+1 }}. {{ Form::label('product_name', Lang::get('myPurchases.product_name').':', array('class' => 'control-label')) }}</strong>
                                <a href="javascript:;">{{ Form::label('product_name', $product_details->product_name.'('.$product_details->product_code.')', $class_arr = array('class' => 'control-label margin-left-10 text-bold js_product_name text-primary', 'id' => $s_order->item_id)) }}</a>
                            </h3>
                            
                            <div id="js_hide_values_{{ $s_order->item_id }}" class="mt10" style="@if($count == "1") display: block; @else display: none; @endif" >
                                <div class="form-group">
                                    {{ Form::label('shipping_company', Lang::get('myPurchases.shipping_company'), array('class' => 'col-sm-4 control-label')) }}
                                    <div class="col-sm-7">
                                        <?php
                                            $shipping_company_name = DB::table('shipping_companies')->where('id',$s_order->shipping_company_name)->first();
                                        ?>
                                        @if($shipping_company_name != '')
                                            {{ Form::label('shipping_company', $shipping_company_name->company_name, array('class' => 'control-label text-bold')) }}
                                        @else
                                            {{ Form::label('shipping_company', "-", array('class' => 'control-label text-bold')) }}
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    {{ Form::label('tracking_id', Lang::get('myPurchases.tracking_id'), array('class' => 'col-sm-4 control-label')) }}
                                    <div class="col-sm-7">
                                        @if($s_order->shipping_tracking_id != '')
                                            {{ Form::label('tracking_id', $s_order->shipping_tracking_id, array('class' => 'control-label text-bold')) }}
                                        @else
                                            {{ Form::label('tracking_id', "-", array('class' => 'control-label text-bold')) }}
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    {{ Form::label('select_country', Lang::get('myPurchases.stock_from_country'), array('class' => 'col-sm-4 control-label')) }}
                                    <div class="col-sm-7">
                                        <?php
                                            $shipping_country = '';
                                         if($s_order->shipping_stock_country == '38')
                                            $shipping_country = "China";
                                        elseif($s_order->shipping_stock_country == '153')
                                            $shipping_country = "Pakistan";
                                        ?>
                                   	    @if(isset($from_country_det['country']) && $from_country_det['country'] != '')
                                        	{{ Form::label('select_country', $from_country_det['country'], array('class' => 'control-label text-bold')) }}
                                        @else
                                            {{ Form::label('select_country', "-", array('class' => 'control-label text-bold')) }}
                                        @endif
                                    </div>
                                </div>
                                
                                <?php
                                    $serial_number = explode("\r\n", $s_order->shipping_serial_number);
                                    $shipping_serial_number = implode("\n", $serial_number);
                                    $s_serial_number = nl2br($shipping_serial_number);
                                ?>
                                
                                <div class="form-group">
                                    {{ Form::label('serial_number', Lang::get('myPurchases.serial_numbers'), array('class' => 'col-sm-4 control-label')) }}
                                    <div class="col-sm-7 form-control-static">
                                        @if($s_order->shipping_serial_number != '')
                                            <strong>{{ $s_serial_number }}</strong>
                                        @else
                                           <strong> - </strong>
                                        @endif
                                    </div>
                                </div>
                                
                                <?php
                                    $shipping_status = '';
                                    if($s_order->shipping_status == 'delivered')
                                        $shipping_status = 'Shipped';
                                    else
                                        $shipping_status = 'Not Shipped';
                                ?>
                                
                                <div class="form-group">
                                    {{ Form::label('shipping_status', Lang::get('myPurchases.shipping_status'), array('class' => 'col-sm-4 control-label')) }}
                                    <div class="col-sm-7">
                                        {{ Form::label('shipping_status', $shipping_status, array('class' => 'control-label text-bold')) }}
                                    </div>
                                </div>
                            </div>
                            
    	                    <?php
    	                        $count++;
    	                    ?>
                        </div>
                    @endif
                @endforeach
            </div>
            
            <div class="form-group">
				<div class="col-sm-7">
		            <a href="javascript:;" itemprop="url" onclick="closeFancyBox()">
		                <button type="reset" class="btn btn-danger"><i class="fa fa-times"></i> {{Lang::get('common.close')}}</button>
		            </a>
            	</div>
            </div>
	    {{ Form::close() }}
	</div>

	<script language="javascript" type="text/javascript">
		function closeFancyBox() {
			parent.$.fancybox.close();
		}

		$(".js_product_name").click(function()
		{
			var value = $(this).attr('id');
			var div_id = 'js_hide_values_'+value;
			$("#"+div_id).toggle();
		});
	</script>
@stop