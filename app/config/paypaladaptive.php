<?php

return array(

'test_end_point' => 'https://svcs.sandbox.paypal.com/AdaptivePayments',
'live_end_point' => 'https://svcs.paypal.com/AdaptivePayments',
'api_version' => '1.3.0',
'api_mode' => 'Signature',
'detail_level' => 'ReturnAll',
'error_lang' => 'en_US',
'request_data_format' => 'NV',
'response_data_format' => 'NV',
'test_approval_url' => 'https://www.sandbox.paypal.com/webscr',
'live_approval_url' => 'https://www.paypal.com/webscr',


//API version required for paypal email verification
'test_end_point_email_verify' => 'https://svcs.sandbox.paypal.com/AdaptiveAccounts',
'live_end_point_email_verify' => 'https://svcs.paypal.com/AdaptiveAccounts',
'api_version_email_verify' => '1.0.0',

);
?>