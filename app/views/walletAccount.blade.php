<?php $account_balance_arr = CUtil::getWalletAccountDetails(); ?>
<div class="acc-bal alert alert-info">
	<h2 class="no-margin">{{ trans('walletAccount.account_balance') }}
    	@foreach($account_balance_arr as $other_bal)
        	<strong>{{ $other_bal['amount']}}</strong> <span class="text-muted">{{ $other_bal['currency']}}</span>
        @endforeach
	</h2>
</div>