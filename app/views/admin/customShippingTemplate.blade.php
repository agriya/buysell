@extends('adminPopup')
@section('includescripts')
	<script type="text/javascript">
		@if (isset($is_redirect) && $is_redirect == 1)
			var is_redirect = {{$is_redirect}};
			@if(isset($id) && $id > 0)
				var temp_comp_id = '{{$id}}';
				parent.$('#template_company_id').val(temp_comp_id);
			@endif
			@if(isset($action_url) && $action_url != '')
				var action_url = '{{$action_url}}';
				parent.$('#shippingTemplateFrm').attr("action", action_url);
			@endif
			@if(isset($temp_name) && $temp_name!='')
				var temp_name = '{{$temp_name}}';
				parent.$('#template_name').val(temp_name);
			@endif
		@endif
		@if(isset($is_close) && $is_close!='')
			parent.hideLoadingImage (false);
			parent.$.fancybox.close();
		@endif
	</script>
@stop

@section('content')
	<!-- Page title starts -->
        <h1>{{ Lang::get('shippingTemplates.custom_shipping_template') }}</h1>
    <!-- Page title end -->

	<?php
        $shipping_setting  = (Input::old('shipping_setting')!='')?Input::old('shipping_setting'):'ship_to';
        $fee_type = (Input::old('fee_type')!='')? Input::old('fee_type') : ($is_custom_only_company ? '1' : '2');
        $custom_fee_type = (Input::old('custom_fee_type')!='')?Input::old('custom_fee_type'):'1';
        $open_tab = 0;
        if(isset($validation_errors) && !empty($validation_errors))
        {
            unset($validation_errors['other_discount']);
            if(count($validation_errors) > 0)
            {
                $open_tab = 1;
            }
        }
    ?>

    <div class="pop-content custom-shippingpop">
        <!-- Notifications Starts -->
        @include('notifications')
        <!-- Notifications End -->

        <!-- Info Starts -->
        <div id="error_msg_div"></div>
        <div id="success_msg_div"></div>
        <!-- Info End -->
        <div class="note note-info"><h2 class="fonts18 mar0">{{trans('shippingTemplates.company_name')}}: <strong>{{ $temp_comp_name }}</strong></h2></div>
        {{ Form::open(array('id'=>'shippingTemplateFrm', 'method'=>'post','class' => 'form-horizontal' )) }}
            <div class="custom-delivery">
                @if(isset($shipping_custom_details) && count($shipping_custom_details) > 0)<?php //!$shipping_custom_details->IsEmpty() ?>
                    @foreach($shipping_custom_details as $key => $custom_details)
                        <div class="row note note-info js-fee-custom-divs countries-cont" id="feeCustomRow_{{$custom_details->id}}">
                            <div class="col-sm-1 col-xs-1 js-fee-custom-orderdiv"><strong>{{$key+1}}</strong></div>
                            @if(!empty($custom_details['countries']))
                                <div class="col-sm-5 col-xs-4 countries-name">
                                    <?php $countries =  $custom_details['countries'];
                                    $countries_list = implode(', ',$countries);
                                    $countries_index = array_slice($countries,0,3);
                                    $countries_name = implode(', ',$countries_index);
                                    ?>
                                    {{$countries_name}}
                                </div>
                                <span class="countries-list">{{$countries_list}}</span>
                            @endif
                            <div class="col-sm-5 col-xs-5">
                                @if(isset($custom_details['shipping_setting']))
                                    @if($custom_details['shipping_setting'] == 'dont_ship_to')
                                        <span class="text-danger">{{trans('shippingTemplates.donot_shipto_this_countries')}}</span>
                                    @else
                                        @if($custom_details['fee_type'] == '2')
                                            {{trans('shippingTemplates.standard')}} (<strong>{{$custom_details['discount']}}%</strong>)
                                        @elseif($custom_details['fee_type'] == '3')
                                            <span class="badge badge-success">{{trans('common.free')}}</span>
                                        @else
                                            <span class="badge badge-default">{{trans('shippingTemplates.custom')}}</span>
                                        @endif
                                    @endif
                                @endif
                            </div>
                            <div class="col-sm-1 col-xs-2 note-btn">
                                <a class="btn blue btn-xs" href="#" onclick="javascript:editFeeCustomShippingTemplate({{ $custom_details->id }});" title="{{ Lang::get('common.edit') }}"><i class="fa fa-edit"></i></a>
                                <a id="delete_{{$custom_details->id}}" href="javascript:void(0)" onclick="javascript:removeFeeCustomShippingTemplate({{ $custom_details->id }});" title="{{trans('common.delete')}}" class="btn btn-xs red"><i class="fa fa-trash-o"></i></a>
                            </div>
                        </div>
                    @endforeach
                @endif

                @if(isset($shipping_custom_details) && count($shipping_custom_details) > 0)
                    <div class="margin-bottom-10">
                        <a title="{{trans('shippingTemplates.add_new_shipping_group')}}" class="btn btn-xs purple-plum js-open-newgroup"><i class="fa fa-plus-circle"></i> {{trans('shippingTemplates.add_new_shipping_group')}}</a>
                    </div>
                @endif

                <div id="js-new-group-countries"  @if(isset($shipping_custom_details) && count($shipping_custom_details) > 0) style="display:none" @endif>
                    <div id="country-selection">
                        <div class="panel panel-default margin-bottom-20">
                            <div class="panel-heading">
                                <h4 class="panel-title ml15">
                                    <div class="checkbox">
                                        <label>
                                            {{Form::checkbox('geo_location','geo_location',true, array('class' => 'js-selection-type', 'id' => 'js-geo-location'))}}
                                            {{ Form::label('js-geo-location', trans('shippingTemplates.country_by_geo_location'), array()) }}
                                        </label>
                                    </div>
                                    <label class="error">{{{ $errors->first('geo_location') }}}</label>
                                </h4>
                            </div>
                            <div id="geo_location_content" class="panel-collapse collapse in mt-m5">
                                <label class="error">{{{ $errors->first('geo_countries') }}}</label>
                                <div class="panel-body">
                                    <ul class="list-unstyled">
                                        @foreach($geo_countries_arr as $geo_location => $countries)
                                            <li class="mb10">
                                                <?php
                                                    $geo_name = Products::getGeoLocationName($geo_location);
                                                ?>
                                                {{ Form::checkbox('geo_locations[]', $geo_location, null, array('id' => 'geo_locations_'.$geo_location, 'class' => 'js-choose-geo-country') ) }}
                                                {{ Form::label('geo_locations_'.$geo_location, $geo_name, array()) }}
                                                [<a href="javascript:;" class="js-disp-geo-country" id="{{$geo_location}}">{{trans('shippingTemplates.show_details')}}</a>]
                                                <label for="geo_locations[]" generated="true" class="error">{{{ $errors->first('geo_locations') }}}</label>
                                                <div id="countries_{{$geo_location}}" class="zonal-custdelvry margin-top-10" style="display:none">
                                                    <div class="ml15 clearfix">
                                                        @foreach($countries as $country)
                                                            <div class="col-sm-4 col-xs-6">
                                                                <?php
                                                                $dis_arr = array('class' => 'geo_contry_'.$geo_location.' geo_contry_list', 'id' => 'geo_countries_'.$geo_location.'_'.$country->id);
                                                                if(in_array($country->id, $prev_selected_countries))
                                                                {
                                                                    $dis_arr = $dis_arr+array('disabled' => 'disabled');
                                                                }
                                                                ?>
                                                                {{ Form::checkbox('geo_countries[]', $country->id, null, $dis_arr) }}
                                                                {{ Form::label('geo_countries_'.$geo_location.'_'.$country->id, $country->country, array()) }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mb20"><strong>OR</strong></div>
                        <div class="panel panel-default mb20">
                            <div class="panel-heading">
                                <h4 class="panel-title ml15">
                                    <div class="checkbox">
                                        <label>
                                            {{Form::checkbox('zone','zone',false, array('class' => 'js-selection-type', 'id' => 'js-zone'))}}
                                            {{ Form::label('js-zone', trans('shippingTemplates.country_by_zone'), array()) }}
                                        </label>
                                    </div>
                                    <label class="error">{{{ $errors->first('zone') }}}</label>
                                </h4>
                            </div>
                            <div id="zone_content" class="panel-collapse collapse mt-m5" style="display:none">
                                <label class="error">{{{ $errors->first('zone_countries') }}}</label>
                                <div class="panel-body">
                                    <ul class="list-unstyled">
                                        @foreach($zone_contries_arr as $zone => $countries)
                                            <li class="mb10">
                                                <?php
                                                    $zone_name = 'Zone '.$zone;
                                                ?>
                                                {{ Form::checkbox('zones[]', $zone, null, array('id' => 'zones_'.$zone, 'class' => 'js-choose-zone-country') ) }}
                                                {{ Form::label('zones_'.$zone, $zone_name, array()) }}
                                                [<a href="javascript:;" class="js-disp-zone-country" id="{{$zone}}">{{trans('shippingTemplates.show_details')}}</a>]
                                                <label class="error">{{{ $errors->first('geo_locations') }}}</label>
                                                <div id="zone_countries_{{$zone}}" class="zonal-custdelvry margin-top-10" style="display:none">
                                                    <div class="ml15 clearfix">
                                                        <label for="zone_countries[]" generated="true" class="error"></label>
                                                        @foreach($countries as $country)
                                                            <div class="col-sm-4 col-xs-6">
                                                                <?php
                                                                    $dis_arr = array('class' => 'zone_contry_'.$zone.' zone_contry_list', 'id' => 'zone_countries_'.$zone.'_'.$country->id);
                                                                    if(in_array($country->id, $prev_selected_countries))
                                                                    {
                                                                        $dis_arr = $dis_arr+array('disabled' => 'disabled');
                                                                    }
                                                                ?>
                                                                {{ Form::checkbox('zone_countries[]', $country->id, null, $dis_arr) }}
                                                                {{ Form::label('zone_countries_'.$zone.'_'.$country->id, $country->country, array()) }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tabbable-custom popup-tab">
                        <ul role="tablist" class="nav nav-tabs" id="myTab">
                            <li class="active">
                                <a href="#shipto" class="radio">
                                    <label>
                                        {{Form::radio('shipping_setting','ship_to',true, array('class' => 'js-shipping-details', 'id' => 'shipping_to'))}}
                                        {{ Form::label('shipping_to', trans('shippingTemplates.set_shipping_details')) }}
                                    </label>
                                </a>
                            </li>
                            <li>
                                <a href="#donotshipto" class="radio">
                                    <label>
                                        {{Form::radio('shipping_setting','dont_ship_to', false, array('class' => 'js-shipping-details', 'id' => 'donot_shipto'))}}
                                        {{ Form::label('donot_shipto', trans('shippingTemplates.donot_ship_to')) }}
                                    </label>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content form-horizontal" id="myTabContent">
                            <div id="shipto" class="tab-pane active">
                                <div class="select-shipping">
                                    <label class="error">{{{ $errors->first('fee_type') }}}</label>
                                    <p class="note note-info">{{trans('shippingTemplates.you_can_choose_only_custom_transport')}}</p>
                                    <div class="form-group mb30">
                                        {{ Form::label('fee_type', trans('shippingTemplates.select_shipping_type'), array('class' => 'control-label col-sm-3 col-xs-4 text-left')) }}
                                        <div class="col-sm-8 col-xs-8">
                                            <div class="row">
                                                <div class="col-sm-4 col-xs-4">
                                                    {{Form::select('fee_type', $fee_type_arr, $fee_type, array('id' => 'fee_type', 'class' => 'js-fee-type form-control bs-select'))}}
                                                </div>
                                                <div class="col-sm-8 col-xs-8 pad-left-0">
                                                    {{Form::select('custom_fee_type', array('1' => trans('shippingTemplates.freight_cost_by_quantity'), '2' => trans('shippingTemplates.freight_cost_by_weight')),1, array('id' => 'shipping_custom', 'class' => 'js-custom-fee-type form-control bs-select select-width', 'style' => 'display:none'))}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="shipping_standard">
                                        <div class="form-group">
                                            {{ Form::label('js-discount', trans('shippingTemplates.discount'), array('class' => 'control-label col-sm-3 col-xs-4 required-icon text-left')) }}
                                            <div class="col-sm-4 col-xs-4 custom-shiplist">
                                                <div class="input-group">
                                                    {{Form::text('discount','0', array('id'=>'js-discount', 'class' => 'form-control'))}}
                                                    <span class="input-group-addon">%</span>
                                                </div>
                                                <label for="js-discount" generated="true" class="error">{{{ $errors->first('discount') }}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="shipping_free" style="display:none">
                                    <div class="alert alert-info">{{trans('shippingTemplates.shipping_fee_for_countries_free')}}</div>
                                </div>

                                <div id="shipping_custom_fee" style="display:none">
                                    <div class="custom-shiplist shiplist-label">
                                        <ul id="shipping_custom_fee_qty" class="list-unstyled list-inline custom-quantity">
                                            <li>
                                                <label class="control-label required-icon" for="min_order">{{trans('shippingTemplates.min_order_for_base')}}</label>
                                                <div>
                                                    {{Form::text('min_order',1,array('class' => 'form-control', 'id' => 'min_order'))}}
                                                    <label class="error">{{{ $errors->first('min_order') }}}</label>
                                                </div>
                                            </li>
                                            <li>
                                                <label class="control-label required-icon" for="max_order">{{trans('shippingTemplates.max_order_for_base_weight')}}</label>
                                                <div>
                                                    {{Form::text('max_order','',array('class' => 'form-control', 'id' => 'max_order'))}}
                                                    <label class="error">{{{ $errors->first('max_order') }}}</label>
                                                </div>
                                            </li>
                                            <li>
                                                <label class="control-label required-icon" for="cost_base_weight">{{trans('shippingTemplates.shipping_cost_for_base_weight')}}</label>
                                                <div>
                                                    {{Form::text('cost_base_weight','',array('class' => 'form-control', 'id' => 'cost_base_weight'))}}
                                                    <label class="error">{{{ $errors->first('base_weight') }}}</label>
                                                </div>
                                            </li>
                                            <li>
                                                <label class="control-label mar0" for="extra_units">{{trans('shippingTemplates.extra_units')}}</label>
                                                <div>
                                                    {{Form::text('extra_units','',array('class' => 'form-control mt12', 'id' => 'extra_units'))}}
                                                    <label class="error">{{{ $errors->first('extra_units') }}}</label>
                                                </div>
                                            </li>
                                            <li>
                                                <label class="control-label mar0" for="extra_costs">{{trans('shippingTemplates.extra_costs')}}</label>
                                                <div>
                                                    {{Form::text('extra_costs','',array('class' => 'form-control mt12', 'id' => 'extra_costs'))}}
                                                    <label class="error">{{{ $errors->first('extra_costs') }}}</label>
                                                </div>
                                            </li>
                                        </ul>

                                        <div id="shipping_custom_fee_weight" class="custom-shiplist" style="display:none">
                                            <ul class="list-unstyled list-inline col-sm-12 clearfix pad-t10">
                                                <li class="col-sm-3 col-xs-6">
                                                    <div class="form-group">
                                                        <label class="control-label" for="initial_weight">{{trans('shippingTemplates.initial_weight')}}</label>
                                                        <div class="input-group">
                                                            {{Form::text('initial_weight',1,array('class' => 'form-control', 'id' => 'initial_weight'))}}
                                                            <span class="input-group-addon">Kg</span>
                                                        </div>
                                                        <label for="initial_weight" generated="true" class="error">{{{ $errors->first('initial_weight') }}}</label>
                                                    </div>
                                                </li>
                                                <li class="col-sm-8 col-xs-6">
                                                    <div class="form-group">
                                                        <label class="control-label required-icon" for="initial_weight_price">{{trans('shippingTemplates.initial_weight_price')}}</label>
                                                        <div class="input-group">
                                                            {{Form::text('initial_weight_price','',array('class' => 'form-control', 'id' => 'initial_weight_price'))}}
                                                            <span class="input-group-addon">$</span>
                                                        </div>
                                                        <label for="initial_weight_price" generated="true" class="error">{{{ $errors->first('initial_weight_price') }}}</label>
                                                    </div>
                                                </li>
                                            </ul>

                                            <ul id="weight_addition_1" class="list-unstyled list-inline col-sm-12 clearfix custom-weight pad-t10">
                                                <li class="col-sm-3 col-xs-6">
                                                    <div class="form-group">
                                                        <label class="control-label" for="base_weight"></label>
                                                        <div class="input-group">
                                                            {{Form::text('weight_from_1','1',array('class' => 'form-control', 'id' => 'weight_from_1', 'disabled' => 'disabled'))}}
                                                       </div>
                                                       <label for="weight_from_1" id="weight_from_error_1" generated="true" class="error">{{{ $errors->first('weight_from_1') }}}</label>
                                                    </div>
                                                </li>
                                                <li class="col-sm-3 col-xs-6">
                                                    <div class="form-group">
                                                        <label class="control-label"></label>
                                                        <div class="input-group">
                                                            {{Form::text('weight_to_1','',array('class' => 'form-control js-weight-to', 'id' => 'weight_to_1'))}}
                                                            <span class="input-group-addon">Kg</span>
                                                        </div>
                                                        <label for="weight_to_1" id="weight_to_error_1" generated="true" class="error">{{{ $errors->first('weight_to_1') }}}</label>
                                                    </div>
                                                </li>
                                                <li class="col-sm-3 col-xs-6">
                                                    <div class="form-group">
                                                        <label class="control-label pt15" for="additional_weight">{{trans('shippingTemplates.additional_weight')}}</label>
                                                        <div class="input-group">
                                                            {{Form::text('additional_weight_1','',array('class' => 'form-control', 'id' => 'additional_weight_1'))}}
                                                            <span class="input-group-addon">Kg</span>
                                                        </div>
                                                        <label for="additional_weight_1" id="additional_weight_error_1" generated="true" class="error">{{{ $errors->first('additional_weight_1') }}}</label>
                                                    </div>
                                                </li>
                                                <li class="col-sm-3 col-xs-6">
                                                    <div class="form-group">
                                                        <label class="control-label" for="additional_weight_added">{{trans('shippingTemplates.additional_weight_added_to_freight')}}</label>
                                                        <div class="input-group">
                                                            {{Form::text('additional_weight_added_1','',array('class' => 'form-control', 'id' => 'additional_weight_added_1'))}}
                                                            <span class="input-group-addon">$</span>
                                                        </div>
                                                        <label for="additional_weight_added_1" id="additional_weight_added_error_1" generated="true" class="error">{{{ $errors->first('additional_weight_added_1') }}}</label>
                                                    </div>
                                                </li>
                                            </ul>

                                            <?php $additional = (Input::old('current_added_group')!='')?Input::old('current_added_group'):'';	?>
                                            @if(isset($additional) && $additional!='' && $additional > 1)
                                                @for($i=2;$i<=$additional;$i++)
                                                    <div id="weight_addition_{{$i}}">
                                                        <div class="form-group">
                                                            <label class="col-sm-4 control-label" for="base_weight"></label>
                                                            <div class="col-sm-4">
                                                                {{Form::text('weight_from_'.$i,null,array('class' => 'form-control', 'id' => 'weight_from_'.$i))}}
                                                                <label class="error">{{{ $errors->first('weight_from_'.$i) }}}</label>
                                                                <div class="input-group">
                                                                    {{Form::text('weight_to_'.$i,null,array('class' => 'form-control', 'id' => 'weight_to_'.$i))}}
                                                                    <span class="input-group-addon">Kg</span>
                                                                </div>
                                                                <label for="weight_to_'.$i" generated="true" class="error">{{{ $errors->first('weight_to_'.$i) }}}</label>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-sm-4 control-label" for="additional_weight">{{trans('shippingTemplates.additional_weight')}}</label>
                                                            <div class="col-sm-4">
                                                                <div class="input-group">
                                                                    {{Form::text('additional_weight_'.$i,null,array('class' => 'form-control input-small', 'id' => 'additional_weight_'.$i))}}
                                                                    <span class="input-group-addon">Kg</span>
                                                                </div>
                                                                <label for="additional_weight_'.$i" generated="true" class="error">{{{ $errors->first('additional_weight_'.$i) }}}</label>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-sm-4 control-label" for="additional_weight_added">{{trans('shippingTemplates.additional_weight_added_to_freight')}}</label>
                                                            <div class="col-sm-4">
                                                                <div class="input-group">
                                                                    {{Form::text('additional_weight_added_'.$i,null,array('class' => 'form-control input-small', 'id' => 'additional_weight_added_'.$i))}}
                                                                    <span class="input-group-addon">$</span>
                                                                </div>
                                                                <label for="additional_weight_added_'.$i" generated="true" class="error">{{{ $errors->first('additional_weight_added_'.$i) }}}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endfor
                                            @endif
                                            <div class="clearfix">
                                                <a title="{{ trans('shippingTemplates.add_new_group')}}" class="btn blue pull-right" id="js-add-newweightgroup"><i class="fa fa-plus-circle"></i> {{ trans('shippingTemplates.add_new_group')}}</a>
                                                <label id="new_group_error" for="js-add-newweightgroup" class="error"></label>
                                            </div>
                                            {{Form::hidden('current_added_group', 1, array('id' => 'current_added_group'))}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="donotshipto" class="tab-pane">
                                <div class="note note-info mar0">{{trans('shippingTemplates.donot_ship_out_of_above_groups')}}</div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(isset($shipping_custom_details) && count($shipping_custom_details) > 0)
                    <div id="other_countries_regions">
                        <div class="clearfix mt20">
                            <span class="text-danger pull-left fonts18 mr5">*</span>
                            <div class="form"><h4 class="form-section no-top-space fonts18">{{ trans('shippingTemplates.add_shipping_setting_for_all_other_countries') }}</h4></div>
                        </div>
                        @if($is_custom_only_company)
                            <div>
                                <label>
                                    {{Form::hidden('other_shipping_setting', 'dont_ship_to')}}
                                    {{Form::checkbox('other_shipping_setting_dummy', '', true, array('class' => 'js-other-shipping-details', 'id' => 'other_shipping_setting_dummy', 'disabled' => 'disabled'))}}
                                    {{ Form::label('other_shipping_setting_dummy', trans('shippingTemplates.donot_ship_out_of_above_groups')) }}
                                </label>
                                <div>
                                    <div class="note note-warning"><i class="fa fa-warning orange mr10"></i>{{ trans('shippingTemplates.donot_ship_out_of_above_groups') }}</div>
                                </div>
                            </div>
                        @else
                            <div class="tabbable-custom popup-tab">
                                <ul role="tablist" class="nav nav-tabs" id="myOtherTab">
                                    <?php
                                        $ship_to_check = true;
                                        $dont_ship_to_check = false;
                                        $other_discount = 0;
                                        if(isset($other_countries_details->shipping_setting) && $other_countries_details->shipping_setting!='')
                                        {
                                            if($other_countries_details->shipping_setting == 'dont_ship_to')
                                            {
                                                $ship_to_check = false;
                                                $dont_ship_to_check = true;
                                            }

                                        }
                                        if(isset($other_countries_details->shipping_setting) && $other_countries_details->shipping_setting!='')
                                        {
                                            $other_discount = $other_countries_details->discount;
                                        }
                                    ?>
                                    <li class="active">
                                        <a href="#othershipto" class="radio">
                                            <label>
                                                {{Form::radio('other_shipping_setting','ship_to', $ship_to_check, array('class' => 'js-other-shipping-details', 'id' => 'other_shipping_to'))}}
                                                {{ Form::label('other_shipping_to', trans('shippingTemplates.set_shipping_details')) }}
                                            </label>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#otherdonotshipto" class="radio">
                                            <label>
                                                {{Form::radio('other_shipping_setting','dont_ship_to', $dont_ship_to_check, array('class' => 'js-other-shipping-details', 'id' => 'other_donot_shipto'))}}
                                                {{ Form::label('other_donot_shipto', trans('shippingTemplates.donot_ship_to')) }}
                                            </label>
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="myOtherTabContent">
                                    <div id="othershipto" class="tab-pane @if($ship_to_check) active @endif" @if(!$ship_to_check) style="display:none" @endif>
                                        <div class="select-shipping">
                                            <div class="form-group">
                                                {{ Form::label('other_fee_type', trans('shippingTemplates.select_shipping_type'), array('class' => 'control-label col-sm-3 col-xs-4 text-left')) }}
                                                <div class="col-sm-8 col-xs-8">
                                                    <div class="row">
                                                        <div class="col-sm-4 col-xs-4">
                                                            {{Form::select('other_fee_type', array('2' => Lang::get('shippingTemplates.standard')), 2, array('id' => 'other_fee_type', 'class' => 'form-control') )}}
                                                            <label class="error">{{{ $errors->first('other_fee_type') }}}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="other_shipping_standard">
                                                <div class="form-group">
                                                    {{ Form::label('other_discount', trans('shippingTemplates.discount'), array('class' => 'control-label col-sm-3 col-xs-4 text-left')) }}
                                                    <div class="col-sm-4 col-xs-4 custom-shiplist">
                                                        <div class="input-group">
                                                            {{Form::text('other_discount',$other_discount, array('id'=>'other_discount', 'class' => 'form-control'))}}
                                                            <span class="input-group-addon">%</span>
                                                        </div>
                                                        <label for="other_discount" generated="true" class="error">{{{ $errors->first('other_discount') }}}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="otherdonotshipto" class="tab-pane @if($dont_ship_to_check) active @endif" @if(!$dont_ship_to_check) style="display:none" @endif>
                                        <div class="note note-info mar0">{{ trans('shippingTemplates.donot_ship_out_of_above_groups') }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="form-group">
                    <div class="col-sm-12">
                        <?php $new_group = (isset($shipping_custom_details) && count($shipping_custom_details) > 0)?0:1;
                         ?>
                        {{Form::hidden('id',$id)}}
                        {{Form::hidden('company_id',$company_id)}}
                        {{Form::hidden('fee_custom_id',0, array('id'=>'fee_custom_id'))}}
                        {{Form::hidden('new_group',$new_group, array('id'=>'new_group'))}}
                        {{Form::hidden('template_name',Input::get('t_name'),array('id'=>'template_name')) }}

                        <button name="confirm_shipping" id="confirm_shipping" value="confirm_shipping" type="submit" class="btn green" @if(isset($shipping_custom_details) && count($shipping_custom_details) > 0) style ="display:none" @endif>
                            <i class="fa fa-check"></i> {{ trans("shippingTemplates.confirm") }}
                        </button>
                        <button name="cancel_shipping" id="cancel_shipping" value="cancel_shipping" type="button" class="btn default" @if(isset($shipping_custom_details) && count($shipping_custom_details) > 0) style ="display:none" @endif>
                            <i class="fa fa-times"></i> {{ trans("shippingTemplates.cancel") }}
                        </button>
                        @if(isset($shipping_custom_details) && count($shipping_custom_details) > 0)
                            <button name="save_shipping" id="save_shipping" value="save_shipping" type="submit" class="btn green">
                                <i class="fa fa-save"></i> {{ trans("shippingTemplates.save") }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        {{Form::close()}}

        <small id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></small>
        <div id="dialog-delete-confirm" class="confirm-dialog-delete" title="" style="display:none;">
            <span class="ui-icon ui-icon-alert"></span>
            <span id="dialog_msg" class="show ml15">{{  trans('admin/shippingTemplates.confirm_delete') }}</span>
        </div>

        <div id="dialog-remove-confirm" class="confirm-dialog-remove" title="" style="display:none;">
            <span class="ui-icon ui-icon-alert"></span>
            <span id="dialog_msg" class="show ml15">{{  trans('admin/shippingTemplates.confirm_remove') }}</span>
        </div>
    </div>

<script type="text/javascript">
	var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
	var template_actions_url = '{{ URL::action('AdminShippingTemplateController@postShippingTemplateFeeCustomAction')}}';
	parent.hideLoadingImage (false);

	var default_shipping_setting = '{{$shipping_setting}}';
	var default_fee_type = '{{$fee_type}}';
	var default_custom_fee_type = '{{$custom_fee_type}}';
	var open_tab = '{{$open_tab}}';

	if(open_tab == '1')
	{
		if($('.js-open-newgroup').length > 0)
		{
			$('.js-open-newgroup').trigger('click');
		}
	}

	if(default_shipping_setting =='ship_to')
	{
		$('#shipto').show();
		$('#donotshipto').hide();

		if(default_fee_type == 1)
		{
			$('#shipping_custom').show();
			$('#shipping_custom_fee').show();
			$('#shipping_standard').hide();
			$('#shipping_free').hide();
			if(default_custom_fee_type==1)
			{
				$('#shipping_custom_fee_qty').show();
				$('#shipping_custom_fee_weight').hide();
			}
			else
			{
				$('#shipping_custom_fee_qty').hide();
				$('#shipping_custom_fee_weight').show();
			}
		}
		else if(default_fee_type == 2)
		{
			$('#shipping_custom').hide();
			$('#shipping_custom_fee').hide();
			$('#shipping_standard').show();
			$('#shipping_free').hide();

		}
		else if(default_fee_type == 3)
		{
			$('#shipping_custom').hide();
			$('#shipping_custom_fee').hide();
			$('#shipping_standard').hide();
			$('#shipping_free').show();
		}

	}
	else
	{
		$('#shipto').hide();
		$('#donotshipto').show();
	}

	function showErrorDialog(err_data) {
		var err_msg ='<div class="note note-danger">'+err_data.error_message+'</div>';
		$('#error_msg_div').html(err_msg);
		var body = $("html, body");
		body.animate({scrollTop:0}, '500', 'swing', function() {
		});
	}
	function showSuccessMessage(err_data)
	{
		var err_msg ='<div class="alert alert-success alert-block">'+err_data.success_message+'</div>';
		$('#success_msg_div').html(err_msg);
		var body = $("html, body");
		body.animate({scrollTop:0}, '500', 'swing', function() {
		});

	}
	function removeErrorDialog() {
		$('#error_msg_div').html('');
	}

	$('.js-choose-geo-country').each(function() {
		var id = $(this).val();
		var checkbox_class = 'geo_contry_'+id;
		

		if($('.'+checkbox_class+':disabled').length == $('.'+checkbox_class).length)
		{
			$(this).prop('disabled',true)
		}
	});

	$(".geo_contry_list").click(function()
	{
		var country_list_id = $(this).attr('id');
		var geo_location_id = country_list_id.split('_')[2];
		if($('.geo_contry_' + geo_location_id + ':checked').length == $('.geo_contry_' + geo_location_id + ':not(:disabled)').length)
		{
			$('#geo_locations_' + geo_location_id).prop('checked',true);
		}
		else
		{
			$('#geo_locations_' + geo_location_id).prop('checked',false);
		}
	});

	$(".zone_contry_list").click(function()
	{
		var country_list_id = $(this).attr('id');
		var zone_id = country_list_id.split('_')[2];
		if($('.zone_contry_' + zone_id + ':checked').length == $('.zone_contry_' + zone_id + ':not(:disabled)').length)
		{
			$('#zones_' + zone_id).prop('checked',true);
		}
		else
		{
			$('#zones_' + zone_id).prop('checked',false);
		}
	});

	$('.js-choose-zone-country').each(function() {
		var id = $(this).val();
		var checkbox_class = 'zone_contry_'+id;
		if($('.'+checkbox_class+':disabled').length == $('.'+checkbox_class).length)
		{
			$(this).prop('disabled',true)
		}
	});


	$("#initial_weight").blur(function(){
		if($(this).val()!='')
		{
			$('#weight_from_1').val($(this).val());
		}
	});

	$(".js-disp-geo-country").click(function()
	{
		var value = $(this).attr('id');
		var div_id = 'countries_'+value;
		$("#"+div_id).toggle();
	});

	$(".js-disp-zone-country").click(function()
	{
		var value = $(this).attr('id');
		var div_id = 'zone_countries_'+value;
		$("#"+div_id).toggle();
	});

	$(".js-weight-to").live("input", function (e) {
		var element_id = $(this).attr('id');
		var id = element_id.split('weight_to_')[1];
		next_id = 0;
		if(id!='')
		{
			next_id = parseInt(id)+1;
		}

		if($('#weight_from_'+next_id).length > 0)
		{
			$('#weight_from_'+next_id).val($(this).val());
		}

	});

	$(".js-choose-geo-country").change(function()
	{

		var value = $(this).val();
		var checkbox_class = 'geo_contry_'+value;

		if($(this).prop('checked'))
		{
			$('.'+checkbox_class).each(function() {
				if (!$(this).is(':disabled'))
				{
					$(this).prop('checked', true)
					$(this).parent().addClass('checked');
				}
			});
			//$("."+checkbox_class).prop('checked', true);
		}
		else
		{
			$('.'+checkbox_class).each(function() {
				if (!$(this).is(':disabled'))
				{
					$(this).prop('checked', false)
					$(this).parent().removeClass('checked');
				}
			});
			//$("."+checkbox_class).prop('checked', false);
		}
	});

	$(".js-choose-zone-country").change(function()
	{

		var value = $(this).val();
		var checkbox_class = 'zone_contry_'+value;

		if($(this).prop('checked'))
		{
			$('.'+checkbox_class).each(function() {
				if (!$(this).is(':disabled'))
				{
					$(this).prop('checked', true)
					$(this).parent().addClass('checked');
				}
			});
			//$("."+checkbox_class).prop('checked', true);
		}
		else
		{
			$('.'+checkbox_class).each(function() {
				if (!$(this).is(':disabled'))
				{
					$(this).prop('checked', false)
					$(this).parent().removeClass('checked');
				}
			});
			//$("."+checkbox_class).prop('checked', false);
		}
	});

	$(".js-shipping-details").click(function()
	{
		$("#myTab>li.active").removeClass("active");
		$(this).closest('li').addClass('active');

		var shippin_val = $(this).val();
		if(shippin_val == 'ship_to')
		{
			$('#shipto').show();
			$('#donotshipto').hide();
		}
		else
		{
			$('#shipto').hide();
			$('#donotshipto').show();
		}
	});
	$(".js-other-shipping-details").click(function()
	{
		$("#myOtherTab>li.active").removeClass("active");
		$(this).closest('li').addClass('active');
		var shippin_val = $(this).val();
		if(shippin_val == 'ship_to')
		{
			$('#othershipto').show();
			$('#otherdonotshipto').hide();
		}
		else
		{
			$('#othershipto').hide();
			$('#otherdonotshipto').show();
		}
	});
	$(".js-fee-type").change(function()
	{
		var valu = $(this).val();
		if(valu==1)
		{
			$('#shipping_custom').show();
			$('#shipping_custom_fee').show();
			$('#shipping_standard').hide();
			$('#shipping_free').hide();
		}
		else if(valu==2)
		{
			$('#shipping_custom').hide();
			$('#shipping_custom_fee').hide();
			$('#shipping_standard').show();
			$('#shipping_free').hide();

		}
		else if(valu==3)
		{
			$('#shipping_custom').hide();
			$('#shipping_custom_fee').hide();
			$('#shipping_standard').hide();
			$('#shipping_free').show();
		}
	});

	$(".js-selection-type").change(function()
	{
		var val = $(this).val();
		var check = $(this).attr('checked');
		var geo_div_id = 'geo_location_content';
		var zone_div_id = 'zone_content';
		if(val == 'geo_location')
		{
			if($(this).prop('checked'))
			{
				$('#'+geo_div_id).show();
				$('#'+zone_div_id).hide();
				$('#js-zone').prop('checked', false);
				$('#js-zone').removeAttr('checked');
				$('#js-zone').parent().removeClass('checked');
			}
			else
			{

				$('#'+zone_div_id).show();
				$('#'+geo_div_id).hide();
				$('#js-zone').prop('checked', true);
				$('#js-zone').attr('checked', 'checked');
				$('#js-zone').parent().addClass('checked');
			}
		}
		else
		{
			if($(this).prop('checked'))
			{
				$('#'+zone_div_id).show();
				$('#js-geo-location').prop('checked', false);
				$('#js-geo-location').removeAttr('checked');
				$('#js-geo-location').parent().removeClass('checked');
				$('#'+geo_div_id).hide();
			}
			else
			{
				$('#'+geo_div_id).show();
				$('#'+zone_div_id).hide();
				$('#js-geo-location').prop('checked', true);
				$('#js-geo-location').attr('checked', 'checked');
				$('#js-geo-location').parent().addClass('checked');
			}
		}
	});

	$('.js-custom-fee-type').change(function(){
		var value = $(this).val();
		if(value==1)
		{
			$('#shipping_custom_fee_qty').show();
			$('#shipping_custom_fee_weight').hide();
		}
		else
		{
			$('#shipping_custom_fee_qty').hide();
			$('#shipping_custom_fee_weight').show();
		}
	});

	$('.js-open-newgroup').click(function(){

		$('#js-new-group-countries').toggle();
		if($('#js-new-group-countries').css('display') == 'none')
		{
			$('#confirm_shipping').hide();
			$('#cancel_shipping').hide();
			$('#save_shipping').show();
			$('#other_countries_regions').show();
			$('#new_group').val(0);

		}
		else
		{
			$('#confirm_shipping').show();
			$('#cancel_shipping').show();
			$('#save_shipping').hide();
			$('#other_countries_regions').hide();
			$('#new_group').val(1);
		}
	});

	function validateCustomWeightAllField(index)
	{
		var result = true;
		var errors = new Array();
		if(index != '' && !isNaN(index))
		{
			for(i=1; i<=index; i++)
			{
				if(i == 1)
				{
					var prev_weight_to = $('#initial_weight').val();
				}
				else
				{
					var prev_index = i-1;
					var prev_weight_to = $('weight_from_'+prev_index).val();
				}
				var weight_from = $('#weight_from_'+i).val();
				var weight_to = $('#weight_to_'+i).val();
				var additional_weight = $('#additional_weight_'+i).val();
				var additional_weight_added = $('#additional_weight_added_'+i).val();
				if(weight_from == '' || weight_from <=0)
				{
					errors['weight_from_'+i] = '{{trans('shippingTemplates.require_and_greater_than_zero')}}';
					result = false;
				}
				else
				{
					if(weight_from != prev_weight_to)
					{
						errors['weight_from_'+i] = '{{trans('shippingTemplates.equal_to_prev_weight')}}';
						result = false;
					}
				}
				//
				if(weight_to == '' || weight_to <=0)
				{
					errors['weight_to_'+i] = '{{trans('shippingTemplates.require_and_greater_than_zero')}}';
				}
				else
				{
					if(weight_to <= weight_from)
					{
						errors['weight_to_'+i] = '{{trans('shippingTemplates.greater_than_weight_from')}}';
						result = false;
					}
				}

				//
				if(additional_weight == '' || additional_weight <=0)
				{
					errors['additional_weight_'+i] = '{{trans('shippingTemplates.require_and_greater_than_zero')}}';
				}
				else
				{

				}
				if(additional_weight_added == '' || additional_weight_added <=0)
				{
					errors['additional_weight_added_'+i] = '{{trans('shippingTemplates.require_and_greater_than_zero')}}';
				}

			}
		}
		return result;
	}
	function removeWeightRange(id)
	{
		$("#dialog-remove-confirm").dialog({ title: cfg_site_name, modal: true,
				buttons: {
					"{{ trans('common.yes') }}": function() {
						//displayLoadingImage(true);
						var last_val = $('#current_added_group').val();
						for(i=id;i<=last_val;i++)
						{
							$('#weight_addition_'+i).remove();
						}
						var current_range = id-1;
						$('#current_added_group').val(current_range);
						$(this).dialog("close");
					},
					"{{ trans('common.no') }}": function() {
						$(this).dialog("close");
					}
				}
			});
	}
	$('#js-add-newweightgroup').click(function(){

		var last_val = $('#current_added_group').val();
		//validate all before weight inputs. if not valid then dont allow to add


		for(i=1;i<=last_val;i++)
		{
			if(!$('#weight_from_'+i).valid() || !$('#weight_to_'+i).valid() || !$('#additional_weight_'+i).valid() || !$('#additional_weight_added_'+i).valid())
			{
				$('#new_group_error').html('{{trans('shippingTemplates.to_add_all_prev_valid')}}');
				return false;
			}
		}
		$('#new_group_error').html('');

		var updated_val = parseInt(last_val)+1;
		var current_div_id = 'weight_addition_'+updated_val;
		var weight_form = $('#weight_addition_1').clone();

		weight_form.attr('id', current_div_id);
		last_div = 'weight_addition_'+last_val;
		$('#'+last_div).after(weight_form);

		//To clear the previous error
		$('#'+current_div_id).find('.error').html('');

		$('#'+current_div_id).find('#weight_to_1').attr('name','weight_to_'+updated_val);
		$('#'+current_div_id).find('#weight_to_1').val('');
		$('#'+current_div_id).find('#weight_to_1').attr('id','weight_to_'+updated_val);
		//label
		$('#'+current_div_id).find('#weight_to_error_1').attr('for','weight_to_'+updated_val);
		$('#'+current_div_id).find('#weight_to_error_1').attr('id','weight_to_error_'+updated_val);

		$('#'+current_div_id).find('#weight_from_1').attr('name','weight_from_'+updated_val);
		$('#'+current_div_id).find('#weight_from_1').val($('#weight_to_'+last_val).val());
		$('#'+current_div_id).find('#weight_from_1').attr('id','weight_from_'+updated_val);
		//label
		$('#'+current_div_id).find('#weight_from_error_1').attr('for','weight_from_'+updated_val);
		$('#'+current_div_id).find('#weight_from_error_1').attr('id','weight_from_error_'+updated_val);


		$('#'+current_div_id).find('#additional_weight_1').attr('name','additional_weight_'+updated_val);
		$('#'+current_div_id).find('#additional_weight_1').val('');
		$('#'+current_div_id).find('#additional_weight_1').attr('id','additional_weight_'+updated_val);
		//label
		$('#'+current_div_id).find('#additional_weight_error_1').attr('for','additional_weight_'+updated_val);
		$('#'+current_div_id).find('#additional_weight_error_1').attr('id','additional_weight_error_'+updated_val);


		$('#'+current_div_id).find('#additional_weight_added_1').attr('name','additional_weight_added_'+updated_val);
		$('#'+current_div_id).find('#additional_weight_added_1').val('');
		$('#'+current_div_id).find('#additional_weight_added_1').attr('id','additional_weight_added_'+updated_val);
		//label
		$('#'+current_div_id).find('#additional_weight_added_error_1').attr('for','additional_weight_added_'+updated_val);
		$('#'+current_div_id).find('#additional_weight_added_error_1').attr('id','additional_weight_added_error_'+updated_val);

		//var close_range  = '<a onclick="removeWeightRange('+updated_val+')">Remove</a>';
		$('#'+current_div_id).append('<a onclick="removeWeightRange('+updated_val+')" class="pos-abs"><i class="fa fa-times-circle"></i></a>');

		$('#current_added_group').val(updated_val);


		//Add validation rules to the form
		$('[name="weight_from_'+updated_val+'"]').each(function (element) {
			$(this).rules('add', {
				required: function(element){
						return is_custom_weight_required();
				},
				number: true,
				equalTo:'#weight_to_'+last_val,
	            messages: {
	                required: mes_required,
					number: jQuery.format("{{trans('shippingTemplates.number_validation')}}"),
					equalTo: jQuery.validator.format("{{trans('shippingTemplates.equal_to_validation')}} {0}")
				}
			});
		});

		$('[name="weight_to_'+updated_val+'"]').each(function (element) {
			$(this).rules('add', {
				required: function(element){
								return is_custom_weight_required();
							},
				number: true,
				min:function(e){

							if($('#weight_from_'+updated_val).val()!='')
							{
								var wf = parseFloat($('#weight_from_'+updated_val).val());
								return wf+1;
							}
							else
							{
								return false;
							}
						},
	            messages: {
	                required: mes_required,
	                number: jQuery.format("{{trans('shippingTemplates.number_validation')}}"),
					min: jQuery.validator.format("{{trans('shippingTemplates.min_validation')}} {0}")
				}
			});
		});

		$('[name="additional_weight_'+updated_val+'"]').each(function (element) {
			$(this).rules('add', {
				required: function(element){
						return is_custom_weight_required();
				},
				number: true,
				min:1,
				max:function(element){
					if(($('#weight_from_'+updated_val).val()!= '') && ($('#weight_to_'+updated_val).val()!= ''))
					{
						var wf = parseFloat($('#weight_from_'+updated_val).val());
						var wt = parseFloat($('#weight_to_'+updated_val).val());
						return wt-wf;
					}
					else
						return false;
				},
				messages: {
					required: mes_required,
					number: jQuery.format("{{trans('shippingTemplates.number_validation')}}"),
					min: jQuery.validator.format("{{trans('shippingTemplates.min_validation')}} {0}"),
					max: jQuery.validator.format("{{trans('shippingTemplates.max_validation')}} {0}")
				}
			});
		});
		$('[name="additional_weight_added_'+updated_val+'"]').each(function (element) {
			$(this).rules('add', {

				required: function(element){
					return is_custom_weight_required();
				},
				number: true,
				min:1,
	            messages: {
	                required: mes_required,
					number: jQuery.format("{{trans('shippingTemplates.number_validation')}}"),
					min: jQuery.validator.format("{{trans('shippingTemplates.min_validation')}} {0}")
				}
			});
		});



	});

	function is_custom_quantity_required()
	{
		if($('#new_group').val() != '1')
			return false;
		var shipping_setting = $('input[name=shipping_setting]:checked').val();
		if(shipping_setting == 'ship_to')
		{
			var fee_type = $('select[name=fee_type]').val();
			var custom_fee_type = $('select[name=custom_fee_type]').val();
			if(fee_type==1 && custom_fee_type==1)
				return true;
			else
				return false;
		}
		else
			return false;
	}
	function is_custom_weight_required()
	{
		if($('#new_group').val() != '1')
			return false;
		var shipping_setting = $('input[name=shipping_setting]:checked').val();
		if(shipping_setting == 'ship_to')
		{
			var fee_type = $('select[name=fee_type]').val();
			var custom_fee_type = $('select[name=custom_fee_type]').val();
			if(fee_type==1 && custom_fee_type==2)
				return true;
			else
				return false;
		}
		else
			return false;
	}
	function is_other_countries_required()
	{
		
		if($('#new_group').val() == '1')
			return false;
		else
			return true;
	}
	function is_custom_standard_required()
	{
		if($('#new_group').val() != '1')
			return false;
		var shipping_setting = $('input[name=shipping_setting]:checked').val();
		if(shipping_setting == 'ship_to')
		{
			var fee_type = $('select[name=fee_type]').val();
			if(fee_type==2)
				return true;
			else
				return false;
		}
		else
			return false;
	}
	function weight_to_rules(element)
	{
		if($('#new_group').val() != '1')
			return false;
		var id = $(element).attr('id');
		var index = id.split('weight_to_')[1];
		index = parseInt(index);

		if($('#weight_from_'+index).val()!='')
		{
			var wf = parseFloat($('#weight_from_'+index).val());
			return wf+1;
		}
		else
		{
			return false;
		}
	}
	function additional_weight_rules(element)
	{
		if($('#new_group').val() != '1')
			return false;
		var id = $(element).attr('id');
		var index = id.split('additional_weight_')[1];
		index = parseInt(index);

		if(($('#weight_from_'+index).val()!= '') && ($('#weight_to_'+index).val()!= ''))
		{
			var wf = parseFloat($('#weight_from_'+index).val());
			var wt = parseFloat($('#weight_to_'+index).val());
			return wt-wf;
		}
		else
			return false;
	}

	$("#shippingTemplateFrm").validate({
				ignore: ":disabled",
				onsubmit: false,
				rules: {
					geo_location: {
						required: function(element) {
							if($('#new_group').val() != '1')
								return false;
							return !$('#js-zone').prop('checked');
						  },
					},
					zone: {
						required: function(element) {
							if($('#new_group').val() != '1')
								return false;
							return !$('#js-geo-location').prop('checked');
						  },
					},

					'geo_locations[]': {
						required: function(element){
							if($('#new_group').val() != '1')
								return false;
							if($('#js-geo-location').prop('checked'))
							{
								var checked_num = $('input[name="geo_countries[]"]:checked').length;
								if ($('input[name="geo_countries[]"]:checked').length > 0)
									return false;
								else
									return true;
							}
							else
								return false;
						}
					},
					'zone_countries[]': {
						required: function(element){
							if($('#new_group').val() != '1')
								return false;
							return $('#js-zone').prop('checked');
						}
					},
					'shipping_setting': {
						required: function(element){
							if($('#new_group').val() != '1')
								return false;
							return true;
						}
					},
					'discount': {
						required: function(element){
							return is_custom_standard_required();
						},
						number: true,
						max: 99,
						min:0
					},
					'min_order': {
						required: function(element){
							return is_custom_quantity_required();
						},
						number: true,
						min:1
					},
					'max_order': {
						required: function(element){
							return is_custom_quantity_required();
						},
						number: true,
						min: function(element){
							if($('#min_order').val() != '')
								return $('#min_order').val();
							else
								return 2;
						},
					},
					'cost_base_weight': {
						required: function(element){
							return is_custom_quantity_required();
						},
						number: true,
						min:1
					},
					'extra_units': {
						required: function(element){
							if($('#extra_costs').val() != '')
								return true;
							else
								return false;
						},
						number: true,
						min:1
					},
					'extra_costs': {
						required: function(element){
							if($('#extra_units').val() != '')
								return true;
							else
								return false;
						},
						number: true,
						min:1
					},
					'initial_weight': {
						required: function(element){
							return is_custom_weight_required();
						},
						number: true
					},
					'initial_weight_price': {
						required: function(element){
							return is_custom_weight_required();
						},
						number: true
					},
					'weight_from_1': {
						required: function(element){
							if(is_custom_weight_required() && $('#weight_from_1').val()!='')
								return true;
							else
								return false;
						},
						number: true,
						equalTo:'#initial_weight'
					},
					'weight_to_1': {
						required: function(element){
							if(is_custom_weight_required() && $('#weight_to_1').val()!='')
								return true;
							else
								return false;
						},
						number: true,
						min:function(element){
							if($('#weight_from_1').val() != '')
							{
								var wf = parseFloat($('#weight_from_1').val());
								return wf+1;
							}
							else
								return false;
						}
					},
					'additional_weight_1': {
						required: function(element){
							if(is_custom_weight_required() && $('#weight_to_1').val()!='')
								return true;
							else
								return false;
						},
						number: true,
						min:1,
						max:function(element){
							if(($('#weight_from_1').val() != '') && ($('#weight_to_1').val() != ''))
							{
								var wf = parseFloat($('#weight_from_1').val());
								var wt = parseFloat($('#weight_to_1').val());
								return wt-wf;
							}
							else
								return false;
						}
					},
					'additional_weight_added_1': {
						required: function(element){
							if(is_custom_weight_required() && $('#weight_to_1').val()!='')
								return true;
							else
								return false;
						},
						number: true,
						min:1
					},
					'other_discount': {
						required: function(element){
							return is_other_countries_required();
						},
						number: true,
						max: 99,
						min:0
					},
					/*'other_discount': {
						required: function(element){
							
							//if($('#new_group').val() == '1')
//								return false;
	//						else
								return true;
						},
						number: true,
						max:99,
						min:0
					}*/
				},
				messages: {
					geo_location: {
						required: mes_required
					},
					zone: {
						required: mes_required
					},
					'geo_locations[]': {
						required: mes_required
					},
					'zone_countries[]': {
						required: mes_required
					},
					'shipping_setting': {
						required: mes_required
					},
					'discount':{
						required: mes_required,
						number: jQuery.format("{{trans('shippingTemplates.number_validation')}}"),
						min: jQuery.validator.format("{{trans('shippingTemplates.min_validation')}} {0}"),
						max: jQuery.validator.format("{{trans('shippingTemplates.max_validation')}} {0}")
					},
					'min_order': {
						required: mes_required,
						number: jQuery.format("{{trans('shippingTemplates.number_validation')}}"),
						min: jQuery.validator.format("{{trans('shippingTemplates.min_validation')}} {0}")
					},
					'max_order': {
						required: mes_required,
						number: jQuery.format("{{trans('shippingTemplates.number_validation')}}"),
						min: jQuery.validator.format("{{trans('shippingTemplates.min_validation')}} {0}")
					},
					'cost_base_weight': {
						required: mes_required,
						number: jQuery.format("{{trans('shippingTemplates.number_validation')}}"),
						min: jQuery.validator.format("{{trans('shippingTemplates.min_validation')}} {0}")
					},
					'extra_units': {
						required: mes_required,
						number: jQuery.format("{{trans('shippingTemplates.number_validation')}}"),
						min: jQuery.validator.format("{{trans('shippingTemplates.min_validation')}} {0}")
					},
					'extra_costs': {
						required: mes_required,
						number: jQuery.format("{{trans('shippingTemplates.number_validation')}}"),
						min: jQuery.validator.format("{{trans('shippingTemplates.min_validation')}} {0}")
					},
					'initial_weight': {
						required: mes_required,
						number: jQuery.format("{{trans('shippingTemplates.number_validation')}}"),
					},
					'initial_weight_price': {
						required: mes_required,
						number: jQuery.format("{{trans('shippingTemplates.number_validation')}}"),
					},
					'weight_from_1': {
						required: mes_required,
						number: jQuery.format("{{trans('shippingTemplates.number_validation')}}"),
						equalTo: jQuery.validator.format("{{trans('shippingTemplates.equal_to_validation')}} {0}")
					},
					'weight_to_1': {
						required: mes_required,
						number: jQuery.format("{{trans('shippingTemplates.number_validation')}}"),
					},
					'additional_weight_1': {
						required: mes_required,
						number: jQuery.format("{{trans('shippingTemplates.number_validation')}}"),
						min: jQuery.validator.format("{{trans('shippingTemplates.min_validation')}} {0}"),
						max: jQuery.validator.format("{{trans('shippingTemplates.max_validation')}} {0}")
					},
					'additional_weight_added_1': {
						required: mes_required,
						number: jQuery.format("{{trans('shippingTemplates.number_validation')}}"),
						min: jQuery.validator.format("{{trans('shippingTemplates.min_validation')}} {0}"),
					},
					'other_discount': {
						required: mes_required,
						number: jQuery.format("{{trans('shippingTemplates.number_validation')}}"),
						min: jQuery.validator.format("{{trans('shippingTemplates.min_validation')}} {0}"),
						max: jQuery.validator.format("{{trans('shippingTemplates.max_validation')}} {0}")
					},
				}
			});



	$("#confirm_shipping").click(function()
	{
		if($('#new_group').val() == '1')
			if($("#shippingTemplateFrm").valid())
			{
				parent.displayLoadingImage(true);
				return true;
			}
			else
				return false;
		else
		{
			parent.displayLoadingImage(true);
			return true;
		}
	});
	$('#cancel_shipping').click(function(){
		parent.$.fancybox.close();
	});
	$('#save_shipping').click(function(){
		//return true;

		if($("#shippingTemplateFrm").valid())
		{
			parent.displayLoadingImage(true);
			return true;
		}
		return false;
		//return $("#shippingTemplateFrm").valid();
	});
	/*
	$("#save_shipping").click(function() {
		if($('#new_group').val() == '1')
			return $("#shippingTemplateFrm").valid();
		else
			return true;
	});*/

	$(window).load(function(){
		  $(".fn_dialog_confirm").click(function(){
				var atag_href = $(this).attr("href");
				var action = $(this).attr("action");
				var cmsg = "";
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
				bootbox.dialog({
					message: cmsg,
					title: cfg_site_name,
					buttons: {
						danger: {
							label: "{{trans('common.ok')}}",
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

	//Change Group Name Confirm
	var post_url = "{{ URL::to('admin/users/change-group-name') }}";
	var page = $('#page').val();
	//alert(page);
	$(window).load(function(){
		  $(".change_group_name_confirm").click(function(){
				var cmsg ="";
				if ($('.checkbox_class:checked').length <= 0) {
					alert("Select the Checkbox");
					return false;
				}
				if ($('.group_name_class').val() =='' ) {
					alert("Select for the Group Name");
					return false;
				}
				cmsg ="Are you sure want to Change Group Name for the selected User?";
				var val = [];
				$(':checkbox:checked').each(function(i){
					val[i] = $(this).attr('id');
				});
				var selected_checkbox_id = val.join(',');
				var selected_group_name_id = $('.group_name_class').val();
				//alert(selected_group_name_id);
				bootbox.dialog({
					message: cmsg,
					title: cfg_site_name,
					buttons: {
						danger: {
							label: "Ok",
							className: "btn-danger",
							callback: function() {
								var post_data = 'selected_checkbox_id='+selected_checkbox_id+'&selected_group_name_id='+selected_group_name_id;
								$.ajax({
									type: 'POST',
									url: post_url,
									data: post_data,
									success: function(data){
										window.location.replace("{{ URL::to('admin').'?page='}}"+page);
										bootbox.hideAll();
									}
								});
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

		function removeFeeCustomShippingTemplate(fee_custom_id) {
			$("#dialog-delete-confirm").dialog({ title: cfg_site_name, modal: true,
				buttons: {
					"{{ trans('common.yes') }}": function() {
						postData = 'action=delete_fee_custom&fee_custom_id=' + fee_custom_id,
						displayLoadingImage(true);
						$.post(template_actions_url, postData,  function(response)
						{
							hideLoadingImage (false);
							data = eval( '(' +  response + ')');
							if(data.result == 'success') {
								$('#feeCustomRow_' + fee_custom_id).remove();
								//To re-order the numbers
								var ord_no =1;
								$('.js-fee-custom-divs').each(function(){
									$(this).find('.js-fee-custom-orderdiv').html(ord_no);
									ord_no++;
								})


								//Enable the countries which are deleted for the delivery address
								var deleted_countries = data.deleted_countries;

								$('input[name="geo_countries[]"]').each(function(){
									var val = parseInt($(this).val());
									if ($.inArray(val, deleted_countries) != -1)
									{
										$(this).prop('checked',false);
										$(this).prop('disabled',false)
										$(this).parent().removeClass('checked');
										$(this).closest("div").addClass('disabled');
									}
								});

								$('input[name="zone_countries[]"]').each(function(){
									var val = parseInt($(this).val());
									if ($.inArray(val, deleted_countries) != -1)
									{
										$(this).prop('checked',false);
										$(this).prop('disabled',false)
										$(this).parent().removeClass('checked');
										$(this).closest("div").addClass('disabled');
									}
								});

								$('.js-choose-geo-country').each(function() {
										var id = $(this).val();
										var checkbox_class = 'geo_contry_'+id;


										if($('.'+checkbox_class+':disabled').length == $('.'+checkbox_class).length)
											$(this).prop('disabled',true);
										else
											$(this).prop('disabled',false);

										if($('.'+checkbox_class+':checked').length == $('.'+checkbox_class).length)
											$(this).prop('checked',true);
										else
											$(this).prop('checked',false);

									});

									$('.js-choose-zone-country').each(function() {
										var id = $(this).val();
										var checkbox_class = 'zone_contry_'+id;
										if($('.'+checkbox_class+':disabled').length == $('.'+checkbox_class).length)
											$(this).prop('disabled',true);
										else
											$(this).prop('disabled',false);

										if($('.'+checkbox_class+':checked').length == $('.'+checkbox_class).length)
											$(this).prop('checked',true);
										else
											$(this).prop('checked',false);

									});

									//make as add if all template deleted already
									if($('.js-fee-custom-divs').length <= 0)
									{
										if($('.js-open-newgroup').length > 0)
										{
											if($('#js-new-group-countries').css('display') == 'block')
											{
												$('#js-new-group-countries').hide();
											}
											$('.js-open-newgroup').trigger('click');
											$('.js-open-newgroup').hide();
											$('#cancel_shipping').html('<i class="fa fa-cancel"></i> {{ trans("admin/shippingTemplates.close") }}');
										}
									}


								showSuccessMessage({success_message: data.success_message})
								removeErrorDialog();
							}
							else {
								showErrorDialog({status: 'error', error_message: data.error_msg});//'{{ trans("product.not_completed") }}'
							}
						});
						$(this).dialog("close");
					},
					"{{ trans('common.no') }}": function() {
						$(this).dialog("close");
					}
				}
			});
		}

		function editFeeCustomShippingTemplate(fee_custom_id)
		{
			postData = 'action=get_fee_custom&fee_custom_id=' + fee_custom_id,
			displayLoadingImage(true);
			$.post(template_actions_url, postData,  function(response)
			{
				hideLoadingImage (false);
				data = eval( '(' +  response + ')');
				
				var fee_custom_details = data.fee_custom_details;
				
				var prev_selected_countries = data.prev_selected_countries;
				var template_countries = fee_custom_details.countries;
				

				$('#fee_custom_id').val(fee_custom_id);

				//Reset the checked checkboxex
				$('input[name="geo_locations[]"]').each(function(){
					$(this).prop('checked',false);
					$(this).prop('disabled',false)
					$(this).parent().removeClass('checked');
					$(this).closest("div").removeClass('disabled');
				});
				$('input[name="zones[]"]').each(function(){
					$(this).prop('checked',false);
					$(this).prop('disabled',false)
					$(this).parent().removeClass('checked');
					$(this).closest("div").removeClass('disabled');
				});
				$('input[name="geo_countries[]"]').each(function(){
					$(this).prop('checked',false);
					$(this).prop('disabled',false)
					$(this).parent().removeClass('checked');
					$(this).closest("div").removeClass('disabled');
				});
				$('input[name="zone_countries[]"]').each(function(){
					$(this).prop('checked',false);
					$(this).prop('disabled',false)
					$(this).parent().removeClass('checked');
					$(this).closest("div").removeClass('disabled');
				});
				$('.js-selection-type').each(function(){
					$(this).prop('checked',false);
					$(this).prop('disabled',false)
					$(this).parent().removeClass('checked');
					$(this).closest("div").removeClass('disabled');
				});
				$('#min_order').val('');
				$('#max_order').val('');
				$('#cost_base_weight').val('');
				$('#extra_units').val('');
				$('#extra_costs').val('');

				if($('.js-open-newgroup').length > 0)
				{
					if($('#js-new-group-countries').css('display') == 'block')
					{
						$('#js-new-group-countries').hide();
					}
					$('.js-open-newgroup').trigger('click');
					$('.js-open-newgroup').hide();
					$('#cancel_shipping').html('<i class="fa fa-cancel"></i> {{ trans("admin/shippingTemplates.close") }}');
				}

				//disable the checkbox for other custom template coutries
				$('input[name="geo_countries[]"]').each(function(){
					var val = parseInt($(this).val());
					if ($.inArray(val, prev_selected_countries) != -1)
					{
						$(this).prop('checked',false);
						$(this).prop('disabled',true)
						$(this).parent().removeClass('checked');
						$(this).closest("div").addClass('disabled');
					}
				});

				$('input[name="zone_countries[]"]').each(function(){
					var val = parseInt($(this).val());
					if ($.inArray(val, prev_selected_countries) != -1)
					{
						$(this).prop('checked',false);
						$(this).prop('disabled',true);
						$(this).parent().removeClass('checked');
						$(this).closest("div").addClass('disabled');
					}
				});

				//Select the checkbox which are available for the current custom templsate
				$('input[name="geo_countries[]"]').each(function(){
					var val = parseInt($(this).val());
					if ($.inArray(val, template_countries) != -1)
					{
						$(this).prop('checked',true);
						$(this).prop('disabled',false)
						$(this).parent().addClass('checked');
						$(this).closest("div").removeClass('disabled');
					}
				});

				$('input[name="zone_countries[]"]').each(function(){
					var val = parseInt($(this).val());
					if ($.inArray(val, template_countries) != -1)
					{
						$(this).prop('checked',true);
						$(this).prop('disabled',false)
						$(this).parent().addClass('checked');
						$(this).closest("div").removeClass('disabled');
					}
				});

				//Show the default selection of countries
				var country_selected_type = fee_custom_details.country_selected_type;
				var geo_div_id = 'geo_location_content';
				var zone_div_id = 'zone_content';
				if(country_selected_type == '')
				{
					country_selected_type = 'geo_location'
				}
				if(country_selected_type == 'geo_location')
				{
					$('#js-geo-location').prop('checked',true);
					$('#js-geo-location').attr('checked', 'checked');
					$('#'+geo_div_id).show();
					$('#js-zone').removeAttr('checked');
					$('#'+zone_div_id).hide();
					$('#js-geo-location').parent().addClass('checked');
					$('#js-zone').parent().removeClass('checked');



					$('.js-choose-geo-country').each(function() {
						var id = $(this).val();
						var checkbox_class = 'geo_contry_'+id;
						var div_id = 'countries_'+id;
						if($('.'+checkbox_class+':checked').length == $('.'+checkbox_class + ':not(:disabled)').length)
						{
							$(this).prop('checked',true);
							$("#"+div_id).hide();
						}
						else
						{
							if($('.'+checkbox_class+':checked').length > 0)
							{
								$("#"+div_id).show();
							}
						}
						if($('.'+checkbox_class+':disabled').length == $('.'+checkbox_class).length)
						{
							$(this).prop('disabled',true)
						}
					});

					$('.js-choose-zone-country').each(function() {
						var id = $(this).val();
						var checkbox_class = 'zone_contry_'+id;
						if($('.'+checkbox_class+':checked').length == $('.'+checkbox_class + ':not(:disabled)').length)
						{
							$(this).prop('checked',true)
						}
						if($('.'+checkbox_class+':disabled').length == $('.'+checkbox_class).length)
						{
							$(this).prop('disabled',true)
						}
						var div_id = 'zone_countries_'+$(this).val();
						$("#"+div_id).hide();
					});

				}
				else
				{
					$('#js-zone').prop('checked',true);
					$('#js-zone').attr('checked', 'checked');
					$('#js-zone').parent().addClass('checked');
					$('#'+zone_div_id).show();
					$('#js-geo-location').removeAttr('checked');
					$('#js-geo-location').parent().removeClass('checked');
					$('#'+geo_div_id).hide();


					$('.js-choose-zone-country').each(function() {
						var id = $(this).val();
						var checkbox_class = 'zone_contry_'+id;
						var div_id = 'zone_countries_'+id;
						
						if($('.'+checkbox_class+':checked').length == $('.'+checkbox_class+ ':not(:disabled)').length)
						{
							$(this).prop('checked',true);
							$("#"+div_id).hide();
						}
						else
						{
							if($('.'+checkbox_class+':checked').length > 0)
							{
								$("#"+div_id).show();
							}
						}
						if($('.'+checkbox_class+':disabled').length == $('.'+checkbox_class).length)
						{
							$(this).prop('disabled',true)
						}
					});


					$('.js-choose-geo-country').each(function() {
						var id = $(this).val();
						var checkbox_class = 'geo_contry_'+id;
						if($('.'+checkbox_class+':checked').length == $('.'+checkbox_class+ ':not(:disabled)').length)
						{
							$(this).prop('checked',true)
						}
						if($('.'+checkbox_class+':disabled').length == $('.'+checkbox_class).length)
						{
							$(this).prop('disabled',true)
						}
						var div_id = 'countries_'+$(this).val();
						$("#"+div_id).hide();
					});

				}

				//show the shipping setting
				var shipping_setting = fee_custom_details.shipping_setting;
				if(shipping_setting == 'ship_to')
				{
					$('#shipping_to').prop('checked', true);
					$('#shipping_to').attr('checked', 'checked');
					$('#donot_shipto').prop('checked', false);
					$('#donot_shipto').removeAttr('checked');
					$('#shipping_to').parent().addClass('checked');
					$('#donot_shipto').parent().removeClass('checked');
					$('#shipto').show();
					$('#donotshipto').hide();
				}
				else
				{
					$('#shipping_to').prop('checked',false);
					$('#shipping_to').removeAttr('checked');
					$('#donot_shipto').prop('checked', true);
					$('#donot_shipto').attr('checked', 'checked');
					$('#shipping_to').parent().removeClass('checked');
					$('#donot_shipto').parent().addClass('checked');
					$('#shipto').hide();
					$('#donotshipto').show();
				}

				//Select the fee type.
				var fee_type = fee_custom_details.fee_type;
				$('.js-fee-type').val(fee_type);
				var valu = fee_type;
				if(valu==1)
				{

					$('#shipping_custom').show();
					$('#shipping_custom_fee').show();
					$('#shipping_standard').hide();
					$('#shipping_free').hide();
					$('#js-discount').val('0');

					var custom_fee_type = fee_custom_details.custom_fee_type;


					$('#shipping_custom').val(custom_fee_type);

					
					if(custom_fee_type==1)
					{
						$('#shipping_custom_fee_qty').show();
						$('#shipping_custom_fee_weight').hide();

						$('#min_order').val(fee_custom_details.min_order);
						$('#max_order').val(fee_custom_details.max_order);
						$('#cost_base_weight').val(fee_custom_details.cost_base_weight);
						$('#extra_units').val(fee_custom_details.extra_units);
						$('#extra_costs').val(fee_custom_details.extra_costs);

					}
					else
					{
						$('#shipping_custom_fee_qty').hide();
						$('#shipping_custom_fee_weight').show();
						$('#initial_weight').val(fee_custom_details.initial_weight);
						$('#initial_weight_price').val(fee_custom_details.initial_weight_price);

						var weight_form = $('#weight_addition_1').clone();
						var custom_weight_details = fee_custom_details.custom_weight_details;
						var custom_weight_count = fee_custom_details.custom_weight_count;
						//First custom weight record value
						$('#weight_from_1').val(custom_weight_details[0].weight_from);
						$('#weight_to_1').val(custom_weight_details[0].weight_to);
						$('#additional_weight_1').val(custom_weight_details[0].additional_weight);
						$('#additional_weight_added_1').val(custom_weight_details[0].additional_weight_price);


						if(custom_weight_count > 1)
						{
							for(i=2;i<=custom_weight_count;i++)
							{
								var weight_form = $('#weight_addition_1').clone();
								var last_val = i-1;
								var index = i-1;
								var updated_val = i;
								var current_div_id = 'weight_addition_'+updated_val;
								weight_form.attr('id', current_div_id);
								last_div = 'weight_addition_'+last_val;

								$('#'+last_div).after(weight_form);

								$('#'+current_div_id).find('#weight_to_1').attr('name','weight_to_'+updated_val);
								$('#'+current_div_id).find('#weight_to_1').val(custom_weight_details[index].weight_to);
								$('#'+current_div_id).find('#weight_to_1').attr('id','weight_to_'+updated_val);
								//label
								$('#'+current_div_id).find('#weight_to_error_1').attr('for','weight_to_'+updated_val);
								$('#'+current_div_id).find('#weight_to_error_1').attr('id','weight_to_error_'+updated_val);

								$('#'+current_div_id).find('#weight_from_1').attr('name','weight_from_'+updated_val);
								$('#'+current_div_id).find('#weight_from_1').val(custom_weight_details[index].weight_from);
								$('#'+current_div_id).find('#weight_from_1').attr('id','weight_from_'+updated_val);
								//label
								$('#'+current_div_id).find('#weight_from_error_1').attr('for','weight_from_'+updated_val);
								$('#'+current_div_id).find('#weight_from_error_1').attr('id','weight_from_error_'+updated_val);

								$('#'+current_div_id).find('#additional_weight_1').attr('name','additional_weight_'+updated_val);
								$('#'+current_div_id).find('#additional_weight_1').val(custom_weight_details[index].additional_weight);
								$('#'+current_div_id).find('#additional_weight_1').attr('id','additional_weight_'+updated_val);
								//label
								$('#'+current_div_id).find('#additional_weight_error_1').attr('for','additional_weight_'+updated_val);
								$('#'+current_div_id).find('#additional_weight_error_1').attr('id','additional_weight_error_'+updated_val);

								$('#'+current_div_id).find('#additional_weight_added_1').attr('name','additional_weight_added_'+updated_val);
								$('#'+current_div_id).find('#additional_weight_added_1').val(custom_weight_details[index].additional_weight_price);
								$('#'+current_div_id).find('#additional_weight_added_1').attr('id','additional_weight_added_'+updated_val);
								//label
								$('#'+current_div_id).find('#additional_weight_added_error_1').attr('for','additional_weight_added_'+updated_val);
								$('#'+current_div_id).find('#additional_weight_added_error_1').attr('id','additional_weight_added_error_'+updated_val);

								$('#'+current_div_id).append('<a onclick="removeWeightRange('+updated_val+')" class="pos-abs"><i class="fa fa-times-circle"></i></a>');




								//Add validation rules to the form
								$('[name="weight_from_'+updated_val+'"]').each(function (element) {
									$(this).rules('add', {
										required: function(element){
												return is_custom_weight_required();
										},
										number: true,
										equalTo:'#weight_to_'+last_val,
										messages: {
											required: mes_required
										}
									});
								});

								$('[name="weight_to_'+updated_val+'"]').each(function (element) {
									$(this).rules('add', {
										required: function(element){
														return is_custom_weight_required();
													},
										number: true,
										min:function(e){
													return weight_to_rules(e);
												},
										messages: {
											required: mes_required
										}
									});
								});

								$('[name="additional_weight_'+updated_val+'"]').each(function (element) {
									$(this).rules('add', {
										required: function(element){
												return is_custom_weight_required();
										},
										number: true,
										min:1,
										max:function(e){
											return additional_weight_rules(e);
										},
										messages: {
											required: mes_required
										}
									});
								});
								$('[name="additional_weight_added_'+updated_val+'"]').each(function (element) {
									$(this).rules('add', {

										required: function(element){
											return is_custom_weight_required();
										},
										number: true,
										min:1,
										messages: {
											required: mes_required
										}
									});
								});


							}
							$('#current_added_group').val(custom_weight_count);
						}
					}
				}
				else if(valu==2)
				{
					$('#shipping_custom').hide();
					$('#shipping_custom_fee').hide();
					$('#shipping_standard').show();
					$('#shipping_free').hide();
					$('#js-discount').val(fee_custom_details.discount);
				}
				else if(valu==3)
				{
					$('#shipping_custom').hide();
					$('#shipping_custom_fee').hide();
					$('#shipping_standard').hide();
					$('#shipping_free').show();
					$('#js-discount').val('0');
				}

				//hide other shipping template to prevent delete
				$(".js-fee-custom-divs:not(#feeCustomRow_"+fee_custom_id+")").hide();
				$("#delete_"+fee_custom_id).addClass('anc-disabled');


				//$('input[name="geo_countries[]"]').prop('diaabled', false);

				
				/*if(data.result == 'success') {
					$('#feeCustomRow_' + fee_custom_id).remove();
					//To re-order the numbers
					var ord_no =1;
					$('.js-fee-custom-divs').each(function(){
						$(this).find('.js-fee-custom-orderdiv').html(ord_no);
						ord_no++;
					})
					showSuccessMessage({success_message: data.success_message})
					removeErrorDialog();
				}
				else {
					showErrorDialog({status: 'error', error_message: data.error_msg});//'{{ trans("product.not_completed") }}'
				}*/
			});
		}
</script>
@stop