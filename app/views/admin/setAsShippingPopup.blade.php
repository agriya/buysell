@extends('adminPopup')
@section('includescripts')
	<script type="text/javascript">
		@if (isset($is_redirect) && $is_redirect == 1)
			parent.location.reload();
		@endif
	</script>
@stop

@section('content')
    <h1>{{trans('myPurchases.set_as_shipping')}}</h1>
	<div id="error_msg_div"></div>
    <div class="pop-content view-shippingdet">
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

        {{ Form::open(array('url' => URL::action('AdminPurchasesController@postSetAsShippingPopup'), 'class' => 'form-horizontal setas-shipping',  'id' => 'set_as_shipping_frm', 'name' => 'form_checkout')) }}
        {{ Form::hidden('order_id', $order_id, array('id' => 'order_id')) }}
        {{ Form::hidden('item_id', $item_id, array('id' => 'item_id')) }}
        	<div class="form-group">
                {{ Form::label('invoice_id', trans('myPurchases.invoice_id').':', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">
                    {{ Form::label('invoice_id', $invoice_details->common_invoice_id, array('class' => 'control-label text-bold')) }}
                </div>
	        </div>
            
	        <div class="form-group">
                {{ Form::label('order_code', trans('myPurchases.order_code').':', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">
                    {{ Form::label('order_code', $order_code, array('class' => 'control-label text-bold')) }}
                </div>
	        </div>
            
	        <?php $count=1; ?>
            <div class="mt30">
                <?php
                    $allow_deal_block = 0;
                    if(CUtil::chkIsAllowedModule('deals'))
                    {
                        $deal_service = new DealsService();
                        $allow_deal_block = 1;
                    }
                ?>
                @foreach($shop_order as $key => $s_order)
                    {{ Form::hidden('order_item_qty', $s_order->item_qty, array('id' => 'order_item_qty_'.$s_order->item_id)) }}
                    <?php
                        $product_details = DB::table('product')->where('id',$s_order->item_id)->first();
                        $from_country_id = isset($product_details->shipping_from_country)?$product_details->shipping_from_country:0;
                        if(count($product_details) > 0)
                            $view_url = Products::getProductViewURL($product_details->id, (array)$product_details);
                        else
                            $view_url = '#';

                        $from_country_det = Products::getCountryDetailsByCountryId($from_country_id);
                        
                        $allow_shipping = true;
                        if($allow_deal_block && isset($s_order->deal_id) && $s_order->deal_id > 0 && !$deal_service->allowToDownloadDealItem($s_order->deal_id))
                            $allow_shipping = false;
                    ?>
                    
                    @if($product_details->is_downloadable_product ==  'No' && $allow_shipping)
                        <div class="well">
                            <h3 id = "{{ $s_order->item_id }}">
                                <strong>{{ Form::label('product_name', trans('myPurchases.product_name').':', array('class' => 'control-label')) }}</strong>
                                <a href="{{$view_url}}" target="_blank" class="text-primary ml10">{{ Form::label('product_name', $product_details->product_name.'('.$product_details->product_code.')') }}</a>
                                @if($s_order->shipping_status == 'shipped')  <strong>Shipped</strong> @endif
                            </h3>
                            
                            <?php
                                $shipping_template_service = new ShippingTemplateService();
                                $company_list = $shipping_template_service->getCompanyList($product_details->shipping_template);
                                $company_id = $shipping_template_service->getCompanyId($product_details->shipping_template);
                                //print_r($company_id);
                            ?>
                            
                            <div id="js_hide_values_{{ $s_order->item_id }}"  class="mt10" @if($s_order->shipping_status != 'delivered') @else style="display: none;" @endif>
                                <div class="form-group">
                                    {{ Form::label('shipping_quantity', trans('myPurchases.shipping_quantity').':', array('class' => 'col-sm-3 control-label')) }}
                                    <div class="col-sm-6">
                                        {{ Form::label('shipping_quantity', $s_order->item_qty, array('class' => 'control-label text-bold')) }}
                                    </div>
                                </div>
                                
                                @if($product_details->is_downloadable_product == 'No')
                                    <div class="form-group">
                                        {{ Form::label('shipping_company', trans('myPurchases.shipping_company').':', array('class' => 'col-sm-3 control-label')) }}
                                        <div class="col-sm-6">
                                            {{ Form::select('shipping_company_'.$s_order->item_id, $company_list,Input::old('shipping_company', $s_order->shipping_company), array('class' => 'form-control select2me', 'id' => 'shipping_company_value_'.$s_order->item_id)) }}
                                            <label class="error">{{{ $errors->first('shipping_company') }}}</label>
                                        </div>
                                    </div>
                                @else
                                    {{Form::hidden('shipping_company_'.$s_order->item_id,'')}}
                                @endif
                                
                                <div class="form-group">
                                    {{ Form::label('tracking_id', trans('myPurchases.tracking_id').':', array('class' => 'col-sm-3 control-label required-icon')) }}
                                    <div class="col-sm-6">
                                        {{ Form::text('tracking_id_'.$s_order->item_id, '', array('class' => 'form-control', 'id' => 'tracking_id_value_'.$s_order->item_id)) }}
                                        <label class="error">{{{ $errors->first('tracking_id') }}}</label>
                                    </div>
                                </div>
                                
                                @if(isset($from_country_det) && !empty($from_country_det))
                                    <div class="form-group">
                                        {{ Form::label('select_country', trans('myPurchases.stock_from_country').':', array('class' => 'col-sm-3 control-label')) }}
                                        <div class="col-sm-3">
                                            <span class="form-control">{{$from_country_det['country']}}</span>
                                            {{Form::hidden('select_country_'.$s_order->item_id, $from_country_det['id'])}}
                                            {{-- Form::select('select_country_'.$s_order->item_id, array('38' => 'China', '153' => 'Pakistan'),'', array('class' => 'form-control select2me input-medium ', 'id' => 'select_country_value_'.$s_order->item_id)) --}}
                                            <!--<label class="error">{{{ $errors->first('select_country') }}}</label>-->
                                        </div>
                                    </div>
                                @else
                                    {{Form::hidden('select_country_'.$s_order->item_id, $from_country_id)}}
                                @endif
                                
                                <div class="form-group">
                                    {{ Form::label('serial_number', trans('myPurchases.serial_numbers').':', array('class' => 'col-sm-3 control-label')) }}
                                    <div class="col-sm-7">
                                        {{ Form::textarea('serial_number_'.$s_order->item_id, Null, array('class' => 'form-control serial_number', 'rows' =>'8', 'cols' => '50', 'id' => $s_order->item_id)) }}
                                        <label class="error">{{{ $errors->first('serial_number') }}}</label>
                                    </div>
                                </div>
                            </div>
                            
                            <?php $count++;?>
                        </div>
                    @else
                        <div class="well">
                            <h3 id = "{{ $s_order->item_id }}">
                                <strong>{{ Form::label('product_name', trans('myPurchases.product_name').':', array('class' => 'control-label')) }}</strong>
                                <a href="{{$view_url}}" target="_blank" class="text-primary margin-left-10">{{ Form::label('product_name', $product_details->product_name.'('.$product_details->product_code.')' ) }}</a>
                                @if($s_order->shipping_status == 'shipped')  <strong>Shipped</strong> @endif
                            </h3>
                            <div class="mt10">
                                <div class="form-group">
                                    {{ Form::label('shipping_quantity', trans('myPurchases.shipping_quantity').':', array('class' => 'col-sm-3 control-label')) }}
                                    <div class="col-sm-6">
                                        {{ Form::label('shipping_quantity', $s_order->item_qty, array('class' => 'control-label text-bold')) }}
                                    </div>
                                </div>
                                @if($product_details->is_downloadable_product  == 'Yes')
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="note note-danger">{{ trans('myPurchases.shipping_not_allowed_for_downloadable_product') }}</div>
                                        </div>
                                    </div>
                                @elseif($allow_deal_block && isset($s_order->deal_id) && $s_order->deal_id > 0)   
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="note note-danger">{{Lang::get('deals::deals.shipping_not_allowed_not_tipped')}}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="note note-danger">{{Lang::get('myPurchases.cant_ship_investigate_the_order')}}</div>
                                        </div>
                                    </div>
                                @endif
                                
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="form-group">
                 <div class=" col-sm-9">
					 <?php
                        $is_all_not_shipped = $is_all_delivered = true;
                         foreach($shop_order as $key => $s_order){
                            if($s_order->shipping_status != 'delivered')
                                $is_all_delivered = false;
                            if($s_order->shipping_status != 'not_shipping')
                                $is_all_not_shipped = false;
                        }
                        $status = 'Partial';
                        if($is_all_delivered){
                            $status = 'Shipped';
                        }
                        elseif($is_all_not_shipped){
                            $status = 'Not Shipped';
                        }
                    ?>
					@if($status != 'Shipped')
		            	<a href="{{ URL::action('AdminPurchasesController@postSetAsShippingPopup') }}">
						<button type="submit" class="btn btn-success"><i class="fa fa-check"></i> {{trans('common.submit')}}</button></a>
		                <a href="javascript:;" itemprop="url" onclick="closeFancyBox()">
                        	<button type="reset" class="btn default"><i class="fa fa-times"></i> {{trans('common.cancel')}}</button>
                    	</a>
                    @else
                    	<a href="javascript:;" itemprop="url" onclick="closeFancyBox()">
                        	<button type="reset" class="btn btn-danger"><i class="fa fa-times"></i> {{trans('common.close')}}</button>
                    	</a>
                    	<a href="javascript:;" itemprop="url" onclick="closeFancyBox()">
                        	<button type="reset" class="btn default"><i class="fa fa-times"></i> {{trans('common.cancel')}}</button>
                    	</a>
                    @endif
                </div>
            </div>
	    {{ Form::close() }}
    </div>

	<script language="javascript" type="text/javascript">
		jQuery.validator.addMethod("checkEmptyLines", function (value, element) {
			if(value!='')
			{
		    	var serial_numbers_array = value.split("\n");
		    	var empty_lines = false;
		    	for(i = 0; i < serial_numbers_array.length; i++) {
		    		if (serial_numbers_array[i].trim() == "") {
		    			empty_lines = true;
		    		}
		    	}
		    	if (empty_lines) {
		    		return false;
		    	}
	    	}
			return true;
	    }, "There are some empty lines. Enter each serial number in sperate lines without empty line");

		jQuery.validator.addMethod("validateSerialNumberChina", function (value, element) {
			if(value!='')
			{
				var item_id = $(element).attr('id');
				var quantity = $('#order_item_qty_'+item_id).val();
				var serial_numbers_array = value.split("\n");
				if (serial_numbers_array.length == quantity) {
					return true;
				}
				return false;
			}
			else
				return true;
		}, "Serial numbers count must be equal to Quantity purchase");



		$("#set_as_shipping_frm").validate({
				rules: {
						serial_number: {
							//required: true,
							//validateSerialNumberChina: true,
							checkEmptyLines: true
						},
						tracking_id: {
		                	required: true
		            	}
					},
					messages: {
						serial_number: {
							required: mes_required
						},
						tracking_id: {
							required: mes_required
						}
					},
				submitHandler: function(form) {
				form.submit();
			},

			highlight: function (element) { // hightlight error inputs
				$(element)
				.closest('.form-group').addClass('has-error'); // set error class to the control group
			},

			unhighlight: function (element) { // revert the change done by hightlight
				$(element)
				.closest('.form-group').removeClass('has-error'); // set error class to the control group
			}
		});
		 $('[name^="serial_number_"]').each(function (element) {
				$(this).rules('add', {
						//validateSerialNumberChina: true,
						checkEmptyLines: true,
						//required: true,
				});
			});
			$('[name^="tracking_id_"]').each(function (element) {
				$(this).rules('add', {
						required: true,
				});
			});

		function closeFancyBox() {
			parent.$.fancybox.close();
		}

		$(".js_product_name").click(function()
		{
			var value = $(this).attr('id');
			var div_id = 'js_hide_values_'+value;
			$("#"+div_id).toggle();
		});

		/*function updateSetAsShipping(order_id) {
			//var id = $('#js_product_name').attr('id');
			//var tracking_id =$('#tracking_id_value'+id).val();
			//var select_country = $('#select_country_value'+id).val();
			///var serial_number = $('#serial_number_value'+id).val();
			//var shipping_company = $('#shipping_company_value'+id).val();
			//alert(id); return false;
			var actions_url = '{{ URL::action('AdminPurchasesController@postSetAsShippingPopup')}}';
			postData = 'order_id='+ order_id;
			parent.displayLoadingImage(true);
			$.post(actions_url, postData,  function(data)
			{
				parent.hideLoadingImage (false);
				//data = eval( '(' +  response + ')');

				if(data == 'success')
				{
					parent.$('#set_as_paid_info_'+order_id).html(data);
					parent.$('#container_'+order_id).html('<p class="label label-success">Payment Completed</p>');
					//parent.updateSetAsPaidValues(data);
					parent.$.fancybox.close();
				}
				else{
					$('#error_msg_div').html(data);
				}
			});
		}*/
	</script>
@stop