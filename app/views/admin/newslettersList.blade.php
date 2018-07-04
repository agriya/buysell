@extends('admin')
@section('content')
	<!-- BEGIN: NOTIFICATIONS -->
    @include('notifications')
    <!-- END: NOTIFICATIONS -->

    <!-- BEGIN: INFO BLOCK -->
    @if(Session::has('success_message') && Session::get('success_message') != '')
        <div class="note note-success">{{ Session::get('success_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif

    @if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif
    <!-- END: INFO BLOCk -->

	<!-- BEGIN: PAGE TITLE -->
	<a class="pull-right mt10 btn btn-success btn-xs" href="{{ URL::action('AdminNewsletterController@getAdd') }}" title="{{ Lang::get('admin/taxation.add_taxation') }}">
    	<i class="fa fa-plus-circle"></i> {{ Lang::get('admin/newsletter.add_newsletter') }}
    </a>
    <h1 class="page-title">{{Lang::get('admin/newsletter.newsletter_history')}}</h1>
    <!-- END: PAGE TITLE -->

    <!-- BEGIN: NEWSLETTER SEARCH -->
    {{ Form::open(array('url' => Url::action('AdminNewsletterController@getIndex'), 'id'=>'newsletterFrm', 'method'=>'get','class' => 'form-horizontal' )) }}
    	<div class="portlet box blue-madison mb40">
            <!-- BEGIN: SEARCH TITLE -->
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-search"></i> {{ trans('admin/newsletter.search_newsletter') }}
                </div>
                <div class="tools">
                    <a class="collapse" href="javascript:;"></a>
                </div>
            </div>
            <!-- END: SEARCH TITLE -->

            <div class="portlet-body form">
                <div id="search_holder">
                    <div id="selSrchScripts">
                    	<div class="form-body">
                            <div class="form-group">
                                {{ Form::label('subject', trans('admin/newsletter.subject'), array('class' => 'col-md-3 control-label')) }}
                                <div class="col-md-4">
                                    {{ Form::text('subject', Input::get('subject'), array('class' => 'form-control')) }}
                                    <label class="error" for="status" generated="true">{{$errors->first('subject')}}</label>
                                </div>
                            </div>
                            <div class="form-group">
                                {{ Form::label('date_sent', trans('admin/newsletter.date_sent'), array('class' => 'col-md-3 control-label')) }}
                                <div class="col-md-4">
                                    {{ Form::text('date_sent', Input::get('date_sent'), array('class' => 'form-control', 'id'=>'date_sent')) }}
                                    <label class="error" for="status" generated="true">{{$errors->first('date_sent')}}</label>
                                </div>
                            </div>
                            <div class="form-group">
                                {{ Form::label('status', trans('admin/newsletter.status'), array('class' => 'col-md-3 control-label')) }}
                                <div class="col-md-4">
                                    {{ Form::select('status', $status, Input::get("status"), array('class' => 'form-control bs-select input-medium')) }}
                                    <label class="error" for="status" generated="true">{{$errors->first('status')}}</label>
                                </div>
                            </div>
                         </div>
                        <div class="form-actions fluid">
                            <div class="col-md-offset-3 col-md-4">
                                <button type="submit" name="search_submit" value="search_submit" class="btn purple-plum">
                                	{{ trans("common.search") }} <i class="fa fa-search"></i>
                                </button>
                                <button type="reset" name="search_reset" value="search_reset" class="btn default" onclick="javascript:location.href='{{ URL::action('AdminNewsletterController@getIndex') }}'">
                                    <i class="fa fa-rotate-left bigger-110"></i> {{ trans("common.reset")}}
                                </button>
                            </div>
						</div>
                    </div>
                </div>
            </div>
     	</div>
    {{ Form::close() }}
    <!-- END: NEWSLETTER SEARCH -->

    <!-- BEGIN: NEWSLETTER LIST -->
	<div class="portlet box blue-hoki">
        <!-- BEGIN: TABLE TITLE -->
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-list"></i> {{ Lang::get('admin/newsletter.newsletter_list') }}
            </div>
        </div>
        <!-- END: TABLE TITLE -->

        <div class="portlet-body">
            @if(count($newsletters) > 0 )
            	<!-- BEGIN: NEWSLETTER LIST TABLE -->
                {{ Form::open(array('url'=>URL::action('AdminNewsletterController@postBulkAction'),'id'=>'listFrm', 'method'=>'post','class' => 'form-horizontal' )) }}
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th width="40">{{Form::checkbox('select_al','yes',false,array('id' => 'select_all', 'class' => 'group-checkable'))}}</th>
                                    <th class="col-md-4">{{ Lang::get('admin/newsletter.subject') }}</th>
                                    <th>{{ Lang::get('admin/newsletter.date_sent') }}</th>
                                    <th>{{ Lang::get('admin/newsletter.total_sent') }}</th>
                                    <th>{{ Lang::get('admin/newsletter.status') }}</th>
                                    <th>{{ Lang::get('admin/newsletter.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($newsletters as $newsletter)
                                	<tr>
                                        <td>{{Form::checkbox('ids[]',$newsletter->id, false, array('class' => 'checkboxes js-ids') )}}</td>
                                        <td>{{ $newsletter->subject }}</td>
                                        <td class="text-muted">{{ CUtil::FMTDate($newsletter->updated_at, 'Y-m-d H:i:s', '')}}</td>
										<td>{{ $newsletter->total_sent }}</td>
										<td>
										   <?php
												if($newsletter->status == 'Pending') {
													$lbl_class = " label-warning";
												}
												elseif($newsletter->status == 'Started') {
													$lbl_class = "label-primary";
												}
												elseif($newsletter->status == 'Finished') {
													$lbl_class = "label-success";
												}
												else {
													$lbl_class = "label-default";
												}
											?>
											<span class="label {{ $lbl_class }}">{{ $newsletter->status }}</span>
                                        </td>
										<td class="status-btn">
                                            <a class="btn btn-info btn-xs" title="{{Lang::get('admin/newsletter.view')}}" data-toggle="modal" data-target="#myModal_{{$newsletter->id}}"><i class="fa fa-eye"></i></a>
                                            <!-- BEGIN: VIEW NEWSLETTER LIST -->
                                            <div class="modal fade" id="myModal_{{$newsletter->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal">
                                                                <span aria-hidden="true"></span><span class="sr-only">{{trans('common.close')}}</span>
                                                            </button>
                                                            <h3 class="mar0" id="myModalLabel">{{Config::get('generalConfig.site_name')}}</h3>
                                                        </div>
                                                        <div class="modal-body">
                                                            <dl class="dl-horizontal">
                                                                <dt>{{Lang::get('admin/newsletter.subject')}}</dt>
                                                                <dd><p>{{$newsletter->subject}}</p></dd>
                                                                <dt>{{Lang::get('admin/newsletter.message')}}</dt>
                                                                <dd><p>{{$newsletter->message}}</p></dd>
                                                                <dt>{{Lang::get('admin/newsletter.total_sent')}}</dt>
                                                                <dd><p>{{$newsletter->total_sent}}</p></dd>
                                                                <dt>{{Lang::get('admin/newsletter.status')}}</dt>
                                                                <dd><p class="label {{ $lbl_class }}">{{$newsletter->status}}</p></dd>
                                                            </dl>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-times"></i> {{trans('common.close')}}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--<a class="btn btn-xs blue" href="{{Url::action('AdminNewsletterController@getView',$newsletter->id)}}"><i class="fa fa-eye"></i></a>-->
                                            <!-- END: VIEW NEWSLETTER LIST -->
										</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="6">
                                        <p class="pull-left margin-top-10 margin-right-10">
                                            {{Form::select('action',$actions,'',array('class'=>'form-control bs-select input-small', 'id'=>'newsletter_action'))}}
                                        </p>
                                        <p class="pull-left margin-top-10">
                                            <input type="submit" value="Submit" class="btn green" id="page_action" name="page_action">
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                         </table>
                    </div>
                 {{Form::close()}}
                 <!-- END: NEWSLETTER LIST TABLE -->

                <!-- BEGIN: PAGINATION -->
                <div class="text-right">
                    {{ $newsletters->appends(array('subject' => Input::get('subject'), 'date_sent' => Input::get('date_sent'),
						'status' => Input::get('status') ))->links() }}
                </div>
                <!-- END: PAGINATION -->
            @else
                <div class="alert alert-info mar0">{{ Lang::get('admin/newsletter.no_newsletter_found') }}</div>
            @endif
            <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
    	</div>
    </div>
    <!-- END: NEWSLETTER LIST -->

    {{ Form::open(array('id'=>'actionfrm', 'method'=>'post', 'url' => URL::action('AdminNewsletterController@postBulkAction'))) }}
        {{ Form::hidden('id', '', array('id' => 'list_id')) }}
        {{ Form::hidden('action', '', array('id' => 'list_action')) }}
    {{ Form::close() }}

	<div id="dialog-confirm" title="" style="display:none;">
	    <span class="ui-icon ui-icon-alert"></span>
		<span id="dialog-confirm-content" class="show ml15"></span>
	</div>
@stop

@section('script_content')
	<script type="text/javascript">
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

		$('#page_action').click(function(e){
			e.preventDefault(e);
			error_found = false;
			if($(".js-ids:checkbox:checked").length <= 0)
			{
				$('#dialog-confirm-content').html("{{ trans('admin/newsletter.select_atleast_one_newsletter') }}");
				error_found = true;
			}
			var selected_action = $('#newsletter_action').val();
			if(selected_action == '')
			{
				$('#dialog-confirm-content').html("{{ trans('admin/newsletter.error_select_action') }}");
				error_found = true;
			}
			if(!error_found)
			{
				if(selected_action == 'Finished')
				{
					$('#dialog-confirm-content').html('{{ trans('admin/newsletter.confirm_finish_newsletter') }}');
				}
				if(selected_action == 'Pending')
				{
					$('#dialog-confirm-content').html('{{ trans('admin/newsletter.confirm_active_newsletter') }}');
				}

			}
			if(error_found)
			{
				$("#dialog-confirm").dialog({ title: '{{ trans('admin/newsletter.newsletters_head') }}', modal: true,
					buttons: {
						"{{ trans('common.cancel') }}": function() {
							$(this).dialog("close");
						}
					}
				});
			}
			else
			{
				$("#dialog-confirm").dialog({ title: '{{ trans('admin/newsletter.newsletters_head') }}', modal: true,
					buttons: {
						"{{ trans('common.yes') }}": function() {
							$('#listFrm').submit();
						}, "{{ trans('common.cancel') }}": function() {  $(this).dialog("close");  }
					}
				});
			}
		})

		$(function() {
            $('#date_sent').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });
        });
	</script>
@stop