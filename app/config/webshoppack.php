<?php

return array(

/*
*This view for Product list
*/
'manage_product_title'	=>	'Manage products',

/*
*	Pagination value for product list
*/
'paginate'	=>	'2',
/**
 * Page title of the jobs index page
 *
 * @type string
 */
'index_page_title' => 'Products',

/**
 * Meta description of the jobs index page
 *
 * @type string
 */
'index_page_meta_description' => '',

/**
 * Meta keywords of the jobs index page
 *
 * @type string
 */
'index_page_meta_keywords' => '',


/*
*This view for email file
*/
'admin_mail' => 'buysell@agriya.in',

/**
 * The login path is the path where the user if they fail a permission check
 *
 * @type string
 */
'user_fields' => array('fname' => 'first_name', 'lname' => 'last_name', 'email' => 'email'),


'user_id_field' => 'id',

'user_table' => 'users',

'title_min_length' => '5',
'title_max_length' => '100',
'summary_max_length' => '500',
'product_tab_validation' => false,
'download_files_is_mandatory' => 0,
'can_upload_free_product' => 1,
'photos_thumb_width' => 215,
'photos_thumb_height' => 170,
'photos_large_width' => 526,
'photos_large_height' => 526,
'photos_folder' => 'files/product_image/',
'photos_large_no_image' => 'prodnoimage-526x526.jpg',
'photos_thumb_no_image' => 'prodnoimage-215x170.jpg',
'photos_small_no_image' => 'prodnoimage-75x75.jpg',
'thumb_format_arr' => array('jpg','jpeg','gif','png'),
'default_format_arr' => array('jpg','jpeg','gif','png'),
'default_max_size' => 5, // in MB
'preview_format_arr' => array('jpg','jpeg','gif','png'),
'preview_max_size' => 5, // in MB
'preview_max' => 5, // maximum number of files
'thumb_max_size' => 5, // in MB
'photos_small_width' => 75,
'photos_small_height' => 75,
'photos_small_name' => 'S',
'package_name'	=>	'Buysell',
//'admin_uri'	=>	'webshop/admin/product',
//'admin_shop_uri'	=>	'webshop/admin/shop',
//'logout_uri'	=>	'webshop/admin/product',
'download_files_is_mandatory' => 1,
'download_format_arr' => array('zip', 'rar'),
'download_max_size' => 500, // in MB
'archive_folder' => 'files/product_zip/',
'product_auto_approve' => 0,
'site_default_currency' => 'USD',
'site_exchange_rate' => 5,
'shopname_min_length' => 3,
'shopname_max_length' => 50,
'shopslogan_min_length' => 3,
'shopslogan_max_length' => 75,
'fieldlength_shop_description_min' => 200,
'fieldlength_shop_description_max' => 2000,
'fieldlength_shop_contactinfo_min' => 50,
'fieldlength_shop_contactinfo_max' => 500,
'shop_image_folder' => 'files/shop_image/',
'shop_cancellation_policy_folder' => 'files/shop_cancel_policy/',
'shop_uploader_allowed_extensions' => 'jpg, jpeg, png, gif',
'shop_image_uploader_allowed_file_size' => '3072',
'shop_cancellation_policy_allowed_extensions' => 'txt,doc,docx',
'shop_cancellation_policy_allowed_file_size' => '3072',
'shop_image_thumb_width' => 700,
'shop_image_thumb_height' => 90,
'shop_image_allowed_upload_limit' => '1',
'site_cookie_prefix' => 'buysell',
'locatorhq_api_username' => 'Muralidharan5841',
'locatorhq_api_key' => 'ad498e79838d37537da4d3ac39253169ecfa0685',
'currency_is_multi_currency_support' => false,
'user_image_folder' => 'files/user_image/',
/*'user_image_small_width' => 50,
'user_image_small_height' => 50,
'user_image_thumb_width' => 90,
'user_image_thumb_height' => 90,
'user_image_large_width' => 140,
'user_image_large_height' => 140,*/
'currency_seeder_file' => 'files/currency.txt',
'shop_product_per_page_list' => '10',
'admin_product_catalog_uri'	=>	'webshop/admin/manage-product-catalog',
'admin_product_cat_uri'	=>	'webshop/admin/product-category',
'admin_product_cat_attr_uri'	=>	'webshop/admin/category-attributes',
'admin_product_attr_uri'	=>	'webshop/admin/product-attributes',
'product_category_image_folder' => 'files/product_category_image/',
'product_category_uploader_allowed_extensions' => 'jpg, jpeg, png, gif',
'product_category_image_uploader_allowed_file_size' => '5',
'product_category_image_thumb_width' => '176',
'product_category_image_thumb_height' => '160',
'product_category_image_allowed_upload_limit' => '1',
'product_cancellation_policy_folder' => 'files/product_cancel_policy/',
'admin_email' => 'l.aboobakkar+admin@agriya.in',
'ui_options' => array('select'=>'Dropdown','check'=>'Checkbox','option'=>'Radio button','multiselectlist'=>'Multi-select list'),
'ui_no_options' => array('text'=>'Textbox','textarea'=>'Textarea'),
'validation_rules' => array(
		array(
			'name'=>'required',
			'caption'=>'Required',
			'input_box'=> false,
			'validation'=>false
		),
		array(
			'name'=>'numeric',
			'caption'=>'Numeric',
			'input_box'=> false,
			'validation'=>false
		),
		array(
			'name'=>'alpha',
			'caption'=>'Alpha',
			'input_box'=> false,
			'validation'=>false
		),
		array(
			'name'=>'maxlength',
			'caption'=>'Max Length',
			'input_box'=> true,
			'validation'=>'required|number'
		),
		array(
			'name'=>'minlength',
			'caption'=>'Min Length',
			'input_box'=> true,
			'validation'=>'required|number'
		)
	),
'attribute_per_page_list' => 10,
'list_paging_sort_by' => array(
	'featured'	=> 'featured',
	'id'		=> 'id',
	'views' 	=> 'total_views',
	'download'	=> 'product_sold',
	'free'		=> 'is_free_product'
),
'collections_list_sort_by' => array(
	'id'		=> 'id',
	'views'		=> 'total_views',
	'comments'	=> 'total_comments'
),
'favorites_header_arr' => array(
	'product'		=> 'product',
	'shop'			=> 'shop',
	'collections'	=> 'collection'
),
'product_per_page_list' => 21,
'shop_per_page_list' => 10,
'product_search_include_title'	=> 1,
'allow_to_purchase_own_item' => true,
//'tags_csv_file' => 'packages/agriya/webshoppack/files/tags.csv',
);
?>