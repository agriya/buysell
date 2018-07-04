@extends('base')
@section('content')
	<div class="row">
		<div class="col-md-2 clearfix">
			<!-- BEGIN: MANAGE ACCOUNT MENU -->
			@include('myaccount.myAccountMenu')
			<!-- END: MANAGE ACCOUNT MENU -->
		</div>

		<div class="col-md-10">
			<!-- BEGIN: PAGE TITLE -->
			<div class="margin-bottom-10 clearfix">
				<h1>{{ trans("shopDetails.title") }}</h1>
			</div>
			<!-- END: PAGE TITLE -->

			<!-- BEGIN: INFO BLOCK -->
			@if (Session::has('success_message') && Session::get('success_message') != "")
				<div class="note note-success">{{	Session::get('success_message') }}</div>
				<?php
					Session::forget('success_message');
				?>
			@elseif(Session::has('error_msg'))
				<div class="note note-danger">
					{{ Session::get('error_msg') }}
				</div>
			@endif
			<!-- END: INFO BLOCK -->

            <!-- BEGIN: ALERT BLOCK -->
			@if(!CUtil::isShopOwner(null, $shop_obj))
				<div class="alert alert-info"><i class="fa fa-question-circle"></i> {{ trans("shopDetails.shop_not_set_error_message") }}</div>
			@endif
			<!-- END: ALERT BLOCK -->

			<div class="well bg-form">
				<!-- BEGIN: PAYPAL DETAILS -->
				@if(CUtil::chkIsAllowedModule('sudopay') && $d_arr['sc']->checkGateways('Marketplace-Capture'))
				<div class="portlet">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-money"></i>
							{{ trans('sudopay::sudopay.create_receiver_account') }}
							<!--<a href="javascript:void(0);" class="fn_clsDropDetails" onclick="showHidepanels(this, 'paypal_details')"> <i class="customize-fn_show"></i></a>-->
						</div>
						<div class="tools">
							<a class="collapse" href="javascript:;"></a>
						</div>
					</div>
					<div class="portlet-body">
						<div id="paypal_details">
							<!-- Receiver account creation block.  -->
							<ul class="list-unstyled">
				                <li class="clearfix ver-space">
									<?php
										//echo $sc->displayReceivers($_receiver_account_id);
										echo $d_arr['sc']->displayReceivers('');
									?>
				                </li>
			                </ul>
							<!-- End of Receiver account creation block.  -->
						</div>
					</div>
				</div>
				@endif
				<!-- END: PAYPAL DETAILS -->

				<!-- BEGIN: SHOP DETAILS -->
				<div class="portlet">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-truck"></i>
							{{ trans("shopDetails.shop_details") }}
							<!--<a href="javascript:void(0);" class="fn_clsDropDetails btn-link" onclick="showHidepanels(this, 'shop_details')"> <i class="customize-fn_show"></i></a>-->
						</div>
						<div class="tools">
							<a class="collapse" href="javascript:;"></a>
						</div>
					</div>
					<div class="portlet-body">
						<div id="shop_details" class="">
							@include('shopPolicy')
						</div>
					</div>
				</div>
				<!-- END: SHOP DETAILS -->

				<!-- BEGIN: FEATURED SELLERS -->
				@if(CUtil::chkIsAllowedModule('featuredsellers'))
					<?php
						$featured_sellers_service = new FeaturedSellersService();
						$plan_count = $featured_sellers_service->featuredSellersPlansCount();
					?>
					@if($plan_count > 0)
						<div class="portlet">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-money"></i>
									{{ Lang::get('featuredsellers::featuredsellers.set_as_featured_seller') }}
								</div>
								<div class="tools">
									<a class="collapse" href="javascript:;"></a>
								</div>
							</div>
							<div class="portlet-body">
								<div id="" class="">
									@if(isset($shop_details['is_featured_seller']) && $shop_details['is_featured_seller'] == "Yes" && (strtotime($shop_details['featured_seller_expires']) >= strtotime(date('Y-m-d'))))
					                    <p class="alert alert-info margin-0">{{ Lang::get('featuredsellers::featuredsellers.already_set_featured_seller') }}</p>
					                @else
					            		<?php
					            			$logged_user_id = BasicCUtil::getLoggedUserId();
											$featured_seller_block = $featured_sellers_service->getFeaturedSellersBlock($logged_user_id);
										?>
										{{ $featured_seller_block }}
					                @endif
								</div>
							</div>
						</div>
					@endif
				@endif
				<!-- END: FEATURED SELLERS -->

				<!-- BEGIN: SHOP CANCELLATION POLICY  -->
				<div class="portlet">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-truck"><sup class="fa fa-times"></sup></i>
							{{ trans("shopDetails.shop_cancellation_policy") }}
							<!--<a href="javascript:void(0);" class="fn_clsDropDetails btn-link" onclick="showHidepanels(this, 'shop_cancellation_policy')">{{ trans("shopDetails.shop_cancellation_policy") }} <i class="customize-fn_show"></i></a>-->
						</div>
						<div class="tools">
							<a class="collapse" href="javascript:;"></a>
						</div>
					</div>
					<div class="portlet-body">
						<div id="shop_cancellation_policy" class="clearfix">
							@include('shopCancellationPolicy')
						</div>
					</div>
				</div>
				<!-- END: SHOP CANCELLATION POLICY  -->

				<!-- BEGIN: SHOP BANNER DETAILS -->
				<div class="portlet">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-truck"></i>
							{{ trans("shopDetails.shop_banner_details") }}
							<!--<a href="javascript:void(0);" class="fn_clsDropBanner btn-link" onclick="showHidepanels(this, 'banner_details')">{{ trans("shopDetails.shop_banner_details") }} <i class="customize-fn_show"></i></a>-->
						</div>
						<div class="tools">
							<a class="collapse" href="javascript:;"></a>
						</div>
					</div>
					<div class="portlet-body">
						<div id="banner_details" class="">
							@include('shopBanner')
						</div>
					</div>
				</div>
				<!-- END: SHOP BANNER DETAILS -->

				<!-- BEGIN: SHOP ADDRESS DETAILS -->
				<div class="portlet">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-home"></i>
							{{ trans("shopDetails.shop_address_details") }}
							<!--<a href="javascript:void(0);" class="fn_clsDropAddress btn-link" onclick="showHidepanels(this, 'address_details')">{{ trans("shopDetails.shop_address_details") }} <i class="customize-fn_show"></i></a>-->
						</div>
						<div class="tools">
							<a class="collapse" href="javascript:;"></a>
						</div>
					</div>
					<div class="portlet-body">
						<div id="address_details" class="">
							@include('shopAddress')
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
	<?php $page_name = "shop_details"; ?>
	<script type="text/javascript">
		var page_name = "shop_details";
        var mes_required = "{{ trans('common.required') }}";
        var valid_email = "{{ trans('shopDetails.not_valid_email') }}";
        var shopdetails_cancellation_succ= "{{trans('shopDetails.shopdetails_cancellation_policy_file_deleted_success')}}";
        var url_policy= "{{ Url::action('ShopController@getDeleteCancellationPolicy') }}";
        var shopdetails_banner_succ = "{{trans('shopDetails.shopdetails_banner_deleted_success')}}";
        var url_del_image = "{{ Url::action('ShopController@getDeleteShopImage') }}";
       	var common_no_label = "{{ trans('common.cancel') }}" ;
		var common_yes_label = "{{ trans('common.yes') }}" ;
		var package_name = "{{ Config::get('generalConfig.site_name') }}" ;
		var mes_required = "{{trans('common.required')}}";
		var alpha_numeric = "{{trans('shopDetails.alpha_numeric_characters')}}";
		var shopname_min_length = "{{ Config::get('webshoppack.shopname_min_length') }}";
		var shopname_max_length = "{{ Config::get('webshoppack.shopname_max_length') }}";
		var shopslogan_min_length = "{{ Config::get('webshoppack.shopslogan_min_length') }}";
		var shopslogan_max_length = "{{ Config::get('webshoppack.shopslogan_max_length') }}";
		var fieldlength_shop_description_min = "{{ Config::get('webshoppack.fieldlength_shop_description_min') }}";
		var fieldlength_shop_description_max = "{{ Config::get('webshoppack.fieldlength_shop_description_max') }}";
		var fieldlength_shop_contactinfo_min = "{{ Config::get('webshoppack.fieldlength_shop_contactinfo_min') }}";
		var fieldlength_shop_contactinfo_max = "{{ Config::get('webshoppack.fieldlength_shop_contactinfo_max') }}";
		var desc_max = "{{ Config::get('webshoppack.fieldlength_shop_description_max') }}";
		var contactinfo_max = "{{ Config::get('webshoppack.fieldlength_shop_contactinfo_max') }}";
		var featuredsellers = "{{ CUtil::chkIsAllowedModule('featuredsellers') }}";
		var mes_required = '{{ Lang::get('auth/form.required') }}';
		var sellers_featured_head = '{{ trans('featuredsellers::featuredsellers.sellers_featured_head') }}';
		var alert_msg = '{{ trans('featuredsellers::featuredsellers.confirm_featured_seller_fee_from_credit') }}';
	</script>

@stop