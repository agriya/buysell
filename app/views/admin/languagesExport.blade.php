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
				<i class="fa fa-upload"></i> {{$d_arr['pageTitle']}}
			</div>
		</div>
        <!-- END: PAGE TITLE -->

        <div class="portlet-body form" >
            <!-- BEGIN: LANGUAGE EXPORT FORM -->
            {{ Form::model(array(), [
            'method' => 'post',
            'id' => 'language_frm', 'class' => 'form-horizontal']) }}
			{{ Form::hidden('folder', 'all') }}
				<div class="form-body">
					<div class="alert alert-info">{{ Lang::get('admin/languageManage.active_and_imported_lang_only_listed') }}</div>
                    <div class="form-group">
                        {{ Form::label('language', Lang::get('admin/languageManage.language_export_language'), array('class' => 'control-label col-md-3 required-icon')) }}
                        <div class="col-md-3">
                            {{ Form::select('language', $d_arr['language_list'], null, array('class' => 'form-control bs-select', 'id' => 'js-page-type')) }}
                        	<label class="error">{{{ $errors->first('language') }}}</label>
						</div>
						<div class="col-md-1 lang-addnew">
                            <a href="{{ URL::to('admin/manage-language') }}"><span class="add_new label label-success"><i class="fa fa-plus font11"></i> {{{ Lang::get('admin/languageManage.add_new') }}}</span></a>
						</div>
                    </div>
				</div>

                <div class="form-actions fluid">
					<div class="col-md-offset-3 col-md-9">
						<button type="submit" name="add_language" class="btn green" id="add_language" value="edit_banner">
							<i class="fa fa-arrow-up"></i> {{ trans("admin/languageManage.language_export_submit") }}
                        </button>
					</div>
				</div>
            {{ Form::close() }}
            <!-- END: LANGUAGE EXPORT FORM -->
        </div>
    </div>
@stop