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
				<i class="fa fa-language"><sup class="fa fa-cog font11"></sup></i> {{$d_arr['pageTitle']}}
			</div>
		</div>
        <!-- END: PAGE TITLE -->
        <div class="portlet-body form" >
            <!-- BEGIN: LANGUAGE SETTINGS FORM -->
            {{ Form::model(array(), ['method' => 'post', 'id' => 'language_frm', 'class' => 'form-horizontal','files' => 'true', 'enctype' => 'multipart/form-data']) }}
				<div class="form-body">
					<div class="form-group {{{ $errors->has('is_translated') ? 'error' : '' }}}">
                        {{ Form::label('multi_lang_support', Lang::get('admin/languageManage.managelanguage_is_multilang_support_label'), array('class' => 'col-md-3 control-label')) }}
                        <div class="col-md-4">
                            <div class="radio-list">
                                <label class="radio-inline">
                                    {{Form::radio('multi_lang_support','Yes', ($is_multi_lang_support)?true:false, array('class' => 'radio')) }}
                                    <label>{{Lang::get('common.yes')}}</label>
                                </label>
                                <label class="radio-inline">
                                    {{Form::radio('multi_lang_support','No', (!$is_multi_lang_support)?true:false, array('class' => 'radio')) }}
                                    <label>{{Lang::get('common.no')}}</label>
                                </label>
                            </div>
                            <label class="error">{{{ $errors->first('is_translated') }}}</label>
                        </div>
                    </div>
                    
                    <div class="form-group {{{ $errors->has('default_lang_code') ? 'error' : '' }}}">
                        {{ Form::label('default_lang_code', Lang::get('admin/languageManage.managelanguage_def_lang_label'), array('class' => 'control-label col-md-3 required-icon')) }}
                        <div class="col-md-3">
                            {{ Form::select('default_lang_code', array('' => '--Select--') + $d_arr['language_list'], $lang, array('class' => 'form-control bs-select', 'id' => 'js-page-type')) }}
                            <label class="error">{{{ $errors->first('default_lang_code') }}}</label>
						</div>
						<div class="col-md-2 lang-addnew">
                            <a href="{{ URL::to('admin/manage-language') }}"><span class="add_new label label-success"><i class="fa fa-plus font11"></i> {{{ Lang::get('admin/languageManage.add_new') }}}</span></a>
						</div>
                    </div>
				</div>

                <div class="form-actions fluid">
					<div class="col-md-offset-3 col-md-9">
						<button type="submit" name="add_language" class="btn green" id="add_language" value="edit_banner">
							<i class="fa fa-arrow-up"></i> {{ trans("common.update") }}
                        </button>
					</div>
				</div>
            {{ Form::close() }}
            <!-- END: LANGUAGE SETTINGS FORM -->
        </div>
    </div>
@stop

@section('script_content')
	<script language="javascript" type="text/javascript">
		$("#language_frm").validate({
            rules: {
            	default_lang_code: {
					required: true,
				}
			},
            messages: {
            	default_lang_code: {
					required: mes_required
				},
            },
        });
    </script>
@stop