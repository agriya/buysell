@extends('popup')
@section('content')
	<h1>{{trans("auth/form.forget_password.forgot_password")}}</h1>
    <div class="pop-content">
		@if (Session::has('error'))
			<div class="note note-danger">{{ trans(Session::get('reason')) }}</div>
		@elseif (Session::has('success'))
			<div class="note note-success">{{trans("auth/form.forget_password.password_mail_sent")}}</div>
		@else
			<div id="selHideInfo" class="note note-info">{{trans("auth/form.forget_password.reset_your_password")}}</div>
		@endif
        <!-- Forgot Password Details Starts -->
            {{ Form::open(array('url' => 'users/signup-pop/selForgotPassword', 'class' => 'form-horizontal form-without-legend',  'id' => 'forgotpassword_frm')) }}
                <div class="form-group {{{ $errors->has('email') ? 'error' : '' }}}">
                    {{ Form::label('email', trans('auth/form.forget_password.enter_email_id'), array('class' => 'col-sm-3 control-label required-icon')) }}
                    <div class="col-sm-5">
                        {{  Form::text('email', null, array('class' => 'form-control')); }}
                        <label class="error">{{{ $errors->first('email') }}}</label>
                    </div>
                </div>

                <div class="form-group label-none">
                    <label class="col-sm-3 control-label">&nbsp;</label>
                    <div class="col-sm-9">
                        <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> {{trans('common.submit')}}</button>
                        <a href="javascript://" itemprop="url" onclick="fancyPopupUrlRedirect('{{ url('users/signup-pop/selLogin') }}')">
                            <button type="reset" class="btn default"><i class="fa fa-times"></i> {{trans('common.cancel')}}</button>
                        </a>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
        <!-- Forgot Password Details Ends -->
	</div>
	<script language="javascript" type="text/javascript">
		var mes_required = "{{trans('auth/form.required')}}";
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
					required: mes_required
				}
			}
		});
	</script>
@stop
