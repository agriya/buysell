@extends('popup')
@section('content')
	<div id="error_msg_div"></div>
    <h1>{{trans('showCart.shipping_details')}}</h1>

    <div class="pop-content">
        {{ Form::open(array('url' => URL::action('CartController@postUpdateShippingCountryAndCost'), 'class' => 'form-horizontal',  'id' => 'shipping_company_frm', 'name' => 'form_checkout')) }}
			{{Form::hidden('ship_template_id', $d_arr['ship_template_id'], array('id' => 'ship_template_id'))}}
			{{Form::hidden('quantity', $d_arr['quantity'], array('id' => 'quantity'))}}
			{{Form::hidden('product_id', $d_arr['product_id'], array('id' => 'product_id'))}}
			{{Form::hidden('matrix_id', $d_arr['matrix_id'], array('id' => 'matrix_id'))}}
        	<div class="form-group fn_clsPriceFields {{{ $errors->has('shipping_country') ? 'error' : '' }}}">
                {{ Form::label('shipping_country', trans('showCart.ship_orders_to').':', array('class' => 'col-sm-3 control-label required-icon')) }}
                <div class="col-sm-2">
                    {{  Form::select('shipping_country', $d_arr['countries_list'], Input::old('shipping_country', $d_arr['shipping_country_id']), array('class' => 'form-control select2me input-medium fnShippingCostEstimate', 'id' => 'shipping_country')); }}
                    <label class="error">{{{ $errors->first('shipping_country') }}}</label>
                </div>
            </div>

			<div class="margin-top-30">
                <h2 class="title-one">{{trans('shippingTemplates.choose_shipping_method')}}</h2>
                <div class="table-responsive shippingpop-table">
                    <table summary="" id="resourcednd" class="fn_ItemResourceImageListTable table table-striped table-hover table-bordered">
                        <thead>
                            <tr class="nodrag nodrop" id="ItemResourceTblHeader">
                                <th class="col-sm-3">{{trans('shippingTemplates.shipping_company')}}</th>
                                <!--<th class="col-sm-3">Shipping Type</th>-->
                                <th class="col-sm-3">{{trans('shippingTemplates.cost')}}</th>
                                <th class="col-sm-3">{{trans('shippingTemplates.shipping_time')}}</th>
                            </tr>
                        </thead>
                        <tbody class="fn_formBuilderListBody" id="shippingfee_tbl_body">
                            <?php $submit_disabled = 'disabled=disable'; ?>
                            @if(count($d_arr['shipping_companies_list']) > 0)
                                @foreach($d_arr['shipping_companies_list'] as $companies_list)
                                    <?php
                                        $shipping_company_id = isset($d_arr['shipping_company_id']) ? $d_arr['shipping_company_id'] : 0;
                                        $checked = ($companies_list['company_id'] == $shipping_company_id) ? 'checked = "checked"' : '';
                                        $disabled = (isset($companies_list['error_message']) && $companies_list['error_message']!='')?'disabled=disable':'';
                                        if($disabled == '')
                                            $submit_disabled = '';
                                    ?>
                                    <tr id="itemShippingRow_{{$companies_list['id']}}">
                                        <td>
                                            <input type="radio" name="shipping_company" id="shipping_company_{{ $companies_list['company_id'] }}" value="{{ $companies_list['company_id'] }}" {{ $checked }} {{$disabled}} >
                                            <label for="shipping_company_{{ $companies_list['company_id'] }}">{{$companies_list['company_name']}}</label>
                                        </td>
                                        <!--<td>{{$companies_list['shipping_type']}}</td>-->
                                        @if(isset($companies_list['error_message']) && $companies_list['error_message']!='')
                                            <td colspan="2"><p class="text-danger">{{$companies_list['error_message']}}</p></td>
                                        @else
                                            <td>@if($companies_list['shipping_fee'] > 0)
													{{ CUtil::convertAmountToCurrency($companies_list['shipping_fee'], Config::get('generalConfig.site_default_currency'), '', true) }}
												@else
													<span class="text-success">Free</span>
												@endif
											</td>
                                            <td>{{$companies_list['shipping_times']}}</td>
                                        @endif

                                    </tr>
                                @endforeach
                            @else
                                {{Form::hidden('no_records', 1, array('id' => 'no_records'))}}
                                <tr><td colspan="4"><p class="alert alert-info">{{ trans('common.do_not_ship_to_country') }}</p></td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    <div class="col-sm-7">
                        <button type="button" {{$submit_disabled}} onclick="updateShippingCompany();" class="btn btn-success"><i class="fa fa-check"></i> {{Lang::get('common.submit')}}</button>
                        <a href="javascript:;" itemprop="url" onclick="closeFancyBox()">
                            <button type="reset" class="btn default"><i class="fa fa-times"></i> {{Lang::get('common.cancel')}}</button>
                        </a>
                    </div>
                </div>
            </div>
	    {{ Form::close() }}
    </div>

    <script language="javascript" type="text/javascript">
    	parent.hideLoadingImage(false);
		function showErrorDialog(err_data) {
			var err_msg ='<div class="note note-danger">'+err_data.error_message+'</div>';
			$('#error_msg_div').html(err_msg);
		}

		function updateShippingCompany() {
			var no_records = $('#no_records').val();
			if(no_records)
				return false;
			var product_id = $('#product_id').val();
			var ship_template_id = $('#ship_template_id').val();
			var actions_url = '{{ URL::action('CartController@postUpdateShippingCountryAndCost')}}';
			var shipping_country_id = $('#shipping_country').val();
			var matrix_id = $('#matrix_id').val();
			var shipping_company = $('input[name=shipping_company]:checked', '#shipping_company_frm').attr('id');
			var shipping_company_id = shipping_company.split('_')[2];
			var quantity = $('#quantity').val();
			postData = 'product_id=' + product_id + '&ship_template_id=' + ship_template_id + '&shipping_country=' + shipping_country_id + '&shipping_company=' +shipping_company_id + '&quantity=' + quantity + '&matrix_id=' +matrix_id,
			parent.displayLoadingImage(true);
			$.post(actions_url, postData,  function(response)
			{
				parent.hideLoadingImage (false);
				data = eval( '(' +  response + ')');
				if(data.result == 'success')
				{
					parent.updateShippingCompanyValues(data, quantity);
				}
				else
				{
					showErrorDialog({status: 'error', error_message: data.error_msg});
				}
			});
		}

    	function closeFancyBox() {
			parent.$.fancybox.close();
		}
		$('#shipping_country').change(function(){
			var ship_template_id = $('#ship_template_id').val();
			var actions_url = '{{ URL::action('CartController@getUpdateShippingCountryAndCost')}}';
			var shipping_country_id = $('#shipping_country').val();
			if($('input[name=shipping_company]:checked').length > 0)
			{
				var shipping_company = $('input[name=shipping_company]:checked', '#shipping_company_frm').attr('id');
				var shipping_company_id = shipping_company.split('_')[2];
			}
			else
				var shipping_company_id = 0;
			var quantity = $('#quantity').val();
			var product_id = $('#product_id').val();
			var matrix_id = $('#matrix_id').val();

			postData = 'ship_template_id=' + ship_template_id + '&shipping_country_id=' + shipping_country_id + '&shipping_company_id='+shipping_company_id+'&product_id='+product_id+'&quantity='+quantity + '&matrix_id=' +matrix_id;
			parent.displayLoadingImage(true);
			var reload_url = actions_url+'?'+postData;
			window.location.href=reload_url;
		});
    </script>
@stop