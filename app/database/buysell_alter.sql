
-- date added : 31.03.2015
ALTER TABLE taxations MODIFY COLUMN tax_fee decimal(10,2);

ALTER TABLE `users`
MODIFY COLUMN `about_me` varchar(225) NOT NULL DEFAULT '',
MODIFY COLUMN `paypal_id` varchar(100) NOT NULL  DEFAULT '',
MODIFY COLUMN `total_products` bigint(20) NOT NULL  DEFAULT '0',
MODIFY COLUMN `featured_seller_expires` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
MODIFY COLUMN `sudopay_receiver_id` varchar(15) NOT NULL DEFAULT '';

-- Boobathi --
-- May-06-2015 --
ALTER TABLE `shop_order_item` MODIFY COLUMN `shipping_stock_country` int(11) DEFAULT NULL;

DROP TABLE IF EXISTS `meta_details`;
CREATE TABLE `meta_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_name` varchar(255) NOT NULL,
  `meta_title` text NOT NULL,
  `meta_description` text,
  `meta_keyword` text,
  `common_terms` text NOT NULL,
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- Boobathi --
-- May-15-2015 --
INSERT INTO `meta_details` (`id`, `page_name`, `meta_title`, `meta_description`, `meta_keyword`, `common_terms`, `date_added`, `date_updated`, `status`) VALUES
('1', 'default', 'VAR_SITE_NAME', 'VAR_SITE_NAME, agriya, products, online', 'VAR_SITE_NAME - A product of Agriya', 'VAR_SITE_NAME', '0000-00-00 00:00:00', '2015-05-15 11:41:24', 1);

-- Boobathi --
-- May-19-2015 --
ALTER TABLE `meta_details` ADD COLUMN `language` varchar(20) NOT NULL AFTER `common_terms`;
INSERT INTO `meta_details` (`id`, `page_name`, `meta_title`, `meta_description`, `meta_keyword`, `common_terms`, `language`, `date_added`, `date_updated`, `status`) VALUES
(1, 'default', 'VAR_SITE_NAME', 'VAR_SITE_NAME, agriya, products, online', 'VAR_SITE_NAME - A product of Agriya', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '2015-05-15 11:41:24', 1),
(11, 'home', 'VAR_SITE_NAME', 'VAR_SITE_NAME', 'VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '2015-05-19 20:18:54', 1),
(12, 'my-account', 'My Account - VAR_SITE_NAME', 'My Account - VAR_SITE_NAME', 'My Account - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(13, 'collections', 'Collections - VAR_SITE_NAME', 'Collections - VAR_SITE_NAME', 'Collections - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(14, 'view-collections', 'View Collections - VAR_SITE_NAME', 'View Collections - VAR_SITE_NAME', 'View Collections - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(15, 'my-collections', 'My Collections - VAR_SITE_NAME', 'My Collections - VAR_SITE_NAME', 'My Collections - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(16, 'add-collection', 'Add Collections - VAR_SITE_NAME', 'Add Collections - VAR_SITE_NAME', 'Add Collections - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(17, 'update-collection', 'Edit Collections - VAR_SITE_NAME', 'Edit Collections - VAR_SITE_NAME', 'Edit Collections - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(18, 'people-list', 'People List - VAR_SITE_NAME', 'People List - VAR_SITE_NAME', 'People List - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(19, 'my-purchases', 'My Purchases - VAR_SITE_NAME', 'My Purchases - VAR_SITE_NAME', 'My Purchases - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(20, 'order-details', 'Order Details - VAR_SITE_NAME', 'Order Details - VAR_SITE_NAME', 'Order Details - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(21, 'my-sales', 'My Sales - VAR_SITE_NAME', 'My Sales - VAR_SITE_NAME', 'My Sales - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(22, 'manage-shipping-templates', 'Manage Shipping Templates - VAR_SITE_NAME', 'Manage Shipping Templates - VAR_SITE_NAME', 'Manage Shipping Templates - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(23, 'add-shipping-template', 'Add Shipping Template - VAR_SITE_NAME', 'Add Shipping Template - VAR_SITE_NAME', 'Add Shipping Template - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(24, 'edit-shipping-template', 'Edit Shipping Template - VAR_SITE_NAME', 'Edit Shipping Template - VAR_SITE_NAME', 'Edit Shipping Template - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(25, 'view-shipping-template', 'View Shipping Template - VAR_SITE_NAME', 'View Shipping Template - VAR_SITE_NAME', 'View Shipping Template - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(26, 'email-activation', 'New email activation', 'New email activation', 'New email activation', '', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(27, 'edit-addresses', 'Edit Address - VAR_SITE_NAME', 'Edit Address - VAR_SITE_NAME', 'Edit Address - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(28, 'add-addresses', 'Add Address - VAR_SITE_NAME', 'Add Address - VAR_SITE_NAME', 'Add Address - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(29, 'checkout', 'Checkout - VAR_SITE_NAME', 'Checkout - VAR_SITE_NAME', 'Checkout - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(30, 'my-coupons', 'My Coupons - VAR_SITE_NAME', 'My Coupons - VAR_SITE_NAME', 'My Coupons - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(31, 'add-coupons', 'Add Coupons - VAR_SITE_NAME', 'Add Coupons - VAR_SITE_NAME', 'Add Coupons - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(32, 'edit-coupons', 'Edit Coupons - VAR_SITE_NAME', 'Edit Coupons - VAR_SITE_NAME', 'Edit Coupons - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(33, 'my-addresses', 'My Addresses - VAR_SITE_NAME', 'My Addresses - VAR_SITE_NAME', 'My Addresses - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(34, 'my-favorite-product', 'Product - Favourites - VAR_SITE_NAME', 'Product - Favourites - VAR_SITE_NAME', 'Product - Favourites - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(35, 'my-favorite-shop', 'Shop - Favourites - VAR_SITE_NAME', 'Shop - Favourites - VAR_SITE_NAME', 'Shop - Favourites - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(36, 'my-favorite-collection', 'Collection - Favourites - VAR_SITE_NAME', 'Collection - Favourites - VAR_SITE_NAME', 'Collection - Favourites - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(37, 'my-wishlist', 'My Wishlist - VAR_SITE_NAME', 'My Wishlist - VAR_SITE_NAME', 'My Wishlist - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(38, 'manage-feedbacks', 'Manage Feedbacks - VAR_SITE_NAME', 'Manage Feedbacks - VAR_SITE_NAME', 'Manage Feedbacks - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(39, 'add-feedbacks', 'Add Feedback - VAR_SITE_NAME', 'Add Feedback - VAR_SITE_NAME', 'Add Feedback - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(40, 'edit-feedbacks', 'Edit Feedback - VAR_SITE_NAME', 'Edit Feedback - VAR_SITE_NAME', 'Edit Feedback - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(41, 'my-invoice', 'My Invoices - VAR_SITE_NAME', 'My Invoices - VAR_SITE_NAME', 'My Invoices - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(42, 'invoice-details', 'Invoice Details - VAR_SITE_NAME', 'Invoice Details - VAR_SITE_NAME', 'Invoice Details - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(43, 'inbox-message', 'Inbox Message - VAR_SITE_NAME', 'Inbox Message - VAR_SITE_NAME', 'Inbox Message - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(44, 'sent-message', 'Sent Message - VAR_SITE_NAME', 'Sent Message - VAR_SITE_NAME', 'Sent Message - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(45, 'trash-message', 'Trash Message - VAR_SITE_NAME', 'Trash Message - VAR_SITE_NAME', 'Trash Message - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(46, 'saved-message', 'Saved Message - VAR_SITE_NAME', 'Saved Message - VAR_SITE_NAME', 'Saved Message - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(47, 'compose-message', 'Compose Message - VAR_SITE_NAME', 'Compose Message - VAR_SITE_NAME', 'Compose Message - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(48, 'convert-funds', 'Convert funds - VAR_SITE_NAME', 'Convert funds - VAR_SITE_NAME', 'Convert funds - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(49, 'my-withdrawals', 'My Withdrawals - VAR_SITE_NAME', 'My Withdrawals - VAR_SITE_NAME', 'My Withdrawals - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(50, 'convert-funds', 'convert funds - VAR_SITE_NAME', 'convert funds - VAR_SITE_NAME', 'convert funds - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(51, 'pay-check-out', 'Pay checkout - VAR_SITE_NAME', 'Checkout page of the site', 'pay,checkout', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(52, 'product-edit-meta', 'Edit Product - VAR_SITE_NAME', 'Edit Product - VAR_SITE_NAME', 'Edit Product - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(53, 'manage-products', 'Manage products - VAR_SITE_NAME', 'Manage products - VAR_SITE_NAME', 'Manage products - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(54, 'view-profile', 'View Profile - VAR_SITE_NAME', 'View Profile - VAR_SITE_NAME', 'View Profile - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(55, 'shop-policies', 'Shop Policies - VAR_SITE_NAME', 'Shop Policies - VAR_SITE_NAME', 'Shop Policies - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(56, 'manage-taxations', 'Manage Taxations - VAR_SITE_NAME', 'Manage Taxations - VAR_SITE_NAME', 'Manage Taxations - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(57, 'add-taxations', 'Add Taxations - VAR_SITE_NAME', 'Add Taxations - VAR_SITE_NAME', 'Add Taxations - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(58, 'update-taxations', 'Update Taxations - VAR_SITE_NAME', 'Update Taxations - VAR_SITE_NAME', 'Update Taxations - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(59, 'transactions-history', 'Transactions History  - VAR_SITE_NAME', 'Transactions History  - VAR_SITE_NAME', 'Transactions History  - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(60, 'circle', 'Circle - VAR_SITE_NAME', 'Circle - VAR_SITE_NAME', 'Circle - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(61, 'my-wallet-account', 'My Wallet Account - VAR_SITE_NAME', 'My Wallet Account - VAR_SITE_NAME', 'My Wallet Account - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(62, 'add-amount-to-wallet-account', 'Add Amount To Wallet Account - VAR_SITE_NAME', 'Add Amount To Wallet Account - VAR_SITE_NAME', 'Add Amount To Wallet Account - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '2015-05-18 18:50:37', 1),
(66, 'featured-deal-req-approved', 'Approved by admin Featured Deal Requests - VAR_SITE_NAME', 'Approved by admin Featured Deal Requests - VAR_SITE_NAME', 'Approved by admin Featured Deal Requests - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(67, 'view-shop', 'Shop - SHOP_NAME - VAR_SITE_NAME', 'Shop SHOP_NAME VAR_SITE_NAME', 'Shop, SHOP_NAME, VAR_SITE_NAME', 'SHOP_NAME, VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(68, 'view-shop-policy', 'Shop - SHOP_NAME - Shop Policies -VAR_SITE_NAME', 'Shop SHOP_NAME Shop Policies -VAR_SITE_NAME ', 'Shop, SHOP_NAME, Shop Policies, VAR_SITE_NAME  ', 'SHOP_NAME, VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(69, 'view-shop-reviews', 'Shop - SHOP_NAME - Shop Review -VAR_SITE_NAME', 'Shop SHOP_NAME Shop Review VAR_SITE_NAME', 'Shop, SHOP_NAME, Shop Review, VAR_SITE_NAME', 'SHOP_NAME, VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(70, 'view-shop-favorites', 'Shop - SHOP_NAME - Shop Favorites -VAR_SITE_NAME ', 'Shop SHOP_NAME Shop Favorites VAR_SITE_NAME ', 'Shop, SHOP_NAME, Shop Favorites, VAR_SITE_NAME ', 'SHOP_NAME, VAR_SITE_NAME ', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(71, 'add-product', 'Add product - TAB_NAME - VAR_SITE_NAME', 'Add product TAB_NAME VAR_SITE_NAME', 'Add product, TAB_NAME, VAR_SITE_NAME', 'TAB_NAME, VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(72, 'edit-product', 'PRODUCT_NAME - TAB_NAME - Edit Product', 'PRODUCT_NAME TAB_NAME Edit Product', 'PRODUCT_NAME, TAB_NAME, Edit Product', 'TAB_NAME,PRODUCT_NAME, VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(73, 'customer-product-view', 'View Product - PRODUCT_NAME - VAR_SITE_NAME', 'View Product - PRODUCT_NAME - VAR_SITE_NAME', 'View Product - PRODUCT_NAME - VAR_SITE_NAME', 'PRODUCT_NAME, VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(74, 'customer-category-view', 'VAR_SITE_NAME - Marketplace - CATEGORY_NAME', 'VAR_SITE_NAME  Marketplace  CATEGORY_NAME', 'VAR_SITE_NAME, Marketplace, CATEGORY_NAME', 'VAR_SITE_NAME,CATEGORY_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1);

-- June-03-2015
-- Boobathi N
INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES
('generalConfig', 'cache', '1', 'Boolean', 'site', 'site', 'Yes', 999, 'Cache enable', NOW()),
('generalConfig', 'cache_expiry_minutes', '10', 'Int', 'site', 'site', 'Yes', 999, 'Cache Expiry Minutes', NOW());

ALTER TABLE `users` ENGINE=InnoDB;
ALTER TABLE `groups` ENGINE=InnoDB;
ALTER TABLE `users_groups` ENGINE=InnoDB;
ALTER TABLE `throttle` ENGINE=InnoDB;
ALTER TABLE `password_reminders` ENGINE=InnoDB;
ALTER TABLE `message` ENGINE=InnoDB;
ALTER TABLE `site_transaction_details` ENGINE=InnoDB;
ALTER TABLE `user_products_wishlist` ENGINE=InnoDB;
ALTER TABLE `user_cart` ENGINE=InnoDB;
ALTER TABLE `user_account_balance` ENGINE=InnoDB;
ALTER TABLE `withdrawal_request` ENGINE=InnoDB;
ALTER TABLE `paypal_adaptive_payments_transaction` ENGINE=InnoDB;
ALTER TABLE `product` ENGINE=InnoDB;
ALTER TABLE `product_category` ENGINE=InnoDB;
ALTER TABLE `shop_details` ENGINE=InnoDB;
ALTER TABLE `user_product_section` ENGINE=InnoDB;
ALTER TABLE `product_category_attributes` ENGINE=InnoDB;
ALTER TABLE `product_image` ENGINE=InnoDB;
ALTER TABLE `product_attributes_values` ENGINE=InnoDB;
ALTER TABLE `product_resource` ENGINE=InnoDB;
ALTER TABLE `product_log` ENGINE=InnoDB;
ALTER TABLE `product_attributes_option_values` ENGINE=InnoDB;
ALTER TABLE `product_attributes` ENGINE=InnoDB;
ALTER TABLE `product_attribute_options` ENGINE=InnoDB;
ALTER TABLE `currency_exchange_rate` ENGINE=InnoDB;
ALTER TABLE `users_shop_details` ENGINE=InnoDB;
ALTER TABLE `api_exclude_tags` ENGINE=InnoDB;
ALTER TABLE `addresses` ENGINE=InnoDB;
ALTER TABLE `billing_address` ENGINE=InnoDB;
ALTER TABLE `shipping_fees` ENGINE=InnoDB;
ALTER TABLE `countries` ENGINE=InnoDB;
ALTER TABLE `taxations` ENGINE=InnoDB;
ALTER TABLE `product_taxations` ENGINE=InnoDB;
ALTER TABLE `shop_order` ENGINE=InnoDB;
ALTER TABLE `shop_order_item` ENGINE=InnoDB;
ALTER TABLE `order_receivers` ENGINE=InnoDB;
ALTER TABLE `invoices` ENGINE=InnoDB;
ALTER TABLE `user_cart_shipping_address` ENGINE=InnoDB;
ALTER TABLE `product_stocks` ENGINE=InnoDB;
ALTER TABLE `cancellation_policy` ENGINE=InnoDB;
ALTER TABLE `product_price_groups` ENGINE=InnoDB;
ALTER TABLE `temp_table_log` ENGINE=InnoDB;
ALTER TABLE `product_price_groups_orders` ENGINE=InnoDB;
ALTER TABLE `common_invoice` ENGINE=InnoDB;
ALTER TABLE `credits_log` ENGINE=InnoDB;
ALTER TABLE `geographic_location` ENGINE=InnoDB;
ALTER TABLE `shipping_companies` ENGINE=InnoDB;
ALTER TABLE `shipping_templates` ENGINE=InnoDB;
ALTER TABLE `shipping_template_companies` ENGINE=InnoDB;
ALTER TABLE `shipping_template_fee_custom` ENGINE=InnoDB;
ALTER TABLE `shipping_template_fee_custom_countries` ENGINE=InnoDB;
ALTER TABLE `product_package_details` ENGINE=InnoDB;
ALTER TABLE `shipping_template_delivery_custom` ENGINE=InnoDB;
ALTER TABLE `shipping_template_delivery_custom_countries` ENGINE=InnoDB;
ALTER TABLE `shippping_template_fee_custom_weight` ENGINE=InnoDB;
ALTER TABLE `dhl_zone_additional_price` ENGINE=InnoDB;
ALTER TABLE `dhl_zone_price` ENGINE=InnoDB;
ALTER TABLE `dhl_zone_service` ENGINE=InnoDB;
ALTER TABLE `china_post_group_price` ENGINE=InnoDB;
ALTER TABLE `shipping_countries_aramex` ENGINE=InnoDB;
ALTER TABLE `shipping_countries_ctr_land_pickup` ENGINE=InnoDB;
ALTER TABLE `shipping_countries_hongkong_post_air_mail` ENGINE=InnoDB;
ALTER TABLE `shipping_countries_seller_shipping_method` ENGINE=InnoDB;
ALTER TABLE `shipping_countries_sf_express` ENGINE=InnoDB;
ALTER TABLE `shipping_countries_singapore_post` ENGINE=InnoDB;
ALTER TABLE `shipping_countries_sweden_post` ENGINE=InnoDB;
ALTER TABLE `shipping_countries_swiss_post` ENGINE=InnoDB;
ALTER TABLE `collections` ENGINE=InnoDB;
ALTER TABLE `collection_products` ENGINE=InnoDB;
ALTER TABLE `coupons` ENGINE=InnoDB;
ALTER TABLE `collection_comments` ENGINE=InnoDB;
ALTER TABLE `users_circle` ENGINE=InnoDB;
ALTER TABLE `product_favorites` ENGINE=InnoDB;
ALTER TABLE `shop_favorites` ENGINE=InnoDB;
ALTER TABLE `collection_favorites` ENGINE=InnoDB;
ALTER TABLE `product_invoice_feedback` ENGINE=InnoDB;
ALTER TABLE `social` ENGINE=InnoDB;
ALTER TABLE `site_logo` ENGINE=InnoDB;
ALTER TABLE `static_pages` ENGINE=InnoDB;
ALTER TABLE `seller_request` ENGINE=InnoDB;
ALTER TABLE `reported_products` ENGINE=InnoDB;
ALTER TABLE `banner_images` ENGINE=InnoDB;
ALTER TABLE `users_favorites_products` ENGINE=InnoDB;
ALTER TABLE `users_featured` ENGINE=InnoDB;
ALTER TABLE `users_top_picks` ENGINE=InnoDB;
ALTER TABLE `sell_page_static_content` ENGINE=InnoDB;
ALTER TABLE `newsletter` ENGINE=InnoDB;
ALTER TABLE `newsletter_users` ENGINE=InnoDB;
ALTER TABLE `currencies` ENGINE=InnoDB;
ALTER TABLE `product_comments` ENGINE=InnoDB;
ALTER TABLE `product_favorites_list` ENGINE=InnoDB;
ALTER TABLE `advertisement` ENGINE=InnoDB;
ALTER TABLE `newsletter_subscriber` ENGINE=InnoDB;
ALTER TABLE `mass_mail` ENGINE=InnoDB;
ALTER TABLE `mass_mail_users` ENGINE=InnoDB;
ALTER TABLE `mass_mail_sent_users` ENGINE=InnoDB;
ALTER TABLE `languages` ENGINE=InnoDB;
ALTER TABLE `config_data` ENGINE=InnoDB;

-- Feb-16-2016 --
-- Ravikumar --
INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('mail', 'from_name', 'noreply', 'String', 'site', 'mailer', 'Yes', 999, 'From Name', NOW() );

UPDATE `config_data` SET `file_name` = 'mail', `config_section` = 'mailer', `edit_order` = 999 WHERE `config_data`.`config_var` = 'from_email';