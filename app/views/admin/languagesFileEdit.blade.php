@extends('admin')
{{ $header->setMetaTitle($d_arr['pageTitle']) }}
@section('content')
    <!-- BEGIN: INFO BLOCK -->
	@if (Session::has('success_message') && Session::get('success_message') != "")
        <div class="note note-success">{{   Session::get('success_message') }}</div>
    @endif
    @if (Session::has('warning_message') && Session::get('warning_message') != "")
        <div class="note note-warning">{{   Session::get('warning_message') }}</div>
    @endif
    @if (Session::has('error_message') && Session::get('error_message') != "")
        <div class="note note-danger">{{    Session::get('error_message') }}</div>
    @endif
	<!-- END: INFO BLOCK -->

    <div class="portlet box blue-madison">
        <!-- BEGIN: PAGE TITLE -->
		<div class="portlet-title">
			<div class="caption">
				<i class="fa fa-language"><sup class="fa fa-pencil"></sup></i>{{$d_arr['pageTitle']}}
			</div>
		</div>
        <!-- END: PAGE TITLE -->

        <div class="portlet-body form" >
            <!-- BEGIN: LANGUAGE EXPORT FORM -->
            {{ Form::model(array(), [
            'method' => 'post',
            'id' => 'language_frm', 'class' => 'form-horizontal']) }}
				{{ Form::hidden('active_block', $d_arr['active_block']) }}
            	@if($d_arr['active_block'] == 'block_form_directory_list')
					<div class="form-body">
	                    <div class="form-group">
	                        {{ Form::label('language', Lang::get('admin/languageManage.language_export_language'), array('class' => 'control-label col-md-3 required-icon')) }}
	                        <div class="col-md-4">
	                            {{ Form::select('language', $d_arr['language_list'], Input::get('language'), array('class' => 'form-control bs-select input-medium', 'id' => 'language')) }}
	                        	<label class="error">{{{ $errors->first('language') }}}</label>
							</div>
	                    </div>

	                    <div class="form-group">
	                        {{ Form::label('directory', Lang::get('admin/languageManage.langedit_directory'), array('class' => 'control-label col-md-3 required-icon')) }}
	                        <div class="col-md-4">
	                            {{ Form::select('directory', $d_arr['lang_folders_arr'], Input::get('directory'), array('class' => 'form-control bs-select input-medium', 'id' => 'directory')) }}
	                        	<label class="error">{{{ $errors->first('directory') }}}</label>
							</div>
	                    </div>
					</div>

	                <div class="form-actions fluid">
						<div class="col-md-offset-3 col-md-9">
							<button type="submit" name="submit" class="btn green" id="submit_directory" value="submit_directory">
								<i class="fa fa-arrow-up"></i> {{ trans("admin/languageManage.langedit_submit") }}
	                        </button>
						</div>
					</div>
				@elseif($d_arr['active_block'] == 'block_form_files_list')
					{{ Form::hidden('language', $input['language']) }}
					{{ Form::hidden('directory', $input['directory']) }}
					<div class="form-body">
	                    <div class="form-group">
	                        {{ Form::label('language', Lang::get('admin/languageManage.language_export_language'), array('class' => 'control-label col-md-3 required-icon')) }}
	                        <div class="col-md-4 filedit-lbl">
	                            {{ $d_arr['lang_name'] }}
							</div>
	                    </div>

	                    <div class="form-group">
	                        {{ Form::label('directory', Lang::get('admin/languageManage.langedit_directory'), array('class' => 'control-label col-md-3 required-icon')) }}
	                        <div class="col-md-4 filedit-lbl">
								{{ Lang::get('admin/languageManage.langedit_'.$input['directory']) }}
							</div>
	                    </div>

	                    <div class="form-group">
	                        {{ Form::label('file', Lang::get('admin/languageManage.language_import_file'), array('class' => 'control-label col-md-3 required-icon')) }}
	                        <div class="col-md-4">
	                            {{ Form::select('file', $d_arr['file_list'], Input::get('file'), array('class' => 'form-control bs-select input-medium', 'id' => 'file')) }}
	                        	<label class="error">{{{ $errors->first('file') }}}</label>
							</div>
	                    </div>
					</div>

	                <div class="form-actions fluid">
						<div class="col-md-offset-3 col-md-9">
							<button type="submit" name="submit_back" class="btn default" id="submit_back_files" value="files">
								<i class="fa fa-arrow-left"></i> {{ trans("admin/languageManage.langedit_back") }}
	                        </button>
							<button type="submit" name="submit" class="btn green" id="submit_files" value="submit_files">
								<i class="fa fa-check"></i> {{ trans("admin/languageManage.langedit_submit") }}
	                        </button>
						</div>
					</div>
				@elseif($d_arr['active_block'] == 'block_form_edit_phrases')
					{{ Form::hidden('language', $input['language']) }}
					{{ Form::hidden('directory', $input['directory']) }}
					{{ Form::hidden('file', $input['file']) }}
					<div class="form-body">
						@foreach($d_arr['lang_orig'] as $key => $value)
							@if($value == 'levelone')
								<h2>{{ $key }}</h2>
							@elseif($value == 'leveltwo')
								<h2>{{ $key }}</h2>
							@else
			                    <div class="form-group">
			                        {{ Form::label($key, str_replace('~', '.', $key), array('class' => 'control-label col-md-3')) }}
			                        <div class="col-md-4">
			                            <textarea name="lang_phrases[{{ $key }}]" id="{{ $key }}" tabindex="1000" rows="3" cols="50">{{ $value }}</textarea>
									</div>
			                    </div>
			                @endif
						@endforeach
					</div>

	                <div class="form-actions fluid">
						<div class="col-md-offset-3 col-md-9">
							<button type="submit" name="submit_back" class="btn green" id="submit_back_phrases" value="phrases">
								<i class="fa fa-arrow-left"></i> {{ trans("admin/languageManage.langedit_back") }}
	                        </button>
							<button type="submit" name="submit" class="btn green" id="submit_phrases" value="submit_phrases">
								<i class="fa fa-arrow-up"></i> {{ trans("admin/languageManage.langedit_submit") }}
	                        </button>
						</div>
					</div>
				@endif
            {{ Form::close() }}
            <!-- END: LANGUAGE EXPORT FORM -->
        </div>
    </div>
@stop

@section('script_content')
	<script language="javascript" type="text/javascript">

    </script>
@stop