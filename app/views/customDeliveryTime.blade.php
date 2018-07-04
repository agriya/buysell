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
			parent.$.fancybox.close();
		@endif
	</script>
@stop

@section('content')
	<!-- Page title starts -->
    <h1>{{ Lang::get('shippingTemplates.custom_delivery_time') }}</h1>
	<!-- Page title end -->

    <div class="pop-content">
        <!-- Notifications Starts -->
        @include('notifications')
        <!-- Notifications End -->

        <div id="error_msg_div"></div>
        <div id="success_msg_div"></div>

            {{ Form::open(array('id'=>'shippingCustomDeliveryFrm', 'method'=>'post','class' => 'form-horizontal' )) }}
                <div class="custom-delivery">
                    @if(isset($delivery_custom_details) && count($delivery_custom_details) > 0)<?php //!$delivery_custom_details->IsEmpty() ?>
                        @foreach($delivery_custom_details as $key => $custom_details)
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
                                    {{trans('shippingTemplates.promised_delivery_time')}}:
                                    @if(isset($custom_details['days']))
                                        <strong>{{$custom_details['days']}}</strong>
                                    @endif {{trans('shippingTemplates.days')}}
                                </div>
                                <div class="col-sm-1 col-xs-2 note-btn">
                                   <a class="btn blue btn-xs" href="#" onclick="javascript:editDeliveryCustomShippingTemplate({{ $custom_details->id }});" title="{{ Lang::get('common.edit') }}"><i class="fa fa-edit"></i></a>
                                   <a href="javascript:void(0)" onclick="javascript:removeDeliveryCustomShippingTemplate({{ $custom_details->id }});" title="{{ Lang::get('common.edit') }}" class="btn btn-xs red"><i class="fa fa-trash-o"></i></a>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @if(isset($delivery_custom_details) && count($delivery_custom_details) > 0)
                        <div class="mb15">
                            <a href="javascript:void(0);" title="Add a new group" class="btn btn-xs purple-plum js-open-newgroup">
                            	<i class="fa fa-plus-circle"></i>  {{trans('shippingTemplates.add_new_group')}}
                            </a>
                        </div>
                    @endif

                    <div id="js-new-group-countries"  @if(isset($delivery_custom_details) && count($delivery_custom_details) > 0) style="display:none" @endif>
                        <div class="panel-group" id="country-selection">
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
                                <div id="geo_location_content" class="panel-collapse collapse in">
                                    <div class="panel-body">
										 <label class="error">{{{ $errors->first('geo_countries') }}}</label>
                                        <ul class="list-unstyled">
                                            @foreach($geo_countries_arr as $geo_location => $countries)
                                                <li class="mb10">
                                                    <?php
														$geo_name = Products::getGeoLocationName($geo_location);
                                                    ?>
                                                    <label class="error">{{{ $errors->first('geo_locations') }}}</label>
                                                    {{ Form::checkbox('geo_locations[]', $geo_location, null, array('id' => 'geo_locations_'.$geo_location, 'class' => 'js-choose-geo-country') ) }}
                                                    {{ Form::label('geo_locations_'.$geo_location, $geo_name, array()) }}
                                                    [<a href="javascript:;" class="js-disp-geo-country" id="{{$geo_location}}">{{trans('shippingTemplates.show_details')}}</a>]
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
                            <div class="text-center mb20"><strong>{{ trans('common.or') }}</strong></div>
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
                                <div id="zone_content" class="panel-collapse collapse mt-m7" style="display:none">
                                    <label class="error">{{{ $errors->first('zone_countries') }}}</label>
                                    <div class="panel-body">
                                        <ul class="list-unstyled">
                                            @foreach($zone_contries_arr as $zone => $countries)
                                                <li class="mb10">
                                                    <?php
                                                        $zone_name = 'Zone '.$zone;
                                                    ?>
                                                    <label class="error">{{{ $errors->first('geo_locations') }}}</label>
                                                    {{ Form::checkbox('zones[]', $zone, null, array('id' => 'zones_'.$zone,'class' => 'js-choose-zone-country') ) }}
                                                    {{ Form::label('zones_'.$zone, $zone_name, array()) }}
                                                    [<a href="javascript:;" class="js-disp-zone-country" id="{{$zone}}">{{trans('shippingTemplates.show_details')}}</a>]</label>
                                                    <div id="zone_countries_{{$zone}}" class="zonal-custdelvry margin-top-10" style="display:none">
                                                        <div class="ml15 clearfix">
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
                        <div class="form-group">
                            <div id="delivery_days" class="delv-days col-sm-12">
                            	<div class="clearfix well-new pad10">
                                    <strong class="pull-left">{{trans('shippingTemplates.set_promised_delivery_time_to')}}</strong>
                                    <div class="col-sm-1 col-xs-2">
                                        {{Form::text('days', Config::get('generalConfig.shipping_custom_delivery_time_days'), array('id'=>'days', 'class' => 'form-control'))}}
                                    </div>
                                    <strong class="pull-left">{{trans('shippingTemplates.days_for_country_above')}}</strong>
                                </div>
                                <label class="error">{{{ $errors->first('days') }}}</label>
                            </div>
                        </div>
                    </div>

                    @if(isset($delivery_custom_details) && count($delivery_custom_details) > 0)
                        <div class="form-group" id="other_countries_regions">
                        	<div class="clearfix ml15">
                            	<span class="text-danger pull-left fonts18">*</span>
                                <div class="form pull-left"><h2 class="form-section fonts18">{{trans('shippingTemplates.if_the_buyer_not_in_above_contries')}}</h2></div>
                            </div>
                            <div id="other_delivery_days" class="delv-days col-sm-12">
                                <?php
									$other_days = (isset($other_countries_details->days) && $other_countries_details->days > 0)?$other_countries_details->days: Config::get('generalConfig.shipping_custom_delivery_time_days');
								?>
                                <div class="clearfix well-new pad10">
                                	<strong class="pull-left">{{trans('shippingTemplates.set_promised_delivery_time_to')}}</strong>
                                    <div class="col-sm-1 col-xs-2">{{Form::text('other_days', Input::old('other_days')?Input::old('other_days'):$other_days, array('class' => 'js-fee-type', 'id' => 'other_days', 'class' => 'form-control'))}}</div>
                                    <strong class="pull-left">{{trans('shippingTemplates.days_for_country_above')}}</strong>
                                </div>
                                <label class="error">{{{ $errors->first('other_days') }}}</label>
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <div class="col-sm-12">
                            <?php $new_group = (isset($delivery_custom_details) && count($delivery_custom_details) > 0)?0:1;
                            ?>
                            {{Form::hidden('id',$id)}}
                            {{Form::hidden('company_id',$company_id)}}
                            {{Form::hidden('custom_delivery_id',0, array('id'=>'custom_delivery_id'))}}
                            {{Form::hidden('new_group',$new_group, array('id'=>'new_group'))}}
                            {{Form::hidden('template_name',Input::get('t_name'),array('id'=>'template_name')) }}

                            <button name="confirm_delivery" id="confirm_delivery" value="save_shipping" type="submit" class="btn green" @if(isset($delivery_custom_details) && count($delivery_custom_details) > 0) style ="display:none" @endif>
                                <i class="fa fa-check"></i> {{ trans("shippingTemplates.confirm") }}
                            </button>
                            <button name="cancel_delivery" id="cancel_delivery" value="save_shipping" type="submit" class="btn default" @if(isset($delivery_custom_details) && count($delivery_custom_details) > 0) style ="display:none" @endif>
                                <i class="fa fa-times"></i> {{ trans("shippingTemplates.cancel") }}
                            </button>
                            @if(isset($delivery_custom_details) && count($delivery_custom_details) > 0)
                                <button name="save_delivery" id="save_delivery" value="save_shipping" type="submit" class="btn green">
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
    </div>

	<script type="text/javascript">
		parent.hideLoadingImage (false);
        var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;

        var template_actions_url = '{{ URL::action('ShippingTemplateController@postShippingTemplateDeliveryCustomAction')}}';
        var default_delivery_days = '{{ Config::get('generalConfig.shipping_custom_delivery_time_days') }}';

        function showErrorMessage(err_msg) {
            var err_msg_div ='<div class="note note-danger">'+err_msg+'</div>';
            $('#error_msg_div').html(err_msg_div);
            var body = $("html, body");
            body.animate({scrollTop:0}, '500', 'swing', function() {
            });
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

        $('.js-choose-zone-country').each(function() {
            var id = $(this).val();
            var checkbox_class = 'zone_contry_'+id;
            if($('.'+checkbox_class+':disabled').length == $('.'+checkbox_class).length)
            {
                $(this).prop('disabled',true)
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
                    $('#js-zone').removeAttr('checked');
                    $('#'+geo_div_id).show();
                    $('#'+zone_div_id).hide();
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
                    $('#'+geo_div_id).hide();
                    $('#js-geo-location').prop('checked', false);
                    $('#js-geo-location').removeAttr('checked');
                    $('#js-geo-location').parent().removeClass('checked');
                }
                else
                {
                    $('#'+geo_div_id).show();
                    $('#'+zone_div_id).hide();
                    $('#js-geo-location').prop('checked', 'checked');
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
                $('#confirm_delivery').hide();
                $('#cancel_delivery').hide();
                $('#save_delivery').show();
                $('#other_countries_regions').show();
                $('#new_group').val(0);

            }
            else
            {
                $('#confirm_delivery').show();
                $('#cancel_delivery').show();
                $('#save_delivery').hide();
                $('#other_countries_regions').hide();
                $('#new_group').val(1);
            }
        });

        function is_custom_quantity_required()
        {
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

        $("#shippingCustomDeliveryFrm").validate({
                    ignore: ":disabled",
                    onsubmit: false,
                    rules: {
                        geo_location: {
                            required: function(element) {
                                return !$('#js-zone').prop('checked');
                              },
                        },
                        zone: {
                            required: function(element) {
                                return !$('#js-geo-location').prop('checked');
                              },
                        },
                        'geo_locations[]': {
                            required: function(element){
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
                                return $('#js-zone').prop('checked');
                            }
                        },
                        'days': {
                            required: true
                        },
                    },
                    messages: {
                        geo_location: {
                            required: mes_required,
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
                        'days': {
                            required: mes_required
                        },
                    }
                });

        $("#confirm_delivery").click(function() {
            //if($('#new_group').val() == '1')
            	//$("#shippingCustomDeliveryFrm").valid();
                if(validateCustomDeliveryForm())
				{
					parent.displayLoadingImage(true);
					return true
				}
				else
					return false;
            //else
                //return true;
        });
        $('#cancel_delivery').click(function(){
            parent.$.fancybox.close();
        });
        $('#save_delivery').click(function(){
            return validateCustomDeliveryForm();//return $("#shippingCustomDeliveryFrm").valid();
        });

        function validateCustomDeliveryForm()
        {
            
            if($('#new_group').val() == '1')
            {
                if(!$('#js-zone').prop('checked') && !$('#js-geo-location').prop('checked'))
                {
                    showErrorMessage("{{trans('shippingTemplates.select_country_selection_type')}}");
                    return false;
                }
                if($('#js-geo-location').prop('checked'))
                {
                    
                    var checked_num = parseInt($('input[name="geo_countries[]"]:checked').length);
                    
                    if(checked_num <= 0)
                    {
                        showErrorMessage("{{trans('shippingTemplates.you_havenot_choose_dest_country')}} ");
                        return false;
                    }
                }
                if($('#js-zone').prop('checked'))
                {
                    
                    var checked_num = parseInt($('input[name="zone_countries[]"]:checked').length);
                    
                    if(checked_num <= 0)
                    {
                        showErrorMessage("{{trans('shippingTemplates.you_havenot_choose_dest_country')}}");
                        return false;
                    }
                }
                if($('#days').val()=='')
                {
                    showErrorMessage("{{trans('shippingTemplates.enter_the_promised_delivery_time')}}");
                    return false;
                }
            }
            else {
                if($('#other_days').val()=='')
                {
                    showErrorMessage("{{trans('shippingTemplates.enter_the_promised_delivery_time')}}");
                    return false;
                }
            }
            return true;
        }

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

            function removeDeliveryCustomShippingTemplate(custom_delivery_id) {
                $("#dialog-delete-confirm").dialog({ title: cfg_site_name, modal: true,
                    buttons: {
                        "{{ trans('common.yes') }}": function() {
                            postData = 'action=delete_custom_delivery&custom_delivery_id=' + custom_delivery_id,
                            displayLoadingImage(true);
                            $.post(template_actions_url, postData,  function(response)
                            {
                                hideLoadingImage (false);
                                data = eval( '(' +  response + ')');
                                if(data.result == 'success') {
                                    $('#feeCustomRow_' + custom_delivery_id).remove();
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

            function editDeliveryCustomShippingTemplate(custom_delivery_id)
            {
                postData = 'action=get_custom_delivery&custom_delivery_id=' + custom_delivery_id,
                displayLoadingImage(true);
                $.post(template_actions_url, postData,  function(response)
                {
                    hideLoadingImage (false);
                    data = eval( '(' +  response + ')');
                    var custom_delivery_details = data.custom_delivery_details;
                    
                    var prev_selected_countries = data.prev_selected_countries;
                    var template_countries = custom_delivery_details.countries;
                    

                    //Reset the checked checkboxex
                    $('input[name="geo_locations[]"]').each(function(){
                        $(this).prop('checked',false);
                        $(this).parent().removeClass('checked');
                        $(this).prop('disabled',false)
                        $(this).closest("div").removeClass('disabled');
                    });
                    $('input[name="zones[]"]').each(function(){
                        $(this).prop('checked',false);
                        $(this).parent().removeClass('checked');
                        $(this).prop('disabled',false)
                        $(this).closest("div").removeClass('disabled');
                    });
                    $('input[name="geo_countries[]"]').each(function(){
                        $(this).prop('checked',false);
                        $(this).parent().removeClass('checked');
                        $(this).prop('disabled',false)
                        $(this).closest("div").removeClass('disabled');
                    });
                    $('input[name="zone_countries[]"]').each(function(){
                        $(this).prop('checked',false);
                        $(this).parent().removeClass('checked');
                        $(this).prop('disabled',false)
                        $(this).closest("div").removeClass('disabled');
                    });
                    $('#days').val('');

                    if($('.js-open-newgroup').length > 0)
                    {

                        if($('#js-new-group-countries').css('display') == 'block')
                        {
                            $('#js-new-group-countries').hide();
                        }
                        $('.js-open-newgroup').trigger('click');
                        $('.js-open-newgroup').hide();
                        $('#cancel_delivery').html('<i class="fa fa-times"></i> {{ trans("shippingTemplates.close") }}');
                    }

                    //disable the checkbox for other custom template coutries
                    $('input[name="geo_countries[]"]').each(function(){
                        var val = parseInt($(this).val());
                        if ($.inArray(val, prev_selected_countries) != -1)
                        {
                            $(this).prop('checked',false);
                            $(this).parent().removeClass('checked');
                            $(this).prop('disabled',true)
                            $(this).closest("div").addClass('disabled');
                        }
                    });

                    $('input[name="zone_countries[]"]').each(function(){
                        var val = parseInt($(this).val());
                        if ($.inArray(val, prev_selected_countries) != -1)
                        {
                            $(this).prop('checked',false);
                            $(this).parent().removeClass('checked');
                            $(this).prop('disabled',true)
                            $(this).closest("div").addClass('disabled');
                        }
                    });

                    //Select the checkbox which are available for the current custom templsate
                    $('input[name="geo_countries[]"]').each(function(){
                        var val = parseInt($(this).val());
                        if ($.inArray(val, template_countries) != -1)
                        {
                            $(this).prop('checked',true);
                            $(this).parent().addClass('checked');
                            $(this).prop('disabled',false)
                            $(this).closest("div").removeClass('disabled');
                        }
                    });

                    $('input[name="zone_countries[]"]').each(function(){
                        var val = parseInt($(this).val());
                        if ($.inArray(val, template_countries) != -1)
                        {
                            $(this).prop('checked',true);
                            $(this).parent().addClass('checked');
                            $(this).prop('disabled',false)
                            $(this).closest("div").removeClass('disabled');
                        }
                    });

                    //Show the default selection of countries
                    var country_selected_type = custom_delivery_details.country_selected_type;
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
                        $('#js-geo-location').parent().addClass('checked');
                        $('#'+geo_div_id).show();
                        $('#js-zone').removeAttr('checked');
                        $('#js-zone').prop('checked',false);
                        $('#js-zone').parent().removeClass('checked');
                        $('#'+zone_div_id).hide();



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
                            if($('.'+checkbox_class+':checked').length == $('.'+checkbox_class+ ':not(:disabled)').length)
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
                        $('#js-geo-location').prop('checked',false);
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
                            if($('.'+checkbox_class+':checked').length == $('.'+checkbox_class).length)
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

                    //Select the fee type.
                    var days = custom_delivery_details.days;
                    if(days == '' || days == 0 )
                        days = default_delivery_days;
                    $('#days').val(days);
                    $('#custom_delivery_id').val(custom_delivery_id);
                });
            }

            $(document).ready(function() {
                var specialKeys = new Array();
                specialKeys.push(8); //Backspace
                specialKeys.push(9); //tab
                specialKeys.push(13); //Enter
                specialKeys.push(46); //Delete
                if($("#days").length > 0) {
                    $("#days").on("keypress", "", function (e) {
                        var keyCode = e.which ? e.which : e.keyCode
                        var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeys.indexOf(keyCode) != -1);
                        var limit = 2;
                        if (keyCode == 8 || e.keyCode == 46) limit = 3;
                        ret = (ret && this.value.length < limit);
                        ret = (ret && !(e.keyCode == 0 && e.which == 46));
                        return ret;
                    });
                    $("#days").on("keyup", "", function (e) {
                        var days = $(this).val();
                        if (days!='' && days <= 0) {
                            $(this).val(default_delivery_days);
                            return false;
                        }
                    });
                    $("#days").on("blur", "", function (e) {
                        var days = $(this).val();
                        if (days=='') {
                            $(this).val(default_delivery_days);
                        }
                    });

                }

                if($("#other_days").length > 0) {
                    $("#other_days").on("keypress", function (e) {
                        var keyCode = e.which ? e.which : e.keyCode
                        var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeys.indexOf(keyCode) != -1);
                        var limit = 2;
                        if (keyCode == 8 || e.keyCode == 46) limit = 3;
                        ret = (ret && this.value.length < limit);
                        ret = (ret && !(e.keyCode == 0 && e.which == 46));
                        return ret;
                        
                    });
                    $("#other_days").on("keyup", function (e) {
                        var days = $(this).val();
                        if (days!='' && days <= 0) {
                            $(this).val(default_delivery_days);
                            return false;
                        }
                    });
                    $("#other_days").on("blur", "", function (e) {
                        var days = $(this).val();
                        if (days=='') {
                            $(this).val(default_delivery_days);
                        }
                    });
                }
            });
        </script>
@stop