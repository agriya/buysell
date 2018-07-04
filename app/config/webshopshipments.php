<?php

return array(

/*
*Table name for coutries list
*/
'countries_table_details'	=>	array(
	'table_name' => 'currency_exchange_rate',
	'id' => 'id',
	'country_name' => 'country',
),

/*
*Table name for shipping fees
*/
'shipping_fees_table_details'	=>	array(
	'table_name' => 'shipping_fees',
	'id' => 'id',
	'country_id' => 'country_id',
	'shipping_fee' => 'shipping_fee',
	'foreign_id' => 'foreign_id'
),


);
?>