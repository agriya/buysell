<?php

return array(
	//TaxationsService
	'input_array_empty' => 'Input array can not be empty',
	'options_invalid' => 'The options can not be empty. Options array should have either \'id\' or \'user_id\'. ',
	'return_type_invalid' => 'Return type can be either \'all\' or \'paginate\' or \'list\' or \'first\'. ',

	'taxation_id_empty' => 'The taxation id can not be empty',
	'taxation_id_not_avail' => 'Given taxation id is not present or may be deleted',
	'something_went_wrong' => 'Something went wrong. Details of the given taxation is wrong',

	//Product Taxation service
	'tax_already_added' => 'Specified Tax fee has already been added for this product',
	'options_empty' => 'Options can not be empty',
	'producttax_return_type' => 'Return type can be either \'all\' or \'first\'. ',
	'producttax_options_invalid' => 'Options array should have either sperate or compbination of \'id\', \'product_id\' , \'taxation_id\'. Other elements are not valid',
	'taxation_and_condition_empty' => 'Both taxation id and condtions can not be empty. Either one should be passed to update the taxation fee.',
	'input_condition_mismatch' => 'Input conditions are mismatch. Only \' product_id\' and \'taxation_id\' are accepted',
	'tax_fee_and_condition_required' => 'To do taxation fee operations, either id or conditions parameters are required',

	'producttax_not_avail' => 'Given product taxation details is not present or may be deleted',
	'producttax_went_wrong' => 'Something went wrong with the details of the given product taxation',
	'update_field_invalid' => 'You can either update \' tax_fee\' or \'fee_type\'. Other fields are not valid',
	'tax_fee_min' => 'Tax fee should be a positive number',
	'product_id_empty' => 'Product can not be empty',


);