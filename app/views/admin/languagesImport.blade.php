@extends('admin')
{{ $header->setMetaTitle($d_arr['pageTitle']) }}
@section('content')
    <!-- BEGIN: INFO BLOCK -->
	@if (Session::has('success_message') && Session::get('success_message') != "")
        <div class="note note-success">{{ Session::get('success_message') }}</div>
    @endif
    @if (Session::has('warning_message') && Session::get('warning_message') != "")
        <div class="note note-warning">{{ Session::get('warning_message') }}</div>
    @endif
    @if (Session::has('error_message') && Session::get('error_message') != "")
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
    @endif
	<!-- END: INFO BLOCK -->

    <div class="portlet box blue-madison">
        <!-- BEGIN: PAGE TITLE -->
		<div class="portlet-title">
			<div class="caption">
				<i class="fa fa-download"></i> {{$d_arr['pageTitle']}}
			</div>
		</div>
        <!-- END: PAGE TITLE -->

        <div class="portlet-body form" >
            <!-- BEGIN: LANGUAGE IMPORT FORM -->
            {{ Form::model(array(), [
            'method' => 'post',
            'id' => 'language_frm', 'class' => 'form-horizontal', 'files' => 'true', 'enctype' => 'multipart/form-data']) }}
				<div class="form-body">
					<div class="alert alert-info">{{ Lang::get('admin/languageManage.active_lang_only_listed') }}</div>
					<div class="form-group {{{ $errors->has('code') ? 'error' : '' }}}">
						{{ Form::label('code', Lang::get('admin/languageManage.language_export_language'), array('class' => 'control-label col-md-3 required-icon')) }}
                        <div class="col-md-3">
                            {{ Form::select('code', $d_arr['language_list'], null, array('class' => 'form-control bs-select', 'id' => 'js-page-type')) }}
                        	<label class="error">{{{ $errors->first('code') }}}</label>
						</div>
						<div class="col-md-1 lang-addnew">
                            <a href="{{ URL::to('admin/manage-language') }}"><span class="add_new label label-success"><i class="fa fa-plus font11"></i> {{{ Lang::get('admin/languageManage.add_new') }}}</span></a>
						</div>
					</div>

                    <div class="form-group {{{ $errors->has('language_file') ? 'error' : '' }}}">
						{{ Form::label('language_file', trans("admin/languageManage.language_import_file"), array('class' => 'col-md-3 control-label required-icon')) }}
						<div class="col-md-5">
							{{ Form::file('language_file', array ('class' => 'filestyle', 'data-buttonText' => Lang::get('common.choose_file'))) }}
							<div class="mt5 mb5">
								<!--<i class="fa fa-question-circle pull-left"></i>
								<div class="show ml20">
									<small class="text-muted">
										{{ str_replace("VAR_FILE_FORMAT",  Config::get('generalConfig.language_image_allowed_extensions'), trans('shop.uploader_allowed_upload_format_text')) }}
										<br />
										{{ str_replace("VAR_IMAGE_RESOLUTION",  Config::get('generalConfig.language_image_width').'x'.Config::get('generalConfig.language_image_height'), trans('shop.allowed_image_resolution')) }}
									</small>
								</div>-->
							</div>
							<label class="error" for="language_file" generated="true">{{{ $errors->first('language_file') }}}</label>
						</div>
					</div>
				</div>

                <div class="form-actions fluid">
					<div class="col-md-offset-3 col-md-9">
						<button type="submit" name="import_language" class="btn green" id="import_language" value="import_language">
							<i class="fa fa-arrow-down"></i> {{ trans("admin/languageManage.language_import_submit") }}
                        </button>
					</div>
				</div>
            {{ Form::close() }}
            <!-- END: LANGUAGE IMPORT FORM -->
        </div>
    </div>
@stop