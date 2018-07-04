<tbody>
    @if(count($companies) > 0)
        @foreach($companies as $company)
            <?php
                $company_block = 'company_block_'.$company->id;
                $company_name = 'company_'.$company->id;
                $fee_type_name = 'fee_type_'.$company->id;
                $discount_name = 'discount_'.$company->id;
                $discount_group = 'discount_group_'.$company->id;
                $delivery_type_name = 'delivery_type_'.$company->id;
                $delivery_days_name = 'delivery_days_'.$company->id;
                $delivery_group = 'delivery_group_'.$company->id;
                $error_field_name = 'error_field_'.$company->id;
                //Assign values
                $discount = 0;
                $delivery_days = ($company->default_delivery_days)?$company->default_delivery_days:'';
                $is_checked = '';
                $is_checked_fee_type = 2;
                $is_checked_delivery_type = 2;
                if(in_array($company->id, $checked_companies)) {
                    $is_checked = "checked='checked'";
                    $is_checked_fee_type = isset($shipping_template_details[$fee_type_name]) ? $shipping_template_details[$fee_type_name] : 2;
                    $is_checked_delivery_type = isset($shipping_template_details[$delivery_type_name]) ? $shipping_template_details[$delivery_type_name] : 2;
                    $delivery_days = isset($shipping_template_details[$delivery_days_name]) ? $shipping_template_details[$delivery_days_name] : $delivery_days;
                    $discount = isset($shipping_template_details[$discount_name]) ? $shipping_template_details[$discount_name] : $discount;
                }
            ?>
            <tr class="clsShippingCompany {{ $tabname }}" id="{{ $company_block }}">
                <td class="checkbox-list">
                    <label>
                        <input type="checkbox" name="companies[]" {{ $is_checked }} id="{{ $company_name }}" value="{{ $company->id }}" />
                        <label for="{{ $company_name }}">{{$company->company_name}}</label>
                    </label>
                </td>
                <td class="col-md-6">
                    <div>
                        <ul class="list-unstyled shipping-list clearfix">
                            <li><label for="{{ $fee_type_name.'_standard' }}" class="control-label">{{ Lang::get('shippingTemplates.shipping_cost') }}: </label></li>
                            @if($company->is_standard_fee_available)
                                <li class="radio-list">
                                    <label class="control-label">
                                        <input type="radio" name="{{ $fee_type_name }}" id="{{ $fee_type_name.'_standard' }}" value="2" @if($is_checked_fee_type == 2) checked="checked" @endif />
                                        <label for="{{ $fee_type_name.'_standard' }}">{{Lang::get('shippingTemplates.standard')}} </label>
                                    </label>
                                    <a href="#" data-toggle="tooltip" data-original-title="{{Lang::get('shippingTemplates.standard_fee_tooltip')}}"><i class="fa fa-question-circle"></i></a>
                                </li>
                                <li id="{{ $discount_group }}">
                                    {{ Form::label($discount_name, trans("admin/shippingTemplates.discount"), array('class' => 'control-label pull-left mr10')) }}
                                    <div class="input-group pull-left">
                                        {{ Form::text($discount_name, $discount, array('class' => 'form-control')) }}
                                        <span class="input-group-addon">%</span>
                                    </div>
                                    <a href="#" data-toggle="tooltip" data-original-title="{{Lang::get('shippingTemplates.discount_for_standar_tooltip')}}"><i class="fa fa-question-circle"></i></a>
                                </li>
                            @endif
                        </ul>
                        <label class="error">{{{ $errors->first($fee_type_name) }}}</label>

                        <ul class="list-unstyled shipping-list clearfix mar0">
                            <li><label for="{{ $delivery_type_name.'_promised' }}" class="control-label">{{Lang::get('shippingTemplates.delivery_time')}}: </label></li>
                            <li>
                                <div class="radio-list">
                                    <label>
                                        <input type="radio" name="{{ $delivery_type_name }}" id="{{ $delivery_type_name.'_promised' }}" value="2"  @if($is_checked_delivery_type == 2) checked="checked" @endif/> <label for="{{ $delivery_type_name.'_promised' }}">{{ trans("admin/shippingTemplates.promised_time") }}</label>
                                    </label>
                                </div>
                                <label class="error">{{{ $errors->first($delivery_type_name) }}}</label>
                            </li>
                            <li id="{{ $delivery_group }}">
                                <div class="input-group">
                                    {{ Form::text($delivery_days_name, $delivery_days, array('id' => $delivery_days_name, 'class' => 'form-control')) }}
                                    <label for="{{$delivery_days_name}}" class="input-group-addon">{{Lang::get('shippingTemplates.days')}}</label>
                                </div>
                                <label class="error">{{{ $errors->first($delivery_days_name) }}}</label>
                            </li>
                        </ul>
                        <label class="error" style="display: none" id="{{ $error_field_name }}"></label>
                    </div>
                </td>
                <td class="radio-list">
                    <label>
                        <input type="radio" name="{{ $fee_type_name }}" {{ $is_checked_fee_type }} id="{{ $fee_type_name.'_free' }}" value="3" @if($is_checked_fee_type == 3) checked="checked" @endif /> <label for="{{ $fee_type_name.'_free' }}">{{ trans("admin/shippingTemplates.free") }}</label>
                    </label>
                    @if($company->is_custom_delivery_available)
                        <label>
                            <input type="radio" name="{{ $delivery_type_name }}" id="{{ $delivery_type_name.'_custom' }}" value="1" @if($is_checked_delivery_type == 1) checked="checked" @endif />
                            <label for="{{ $delivery_type_name.'_custom' }}">
                                @if(isset($id))
                                    <a onclick="openCustomDeliveryTimePopup('{{URL::action('AdminShippingTemplateController@getCustomDeliveryTime')}}', {{$company->id}})" href="javascript:;" data-id="{{$company->id}}">{{ trans("admin/shippingTemplates.customize_delivery_time") }}</a>
                                @else
                                    <a onclick="openCustomDeliveryTimePopup('{{URL::action('AdminShippingTemplateController@getCustomDeliveryTime')}}', {{$company->id}})" href="javascript:;" data-id="{{$company->id}}">{{ trans("admin/shippingTemplates.customize_delivery_time") }}</a>
                                @endif
                            </label>
                        </label>
                    @endif
                </td>
                <td class="radio-list">
                    @if($company->is_custom_fee_available)
                        <label>
                            <input type="radio" name="{{ $fee_type_name }}" {{ $is_checked_fee_type }} id="{{ $fee_type_name.'_custom' }}" value="1" @if($is_checked_fee_type == 1) checked="checked" @endif /><label for="{{ $fee_type_name.'_custom' }}"></label>
                            @if(isset($id))
                                <a onclick="openCustomShippingTemplatePopup('{{URL::action('AdminShippingTemplateController@getCustomShippingTemplate')}}', {{$company->id}})" href="javascript:;" data-id="{{$company->id}}">{{Lang::get('shippingTemplates.custom')}}</a>
                            @else
                                <a onclick="openCustomShippingTemplatePopup('{{URL::action('AdminShippingTemplateController@getCustomShippingTemplate')}}', {{$company->id}})"  rel="screenshots_group" href="javascript:;" class="fn_fancyboxview" data-id="{{$company->id}}">{{Lang::get('shippingTemplates.custom')}}</a>
                            @endif
                         </label>
                    @endif
                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="4"><p class="alert alert-info">{{Lang::get('shippingTemplates.no_shipping_for_category')}}</p></td>
        </tr>
    @endif
</tbody>