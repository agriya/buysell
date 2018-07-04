@extends('base')
@section('content')
	<!-- BEGIN: ALERT INFO -->
    @if ( $errors->count() > 0 )
        <div class="alert alert-danger">{{trans('common.errors_occured')}}</div>
    @endif
    <!-- END: ALERT INFO -->

    @if(isset($success))
        <!-- BEGIN: ACTIVATION LINK -->
         <h1>{{trans('auth/form.register.signup_done')}}</h1>
		<div class="well">
            <div class="note note-success margin-0">
				<!--<button type="button" class="close" data-dismiss="alert">&times;</button>-->
				{{trans('auth/form.register.signup_sent_email_1')}} <strong>{{$email}}</strong> {{trans('auth/form.register.signup_sent_email_2')}}
				{{trans('auth/form.register.signup_sent_email_3')}}
		   </div>
        </div>
        <!-- END: ACTIVATION LINK -->
    @else
        <h1>{{trans('auth/form.register.legend')}}</h1>
		<div class="row">
			<!-- BEGIN: SIGNUP DETAILS -->
			<div class="col-md-9">
				{{ Form::open(array('url' => 'users/external-signup', 'class' => 'form-horizontal well',  'id' => 'external_signup', 'name' => 'external_signup')) }}
					{{ Form::token() }}
					{{ Form::hidden('social-email',  $attributes['email']) }}
					<p class="note note-info">Fields marked with  <span class="required-icon"></span>  are required</p>
					<fieldset>
						<div class="form-group {{{ $errors->has('user_name') ? 'error' : '' }}}">
							{{ Form::label('user_name', trans('auth/form.register.user_name'), array('class' => 'col-md-3 control-label required-icon')) }}
							<div class="col-md-5">
								{{  Form::text('user_name', '', array('class' => 'form-control')); }}
								<label for="user_name" class="error">{{{ $errors->first('user_name') }}}</label>
							</div>
						</div>

						<div class="form-group {{{ $errors->has('first_name') ? 'error' : '' }}}">
							{{ Form::label('first_name', trans('auth/form.register.first_name'), array('class' => 'col-md-3 control-label required-icon')) }}
							<div class="col-md-5">
								{{  Form::text('first_name', $attributes['first_name'], array('class' => 'form-control')); }}
								<label for="first_name" class="error">{{{ $errors->first('first_name') }}}</label>
							</div>
						</div>

						<div class="form-group {{{ $errors->has('last_name') ? 'error' : '' }}}">
							{{ Form::label('last_name', trans('auth/form.register.last_name'), array('class' => 'col-md-3 control-label required-icon')) }}
							<div class="col-md-5">
								@if(isset($attributes['last_name']))
									{{  Form::text('last_name', $attributes['last_name'], array('class' => 'form-control')); }}
								@else
									{{  Form::text('last_name', '', array('class' => 'form-control')); }}
								@endif
								<label for="last_name" class="error">{{{ $errors->first('last_name') }}}</label>
							</div>
						</div>

						<div class="form-group {{{ $errors->has('email') ? 'error' : '' }}}">
							{{ Form::label('email', trans('auth/form.register.email'), array('class' => 'col-md-3 control-label required-icon')) }}
							<div class="col-md-5">
								{{  Form::text('email', $attributes['email'], array('class' => 'form-control')); }}
								<label for="email" id="email_validation_err" class="error">{{{ $errors->first('email') }}}</label>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-offset-3 col-md-9">
								<button name="login" id="login" class="btn green"><i class="fa fa-check"></i> {{trans('auth/form.login.signup')}}</button>
							</div>
						</div>
					</fieldset>
				{{ Form::close() }}
			</div>
			<!-- END: SIGNUP DETAILS -->

			<!-- BEGIN: SIGNUP SIDEBAR -->
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
			</div>
			<!-- END: SIGNUP SIDEBAR -->
		</div>

        @if ($page_name = 'external_signup') @endif
        <script language="javascript" type="text/javascript">
            var page_name = "external_signup";
            var BASE = "{{ Request::root() }}";
            var user_input = "";
            var err_msg = "";
            var validation_phno = "{{trans('auth/form.register.validation_phno')}}";
            var screen_name_restrict_keywords_like = "{{Config::get('auth.screen_name_restrict_keywords_like')}}";
            var screen_name_restrict_keywords_exact = "{{Config::get('auth.screen_name_restrict_keywords_exact')}}";
            var restricted_keyword = "{{trans('auth/form.register.restricted_keyword')}}";
            var validation_maxLength = "{{trans('auth/form.register.validation_maxLength')}}";
            var validation_domain_length_low = "{{trans('auth/form.register.validation_domain_length_low')}}";
            var fieldlength_domain_min = "{{Config::get('auth.fieldlength_domain_min')}}";
            var fieldlength_domain_max = "{{Config::get('auth.fieldlength_domain_max')}}";
        </script>
    @endif
@stop