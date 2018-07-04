@extends('admin')
@section('content')
	<!--- SUCCESS INFO STARTS --->
    @if (Session::has('success_message') && Session::get('success_message') != "")
        <div class="note note-success">{{	Session::get('success_message') }}</div>
    @endif
    <div class="note note-success" id="cancellation_pollicy_success_div" style="display:none;"></div>
    <!--- SUCCESS INFO END --->

    @if (Session::has('error_message') && Session::get('error_message') != "")
    	<!--- ERROR INFO STARTS --->
        <div class="note note-danger">{{	Session::get('error_message') }}</div>
        <!--- ERROR INFO END --->
    @endif
    @if(isset($d_arr['error_msg']) && $d_arr['error_msg'] != '')
        <!--<h1 class="page-title">{{ $d_arr['pageTitle'] }}</h1>-->
        <!--- ERROR INFO STARTS --->
        <p class="note note-danger">{{ $d_arr['error_msg'] }}</p>
        <!--- ERROR INFO END --->
    @endif
    {{ Form::model($cancellation_policy, ['method' => 'post', 'id' => 'addMemberfrm', 'class' => 'form-horizontal', 'files' => true]) }}
   	 	{{ Form::hidden('user_id', $d_arr['user_id'], array("id" => "user_id")) }}
		<div class="portlet box blue-hoki">
			<!--- TITLE STARTS --->
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-edit"></i> {{ $d_arr['pageTitle'] }}
				</div>
			</div>
			<!--- TITLE END --->

			<div class="portlet-body form">
				<div class="form-body">
					@if(count($cancellation_policy) > 0 && $cancellation_policy['cancellation_policy_filename'] != '')
						<div class="form-group">
							{{ Form::label('shop_default_cancellation_policy_file', trans("product.default_cancellation_policy_file"), array('class' => 'control-label required-icon col-md-3')) }}
							<div class="col-md-4">
								<div class="uploadedimg-list clearfix">
									<ul id="uploadedFilesList" class="list-unstyled">
										<li id="shopCancellationPolicyRow_{{ $cancellation_policy['id'] }}">
											<?php $filePath = $cancellation_policy['cancellation_policy_server_url']; //URL::asset(Config::get('webshoppack.shop_cancellation_policy_folder'));
											$filename = $cancellation_policy['cancellation_policy_filename'].'.'.$cancellation_policy['cancellation_policy_filetype'];?>
                                            <a target="_blank" href="{{$filePath.'/'.$filename}}" class="btn btn-info btn-xs"><i class="fa fa-eye"></i> View file</a>
                                            <a title="{{trans('common.delete')}}" href="javascript: void(0);" onclick="javascript:removeShopCancellationPolicy({{ $cancellation_policy['id'] }});" class="remove-image"><i class="fa fa-times-circle text-danger"></i></a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					@else
						<div class="form-group">
                            {{ Form::label('shop_cancellation_policy_file', trans("admin/cancellationpolicy.cancellation_policy_file"), array('class' => 'control-label required-icon col-md-3 col-sm-3 col-xs-12')) }}
							<div class="col-md-4 col-sm-6 col-xs-12">
								{{ Form::file('shop_cancellation_policy_file', array('title' => trans("admin/cancellationpolicy.cancellation_policy_file"), 'class' => 'filestyle', 'data-buttonText' => Lang::get('common.choose_file'))) }}
								<label class="error">{{ $errors->first('shop_cancellation_policy_file') }}</label>
								<div class="mt5 clearfix">
									<small class="pull-left"><i class="fa fa-question-circle"></i></small>
									<p class="ml20 text-muted">
                                        <small class="show">{{ str_replace("VAR_FILE_FORMAT",  Config::get('webshoppack.shop_cancellation_policy_allowed_extensions'), trans('shop.uploader_allowed_upload_format_text')) }}</small>
                                        <small>{{ str_replace("VAR_FILE_MAX_SIZE",  (Config::get('webshoppack.shop_cancellation_policy_allowed_file_size')/1024).' MB', trans('shop.uploader_allowed_upload_limit')) }}</small>
									</p>
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-offset-1 col-md-7"><center>( {{ trans('common.or') }} )</center></div>
						</div>

						<div class="form-group">
							{{ Form::label('cancellation_policy_text', trans("admin/cancellationpolicy.cancellation_policy_text"), array('class' => 'control-label required-icon col-md-3')) }}
							<div class="col-md-6">
								{{  Form::textarea('cancellation_policy_text', Input::old('cancellation_policy_text'), array('class' => 'form-control')) }}
								<label class="error">{{ $errors->first('cancellation_policy_text') }}</label>
							</div>
						</div>
					@endif
				</div>
				<!--- ADD MEMBER BASIC DETAILS END --->

				<!--- ACTIONS STARTS --->
				<div class="form-actions fluid">
					<div class="col-md-offset-3 col-md-5">
						{{ Form::hidden('id')}}
						<button type="submit" name="add_cancellation_policy" value="add_members" class="btn green">
							<i class="fa fa-arrow-up"></i> {{ Lang::get('common.update') }}
						</button>
					</div>
				</div>
				<!--- ACTIONS END --->
			</div>
		</div>
    {{ Form::close() }}
    <div id="dialog-cancellation-policy-delete-confirm" class="confirm-dialog-delete" title="" style="display:none;">
          <p><span class="ui-icon ui-icon-alert"></span><small>{{  trans('admin/cancellationpolicy.cancellation_policy_delete_confirm') }}</small></p>
    </div>
@stop

@section('script_content')
	<script language="javascript" type="text/javascript">
        var common_no_label = "{{ trans('common.cancel') }}" ;
        var common_yes_label = "{{ trans('common.yes') }}" ;
        var package_name = "{{ Config::get('generalConfig.site_name') }}" ;
        function removeShopCancellationPolicy(resource_id)
        {
            $("#dialog-cancellation-policy-delete-confirm").dialog({
                title: package_name,
                modal: true,
                buttons: [{
                    text: common_yes_label,
                    click: function()
                    {
                        displayLoadingImage();
                        $.getJSON("{{ Url::action('AdminCancellationPolicyController@getDeleteCancellationPolicy') }}",
                        {resource_id: resource_id},
                        function(data)
                        {

                            if(data.result == 'success')
                            {
                                window.location.reload(true);
                                //$('#shopCancellationPolicyRow_'+resource_id).remove();
                                //$('#cancellation_pollicy_success_div_msg').hide();
                                //$('#cancellation_pollicy_success_div').show();
                                //$('#cancellation_pollicy_success_div').html("{{trans('admin/cancellationpolicy.cancellation_policy_file_deleted_success')}}");
                            }
                            else
                            {
                            hideLoadingImage();
                                $('#cancellation_pollicy_success_div').hide();
                                $('#cancellation_pollicy_success_div_msg').hide();
                                showErrorDialog({status: 'error', error_message: '{{ trans('common.invalid_action') }}'});
                            }
                        });
                        $(this).dialog("close");
                    }
                },
                {
                    text: common_no_label,
                    click: function()
                    {
                         $(this).dialog("close");
                    }
                }]
            });
        }
    </script>
@stop
