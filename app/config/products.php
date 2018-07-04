<?php

return array(

/**
 * Logged user id
 *
 * @type string
 */
'logged_user_id'=> function()
{
	$user_id = 0;
	if(BasicCUtil::sentryCheck()) {
		$user_id = Sentry::getUser()->id;
	}
	return $user_id;
},

/**
 * is user Logged
 *
 * @type boolean
 */
'is_logged_in'=> function()
{
	if(BasicCUtil::sentryCheck()) {
		return true;
	}
	return false;
},

/**
 * is Admin user
 *
 * @type boolean
 */
'is_admin'=> function()
{
	if(Sentry::getUser()->hasAnyAccess(['system', 'system.Admin'])) {
		return true;
	}
	return false;
},

'title_min_length' => '5',
'title_max_length' => '100',
'summary_max_length' => '500',
'can_upload_free_product' => 1,
'product_auto_approve' => 0,
'site_default_currency' => 'USD',	// Not used, need to remove
'site_exchange_rate' => 5,
'shopname_min_length' => 3,
'shopname_max_length' => 50,
'shopslogan_min_length' => 3,
'shopslogan_max_length' => 75,
'fieldlength_shop_description_min' => 200,
'fieldlength_shop_description_max' => 2000,
'fieldlength_shop_contactinfo_min' => 50,
'fieldlength_shop_contactinfo_max' => 500,
'site_cookie_prefix' => 'buysell',
'locatorhq_api_username' => 'Muralidharan5841',
'locatorhq_api_key' => 'ad498e79838d37537da4d3ac39253169ecfa0685',
'currency_is_multi_currency_support' => false,
'currency_seeder_file' => 'packages/agriya/products/files/currency.txt',
'download_files_is_mandatory' => 1,
'ui_options' => array('select'=>'Dropdown','check'=>'Checkbox','option'=>'Radio button','multiselectlist'=>'Multi-select list'),
'ui_no_options' => array('text'=>'Textbox','textarea'=>'Textarea'),
'item_couponcode_exists' => false,
);
?>