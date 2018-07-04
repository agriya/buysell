@extends('base')
@section('content')
	@include('user_breadcrumb')
	@if(isset($success))
    	<!-- BEGIN: ACTIVATION LINK -->
		<h1>{{ Lang::get('auth/form.register.signup_done') }}</h1>
	   	<div class="well">
			<div id="success" class="note note-success margin-0">
				{{ Lang::get('auth/form.register.signup_sent_email_1') }} <strong>{{$email}}</strong> {{ Lang::get('auth/form.register.signup_sent_email_2') }}
				{{ Lang::get('auth/form.register.signup_sent_email_3') }}
			</div>
		</div>
        <!-- END: ACTIVATION LINK -->
	@else
		<!-- BEGIN: SIGNUP DETAILS -->
		<h1>{{ Lang::get('users.new_user_sigup') }}</h1>
		<div class="row captcha-code">
			<div class="col-md-9">
				{{ Form::open(array('method' => 'post', 'class' => 'form-horizontal form-without-legend well', 'role' => 'form', 'name' => 'signup_form', 'id' => 'signup_form')) }}
					<p class="note note-info"> {{trans('common.field_marked_required_text')}}</p>
					<div class="form-group {{{ $errors->has('email') ? 'error' : '' }}}">
						{{ Form::label('email', Lang::get('users.sign_up.email_address'),  array('class' => 'col-md-3 control-label required-icon', 'for' => 'email')) }}
						<div class="col-md-5">
							{{ Form::text('email', null, array('class' => 'form-control', 'id' => 'email')) }}
							<label class="error">{{{ $errors->first('email') }}}</label>
						</div>
					</div>

					<div class="form-group {{{ $errors->has('user_name') ? 'error' : '' }}}">
						{{ Form::label('user_name', Lang::get('users.sign_up.user_name'),  array('class' => 'col-md-3 control-label required-icon', 'for' => 'user_name')) }}
						<div class="col-md-5">
							{{ Form::text('user_name', null, array('class' => 'form-control', 'id' => 'user_name')) }}
							<label class="error">{{{ $errors->first('user_name') }}}</label>
						</div>
					</div>

					<div class="form-group {{{ $errors->has('first_name') ? 'error' : '' }}}">
						{{ Form::label('first_name', Lang::get('users.sign_up.first_name'), array('class' => 'col-md-3 control-label required-icon', 'for' => 'first_name')) }}
						<div class="col-md-5">
							{{ Form::text('first_name',null,array('class' => 'form-control', 'id' => 'first_name')) }}
							<label class="error">{{{ $errors->first('first_name') }}}</label>
						</div>
					</div>

					<div class="form-group {{{ $errors->has('last_name') ? 'error' : '' }}}">
						{{ Form::label('last_name', Lang::get('users.sign_up.last_name'), array('class' => 'col-md-3 control-label required-icon', 'for' => 'last_name')) }}
						<div class="col-md-5">
							{{ Form::text('last_name',null, array('class' => 'form-control', 'id' => 'last_name')) }}
							<label class="error">{{{ $errors->first('last_name') }}}</label>
						</div>
					</div>

					<div class="form-group {{{ $errors->has('password') ? 'error' : '' }}}">
						{{ Form::label('password', Lang::get('users.sign_up.password'), array('class' => 'col-md-3 control-label required-icon', 'for' => 'password')) }}
						<div class="col-md-5">
							{{ Form::password('password', array('class' => 'form-control', 'id' => 'password')) }}
							<label class="error">{{{ $errors->first('password') }}}</label>
						</div>
					</div>

					<div class="form-group {{{ $errors->has('password_confirmation') ? 'error' : '' }}}">
						{{ Form::label('password_confirmation', Lang::get('users.sign_up.confirm_password'), array('class' => 'col-md-3 control-label required-icon', 'for' => 'password_confirmation')) }}
						<div class="col-md-5">
							{{ Form::password('password_confirmation', array('class' => 'form-control', 'id' => 'password_confirmation')) }}
							<label class="error">{{{ $errors->first('password_confirmation') }}}</label>
						</div>
					</div>

					@if(Config::get('generalConfig.signup_captcha_display'))
						@if(Config::get('generalConfig.captcha_type') == 'Recaptcha')
							<div class="form-group {{{ $errors->has('captcha') ? 'error' : '' }}}">
								{{ Form::label('captcha', Lang::get('auth/form.register.security_code'), array('class' => 'col-md-3 col-xs-12 pull-left control-label required-icon')) }}
                                <div class="col-md-8 captcha-bk">
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
								{{ Form::label('adcopy_response', Lang::get('auth/form.register.security_code'), array('class' => 'col-md-3 control-label required-icon')) }}
								<div class="col-md-5">
									{{ solvemedia_get_html(Config::get('generalConfig.challenge_key')) }}
									<label class="error">{{{ $errors->first('adcopy_response') }}}</label>
								</div>
							</div>
						@endif
					@endif
					<div class="form-group {{{ $errors->has('terms_conditions') ? 'error' : '' }}}">
						<div class="col-md-8 col-md-offset-3">
							<label class="checkbox-inline">
								{{ Form::checkbox('terms_conditions', 'yes', true, array('id' =>"terms_conditions", 'class' => "margin-top-3")) }}
								<?php
                                    if(CUtil::chkIsStaticPageAvailable('terms'))
                                        $term1 = "<a href=".URL::to('static/terms').">".Lang::get('users.sign_up.terms_conditions_msg2')."</a>";
                                    else
                                        $term1 = Lang::get('users.sign_up.terms_conditions_msg2');

									$terms_conditions2 = str_replace(array('VAR_SITENAME','VAR_TERMS_LINK'), array(Config::get('generalConfig.site_name'),$term1), trans('users.sign_up.terms_conditions_msg1'));
								?>
								<label for="terms_conditions" class="fonts12">{{ $terms_conditions2 }}</label>
							</label>
							<label for="terms_conditions" generated="true" class="error">{{{ $errors->first('terms_conditions') }}}</label>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-offset-3 col-md-9">
							<button type="submit" class="btn green" value="submit"><i class="fa fa-check"></i> {{ Lang::get('users.sign_up.submit') }}</button>
							<a href="{{ URL::to('users/login') }}" class="btn default" ><i class="fa fa-chevron-left"></i> {{ Lang::get('users.back_view') }}</a>
						</div>
					</div>
				{{ Form::close() }}
			</div>

			<div class="col-md-3">
				<div class="signup-sidebar">
					<h3 class="title-one"> {{Lang::get('home.why_site', array('site_name' => Config::get('generalConfig.site_name'))) }}</h3>
					<p>	{{Lang::get('home.great_place_to_buy')}}</p>
				</div>
				<div class="signup-sidebar">
					<h3 class="title-one"> {{Lang::get('home.whats_in_site', array('site_name' => Config::get('generalConfig.site_name'))) }}</h3>
					<p>	{{Lang::get('home.browse_right_kind_of_gifts')}}</p>
					<p>	{{Lang::get('home.become_among_community_of_artists')}}</p>
					<p>	{{Lang::get('home.creat_shop_of_your_own')}}</p>
				</div>

				<!-- BEGIN: SIDE BANNER GOOGLE ADDS -->
				{{ getAdvertisement('side-banner') }}
				<!-- END: SIDE BANNER GOOGLE ADDS -->
			</div>
		</div>
		<!-- END: SIGNUP DETAILS -->
	@endif
@stop
@section('script_content')
	<script language="javascript" type="text/javascript">
        var BASE = "{{ Request::root() }}";
        var minlength_errmsg = '{{Lang::get('common.minlength_validation')}}';
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
        var valid_email = "{{ Lang::get('common.enter_valid_email') }}";
        $("#signup_form").validate({
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
                email: {
                    required: true,
                    email: true
                },
                user_name: {
		            required: true,
		            minlength: "{{ Config::get('webshopauthenticate.fieldlength_password_min') }}",
		            maxlength: "{{ Config::get('webshopauthenticate.fieldlength_password_max') }}",
		            chkIsNameHasRestrictedWordsLike: true,
                    chkIsNameHasRestrictedWordsExact: true,
		            alphanumeric : true
                },
                password: {
                    required: true,
                    minlength: "{{ Config::get('webshopauthenticate.fieldlength_password_min') }}",
                    maxlength: "{{ Config::get('webshopauthenticate.fieldlength_password_max') }}"
                },
                terms_conditions: {
					required: true
				},
                password_confirmation: {
                    required: true,
                    equalTo: "#password"
                }
                @if(Config::get('generalConfig.signup_captcha_display'))
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
                    minlength: jQuery.format("{{ Lang::get('auth/form.register.validation_password_length_low') }}"),
                    maxlength: jQuery.format("{{ Lang::get('auth/form.register.validation_maxLength') }}")
                },
                "password_confirmation": {
                    required: mes_required,
                    equalTo: "{{ Lang::get('auth/form.register.validation_password_mismatch') }}"
                }
                @if(Config::get('generalConfig.signup_captcha_display'))
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
    </script>
@stop