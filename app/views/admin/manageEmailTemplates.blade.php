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

    @if (isset($error) && $error != "")
        <div class="note note-danger">{{ $error }}</div>
    @endif
	<!-- END: INFO BLOCK -->

    <div class="portlet box blue-madison">
        <!-- BEGIN: PAGE TITLE -->
		<div class="portlet-title">
			<div class="caption">
				{{$d_arr['actionicon']}} {{$d_arr['pageTitle']}}
			</div>
		</div>
        <!-- END: PAGE TITLE -->

        <div class="portlet-body form" >
            <!-- BEGIN: Email template language selection -->
            {{ Form::model($d_arr, [
            'method' => 'get',
            'id' => 'language_frm', 'class' => 'form-horizontal','files' => 'true', 'enctype' => 'multipart/form-data'
            ]) }}
            	<div class="form-body">
					<div class="form-group {{{ $errors->has('name') ? 'error' : '' }}}">
						{{ Form::label('current_language', trans("admin/languageManage.managelanguage_lang_name_label"), array('class' => 'col-md-3 control-label required-icon')) }}
						<div class="col-md-4">
							{{ Form::select('current_language', $languages_list, $current_language, array ('class' => 'form-control bs-select input-medium')); }}
							<label class="error">{{{ $errors->first('current_language') }}}</label>
						</div>
					</div>
				</div>

                <div class="form-actions fluid">
					<div class="col-md-offset-3 col-md-9">
						<button type="submit" name="chagne_language" class="btn green" id="chagne_language" value="chagne_language">
							<i class="fa fa-check"></i> {{trans("common.submit")}}
                        </button>
					</div>
				</div>
            {{ Form::close() }}
            <!-- END: BANNER SETTINGS FORM -->
        </div>
    </div>
    {{ Form::model($d_arr, [
        'method' => 'post',
        'id' => 'emailTemplatesList_frm', 'class' => 'form-horizontal']) }}
        <div class="portlet blue-hoki box">
            <!-- BEGIN: PAGE TITLE -->
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-language"><sup class="fa fa-list font11"></sup></i> {{ trans('admin/manageEmailTemplate.email_templates') }}</div>
            </div>
            <!-- END: PAGE TITLE -->

            <!--  BEGIN: BANNER SETTINGS TABLE -->
            <div class="portlet-body form">
                @if(!empty($d_arr['languag_phrases']))
                    <div class="form-body">
                        <div class="table-scrollable">
                            <table class="table table-bordered table-hover custom-list">
                                <thead>
                                    <tr>
                                        <th colspan="2">
                                            <div>
                                                {{trans('admin/manageEmailTemplate.allow_common_variables')}}
                                                <ul class="list-inline mt10">
                                                    <li>VAR_SITE_NAME</li>
                                                    <li>VAR_SITE_URL</li>
                                                </ul>
                                            </div>
                                        </th>
                                    </tr>
        
                                </thead>
        
                                <tbody>
                                    @foreach($d_arr['languag_phrases'] as $temp_key =>  $email_templates)
                                        <?php
                                            $used_keywords = array();
                                            if($email_templates['used_keywords']!='')
                                            $used_keywords = explode(',', $email_templates['used_keywords']);
                                        ?>
                                        <tr>
                                            <th colspan="2">
                                                <div>
                                                    {{{$email_templates['title']}}}
                                                    {{ Form::hidden("languag_phrases[".$temp_key."][title]", null) }} 
                                                    {{ Form::hidden("languag_phrases[".$temp_key."][used_keywords]", null) }} 
                                                    @if(!empty($used_keywords))
                                                        <ul class="list-inline mt10">
                                                            @foreach($used_keywords as $keyword)
                                                                <li>{{{$keyword}}}</li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </div>
                                            </th>
                                        </tr>
                                        
                                        <tr>
                                            <td width="280"><label for="activation_subject">{{trans('admin/manageEmailTemplate.subject')}}</label></td>
                                            <td>
                                                {{Form::textarea('languag_phrases['.$temp_key.'][subject]', null, array('cols' => '15', 'rows' =>'5', 'class' => 'form-control'))}}
                                            </td>
                                        </tr>
        
                                        <tr>
                                            <td width="280"><label for="{{$temp_key.'_content'}}">{{trans('admin/manageEmailTemplate.content')}}</label></td>
                                            <td>
                                                {{Form::textarea('languag_phrases['.$temp_key.'][content]', null, array('cols' => '15', 'rows' =>'5', 'class' => 'form-control'))}}
                                            </td>    
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        {{Form::hidden('current_language', $current_language)}}
                        <button id="edit_submit" name="edit_submit" class="btn green" type="submit"><i class="fa fa-arrow-up"></i> {{trans("common.update")}}</button>
                        <button id="cancel_submit" name="cancel_submit" class="btn default" type="button" onclick="window.location = '{{ url::action('AdminManageEmailTemplateController@getIndex') }}'"><i class="fa fa-times"></i> {{trans("common.cancel")}}</button>
                    </div>
                @endif
            </div>
            <!--  END: BANNER SETTINGS TABLE -->
        </div>
    {{ Form::close() }}
    <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
@stop