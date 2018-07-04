@extends('admin')
@section('content')
	<!--- BEGIN: SUCCESS INFO --->
    @if (Session::has('success_message') && Session::get('success_message') != "")
        <div class="note note-success">{{	Session::get('success_message') }}</div>
    @endif
    <!--- END: SUCCESS INFO --->

    @if (Session::has('error_message') && Session::get('error_message') != "")
    	<!--- BEGIN: ERROR INFO --->
        <div class="note note-danger">{{	Session::get('error_message') }}</div>
        <!--- END: ERROR INFO --->
    @endif

	<!--- BEGIN: ERROR INFO --->
	@if(isset($d_arr['error_msg']) && $d_arr['error_msg'] != '')
		<!--<h1 class="page-title">{{ $d_arr['pageTitle'] }}</h1>-->
		<p class="note note-danger">{{ $d_arr['error_msg'] }}</p>
	@endif
	<!--- END: ERROR INFO --->

	{{ Form::model($user_details, ['method' => 'post', 'id' => 'addMemberfrm', 'class' => 'form-horizontal']) }}
    {{ Form::hidden('mode', $d_arr['mode'], array("id" => "mode")) }}
    {{ Form::hidden('user_id', $d_arr['user_id'], array("id" => "user_id")) }}
        <div class="portlet box blue-hoki">
            <!--- BEGIN: TITLE --->
            <div class="portlet-title">
                <div class="caption">
                    @if($d_arr['mode'] == 'edit')<i class="fa fa-edit"></i>@else<i class="fa fa-plus-circle"></i>@endif {{ $d_arr['pageTitle'] }}
                </div>
                <a href="{{ URL::to('admin/users') }}" title="{{ Lang::get('common.back_to_list') }}" class="btn default btn-xs purple-stripe pull-right">
                    <i class="fa fa-chevron-left"></i> {{ Lang::get('common.back_to_list') }}
                </a>
            </div>
            <!--- END: TITLE --->

            <div class="portlet-body form">
            	<!--- BEGIN: ADD MEMBER BASIC DETAILS --->
                <div class="form-body">
                    <div class="form-group">
                        {{ Form::label('first_name', Lang::get('admin/addMember.first_name_label'), array('class' => 'control-label required-icon col-md-3')) }}
                        <div class="col-md-4">
                            {{ Form::text('first_name', null, array('class' => 'form-control')) }}
                            <label class="error">{{ $errors->first('first_name') }}</label>
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('last_name', Lang::get('admin/addMember.last_name_label'), array('class' => 'control-label required-icon col-md-3')) }}
                        <div class="col-md-4">
                            {{ Form::text('last_name', null, array('class' => 'form-control')) }}
                            <label class="error">{{ $errors->first('last_name') }}</label>
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('user_name', Lang::get('admin/addMember.user_name'), array('class' => 'control-label required-icon col-md-3')) }}
                        <div class="col-md-4">
                            {{ Form::text('user_name', null, array('class' => 'form-control')) }}
                            <label class="error">{{ $errors->first('user_name') }}</label>
                        </div>
                    </div>
                    <div class="form-group {{{ $errors->has('email') ? 'error' : '' }}}">
                        {{ Form::label('email', Lang::get('admin/addMember.email_label'), array('class' => 'control-label required-icon col-md-3')) }}
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-addon">
                                <i class="fa fa-envelope"></i>
                                </span>
                                {{ Form::text('email',null,array('class' => 'form-control')) }}
                            </div>
                            <label class="error" for="email" generated="true">{{ $errors->first('email') }}</label>
                        </div>
                    </div>
                    @if ($d_arr['user_id'] != 1)
                        <div class="form-group {{{ $errors->has('email_address') ? 'error' : '' }}}">
                            {{ Form::label('group_id', trans('admin/addMember.group_name_label'), array('class' => 'control-label required-icon col-md-3')) }}
                            <div class="col-md-4">
                            	{{ Form::select('group_id', $group_array, $user_group_id, array('class' => 'form-control bs-select input-medium')) }}
                            </div>
                        </div>

                        <div class="form-group {{{ $errors->has('status') ? 'error' : '' }}}">
                            {{ Form::label('status', trans('admin/addMember.status'), array('class' => 'control-label required-icon col-md-3')) }}
                            <div class="col-md-4">
                            	{{ Form::select('status', $status_arr, $status, array('class' => 'form-control bs-select input-medium')) }}
                            </div>
                        </div>
                    @else
                    	{{ Form::hidden('group_id', 1, array("id" => "group_id")) }}
                    @endif

                    <?php
                        $mandatory_class = ($d_arr['mode'] != 'edit') ? "required-icon" : "";
                    ?>
                    <div class="form-group {{{ $errors->has('password') ? 'error' : '' }}}">
                        {{ Form::label('password', Lang::get('admin/addMember.password_label'), array('class' => "control-label required-icon col-md-3")) }}
                         <div class="col-md-4">
                            <div class="input-group input-group-new">
                                {{ Form::password('password',array('class' => 'form-control')) }}
                                <span class="input-group-addon">
                                <i class="fa fa-lock"></i>
                                </span>
                            </div>
                            <label class="error" for="password" generated="true">{{ $errors->first('password') }}</label>
                        </div>
                     </div>
                    <div class="form-group {{{ $errors->has('password_confirmation') ? 'error' : '' }}}">
                        {{ Form::label('password_confirmation', Lang::get('admin/addMember.confirm_password_label'), array('class' => "control-label required-icon col-md-3")) }}
                        <div class="col-md-4">
                            <div class="input-group input-group-new">
                                {{ Form::password('password_confirmation',array('class' => 'form-control')) }}
                                <span class="input-group-addon">
                                <i class="fa fa-lock"></i>
                                </span>
                            </div>
                            <label class="error" for="password_confirmation" generated="true">{{ $errors->first('password_confirmation') }}</label>
                        </div>
                    </div>
                </div>
                <!--- END: ADD MEMBER BASIC DETAILS --->

                <!--- BEING: ACTION --->
                <div class="form-actions fluid">
                    <div class="col-md-offset-3 col-md-5">
                        @if($d_arr['mode'] == 'edit')
                            <button type="submit" name="add_members" value="add_members" class="btn green">
                                <i class="fa fa-arrow-up"></i> {{ Lang::get('common.update') }}
                            </button>
                        @else
                            <button type="submit" name="add_members" value="add_members" class="btn green">
                            	<i class="fa fa-check"></i> {{ Lang::get('common.submit') }}
                            </button>
                        @endif
                        <button type="reset" name="reset_members" value="reset_members" class="btn default" onclick="javascript:location.href='{{ URL::to('admin') }}'">
                        	<i class="fa fa-times"></i> {{ Lang::get('common.cancel') }}
                        </button>
                    </div>
                </div>
                <!--- END: ACTION --->
            </div>
       </div>
    {{ Form::close() }}
@stop

@section('script_content')
	<script language="javascript" type="text/javascript">
        var err_msg = '';
        var messageFunc = function() { return err_msg; };
        jQuery.validator.addMethod(
              "chkIsNameHasRestrictedWordsLike",
              function(value, element) {
                if(value != "") {
                    var filterWords = new Array();
                    var restricted_keywords = "{{ Config::get('webshopauthenticate.screen_name_restrict_keywords_like') }}";
                    filterWords = restricted_keywords.split(",");
                    for(i = 0; i < filterWords.length; i++) {
                        // "i" is to ignore case
                        var regex = new RegExp(filterWords[i], "gi");
                        if(value.match(regex)) {
                            err_msg = "{{ Lang::get('auth/form.register.restricted_keyword') }}";
                            err_msg = err_msg.replace("{0}", filterWords[i]);
                            return false;
                        }
                    }
                    return true;
                }
                return true;
              },
              messageFunc
            );
            jQuery.validator.addMethod(
                  "chkIsNameHasRestrictedWordsExact",
                  function(value, element) {
                    if(value != "") {
                        var filterWords = new Array();
                        var restricted_keywords = "{{ Config::get('webshopauthenticate.screen_name_restrict_keywords_exact') }}";
                        filterWords = restricted_keywords.split(",");
                        for(i = 0; i < filterWords.length; i++) {
                            // "i" is to ignore case
                            var regex = new RegExp('\\b' + filterWords[i] + '\\b' , "gi");
                            if(value.match(regex)) {
                                err_msg = "{{ Lang::get('auth/form.register.restricted_keyword') }}";
                                err_msg = err_msg.replace("{0}", filterWords[i]);
                                return false;
                            }
                        }
                        return true;
                    }
                    return true;
                  },
                  messageFunc
            );
        jQuery.validator.addMethod(
            "chkAlphaNumericchars",
            function(value, element) {
                if(value!=""){
                    if (/^[a-zA-Z0-9\s]*$/.test(value))
                        return true;
                    return false;
                }
                return true;
            },
            "{{ Lang::get('auth/form.edit-profile.merchant_signup_specialchars_not_allowed')}}"
        );
        jQuery.validator.addMethod(
            "chkspecialchars",
            function(value, element) {
                if(value!=""){
                    if (/^[a-zA-Z0-9'/,&() -]*$/.test(value))
                        return true;
                    return false;
                }
                return true;
            },
            "{{ Lang::get('auth/form.edit-profile.merchant_signup_specialchars_not_allowed')}}"
        );
        jQuery.validator.addMethod(
			"alphanumeric",
			function(value, element) {
    			if(value!=""){
					if(/^[a-z0-9-_]+$/.test(value))
						return true;
					return false;
				}
				return true;
			},
			"{{ Lang::get('auth/form.edit-profile.accept_only_alphanumeric_underscore') }}"
		);
        var mes_required = "{{ Lang::get('auth/form.required') }}";
        $("#addMemberfrm").validate({
            onfocusout: injectTrim($.validator.defaults.onfocusout),
            rules: {
                first_name: {
                    required: true,
                    minlength: "{{ Config::get('webshopauthenticate.fieldlength_name_min_length') }}",
                    maxlength: "{{ Config::get('webshopauthenticate.fieldlength_name_max_length') }}",
                    chkIsNameHasRestrictedWordsLike: true,
                    chkIsNameHasRestrictedWordsExact: true,
                    chkspecialchars: true
                },
                last_name: {
                    required: true,
                    minlength: "{{ Config::get('webshopauthenticate.fieldlength_name_min_length') }}",
                    maxlength: "{{ Config::get('webshopauthenticate.fieldlength_name_max_length') }}",
                    chkIsNameHasRestrictedWordsLike: true,
                    chkIsNameHasRestrictedWordsExact: true,
                    chkspecialchars: true
                },
                user_name: {
                    required: true,
                    minlength: "{{ Config::get('webshopauthenticate.fieldlength_password_min') }}",
                    maxlength: "{{ Config::get('webshopauthenticate.fieldlength_password_max') }}",
                    chkIsNameHasRestrictedWordsLike: true,
                    chkIsNameHasRestrictedWordsExact: true,
                    alphanumeric : true
                },
                email: {
                    required: true,
                    email: true
                }

                @if($d_arr['mode'] == 'add')
                    ,
                    "password": {
                        required: true,
                        minlength: "{{ Config::get('webshopauthenticate.fieldlength_password_min') }}",
                        maxlength: "{{ Config::get('webshopauthenticate.fieldlength_password_max') }}"
                    },
                    "password_confirmation": {
                        required: true,
                        equalTo: "#password"
                    }
                @else
                    ,
                    "password": {
                         minlength:  {
                            param: "{{ Config::get('webshopauthenticate.fieldlength_password_min') }}",
                            depends: function (element) {
                                   return $("#password").val() != "";
                            }
                         },
                         maxlength:  {
                            param: "{{ Config::get('webshopauthenticate.fieldlength_password_max') }}",
                            depends: function (element) {
                                   return $("#password").val() != "";
                            }
                         }
                     },
                     "password_confirmation": {
                        equalTo: {
                            param: "#password",
                            depends:  function (element) {
                                   return $("#password").val() != "";
                            }
                        }
                     }
                @endif
            },
            messages: {
                first_name: {
                    required: mes_required
                },
                last_name: {
                    required: mes_required
                },
                user_name: {
                    required: mes_required,
                },
                email: {
                    required: mes_required
                }

                @if($d_arr['mode'] == 'add')
                    ,
                    password: {
                        required: mes_required,
                        minlength: jQuery.format("{{ Lang::get('auth/form.register.validation_password_length_low') }}"),
                        maxlength: jQuery.format("{{ Lang::get('auth/form.register.validation_maxLength') }}")
                    },
                    "password_confirmation": {
                        required: mes_required,
                        equalTo: "{{ Lang::get('auth/form.register.validation_password_mismatch') }}"
                    }
                @endif
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
@stop
