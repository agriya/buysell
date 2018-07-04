@extends('admin')
@section('content')
	<!-- BEGIN: NOTIFICATIONS -->
    @include('notifications')
    <!-- END: NOTIFICATIONS -->

    <!-- BEGIN: SUCCESS INFO -->
    @if(Session::has('success_message') && Session::get('success_message') != '')
        <div class="note note-success">{{ Session::get('success_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif

    @if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif
    <!-- END: SUCCESS INFO -->

	<!-- BEGIN: PAGE TITLE -->
	<!--<a class="pull-right mt10 btn btn-success btn-xs" href="{{ URL::action('AdminTaxationsController@getAddTaxation') }}" title="{{ Lang::get('admin/taxation.add_taxation') }}">
    	<i class="fa fa-plus-circle"></i> {{ Lang::get('admin/staticPage.add_taxation') }}
    </a>-->
    <h1 class="page-title">{{Lang::get('admin/sellerRequest.seller_request')}}</h1>
    <!-- END: PAGE TITLE -->
    {{ Form::open(array('url' => Url::action('AdminSellerRequestController@getIndex'), 'id'=>'sellerRequestFrm', 'method'=>'get','class' => 'form-horizontal' )) }}
    	<div class="portlet box blue-madison mb40">
            <!-- BEGIN: SEARCH TITLE -->
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-search"></i> {{ Lang::get('admin/sellerRequest.search_request') }}
                </div>
            </div>
            <!-- END: SEARCH TITLE -->

            <div class="portlet-body form">
                <div id="search_holder">
                    <div id="selSrchScripts">
                    	<div class="form-body">
                        	<div class="row">
                                <div class="col-md-6">
                                	<div class="form-group">
                                        {{ Form::label('user_name', Lang::get('admin/sellerRequest.user_name'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
                                            {{ Form::text('user_name', Input::get("user_name"), array('class' => 'form-control')) }}
                                            <label class="error" for="search_user_name" generated="true">{{$errors->first('user_name')}}</label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{ Form::label('request_date', Lang::get('admin/sellerRequest.request_date'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
                                        	<div class="input-group date date-picker input-daterange" data-date-format="dd-mm-yyyy">
			                                    {{ Form::text('from_date', Input::old('from_date', Input::get('from_date')), array('id'=>"from_date", 'class'=>'form-control', 'maxlength'=>'100')) }}
												<label for="date_added_to" class="input-group-addon">{{ Lang::get('common.to') }}</label>
			                                    {{ Form::text('to_date', Input::old('to_date', Input::get('to_date')), array('id'=>"to_date", 'class'=>'form-control', 'maxlength'=>'100')) }}
			                                </div>
			                                <label class="error" for="request_date" generated="true">{{$errors->first('from_date')}}</label>
                                        </div>
                                    </div>
                               </div>
                               <div class="col-md-6">
                               		<div class="form-group">
                                        {{ Form::label('request_status', Lang::get('admin/sellerRequest.request_status'), array('class' => 'col-md-4 control-label')) }}
                                        <div class="col-md-6">
                                            {{ Form::select('request_status', $status, Input::get("request_status"), array('class' => 'form-control bs-select')) }}
                                            <label class="error" for="search_user_name" generated="true">{{$errors->first('search_status')}}</label>
                                        </div>
                                    </div>
                                </div>
                           	</div>
                         </div>
                        <div class="form-actions fluid">
                            <div class="col-md-offset-2 col-md-5">
                                <button type="submit" name="search_submit" value="search_submit" class="btn purple-plum">
                                	{{ trans("common.search") }} <i class="fa fa-search"></i>
                                </button>
                                <button type="reset" name="search_reset" value="search_reset" class="btn default" onclick="javascript:location.href='{{ URL::action('AdminSellerRequestController@getIndex') }}'"><i class="fa fa-rotate-left bigger-110"></i> {{ trans("common.reset")}}</button>
                            </div>
						</div>
                    </div>
                </div>
            </div>
     	</div>
    {{ Form::close() }}

	<div class="portlet box blue-hoki">
        <!-- BEGIN: TABLE TITLE -->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-tags new-icon"><sup class="fa fa-question-circle"></sup></i> {{ Lang::get('admin/sellerRequest.seller_request_list') }}
            </div>
        </div>
        <!-- END: TABLE TITLE -->

        <div class="portlet-body">
            @if(count($seller_requests) > 0 )
            	<!-- BEGIN: Request LIST -->
                {{ Form::open(array('url'=>URL::action('AdminSellerRequestController@postBulkAction'),'id'=>'listFrm', 'method'=>'post','class' => 'form-horizontal' )) }}
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th width="40">{{Form::checkbox('select_al','yes',false,array('id' => 'select_all', 'class' => 'group-checkable'))}}</th>
                                    <th>{{ Lang::get('admin/sellerRequest.user_details') }}</th>
                                    <th class="col-md-2">{{ Lang::get('admin/sellerRequest.request_date') }}</th>
                                    <th class="col-md-4">{{ Lang::get('admin/sellerRequest.message') }}</th>
                                    <th>{{ Lang::get('admin/sellerRequest.status') }}</th>
                                    <th class="col-md-1">{{ Lang::get('admin/sellerRequest.action') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($seller_requests as $request)
                                    <?php $user_details = CUtil::getUserDetails($request['user_id']); ?>
                                    <tr>
                                        <td>
											@if($request->request_status != 'Allowed')
												{{Form::checkbox('ids[]',$request->id, false, array('class' => 'checkboxes fn_checkbox_class') )}}
											@else
												{{Form::checkbox('ids[]',$request->id, false, array('class' => 'checkboxes', 'disabled'=>'disabled') )}}
											@endif
										</td>
                                        <td>
                                        	<div class="dl-horizontal-new dl-horizontal">
	                                        	<dl>
													<dt>{{ Lang::get('admin/sellerRequest.user_name') }}</dt>
													<dd><span>
														<a href="{{ URL::to('admin/users/user-details').'/'.$user_details['id'] }}">{{$user_details['display_name']}}</a>
														(<a class="text-muted" href="{{ URL::to('admin/users/user-details').'/'.$user_details['id'] }}">{{$user_details['user_code']}}</a>)
													</span></dd>
												</dl>
												<dl>
													<dt><span title="{{ Lang::get('admin/sellerRequest.request_email') }}">{{ Lang::get('admin/sellerRequest.request_email') }}</span></dt>
													<dd><span>{{$request->email}}</span></dd>
												</dl>
												<dl>
													<dt>{{ Lang::get('admin/sellerRequest.reply_sent') }}</dt>
													<dd>
														<?php
															if($request->reply_sent == 'Yes') {
																$lbl_class = "text-success";
															}
															elseif($request->reply_sent == 'No') {
																$lbl_class = "text-danger";
															}
															else {
																$lbl_class = "text-default";
															}
														?>
														<span class="{{ $lbl_class }}">{{trans('common.'.strtolower($request->reply_sent)) }}</span>
													</dd>
												</dl>
											</div>
										</td>
                                        <td>{{ CUtil::FMTDate($request->created_at, "Y-m-d H:i:s", "") }}</td>
                                        <td><div class="wid-">{{ $request->request_message }}</div></td>
                                        <td>
										   <?php
												if($request->request_status == 'NewRequest') {
													$lbl_class = " label-info";
												}
												elseif($request->request_status == 'Allowed') {
													$lbl_class = "label-success";
												}
												elseif($request->request_status == 'Rejected') {
													$lbl_class = "label-danger";
												}
												elseif($request->request_status == 'ConsiderLater') {
													$lbl_class = "label-primary";
												}
												else {
													$lbl_class = "label-default";
												}
											?>
											<span class="label {{ $lbl_class }}" id="status_txt_{{$request['id']}}">
												{{Lang::get('admin/sellerRequest.status_txt_'.$request->request_status)}}
											</span>
                                        </td>

                                        <td class="status-btn">
											@if($request->request_status != 'Allowed')
												<a class="btn btn-xs green fn_dialog_confirm" title="{{trans('common.allow')}}" href="{{URL::action('AdminSellerRequestController@getRequestAction').'?request_id='.$request->id.'&action=allow'}}" action="allow"><i class="fa fa-check"></i></a>
												<a class="btn btn-primary btn-xs" title="{{trans('common.reply')}}" data-toggle="modal" data-target="#myModal_{{$request->id}}"><i class="fa fa-reply"></i></a>
												<div class="modal fade" id="myModal_{{$request->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
													<div class="modal-dialog">
														<div class="modal-content">
															<div class="modal-header">
																<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">
																</span><span class="sr-only">{{trans('common.close')}}</span></button>
																<h4 class="modal-title" id="myModalLabel">{{Config::get('generalConfig.site_name')}}</h4>
															</div>
															<div class="modal-body">
																<div class="form-horizontal">
																	<div class="form-group">
																		<label class="control-label col-sm-3">{{Lang::get('admin/sellerRequest.request_email')}}</label>
																		<div class="col-sm-3 mt8">{{$request->email}}</div>
																	</div>
																	<div class="form-group">
																		<label class="control-label col-sm-3">{{Lang::get('admin/sellerRequest.comments')}}</label>
																		<div class="col-sm-8">
																			{{ Form::textarea('comments_'.$request->id,'',array('class' => 'form-control', 'id' => 'comments_'.$request->id, 'rows' => '4', 'cols' => '30')) }}</div>
																	</div>
																</div>
															</div>
															<div class="modal-footer">
																<div class="form-group">
																	<div class="col-sm-offset-3 col-sm-8">
																		<button type="button" data-requestid="{{$request->id}}" class="btn green js-submit-reply">
																		<i class="fa fa-check"></i> {{trans('common.save_changes')}}</button>
																		<button type="button" class="btn default" data-dismiss="modal">
																		<i class="fa fa-times"></i> {{trans('common.close')}}</button>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											@else
												{{'N/A'}}
											@endif
											<!--<a href="javascript:;" onclick="editrequest({{$request['id']}})" class="btn btn-xs blue" title="{{ Lang::get('admin/sellerRequest.edit') }}">
											<i class="fa fa-edit"></i></a>-->
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="6">
                                        <p class="pull-left mt10 mr20">
                                            {{Form::select('action',$actions,'',array('class'=>'form-control', 'id'=>'action'))}}
                                        </p>
                                        <p class="pull-left margin-top-10 margin-right-10">
                                            <input type="submit" value="{{trans('common.submit')}}" class="btn green" id="page_action" name="page_action">
                                        </p>
                                        <p class="pull-left mt10">
                                        	@if($view_type == 'new')
												<a class="btn default blue" href="{{URL::action('AdminSellerRequestController@getIndex').'?view_type=all'}}">
												<i class="fa fa-eye"></i> {{trans('common.all')}}</a>
											@else
												<a class="btn default red" href="{{URL::action('AdminSellerRequestController@getIndex')}}">
												<i class="fa fa-eye"></i> {{trans('common.not_allowed')}}</a>
											@endif
										</p>
                                    </td>
                                </tr>
                            </tbody>
                         </table>
                    </div>
                 {{Form::close()}}
                 <!-- END: request LIST -->

                <!-- BEGIN: PAGINATION -->
                <div class="text-right">
                    {{ $seller_requests->appends(array('user_name' => Input::get('user_name'), 'from_date' => Input::get('from_date'),
						'to_date' => Input::get('to_date'), 'request_status' => Input::get('request_status'),
						'view_type' => Input::get('view_type')))->links() }}
                </div>
                <!-- END: PAGINATION -->
            @else
                <div class="alert alert-info mar0">{{ Lang::get('admin/sellerRequest.no_request_found') }}</div>
            @endif
            <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
    	</div>
    </div>

    {{ Form::open(array('id'=>'actionfrm', 'method'=>'post', 'url' => URL::action('AdminSellerRequestController@postRequestAction'))) }}
    {{ Form::hidden('request_id', '', array('id' => 'action_request_id')) }}
    {{ Form::hidden('comment', '', array('id' => 'reply_message')) }}
    {{ Form::hidden('action', '', array('id' => 'request_action')) }}
    {{ Form::close() }}

	<div id="dialog-confirm" title="" style="display:none;">
	    <span class="ui-icon ui-icon-alert"></span>
		<span id="dialog-confirm-content" class="show ml15"></span>
	</div>
@stop

@section('script_content')
	<script type="text/javascript">
		var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
		var request_actions_url = '{{URL::action('AdminSellerRequestController@postBulkAction')}}';

		$('.js-submit-reply').click(function(){
			var request_id = $(this).data('requestid');

			var comment = $('#comments_'+request_id).val();

			$('#action_request_id').val(request_id);
			$('#reply_message').val(comment);
			$('#request_action').val('send_reply');
			$('#actionfrm').submit();
		})

		$(function() {
            $('#from_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });
            $('#to_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });
        });

		$(document).ready(function(){
			var val = $('#js-page-type').val();
			if(val == 'external')
			{
				$('#static-content-div').hide();
				$('#external-link-div').show();
			}
			else
			{
				$('#static-content-div').show();
				$('#external-link-div').hide();
			}
		})
		$('#js-page-type').change(function(){
			var val = $(this).val();

			if(val == 'external')
			{
				$('#static-content-div').hide();
				$('#external-link-div').show();
			}
			else
			{
				$('#static-content-div').show();
				$('#external-link-div').hide();
			}
		})

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
				error_found = false;
				if ($('.fn_checkbox_class:checked').length <= 0) {
					$('#dialog-confirm-content').html("{{ trans('common.select_the_checkbox') }}");
					error_found = true;
					//bootbox.alert("{{ trans('common.select_the_checkbox') }}");
					//return false;
				}
				if ($('#action').val() =='' ) {
					$('#dialog-confirm-content').html("{{ trans('common.please_select_an_action') }}");
					error_found = true;
					//bootbox.alert("{{ trans('common.please_select_an_action') }}");
					//return false;
				}
				if(error_found == true){
					$("#dialog-confirm").dialog({ title:  cfg_site_name, modal: true,
						buttons: {
							"{{ trans('common.cancel') }}": function() {
								$(this).dialog("close");
							}
						}
					});
					return false;
				}
				var action = $('#action').val();
				var cmsg ="{{ Lang::get('admin/sellerRequest.set_as_new_request_confirm') }}";
				if(action == 'Allowed' || action == 'Rejected') {
					replace_txt = (replace_txt == 'Allowed') ? 'Allow' : 'Reject';
					cmsg ="{{ Lang::get('admin/sellerRequest.seller_request_action_confirm') }}";
					cmsg = cmsg.replace("VAR_ACTION", replace_txt);
				}
				bootbox.dialog({
					message: cmsg,
				  	title: cfg_site_name,
				  	buttons: {
						danger: {
				      		label: "{{ trans('common.ok')}}",
				      		className: "btn-danger",
				      		callback: function() {
				      			$('#listFrm').submit();
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

		var cfg_site_name = '{{ Config::get('generalConfig.site_name') }}' ;
		$(window).load(function(){
			  $(".fn_dialog_confirm").click(function(){
					var atag_href = $(this).attr("href");
					var action = $(this).attr("action");
					var cmsg = "";
					//alert(action); return false;
					switch(action){
						case "allow":
							cmsg = '{{ Lang::get('admin/sellerRequest.seller_request_action') }}';
							break;
					}
					bootbox.dialog({
						message: cmsg,
					  	title: cfg_site_name,
					  	buttons: {
							danger: {
					      		label: "{{ trans('common.ok')}}",
					      		className: "btn-danger",
					      		callback: function() {
					      			Redirect2URL(atag_href);
					      			bootbox.hideAll();
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