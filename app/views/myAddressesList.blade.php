@extends('base')
@section('content')
	<div class="row">
		<div class="col-md-2 clearfix">
			<!-- BEGIN: MANAGE ACCOUNT MENU -->
			@include('myaccount.myAccountMenu')
			<!-- END: MANAGE ACCOUNT MENU -->
		</div>

		<div class="col-md-10">
			<div class="responsive-pull-none">
				<a href="{{ URL::action('AddressesController@getAddAddress') }}" class="pull-right btn btn-xs green-meadow responsive-btn-block">
					<i class="fa fa-plus"></i> {{ Lang::get('myAddresses.add_address') }}
				</a>
				<h1>{{ Lang::get('myAddresses.my_addresses') }}</h1>
			</div>

			<!-- BEGIN: INFO BLOCK -->
			@if(Session::has('error_message') && Session::get('error_message') != '')
				<div class="note note-danger">{{ Session::get('error_message') }}</div>
				<?php Session::forget('error_message'); ?>
			@endif

			@if(Session::has('success_message') && Session::get('success_message') != '')
				<div class="note note-success">{{ Session::get('success_message') }}</div>
				<?php Session::forget('success_message'); ?>
			@endif
            <!-- END: INFO BLOCK -->

			<div class="well">
				@if(!isset($shipping_addresses) || !$shipping_addresses || count($shipping_addresses) <= 0)
					<div class="note note-info margin-bottom-0">
					   {{ Lang::get('myAddresses.no_result') }}
					</div>
				@else
					@if(count($shipping_addresses) > 0)
						<!-- BEGIN: SHIPPING ADDRESS LIST -->
						{{ Form::open(array('action' => array('AddressesController@getIndex'), 'id'=>'addressFrm', 'method'=>'get','class' => 'form-horizontal' )) }}
							<div class="table-responsive margin-bottom-30">
								<table class="table table-bordered table-hover table-striped">
									<thead>
										<tr>
											<th class="col-md-4"></th>
											<th class="col-md-6">{{ Lang::get('myAddresses.address') }}</th>
											<th class="col-md-2">{{ Lang::get('common.action') }}</th>
										</tr>
									</thead>

									<tbody>
										@if(count($shipping_addresses) > 0)
											@foreach($shipping_addresses as $address)
												<tr>
													<td>
														<div class="dl-horizontal dl-horizontal-new">
															@if($address->is_primary == 'Yes')
																<p class="margin-bottom-0"><span><strong>{{ Lang::get('myAddresses.primary_shipping_address') }}</strong></span></p>
																<p><small class="text-muted">({{ Lang::get('myAddresses.your_main_address_for_purchase') }})</small></p>
															@endif
														</div>
													</td>
													<td class="myaddr-space">
														@if(isset($address->address_line1))
															<p>{{{$address->address_line1}}}</p>
														@endif
														@if(isset($address->address_line2))
															<p>{{{$address->address_line2}}}</p>
														@endif
														@if(isset($address->street))
															<p>{{{$address->street}}}</p>
														@endif
														@if(isset($address->city))
															<p>{{{$address->city}}}</p>
														@endif
														@if(isset($address->state))
															<p>{{{$address->state}}}</p>
														@endif
														@if(isset($address->country))
															<p>{{{$address->country}}}</p>
														@endif
														@if(isset($address->zip_code))
															<p>{{{$address->zip_code}}}</p>
														@endif
													</td>
													<td class="action-btn">
														@if($address->is_primary != 'Yes')
															<a href="javascript:;" onclick="doAction({{$address->id}}, 'make_primary')" class="btn btn-xs btn-success">
															{{ Lang::get('myAddresses.make_primary')  }}</a>
														@endif
														<a href="{{ URL::action('AddressesController@getAddAddress', $address->id) }}" class="btn btn-xs blue" title="{{ Lang::get('myAddresses.edit')  }}"><i class="fa fa-edit"></i></a>
														<a href="javascript:;" onclick="doAction({{$address->id}}, 'delete')" class="btn btn-xs btn-danger" title="{{ Lang::get('common.delete')}}">
														<i class="fa fa-trash-o"></i></a>
													</td>
												</tr>
											@endforeach
										@else
											<tr>
												<td colspan="4"><p class="alert alert-info">{{ Lang::get('myAddresses.no_result') }}</p></td>
											</tr>
										@endif
									</tbody>
								</table>
							</div>
							@if(count($shipping_addresses) > 0)
								<div class="text-right">{{ $shipping_addresses->links() }}</div>
							@endif
						{{ Form::close() }}
						<!-- END: SHIPPING ADDRESS LIST -->

						{{ Form::open(array('id'=>'addresssActionfrm', 'method'=>'post', 'url' => URL::action('AddressesController@postAddressAction'))) }}
							{{ Form::hidden('addr_id', '', array('id' => 'addr_id')) }}
							{{ Form::hidden('addr_action', '', array('id' => 'addr_action')) }}
						{{ Form::close() }}

						<div id="dialog-product-confirm" class="confirm-dialog-delete" title="" style="display:none;">
							<span class="ui-icon ui-icon-alert"></span>
							<span id="dialog-product-confirm-content" class="show"></span>
						</div>

						<div id="address_confirm_primary" class="confirm-dialog-delete" title="" style="display:none;">
							<span class="ui-icon ui-icon-alert"></span>
							<span id="dialog-product-confirm-address" class="show"></span>
						</div>
					@else
						<div class="note note-info">
						   {{ Lang::get('myAddresses.list_empty') }}
						</div>
					@endif
				@endif
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
	            $('.fn_clsDropSearch').html('{{ Lang::get('product.show_search_filters') }} <i class="fa fa-caret-down"></i>');
	        } else {
	            $('.fn_clsDropSearch').html('{{ Lang::get('product.hide_search_filters') }} <i class="fa fa-caret-up"></i>');
	        }
	        return false;
	    });

	    function doAction(addr_id, selected_action)
		{
			if(selected_action == 'make_primary')
			{
				$('#dialog-product-confirm-address').html('{{ Lang::get('myAddresses.address_confirm_primary') }}');
			}
			if(selected_action == 'delete')
			{
				$('#dialog-product-confirm-address').html('{{ Lang::get('myAddresses.address_confirm_delete') }}');
			}

			$("#address_confirm_primary").dialog({ title: '{{ Lang::get('myAddresses.my_addresses') }}', modal: true,
				buttons: {
					"{{ Lang::get('common.yes') }}": function() {
					$(this).dialog("close");
					$('#addr_action').val(selected_action);
					$('#addr_id').val(addr_id);
					document.getElementById("addresssActionfrm").submit();
					}, "{{ Lang::get('common.cancel') }}": function() { $(this).dialog("close"); }
				}
			});
			return false;
			if(selected_action == 'delete')
			{
				$('#dialog-product-confirm-content').html('{{ Lang::get('myAddresses.address_confirm_delete') }}');
			}
			$("#dialog-product-confirm").dialog({ title: '{{ Lang::get('myAddresses.my_addresses') }}', modal: true,
				buttons: {
					"{{ Lang::get('common.yes') }}": function() {
						$(this).dialog("close");
						$('#addr_action').val(selected_action);
						$('#addr_id').val(addr_id);
						document.getElementById("addresssActionfrm").submit();
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