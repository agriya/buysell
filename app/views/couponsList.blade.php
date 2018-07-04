@extends('base')
@section('content')
	<div class="row">
		<div class="col-md-2 clearfix">
			<!-- BEGIN: MANAGE ACCOUNT -->
			@include('myaccount.myAccountMenu')
			<!-- END: MANAGE ACCOUNT -->
		</div>

		<div class="col-md-10">
			<!-- BEGIN: PAGE TITLE -->
			<div class="responsive-pull-none">
				<div class="@if(count($coupons) <= 0) responsive-text-center @else  @endif">
					<a href="{{ URL::action('CouponsController@getAdd') }}" class="btn btn-xs green-meadow responsive-btn-block pull-right">
					<i class="fa fa-plus margin-right-5"></i>{{ Lang::get('coupon.add_coupon')  }}</a>
				</div>
				<h1>{{ Lang::get('coupon.coupons_list') }}</h1>
			</div>
			<!-- END: TITLE -->

			<!-- BEGIN: ALERT BLOCK -->
			@if(Session::has('error_message') && Session::get('error_message') != '')
				<div class="note note-danger">{{ Session::get('error_message') }}</div>
				<?php Session::forget('error_message'); ?>
			@endif
			@if(Session::has('success_message') && Session::get('success_message') != '')
				<div class="note note-success">{{ Session::get('success_message') }}</div>
				<?php Session::forget('success_message'); ?>
			@endif
			<!-- END: ALERT BLOCK -->

			<div class="well">
				{{ Form::open(array('action' => array('CouponsController@getIndex'), 'id'=>'couponFrm', 'method'=>'get','class' => 'form-horizontal' )) }}
					<!-- BEGIN: SEARCH BLOCK -->
					<div id="search_holder" class="portlet bg-form">
						<div class="portlet-title">
							<div class="caption">
								{{ Lang::get('coupon.serach_coupons') }}
							</div>
							<div class="tools">
								<a class="collapse" href="javascript:;"></a>
							</div>
						</div>

						<div id="selSrchProducts" class="portlet-body">
							<fieldset>
								<div class="form-group">
									{{ Form::label('coupon_code', Lang::get('coupon.coupon_code'), array('class' => 'col-md-2 control-label')) }}
									<div class="col-md-4">
										{{ Form::text('coupon_code', Input::get("coupon_code"), array('class' => 'form-control valid')) }}
									</div>
								</div>
								<div class="form-group">
									<div class="col-md-offset-2 col-md-10">
										<button type="submit" name="srchcoupon_submit" value="srchcoupon_submit" class="btn purple-plum">
										<i class="fa fa-search"></i> {{ Lang::get('coupon.search') }}</button>
										<button type="reset" name="srchproduct_reset" value="srchproduct_reset" class="btn default" onclick="javascript:location.href='{{ Request::url() }}'">
										<i class="fa fa-rotate-left"></i> {{ Lang::get('coupon.reset') }}</button>
									</div>
								</div>
							</fieldset>
						</div>
					</div>
					<!-- END: SEARCH BLOCK -->

					@if(count($coupons) > 0)
						<!-- BEGIN: TAXATION LIST -->
						<div class="table-responsive margin-bottom-30">
							<table class="table table-bordered table-hover table-striped">
								<thead>
									<tr>
										<th class="col-md-2">{{ Lang::get('coupon.created_at') }}</th>
										<th class="col-md-2">{{ Lang::get('coupon.coupon_code') }}</th>
										<th class="col-md-1">{{ Lang::get('coupon.from_date') }}</th>
										<th class="col-md-1">{{ Lang::get('coupon.to_date') }}</th>
										<th class="col-md-2">{{ Lang::get('coupon.price') }}</th>
										<th class="col-md-1">{{ Lang::get('coupon.offer') }}</th>
										<th class="col-md-1">{{ Lang::get('coupon.status') }}</th>
										<th width="col-md-1">{{ Lang::get('coupon.action') }}</th>
									</tr>
								</thead>

								<tbody>
									@if(count($coupons) > 0)
										@foreach($coupons as $coupon)
											<tr>
												<td>{{ CUtil::FMTDate($coupon->created_at, 'Y-m-d H:i:s', '') }}</td>
												<td>{{ $coupon->coupon_code }}</td>
												<td>{{ CUtil::FMTDate($coupon->from_date, 'Y-m-d', '') }}</td>
												<td>{{ CUtil::FMTDate($coupon->to_date, 'Y-m-d', '') }}</td>
												<td>
													@if($coupon->price_restriction == 'none')
														{{ Lang::get('coupon.price_restriction_'.$coupon->price_restriction) }}
													@elseif($coupon->price_restriction == 'between')
														{{Lang::get('coupon.if')}} {{Lang::get('coupon.price_restriction_'.$coupon->price_restriction)}} {{ Config::get('generalConfig.site_default_currency') }} <strong>{{Cutil::formatAmount($coupon->price_from)." - ".Cutil::formatAmount($coupon->price_to)}}</strong>
													@else
														{{Lang::get('coupon.if')}} {{Lang::get('coupon.price_restriction_'.$coupon->price_restriction)}} {{ Config::get('generalConfig.site_default_currency') }} <strong>{{ Cutil::formatAmount($coupon->price_from) }}</strong>
													@endif
												</td>
												<td>
													@if($coupon->offer_type == 'Percentage')
														{{Cutil::formatAmount($coupon->offer_amount)."%"}}
													@else
														{{ Config::get('generalConfig.site_default_currency') }} {{Cutil::formatAmount($coupon->offer_amount)}}
													@endif
												</td>

												<td>
													<?php
														if(count($coupon) > 0)
														{
															if($coupon['status'] == 'Active' && $coupon['to_date'] >= date('Y-m-d') && $coupon['from_date'] <= date('Y-m-d'))
															{
																$lbl_class = "badge-success";
																$status = 'Active';
															}
																else
															{
																$lbl_class = " badge-danger";
																$status = 'InActive';
															}
														}
													?>
													<span class="badge {{ $lbl_class }}">{{ Lang::get('common.'.strtolower($status)) }}</span>
												</td>

												<td class="action-btn">
													<a title="{{Lang::get('coupon.edit')}}" href="{{ URL:: action('CouponsController@getUpdate',$coupon->id) }}" class="btn btn-xs blue">
													<i class="fa fa-edit"></i></a>
													@if($coupon->status == 'Active')
														<a title="{{Lang::get('coupon.deactivate')}}" href="javascript:void(0)" onclick="doAction('{{ $coupon->id }}', 'deactivate')" class="btn btn-xs red"><i class="fa fa-ban"></i></a>
													@else
														<a title="{{Lang::get('coupon.activate')}}" href="javascript:void(0)" onclick="doAction('{{ $coupon->id }}', 'activate')" class="btn btn-xs green"><i class="fa fa-check"></i></a>
													@endif
													<a title="{{Lang::get('coupon.delete')}}" href="javascript:void(0)" onclick="doAction('{{ $coupon->id }}', 'delete')" class="btn btn-xs btn-danger">
													<i class="fa fa-trash-o"></i></a>
												</td>
											</tr>
										@endforeach
									@else
										<tr>
											<td colspan="8"><p class="alert alert-info">{{ Lang::get('coupon.products_not_found_msg') }}</p></td>
										</tr>
									@endif
								</tbody>
							</table>
						</div>
					@else
						<div class="note note-info">
						   {{ Lang::get('coupon.list_empty') }}
						</div>
					@endif
					<!-- END: TAXATION LIST -->

					@if(count($coupons) > 0)
						{{ $coupons->appends(array('coupon_code' => Input::get('coupon_code'), 'srchcoupon_submit' => Input::get('srchcoupon_submit')))->links() }}
					@endif
				{{ Form::close() }}

				{{ Form::open(array('id'=>'couponsActionfrm', 'method'=>'post', 'url' => URL::action('CouponsController@postAction'))) }}
					{{ Form::hidden('coupon_id', '', array('id' => 'coupon_id')) }}
					{{ Form::hidden('coupon_action', '', array('id' => 'coupon_action')) }}
				{{ Form::close() }}
				<div id="dialog-product-confirm" class="confirm-dialog-delete" title="" style="display:none;">
					<span class="ui-icon ui-icon-alert"></span>
					<span id="dialog-product-confirm-content" class="show"></span>
				</div>
			</div>
		</div>
	</div>
@stop

@section('script_content')
	<script type="text/javascript">
		$('.fn_clsDropSearch').click(function() {
	        $('#search_holder').slideToggle(500);
	        // toggle open/close symbol
	        var span_elm = $('.fn_clsDropSearch i');
	        if(span_elm.hasClass('fa fa-caret-up')) {
	            $('.fn_clsDropSearch').html('{{ Lang::get('coupon.show_search_filters') }} <i class="fa fa-caret-down ml5"></i>');
	        } else {
	            $('.fn_clsDropSearch').html('{{ Lang::get('coupon.hide_search_filters') }} <i class="fa fa-caret-up ml5"></i>');
	        }
	        return false;
	    });

	    function doAction(coupon_id, selected_action)
		{
			if(selected_action == 'delete')
			{
				$('#dialog-product-confirm-content').html('{{ Lang::get('coupon.confirm_delete') }}');
			}
			else if(selected_action == 'deactivate')
			{
				$('#dialog-product-confirm-content').html('{{ Lang::get('coupon.confirm_deactivate') }}');
			}
			else if(selected_action == 'activate')
			{
				$('#dialog-product-confirm-content').html('{{ Lang::get('coupon.confirm_activate') }}');
			}

			$("#dialog-product-confirm").dialog({ title: '{{ Lang::get('coupon.coupons_list') }}', modal: true,
				buttons: {
					"{{ Lang::get('common.yes') }}": function() {
						$(this).dialog("close");
						$('#coupon_action').val(selected_action);
						$('#coupon_id').val(coupon_id);
						document.getElementById("couponsActionfrm").submit();
					}, "{{ Lang::get('common.cancel') }}": function() { $(this).dialog("close"); }
				}
			});

			return false;
		}

		$(".fn_changeStatus").fancybox({
	        maxWidth    : 800,
	        maxHeight   : 430,
	        fitToView   : false,
	        width       : '70%',
	        height      : '430',
	        autoSize    : false,
	        closeClick  : false,
	        type        : 'iframe',
	        openEffect  : 'none',
	        closeEffect : 'none'
	    });
	</script>
@stop