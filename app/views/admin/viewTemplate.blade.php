@extends('admin')
@section('content')
    @if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('error_message'); ?>
    @endif
    @if(Session::has('success_message') && Session::get('success_message') != '')
        <div class="note note-success">{{ Session::get('success_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif
  	@if(sizeof($template_name) > 0 )
        <div class="mobilemenu mb30">
        	<!-- MOBILE TOGGLER STARTS -->
                <button class="btn btn-primary btn-sm mobilemenu-bar mb10"><i class="fa fa-chevron-down"></i> Menu</button>
            <!-- MOBILE TOGGLER END -->
            <div class="bs-example bs-example-tabs">
                <ul role="tablist" class="nav nav-tabs mbldropdown-menu ac-custom-tabs" id="myTab">
                    <li class="active"><a href="#shipping_cost_set" data-toggle="tab" role="tab" >{{trans('shippingTemplates.shipping_cost_set')}}</a></li>
                    <li><a href="#delivery_time_set" data-toggle="tab" role="tab">{{trans('shippingTemplates.delivery_time_set')}}</a></li>
               </ul>
            </div>
        </div>

        <div class="table-responsive">
            <div class="portlet box blue-hoki">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-list"></i>{{ Lang::get('admin/shippingTemplates.shipping_template_name') }}: <strong>{{ $template_name->template_name }}</strong>
                    </div>
                </div>
                <div class="portlet-body form">
                    <div class="form-body">
                        <div class="tab-content" id="myTabContent">
                            <div id="shipping_cost_set" class="tab-pane fade in active">
                                <div class="table-responsive">
                                    @if(count($company_name) > 0)
                                        @foreach($company_name as $key => $name)
                                            <div class="mb30">
                                                <table class="table table-bordered table-hover table-striped">
                                                    <thead>
                                                        <th colspan="4"><strong>{{ $name }}</strong></th>
                                                    </thead>
                                                     <?php
                                                        $template_company_id =$id=$other_countries='';
                                                        $countries_name = array();
                                                        $shippingTemplateService = new ShippingTemplateService();
                                                        $fee_type = $shippingTemplateService->getCompanyDetails($template_name->id, $key);
                                                        $countries_details = $shippingTemplateService->getCountriesDetails($template_name->id, $fee_type->id);
                                                        if($countries_details != ''){
                                                            foreach($countries_details as $key => $c_details){
                                                                    $template_company_id = $c_details->template_company_id;
                                                                    $id = $c_details->id;
                                                            }
                                                            //$countries = $shippingTemplateServicegetCountries($template_name->id, $template_company_id, $id);
                                                            //
                                                            $shipping_custom_details = $shippingTemplateService->getCustomDetails($template_company_id);

                                                        //echo "<pre>";print_r($c);exit;
                                                        }
													?>
                                                    <tbody>
                                                        <tr>
                                                            <td width="150">{{Lang::get('shippingTemplates.group')}}</td>
                                                            <td class="col-md-5">{{Lang::get('shippingTemplates.country_region')}}</td>
                                                            <td colspan="2">{{Lang::get('shippingTemplates.shipping_cost_details')}}</td>
                                                        </tr>
                                                        @if(($fee_type->fee_type == 2 && isset($fee_type)) || ($fee_type->fee_type == 3 && isset($fee_type)))
                                                            <tr>
                                                                <td>1</td>
                                                                <td>{{Lang::get('shippingTemplates.all_countries')}}</td>
                                                                @if($fee_type->fee_type == 2 )
                                                                    <td @if($fee_type->fee_type == 2 ) colspan="2" @endif>{{Lang::get('shippingTemplates.standard')}} (<strong>{{ $fee_type->fee_discount }}</strong>%)</td>
                                                                @endif
                                                                @if($fee_type->fee_type == 3 )
                                                                    <td @if($fee_type->fee_type == 3 ) colspan="2" @endif><span class="badge badge-primary">{{Lang::get('common.free')}}</span></td>
                                                                @endif
                                                            </tr>
                                                        @endif
                                                        @if(isset($shipping_custom_details) && count($shipping_custom_details) > 0)<?php //!$shipping_custom_details->IsEmpty() ?>
                                                            @foreach($shipping_custom_details as $key => $custom_details)
                                                               <tr>
                                                                    <td>{{$key+1}}</td>
                                                                    @if(!empty($custom_details['countries']))
                                                                        <?php
																		$weight_from = $weight_to = $additional_weight = $additional_weight_price = '0';
																		$weight_details = $shippingTemplateService->getWeightDetails($custom_details->id);
																		//$other_countries_details = $shippingTemplateService->getCustomDetails($template_company_id);
																		$countries =  $custom_details['countries'];
																		//$countries_index = array_slice($countries,0,3);
																		$countries_name = implode(', ',$countries);
																		?>
																		<td>{{ $countries_name }}</td>
																	@else
																		<td>{{Lang::get('common.others')}}</td>
                                                                    @endif
                                                                    @if(isset($custom_details['shipping_setting']))
                                                                            @if($custom_details['shipping_setting'] == 'dont_ship_to')
                                                                                <td @if($custom_details['shipping_setting'] == 'dont_ship_to') colspan="2" @endif>
                                                                                	{{Lang::get('shippingTemplates.exclude_all_other_countries')}}
                                                                                </td>
                                                                            @else
                                                                                @if($custom_details['fee_type'] == '2')
                                                                                    <td @if($custom_details['fee_type'] == '2') colspan="2" @endif>
                                                                                    	{{Lang::get('shippingTemplates.standard')}} (<strong>{{$custom_details['discount']}}</strong>%)
                                                                                    </td>
                                                                                @elseif($custom_details['fee_type'] == '3')
                                                                                    <td @if($custom_details['fee_type'] == '3') colspan="2" @endif>{{Lang::get('common.free')}}</td>
                                                                                @else
                                                                                    @if($custom_details['custom_fee_type'] == '1')
                                                                                        <td width="220">
                                                                                            <p>{{Lang::get('shippingTemplates.quantity_range')}}:
                                                                                            	<strong>{{ $custom_details['min_order'] }} - {{ $custom_details['max_order'] }}</strong></p>
                                                                                            <p>{{Lang::get('shippingTemplates.extra_units')}}: <strong>{{ $custom_details['extra_units'] }}</strong></p>
                                                                                        </td>
                                                                                        <td>
                                                                                        	<p>{{Lang::get('shippingTemplates.shipping_costs')}}:
                                                                                            	US$ <strong>{{ CUtil::formatAmount($custom_details['cost_base_weight']) }}</strong></p>
                                                                                            <p>{{Lang::get('shippingTemplates.extra_costs')}}:
                                                                                                US$ <strong>{{ CUtil::formatAmount($custom_details['extra_costs']) }}</strong></p>
                                                                                        </td>
                                                                                    @endif
                                                                                    @if($custom_details['custom_fee_type'] == '2')
                                                                                        <td>
                                                                                        	<p>{{Lang::get('shippingTemplates.initial_weight')}}:
                                                                                               <strong>{{ $custom_details['initial_weight'] }}</strong>
                                                                                            <p>{{Lang::get('shippingTemplates.initial_weight_price')}}:
                                                                                               US$ <strong>{{ CUtil::formatAmount($custom_details['initial_weight_price']) }}</strong></p>
                                                                                        </td>
                                                                                        <td>
                                                                                            @foreach($weight_details as $key => $w_d)
                                                                                                <p>{{Lang::get('shippingTemplates.initial_weight')}}:
                                                                                                	<strong>{{ $w_d->weight_from }} - {{ $w_d->weight_to }}</strong></p>
                                                                                                <p>{{Lang::get('shippingTemplates.additional_weight')}} + {{ $w_d->additional_weight }}
	                                                                                                {{Lang::get('shippingTemplates.additional_weight_added_to_freight')}} + US$
                                                                                                    <strong>{{ CUtil::formatAmount($w_d->additional_weight_price) }}</strong>
                                                                                            @endforeach
                                                                                        </td>
                                                                                    @endif
                                                                                @endif
                                                                            @endif
                                                                        @endif
                                                                    </tr>
                                                                @endforeach
                                                            @endif
                                                     </tbody>
                                                </table>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div id="delivery_time_set" class="tab-pane fade in ">
                                <div class="table-responsive">
                                    @if(count($company_name) > 0)
                                        @foreach($company_name as $key => $name)
                                            <div class="mb30">
                                                <table class="table table-bordered table-hover table-striped">
                                                    <thead>
                                                        <th colspan="4"><strong>{{ $name }}</strong></th>
                                                    </thead>
                                                     <?php
                                                        $shippingTemplateService = new ShippingTemplateService();
                                                        $fee_type = $shippingTemplateService->getCompanyDetails($template_name->id, $key);
                                                        $template_company_id ='';
                                                        $delivery_countries_details = $shippingTemplateService->getDeliveryCountriesDetails($template_name->id, $fee_type->id);
                                                        if($delivery_countries_details != ''){
                                                            foreach($delivery_countries_details as $key => $delivery_countries){
                                                                    $template_company_id = $delivery_countries->template_company_id;

                                                            }
                                                            //$shipping_custom_details = $shippingTemplateService->getCustomDetails($template_company_id);
                                                            $delivery_custom_details = $shippingTemplateService->getDeliveryCustomDetails($template_company_id);
                                                        }
                                                        //echo "<pre>";print_r($template_company_id);exit;
                                                    ?>
                                                    <tbody>
                                                        <tr>
                                                            <td width="150">{{Lang::get('shippingTemplates.delivery_time_set')}}</td>
                                                            <td class="col-md-6">{{Lang::get('shippingTemplates.country_region')}}</td>
                                                            <td colspan="2">{{Lang::get('shippingTemplates.promised_delivery_time')}}</td>
                                                        </tr>
                                                        @if($fee_type->delivery_type == '2')
                                                            <tr>
                                                                @if(($fee_type->fee_type == 2 && isset($fee_type)) || ($fee_type->fee_type == 3 && isset($fee_type)))
                                                                    <td>1</td>
                                                                    <td>{{Lang::get('shippingTemplates.all_countries')}}</td>
                                                                    @if($fee_type->fee_type == 2 )
                                                                        @if($fee_type->days == 1 || $fee_type->days == 0)
                                                                            <td @if($fee_type->days == 1 || $fee_type->days == 0) colspan="2" @endif>
                                                                            	<strong>{{ $fee_type->days }}</strong> day
                                                                            </td>
                                                                        @else
                                                                            <td @if(!$fee_type->days == 1 || $fee_type->days == 0) colspan="2" @endif>
                                                                            	<strong>{{ $fee_type->days }}</strong> days
                                                                            </td>
                                                                        @endif
                                                                    @endif
                                                                    @if($fee_type->fee_type == 3 )
                                                                        @if($fee_type->days == 1 || $fee_type->days == 0)
                                                                            <td @if($fee_type->days == 1 || $fee_type->days == 0) colspan="2" @endif>
                                                                            	<strong>{{ $fee_type->days }}</strong> day
                                                                            </td>
                                                                        @else
                                                                            <td @if(!$fee_type->days == 1 || $fee_type->days == 0) colspan="2" @endif>
                                                                            	<strong>{{ $fee_type->days }}</strong> days
                                                                            </td>
                                                                        @endif
                                                                    @endif
                                                                @else
                                                                    <td>1</td>
                                                                    <td>{{Lang::get('shippingTemplates.all_countries')}}</td>
                                                                    <td colspan="2"><strong>{{ $fee_type->days }}</strong> days</td>
                                                                @endif
                                                            </tr>
                                                        @endif
                                                        @if($fee_type->delivery_type == '1')
                                                            @if(isset($delivery_custom_details) && count($delivery_custom_details) > 0)<?php //!$delivery_custom_details->IsEmpty() ?>
                                                                @foreach($delivery_custom_details as $key => $custom_details)
                                                                    <tr>
                                                                        <td>{{$key+1}}</td>
                                                                        @if(!empty($custom_details['countries']))
                                                                        <?php $countries =  $custom_details['countries'];
                                                                            //$countries_index = array_slice($countries,0,3);
                                                                            $countries_name = implode(', ',$countries);
                                                                            //echo "<pre>";print_r($custom_details);
                                                                            ?>
                                                                           <td>{{ $countries_name }}</td>
                                                                        @else
                                                                            <td>{{Lang::get('shippingTemplates.other_countries')}}</td>
                                                                        @endif

                                                                        @if(isset($custom_details['days']))
                                                                            <td colspan="2"><strong>{{$custom_details['days']}}</strong> days</td>
                                                                        @endif
                                                                    </tr>
                                                                @endforeach
                                                            @endif
                                                        @endif
                                                 	</tbody>
                                            	</table>
                                        	</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions fluid">
                        <div class="col-md-12">
                            <a class="btn blue" href="{{ URL::action('AdminShippingTemplateController@getEdit', $template_name->id) }}" title="{{ Lang::get('common.edit') }}"><i class="fa fa-edit"></i> {{ Lang::get('common.edit') }}</a>
                            <a class="dialog_confirm btn red" href="{{ URL::action('AdminShippingTemplateController@getDeleteSippingTemplateAction', $template_name->id).'?action=delete' }}" action="Delete" title="{{ Lang::get('common.tem_delete') }}"><i class="fa fa-trash-o"></i> {{ Lang::get('common.tem_delete') }}</a>
                            @if($template_name->is_default == '0')
                            	<a class="btn green dialog_confirm" id="{{ $template_name->id }}" href="{{ URL::action('AdminShippingTemplateController@getSetAsDefaultAction', $template_name->id).'?page='.Input::get('page').'&action=default' }}" action="Default" title="{{ Lang::get('common.set_as_default') }}"><i class="fa fa-check"></i> {{ Lang::get('common.set_as_default') }}</a>
                            @else
                            	 <span class="label label-default pull-right mt8">{{Lang::get('shippingTemplates.default_template')}}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="note note-danger">{{Lang::get('common.invalid_id')}}</div>
    @endif
@stop

@section('script_content')
	<script type="text/javascript">
		var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
		$(window).load(function(){
			  $(".dialog_confirm").click(function(){
					var atag_href = $(this).attr("href");
					var action = $(this).attr("action");
					var cmsg = "";
					//alert(action); return false;
					switch(action){
						case "Delete":
							cmsg = "{{Lang::get('shippingTemplates.confirm_delete_shipping_template')}}";
						break;
						case "Default":
							cmsg = "{{Lang::get('shippingTemplates.confirm_set_default_shipping_template')}}";
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
				      		label: "{{trans('common.cancel')}}",
				      		className: "btn-default",
				    	}
				  	}
				});
				return false;
			});
		});
    </script>
@stop