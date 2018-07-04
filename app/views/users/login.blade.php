@extends('base')
@section('content')
	<div class="content-form-page login-container captcha-code">
        <!-- BEGIN: PAGE TITLE -->
		<div class="clearfix">
            <h1>
            	<?php $logo_image = CUtil::getSiteLogo();
					$image_url = isset($logo_image['image_url'])?$logo_image['image_url']:URL::asset('/images/header/logo/logo.png');
				 ?>
                <a href="{{ URL::to('/') }}" title="{{ Config::get('generalConfig.site_name') }}">
                    <img class="logo-default" src="{{ $image_url }}" alt="{{ Config::get('generalConfig.site_name') }}" />
                </a>
            </h1>
        </div>
		<!-- END: PAGE TITLE -->

        @include('notifications')

        <!-- BEGIN: ALERT MESSAGE -->
        @if(Session::has('success_message'))
            <div class="alert alert-success">
                <p><strong>{{Lang::get('auth/form.reset-password.password_reset_success')}}</strong> {{Lang::get('auth/form.reset-password.password_reset_success_msg')}}</p>
            </div>
        @endif
        @if(Session::has('change_password_error'))
            <div class="alert alert-danger">
                <strong>{{Lang::get('auth/form.reset_password.password_reset_failure')}}</strong>
            </div>
        @endif
        @if(Session::has('success_msg') && Session::get('success_msg') != '')
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>{{trans('auth/form.register.signup_done')}}</strong>
                @if(Session::has('success_msg'))
                   {{trans('auth/form.register.create_account_success')}}
                    <p>{{trans('auth/form.register.signup_sent_email_3')}}</p>
                @endif
            </div>
         <!-- END: ALERT MESSAGE -->
        @else
            <!-- END: LOGIN DETAILS -->
            {{ Form::open(array('action' => array('AuthController@postLogin'), 'method' => 'post', 'class' => 'form-horizontal form-without-legend login-form', 'role' => 'form', 'name' => 'login_form', 'id' => 'login_form')) }}
                <div class="form-group {{{ $errors->has('email') ? 'error' : '' }}}">
                    {{ Form::label('email', Lang::get('users.credential.email_address'),  array('class' => 'col-md-4 col-sm-4 col-xs-4 control-label required-icon', 'for' => 'email')) }}
                    <div class="col-md-8 col-sm-8 col-xs-7">
                        {{ Form::text('email', null, array('class' => 'form-control', 'id' => 'email')) }}
                        <label class="error">{{{ $errors->first('email') }}}</label>
                    </div>
                </div>
                <div class="form-group {{{ $errors->has('password') ? 'error' : '' }}}">
                    {{ Form::label('password', Lang::get('users.credential.password'), array('class' => 'col-md-4 col-sm-4 col-xs-4 control-label required-icon', 'for' => 'password')) }}
                    <div class="col-md-8 col-sm-8 col-xs-7">
                        {{ Form::password('password', array('class' => 'form-control', 'id' => 'password')) }}
                        <label class="error">{{{ $errors->first('password') }}}</label>
                    </div>
                </div>

                @if(Config::get('generalConfig.login_captcha_display'))
					@if(Config::get('generalConfig.captcha_type') == 'Recaptcha')
						<div class="form-group margin-bottom-5 {{{ $errors->has('captcha') ? 'error' : '' }}}">
							{{ Form::label('captcha', Lang::get('auth/form.register.security_code'), array('class' => 'col-md-4 col-sm-4 col-xs-4 control-label required-icon')) }}
							<div class="col-md-8 col-sm-8 col-xs-7 captcha-bk">
                            	<div class="clearfix">
                                    {{ Form::text('captcha', null, array('class' => 'form-control')) }}
                                    {{HTML::image(Captcha::img(), Lang::get('auth/form.register.captcha_image'), array('id' => 'src_captcha')) }}
                                    <a href="javascript:void(0)" id="reload_captcha"><span class="fa fa-refresh"></span></a>
                                </div>
                                <label for="captcha" generated="true" class="error">{{{ $errors->first('captcha') }}}</label>
							</div>
						</div>
					@else
						<div class="form-group margin-bottom-5 {{{ $errors->has('adcopy_response') ? 'error' : '' }}}">
							{{ Form::label('adcopy_response', Lang::get('auth/form.register.security_code'), array('class' => 'col-md-4 col-sm-4 col-xs-4 control-label required-icon')) }}
							<div class="col-md-8 col-sm-8 col-xs-7">
								{{ solvemedia_get_html(Config::get('generalConfig.challenge_key')) }}
								<label class="error">{{{ $errors->first('adcopy_response') }}}</label>
							</div>
						</div>
					@endif
				@endif

                <div class="form-group form-chkbox">
                    <div class="col-md-offset-4 col-sm-offset-4 col-xs-offset-4 col-md-8 col-sm-8 col-xs-7 text-left">
                        <label class="checkbox">
                            {{ Form::checkbox('remember') }}
                            {{trans('auth/form.login.remember_me')}}
                        </label>
                    </div>
                </div>
                <div class="row login-buttons">
                    <button name="login" id="login" data-complete-text="Login" data-loading-text='Loading' class="btn green">{{ Lang::get('users.credential.submit') }}</button>
                    <a href="{{ URL::to('users/signup') }}" class="btn default">{{ Lang::get('users.signup_view') }}</a>
                    <a href="{{ URL::to('users/forgotpassword') }}" class="btn default">{{ Lang::get('users.forgot_password') }}</a>
					<div class="login-fbtwitter">
						@if(Config::get('login.enable_facebook') || Config::get('login.enable_twitter'))
							<p>{{trans('common.or')}}</p>
							<ul class="list-inline margin-0">
								<li class="">
									<small class="text-muted">{{trans('common.sign_in_with')}}</small>
									@if(Config::get('login.enable_facebook'))
										<a href="javascript:void(0);" onClick="gotoSocialInPage('{{ URL::action('OAuthController@getAuthorize', 'facebook') }}', 'facebook');" title="{{ Lang::get('auth/form.signin_with_facebook') }}" class="btn btn-xs bg-blue-steel"><i class="fa fa-facebook"></i></a>
									@endif
									@if(Config::get('login.enable_twitter'))
										<a href="javascript:void(0);" onClick="gotoSocialInPage('{{ URL::action('OAuthController@getAuthorize', 'twitter') }}', 'facebook');" title="{{ Lang::get('auth/form.signin_with_twitter') }}" class="btn btn-xs bg-blue"><i class="fa fa-twitter"></i></a>
									@endif
								</li>
							</ul>
						@endif
					</div>
                </div>
            {{ Form::close() }}
            <!-- END: LOGIN DETAILS -->
        @endif
    </div>
@stop

@section('script_content')
	<script language="javascript" type="text/javascript">
        var mes_required = "{{ Lang::get('auth/form.required') }}";

        $("#login_form").validate({
    		onfocusout: injectTrim($.validator.defaults.onfocusout),
            rules: {
                email: {
                    required: true
                },
                password: {
                    required: true
                }
                @if(Config::get('generalConfig.login_captcha_display'))
					,
					@if(Config::get('generalConfig.captcha_type') == 'Recaptcha')
		                captcha: {
			                required: true
			            }
			        @else
			        	adcopy_response: {
			                required: true
			            }
		            @endif
		        @endif
            },
            messages: {
                email: {
                    required: mes_required
                },
                password: {
                    required: mes_required
                }
                @if(Config::get('generalConfig.login_captcha_display'))
	                ,
	                @if(Config::get('generalConfig.captcha_type') == 'Recaptcha')
		                captcha: {
			                required: mes_required
			            }
			        @else
			        	adcopy_response: {
			                required: mes_required
			            }
		            @endif
		        @endif
            }
        });

		function resendActivationCode() {
			$('#activation_resend_msg').show();
            var email = $('#email').val();
       		displayLoadingImage(true);
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
            	hideLoadingImage();
			})
        }

        @if(Config::get('generalConfig.login_captcha_display') && Config::get('generalConfig.captcha_type') == 'Recaptcha')
			$('#reload_captcha').bind('click', function() {
	      		$('#src_captcha').attr('src', "{{Captcha::img()}}?r="+ Math.random())
	    	});
		@endif
    </script>
@stop