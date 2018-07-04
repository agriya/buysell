@extends('admin')
@section('content')
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

	<!-- PAGE TITLE STARTS -->
	<a class="pull-right mt10 btn btn-success btn-xs" href="{{ URL::action('AdminShippingTemplateController@getAdd') }}" title="{{ Lang::get('admin/shippingTemplates.add_shipping_template') }}">
    	<i class="fa fa-plus-circle"></i> {{ Lang::get('admin/shippingTemplates.add_shipping_template') }}
    </a>
    <h1 class="page-title">{{ Lang::get('admin/shippingTemplates.manage_shipping_templates') }}</h1>
    <!-- PAGE TITLE END -->

    {{ Form::open(array('id'=>'MemberSearchfrm', 'method'=>'get','class' => 'form-horizontal' )) }}
    {{ Form::hidden('page',Input::get('page'),array('id' => 'page')) }}
    	<div class="portlet box blue-madison">
            <!--- SEARCH TITLE STARTS --->
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-search"></i> {{ Lang::get('admin/shippingTemplates.search_shipping_template') }}
                </div>
                <div class="tools">
                    <a class="collapse" href="javascript:;"></a>
                </div>
            </div>
            <!--- SEARCH TITLE END --->

            <div class="portlet-body form">
                <div class="form-body" id="search_holder">
                    <div id="selSrchBooking">
                        <div class="form-group">
                            {{ Form::label('template_name', Lang::get('admin/shippingTemplates.template_name'), array('class' => 'control-label col-md-3')) }}
                            <div class="col-md-4">
                                {{ Form::text('template_name', Input::get("template_name"), array('class' => 'form-control')) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEARCH ACTIONS STARTS -->
                <div class="form-actions fluid">
                	<div class="col-md-offset-3 col-md-4">
                        <button type="submit" name="search_shipping_template" value="search_shipping_template" class="btn purple-plum">{{ Lang::get('common.search') }} <i class="fa fa-search bigger-110"></i></button>
                        <button type="reset" name="reset_search" value="reset_search" class="btn default" onclick="javascript:location.href='{{ URL::to('admin/shipping-template') }}'"><i class="fa fa-rotate-left bigger-110"></i> {{ Lang::get('common.reset') }}</button>
                    </div>
                </div>
                <!-- SEARCH ACTIONS END -->
            </div>
         </div>
    {{ Form::close() }}

	<div class="portlet box blue-hoki">
        <!--- TABLE TITLE STARTS --->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-list"></i> {{ Lang::get('admin/shippingTemplates.shipping_templates_list') }}
            </div>
        </div>
        <!--- TABLE TITLE END --->

        <div class="portlet-body">
            @if(sizeof($shipping_templates) > 0 )
                <div class="table-responsive">
                     @foreach($shipping_templates as $template)
						 <?php
                             $shippingTemplateService = new ShippingTemplateService();
                             $company_details = $shippingTemplateService->getFeeType($template->id);
                             //print_r($company_details);
                         ?>
                         <div class="mb30">
                             <table class="table table-bordered table-hover table-striped">
                                <thead>
                                    <tr>
                                        <td colspan="2" class="pull-ltor"><strong>{{ Lang::get('admin/shippingTemplates.template_name') }}:</strong>
                                            <a class="btn btn-primary btn-xs" href="{{ URL::action('AdminShippingTemplateController@getViewTemplate', $template->id) }}" title="{{ $template->template_name }}">{{ $template->template_name }}</a>
                                        @if($template->is_default == 1)
                                            <span class="label label-default pull-right">{{ Lang::get('admin/shippingTemplates.default_template') }}</span>
                                        @else
                                            <a class="btn btn-xs btn-info fn_dialog_confirm pull-right" id="{{ $template->id }}" href="{{ URL::action('AdminShippingTemplateController@getSetAsDefaultAction', $template->id).'?page='.Input::get('page').'&action=default' }}" action="Default" title="{{ Lang::get('common.set_as_default') }}">{{ Lang::get('common.set_as_default') }}</a>
                                        @endif
                                            <a class="btn blue btn-xs" href="{{ URL::action('AdminShippingTemplateController@getEdit', $template->id) }}" title="{{ Lang::get('common.edit') }}"><i class="fa fa-edit"></i></a>
                                            <a class="btn btn-xs red fn_dialog_confirm" href="{{ URL::action('AdminShippingTemplateController@getDeleteSippingTemplateAction', $template->id).'?action=delete' }}" action="Delete" title="{{ Lang::get('common.tem_delete') }}"><i class="fa fa-trash-o"></i></a>
                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($company_details as $key => $c_name)
                                        @if($c_name !='')
                                            <tr>
                                                <td width="128">{{ ucfirst($key) }}</td>
                                                <td>{{ $c_name }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                         </div>
                     @endforeach
                </div>
                <!--- PAGINATION STARTS --->
                <div class="text-right">
                    {{ $shipping_templates->appends(array('template_name' => Input::get('template_name'),'search_shipping_template' => Input::get('search_shipping_template')))->links() }}
                </div>
                <!--- PAGINATION END --->
            @else
                <div class="note note-info mar0">{{ Lang::get('admin/shippingTemplates.shipping_templates_not_found') }}</div>
            @endif
            <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
    	</div>
    </div>
@stop

@section('script_content')
	<script type="text/javascript">
		var cfg_site_name = "{{trans('shippingTemplates.shipping_template')}}" ;
		$(window).load(function(){
			  $(".fn_dialog_confirm").click(function(){
					var atag_href = $(this).attr("href");
					var action = $(this).attr("action");
					var cmsg = "";
					//alert(action); return false;
					switch(action){
						case "Delete":
							cmsg = "{{trans('shippingTemplates.confirm_delete_shipping_template')}}";

							break;
						case "Default":
							cmsg = "{{trans('shippingTemplates.confirm_set_default_shipping_template')}}";
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

		/*
		 $(".defult_action").click(function(ele){
		 var page = "{{ Input::get('page') }}";
		 var post_action_url = "{{ URL::to('admin/shipping-template/index/set-as-default-action') }}";
			var id = this.id;
			alert(id);
			var post_data = 'id='+id;
			$.ajax({
				type: 'GET',
				url: post_action_url,
				data: post_data,
				success: function(data){
					if(data)
					{
						//window.location = "{{ URL::to('admin/shipping-template/index').'?page='}}"+page;
					}
				}
			});
		 });
		*/
    </script>
@stop