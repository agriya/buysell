@extends('admin')
{{ $header->setMetaTitle($d_arr['pageTitle']) }}
@section('content')
	<!-- BEGIN: INFO BLOCK -->
    @if (Session::has('success_message') && Session::get('success_message') != "")
        <div class="note note-success">{{	Session::get('success_message') }}</div>
    @endif

    @if (Session::has('error_message') && Session::get('error_message') != "")
        <div class="note note-danger">{{	Session::get('error_message') }}</div>
 	@endif
    <!-- END: INFO BLOCK -->

	<div class="portlet box blue-madison">
		<!-- BEGIN: PAGE TITLE -->
		<div class="portlet-title">
			<div class="caption">
				<i class="fa fa-envelope-o"></i>{{ trans('admin/massEmail.composer.compose_mail') }}
			</div>
            <a href="{{ URL::to('admin/mass-email/list') }}" title="{{ trans('common.back_to_list') }}" class="btn green-stripe default btn-xs pull-right">
                <i class="fa fa-chevron-left"></i> {{ trans('common.back_to_list') }}
            </a>
		</div>
		<!-- END: PAGE TITLE -->
		<div class="portlet-body form" style="display: block;">
			<!-- BEGIN: FORM-->
			@if(isset($mail_details[0]))
				{{ Form::model($mail_details[0], [
				'method' => 'post',
				'id' => 'massEmailComposerfrm', 'class' => 'form-horizontal'
				]) }}
				{{ Form::hidden('getusers', $mail_details[1],array('id' => 'getusers')) }}
				{{ Form::hidden('getusersname', $mail_details[2],array('id' => 'getusersname')) }}
				<?php
					$repeat_every = Input::get('repeat_every');
					$repeat_for = Input::get('repeat_for');
					$mail_to_all = false;
					$mail_to_user = false;
					$mail_to_subscriber = false;
					$user_status_all = false;
					$user_status_active = false;
					$user_status_inactive = false;
					$offer_newsletter_all = false;
					$offer_newsletter_only = false;
					if($mail_details[0]->send_to == 'all') {
						$mail_to_all = true;
					}
					else if($mail_details[0]->send_to == 'newsletter') {
						$mail_to_subscriber = true;
					}
					else if($mail_details[0]->send_to == 'picked_user') {
						$mail_to_user = true;
					}
					if($mail_details[0]->send_to_user_status == 'all') {
						$user_status_all = true;
					}
					else if($mail_details[0]->send_to_user_status == 'active') {
						$user_status_active = true;
					}
					else if($mail_details[0]->send_to_user_status == 'inactive') {
						$user_status_inactive = true;
					}
					$send_to_reseller_user = $mail_details[0]->send_to_reseller_user;
					if($mail_details[0]->offer_newsletter == 'no') {
						$offer_newsletter_all = true;
					}
					else if($mail_details[0]->offer_newsletter == 'yes') {
						$offer_newsletter_only = true;
					}
				?>
			@else
				{{ Form::model($mail_details, [
				'method' => 'post',
				'id' => 'massEmailComposerfrm', 'class' => 'form-horizontal'
				]) }}
				{{ Form::hidden('getusers', null,array('id' => 'getusers')) }}
				{{ Form::hidden('getusersname', null,array('id' => 'getusersname')) }}
				<?php
					$repeat_every = 1;
					$repeat_for = 1;
					$mail_to_all = true;
					$mail_to_user = false;
					$mail_to_subscriber = false;
					$user_status_all = true;
					$user_status_active = false;
					$user_status_inactive = false;
					$send_to_reseller_user = 'no';
					$offer_newsletter_all = true;
					$offer_newsletter_only = false;
				?>
			@endif

			{{ Form::hidden('id',Input::get('id')) }}
				<fieldset>
					<div class="form-body">
						<div class="form-group {{{ $errors->has('mail_to') ? 'error' : '' }}}">
							{{ Form::label('mail_to', trans('admin/massEmail.composer.mail_to'), array('class' => "control-label required-icon col-md-3")) }}
							<div class="col-md-6">
								<div class="radio-list">
                                	<label class="radio-inline">
                                        @if($mail_to_all)
                                            {{ Form::radio('mail_to', 'all', null, array('id' => 'mail_to_user', 'checked' => 'true' ))}}
                                        @else
                                            {{ Form::radio('mail_to', 'all', null, array('id' => 'mail_to_user' ))}}
                                        @endif
                                        {{ Form::label('mail_to_user', trans('admin/massEmail.composer.to_users_only')) }}
                                    </label>

                                    <label class="radio-inline">
                                        @if($mail_to_subscriber)
                                            {{ Form::radio('mail_to', 'newsletter', null, array('id' => 'mail_to_newsletter', 'checked' => 'true' ))}}
                                        @else
                                           {{ Form::radio('mail_to', 'newsletter', null, array('id' => 'mail_to_newsletter' ))}}
                                        @endif
                                        {{ Form::label('mail_to_newsletter', trans('admin/massEmail.composer.to_newsletter')) }}
                                    </label>

                                    <label class="radio-inline">
                                        @if($mail_to_user)
                                            {{ Form::radio('mail_to', 'picked_user', null, array('id' => 'mail_to_picked_user', 'checked' => 'true' ))}}
                                        @else
                                           {{ Form::radio('mail_to', 'picked_user', null, array('id' => 'mail_to_picked_user' ))}}
                                        @endif
                                        {{ Form::label('mail_to_picked_user', trans('admin/massEmail.composer.to_picked_users')) }}
                                    </label>

									<?php if(isset($mail_details[0]['reschedule_id']) != ''){
											 	$mail_det = $mail_details[0]['reschedule_id'];
											} else if(isset($mail_details[0]['id']) != ''){
											 	$mail_det = $mail_details[0]['id'];
											} else {
												$mail_det = '';
											}
									?>
									<span id="get_members_show">
	                                    <button type="button" name="get_members" value="{{Url::to('admin/search-members?send_id='.$mail_det)}}" id="get_members" class="btn green btn-xs ml20 mt5">
	                                    <i class="fa fa-search-plus"></i> {{ trans("admin/massEmail.composer.pick_user")}}</button>
                                    </span>
                                </div>

                                @if(isset($mail_details[0]['id']) != '')
                               		<ul id="userdiv" class="mar0 list-inline ml10"></ul>
                               	@else
                               		<ul id="userdiv" class="mar0 list-inline ml10"></ul>
								@endif

								<label class="error mar0" for="send_to_all" generated="true">{{ $errors->first('mail_to_user','Select any one user') }}</label>
								<label class="error mar0">{{ $errors->first('mail_to') }}</label>
                                <label for="mail_to" generated="true" class="error"></label>
							</div>
						</div>

						<div class="form-group {{{ $errors->has('user_status') ? 'error' : '' }}}" id="user_status">
							{{ Form::label('user_status', trans('admin/massEmail.composer.user_status'), array('class' => "control-label required-icon col-md-3")) }}
							<div class="col-md-7">
                            	<div class="radio-list">
                                    <label class="radio-inline">
                                        @if($user_status_all)
                                            {{ Form::radio('user_status', 'all', null, array('id' => 'user_status_all', 'checked' => 'true' ))}}
                                        @else
                                            {{ Form::radio('user_status', 'all', null, array('id' => 'user_status_all' ))}}
                                        @endif
                                        {{ Form::label('user_status_all', trans('admin/massEmail.composer.to_users_all')) }}
                                    </label>

                                    <label class="radio-inline">
                                        @if($user_status_active)
                                            {{ Form::radio('user_status', 'active', null, array('id' => 'user_status_active', 'checked' => 'true' ))}}
                                        @else
                                            {{ Form::radio('user_status', 'active', null, array('id' => 'user_status_active' ))}}
                                        @endif
                                        {{ Form::label('user_status_active', trans('admin/massEmail.composer.active_user')) }}
                                    </label>

                                    <label class="radio-inline">
                                        @if($user_status_inactive)
                                            {{ Form::radio('user_status', 'inactive', null, array('id' => 'user_status_inactive', 'checked' => 'true' ))}}
                                        @else
                                            {{ Form::radio('user_status', 'inactive', null, array('id' => 'user_status_inactive' ))}}
                                        @endif
                                        {{ Form::label('user_status_inactive', trans('admin/massEmail.composer.inactive_user')) }}
                                    </label>
                                </div>

								<label class="error mar0" for="user_status_err" generated="true">{{ $errors->first('user_status') }}</label>
								<label class="error mar0">{{ $errors->first('user_status') }}</label>
							</div>
						</div>

						<div class="form-group {{{ $errors->has('offer_newsletter') ? 'error' : '' }}}" id="offer_newsletter">
							{{ Form::label('offer_newsletter', trans('admin/massEmail.composer.user_status'), array('class' => "control-label required-icon col-md-3")) }}
							<div class="col-md-6">
                            	<div class="radio-list">
                                    <label class="radio-inline">
                                        @if($offer_newsletter_all)
                                            {{ Form::radio('offer_newsletter', 'no', null, array('id' => 'offer_newsletter_all', 'checked' => 'true' ))}}
                                        @else
                                            {{ Form::radio('offer_newsletter', 'no', null, array('id' => 'offer_newsletter_all' ))}}
                                        @endif
                                        {{ Form::label('offer_newsletter_all', trans('admin/massEmail.composer.to_users_all')) }}
                                    </label>

                                    <label class="radio-inline">
                                        @if($offer_newsletter_only)
                                            {{ Form::radio('offer_newsletter', 'yes', null, array('id' => 'offer_newsletter_only', 'checked' => 'true' ))}}
                                        @else
                                            {{ Form::radio('offer_newsletter', 'yes', null, array('id' => 'offer_newsletter_only' ))}}
                                        @endif
                                        {{ Form::label('offer_newsletter_only', trans('admin/massEmail.composer.offer_newsletter')) }}
                                    </label>
                                </div>

								<label class="error mar0" for="offer_newsletter_err" generated="true">{{ $errors->first('offer_newsletter') }}</label>
								<label class="error mar0">{{ $errors->first('offer_newsletter') }}</label>
							</div>
						</div>

						<div class="form-group {{{ $errors->has('send_on') ? 'error' : '' }}}">
							{{ Form::label('send_on', trans('admin/massEmail.composer.send_on'), array('class' => 'col-md-3 control-label required-icon') ) }}
							<div class="col-md-3">
								<div data-date-format="yyyy-mm-dd" class="input-group date date-picker">
									{{ Form::text('send_on', Input::get('send_on') , array('class' => 'form-control valid send_on', 'data-date-format' => "yyyy-mm-dd", "readonly", "id" => "send_on") ) }}
									<span class="input-group-btn">
										<label class="btn default" for="send_on"><i class="fa fa-calendar"></i></label>
									</span>
								</div>
								<label class="error" for="send_on" generated="true">{{ $errors->first('send_on') }}</label>
							</div>
						</div>

						<div class="form-group {{{ $errors->has('repeat_every') ? 'error' : '' }}}">
							{{ Form::label('repeat_every', trans('admin/massEmail.composer.schedule_for_every'), array('class' => 'col-md-3 control-label') ) }}
							<div class="col-md-9 col-sm-12 col-xs-12 row">
								<div class="col-md-3 col-sm-6 col-xs-12 padrgt0">
									<div class="input-group">
										{{ Form::text('repeat_every', $repeat_every, array('class' => 'form-control', "id" => "repeat_every", "maxlength" => "3") ) }}
										<span class="input-group-addon addon-bg">{{ trans('admin/massEmail.composer.days') }}</span>
									</div>
									<label class="error" for="repeat_every" generated="true">{{ $errors->first('repeat_every') }}</label>
								</div>

								<div class="col-md-6 col-sm-6 col-xs-12 padrgt0">
                                	<div class="input-group">
                                        <span class="input-group-addon addon-bg">{{ trans('admin/massEmail.composer.stop_mail_after_sending') }}</span>
										{{ Form::text('repeat_for', $repeat_for, array('class' => 'form-control', "id" => "repeat_for", "maxlength" => "3") ) }}
										<span class="input-group-addon addon-bg">{{ trans('admin/massEmail.composer.times') }} </span>
									</div>
									<label class="error" for="repeat_for" generated="true">{{ $errors->first('repeat_for') }}</label>
								</div>
							</div>
						</div>

						<div class="form-group {{{ $errors->has('from_name') ? 'error' : '' }}}">
							{{ Form::label('from_name', trans('admin/massEmail.composer.from_name_label'), array('class' => 'col-md-3 control-label required-icon') ) }}
							<div class="col-md-4">
								{{ Form::text('from_name',Input::get('from_name'), array('class' => 'form-control','id' => 'from_name')) }}
								<label class="error">{{ $errors->first('from_name') }}</label>
							</div>
						</div>

						<div class="form-group {{{ $errors->has('from_email') ? 'error' : '' }}}">
							{{ Form::label('from_email', trans('admin/massEmail.composer.from_email_label'), array('class' => 'col-md-3 control-label required-icon') ) }}
							<div class="col-md-4">
								{{ Form::text('from_email',Input::get('from_email'), array('class' => 'form-control','id' => 'from_email')) }}
								<label class="error">{{ $errors->first('from_email') }}</label>
							</div>
						</div>

						<div class="form-group {{{ $errors->has('subject') ? 'error' : '' }}}">
							{{ Form::label('subject', trans('admin/massEmail.composer.subject'), array('class' => 'col-md-3 control-label required-icon') ) }}
							<div class="col-md-4">
								{{ Form::text('subject',Input::get('subject'), array('class' => 'form-control','id' => 'subject')) }}
								<label class="error">{{ $errors->first('subject') }}</label>
							</div>
						</div>

						<div class="form-group ">
							{{ Form::label('content',trans('admin/massEmail.composer.content'), array('class' => 'col-md-3 control-label required-icon') ) }}
							<div class="col-md-8">
								{{ Form::textarea('content', Input::get('content'), array('class' => 'content form-control' , 'rows' => 7, "cols" => 50 )) }}
								<label class="error">{{ $errors->first('content') }}</label>
							</div>
						</div>

                        <div class="form-group">
                            <div class="pull-right">
                                <a href="{{ Url::to('admin/mass-email/list-variable') }}" id="composerVariablePop" class="btn btn-info btn-xs mr20">
                                    <i class="fa fa-cloud-upload"></i> {{ trans('admin/massEmail.composer.variable_lbl') }}
                                </a>
                            </div>
                            <div class="col-md-offset-3 col-md-5 pull-left">
                                <a href="javascript:void(0);" onClick="javascript:triggerMassMailPreview();" id="sendPreviewMailPop" class="btn btn-primary btn-xs">
                                    <i class="fa fa-location-arrow"></i> {{ trans('admin/massEmail.composer.send_preview_mail') }}
                                </a>
                            </div>
                        </div>
					</div>

					<div class="form-actions fluid">
						<div class="col-md-offset-3 col-md-7">
							<button type="button" name="preview_form" value="Preview" class="btn green" onclick="javascript:previewMassMail();">
                            	<i class="fa fa-search bigger-110"></i> {{ trans("admin/massEmail.composer.preview") }}
                            </button>

							<button type="submit" name="add_mass_mail" value="add_mass_mail" class="btn blue">
								<i class="fa fa-check"></i> {{ trans("common.confirm") }}
                            </button>

							@if(!isset($mail_details[0]))
                                <button type="reset" name="reset_mail" id="reset_mail" value="reset_mail" class="btn default">
                                <i class="fa fa-undo"></i> {{ trans("common.reset")}}</button>
                            @endif

							<button type="reset" name="cancel_mail" value="cancel_mail" class="btn red" onclick="javascript:location.href='{{ URL::to('admin/mass-email/list') }}'">
							<i class="fa fa-times"></i> {{ trans("common.cancel")}}</button>
						</div>
					</div>
				</fieldset>
			{{ Form::close() }}
			<!-- END: FORM-->
		</div>
	</div>
@stop

@section('script_content')
	<script src="{{ URL::asset('js/lib/tinymce/tinymce.min.js') }}"></script>

    <script language="javascript" type="text/javascript">
        $(document).ready(function () {
            checkMailTo();

            function checkMailTo() {
                var value = $("input[name='mail_to']:checked").val();
                if(value == 'newsletter') {
                    $("#user_status").hide();
                    $("#get_members_show").hide();
                    $(".userdivname").hide();
                    $("#offer_newsletter").show();
                }
                else if(value == 'picked_user') {
                    $("#user_status").show();
                    $("#get_members_show").show();
                    $(".userdivname").show();
                    $("#offer_newsletter").hide();
                }else{
                	$("#get_members_show").hide();
                	$(".userdivname").hide();
                }
            }

            $("input[name='mail_to']").click(function() {
                checkMailTo();
            });
        });

        tinymce.init({
            menubar: "edit insert view format table tools",
            selector: "textarea",
            mode : "exact",
            elements: "content",
            removed_menuitems: 'newdocument',
            apply_source_formatting : true,
            remove_linebreaks: false,
            height : 400,
            plugins: [
                "advlist autolink lists link image charmap print preview anchor",
                "searchreplace visualblocks code fullscreen",
                "insertdatetime media table contextmenu paste emoticons jbimages"
            ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image jbimages | emoticons",
            relative_urls: false,
            remove_script_host: false
        });

        var err_msg = '';
        var messageFunc = function() { return err_msg; };
        var mes_required = "{{trans('auth/form.required')}}";

        jQuery.validator.addMethod(
        "sentToAll",
            function(value, element) {
                if($("#mail_to_user").is(':checked') || $("#mail_to_supplier").is(':checked') || $("#mail_to_newsletter").is(':checked') || $("#getusers").val()!='')
                {
                    return true;
                }

                return false;
            },'{{ trans('admin/massEmail.composer.select_atleast_any_one_user') }}');

        jQuery.validator.addMethod(
        "userStatus",
            function(value, element) {
                if($("#user_status_all").is(':checked') || $("#user_status_active").is(':checked') || $("#user_status_inactive").is(':checked'))
                {
                    return true;
                }

                return false;
            },'Select User status');

        jQuery.validator.addMethod(
        "tinyMCE",
        function(value, element) {
            var textarea = $("#content");
            var taeditor = tinyMCE.get(textarea.attr('id'));

            if(taeditor.getContent()=='')
            {
                return false;
            }

            return true;
        },'Required');

        $("#massEmailComposerfrm").validate({
            rules: {
                mail_to: {
                    sentToAll: true
                },
                user_status: {
                    userStatus: true
                },
                send_on: {
                    required: true
                },
                repeat_every: {
                    digits: true,
                    min: 1
                },
                repeat_for: {
                    digits: true,
                    min: 1
                },
                from_name: {
                    required: true,
                },
                from_email: {
                    required: true,
                    email: true
                },
                subject: {
                    required: true
                },
                content: {
                    tinyMCE:true
                    //required: true,
                }

            },
            messages: {
                mail_to: {
                    required: mes_required
                },
                user_status: {
                    required: mes_required
                },
                send_on: {
                    required: mes_required
                },
                from_name: {
                    required: mes_required
                },
                from_email: {
                    required: mes_required
                },
                subject: {
                    required: mes_required
                },
                content: {
                    required: mes_required
                }


            },
            submitHandler: function(form) {

                    form.submit();

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
    </script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#send-on').datetimepicker({
                format: 'yyyy-mm-dd',
                highlight: true,
                minDate: -0,

            });
        });

        $("#reset_mail").click(function() {
                $("#userdiv").html('');
                $("#getusers").val('')
                $("#getusersname").val('')
        })

        $("#send_to_all").click(function() {
            if($("#send_to_all").is(':checked'))
            {
                $("#userdiv").html('');
                $("#getusers").val('')
                $("#getusersname").val('')
            }
        })

        $("#get_members").click(function(){
            var linkurl = $(this).val();
            $("#get_members").fancybox({
                href 		: linkurl,
                maxWidth    : 900,
                maxHeight   : 800,
                fitToView   : false,
                width       : '70%',
                height      : '460',
                autoSize    : true,
                closeClick  : false,
                type        : 'iframe',
                openEffect  : 'none',
                closeEffect : 'none',
                'afterClose'  : function() {
                    //window.location.reload();
                }

            });
        });

        function getusers($usersid,$usersname)
        {
            $.fancybox.close();
            $("#send_to_all").attr('checked', false);
            if($("#getusers").val()!='')
            {
                globaluserarray 		= $("#getusers").val().split(',');
                globalusernamearray 	= $("#getusersname").val().split(',');
            }
            else
            {
                globaluserarray 		= new Array();
                globalusernamearray		= new Array();
            }
            $usersid        = eval($usersid);
            $usersname      = eval($usersname);

            for(var i=0;i<$usersid.length;i++)
            {
                //ind=globaluserarray.indexOf($usersid[i]);
                ind = $.inArray($usersid[i], globaluserarray);
                if( ind < 0)
                {
                    globaluserarray.push($usersid[i])
                    globalusernamearray.push($usersname[i])
                    $("#userdiv").html($("#userdiv").html()+'<li class="userdivname btn default green-stripe btn-sm mr5 mb5" id="userdiv_'+$usersid[i]+'" >'+$usersname[i]+' <i class="fa fa-times"></i> </li>');
                    $("#getusers").val(globaluserarray)
                    $("#getusersname").val(globalusernamearray)
                    $(".userdivname").click( function() {
                    removeuser($(this).attr('id'))
                    })
                }
            }
        }

        function removeuser($id)
        {
            $("#"+$id).remove();
            $id=$id.split('_')
            globaluserarray 		= $("#getusers").val().split(',');
            globalusernamearray 	= $("#getusersname").val().split(',');
            var pos = globaluserarray.indexOf($id[1]);
            globaluserarray.splice(pos,1);
            globalusernamearray.splice(pos,1);
            $("#getusers").val(globaluserarray)
            $("#getusersname").val(globalusernamearray)
        }

        window.onload = function(e){

            if($("#getusers").val()!='')
            {
                globaluserarray 		= $("#getusers").val().split(',');
                globalusernamearray 	= $("#getusersname").val().split(',');
            }
            else
            {
                globaluserarray 		= new Array();
                globalusernamearray		= new Array();
            }

            for(var i=0;i<globaluserarray.length;i++)
            {
                $("#userdiv").html($("#userdiv").html()+'<li class="userdivname btn default green-stripe btn-sm mr5 mb5" id="userdiv_'+globaluserarray[i]+'" >'+globalusernamearray[i]+' <i class="fa fa-times"></i> </li>');
                $(".userdivname").click( function() {
                removeuser($(this).attr('id'))
                })
            }
        }

        $("#composerVariablePop").click(function(){
            var linkurl = $(this).attr('href');
            $("#composerVariablePop").fancybox({
                href 		: linkurl,
                maxWidth    : 500,
                maxHeight   : 300,
                fitToView   : false,
                width       : '70%',
                height      : '300',
                autoSize    : true,
                closeClick  : false,
                type        : 'iframe',
                openEffect  : 'none',
                closeEffect : 'none',
                'afterClose'  : function() {
                    //window.location.reload();
                }

            });
        });

        function triggerMassMailPreview()
        {
            var mail_content = tinyMCE.get('content').getContent();
            var linkurl = '{{ Url::to('admin/mass-email/preview-mass-mail') }}';
            $("#massEmailComposerfrm").validate().element("#content");
            $("#massEmailComposerfrm").validate().element("#from_name");
            $("#massEmailComposerfrm").validate().element("#from_email");
            $("#massEmailComposerfrm").validate().element("#subject");
            if(mail_content != '')
            {
                $("#sendPreviewMailPop").fancybox({
                    href 		: linkurl,
                    maxWidth    : 500,
                    maxHeight   : 250,
                    fitToView   : true,
                    width       : '70%',
                    height      : '250',
                    autoSize    : true,
                    closeClick  : false,
                    type        : 'iframe',
                    openEffect  : 'none',
                    closeEffect : 'none',
                    'afterClose'  : function() {
                        //window.location.reload();
                    }
                });
            }
        }

        function previewMassMail()
        {
            tinyMCE.activeEditor.execCommand('mcePreview');
        }

        $(function() {
            $('.send_on').datepicker({
                format: 'yyyy-mm-dd',
                todayHighlight: true,
                autoclose: true,
                startDate: new Date()
            });
        });
    </script>
@stop