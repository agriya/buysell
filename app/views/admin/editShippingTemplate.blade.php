@extends('admin')
@section('content')
	<!-- NOTIFICATIONS STARTS -->
    @include('notifications')
    <!-- NOTIFICATIONS END -->

	<!-- PAGE TITLE STARTS -->
	<h1 class="page-title">{{ Lang::get('admin/shippingTemplates.edit_shipping_template') }}</h1>
    <!-- PAGE TITLE END -->
	<?php $shipping_url = URL::action('AdminShippingTemplateController@postEdit',$id); ?>
	{{ Form::model($shipping_template_details, ['url' => $shipping_url, 'method' => 'post', 'id' => 'editShippingTemplatefrm', 'class' => 'form-horizontal']) }}
        <div class="portlet box blue-hoki">
            <!--- TABLE TITLE STARTS --->
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-list"></i> {{ Lang::get('admin/shippingTemplates.shipping_companies_list') }}
                    </div>
                </div>

                <div class="portlet-body">
                	<div class="form-body">
                    	 <div class="form-group {{{ $errors->has('template_name') ? 'error' : '' }}}">
                            {{ Form::label('template_name', trans("admin/shippingTemplates.shipping_template_name"), array('class' => 'col-md-3 control-label required-icon')) }}
                            <div class="col-md-4">
                                {{  Form::text('template_name', null, array('class' => 'form-control valid')) }}
                                <label class="error">{{{ $errors->first('template_name') }}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="tabbable-custom tabbable-customnew">
                    	<label class="error">{{{ $errors->first('companies') }}}</label>
                        <!-- MOBILE TOGGLER STARTS -->
                        	<button class="btn btn-primary btn-sm mobilemenu-bar mb10"><i class="fa fa-chevron-down"></i> Menu</button>
                        <!-- MOBILE TOGGLER END -->

                        <div class="mobilemenu">
                            <div class="bs-example bs-example-tabs mb30">
                                <ul role="tablist" class="nav nav-tabs mbldropdown-menu ac-custom-tabs" id="myTab">
                                    <li class="active"><a href="#postservice" data-toggle="tab" role="tab" >Post Serive</a></li>
                                    <li><a href="#express" data-toggle="tab" role="tab">Express</a></li>
                                    <li><a href="#specialline" data-toggle="tab" role="tab" >Special Line</a></li>
                                    <li><a href="#others" data-toggle="tab" role="tab">Others</a></li>
                                </ul>
                            </div>

                            <div class="tab-content" id="myTabContent">
                                <div id="postservice" class="tab-pane fade in active">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover table-striped">
                                            <tbody>
                                                <tr>
                                                    <th colspan="4">Post Service</th>
                                                </tr>
                                                @if(count($post_service_companies) > 0)
                                                    @foreach($post_service_companies as $company)
                                                        <?php
                                                            $fee_type_name = 'fee_type_'.$company->id;
                                                            $discount_name = 'discount_'.$company->id;
                                                            $delivery_type_name = 'delivery_type_'.$company->id;
                                                            $delivery_days_name = 'delivery_days_'.$company->id;
															$company_id = 'company_'.$company->id;

                                                            $is_checked = in_array($company->id,$companies)?true:false;
                                                        ?>
                                                    <tr>
                                                        <td>

                                                            {{ Form::checkbox('companies[]', $company->id, $is_checked, array('id' => $company_id))}}
                                                            {{$company->company_name}}</td>
                                                        <td>
                                                            <p><label>Shipping Cost: </label>
                                                            @if($company->is_standard_fee_available)
                                                                {{Form::radio($fee_type_name, '2')}}Standard
                                                                Discount{{Form::text($discount_name,null)}}%
                                                                <label class="error">{{{ $errors->first($fee_type_name) }}}</label>
                                                            @endif
                                                            </p>

                                                            <p><label>Delivery Time: </label>
                                                            {{Form::radio($delivery_type_name, '2')}}Promised Time
                                                            {{Form::text($delivery_days_name,null)}} days
                                                            	<label class="error">{{{ $errors->first($delivery_type_name) }}}</label>
                                                                <label class="error">{{{ $errors->first($delivery_days_name) }}}</label>
                                                            </p>

                                                        </td>
                                                        <td>
                                                            <p>{{Form::radio($fee_type_name, '3')}}Free</p>
                                                            @if($company->is_custom_delivery_available)
                                                                <p>{{Form::radio($delivery_type_name, '1')}}Customize Delivery Time</p>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($company->is_custom_fee_available)
                                                                {{Form::radio($fee_type_name, '1')}}
																<a href="{{URL::action('AdminShippingTemplateController@getCustomShippingTemplate',array($id,$company->id))}}" rel="screenshots_group" class="fn_fancyboxview" data-id="{{$company->id}}">Custom</a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="4">No shipping companies found for this category</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                         </table>
                                    </div>
                                </div>

                                <div id="express" class="tab-pane fade in ">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover table-striped">
                                            <tbody>
                                                <tr>
                                                    <th colspan="4">Express</th>
                                                </tr>
                                                @if(count($express_companies) > 0)
                                                    @foreach($express_companies as $company)
                                                        <?php
                                                            $fee_type_name = 'fee_type_'.$company->id;
                                                            $discount_name = 'discount_'.$company->id;
                                                            $delivery_type_name = 'delivery_type_'.$company->id;
                                                            $delivery_days_name = 'delivery_days_'.$company->id;

                                                            $is_checked = in_array($company->id,$companies)?true:false;
                                                        ?>
                                                    <tr>
                                                        <td>

                                                            {{ Form::checkbox('companies[]', $company->id, $is_checked) }}
                                                            {{$company->company_name}}</td>
                                                        <td>
                                                            <p><label>Shipping Cost: </label>
                                                            @if($company->is_standard_fee_available)
                                                                {{Form::radio($fee_type_name, '2')}}Standard
                                                                Discount{{Form::text($discount_name,null)}}%
                                                                <label class="error">{{{ $errors->first($fee_type_name) }}}</label>
                                                            @endif
                                                            </p>

                                                            <p><label>Delivery Time: </label>
                                                            {{Form::radio($delivery_type_name, '2')}}Promised Time
                                                            {{Form::text($delivery_days_name,null)}} days
                                                            	<label class="error">{{{ $errors->first($delivery_type_name) }}}</label>
                                                                <label class="error">{{{ $errors->first($delivery_days_name) }}}</label>
                                                            </p>

                                                        </td>
                                                        <td>
                                                            <p>{{Form::radio($fee_type_name, '3')}}Free</p>
                                                            @if($company->is_custom_delivery_available)
                                                                <p>{{Form::radio($delivery_type_name, '1')}}Customize Delivery Time</p>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($company->is_custom_fee_available)
                                                                {{Form::radio($fee_type_name, '1')}}Custom
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="4">No shipping companies found for this category</td>
                                                    </tr>
                                                @endif


                                            </tbody>
                                         </table>
                                    </div>
                                </div>

                                <div id="specialline" class="tab-pane fade in">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover table-striped">
                                            <tbody>
                                                <tr>
                                                    <th colspan="4">Special Line</th>
                                                </tr>
                                                @if(count($special_line_companies) > 0)
                                                    @foreach($special_line_companies as $company)
                                                        <?php
                                                            $fee_type_name = 'fee_type_'.$company->id;
                                                            $discount_name = 'discount_'.$company->id;
                                                            $delivery_type_name = 'delivery_type_'.$company->id;
                                                            $delivery_days_name = 'delivery_days_'.$company->id;

                                                            $is_checked = in_array($company->id,$companies)?true:false;
                                                        ?>
                                                    <tr>
                                                        <td>

                                                            {{ Form::checkbox('companies[]', $company->id, $is_checked) }}
                                                            {{$company->company_name}}</td>
                                                        <td>
                                                            <p><label>Shipping Cost: </label>
                                                            @if($company->is_standard_fee_available)
                                                                {{Form::radio($fee_type_name, '2')}}Standard
                                                                Discount{{Form::text($discount_name,null)}}%
                                                                <label class="error">{{{ $errors->first($fee_type_name) }}}</label>
                                                            @endif
                                                            </p>

                                                            <p><label>Delivery Time: </label>
                                                            {{Form::radio($delivery_type_name, '2')}}Promised Time
                                                            {{Form::text($delivery_days_name,null)}} days
                                                            	<label class="error">{{{ $errors->first($delivery_type_name) }}}</label>
                                                                <label class="error">{{{ $errors->first($delivery_days_name) }}}</label>
                                                            </p>

                                                        </td>
                                                        <td>
                                                            <p>{{Form::radio($fee_type_name, '3')}}Free</p>
                                                            @if($company->is_custom_delivery_available)
                                                                <p>{{Form::radio($delivery_type_name, '1')}}{{ trans("admin/shippingTemplates.customize_delivery_time") }}</p>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($company->is_custom_fee_available)
                                                                {{Form::radio($fee_type_name, '1')}}Custom
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="4">No shipping companies found for this category</td>
                                                    </tr>
                                                @endif

                                            </tbody>
                                         </table>
                                    </div>
                                </div>
                                <div id="others" class="tab-pane fade in">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover table-striped">
                                            <tbody>
                                                <tr>
                                                    <th colspan="4">Others</th>
                                                </tr>
                                                @if(count($other_companies) > 0)
                                                    @foreach($other_companies as $company)
                                                        <?php
                                                            $fee_type_name = 'fee_type_'.$company->id;
                                                            $discount_name = 'discount_'.$company->id;
                                                            $delivery_type_name = 'delivery_type_'.$company->id;
                                                            $delivery_days_name = 'delivery_days_'.$company->id;

                                                            $is_checked = in_array($company->id,$companies)?true:false;
                                                        ?>
                                                    <tr>
                                                        <td>

                                                            {{ Form::checkbox('companies[]', $company->id, $is_checked) }}
                                                            {{$company->company_name}}</td>
                                                        <td>
                                                            <p><label>Shipping Cost: </label>
                                                            @if($company->is_standard_fee_available)
                                                                {{Form::radio($fee_type_name, '2')}}Standard
                                                                Discount{{Form::text($discount_name,null)}}%
                                                                <label class="error">{{{ $errors->first($fee_type_name) }}}</label>
                                                            @endif
                                                            </p>

                                                            <p><label>Delivery Time: </label>
                                                            {{Form::radio($delivery_type_name, '2')}}Promised Time
                                                            {{Form::text($delivery_days_name,null)}} days
                                                            	<label class="error">{{{ $errors->first($delivery_type_name) }}}</label>
                                                                <label class="error">{{{ $errors->first($delivery_days_name) }}}</label>
                                                            </p>

                                                        </td>
                                                        <td>
                                                            <p>{{Form::radio($fee_type_name, '3')}}Free</p>
                                                            @if($company->is_custom_delivery_available)
                                                                <p>{{Form::radio($delivery_type_name, '1')}}Customize Delivery Time</p>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($company->is_custom_fee_available)
                                                                {{Form::radio($fee_type_name, '1')}}Custom
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="4">No shipping companies found for this category</td>
                                                    </tr>
                                                @endif

                                            </tbody>
                                         </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions fluid">
                        <div class="col-md-offset-3 col-md-8">
                            <button name="edit_product" id="edit_product" value="edit_product" type="submit" class="btn green">
                                <i class="fa fa-save"></i> {{ trans("admin/shippingTemplates.save") }}
                            </button>
                        </div>
                    </div>
                </div>
            <!--- TABLE TITLE END --->
        </div>
    {{Form::close()}}
@stop

@section('script_content')
	<script type="text/javascript">
		$(".fn_fancyboxview").fancybox({
			beforeShow: function() {
				$(".fancybox-wrap").addClass('view-proprevw');
			},
			maxWidth    : 772,
			maxHeight   : 432,
			fitToView   : false,
			width       : '70%',
			height      : '432',
			autoSize    : true,
			closeClick  : true,
			openEffect  : 'none',
			closeEffect : 'none'
		});

		var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
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
	</script>
@stop