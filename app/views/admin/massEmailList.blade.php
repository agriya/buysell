@extends('admin')
@section('content')
	@if (Session::has('success_message') && Session::get('success_message') != "")
		<div class="note note-success">{{	Session::get('success_message') }}</div>
	@endif
	@if (Session::has('error_message') && Session::get('error_message') != "")
		<div class="note note-danger">{{	Session::get('error_message') }}</div>
	@endif

	{{ Form::open(array('id'=>'mailHfrm', 'method'=>'get','class' => 'form-horizontal form-request' )) }}
		<div class="portlet box blue-madison">
			<!-- BEGIN: PAGE TITLE -->
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-globe"><sup class="fa fa-envelope-o font11"></sup></i>{{ trans('admin/massEmail.title') }}
				</div>
				<div class="tools">
					<a class="collapse" href="javascript:;"></a>
				</div>
			</div>
			<!-- END: PAGE TITLE -->

			<!-- BEGIN: SEARCH BAR -->
			<div class="portlet-body form" style="display: block;">
				<div id="selSrchBooking">
					<div class="row form-body">
						<fieldset class="col-md-6">
							<div class="form-group">
								{{ Form::label('todate', trans('admin/massEmail.list.to_date'), array('class' => 'col-md-4 control-label')) }}
								<div class="col-md-7">
									<div data-date-format="yyyy-mm-dd" class="input-group input-medium date date-picker">
										{{ Form::text('to-date',Input::get('to-date'), array('class' => 'form-control', 'data-date-format' => "yyyy-mm-dd", "readonly" => "false", "id" => "to-date") ) }}
									<span class="input-group-btn">
										<label class="btn default" for="to-date"><i class="fa fa-calendar"></i></label>
                                    </span>
									</div>
								</div>
							</div>

							<div class="form-group">
								{{ Form::label('from-date', trans('admin/massEmail.list.from_date'), array('class' => 'col-md-4 control-label')) }}
								<div class="col-md-7">
									<div data-date-format="yyyy-mm-dd" class="input-group input-medium date date-picker">
										{{ Form::text('from-date',   Input::get('from-date'), array('class' => 'form-control', 'data-date-format' => "yyyy-mm-dd", "readonly" => "false", "id" => "from-date") ) }}
										<span class="input-group-btn">
											<label for="from-date" class="btn default"><i class="fa fa-calendar"></i></label>
										</span>
									</div>
								</div>
							</div>
						</fieldset>

						<fieldset class="col-md-6">
							<div class="form-group">
								{{ Form::label('subject', trans('admin/massEmail.list.subject'), array('class' => 'col-md-4 control-label')) }}
								<div class="col-md-5">
									{{ Form::text('subject', Input::get("subject"), array('class' => 'form-control valid', 'id' => 'subject')) }}
								</div>
							</div>

							<div class="form-group">
								{{ Form::label('status', trans('admin/massEmail.list.status'), array('class' => 'col-md-4 control-label')) }}
								<div class="col-md-7">
									{{ Form::select('status',array('' => trans('common.all'))+$d_arr['search_status_arr'], Input::get("status"), array('class' => 'form-control input-small bs-select valid', 'id' => 'status')) }}
								</div>
							</div>
						</fieldset>
					</div>

					<div class="form-actions fluid">
						<div class="col-md-offset-2 col-md-7">
							<button type="submit" name="search_txns" value="search_txns" class="btn purple-plum">{{trans('common.search')}} <i class="fa fa-search bigger-110"></i></button>
							<button type="reset" name="search_reset" value="search_reset" class="btn default" onclick="javascript:location.href='{{ url::to('admin/mass-email/list') }}'"> <i class="fa fa-rotate-left bigger-110"></i> {{ trans("common.reset")}} </button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- END: SEARCH BAR -->

		<div class="portlet blue-hoki box">
			<!-- BEGIN: PAGE TITLE -->
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-list-ul"><sup class="fa fa-envelope-o font11"></sup></i>{{ trans('admin/massEmail.list.title') }}
				</div>
			</div>
			<!-- END: PAGE TITLE -->

			<!--  BEGIN: TABLE PORTLET-->
			<div class="portlet-body">
				<div class="caption">
					<a class="btn green" href="{{ URL::to('admin/mass-email/compose') }}"><i class="fa fa-edit"></i> {{ trans('admin/massEmail.composer.compose_mail') }}</a>
				</div>
				<div class="table-scrollable">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr id="loginlisthead" title="sort" >
								<th width="40">{{ trans('admin/massEmail.list.id') }}</th>
								<th>{{ trans('admin/massEmail.list.send_on') }}</th>
								<th>{{ trans('admin/massEmail.list.subject') }}</th>
								<th>{{ trans('admin/massEmail.list.status') }}</th>
								<th>{{ trans('admin/massEmail.list.action') }}</th>
							</tr>
						</thead>

						<tbody>
							@if(count($details) > 0)
								@foreach($details as $loghis)
									<tr>
										<td>{{$loghis['id']}}</td>
										<td class="text-muted"><div class="wid118">{{ CUtil::FMTDate($loghis['send_on'], 'Y-m-d H:i:s', '') }}</div></td>
										<td><div class="wid-280">{{$loghis['subject']}}</div></td>
										<td>
											<?php
												if(count($loghis) > 0)
												{
													if($loghis['status'] == 'progress') {
														$lbl_class = "label-info";
														$status = trans('admin/massEmail.list.progress');
													}
													elseif($loghis['status'] == 'pending') {
														$lbl_class = "label-warning";
														$status = trans('admin/massEmail.list.pending');
													}
													elseif($loghis['status'] == 'sent') {
														$lbl_class = "label-success";
														$status = trans('admin/massEmail.list.sent');
													}
													elseif($loghis['status'] == 'cancelled') {
														$lbl_class = "label-danger";
														$status = trans('admin/massEmail.list.cancelled');
													}
												}
											?>
											<p class="label {{ $lbl_class }}">{{ $status }}</p>
										</td>
										<td class="status-btn">
                                        	<div class="wid180">
                                                @if($loghis['status']=='pending')
                                                    <a href="{{Url::to('admin/mass-email/compose/edit/'.$loghis['id'])}}" title="{{trans('common.edit')}}" class="btn btn-xs btn-info">
                                                    <i class="fa fa-pencil"></i></a>
                                                @endif

                                                @if($loghis['status']=='pending')
                                                    <a href="{{Url::to('admin/mass-email/change-mass-mail-status/'.$loghis['id'])}}" class="fn_dialog_confirm btn btn-xs btn-danger" action="Cancel" title="{{trans('common.cancel')}}"><i class="fa fa-times"></i></a>
                                                @endif

                                                <a href="{{Url::to('admin/mass-email/compose/view/'.$loghis['id'])}}" title="{{trans('common.view')}}" class="btn btn-xs btn-primary">
                                                <i class="fa fa-eye"></i></a>

                                                @if($loghis['status']=='progress' || $loghis['status']=='sent')
                                                    <a href="{{ URL::to('admin/mass-email/show-mail-users').'?action=sent&mail_id='.$loghis['id'] }}" class="show_users btn btn-xs green" action="Sent" title="{{ trans('admin/massEmail.list.mail_sent_users') }}"><i class="fa fa-envelope"></i></a>
                                                @endif

                                                @if($loghis['status'] != 'progress')
                                                    <a href="{{ Url::to('admin/mass-email/delete-mass-email') }}/{{ $loghis['id'] }}" action="Delete" class="fn_dialog_confirm btn btn-danger btn-xs" title="{{ trans('admin/massEmail.list.delete_lbl') }}"><i class="fa fa-trash-o"></i></a>
                                                @endif

                                                @if($loghis['status']=='sent')
                                                    <a href="{{ Url::to('admin/mass-email/resend-mass-email') }}/{{ $loghis['id'] }}" action="Resend" class="fn_dialog_confirm btn btn-xs btn-success" title="{{ trans('admin/massEmail.list.resend_lbl') }}"><i class="fa fa-mail-forward"></i></a>
                                                @endif

                                                @if($loghis['status']=='progress' || $loghis['status']=='sent')
                                                    <a href="{{ URL::to('admin/mass-email/show-mail-users').'?action=notsent&mail_id='.$loghis['id'] }}" class="show_users btn btn-xs red" action="Not sent" title="{{ trans('admin/massEmail.list.mail_notsent_users') }}"><i class="fa fa-envelope-o"></i></a>
                                                @endif
                                            </div>
										</td>
									</tr>
								@endforeach
							@else
								<tr >
									<td colspan="5"><p class="alert alert-info">{{ trans('admin/massEmail.list.empty') }}</p></td>
								</tr>
							@endif
						</tbody>
					</table>
				</div>
				{{ Form::hidden('order_by', (Input::get("order_by")?Input::get("order_by"):'desc'),array('id' =>'order_by')) }}
				{{ Form::hidden('order_by_field', (Input::get("order_by_field")?Input::get("order_by_field"):'id'),array('id' =>'order_by_field')) }}
                <div class="row">
                    @if(count($details) > 0)
                        <div class="col-md-6 mt10">
                            <div class="dataTables_paginate paging_bootstrap">
                                {{ $details->appends(array('order_by' => Input::get('order_by'),'order_by_field' => Input::get('order_by_field'),'perpage' => Input::get('perpage'),'subject' => Input::get('subject'),'status' => Input::get('status'),'from-date' => Input::get('from-date'),'to-date' => Input::get('to-date') ))->links() }}
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
	<script>
        $(document).ready(function(){

            $("#loginlisthead th").click(function()
            {
            if($(this).attr('id')== undefined)
            return false;
            if($("#order_by_field").val()==$(this).attr('id'))
                {
                    if($('#order_by').val()=='desc')
                    {
                    $('#order_by').val('asc');
                    }
                    else
                    {
                    $('#order_by').val('desc');
                    }
                }
                else
                {
                    $("#order_by_field").val($(this).attr('id'));
                    $('#order_by').val('desc')
                }
                $("#mailHfrm").submit();
            });

            $("#perpage").change(function(){
            $("#mailHfrm").submit();
            })

            if($('#order_by').val()=='desc')
            {
            //$("#loginlisthead th#"+$("#order_by_field").val()+" span").html('>');
            $("#loginlisthead th#"+$("#order_by_field").val()).addClass('sorting_desc');
            }
            else
            {
            //$("#loginlisthead th#"+$("#order_by_field").val()+" span").html('<');
            $("#loginlisthead th#"+$("#order_by_field").val()).addClass('sorting_asc');
            }
        });
    </script>
    <!--<script src="{{ URL::asset('js/bootstrap/bootstrap-datepicker.js') }}"></script>-->
	<script type="text/javascript">
        $(document).ready(function () {
            $('#from-date').datepicker({
                format: 'yyyy-mm-dd'
            });
            $('#to-date').datepicker({
                format: 'yyyy-mm-dd'
            });
        });
    </script>
	<script type="text/javascript">
        var common_ok_label = "{{ trans('common.yes') }}" ;
        var common_no_label = "{{ trans('common.cancel') }}" ;
        var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
        $(window).load(function(){
              $(".fn_dialog_confirm").click(function(){
                    var atag_href 	= $(this).attr("href");
                    var action = $(this).attr("action");
                    var cmsg = "{{trans('admin/massEmail.send_cancel')}}";
                    if(action == 'Delete') {
                        cmsg = "{{trans('admin/massEmail.delete_mail_confirm')}}";
                    }
                    else if(action == 'Resend') {
                        cmsg = "{{trans('admin/massEmail.resend_mail_confirm')}}";
                    }
                    var txtCancel = common_no_label;
                    var buttonText = {};
                    buttonText['{{ trans('common.yes') }}'] = function(){
                                                Redirect2URL(atag_href);
                                                $( this ).dialog( "close" );
                                            };
                    buttonText['{{ trans('common.no') }}'] = function(){	$(this).dialog('close');	};
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

            $(".show_users").fancybox({
                maxWidth    : 800,
                maxHeight   : 460,
                fitToView   : true,
                width       : '70%',
                height      : '460',
                autoSize    : true,
                closeClick  : false,
                type        : 'iframe',
                openEffect  : 'none',
                closeEffect : 'none',
                'afterClose'  : function() {
                    //window.location.reload();
                }
            });
        </script>
@stop