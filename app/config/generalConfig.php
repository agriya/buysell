<?php
// application/config/site/generalConfig.php
return array(
	'allowed_currencies' => array('USD','INR','EUR'),//dont know
	'allowed_currencies_to_add_product' => array('USD'),//dont know
	'site_cookie_prefix' => 'buysell',
	'site_default_country' => array('38' => 'China'),
	'allowed_countries' => array('38' => 'China','153' => 'Pakistan'),
	'site_default_shipping_country' => array('153' => 'Pakistan'),
	'config_tab_list_arr' => array("site" => 1, "login" => 3, "plugin" => 4),
	'config_tab_section_list_arr' => array("site" => array('site', 'captcha', 'product', 'mailer'), "payment" => array('paypal', 'adaptive payment - Live mode', 'adaptive payment - Test mode')),

	'banner_image_large_width' => 1440,
	'banner_image_large_height' => 420,
	'banner_image_folder' => 'files/banner_image/',
	'banner_image_allowed_extensions' => 'jpg, jpeg, png, gif',
	'banner_image_upload_max_filesize'	=> '2048',
	'banner_image_speed'	=> '4000',

	'prod_view_review_list_count'	=> '10',

	'favorite_default_list_name'	=> 'Items I Love',
	'favorite_list_name_char_limit'	=> 15,
	'banner_position_arr'	=> array('side-banner' => '200x200', 'bottom-banner' => '728x90', 'top-banner' => '728x90'),

	'import_folder' => 'files/import_subscriber/',
	'import_upload_max_filesize' => '5120',
	'reply_to_email'	=> 'replay_example@sample.in',

	'language_image_width' => 16,
	'language_image_height' => 11,
	'language_image_folder' => 'files/language_image/',
	'language_image_allowed_extensions' => 'gif',
	'cache' => 1,
	'cache_expiry_minutes' => 1440
);
