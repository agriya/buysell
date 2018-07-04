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
            <!-- BEGIN: BANNER SETTINGS FORM -->
            {{ Form::model($d_arr['image_details'], [
            'method' => 'post',
            'id' => 'imageSlider_frm', 'class' => 'form-horizontal','files' => 'true', 'enctype' => 'multipart/form-data'
            ]) }}
            	{{ Form::hidden('settings_id', $d_arr['id']) }}
				<div class="form-body">
					<div class="form-group {{{ $errors->has('title') ? 'error' : '' }}}">
						{{ Form::label('title', trans("admin/manageBanner.title"), array('class' => 'col-md-3 control-label ')) }}
						<div class="col-md-4">
							{{ Form::text('title', null, array ('class' => 'form-control')); }}
							<label class="error">{{{ $errors->first('title') }}}</label>
						</div>
					</div>

					<div class="form-group {{{ $errors->has('content') ? 'error' : '' }}}">
						{{ Form::label('content', trans("admin/manageBanner.content"), array('class' => 'col-md-3 control-label ')) }}
						<div class="col-md-5">
							{{ Form::textarea('content', null, array ('class' => 'form-control', 'rows'=>'7')); }}
							<label class="error">{{{ $errors->first('content') }}}</label>
						</div>
					</div>

					<div class="form-group {{{ $errors->has('image_name') ? 'error' : '' }}}">
						{{ Form::label('image_name', trans("admin/manageBanner.image_name"), array('class' => 'col-md-3 control-label required-icon')) }}
						<div class="col-md-5">
							{{ Form::file('image_name', array ('class' => 'filestyle', 'data-buttonText' => Lang::get('common.choose_file'))) }}
							<div class="mt5 mb5">
								<i class="fa fa-question-circle pull-left"></i>
								<div class="show ml20">
									<small class="text-muted">
										{{ str_replace("VAR_FILE_FORMAT",  Config::get('generalConfig.banner_image_allowed_extensions'), trans('shop.uploader_allowed_upload_format_text')) }}
										<br />
										<?php $upload_max_size = (Config::get('generalConfig.banner_image_upload_max_filesize') / 1024).' MB'; ?>
										{{ str_replace("VAR_FILE_MAX_SIZE",  (Config::get('generalConfig.banner_image_upload_max_filesize') / 1024).' MB', trans('shop.uploader_allowed_upload_limit')) }}
										<br />
										{{ str_replace("VAR_IMAGE_RESOLUTION",  Config::get('generalConfig.banner_image_large_width').'x'.Config::get('generalConfig.banner_image_large_height'), trans('shop.allowed_image_resolution')) }}
									</small>
								</div>
							</div>
							<label class="error" for="image_name" generated="true">{{{ $errors->first('image_name') }}}</label>
							@if(count($d_arr['image_details']) > 0)
								<?php $imgPath = URL::asset(Config::get("generalConfig.banner_image_folder")); ?>
								<div class="img210x70">
									<img src="{{$imgPath.'/'. $d_arr['image_details']['filename'].'_L.'.$d_arr['image_details']['ext']}}" alt="{{ $d_arr['image_details']['title']}}">
								</div>
							@endif
						</div>
					</div>

					<div class="form-group {{{ $errors->has('display') ? 'error' : '' }}}">
                        {{ Form::label('display', trans("admin/manageBanner.display"), array('class' => 'col-md-3 control-label') ) }}
                        <div class="col-md-4">
                            {{ Form::select('display', array('1'=> Lang::get('common.yes'),'0'=> Lang::get('common.no')), Input::get('display'),array('class' => 'form-control bs-select input-small','id' => 'display')) }}
                            <label class="error">{{ $errors->first('display') }}</label>
                        </div>
                    </div>
				</div>

                <div class="form-actions fluid">
					<div class="col-md-offset-3 col-md-9">
						@if($d_arr['mode'] == 'edit')
							<button type="submit" name="add_banner_image" class="btn green" id="add_banner_image" value="edit_banner">
								<i class="fa fa-arrow-up"></i> {{ trans("common.update") }}
                            </button>
						@else
							<button type="submit" name="add_banner_image" class="btn green" id="add_banner_image" value="add_banner">
								<i class="fa fa-check"></i> {{trans("common.submit")}}
                            </button>
						@endif
						<button type="reset" name="add_banner_image" class="btn default" onclick="window.location = '{{ url::to('admin/index-banner') }}'">
							<i class="fa fa-times"></i> {{trans("common.cancel")}}
                        </button>
					</div>
				</div>
            {{ Form::close() }}
            <!-- END: BANNER SETTINGS FORM -->
        </div>
    </div>
    {{ Form::model($d_arr['image_details'], [
        'method' => 'get',
        'id' => 'imageBannerList_frm', 'class' => 'form-horizontal']) }}
        <div class="portlet blue-hoki box">
            <!-- BEGIN: PAGE TITLE -->
            <div class="portlet-title">
                <div class="caption">{{$d_arr['actionicon']}} {{trans('common.list')}} {{$d_arr['pageTitle']}}</div>
            </div>
            <!-- END: PAGE TITLE -->

            <!--  BEGIN: BANNER SETTINGS TABLE -->
            <div class="portlet-body clearfix">
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover api-log">
                        <thead>
                            <tr>
                                <th width="40">{{ trans('admin/manageBanner.image_id') }}</th>
                                <th>{{ trans('admin/manageBanner.published') }}</th>
                                <th>{{ trans('admin/manageBanner.title') }}</th>
                                <th width="180">{{ trans('common.action') }}</th>
                            </tr>
                        </thead>

                        <tbody>
							@if(count($details) > 0)
	                            @foreach($details as $image)
	                                <tr>
	                                    <td>{{ $image->id }}</td>
	                                    <td>@if($image->display==1) <span class="text-success">{{ trans('admin/manageBanner.published') }}</span> @else <span class="text-danger">{{ trans('admin/manageBanner.unpublished') }}</span> @endif</td>
	                                    <td>{{ $image->title }}</td>
	                                    <td class="status-btn">
											<a class="btn blue btn-xs" title="{{trans('common.edit') }}" href="{{ url::to('admin/index-banner')}}?id={{$image->id}}"><i class="fa fa-edit"></i></a>
											<a class="btn red btn-xs fn_dialog_confirm red" title="{{trans('common.delete') }}" href="{{ url::to('admin/index-banner/delete-banner')}}?id={{$image->id}}">
											<i class="fa fa-trash-o"></i></a>
	                                    </td>
	                                </tr>
	                            @endforeach
                            @else
                                <tr>
                                    <td colspan="4"><p class="alert alert-info">{{ trans('admin/manageBanner.no_result') }}</p></td>
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
            <!--  END: BANNER SETTINGS TABLE -->
        </div>
    {{ Form::close() }}
    <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
@stop

@section('script_content')
	<script language="javascript" type="text/javascript">
        var mes_required = "{{trans('auth/form.required')}}";
        $("#imageSlider_frm").validate({
            rules: {
                @if(count($d_arr['image_details']) == 0)
                image_name: {
                    required: true,
                },
                @endif
                display: {
                    required: true,
                },
            },
            messages: {
                @if(count($d_arr['image_details']) == 0)
                image_name: {
                    required: mes_required
                },
                @endif
                display: {
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
            var common_no = "{{ trans('common.no') }}" ;
            var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
            $(window).load(function(){
             $(".fn_dialog_confirm").click(function(){

                    var atag_href   = $(this).attr("href");
                    var cmsg        = "{{ trans('admin/manageBanner.want_to_delete_image') }}";
                    var txtCancel = common_no_label;
                    var buttonText = {};
                    buttonText[common_ok_label] = function(){
                                                Redirect2URL(atag_href);
                                                $( this ).dialog( "close" );
                                            };
                    buttonText[common_no] = function(){
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
    </script>
@stop