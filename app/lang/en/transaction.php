<?php
return array(

	'paypay_transaction' => 'Paypal transaction ID',
	'transaction_id' => 'Transaction ID',

	'purchase_credit' => 'Credited amount to VAR_USER VAR_PAYMENT_METHOD account for the order: VAR_ORDER',
	'purchase_debit' => 'Debited amount from VAR_USER VAR_PAYMENT_METHOD account for the order: VAR_ORDER',

	'purchase_fee_credit' => 'Credited amount to site VAR_PAYMENT_METHOD account from VAR_BUYER (buyer) for the order: VAR_ORDER',
	'purchase_fee_debit' => 'Debited amount from site VAR_PAYMENT_METHOD account to transfer to VAR_SELLER (seller) for the order (except site commission): VAR_ORDER',

	'purchase_refunded_credit' => 'Credited amount to VAR_USER VAR_PAYMENT_METHOD account for the refund of product: VAR_PRODUCT in the order: VAR_ORDER',
	'purchase_refunded_debit' => 'Debited amount from VAR_USER VAR_PAYMENT_METHOD account for the refund of product: VAR_PRODUCT in the order: VAR_ORDER',

	'purchase_fee_refunded_credit' => 'Credited amount to VAR_USER VAR_PAYMENT_METHOD account for the refund of product: VAR_PRODUCT in the order: VAR_ORDER',
	'purchase_fee_refunded_debit' => 'Debited site commission amount from VAR_USER VAR_PAYMENT_METHOD account for the refund of product: VAR_PRODUCT in the order: VAR_ORDER',

	'withdrawal_credit' => 'Amount credited to VAR_USER VAR_PAYMENT_METHOD account for the withdrawal request: VAR_WITHDRAWAL_PAGE',
	'withdrawal_debit' => 'Amount debited from VAR_USER account for the withdrawal request: VAR_WITHDRAWAL_PAGE',

	'withdrawal_fee_credit' => 'Credited withdrawal fee to site wallet account for the withdrawal request: VAR_WITHDRAWAL_PAGE',

	'product_listing_fee_credit' => 'Credited product listing fee to site wallet account for the product: VAR_PRODUCT_URL',
	'product_listing_fee_debit' => 'Debited product listing fee from VAR_USER wallet for the product: VAR_PRODUCT_URL',

	'walletaccount_credit' => 'Credited amount to VAR_USER wallet account from paypal. View Invoice: VAR_INVOICE_PAGE',
	'walletaccount_debit' => 'Debited amount from VAR_USER wallet account',

	'walletaccount_fromsite_credit' => 'Credited amount to VAR_USER wallet account from Site. View Invoice: VAR_INVOICE_PAGE',

	'walletaccount_purchase_credit' => 'Credited amount to VAR_USER wallet account from paypal for the order: VAR_ORDER',

	'transaction_dates' => 'Transaction from date',
	'paypal_transaction_id' => 'Paypal Transaction Id',

	'gateway_fee_credit' => 'Credited amount to gateway fee',
	'gateway_fee_debit' => 'Debited amount from VAR_USER for gateway fee. View Invoice: VAR_INVOICE_PAGE',

	'gateway_fee_purchase_credit' => 'Credited amount to VAR_USER VAR_PAYMENT_METHOD account as gateway fee for the order: VAR_ORDER',
	'gateway_fee_purchase_debit' => 'Debited amount from VAR_USER VAR_PAYMENT_METHOD account as gateway fee for the order: VAR_ORDER',

	'credit' => 'Credit',
	'pending_credit' => 'Pending Credit',

	'debit' => 'Debit',
	'pending_debit' => 'Pending Debit',
);

?>