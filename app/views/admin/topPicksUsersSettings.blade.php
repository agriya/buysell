@extends('admin')
{{ $header->setMetaTitle($d_arr['pageTitle']) }}
@section('content')
    <!-- BEGIN: ALERT MESSAGE -->
	@if (Session::has('success_message') && Session::get('success_message') != "")
        <div class="note note-success">{{   Session::get('success_message') }}</div>
    @endif
    @if (Session::has('warning_message') && Session::get('warning_message') != "")
        <div class="note note-warning">{{   Session::get('warning_message') }}</div>
    @endif
    @if (Session::has('error_message') && Session::get('error_message') != "")
        <div class="note note-danger">{{    Session::get('error_message') }}</div>
    @endif
	<!-- END: ALERT MESSAGE -->

    <div class="portlet box blue-madison">
        <!-- BEGIN: PAGE TITLE -->
		<div class="portlet-title">
			<div class="caption">
				{{$d_arr['actionicon']}} {{$d_arr['pageTitle']}}
			</div>
		</div>
        <!-- END: PAGE TITLE -->

        <div class="portlet-body form" >
            <!-- BEGIN: TOP PICKSUSER FORM -->
            {{ Form::model($d_arr['top_picks_details'], [
            'method' => 'post',
            'id' => 'toppicks_frm', 'class' => 'form-horizontal','files' => 'true', 'enctype' => 'multipart/form-data'
            ]) }}
            	{{ Form::hidden('settings_id', $d_arr['id']) }}
				<div class="form-body">
					<div class="form-group {{{ $errors->has('user_name') ? 'error' : '' }}}">
						{{ Form::label('user_name', trans("admin/indexSettings.user_name"), array('class' => 'col-md-3 control-label required-icon')) }}
						<div class="col-md-4">
							{{ Form::text('user_name', Input::get("user_name"), array ('class' => 'form-control', 'autocomplete' => 'off')); }}
							{{ Form::hidden('srch_user_id', Input::get("srch_user_id"), array("id" => "srch_user_id")) }}
							<label class="error">{{{ $errors->first('user_name') }}}</label>
						</div>
					</div>
				</div>

                <div class="form-actions fluid">
					<div class="col-md-offset-3 col-md-9">
						@if($d_arr['mode'] == 'edit')
							<button type="submit" name="edit_featured" class="btn green" id="edit_featured" value="edit_featured">
								<i class="fa fa-arrow-up"></i> {{ trans("common.update") }}
                            </button>
						@else
							<button type="submit" name="add_featured" class="btn green" id="add_featured" value="add_featured">
								<i class="fa fa-check"></i> {{trans("common.submit")}}
                            </button>
						@endif
						<button type="reset" name="cancel_fetured" class="btn default" onclick="window.location = '{{ url::to('admin/manage-toppicks-users') }}'">
							<i class="fa fa-times"></i> {{trans("common.cancel")}}
                        </button>
					</div>
				</div>
            {{ Form::close() }}
            <!-- END: TOP PICKSUSER FORM -->
        </div>
    </div>
    {{ Form::model($d_arr['top_picks_details'], [
        'method' => 'get',
        'id' => 'toppicksList_frm', 'class' => 'form-horizontal']) }}
        <div class="portlet blue-hoki box">
            <!-- BEGIN: PAGE TITLE -->
            <div class="portlet-title">
                <div class="caption">{{$d_arr['actionicon']}} List {{$d_arr['pageTitle']}}</div>
            </div>
            <!-- END: PAGE TITLE -->

            <!--  BEGIN: TOP PICKSUSER TABLE -->
            <div class="portlet-body clearfix">
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover api-log">
                        <thead>
                            <tr>
                                <th>{{ Lang::get('common.user_details')}}</th>
                                <th width="180">{{ trans('common.action') }}</th>
                            </tr>
                        </thead>

                        <tbody>
							@if(count($details) > 0)
	                            @foreach($details as $toppicks)
	                            	<?php $user_details = CUtil::getUserDetails($toppicks->user_id); ?>
	                                <tr>
	                                    <td>
											<p>
                                            	<a href="{{ URL::to('admin/users/user-details').'/'.$toppicks->user_id }}">{{ $user_details['display_name'] }}</a>
                                                (<a class="text-muted" href="{{ URL::to('admin/users/user-details').'/'.$toppicks->user_id }}">{{ $user_details['user_code'] }}</a>)
                                            </p>
											@if(isset($user_details['is_banned']) && $user_details['is_banned'] == 1)
                                            	<p><span class="label label-danger"><i class="fa fa-ban"></i> {{ trans('common.blocked') }}</span></p>
											@endif
										</td>
	                                    <td class="status-btn">
											{{--<a class="btn btn-info btn-xs" title="Edit Toppicks" href="{{ url::to('admin/manage-toppicks-users')}}?id={{$toppicks->id}}"><i class="fa fa-edit"></i></a>--}}
											<a class="btn btn-info btn-xs fn_dialog_confirm red" title="{{ trans('common.delete_toppicks') }}" href="{{ url::to('admin/manage-toppicks-users/delete')}}?id={{$toppicks->top_pick_id}}"><i class="fa fa-trash-o"></i></a>
	                                    </td>
	                                </tr>
	                            @endforeach
                            @else
                                <tr>
                                    <td colspan="2"><p class="alert alert-info">{{ trans('admin/indexSettings.no_result') }}</p></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

				<!--- BEGIN: PAGINATION --->
				@if(count($details) > 0)
					<div class="dataTables_paginate paging_bootstrap text-right">
						{{ $details->appends(array())->links() }}
					</div>
				@endif
				<!--- END: PAGINATION --->
            </div>
            <!--  END: TOP PICKSUSER TABLE -->
        </div>
    {{ Form::close() }}
    <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
@stop

@section('script_content')
	<script language="javascript" type="text/javascript">
        var mes_required = "{{trans('auth/form.required')}}";
        $("#toppicks_frm").validate({
            rules: {
                user_name: {
                    required: true,
                },
            },
            messages: {
                user_name: {
                    required: mes_required
                },
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

        var common_ok_label = "{{ trans('common.yes') }}" ;
            var common_no_label = "{{ trans('common.cancel') }}" ;
            var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
            $(window).load(function(){
             $(".fn_dialog_confirm").click(function(){

                    var atag_href   = $(this).attr("href");
                    var cmsg        = "{{trans('common.uploader_confirm_delete')}}";
                    var txtCancel = common_no_label;
                    var buttonText = {};
                    buttonText['Yes'] = function(){
                                                Redirect2URL(atag_href);
                                                $( this ).dialog( "close" );
                                            };
                    buttonText['No'] = function(){
                                                $(this).dialog('close');
                                            };



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

       $(window).load(function(){
			$.ajax({
				url: '{{ URL::to("admin/users-auto-complete") }}',
				dataType: "json",
				success: function(data)
				{
					var cat_data = $.map(data, function(item, val)
					{
						return {
							user_id: val,
							label: item
						};
					});

					$("#user_name").autocomplete({
						delay: 0,
						source: cat_data,
						minlength:3,
						select: function (event, ui) {
							$('#srch_user_id').val(ui.item.user_id);
							return ui.item.label;
						},
						change: function (event, ui) {
							if (!ui.item) {
								$('#srch_user_id').val('');
							}
						}
					});
				}
			});
        });
    </script>
@stop