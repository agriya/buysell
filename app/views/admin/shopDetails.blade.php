@extends('admin')
@section('content')
	<!--- BEGIN: INFO BLOCK --->
	@if (Session::has('success_message') && Session::get('success_message') != "")
        <div class="note note-success">{{	Session::get('success_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif

    @if (Session::has('error_message') && Session::get('error_message') != "")
        <div class="note note-danger">{{	Session::get('error_message') }}</div>
        <?php Session::forget('error_message'); ?>
    @endif
    <!--- END: INFO BLOCK --->

    <div class="portlet box blue-hoki">
        <!--- BEGIN: TITLE PAGE --->
        <div class="portlet-title">
            <div class="caption">
                @if($d_arr['mode'] == 'edit')<i class="fa fa-edit"></i>@else<i class="fa fa-plus-circle"></i>@endif {{ $d_arr['pageTitle'] }}
            </div>
            <a href="{{ URL::to('admin/shops') }}" title="{{ Lang::get('common.back_to_list') }}" class="btn default btn-xs purple-stripe pull-right">
                <i class="fa fa-chevron-left"></i> {{ Lang::get('common.back_to_list') }}
            </a>
        </div>
        <!--- END: TITLE PAGE --->

        <!-- BEGIN: USER DETAILS -->
        <div class="portlet-body form admin-dl">
            <div class="form-body">
                <!-- BEGIN: ALERT BLOCK -->
                @if(!CUtil::isShopOwner(null, $shop_obj))
                    <div class="note note-info"><i class="fa fa-question-circle"></i> {{ trans("shopDetails.shop_not_set_error_message") }}</div>
                @endif
                <!-- END: ALERT BLOCK -->
                <div class="mb20">
                    <h4 class="form-section">{{ Lang::get('admin/manageMembers.memberlist_user_details') }}</h4>
                    <div class="dl-horizontal">
                        <dl><dt>{{ trans('admin/manageMembers.viewmember_user_code') }}</dt> <dd><span>
                            <a href="{{ URL::to('admin/users/user-details').'/'.$user_details['id'] }}">{{ BasicCutil::setUserCode($user_details['id']) }}</a></span></dd></dl>
                        <dl><dt>{{ trans('admin/manageMembers.viewmember_first_name') }}</dt> <dd><span>{{ $user_details['first_name'] }}</span></dd></dl>
                        <dl><dt>{{ trans('admin/manageMembers.viewmember_last_name') }}</dt> <dd><span>{{ $user_details['last_name'] }}</span></dd></dl>
                        <dl><dt>{{ trans('admin/manageMembers.viewmember_user_name') }}</dt> <dd><span>{{ $user_details['user_name'] }}</span></dd></dl>
                        <dl><dt>{{ trans('admin/manageMembers.viewmember_email') }}</dt> <dd><span>{{ $user_details['email'] }}</span></dd></dl>
                    </div>
                </div>
            </div>
        </div>
        <!-- END: USER DETAILS -->
    </div>

    <div class="portlet box blue-hoki">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-list"></i> shop Details
            </div>
        </div>

        <div class="portlet-body form">
            <div class="form-body bckg-color">

            <!-- BEGIN: SHOP DETAILS -->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-truck"></i> {{ trans("shopDetails.shop_details") }}
                    </div>
                    <div class="tools">
                        <a class="collapse" href="javascript:;"></a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div id="shop_details">
                        @include('admin.shopPolicy')
                    </div>
                </div>
            </div>
            <!-- END: SHOP DETAILS -->

            <!-- BEGIN: SHOP CANCELLATION POLICY -->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-truck"><sup class="fa fa-times"></sup></i> {{ trans("shopDetails.shop_cancellation_policy") }}
                    </div>
                    <div class="tools">
                        <a class="collapse" href="javascript:;"></a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div id="shop_cancellation_policy" class="clearfix">
                        @include('admin.shopCancellationPolicy')
                    </div>
                </div>
            </div>
            <!-- END: SHOP CANCELLATION POLICY -->

            <!-- BEGIN: SHOP BANNER DETAILS -->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-truck"></i> {{ trans("shopDetails.shop_banner_details") }}
                    </div>
                    <div class="tools">
                        <a class="collapse" href="javascript:;"></a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div id="banner_details" class="">
                        @include('admin.shopBanner')
                    </div>
                </div>
            </div>
            <!-- END: SHOP BANNER DETAILS -->

            <!-- BEGIN: SHOP ADDRESS DETAILS -->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-home"></i> {{ trans("shopDetails.shop_address_details") }}
                    </div>
                    <div class="tools">
                        <a class="collapse" href="javascript:;"></a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div id="address_details" class="">
                        @include('admin.shopAddress')
                    </div>
                </div>
            </div>
            <!-- END: SHOP ADDRESS DETAILS -->
            </div>
        </div>
    </div>

	<div id="dialog-delete-confirm" class="confirm-dialog-delete" title="" style="display:none;">
		<p><span class="ui-icon ui-icon-alert"></span><small>{{  trans('shopDetails.shopdetails_banner_image_confirm') }}</small></p>
	</div>

	<div id="dialog-cancellation-policy-delete-confirm" class="confirm-dialog-delete" title="" style="display:none;">
		<p><span class="ui-icon ui-icon-alert"></span><small>{{  trans('shopDetails.shopdetails_cancellation_policy_delete_confirm') }}</small></p>
	</div>
@stop
<!-- SHOP SETTINGS ENDS -->

@section('script_content')
	<script src="{{ URL::asset('/js/jquery.form.js') }}"></script>
	<script src="{{ URL::asset('/js/bootstrap/bootstrap.file-input.js') }}"></script>
	<script type="text/javascript">
		var user_id = '{{$user_id}}';
	    function showHidepanels(obj, div_id) {
	    	var link_class = obj.className;
	    	$('#'+div_id).slideToggle(500);
	    	// toggle open/close symbol
	        var span_elm = $('.'+link_class+' i');
	        if(span_elm.hasClass('customize-fn_show')) {
	        	span_elm.removeClass('customize-fn_show');
				span_elm.addClass('customize-fn_hide');
	        }
			else {
				span_elm.removeClass('customize-fn_hide');
	        	span_elm.addClass('customize-fn_show');
	        }
	        return false;
	    }

	    var mes_required = "{{trans('common.required')}}";
		$("#shopanalytics_frm").validate({
			rules: {
				shop_analytics_code: {
					required: true
				}
			},
			messages: {
				shop_analytics_code:{
					required: mes_required
				}
			}
		});

		jQuery.validator.addMethod("slug", function(value, element) {
			return this.optional(element) || /^([a-z0-9_-])+$/i.test(value);
		}, "Alpha-numeric characters, Dashes, and Underscores only please");

		var doSubmit = function(){
			var frmname = arguments[0];
			var divname = arguments[1];

			var form_validated = true;
			//if(frmname != "shopaddress_frm"){
				var validator = $("#"+frmname).validate({  });
		 		if(!$("#"+frmname).valid())
				{
					form_validated = false;
				}
			//}

		 	if(form_validated)
			{
				displayLoadingImage(true);
				var options = {
			    	target:     '#'+divname,
			    	url:        $("#"+frmname).attr('action'),
			    	success: function(responseData)
					{
						if(frmname == "shopbanner_frm")
						{
							$('#success_div').hide();
						}
						hideLoadingImage(true);
					}
				};
				// pass options to ajaxSubmit
				$('#'+frmname).ajaxSubmit(options);
			}
			else
			{
				validator.focusInvalid();
			}
			return false;
		};

		var common_no_label = "{{ trans('common.cancel') }}" ;
		var common_yes_label = "{{ trans('common.yes') }}" ;
		var package_name = "{{ Config::get('generalConfig.site_name') }}" ;

		function removeShopImage(resource_id, imagename, imageext, imagefolder) {
			$("#dialog-delete-confirm").dialog({
				title: package_name,
				modal: true,
				buttons: [{
						text: common_yes_label,
						click: function()
						{
							displayLoadingImage();
							$.getJSON("{{ Url::action('AdminUserController@getDeleteShopImage') }}",
							{resource_id: resource_id, user_id:user_id, imagename: imagename, imageext: imageext, imagefolder: imagefolder},
								function(data)
								{
									hideLoadingImage();
									if(data.result == 'success')
									{
										$('#itemResourceRow_'+resource_id).remove();
										$('#success_div').show();
										$('#success_msg_div').hide();
										$('#success_div').html("{{trans('shopDetails.shopdetails_banner_deleted_success')}}");
									}
									else
									{
										$('#success_div').hide();
										$('#success_msg_div').hide();
										//showErrorDialog({status: 'error', error_message: '{{ trans('common.invalid_action') }}'});
									}
							});
							$(this).dialog("close");
						}
					},
					{
						text: common_no_label,
						click: function()
						{
							 $(this).dialog("close");
						}
					}
				]});
		}

		function removeShopCancellationPolicy(resource_id) {
			$("#dialog-cancellation-policy-delete-confirm").dialog({
				title: package_name,
				modal: true,
				buttons: [{
						text: common_yes_label,
						click: function()
						{
							displayLoadingImage();
							$.getJSON("{{ Url::action('AdminUserController@getDeleteCancellationPolicy') }}",
							{resource_id: resource_id, user_id:user_id},
								function(data)
								{
									hideLoadingImage();
									if(data.result == 'success')
									{
										$('#shopCancellationPolicyRow_'+resource_id).remove();
										$('#cancellation_pollicy_success_div_msg').hide();
										$('#cancellation_pollicy_success_div').show();
										$('#cancellation_pollicy_success_div').html("{{trans('shopDetails.shopdetails_cancellation_policy_file_deleted_success')}}");
									}
									else
									{
										$('#cancellation_pollicy_success_div').hide();
										$('#cancellation_pollicy_success_div_msg').hide();
										//showErrorDialog({status: 'error', error_message: '{{ trans('common.invalid_action') }}'});
									}
							});
							$(this).dialog("close");
						}
					},
					{
						text: common_no_label,
						click: function()
						{
							 $(this).dialog("close");
						}
					}
				]});
		}
	</script>

	<script type="text/javascript">
        var mes_required = "{{ trans('common.required') }}";
        var valid_email = "{{ trans('shopDetails.not_valid_email') }}";
        $("#shoppaypal_frm").validate({
            rules: {
                paypal_id: {
                    required: true,
                    email: true
                },
            },
            messages: {
                paypal_id: {
                    required : mes_required,
                    email: valid_email
                },
            }
        });
    </script>

    <script type="text/javascript">
		var mes_required = "{{trans('common.required')}}";
		$(document).ready(function() {
			var desc_max = "{{ Config::get('webshoppack.fieldlength_shop_description_max') }}";
			var contactinfo_max = "{{ Config::get('webshoppack.fieldlength_shop_contactinfo_max') }}";
			$('#shop_desc').keyup(function(e) {
				var text_length = $('#shop_desc').val().length;
				var text_remaining = desc_max - text_length;
				if(text_remaining >= 0)
				{
					$('#shop_desc_count').html('<div class="form-info">'+text_remaining+' characters left </div>');
				}
				else
				{
					 $('#shop_desc').val($('#shop_desc').val().substring(0, desc_max));
				}
			});

			$('#shop_contactinfo').keyup(function(e) {
				var text_length = $('#shop_contactinfo').val().length;
				var text_remaining = contactinfo_max - text_length;
				if(text_remaining >= 0)
				{
					$('#shop_contactinfo_count').html('<div class="form-info">'+text_remaining+' characters left </div>');
				}
				else
				{
					 $('#shop_contactinfo').val($('#shop_contactinfo').val().substring(0, contactinfo_max));
				}
			});

			$('#shop_name').focusout(function() {
				if ($('#url_slug').val() == '') {
					var tmp_str = $('#shop_name').val().replace(/\s/g,'-'); // to replace spaces with hypens
					tmp_str = tmp_str.replace(/[\-]+/g,'-');	// to remove extra hypens
					tmp_str = tmp_str.replace(/[^a-zA-Z0-9\-]/g,'').toLowerCase(); // to convert to lower case and only allow alpabets and number and hypehn
					tmp_str = alltrimhyphen(tmp_str);
					$('#url_slug').val(tmp_str);
				}
			});
			$('#url_slug').focusout(function() {
				if ($('#url_slug').val() != '') {
					var tmp_str = $('#url_slug').val().replace(/\s/g,'-'); // to replace spaces with hypens
					tmp_str = tmp_str.replace(/[\-]+/g,'-');	// to remove extra hypens
					tmp_str = tmp_str.replace(/[^a-zA-Z0-9\-]/g,'').toLowerCase(); // to convert to lower case and only allow alpabets and number and hypehn
					tmp_str = alltrimhyphen(tmp_str);
					$('#url_slug').val(tmp_str);
				}
			});
			function alltrimhyphen(str) {
				return str.replace(/^\-+|\-+$/g, '');
			}
		});

		$("#shoppolicy_frm").validate({
			rules: {
				shop_name: {
					required: true,
					minlength: "{{ Config::get('webshoppack.shopname_min_length') }}",
					maxlength: "{{ Config::get('webshoppack.shopname_max_length') }}"
				},
				url_slug: {
					required: true
				},
				shop_slogan: {
					minlength: "{{ Config::get('webshoppack.shopslogan_min_length') }}",
					maxlength: "{{ Config::get('webshoppack.shopslogan_max_length') }}",
				},
				shop_desc: {
					minlength: "{{ Config::get('webshoppack.fieldlength_shop_description_min') }}",
					maxlength: "{{ Config::get('webshoppack.fieldlength_shop_description_max') }}",
				},
				shop_status: {
					required: true
				},
				shop_contactinfo: {
					minlength: "{{ Config::get('webshoppack.fieldlength_shop_contactinfo_min') }}",
					maxlength: "{{ Config::get('webshoppack.fieldlength_shop_contactinfo_max') }}",
				}
			},
			messages: {
				shop_name: {
					required : mes_required,
					minlength: jQuery.format("{{ trans('shopDetails.shopname_min_length') }}"),
					maxlength: jQuery.format("{{ trans('shopDetails.shopname_max_length') }}")
				},
				url_slug: {
					required : mes_required,
				},
				shop_slogan: {
					minlength: jQuery.format("{{ trans('shopDetails.shopslogan_min_length') }}"),
					maxlength: jQuery.format("{{ trans('shopDetails.shopslogan_max_length') }}"),
				},
				shop_status: {
					required : mes_required,
				}
			}
		});

		$("#shopaddress_frm").validate({
			rules: {
				shop_country: {
					required: true
				},
				shop_address1: {
					required: true
				},
				shop_city: {
					required: true
				},
				shop_state: {
					required: true
				},
				shop_zipcode: {
					required: true
				}
			},
			messages: {
				shop_country: {
					required: mes_required
				},
				shop_address1: {
					required: mes_required
				},
				shop_city: {
					required: mes_required
				},
				shop_state: {
					required: mes_required
				},
				shop_zipcode: {
					required: mes_required
				},
			},
			/* For Contact info violation */
			submitHandler: function(form) {
				form.submit();
			}
		});
	</script>

	<script>
		$(document).ready(function(){
			$('input[type=file]').bootstrapFileInput();
		});
	</script>
@stop