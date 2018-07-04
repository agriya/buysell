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
				{{$d_arr['actionicon']}} {{$d_arr['pageTitle']}}
			</div>
		</div>
        <!-- END: PAGE TITLE -->

        <div class="portlet-body form" >
            <!-- BEGIN: BANNER SETTINGS FORM -->
            {{ Form::model($d_arr['language_details'], [
            'method' => 'post',
            'id' => 'language_frm', 'class' => 'form-horizontal','files' => 'true', 'enctype' => 'multipart/form-data'
            ]) }}
            	{{ Form::hidden('languages_id', $d_arr['languages_id']) }}
				<div class="form-body">
					<div class="form-group {{{ $errors->has('name') ? 'error' : '' }}}">
						{{ Form::label('name', trans("admin/languageManage.managelanguage_lang_name_label"), array('class' => 'col-md-3 control-label required-icon')) }}
						<div class="col-md-4">
							{{ Form::text('name', null, array ('class' => 'form-control')); }}
							<label class="error">{{{ $errors->first('name') }}}</label>
						</div>
					</div>

					<div class="form-group {{{ $errors->has('code') ? 'error' : '' }}}">
						{{ Form::label('code', trans("admin/languageManage.managelanguage_lang_code_label"), array('class' => 'col-md-3 control-label required-icon')) }}
						<div class="col-md-4">
							{{ Form::text('code', null, array ('class' => 'form-control')); }}
							<label class="error">{{{ $errors->first('code') }}}</label>
						</div>
					</div>

					<div class="form-group {{{ $errors->has('image_name') ? 'error' : '' }}}">
						{{ Form::label('image_name', trans("admin/languageManage.managelanguage_icon_head"), array('class' => 'col-md-3 control-label required-icon')) }}
						<div class="col-md-5">
							{{ Form::file('image_name', array ('class' => 'filestyle', 'data-buttonText' => Lang::get('common.choose_file'))) }}
							<div class="mt5 mb5">
								<i class="fa fa-question-circle pull-left"></i>
								<div class="show ml20">
									<small class="text-muted">
										{{ str_replace("VAR_FILE_FORMAT",  Config::get('generalConfig.language_image_allowed_extensions'), trans('shop.uploader_allowed_upload_format_text')) }}
										<br />
										{{ str_replace("VAR_IMAGE_RESOLUTION",  Config::get('generalConfig.language_image_width').'x'.Config::get('generalConfig.language_image_height'), trans('shop.allowed_image_resolution')) }}
									</small>
								</div>
							</div>
							<label class="error" for="image_name" generated="true">{{{ $errors->first('image_name') }}}</label>
							@if(count($d_arr['language_details']) > 0)
								<?php $imgPath = URL::asset(Config::get("generalConfig.language_image_folder"));
									$lang_base_path = base_path().'/public/'.Config::get("generalConfig.language_image_folder").$d_arr['language_details']['languages_id'].'.gif'; ?>
								<div class="img16x11">
									@if(file_exists($lang_base_path))
										<img src="{{$imgPath.'/'. $d_arr['language_details']['languages_id'].'.gif'}}" alt="{{ $d_arr['language_details']['name'] }}" />
									@else
										<img src="{{$imgPath.'/flag.gif'}}" alt="{{ Lang::get('admin/languageManage.managelanguage_no_flag') }}" />
									@endif
								</div>
							@endif
						</div>
					</div>

					<div class="form-group {{{ $errors->has('is_translated') ? 'error' : '' }}}">
                        {{ Form::label('is_translated', Lang::get('admin/languageManage.managelanguage_is_transalted_label'), array('class' => 'col-md-3 control-label')) }}
                        <div class="col-md-4">
                            <div class="radio-list">
                                <label class="radio-inline">
                                    {{Form::radio('is_translated','Yes', null, array('class' => 'radio')) }}
                                    <label>{{Lang::get('common.yes')}}</label>
                                </label>
                                <label class="radio-inline">
                                    {{Form::radio('is_translated','No', null , array('class' => 'radio')) }}
                                    <label>{{Lang::get('common.no')}}</label>
                                </label>
                            </div>
                            <div class="mt5 ml5">
								<i class="fa fa-question-circle pull-left"></i>
								<div class="show ml20">
									<small class="text-muted">
										{{ Lang::get('admin/languageManage.select_lang_file_exists') }}
									</small>
								</div>
							</div>
                            <label class="error">{{{ $errors->first('is_translated') }}}</label>
                        </div>
                    </div>

                    <div class="form-group {{{ $errors->has('is_published') ? 'error' : '' }}}">
                        {{ Form::label('is_published', Lang::get('admin/languageManage.managelanguage_is_published_label'), array('class' => 'col-md-3 control-label')) }}
                        <div class="col-md-4">
                            <div class="radio-list">
                                <label class="radio-inline">
                                    {{Form::radio('is_published','Yes', null, array('class' => 'radio')) }}
                                    <label>{{Lang::get('common.yes')}}</label>
                                </label>
                                <label class="radio-inline">
                                    {{Form::radio('is_published','No', null , array('class' => 'radio')) }}
                                    <label>{{Lang::get('common.no')}}</label>
                                </label>
                            </div>
                            <label class="error">{{{ $errors->first('is_published') }}}</label>
                        </div>
                    </div>

					<div class="form-group {{{ $errors->has('status') ? 'error' : '' }}}">
                        {{ Form::label('status', Lang::get('admin/languageManage.managelanguage_status_label'), array('class' => 'col-md-3 control-label')) }}
                        <div class="col-md-4">
                            <div class="radio-list">
                                <label class="radio-inline">
                                    {{Form::radio('status','Yes', null, array('class' => 'radio')) }}
                                    <label>{{Lang::get('common.yes')}}</label>
                                </label>
                                <label class="radio-inline">
                                    {{Form::radio('status','No', null , array('class' => 'radio')) }}
                                    <label>{{Lang::get('common.no')}}</label>
                                </label>
                            </div>
                            <label class="error">{{{ $errors->first('status') }}}</label>
                        </div>
                    </div>
				</div>

                <div class="form-actions fluid">
					<div class="col-md-offset-3 col-md-9">
						@if($d_arr['mode'] == 'edit')
							<button type="submit" name="add_language" class="btn green" id="add_language" value="edit_banner">
								<i class="fa fa-arrow-up"></i> {{ trans("common.update") }}
                            </button>
						@else
							<button type="submit" name="add_language" class="btn green" id="add_language" value="add_banner">
								<i class="fa fa-check"></i> {{trans("common.submit")}}
                            </button>
						@endif

						<button type="reset" name="cancel_language" class="btn default" onclick="window.location = '{{ url::to('admin/manage-language') }}'">
							<i class="fa fa-times"></i> {{trans("common.cancel")}}
                        </button>
					</div>
				</div>
            {{ Form::close() }}
            <!-- END: BANNER SETTINGS FORM -->
        </div>
    </div>
    {{ Form::model($d_arr['language_details'], [
        'method' => 'get',
        'id' => 'imageBannerList_frm', 'class' => 'form-horizontal']) }}
        <div class="portlet blue-hoki box">
            <!-- BEGIN: PAGE TITLE -->
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-language"><sup class="fa fa-list font11"></sup></i> {{ trans('admin/languageManage.managelanguage_lang_list') }}</div>
            </div>
            <!-- END: PAGE TITLE -->

            <!--  BEGIN: BANNER SETTINGS TABLE -->
            <div class="portlet-body clearfix">
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover api-log">
                        <thead>
                            <tr>
                                <th width="40">{{ trans('admin/languageManage.managelanguage_langid_head') }}</th>
                                <th>{{ trans('admin/languageManage.managelanguage_icon_head') }}</th>
                                <th>{{ trans('admin/languageManage.managelanguage_lang_code_head') }}</th>
                                <th>{{ trans('admin/languageManage.managelanguage_lang_name_head') }}</th>
                                <th>{{ trans('admin/languageManage.managelanguage_lang_status_head') }}</th>
                                <th>{{ trans('admin/languageManage.managelanguage_is_published_head') }}</th>
                                <th>{{ trans('admin/languageManage.managelanguage_is_translated_head') }}</th>
                                <th width="180">{{ trans('admin/languageManage.managelanguage_action_head') }}</th>
                            </tr>
                        </thead>

                        <tbody>
							@if(count($details) > 0)
	                            @foreach($details as $language)
	                                <tr>
	                                    <td>{{ $language->languages_id }}</td>
	                                    <?php
											$file_imgPath = URL::asset(Config::get("generalConfig.language_image_folder"));
										    $lang_base_path = base_path().'/public/'.Config::get("generalConfig.language_image_folder").$language->languages_id.'.gif';
										    $files_exits = false;
										    $lang_code = $language->code;
											if (is_dir(app_path() . '/lang/'.$lang_code)
															&& file_exists(app_path() . '/lang/'.$lang_code.'/common.php')) {
												$files_exits = true;
											}
										?>
										@if(file_exists($lang_base_path))
	                                    	<td><span class="img16x11"><img src="{{$file_imgPath.'/'.$language->languages_id.'.gif'}}" alt="{{ $language->name }}"></span></td>
	                                    @else
	                                    	<td><span class="img16x11"><img src="{{$file_imgPath.'/flag.gif'}}" alt="{{ Lang::get('admin/languageManage.managelanguage_no_flag') }}" ></span></td>
	                                    @endif
	                                    <td>{{ $language->code }}</td>
	                                    <td>{{ $language->name }}</td>
	                                    <td>
											<?php
												$lbl_class = '';
												if($language->status == 'Yes') {
													$lbl_class = "label-success";
													$status = Lang::get('common.yes');
												}
												elseif($language->status == 'No') {
													$lbl_class = "label-danger";
													$status = Lang::get('common.no');
												}
											?>
                                            <span class="label {{ $lbl_class }}">{{ $status }}</span>
                                        </td>
	                                    <td>
											<?php
												$txt_col = '';
												if($language->is_published == 'Yes') {
													$txt_col = "text-success";
													$status_is_published = Lang::get('common.yes');
												}
												elseif($language->is_published == 'No') {
													$txt_col = "text-danger";
													$status_is_published = Lang::get('common.no');
												}
											?>
                                            <span class="{{ $txt_col }}">{{ $status_is_published }}</span>
                                        </td>
	                                    <td>
											<?php
												$txt_col = '';
												if($language->is_translated == 'Yes') {
													$txt_col = "text-success";
													$status_is_translated = Lang::get('common.yes');
												}
												elseif($language->is_translated == 'No') {
													$txt_col = "text-danger";
													$status_is_translated = Lang::get('common.no');
												}
											?>
                                            <span class="{{ $txt_col }}">{{ $status_is_translated }}</span>
                                        </td>
	                                    <td class="status-btn">
	                                    	@if($language->code != 'en')
												<a class="btn blue btn-xs" title="{{trans('common.edit') }}" href="{{ url::to('admin/manage-language')}}?languages_id={{$language->languages_id}}"><i class="fa fa-edit"></i></a>
												<a href="javascript:void(0)" onclick="doAction('{{ $language->languages_id }}', 'delete')" class="btn btn-xs red" title="{{trans('common.delete') }}"><i class="fa fa-trash-o"></i></a>
												@if(!$files_exits)
													<span class="label label-warning" title="{{ Lang::get('admin/languageManage.lang_not_imported') }}">{{ Lang::get('admin/languageManage.not_imported') }}</span>
												@endif
											@endif
	                                    </td>
	                                </tr>
	                            @endforeach
                            @else
                                <tr>
                                    <td colspan="8"><p class="alert alert-info">{{ trans('admin/languageManage.managelanguage_no_lang_to_list') }}</p></td>
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
    {{ Form::open(array('id'=>'languageDeleteActionfrm', 'method'=>'post', 'url' => URL::action('AdminManageLanguageController@postLanguageDelete'))) }}
    {{ Form::hidden('language_id', '', array('id' => 'language_id')) }}
    {{ Form::hidden('lang_action', '', array('id' => 'lang_action')) }}
    {{ Form::close() }}
    <div id="fn_dialog_confirm_msg" class="confirm-delete" style="display:none;"></div>
    <div id="dialog-lang-confirm" title="" style="display:none;">
	    <span class="ui-icon ui-icon-alert"></span>
		<span id="dialog-lang-confirm-content" class="show ml15"></span>
	</div>
@stop

@section('script_content')
	<script language="javascript" type="text/javascript">
        var mes_required = "{{trans('auth/form.required')}}";
        $("#language_frm").validate({
            rules: {
            	name: {
					required: true,
				},
				code: {
					required: true,
					maxlength: 2,
				},
                @if(count($d_arr['language_details']) == 0)
                image_name: {
                    required: true,
                     accept: "gif"
                },
                @else
                image_name: {
                    accept: "gif"
                },
                @endif
                display: {
                    required: true,
                },
            },
            messages: {
            	name: {
					required: mes_required
				},
				code: {
					required: mes_required
				},
                @if(count($d_arr['language_details']) == 0)
                image_name: {
                    required: mes_required,
                    accept: "{{trans('admin/languageManage.file_accept')}}"
                },
                @else
                image_name: {
                    accept: "{{trans('admin/languageManage.file_accept')}}"
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
            var cfg_site_name = "{{ Config::get('generalConfig.site_name') }}" ;
            $(window).load(function(){
             $(".fn_dialog_confirm").click(function(){

                    var atag_href   = $(this).attr("href");
                    var cmsg        = "{{ trans('admin/languageManage.want_to_delete_image') }}";
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

        function doAction(language_id, lang_action)
		{
			if(lang_action == 'delete')
			{
				$('#dialog-lang-confirm-content').html('{{ trans('admin/languageManage.managelanguage_are_you_sure_want') }}');
			}
			$("#dialog-lang-confirm").dialog({ title: cfg_site_name, modal: true,
				buttons: {
					"{{ trans('common.yes') }}": function() {
						$(this).dialog("close");
						$('#lang_action').val(lang_action);
						$('#language_id').val(language_id);
						document.getElementById("languageDeleteActionfrm").submit();
					}, "{{ trans('common.cancel') }}": function() { $(this).dialog("close"); }
				}
			});
			return false;
		}
    </script>
@stop