@extends('admin')
@section('content')
	<!-- BEGIN: INFO BLOCK -->
	@if (Session::has('success_msg') && Session::get('success_msg') != "")
		<div class="note note-success">{{ Session::get('success_msg') }}</div>
	@endif

	@if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
    @endif
    <!-- END: INFO BLOCK -->

	<!-- BEGIN: CSV FILE DETAILS -->
	@if (Session::has('msg_arr') && Session::get('msg_arr') != "")
		<div class="note note-success">{{	trans('admin/newsletterSubscriber.process_completed') }}</div>
		<div class="well">
			<p>{{ trans('admin/newsletterSubscriber.results_info') }}</p>
			<p class="mar0"><strong class="bigger-110">{{ Session::get('msg_arr')['total_subscribers'] }}</strong> {{ trans('admin/newsletterSubscriber.total_subscribers') }}</p>

			@if (Session::get('msg_arr')['imported_subscribers'] > 0)
				<p class="mar0"><strong class="bigger-110">{{ Session::get('msg_arr')['imported_subscribers'] }}</strong> {{ trans('admin/newsletterSubscriber.processed_subscribers') }}</p>
			@endif

			@if (Session::get('msg_arr')['duplicate_subscribers'] > 0)
				<p><strong class="bigger-110">{{ Session::get('msg_arr')['duplicate_subscribers'] }}</strong> {{ trans('admin/newsletterSubscriber.duplicate_subscribers') }}</p>
				@foreach(Session::get('msg_arr')['duplicate_emails'] as $duplicate_email)
					<p class="label label-success">{{ $duplicate_email }}</p>
				@endforeach
			@endif

			@if (Session::get('msg_arr')['failed_subscribers'] > 0)
				<p class="mar0"><strong class="bigger-110">{{ Session::get('msg_arr')['failed_subscribers'] }}</strong> {{ trans('admin/newsletterSubscriber.failed_subscribers') }}</p>
				<p class="text-danger mt10">{{ trans('admin/newsletterSubscriber.invalid_email') }}</p>
				@foreach(Session::get('msg_arr')['failed_emails'] as $failed_email)
					<div>{{ $failed_email }}</div>
				@endforeach
			@endif
		</div>
	@endif
	<!-- END: CSV FILE DETAILS -->

	{{ Form::open(array('id'=>'newsletterSubscriberFrm', 'url' => Url::to("admin/newsletter-subscriber/list"), 'method'=>'post','class' => 'form-horizontal','files' => true )) }}
		<div class="portlet box blue-madison">
			<!-- BEGIN: PAGE TITLE -->
			<div class="portlet-title">
				<div class="caption"><i class="fa fa-list-ul"><sup class="fa fa-envelope-o font11"></sup></i>{{ trans('admin/newsletterSubscriber.email_list') }}</div>
				<div class="tools"><a class="collapse" href="javascript:;"></a></div>
			</div>
			<!-- END: PAGE TITLE -->

			<!-- BEGIN: SEARCH BAR -->
			<div class="portlet-body form" style="display: block;">
				<div id="selSrchBooking">
					<div class="form-body">
						<fieldset>
							<div class="form-group">
                                <div class="col-md-5">
                                    {{ Form::textarea('subscribers_list', null, array('placeholder' => trans('admin/newsletterSubscriber.paste_info'), 'class'=>'form-control', 'rows'=>'7')) }}
                                    <label class="error pull-left">{{ $errors->first('subscribers_list') }}</label>
                                    <small class="text-muted">{{ trans('admin/newsletterSubscriber.separated_by_new_line') }}</small>
                                </div>
                            </div>
						</fieldset>
					</div>

					<div class="form-actions fluid">
						<div class="col-md-5">
							<button type="submit" name="import_copypaste" value="import_copypaste" class="btn blue">
								<i class="fa fa-download mr5"></i> {{trans('admin/newsletterSubscriber.import')}}
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- END: SEARCH BAR -->
	{{ Form::close() }}

	{{ Form::hidden('page',Input::get('page'),array('id' => 'page')) }}
	{{ Form::open(array('id'=>'newsletterSubscriberImportFrm', 'url' => Url::to("admin/newsletter-subscriber/list"), 'method'=>'post','class' => 'form-horizontal','files' => true )) }}
		<div class="portlet box blue-madison">
			<!-- BEGIN: PAGE TITLE -->
			<div class="portlet-title">
				<div class="caption"><i class="fa fa-file-text-o"><sup class="fa fa-upload font11"></sup></i>{{ trans('admin/newsletterSubscriber.upload_csv_file') }}</div>
				<div class="tools"><a class="collapse" href="javascript:;"></a></div>
			</div>
			<!-- END: PAGE TITLE -->

			<!-- BEGIN: SEARCH BAR -->
			<div class="portlet-body form" style="display: block;">
				<div id="selSrchBooking">
					<div class="form-body">
						<fieldset>
							<div class="form-group">
                                <div class="col-md-5">
                                    {{ Form::file('subscribers_importlist',$attributes = array('class' => 'filestyle', 'data-buttonText' => Lang::get('common.choose_file'))) }}
                                    <label class="error mar0" for="subscribers_importlist" generated="true">{{ $errors->first('subscribers_importlist') }}</label>
                                    <p class="text-muted mar0"><small>{{ trans('admin/newsletterSubscriber.file_format_info') }}</small></p>
                                </div>
                            </div>
						</fieldset>
					</div>

					<div class="form-actions fluid">
						<div class="col-md-5">
							<button type="submit" name="import_csv" value="import_csv" class="btn blue"><i class="fa fa-download mr5"></i>
								{{trans('admin/newsletterSubscriber.import')}}
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- END: SEARCH BAR -->
	{{ Form::close() }}

	{{ Form::open(array('url' => URL::action('AdminNewsletterSubscriberController@getChangeStatusIds'), 'id'=>'subscriberListFrm', 'method'=>'get', 'class' => 'form-horizontal' )) }}
	{{ Form::hidden('page',Input::get('page'),array('id' => 'page')) }}
		<div class="portlet blue-hoki box">
			<!-- BEGIN: PAGE TITLE -->
			<div class="portlet-title">
				<div class="caption"><i class="fa fa-tasks"><sup class="fa fa-user font11"></sup></i>{{ trans('admin/newsletterSubscriber.title') }}</div>
			</div>
			<!-- END: PAGE TITLE -->

			<!--  BEGIN: TABLE PORTLET-->
			<div class="portlet-body clearfix">
				<div class="table-scrollable">
					<table class="table table-striped table-bordered table-hover api-log">
						<thead>
							<tr>
								<th width="40">{{Form::checkbox('select_al','yes',false,array('id' => 'select_all', 'class' => 'group-checkable'))}}</th>
								<th>{{ trans('admin/newsletterSubscriber.email_id') }}</th>
								<th>{{ trans('admin/newsletterSubscriber.first_name') }}</th>
								<th>{{ trans('admin/newsletterSubscriber.last_name') }}</th>
								<th>{{ trans('admin/newsletterSubscriber.ip_addr') }}</th>
                                <th>{{ trans('admin/newsletterSubscriber.subscribed_date') }}</th>
                                <th>{{ trans('common.status') }}</th>
                                <th>{{ trans('common.action') }}</th>
							</tr>
						</thead>
						<tbody>
							@if(count($details) > 0)
								@foreach($details as $subdet)
									<tr>
										<td>{{Form::checkbox('ids[]',$subdet->id, false, array('class' => 'checkboxes fn_checkbox_class') )}}</td>
                                        <td>{{ $subdet->email }}</td>
                                        <td>{{ $subdet->first_name }}</td>
                                        <td>{{ $subdet->last_name }}</td>
                                        <td>{{ $subdet->ip }}</td>
                                        <td class="text-muted"><div class="wid100">{{ CUtil::FMTDate($subdet->date_added, 'Y-m-d', '') }}</div></td>
                                        <td>
											<?php
                                                if(count($subdet) > 0)
                                                {
                                                    if($subdet['status'] == 'active')
                                                    {
                                                        $lbl_class = "fa-check green bigger-120";
                                                    }
                                                    elseif($subdet['status'] == 'inactive')
                                                    {
                                                        $lbl_class = "fa-ban red bigger-120";
                                                    }
                                                }
                                            ?>
                                            <p><i class="fa {{ $lbl_class }}" title="{{ ucwords($subdet->status) }}"></i></p>
											<p>
												@if($subdet->status == 'active')
													<a href="{{ URL::to('admin/newsletter-subscriber/change-status').'?action=inactive&subscriber_id='.$subdet->id.'&page='.Input::get('page') }}" class="fn_dialog_confirm label label-danger" action="Inactive" title="{{ trans('common.inactive') }}">{{ trans('common.inactive') }}</a>
												@else
													<a href="{{ URL::to('admin/newsletter-subscriber/change-status').'?action=active&subscriber_id='.$subdet->id.'&page='.Input::get('page') }}" class="fn_dialog_confirm label label-success" action="Active" title="{{ trans('common.active') }}">{{ trans('common.active') }}</a>
												@endif
											</p>
                                        </td>
                                        <td class="status-btn">
											<a href="{{ URL::to('admin/newsletter-subscriber/change-status').'?action=delete&subscriber_id='.$subdet->id.'&page='.Input::get('page') }}" class="fn_dialog_confirm btn btn-xs red" action="Delete" title="{{ trans('common.delete') }}"><i class="fa fa-trash"></i></a>
                                        </td>
									</tr>
								@endforeach
							@else
								<tr>
									<td colspan="8"><p class="alert alert-info">{{ trans('admin/newsletterSubscriber.no_records_found') }}</p></td>
								</tr>
							@endif
						</tbody>
					</table>
				</div>
				@if(count($details) > 0)
				<div class="clearfix">
                    <p class="pull-left mt10 mr10">
                        {{Form::select('action',$actions,'',array('class'=>'form-control bs-select input-medium', 'id'=>'action'))}}
                    </p>
                    <p class="pull-left mt10">
                        <input type="submit" value="{{ trans('common.submit') }}" class="btn green" id="page_action" name="page_action">
                    </p>
                </div>
                @endif
				<div class="row paginate-blk">
					@if(count($details) > 0)
						<div class="col-md-6 col-sm-6 col-xs-12">
							<div class="dataTables_paginate paging_bootstrap">
								<div class="mt15">
									{{ $details->appends(array('page' =>  Input::get('page')))->links() }}
								</div>
							</div>
						</div>
					@endif
				</div>
			</div>
			<!--  END: TABLE PORTLET -->
		</div>
    {{ Form::close() }}
    <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
@stop

@section('script_content')
	<script type="text/javascript">
		var common_ok_label = "{{ trans('common.yes') }}" ;
		var common_no_label = "{{ trans('common.cancel') }}" ;
		var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
        $(document).ready(function () {
            $("#perpage").change(function(){
                $("#subscriberListFrm").submit();
            });
        });

        var mes_required = "{{trans('auth/form.required')}}";
        $("#newsletterSubscriberFrm").validate({
            rules: {
                subscribers_list: {
                    required: true,
                }
            },
            messages: {
                subscribers_list: {
                    required: mes_required
                }
            },
			highlight: function (element) { // hightlight error inputs
               $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            unhighlight: function (element) { // revert the change done by hightlight
                $(element)
                    .closest('.form-group').removeClass('has-error'); // set error class to the control group
            }
        });

        $("#newsletterSubscriberImportFrm").validate({
            rules: {
                subscribers_importlist: {
                    required: true,
                }
            },
            messages: {
                subscribers_importlist: {
                    required: mes_required
                }
            },
        });

        $(window).load(function(){
        	$(".fn_dialog_confirm").click(function(){
				var atag_href = $(this).attr("href");
				var action = $(this).attr("action");
				var cmsg = "";
				var txtDelete = action;

				var txtCancel = common_no_label;
				var buttonText = {};
				buttonText[txtDelete] = function(){
											Redirect2URL(atag_href);
											$( this ).dialog( "close" );
										};
				buttonText[txtCancel] = function(){
											$(this).dialog('close');
										};
				switch(action){
					case "Active":
						cmsg = "{{ trans('admin/newsletterSubscriber.subscriber_activate_confirm_msg') }}";

						break;
					case "Inactive":
						cmsg = "{{ trans('admin/newsletterSubscriber.subscriber_deactivate_confirm_msg') }}";
						break;
					case "Delete":
						cmsg = "{{ trans('admin/newsletterSubscriber.subscriber_delete_confirm_msg') }}";
						break;
				}
				$("#fn_dialog_confirm_msg").html(cmsg);
				$("#fn_dialog_confirm_msg").dialog({
					resizable: false,
					height:240,
					width: 360,
					modal: true,
					title: cfg_site_name,
					buttons:buttonText
				});
				return false;
			});
		});

		$('#select_all').change(function() {
			var checkboxes = $(this).closest('form').find(':checkbox');
			if($(this).is(':checked')) {
				checkboxes.each(function(){
					$(this).prop('checked', true);
					$(this).parent().addClass('checked');
				});
			}
			else
			{
				checkboxes.each(function(){
					$(this).prop('checked', false);
					$(this).parent().removeClass('checked');
				});
			}
		});

		$(window).load(function(){
		  $("#page_action").click(function(){
				var cmsg ="";
				if ($('.fn_checkbox_class:checked').length <= 0) {
					bootbox.alert("{{ trans('common.select_the_checkbox') }}");
					return false;
				}
				if ($('#action').val() =='' ) {
					bootbox.alert("{{ trans('common.please_select_an_action') }}");
					return false;
				}
				var action = $('#action').val();
				if(action == 'active') {
					var cmsg ="{{ Lang::get('admin/newsletterSubscriber.subscriber_activate_confirm_msg') }}";
				}
				if(action == 'inactive') {
					var cmsg ="{{ Lang::get('admin/newsletterSubscriber.subscriber_deactivate_confirm_msg') }}";
				}
				if(action == 'delete') {
					var cmsg ="{{ Lang::get('admin/newsletterSubscriber.subscriber_delete_confirm_msg') }}";
				}
				bootbox.dialog({
					message: cmsg,
				  	title: cfg_site_name,
				  	buttons: {
						danger: {
				      		label: "{{ trans('common.ok')}}",
				      		className: "btn-danger",
				      		callback: function() {
				      			$('#subscriberListFrm').submit();
					    	}
				    	},
				    	success: {
				      		label: "{{ trans('common.cancel')}}",
				      		className: "btn-default",
				    	}
				  	}
				});
				return false;
			});
		});
    </script>
@stop
