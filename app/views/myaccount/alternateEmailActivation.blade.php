@extends('layouts.base')
@section('content')
    @if (isset($error_msg) && $error_msg != "")
        <div class="alert alert-danger">{{ $error_msg }}</div>
    @elseif (isset($success_msg)  && $success_msg != "")
        <div class="alert alert-success">{{	$success_msg }}</div>
        @if(BasicCUtil::sentryCheck())
            <?php $profile_url = CUtil::getUserDetails(Sentry::getUser()->id, 'profile_url'); ?>
            <a itemprop="url" href="{{ URL::to('users/my-account') }}"><strong>{{trans("myaccount/form.email-activation.my_settings")}}</strong></a>
        @else
            <a itemprop="url" href="{{ URL::to('users/login') }}"><strong>{{trans('index.login')}}</strong></a>
        @endif
    @endif
@stop