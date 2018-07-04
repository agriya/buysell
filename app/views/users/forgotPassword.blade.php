@extends('base')
@section('content')
	@include('user_breadcrumb')
	@include('notifications')
    <!-- BEGIN: FORGOT PASSWORD DETAILS -->
    <h1>{{ Lang::get('auth/form.forget_password.forgot_password') }}</h1>
	{{ Form::open(array('action' => array('AuthController@postForgotpassword'), 'class' => 'form-horizontal form-without-legend well',  'id' => 'forgotpassword_frm')) }}
		<div class="note note-info">
		{{ Lang::get('auth/form.forget_password.forgot_password_instruction') }}
		</div>
		<div class="form-group {{{ $errors->has('email') ? 'error' : '' }}}">
			{{ Form::label('email', Lang::get('users.forgot_credential.email_address'), array('class' => 'col-md-3 control-label required-icon')) }}
			<div class="col-md-5">
				{{  Form::text('email', null, array ('class' => 'form-control')); }}
				<label class="error">{{{ $errors->first('email') }}}</label>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-offset-3 col-md-9">
				<button name="login" id="login" data-complete-text="Login" data-loading-text='Loading' class="btn green">
				<i class="fa fa-check"></i> {{ Lang::get('users.forgot_credential.submit') }}</button>
				<a href="{{ URL::to('users/login') }}" class="btn default" ><i class="fa fa-chevron-left"></i> {{ Lang::get('users.forgot_credential.back') }}</a>
			</div>
		</div>
	{{ Form::close() }}
    <!-- END: FORGOT PASSWORD DETAILS -->
@stop

@section('script_content')
	<script language="javascript" type="text/javascript">
        var mes_required = "{{ Lang::get('auth/form.required') }}";
        var valid_email = '{{ Lang::get('common.enter_valid_email') }}';
	    $("#forgotpassword_frm").validate({
	        onfocusout: injectTrim($.validator.defaults.onfocusout),
            rules: {
	            email: {
	                required: true,
				    email: true
	            }
	        },
	        messages: {
	            email: {
	                required: mes_required,
	                email: valid_email
	            }
	        }
	    });
	</script>
@stop