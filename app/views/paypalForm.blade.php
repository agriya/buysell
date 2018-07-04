@if($error_exists)
	{{ $redirect_url }}|~~|{{ $error_exists }}
@else
	<form action="{{$payment_url}}" method="post" id="frmTransaction" name="frmTransaction">
	 <input type="hidden" name="charset" value="utf-8">
	 <input type="hidden" name="cmd" value="_xclick">
	 <input type="hidden" name="notify_url" value="{{ Url::to('payment/process-paypal')}}">
	 <input type="hidden" name="return" value="{{ $return_url}}">
	 <input type="hidden" name="cancel_return" value="{{ $cancel_url}}">
	 <input type="hidden" name="business" value="{{$business}}">
	 <input type="hidden" name="item_name" value="{{$item_name}}">
	 <input type="hidden" name="amount" value="{{$invoice_amount}}">
	 <input type="hidden" name="currency_code" value="{{ $currency_code }}" />
	 <input type="hidden" name="on0" value="InvoiceNumber" />
	 <input type="hidden" name="os0" value="{{$invoice_id}}" />
	 <input type="image" name="submit" border="0" src="https://www.paypal.com/en_US/i/btn/btn_buynow_LG.gif" alt="PayPal - The safer, easier way to pay online">
	</form>
@endif