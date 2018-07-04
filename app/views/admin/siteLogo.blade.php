@extends('admin')
@section('content')
	<!-- BEGIN: SUCCESS INFO -->
    @if (Session::has('success_message') && Session::get('success_message') != "")
        <div class="note note-success">{{	Session::get('success_message') }}</div>
    @endif
    <div class="note note-success" id="cancellation_pollicy_success_div" style="display:none;"></div>
    <!-- END: SUCCESS INFO -->

    @if (Session::has('error_message') && Session::get('error_message') != "")
    	<!-- BEGIN: ERROR INFO -->
        <div class="note note-danger">{{	Session::get('error_message') }}</div>
        <!-- END: ERROR INFO -->
    @endif

    @if(isset($d_arr['error_msg']) && $d_arr['error_msg'] != '')
        <!-- BEGIN: ERROR INFO -->
        <p class="note note-danger">{{ $d_arr['error_msg'] }}</p>
        <!-- END: ERROR INFO -->
    @else
        {{ Form::model($image_details, ['method' => 'post', 'id' => 'addMemberfrm', 'class' => 'form-horizontal', 'files' => true]) }}
            <div class="mobilemenu mobmenu-only">
                <!-- BEGIN: MOBILE TOGGLER -->
                    <button class="btn btn-primary btn-sm mobilemenu-bar mb10"><i class="fa fa-chevron-down"></i> Menu</button>
                <!-- END: MOBILE TOGGLER -->

                <ul class="nav nav-tabs mbldropdown-menu ac-custom-tabs">
                    <li @if($logo_type == 'logo') class="active" @endif><a href="{{URL::action('AdminSiteLogoController@getIndex')}}"><span>{{ trans("admin/siteLogo.logo_settings")}}</span></a></li>
                    <li @if($logo_type == 'favicon') class="active" @endif><a href="{{URL::action('AdminSiteLogoController@getFavIcon')}}"><span>{{ trans("admin/siteLogo.favicon_settings") }}</span></a></li>
                </ul>
            </div>
       	 	<div class="portlet box blue-madison">
				<!-- BEGIN: TITLE -->
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-bullseye"></i>
                        @if($logo_type == 'favicon')
                            {{ trans("admin/siteLogo.site_favicon_title") }}
                        @else
                            {{ trans("admin/siteLogo.site_logo_title") }}
                        @endif
					</div>
				</div>
				<!-- END: TITLE -->

				<div class="portlet-body form sitelogo-block">
					<div class="form-body">
						<div class="form-group mb0">
						<?php
							if($logo_type == 'favicon')
							{
								$title = trans("admin/siteLogo.site_favicon_title");
								$file_format = Config::get('generalConfig.sitefavicon_allowed_extension');
								$file_size = Config::get('generalConfig.sitefavicon_allowed_file_size');
								$file_size_type = 'KB';
								$file_width = Config::get('generalConfig.sitefavicon_width');
								$file_height = Config::get('generalConfig.sitefavicon_height');
							}
							else{
								$title = trans("admin/siteLogo.site_logo_title");
								$file_format = Config::get('generalConfig.sitelogo_allowed_extension');
								$file_size = Config::get('generalConfig.sitelogo_allowed_file_size');
								$file_size_type = 'MB';
								$file_width = Config::get('generalConfig.sitelogo_width');
								$file_height = Config::get('generalConfig.sitelogo_height');
							}

						?>
						{{ Form::label('file', $title, array('class' => 'control-label required-icon col-md-3')) }}
							<div class="col-md-4">
								{{ Form::file('attachment', array('title' => $title, 'class' => 'filestyle', 'data-buttonText' => Lang::get('common.choose_file'))) }}
								<label class="error">{{ $errors->first('attachment') }}</label>
								<div class="mt5 clearfix">
									<small class="pull-left"><i class="fa fa-question-circle"></i></small>
									<p class="ml20 text-muted">
                                        <small class="show">{{ str_replace("VAR_FILE_FORMAT",  $file_format, trans('admin/siteLogo.uploader_allowed_upload_format_text')) }}</small>
                                        <small>{{ str_replace("VAR_FILE_MAX_SIZE",  $file_size.' '.$file_size_type, trans('admin/siteLogo.uploader_allowed_upload_limit')) }}</small>
                                        <small>{{ Lang::get('admin/siteLogo.logo_expected_dimentsion', array('width' => $file_width, 'height' => $file_height )) }} </small>
									</p>
								</div>

                                @if(isset($image_details['image_url']))
                                    <div class="uploadedimg-list clearfix">
                                        <ul id="uploadedFilesList" class="list-unstyled">
                                            <li>
                                                @if(isset($image_details['image_url']))
                                                    <div class="upload-img">
                                                        <a href="javascript:;" title="Profile image">
                                                            {{-- URL::asset('/images/no_image/no-maleprofile_T.jpg') --}}
                                                            <img class="" src="{{$image_details['image_url']}}" alt="Profile image" />
                                                        </a>
                                                    </div>
                                                @endif
                                            </li>
                                        </ul>
                                    </div>
                                @endif
							</div>
						</div>
					</div>
					<!-- END: ADD MEMBER BASIC DETAILS -->

					<!-- BEGIN: ACTIONS -->
					<div class="form-actions fluid">
						<div class="col-md-offset-3 col-md-5">
							{{ Form::hidden('id')}}
							<button type="submit" name="add_cancellation_policy" value="add_members" class="btn green">
								<i class="fa fa-arrow-up"></i> {{ Lang::get('common.update') }}
							</button>
						</div>
					</div>
					<!-- END: ACTIONS -->
				</div>
			</div>
        {{ Form::close() }}
        <div id="dialog-cancellation-policy-delete-confirm" class="confirm-dialog-delete" title="" style="display:none;">
              <p><span class="ui-icon ui-icon-alert"></span><small>{{  trans('admin/cancellationpolicy.cancellation_policy_delete_confirm') }}</small></p>
        </div>
    @endif
@stop
