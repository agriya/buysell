@extends('popup')
@section('includescripts')
	<script type="text/javascript">
		@if (BasicCUtil::sentryCheck())
			@if (Session::has('return_thread'))
	            parent.location.href = {{ URL::to('/') }}+'?thread='+Session::has('return_thread');
			@else
				parent.location.reload();
			@endif
		@endif
	</script>
@stop
@section('content')
    <h1>{{trans('auth/form.register.legend')}}</h1>
    <ul class="nav nav-tabs margin-bottom-10">
        <?php  $signup_cls="active"; $signin_cls="";?>
        @if(isset($page) && $page=='login')
            <?php  $signup_cls=""; $signin_cls="active";?>
        @endif
        <li class="{{$signup_cls}}"><a href="{{URL::to('users/login?form_type=selLogin&page=signup')}}">{{ Lang::get('users.signup_view') }}</a></li>
        <li class="{{$signin_cls}}"><a href="{{URL::to('users/login?form_type=selLogin&page=login')}}">{{ Lang::get('common.sign_in') }}</a></li>
    </ul>
    <div class="tab-content">
    	@if($page == 'signup')
        <div id="signup_block" class="tab-pane {{$signup_cls}}">
            @if(isset($success))
                <h2 class="title-six">{{trans('auth/form.register.signup_done')}}</h2>
                <div id="success" class="alert alert-success">
                    {{trans('auth/form.register.signup_sent_email_1')}} <strong>{{$email}}</strong> {{trans('auth/form.register.signup_sent_email_2')}}
                    {{trans('auth/form.register.signup_sent_email_3')}}
                </div>
            @else
                {{ Form::open(array('url' => 'users/signup-pop/selSignup', 'class' => 'form-horizontal',  'id' => 'signup', 'name' => 'signup' )) }}
                {{ Form::token() }}
                    <fieldset>
                        <div class="form-group {{{ $errors->has('email') ? 'error' : '' }}}">
                            {{ Form::label('email', trans("auth/form.register.email"), array('class' => 'col-sm-3 control-label required-icon')) }}
                            <div class="col-sm-5">
                                {{  Form::text('email', null, array('class' => 'form-control')); }}
                                <label class="error">{{{ $errors->first('email') }}}</label>
                            </div>
                        </div>
						<div class="form-group {{{ $errors->has('user_name') ? 'error' : '' }}}">
                            {{ Form::label('user_name', trans("users.sign_up.user_name"), array('class' => 'col-sm-3 control-label required-icon')) }}
                            <div class="col-sm-5">
                                {{  Form::text('user_name', null, array('class' => 'form-control')); }}
                                <label class="error">{{{ $errors->first('user_name') }}}</label>
                            </div>
                        </div>
                        <div class="form-group {{{ $errors->has('first_name') ? 'error' : '' }}}">
                            {{ Form::label('first_name', trans("auth/form.register.first_name"), array('class' => 'col-sm-3 control-label required-icon')) }}
                            <div class="col-sm-5">
                                {{  Form::text('first_name', null, array('class' => 'form-control')); }}
                                <label class="error">{{{ $errors->first('first_name') }}}</label>
                            </div>
                        </div>

                        <div class="form-group {{{ $errors->has('last_name') ? 'error' : '' }}}">
                            {{ Form::label('last_name', trans("auth/form.register.last_name"), array('class' => 'col-sm-3 control-label required-icon')) }}
                            <div class="col-sm-5">
                                {{  Form::text('last_name', null, array('class' => 'form-control')); }}
                                <label class="error">{{{ $errors->first('last_name') }}}</label>
                            </div>
                        </div>

                        <div class="form-group {{{ $errors->has('password') ? 'error' : '' }}}">
                            {{ Form::label('password',  trans("auth/form.register.password"), array('class' => 'col-sm-3 control-label required-icon')) }}
                            <div class="col-sm-5">
                                {{  Form::password('password', array('class' => 'form-control')); }}
                                <label class="error">{{{ $errors->first('password') }}}</label>
                            </div>
                        </div>

                        <div class="form-group {{{ $errors->has('password_confirmation') ? 'error' : '' }}}">
                            {{ Form::label('password_confirmation', trans("auth/form.register.confirm_password"), array('class' => 'col-sm-3 control-label required-icon')) }}
                            <div class="col-sm-5">
                                {{  Form::password('password_confirmation', array('class' => 'form-control')); }}
                                <label class="error">{{{ $errors->first('password_confirmation') }}}</label>
                            </div>
                        </div>

                        @if(Config::get('generalConfig.signup_captcha_display'))
							@if(Config::get('generalConfig.captcha_type') == 'Recaptcha')
								<div class="form-group {{{ $errors->has('captcha') ? 'error' : '' }}}">
									{{ Form::label('captcha', Lang::get('auth/form.register.security_code'), array('class' => 'col-sm-3 col-xs-12 pull-left control-label required-icon')) }}
	                                <div class="col-sm-5 captcha-bk">
	                                    <div class="clearfix">
	                                        {{ Form::text('captcha', null, array('class' => 'form-control')) }}
	                                        {{HTML::image(Captcha::img(), Lang::get('auth/form.register.captcha_image'), array('id' => 'src_captcha')) }}
	                                        <a href="javascript:void(0)" id="reload_captcha"><span class="fa fa-refresh"></span></a>
	                                    </div>
	                                    <label for="captcha" generated="true" class="error">{{{ $errors->first('captcha') }}}</label>
	                                </div>
								</div>
							@else
								<div class="form-group {{{ $errors->has('adcopy_response') ? 'error' : '' }}}">
									{{ Form::label('adcopy_response', Lang::get('auth/form.register.security_code'), array('class' => 'col-sm-3 control-label required-icon')) }}
									<div class="col-sm-5 captcha-code">
										{{ solvemedia_get_html(Config::get('generalConfig.challenge_key')) }}
										<label class="error">{{{ $errors->first('adcopy_response') }}}</label>
									</div>
								</div>
							@endif
						@endif

                        <div class="form-group {{{ $errors->has('terms_conditions') ? 'error' : '' }}}">
							<div class="col-sm-7 col-sm-offset-3">
								<div class="checkbox-inline">
									{{ Form::checkbox('terms_conditions', 'yes', true, array('id' =>"terms_conditions", 'class' => "margin-top-3")) }}
									<?php
                                        if(CUtil::chkIsStaticPageAvailable('terms'))
                                            $term1 = "<a href=".URL::to('static/terms').">".Lang::get('users.sign_up.terms_conditions_msg2')."</a>";
                                        else
                                            $term1 = Lang::get('users.sign_up.terms_conditions_msg2');
										$terms_conditions2 = str_replace(array('VAR_SITENAME','VAR_TERMS_LINK'), array(Config::get('generalConfig.site_name'),$term1), trans('users.sign_up.terms_conditions_msg1'));
									?>
									<label for="terms_conditions" class="fonts12">{{ $terms_conditions2 }}</label>
								</div>
								<label class="error" for="terms_conditions" generated="true" >{{{ $errors->first('terms_conditions') }}}</label>
							</div>
						</div>

                        <div class="form-group label-none">
                            <div class="col-sm-7 col-sm-offset-3">
                                <button name="signup" id="signup" type="submit" class="btn btn-success"><i class="fa fa-check"></i> {{ Lang::get('users.signup_view') }}</button>
                            </div>
                        </div>
                    </fieldset>
                {{ Form::close() }}
            @endif
        </div>
        @endif
        @if($page == 'login')
        <div id="login_block" class="tab-pane {{$signin_cls}}">
            <div id="activation_resend_msg" style="display: none;"></div>
            @if (isset($error))
                @if($error == 'ToActivate')
                    <div id="selErrorMsg" class="alert alert-danger">
                        {{trans("auth/form.login.activation_required")}}
                    </div>
                @elseif($error == 'Locked' OR $error == 'Deleted')
                    <div id="selErrorMsg" class="alert alert-danger">
                        {{trans("auth/form.login.login_error")}} {{$error}}
                    </div>
                @elseif($error == 'Invalid')
                    <div id="selErrorMsg" class="alert alert-danger">
                        {{trans("auth/form.login.invalid_login")}}
                    </div>
                @else
                    <div class="alert alert-danger">{{ $error }}</div>
                @endif
            @endif
            @if (isset($reference_msg) && $reference_msg !='')
                <div class="alert alert-info">{{ $reference_msg }}</div>
            @endif

            @if (isset($flash_notice) && $flash_notice !='')
            	<div id="success" class="alert alert-success">{{ $flash_notice }}</div>
            @else
	            {{ Form::open(array('url' => 'users/signup-pop/selLogin', 'class' => 'form-horizontal',  'id' => 'login_frm', 'name' => 'login_frm')) }}
	            @if (isset($act) && $act != '')
	                {{ Form::hidden('act', $act) }}
	            @endif
	            {{ Form::hidden('form_open_type', 'popup') }}
	                <fieldset>
	                    <div class="form-group {{{ $errors->has('login_email') ? 'error' : '' }}}">
	                        {{ Form::label('login_email', trans('auth/form.login.email'), array('class' => 'col-sm-3 control-label required-icon')) }}
	                        <div class="col-sm-5">
	                            {{  Form::text('login_email', null, array('class' => 'form-control', 'id'=>'login_email')); }}
	                            <label class="error">{{{ $errors->first('login_email') }}}</label>
	                        </div>
	                    </div>

	                    <div class="form-group {{{ $errors->has('login_password') ? 'error' : '' }}}">
	                        {{ Form::label('login_password', trans('auth/form.login.password'), array('class' => 'col-sm-3 control-label required-icon')) }}
	                        <div class="col-sm-5">
	                            {{  Form::password('login_password', array('class' => 'form-control', 'id'=>'login_password')); }}
	                            <label class="error">{{{ $errors->first('login_password') }}}</label>
	                        </div>
	                    </div>

	                     @if(Config::get('generalConfig.login_captcha_display'))
							@if(Config::get('generalConfig.captcha_type') == 'Recaptcha')
								<div class="form-group {{{ $errors->has('captcha') ? 'error' : '' }}}">
									{{ Form::label('captcha', Lang::get('auth/form.register.security_code'), array('class' => 'col-sm-3 col-xs-12 pull-left control-label required-icon')) }}
	                                <div class="col-sm-5 captcha-bk">
	                                    <div class="clearfix">
	                                        {{ Form::text('captcha', null, array('class' => 'form-control')) }}
	                                        {{HTML::image(Captcha::img(), Lang::get('auth/form.register.captcha_image'), array('id' => 'src_captcha')) }}
	                                        <a href="javascript:void(0)" id="reload_captcha"><span class="fa fa-refresh"></span></a>
	                                    </div>
	                                    <label for="captcha" generated="true" class="error">{{{ $errors->first('captcha') }}}</label>
	                                </div>
								</div>
							@else
								<div class="form-group {{{ $errors->has('adcopy_response') ? 'error' : '' }}}">
									{{ Form::label('adcopy_response', Lang::get('auth/form.register.security_code'), array('class' => 'col-sm-3 control-label required-icon')) }}
									<div class="col-sm-5 captcha-code">
										{{ solvemedia_get_html(Config::get('generalConfig.challenge_key')) }}
										<label class="error">{{{ $errors->first('adcopy_response') }}}</label>
									</div>
								</div>
							@endif
						@endif

	                    <div class="form-group label-none">
	                        <div class="col-sm-8 col-sm-offset-3">
	                            <label class="checkbox-inline">
	                                {{ Form::checkbox('remember',1,true, array('class' => 'margin-top-3', 'id'=>'remember')); }}
	                                {{ Form::label('remember', trans('auth/form.login.remember_me'), array('for' => 'remember')) }}&nbsp;
	                                <a href="{{ url('/users/signup-pop/selForgotPassword') }}" class="fn_signuppop btn-link" itemprop="url" >
	                                    {{trans('auth/form.login.forget_password')}}
	                                </a>
	                            </label>
	                        </div>
	                    </div>

	                    <div class="form-group label-none">
	                        <div class="col-sm-8 col-sm-offset-3">
	                            <button type="submit" value="" class="btn btn-success"><i class="fa fa-sign-in"></i> {{trans('auth/form.login.login')}}</button>
	                        </div>
	                    </div>
	                </fieldset>
	            {{ Form::close() }}
            @endif
        </div>
        @endif
	</div>

    <script language="javascript" type="text/javascript">
        var mes_required = "{{trans('auth/form.required')}}";
        $("#login_frm").validate({
            onfocusout: injectTrim($.validator.defaults.onfocusout),
            rules: {
                login_email: {
                    required: true,
                },
                login_password: {
                    required: true
                }
            },
            messages: {
                login_email: {
                    required: mes_required
                },
                login_password: {
                    required: mes_required
                }
            }
        });

        function resendActivationCode() {
            $('#activation_resend_msg').show();
            var email = $('#email').val();
        //	displayLoadingImage(true);
            $.post("{{ url('/users/resend-activation-code') }}", {"email": email} , function(data){
                if(data == 'success') {
                    if($('#selErrorMsg').length > 0)
                        $('#selErrorMsg').hide();
                    $('#activation_resend_msg').html("{{trans('auth/form.login.activation_code_send')}}");
                    $("#activation_resend_msg").addClass('alert alert-success');
                }
                else {
                    $('#activation_resend_msg').html(data);
                    $("#activation_resend_msg").addClass('alert alert-danger');
                }
            //	hideLoadingImage();
            })
        }

        jQuery.validator.addMethod(
          "validatephone",
          function(value, element) {
            if(value != "") {
                var regex = /^\+?[0-9-(\)]+?$/;
                if(value.match(regex)) {
                    return true;
                }
                return false;
            }
            return true;
          },
         "{{trans('auth/form.register.validation_phno')}}"
        );

        var err_msg = '';
    var messageFunc = function() { return err_msg; };
    jQuery.validator.addMethod(
          "chkIsNameHasRestrictedWordsLike",
          function(value, element) {
            if(value != "") {
                var filterWords = new Array();
                var restricted_keywords = "{{Config::get('webshopauthenticate.screen_name_restrict_keywords_like')}}";
                filterWords = restricted_keywords.split(",");
                for(i = 0; i < filterWords.length; i++) {
                    // "i" is to ignore case
                    var regex = new RegExp(filterWords[i], "gi");
                    if(value.match(regex)) {
                        err_msg = "{{trans('auth/form.register.restricted_keyword')}}";
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
                var restricted_keywords = "{{Config::get('webshopauthenticate.screen_name_restrict_keywords_exact')}}";
                filterWords = restricted_keywords.split(",");
                for(i = 0; i < filterWords.length; i++) {
                    // "i" is to ignore case
                    var regex = new RegExp('\\b' + filterWords[i] + '\\b' , "gi");
                    if(value.match(regex)) {
                        err_msg = "{{trans('auth/form.register.restricted_keyword')}}";
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
        "chkspecialchars",
        function(value, element) {
            if(value!=""){
                if (/^[a-zA-Z0-9'/,&() -]*$/.test(value))
                    return true;
                return false;
            }
            return true;
        },
        "{{trans('auth/form.register.specialchars_not_allowed')}}"
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
    var mes_required = "{{trans('auth/form.required')}}";
    var valid_email = "{{trans('common.enter_valid_email')}}";
    $("#signup").validate({
        onfocusout: injectTrim($.validator.defaults.onfocusout),
        rules: {
            first_name: {
                required: true,
                minlength: "{{Config::get('webshopauthenticate.fieldlength_name_min_length')}}",
                maxlength: "{{Config::get('webshopauthenticate.fieldlength_name_max_length')}}",
                chkIsNameHasRestrictedWordsLike: true,
                chkIsNameHasRestrictedWordsExact: true,
                chkspecialchars: true
            },
            last_name: {
                required: true,
                minlength: "{{Config::get('webshopauthenticate.fieldlength_name_min_length')}}",
                maxlength: "{{Config::get('webshopauthenticate.fieldlength_name_max_length')}}",
                chkIsNameHasRestrictedWordsLike: true,
                chkIsNameHasRestrictedWordsExact: true,
                chkspecialchars: true
            },
            email: {
                required: true,
			    email: true
            },
            terms_conditions: {
				required: true
			},
            user_name: {
		            required: true,
		            minlength: "{{ Config::get('webshopauthenticate.fieldlength_password_min') }}",
		            maxlength: "{{ Config::get('webshopauthenticate.fieldlength_password_max') }}",
		            chkIsNameHasRestrictedWordsLike: true,
                    chkIsNameHasRestrictedWordsExact: true,
		            alphanumeric : true
            },
            "password": {
                required: true,
                minlength: "{{Config::get('webshopauthenticate.fieldlength_password_min')}}",
                maxlength: "{{Config::get('webshopauthenticate.fieldlength_password_max')}}"
            },
            "password_confirmation": {
                required: true,
                equalTo: "#password"
            }
        },
        messages: {
            first_name: {
                required: mes_required,
                minlength: jQuery.format("{{ Lang::get('common.minlength_validation') }}"),
                maxlength: jQuery.format("{{ Lang::get('common.maxlength_validation') }}"),
            },
            last_name: {
                required: mes_required,
                minlength: jQuery.format("{{ Lang::get('common.minlength_validation') }}"),
                maxlength: jQuery.format("{{ Lang::get('common.maxlength_validation') }}"),
            },
            user_name: {
                required: mes_required,
                minlength: jQuery.format("{{ Lang::get('common.minlength_validation') }}"),
                maxlength: jQuery.format("{{ Lang::get('common.maxlength_validation') }}"),
            },
            email: {
                required: mes_required,
                email: valid_email,
            },
            terms_conditions: {
				required: mes_required,
			},
            password: {
                required: mes_required,
                minlength: jQuery.format("{{trans('auth/form.register.validation_password_length_low')}}"),
                maxlength: jQuery.format("{{trans('auth/form.register.validation_maxLength')}}")
            },
            "password_confirmation": {
                required: mes_required,
                equalTo: "{{trans('auth/form.register.validation_password_mismatch')}}"
            }
        },
        submitHandler: function(form) {
                form.submit();
        }
    });

    @if(Config::get('generalConfig.signup_captcha_display') && Config::get('generalConfig.captcha_type') == 'Recaptcha')
		$('#reload_captcha').bind('click', function() {
      		$('#src_captcha').attr('src', "{{Captcha::img()}}?r="+ Math.random())
    	});
	@endif

	@if(Config::get('generalConfig.login_captcha_display') && Config::get('generalConfig.captcha_type') == 'Recaptcha')
		$('#reload_captcha').bind('click', function() {
			$('#src_captcha').attr('src', "{{Captcha::img()}}?r="+ Math.random())
		});
	@endif
    </script>
@stop