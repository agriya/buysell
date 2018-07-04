@extends('admin')
@section('content')
	<!-- BEGIN: NOTIFICATIONS -->
    @include('notifications')
    <!-- END: NOTIFICATIONS -->

    <!--- BEGIN: ERROR INFO --->
	@if(Session::has('error_message') && Session::get('error_message') != '')
        <div class="note note-danger">{{ Session::get('error_message') }}</div>
        <?php Session::forget('error_message'); ?>
    @endif
    <!--- END: ERROR INFO --->

    <!--- BEGIN: SUCCESS INFO --->
    @if(Session::has('success_message') && Session::get('success_message') != '')
        <div class="note note-success">{{ Session::get('success_message') }}</div>
        <?php Session::forget('success_message'); ?>
    @endif
    <!--- END: SUCCESS INFO --->

	<!-- BEGIN: PAGE TITLE -->
    <h1 class="page-title">{{ Lang::get('walletAccount.site_wallet') }}</h1>
    <!-- END: PAGE TITLE -->

	<!-- BEGIN: CURRENCY BALANCE DETAILS -->
	<div class="note note-info">
		<h4 class="no-margin">{{ Lang::get('walletAccount.account_balance') }}:
			@if(count($account_balance_arr) > 0)
		    	@foreach($account_balance_arr as $other_bal)
		        	{{ CUtil::convertAmountToCurrency($other_bal['amount'], Config::get('generalConfig.site_default_currency'), '', true) }}
		        @endforeach
		    @else
		    	{{ CUtil::convertAmountToCurrency(0, Config::get('generalConfig.site_default_currency'), '', true) }}
		    @endif
		</h4>
	</div>
	<!-- END: CURRENCY BALANCE DETAILS -->
@stop