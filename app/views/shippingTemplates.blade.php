@extends('base')
@section('content')
	<div class="row">
		<div class="col-md-2 clearfix">
			<!-- MANAGE ACCOUNT STARTS -->
			@include('myaccount.myAccountMenu')
			<!-- MANAGE ACCOUNT ENDS -->
		</div>

		<div class="col-md-10">
			<!-- PAGE TITLE STARTS -->
			<div class="responsive-pull-none">
				<a class="pull-right btn btn-success btn-xs responsive-btn-block" href="{{ URL::action('ShippingTemplateController@getAdd') }}" title="{{ Lang::get('shippingTemplates.add_shipping_template') }}"><i class="fa fa-plus"></i> {{ Lang::get('shippingTemplates.add_shipping_template') }}</a>
				<h1>{{ Lang::get('shippingTemplates.manage_shipping_templates') }}</h1>
			</div>
			<!-- PAGE TITLE END -->

			<!-- NOTIFICATIONS STARTS -->
			@include('notifications')
			<!-- NOTIFICATIONS END -->

			@if(Session::has('error_message') && Session::get('error_message') != '')
				<div class="note note-danger">{{ Session::get('error_message') }}</div>
				<?php Session::forget('error_message'); ?>
			@endif
			@if(Session::has('success_message') && Session::get('success_message') != '')
				<div class="note note-success">{{ Session::get('success_message') }}</div>
				<?php Session::forget('success_message'); ?>
			@endif

			<div class="well">
				{{ Form::open(array('id'=>'MemberSearchfrm', 'method'=>'get','class' => 'form-horizontal' )) }}
					{{ Form::hidden('page',Input::get('page'),array('id' => 'page')) }}
					<!-- SEARCH ACTIONS STARTS -->
					<div id="search_holder" class="portlet bg-form">
						<div class="portlet-title">
							<div class="caption">
								{{ Lang::get('shippingTemplates.search_shipping_template') }}
							</div>
							<div class="tools">
								<a class="collapse" href="javascript:;"></a>
							</div>
						</div>

						<div id="selSrchBooking" class="portlet-body">
							<div class="form-group">
								{{ Form::label('template_name', Lang::get('shippingTemplates.template_name'), array('class' => 'control-label col-md-2')) }}
								<div class="col-md-4">
									{{ Form::text('template_name', Input::get("template_name"), array('class' => 'form-control')) }}
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-offset-2 col-md-5">
									<button type="submit" name="search_shipping_template" value="search_shipping_template" class="btn purple-plum">
									<i class="fa fa-search"></i> {{ Lang::get('common.search') }}</button>
									<button type="reset" name="reset_search" value="reset_search" class="btn default" onclick="javascript:location.href='{{ URL::to('shipping-template') }}'">
									<i class="fa fa-rotate-left"></i> {{ Lang::get('common.reset') }}</button>
								</div>
							</div>
						</div>
					</div>
					<!-- SEARCH ACTIONS END -->
				{{ Form::close() }}

				<h2 class="title-one">{{ Lang::get('shippingTemplates.shipping_templates_list') }}</h2>
				@if(sizeof($shipping_templates) > 0 )
					@foreach($shipping_templates as $template)
						<?php
							$shippingTemplateService = new ShippingTemplateService();
							$company_details = $shippingTemplateService->getFeeType($template->id);
							//print_r($company_details);
						 ?>
						 <div class="margin-bottom-30 table-responsive">
							 <table class="table table-bordered table-hover table-striped">
								<thead>
									<tr>
										<td colspan="2" class="ship-name"><strong>{{ Lang::get('shippingTemplates.template_name') }}:</strong>
											<a class="btn green btn-xs" href="{{ URL::action('ShippingTemplateController@getViewTemplate', $template->id) }}" title="{{ $template->template_name }}">{{ $template->template_name }}</a>
										@if($template->is_default == 1)
											<span class="label label-default pull-right">{{ Lang::get('shippingTemplates.default_template') }}</span>
										@else
											<a class="btn btn-xs btn-info fn_dialog_confirm pull-right" id="{{ $template->id }}" href="{{ URL::action('ShippingTemplateController@getSetAsDefaultAction', $template->id).'?page='.Input::get('page').'&action=default' }}" action="Default" title="{{ Lang::get('common.set_as_default') }}">{{ Lang::get('shippingTemplates.set_as_default') }}</a>
										@endif
											<a class="btn blue btn-xs" href="{{ URL::action('ShippingTemplateController@getEdit', $template->id) }}" title="{{ Lang::get('common.edit') }}"><i class="fa fa-edit"></i></a>
											<a class="btn btn-xs red fn_dialog_confirm" href="{{ URL::action('ShippingTemplateController@getDeleteSippingTemplateAction', $template->id).'?action=delete' }}" action="Delete" title="{{ Lang::get('common.tem_delete') }}"><i class="fa fa-trash-o"></i></a>
										</td>
									</tr>
								</thead>

								<tbody>
									@foreach($company_details as $key => $c_name)
										@if($c_name !='')
											<tr>
												<td width="128">{{ trans('shippingTemplates.'.$key) }}</td>
												<td>{{ $c_name }}</td>
											</tr>
										@endif
									@endforeach
								</tbody>
							</table>
						</div>
					@endforeach
					<!--- PAGINATION STARTS --->
					<div class="text-right">
						{{ $shipping_templates->appends(array('template_name' => Input::get('template_name'),'search_shipping_template' => Input::get('search_shipping_template')))->links() }}
					</div>
					<!--- PAGINATION END --->
				@else
					<div class="note note-info margin-0">{{ Lang::get('shippingTemplates.shipping_templates_not_found') }}</div>
				@endif
				<div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		var page_name = "shipping_templates_list";
		var cfg_site_name = "{{trans('shippingTemplates.shipping_template')}}";
		var confirm_delete_shipping_template = "{{trans('shippingTemplates.confirm_delete_shipping_template')}}";
		var confirm_set_default_shipping_template = "{{trans('shippingTemplates.confirm_set_default_shipping_template')}}";
		var ok_label = "{{trans('common.ok')}}";
		var cancel_label = "{{trans('common.cancel')}}";
    </script>
@stop