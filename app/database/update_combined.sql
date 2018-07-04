-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 10, 2014 at 12:20 PM
-- Server version: 5.5.36
-- PHP Version: 5.4.25

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(225) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `permissions` text NOT NULL,
  `activated` int(11) NOT NULL,
  `activation_code` varchar(255) NOT NULL,
  `activated_at` datetime NOT NULL,
  `last_login` datetime NOT NULL,
  `persist_code` varchar(255) NOT NULL,
  `reset_password_code` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `about_me` varchar(225) NOT NULL DEFAULT '',
  `image_name` varchar(255) DEFAULT NULL,
  `image_ext` varchar(255) DEFAULT NULL,
  `large_width` int(11) DEFAULT NULL,
  `large_height` int(11) DEFAULT NULL,
  `small_width` int(11) DEFAULT NULL,
  `small_height` int(11) DEFAULT NULL,
  `thumb_width` int(11) DEFAULT NULL,
  `thumb_height` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `is_banned` int(11) DEFAULT '0',
  `is_shop_owner` enum('No','Yes') DEFAULT 'No',
  `shop_status` tinyint(4) NOT NULL DEFAULT '1',
  `paypal_id` varchar(100) NOT NULL DEFAULT '',
  `total_products` bigint(20) NOT NULL DEFAULT '0',
  `is_requested_for_seller` enum('No','Yes') DEFAULT 'No',
  `is_featured_seller` enum('Yes','No') NOT NULL DEFAULT 'No',
  `featured_seller_expires` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_allowed_to_add_product` enum('No','Yes','Blocked') DEFAULT 'No',
  `subscribe_newsletter` enum('0','1') NOT NULL DEFAULT '1',
  `sudopay_receiver_id` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `Ind_featured_created` (`is_featured_seller`,`created_at`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `permissions` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_groups`
--

DROP TABLE IF EXISTS `users_groups`;
CREATE TABLE `users_groups` (
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  KEY `Ind_user_group` (`user_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `throttle`
--

DROP TABLE IF EXISTS `throttle`;
CREATE TABLE `throttle` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `attempts` int(11) NOT NULL,
  `suspended` int(11) NOT NULL,
  `banned` int(11) NOT NULL,
  `last_attempt_at` datetime NOT NULL,
  `suspended_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Ind_user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `password_reminders`
--

DROP TABLE IF EXISTS `password_reminders`;
CREATE TABLE `password_reminders` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date_added` datetime NOT NULL,
  `from_user_id` bigint(20) NOT NULL,
  `to_user_id` bigint(20) NOT NULL,
  `last_replied_by` bigint(20) NOT NULL,
  `last_replied_date` datetime NOT NULL,
  `subject` varchar(200) NOT NULL,
  `reply_count` int(16) NOT NULL,
  `message_text` text NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `from_message_status` enum('Unread','Read','Saved','Deleted') DEFAULT 'Read',
  `to_message_status` enum('Unread','Read','Saved','Deleted') DEFAULT 'Unread',
  `open_alert_needed` enum('Yes','No') DEFAULT 'No',
  `is_replied` tinyint(1) NOT NULL DEFAULT '0',
  `rel_type` varchar(100) NOT NULL,
  `rel_id` int(16) NOT NULL,
  `rel_table` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `site_transaction_details`;
CREATE TABLE IF NOT EXISTS `site_transaction_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date_added` datetime NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `transaction_type` enum('credit','debit','pending_credit','pending_debit') NOT NULL DEFAULT 'credit',
  `amount` decimal(20,2) NOT NULL,
  `currency` varchar(15) DEFAULT NULL,
  `transaction_key` enum('purchase', 'purchase_fee', 'purchase_cancelled', 'purchase_fee_cancelled', 'purchase_refunded', 'purchase_fee_refunded', 'withdrawal', 'withdrawal_fee', 'conversion_fee', 'currency_conversion', 'product_listing_fee', 'walletaccount', 'walletaccount_purchase', 'walletaccount_fromsite', 'deal_listfee_paid', 'deal_tipping_success', 'deal_tipping_failure', 'product_featured_fee', 'seller_featured_fee', 'gateway_fee', 'gateway_fee_purchase') NOT NULL,
  `reference_content_id` bigint(20) NOT NULL,
  `reference_content_table` varchar(100) NOT NULL,
  `invoice_id` bigint(20) NOT NULL,
  `purchase_code` varchar(50) NOT NULL,
  `related_transaction_id` bigint(20) NOT NULL,
  `status` varchar(100) NOT NULL,
  `transaction_notes` text NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `payment_type` varchar(10) DEFAULT NULL,
  `paypal_adaptive_transaction_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_products_wishlist`
--

DROP TABLE IF EXISTS `user_products_wishlist`;
CREATE TABLE `user_products_wishlist`(
  `id` INT NOT NULL AUTO_INCREMENT ,
  `user_id` INT ,
  `product_id` INT ,
  `created_at` DATETIME ,
  `updated_at` DATETIME ,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- Package addtocart

DROP TABLE IF EXISTS `user_cart`;
CREATE TABLE `user_cart` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `item_id` varchar(255) NOT NULL,
  `item_owner_id` bigint(20) NOT NULL,
  `qty` int(11) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `cookie_id` varchar(50) NOT NULL,
  `matrix_id` bigint(20) NOT NULL DEFAULT '0',
  `use_giftwrap` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `Ind_cookie_id` (`cookie_id`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Package addtocart

-- --------------------------------------------------------

--
-- Table structure for table `user_account_balance`
--

DROP TABLE IF EXISTS `user_account_balance`;
CREATE TABLE `user_account_balance` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `cleared_amount` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Ind_user_id_id` (`user_id`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `withdrawal_request`
--

DROP TABLE IF EXISTS `withdrawal_request`;
CREATE TABLE `withdrawal_request` (
  `withdraw_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_type_del` enum('','Paypal','NEFT','WireTransfer') NOT NULL,
  `available_balance` decimal(20,2) NOT NULL COMMENT 'at the time of withdrawal',
  `fee` decimal(20,2) NOT NULL,
  `payment_type` enum('Paypal','NEFT','WireTransfer') NOT NULL DEFAULT 'Paypal',
  `pay_to_user_account` text NOT NULL,
  `paid_notes` text NOT NULL,
  `admin_notes` text NOT NULL,
  `set_as_paid_by` bigint(20) NOT NULL,
  `site_transaction_id` bigint(20) NOT NULL,
  `date_paid` datetime NOT NULL,
  `date_cancelled` datetime NOT NULL,
  `cancelled_reason` text NOT NULL,
  `cancelled_by` bigint(20) NOT NULL,
  `added_date` datetime NOT NULL,
  `status` enum('Active', 'Paid', 'Cancelled') NOT NULL DEFAULT 'Active',
  PRIMARY KEY (`withdraw_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Package  paypaladaptive


DROP TABLE IF EXISTS `paypal_adaptive_payments_transaction`;
CREATE TABLE `paypal_adaptive_payments_transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pay_key` varchar(255) DEFAULT NULL,
  `tracking_id` varchar(255) DEFAULT NULL,
  `currency_code` varchar(100) DEFAULT NULL,
  `buyer_email` varchar(255) DEFAULT NULL,
  `receiver_details` text,
  `ipn_post_str` text,
  `payment_details_str` text,
  `error_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `buyer_trans_id` varchar(255) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- webshoppack
-- --------------------------------------------------------
--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_code` varchar(20) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_description` text NOT NULL,
  `product_support_content` text NOT NULL,
  `meta_title` varchar(255) NOT NULL,
  `meta_keyword` varchar(255) NOT NULL,
  `meta_description` varchar(255) NOT NULL,
  `product_highlight_text` varchar(500) NOT NULL,
  `product_slogan` varchar(255) NOT NULL,
  `purchase_price` decimal(15,2) NOT NULL,
  `product_price` decimal(15,2) NOT NULL,
  `product_price_usd` decimal(20,2) NOT NULL,
  `product_price_currency` varchar(10) NOT NULL,
  `product_user_id` bigint(20) NOT NULL,
  `product_sold` bigint(20) NOT NULL,
  `product_added_date` date NOT NULL,
  `url_slug` varchar(150) NOT NULL,
  `demo_url` varchar(255) NOT NULL,
  `demo_details` text NOT NULL,
  `product_category_id` bigint(20) NOT NULL,
  `product_tags` varchar(255) NOT NULL,
  `total_views` bigint(20) NOT NULL,
  `is_featured_product` enum('Yes','No') NOT NULL DEFAULT 'No',
  `featured_product_expires` datetime NOT NULL,
  `is_user_featured_product` enum('Yes','No') NOT NULL DEFAULT 'No',
  `date_activated` bigint(20) NOT NULL DEFAULT '0',
  `product_discount_price` decimal(15,2) NOT NULL,
  `product_discount_price_usd` decimal(20,2) NOT NULL,
  `product_discount_fromdate` date NOT NULL,
  `product_discount_todate` date NOT NULL,
  `product_preview_type` enum('image','audio','video') NOT NULL DEFAULT 'image',
  `is_free_product` enum('Yes','No') NOT NULL DEFAULT 'No',
  `last_updated_date` datetime NOT NULL,
  `total_downloads` bigint(20) NOT NULL,
  `product_moreinfo_url` varchar(255) NOT NULL,
  `global_transaction_fee_used` enum('Yes','No') NOT NULL DEFAULT 'No',
  `site_transaction_fee_type` enum('Flat','Percentage','Mix') NOT NULL DEFAULT 'Flat',
  `site_transaction_fee` double(10,2) NOT NULL,
  `site_transaction_fee_percent` double(10,2) NOT NULL,
  `is_downloadable_product` enum('Yes','No') NOT NULL DEFAULT 'Yes',
  `user_section_id` bigint(20) NOT NULL,
  `delivery_days` bigint(20) NOT NULL,
  `date_expires` datetime NOT NULL,
  `default_orig_img_width` int(11) NOT NULL,
  `default_orig_img_height` int(11) NOT NULL,
  `product_status` enum('Draft','Ok','Deleted','ToActivate','NotApproved') NOT NULL DEFAULT 'ToActivate',
  `use_cancellation_policy` enum('Yes','No') DEFAULT 'Yes',
  `use_default_cancellation` enum('Yes','No') DEFAULT 'No',
  `cancellation_policy_text` longtext,
  `cancellation_policy_filename` varchar(255) DEFAULT NULL,
  `cancellation_policy_filetype` varchar(100) DEFAULT NULL,
  `cancellation_policy_server_url` varchar(255) DEFAULT NULL,
  `shipping_from_country` int(11) DEFAULT NULL COMMENT 'Shipping From Country to Caclulate the shipping fee for statndard method',
  `shipping_from_zip_code` varchar(20) DEFAULT NULL,
  `shipping_template` int(11) NOT NULL DEFAULT '0',
  `variation_group_id` bigint(20) NOT NULL DEFAULT '0',
  `accept_giftwrap` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'if this set as 1 as then gift wrap will be accept for this item',
  `accept_giftwrap_message` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'if this set as 1 as then gift wrap message will be accept for this item',
  `giftwrap_type` enum('single','bulk') NOT NULL COMMENT 'if single, giftwrap price will be calculated for the no of items ordered. if bulk, giftwrap price will be the same irrespective of the qty ordered ',
  `giftwrap_pricing` decimal(15,2) NOT NULL DEFAULT '0.00',
  `use_variation` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'if this set as 1 as then this item will use variations',
  PRIMARY KEY (`id`),
  KEY `Ind_product_code_status` (`product_code`,`product_status`),
  KEY `Ind_product_status_id` (`product_status`,`id`),
  KEY `Ind_status_activated_date` (`product_status`,`date_activated`),
  KEY `Ind_user_section_id` (`user_section_id`),
  KEY `Ind_product_user_id` (`product_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- --------------------------------------------------------

--
-- Table structure for table `product_category`
--

DROP TABLE IF EXISTS `product_category`;
CREATE TABLE `product_category` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `seo_category_name` varchar(255) NOT NULL,
  `category_name` varchar(250) NOT NULL,
  `category_description` varchar(250) NOT NULL,
  `category_meta_title` text NOT NULL,
  `category_meta_description` text,
  `category_meta_keyword` text NOT NULL,
  `category_level` tinyint(4) NOT NULL COMMENT 'To store the level, 1 stands for the top level category ',
  `parent_category_id` bigint(20) NOT NULL COMMENT 'category_id of the category which is the parent for this category, 0 for level 1 category',
  `category_left` bigint(20) NOT NULL COMMENT 'cgories with lft and rgt values in between this ones are descendants',
  `category_right` bigint(20) NOT NULL COMMENT 'cgories with lft and rgt values in between this ones are descendants',
  `date_added` date NOT NULL,
  `display_order` bigint(20) NOT NULL COMMENT 'Order in which the categories must be displayed in the front end in the listing',
  `available_sort_options` varchar(250) NOT NULL DEFAULT 'all' COMMENT 'Available sorting options saved as a string from array separated by '','' ',
  `image_name` varchar(200) NOT NULL,
  `image_ext` varchar(10) NOT NULL,
  `image_width` int(11) NOT NULL,
  `image_height` int(11) NOT NULL,
  `is_featured_category` enum('Yes','No') NOT NULL DEFAULT 'No',
  `use_parent_meta_detail` enum('Yes','No') NOT NULL DEFAULT 'No' COMMENT 'Use parent category meta details or not',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `SEO_PARENT` (`seo_category_name`,`parent_category_id`),
  KEY `Ind_category_left` (`category_left`),
  KEY `Ind_parent_category_id_left` (`parent_category_id`,`category_left`),
  KEY `Ind_category_level_left` (`category_level`,`category_left`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shop_details`
--

DROP TABLE IF EXISTS `shop_details`;
CREATE TABLE `shop_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `shop_name` varchar(150) NOT NULL,
  `url_slug` varchar(150) NOT NULL,
  `shop_slogan` varchar(200) NOT NULL,
  `shop_desc` text NOT NULL,
  `shop_address1` varchar(150) NOT NULL,
  `shop_address2` varchar(150) NOT NULL,
  `shop_city` varchar(50) NOT NULL,
  `shop_state` varchar(50) NOT NULL,
  `shop_zipcode` varchar(15) NOT NULL,
  `shop_country` varchar(10) NOT NULL,
  `shop_message` text NOT NULL,
  `shop_contactinfo` text NOT NULL,
  `image_name` varchar(100) NOT NULL,
  `image_ext` varchar(10) NOT NULL,
  `image_server_url` varchar(255) NOT NULL,
  `t_height` varchar(15) NOT NULL,
  `t_width` varchar(15) NOT NULL,
  `is_featured_shop` enum('Yes','No') NOT NULL DEFAULT 'No',
  `cancellation_policy_text` text,
  `cancellation_policy_filename` varchar(255) DEFAULT NULL,
  `cancellation_policy_filetype` varchar(10) DEFAULT NULL,
  `cancellation_policy_server_url` varchar(255) DEFAULT NULL,
  `policy_welcome` text,
  `policy_payment` text,
  `policy_shipping` text,
  `policy_refund_exchange` text,
  `policy_faq` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `Ind_url_slug` (`url_slug`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_product_section`
--

DROP TABLE IF EXISTS `user_product_section`;
CREATE TABLE `user_product_section` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `section_name` varchar(150) NOT NULL,
  `date_added` datetime NOT NULL,
  `status` enum('Yes','No') NOT NULL DEFAULT 'Yes',
  PRIMARY KEY (`id`),
  KEY `Ind_user_id_id` (`user_id`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `product_category_attributes`
--

DROP TABLE IF EXISTS `product_category_attributes`;
CREATE TABLE `product_category_attributes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `attribute_id` bigint(20) NOT NULL,
  `category_id` bigint(20) NOT NULL,
  `date_added` date NOT NULL,
  `display_order` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Ind_category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product_image`
--

DROP TABLE IF EXISTS `product_image`;
CREATE TABLE `product_image` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) NOT NULL,
  `thumbnail_title` varchar(250) NOT NULL,
  `thumbnail_img` varchar(150) NOT NULL,
  `thumbnail_ext` varchar(4) NOT NULL,
  `thumbnail_width` int(11) NOT NULL,
  `thumbnail_height` int(11) NOT NULL,
  `thumbnail_s_width` int(11) NOT NULL DEFAULT '0',
  `thumbnail_s_height` int(11) NOT NULL DEFAULT '0',
  `thumbnail_t_width` int(11) NOT NULL DEFAULT '0',
  `thumbnail_t_height` int(11) NOT NULL DEFAULT '0',
  `thumbnail_l_width` int(11) NOT NULL DEFAULT '0',
  `thumbnail_l_height` int(11) NOT NULL DEFAULT '0',
  `default_title` varchar(250) NOT NULL,
  `default_img` varchar(150) NOT NULL,
  `default_ext` varchar(4) NOT NULL,
  `default_width` int(11) NOT NULL,
  `default_height` int(11) NOT NULL,
  `default_s_width` int(11) NOT NULL,
  `default_s_height` int(11) NOT NULL,
  `default_t_width` int(11) NOT NULL DEFAULT '0',
  `default_t_height` int(11) NOT NULL DEFAULT '0',
  `default_l_width` int(11) NOT NULL DEFAULT '0',
  `default_l_height` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `Ind_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product_attributes_values`
--

DROP TABLE IF EXISTS `product_attributes_values`;
CREATE TABLE `product_attributes_values` (
  `product_id` bigint(20) NOT NULL,
  `attribute_id` bigint(20) NOT NULL,
  `attribute_value` text NOT NULL,
  PRIMARY KEY (`product_id`,`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product_resource`
--

DROP TABLE IF EXISTS `product_resource`;
CREATE TABLE `product_resource` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) NOT NULL,
  `resource_type` enum('Archive','Audio','Video','Image','Other') NOT NULL DEFAULT 'Other',
  `is_downloadable` enum('Yes','No') NOT NULL DEFAULT 'No',
  `filename` varchar(150) NOT NULL,
  `ext` varchar(10) NOT NULL,
  `title` varchar(150) NOT NULL,
  `default_flag` enum('Yes','No') NOT NULL DEFAULT 'No',
  `server_url` varchar(255) NOT NULL,
  `display_order` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `l_width` int(11) NOT NULL,
  `l_height` int(11) NOT NULL,
  `t_width` int(11) NOT NULL,
  `t_height` int(11) NOT NULL,
  `s_width` int(11) NOT NULL DEFAULT '0',
  `s_height` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `Ind_product_display_order` (`product_id`,`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `product_log`
--

DROP TABLE IF EXISTS `product_log`;
CREATE TABLE `product_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) NOT NULL,
  `date_added` datetime NOT NULL,
  `added_by` enum('User','Admin','Staff') NOT NULL DEFAULT 'User',
  `user_id` bigint(20) NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `product_attributes_option_values`
--

DROP TABLE IF EXISTS `product_attributes_option_values`;
CREATE TABLE `product_attributes_option_values` (
  `product_id` bigint(20) NOT NULL,
  `attribute_id` bigint(20) NOT NULL,
  `attribute_options_id` bigint(20) NOT NULL,
  KEY `product_id` (`product_id`,`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product_attributes`
--

DROP TABLE IF EXISTS `product_attributes`;
CREATE TABLE `product_attributes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `attribute_label` varchar(250) NOT NULL,
  `attribute_help_tip` varchar(250) NOT NULL,
  `attribute_question_type` enum('text','textarea','select','check','option','multiselectlist') NOT NULL DEFAULT 'text',
  `default_value` text NOT NULL,
  `validation_rules` varchar(250) NOT NULL,
  `date_added` date NOT NULL,
  `is_searchable` enum('yes','no') NOT NULL DEFAULT 'no',
  `show_in_list` enum('yes','no') NOT NULL DEFAULT 'yes',
  `description` varchar(250) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `product_attribute_options`
--

DROP TABLE IF EXISTS `product_attribute_options`;
CREATE TABLE `product_attribute_options` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `attribute_id` bigint(20) NOT NULL,
  `option_label` varchar(150) NOT NULL,
  `option_value` varchar(150) NOT NULL,
  `is_default_option` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `currency_exchange_rate`
--

DROP TABLE IF EXISTS `currency_exchange_rate`;
CREATE TABLE `currency_exchange_rate` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `country` varchar(250) NOT NULL,
  `country_code` varchar(10) NOT NULL,
  `iso2_country_code` varchar(2) NOT NULL,
  `currency_code` varchar(10) NOT NULL,
  `currency_symbol` varchar(25) NOT NULL,
  `currency_name` varchar(250) NOT NULL,
  `exchange_rate` varchar(20) NOT NULL,
  `exchange_rate_static` varchar(20) NOT NULL,
  `status` enum('Active','InActive') NOT NULL DEFAULT 'Active',
  `paypal_supported` enum('Yes','No') NOT NULL DEFAULT 'No',
  `display_currency` enum('Yes','No') NOT NULL DEFAULT 'No',
  `country_name_chinese` varchar(255) NOT NULL,
  `china_post_group` enum('0','1','2','3','4') NOT NULL DEFAULT '0' COMMENT '0-Unknown Group,1-First group, 2-Second Group, 3-Third Group,4-Fourth Group',
  `geo_location_id` int(11) DEFAULT NULL,
  `zone_id` int(11) DEFAULT NULL,
  `capital` varchar(225) NOT NULL,
  `zip_code` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Ind_country_code` (`country_code`),
  KEY `Ind_country` (`country`),
  KEY `Ind_country_id` (`country`,`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_shop_details`
--

DROP TABLE IF EXISTS `users_shop_details`;
CREATE TABLE `users_shop_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `is_shop_owner` enum('Yes','No') NOT NULL DEFAULT 'No',
  `shop_status` tinyint(1) NOT NULL DEFAULT '1',
  `total_products` bigint(20) NOT NULL,
  `paypal_id` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Ind_user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



-- --------------------------------------------------------

--
-- Table structure for table `api_exclude_tags`
--

DROP TABLE IF EXISTS `api_exclude_tags`;
CREATE TABLE `api_exclude_tags` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tags` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `addresses`;
CREATE TABLE `addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `address_line1` varchar(255) DEFAULT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `zip_code` varchar(50) NOT NULL,
  `phone_no` varchar(225) NOT NULL,
  `country_id` int(11) DEFAULT NULL,
  `address_type` enum('shipping','billing') DEFAULT 'shipping',
  `is_primary` enum('No','Yes') DEFAULT 'No',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `billing_address`;
CREATE TABLE `billing_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL,
  `billing_address_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `shipping_address` text,
  `billing_address` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Ind_order_id` (`order_id`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `shipping_fees`;
CREATE TABLE `shipping_fees` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country_id` int(10) unsigned NOT NULL,
  `shipping_fee` double NOT NULL,
  `foreign_id` double NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `countries`;
CREATE TABLE `countries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country_slug` varchar(255) NOT NULL,
  `country_name` varchar(255) NOT NULL,
  `permissions` text,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `countries_country_slug_unique` (`country_slug`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `taxations`;
CREATE TABLE IF NOT EXISTS `taxations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `tax_name` varchar(255) DEFAULT NULL,
  `tax_slug` varchar(255) DEFAULT NULL,
  `tax_description` mediumtext,
  `tax_fee` decimal(10, 2) DEFAULT NULL,
  `fee_type` enum('percentage','flat') DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Ind_deleted_at_id` (`deleted_at`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `product_taxations`;
CREATE TABLE `product_taxations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `taxation_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `tax_fee` float DEFAULT NULL,
  `fee_type` enum('percentage','flat') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `shop_order`
--

DROP TABLE IF EXISTS `shop_order`;
CREATE TABLE `shop_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `buyer_id` int(11) DEFAULT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `sub_total` double DEFAULT NULL,
  `discount_amount` double DEFAULT NULL,
  `total_amount` double DEFAULT NULL COMMENT 'This amount is included site_commission',
  `coupon_code` varchar(255) DEFAULT NULL,
  `tax_fee` double DEFAULT NULL,
  `shipping_fee` double DEFAULT NULL,
  `site_commission` double DEFAULT NULL COMMENT 'This is is site commission amount for all item',
  `currency` varchar(100) DEFAULT NULL,
  `order_status` enum('draft','pending_payment','payment_completed','refund_requested','refund_completed','refund_rejected','not_paid','payment_cancelled') DEFAULT 'draft',
  `set_as_paid_notes` varchar(225) NOT NULL,
  `set_as_paid_transaction_type` enum('pay_pal','credit','others') DEFAULT NULL,
  `set_as_paid_amount` double DEFAULT NULL,
  `shipping_tracking_id` int(11) DEFAULT NULL,
  `pay_key` varchar(255) DEFAULT NULL,
  `tracking_id` varchar(255) DEFAULT NULL,
  `payment_status` varchar(255) DEFAULT NULL,
  `payment_response` text,
  `date_created` datetime DEFAULT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `payment_gateway_type` varchar(255) DEFAULT NULL,
  `set_as_delivered` enum('yes','no') NOT NULL DEFAULT 'no',
  `delivered_date` datetime NOT NULL,
  `deal_tipping_status` enum('','pending_tipping','tipping_reached','tipping_failed') NOT NULL COMMENT 'Deal tipping status if applicable for this invoice.',
  `giftwrap_price` double(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Ind_buyer_id` (`buyer_id`),
  KEY `Ind_seller_id` (`seller_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- --------------------------------------------------

--
-- Table structure for table `shop_order_item`
--

DROP TABLE IF EXISTS `shop_order_item`;
CREATE TABLE `shop_order_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `buyer_id` int(11) DEFAULT NULL,
  `item_owner_id` int(11) DEFAULT NULL,
  `item_amount` double DEFAULT NULL,
  `item_qty` int(11) NOT NULL,
  `shipping_company` int(11) NOT NULL DEFAULT '0',
  `shipping_fee` double DEFAULT NULL,
  `total_tax_amount` double DEFAULT NULL,
  `tax_ids` varchar(255) DEFAULT NULL,
  `tax_amounts` varchar(255) DEFAULT NULL,
  `services_amount` double DEFAULT NULL,
  `total_amount` double DEFAULT NULL,
  `discount_ratio_amount` double DEFAULT NULL,
  `service_ids` varchar(255) DEFAULT NULL,
  `item_type` enum('product') DEFAULT 'product',
  `site_commission` double DEFAULT NULL,
  `seller_amount` double DEFAULT NULL,
  `shipping_tracking_id` varchar(255) DEFAULT NULL,
  `shipping_stock_country` int(11) DEFAULT NULL,
  `shipping_serial_number` text NOT NULL,
  `shipping_company_name` varchar(50) DEFAULT NULL,
  `shipping_status` enum('not_shipping','shipped','delivered') NOT NULL DEFAULT 'not_shipping',
  `shipping_date` datetime NOT NULL,
  `delivered_date` datetime DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `deal_id` bigint(20) NOT NULL DEFAULT '0' COMMENT 'Store deal id if applicable fo this item',
  `deal_tipping_status` enum('','pending_tipping','tipping_reached','tipping_failed') NOT NULL COMMENT 'Deal tipping status if applicable for this item.',
  `matrix_id` bigint(20) NOT NULL DEFAULT '0' COMMENT 'Matrix id of the item, if set as 9 then no variations available for the item',
  `is_use_giftwrap` tinyint(1) NOT NULL DEFAULT '0',
  `giftwrap_price` double(15,2) NOT NULL,
  `giftwrap_price_per_qty` double(15,2) NOT NULL,
  `giftwrap_msg` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Ind_order_id` (`order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `order_receivers`
--

DROP TABLE IF EXISTS `order_receivers`;
CREATE TABLE `order_receivers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_invoice_id` int(11) DEFAULT NULL,
  `buyer_id` int(11) DEFAULT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `is_admin` enum('No','Yes') DEFAULT 'No',
  `receiver_paypal_email` varchar(255) DEFAULT NULL,
  `status` varchar(100) DEFAULT 'draft',
  `txn_id` varchar(255) DEFAULT NULL,
  `pay_key` varchar(255) DEFAULT NULL,
  `is_refunded` enum('No','Yes') DEFAULT 'No',
  `refunded_amount` double DEFAULT NULL,
  `payment_type` enum('Paypal','Dummy','Wallet') DEFAULT 'Paypal',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
CREATE TABLE `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `buyer_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `item_owner_id` int(11) DEFAULT NULL,
  `order_item_id` int(11) DEFAULT NULL,
  `order_receiver_id` int(11) DEFAULT NULL,
  `invoice_status` enum('pending','completed','refund_requested','refunded','refund_rejected') DEFAULT 'completed',
  `is_refund_requested` enum('No','Yes') DEFAULT 'No',
  `refund_reason` text,
  `is_refund_approved_by_admin` enum('No','Yes','Rejected') DEFAULT 'No',
  `refund_response_by_admin` text,
  `refund_response_to_user_by_admin` text,
  `refunded_amount_by_admin` double DEFAULT NULL,
  `refund_responded_by_admin_id` int(11) DEFAULT NULL,
  `is_refund_approved_by_seller` enum('No','Yes','Rejected') DEFAULT 'No',
  `refund_response_by_seller` text,
  `refund_amount_by_seller` double DEFAULT NULL,
  `refund_paypal_amount_by_seller` double DEFAULT NULL,
  `refund_status` varchar(255) DEFAULT NULL,
  `txn_id` varchar(255) DEFAULT NULL,
  `pay_key` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Ind_order_id` (`order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_cart_shipping_address`
--

DROP TABLE IF EXISTS `user_cart_shipping_address`;
CREATE TABLE `user_cart_shipping_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(100) DEFAULT NULL,
  `shipping_address_id` int(11) DEFAULT NULL,
  `billing_address_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product_stocks`
--

DROP TABLE IF EXISTS `product_stocks`;
CREATE TABLE `product_stocks` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) NOT NULL,
  `stock_country_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `serial_numbers` text NOT NULL,
  `date_updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Ind_product_stock` (`product_id`,`stock_country_id`),
  KEY `Ind_product_id_date_updated` (`product_id`,`date_updated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `cancellation_policy`
--

DROP TABLE IF EXISTS `cancellation_policy`;
CREATE TABLE `cancellation_policy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `cancellation_policy_text` text,
  `cancellation_policy_filename` varchar(255) DEFAULT NULL,
  `cancellation_policy_filetype` varchar(10) DEFAULT NULL,
  `cancellation_policy_server_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `product_price_groups`
--

DROP TABLE IF EXISTS `product_price_groups`;
CREATE TABLE IF NOT EXISTS `product_price_groups` (
  `price_group_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `range_start` int(11) NOT NULL,
  `range_end` int(11) NOT NULL COMMENT 'If ''-1'', unlimitted',
  `currency` varchar(5) NOT NULL DEFAULT 'USD',
  `price` double(10,2) NOT NULL,
  `price_usd` double(10,2) NOT NULL,
  `discount_percentage` double(10,2) NOT NULL,
  `discount` double(10,2) NOT NULL,
  `discount_usd` double NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`price_group_id`),
  KEY `product_id` (`product_id`,`group_id`),
  KEY `price_usd` (`price_usd`),
  KEY `discount_usd` (`discount_usd`),
  KEY `Ind_product_group` (`product_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='To store product price based on groups';


-- --------------------------------------------------------

--
-- Table structure for table `temp_table_log`
--

DROP TABLE IF EXISTS `temp_table_log`;
CREATE TABLE `temp_table_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `temp_table_name` varchar(100) NOT NULL,
  `date_used` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product_price_groups_order`
--

DROP TABLE IF EXISTS `product_price_groups_orders`;
CREATE TABLE `product_price_groups_orders` (
  `price_group_order_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `range_start` int(11) NOT NULL,
  `range_end` int(11) NOT NULL COMMENT 'If ''-1'', unlimitted',
  `currency` varchar(5) NOT NULL DEFAULT 'USD',
  `price` float(8,2) NOT NULL,
  `price_usd` float(8,2) NOT NULL,
  `discount_percentage` float(4,2) NOT NULL,
  `discount` float(8,2) NOT NULL,
  `discount_usd` float(8,2) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`price_group_order_id`),
  KEY `product_id` (`product_id`,`group_id`),
  KEY `price_usd` (`price_usd`),
  KEY `discount_usd` (`discount_usd`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='To log product price based on groups for order';

-- --------------------------------------------------------

--
-- Table structure for table `common_invoice`
--

DROP TABLE IF EXISTS `common_invoice`;
CREATE TABLE IF NOT EXISTS `common_invoice` (
  `common_invoice_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `reference_type` enum('Products','Credits','Usercredits') NOT NULL DEFAULT 'Products',
  `reference_id` int(11) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `amount` decimal(20,2) NOT NULL,
  `is_credit_payment` enum('Yes','No') NOT NULL DEFAULT 'No',
  `paypal_amount` decimal(20,2) NOT NULL,
  `pay_key` varchar(255) NOT NULL,
  `tracking_id` varchar(255) NOT NULL,
  `status` enum('Paid','Unpaid','Draft','Cancelled') NOT NULL DEFAULT 'Unpaid',
  `date_paid` datetime NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`common_invoice_id`),
  KEY `Ind_user_id` (`user_id`,`status`,`common_invoice_id`),
  KEY `Ind_reference` (`reference_id`,`reference_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Maintain invoice status for credits and products';


-- --------------------------------------------------------

--
-- Table structure for table `credits_log`
--

DROP TABLE IF EXISTS `credits_log`;
CREATE TABLE `credits_log` (
  `credit_id` int(11) NOT NULL AUTO_INCREMENT,
  `credit_type` enum('credits','user_credits') NOT NULL DEFAULT 'credits' COMMENT 'credits - credits added by admin for user, user_credits - credits added by user via paypal',
  `currency` varchar(10) NOT NULL,
  `amount` decimal(20,2) NOT NULL,
  `credited_by` int(11) NOT NULL,
  `credited_to` int(11) NOT NULL,
  `admin_notes` text NOT NULL,
  `user_notes` text NOT NULL,
  `paid` enum('Yes','No') NOT NULL DEFAULT 'No',
  `date_paid` datetime NOT NULL,
  `generate_invoice` enum('Yes','No') NOT NULL DEFAULT 'No',
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  PRIMARY KEY (`credit_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `geographic_location`
--

DROP TABLE IF EXISTS `geographic_location`;
CREATE TABLE `geographic_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shipping_companies`
--

DROP TABLE IF EXISTS `shipping_companies`;
CREATE TABLE IF NOT EXISTS `shipping_companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(50) DEFAULT NULL,
  `service_code` varbinary(100) DEFAULT NULL,
  `category` enum('1','2','3','4') DEFAULT NULL COMMENT '1=>post service, 2=>Express, 3=>Special line, 4=>Others',
  `is_custom_fee_available` enum('1','0') DEFAULT NULL COMMENT 'Check is custom fee type available',
  `is_standard_fee_available` enum('1','0') DEFAULT NULL COMMENT 'Check is standard fee type available or not',
  `is_custom_delivery_available` enum('1','0') DEFAULT NULL COMMENT 'Check is custom delivery address available or not',
  `default_delivery_days` int(11) DEFAULT NULL,
  `display` enum('1','0') DEFAULT NULL COMMENT 'Whether or not to display in list',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shipping_templates`
--

DROP TABLE IF EXISTS `shipping_templates`;
CREATE TABLE `shipping_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `template_name` varchar(255) DEFAULT NULL,
  `is_default` enum('1','0') DEFAULT NULL,
  `status` enum('Active','InActive') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Ind_user_id_id` (`user_id`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `shipping_template_companies`
--

DROP TABLE IF EXISTS `shipping_template_companies`;
CREATE TABLE `shipping_template_companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `fee_type` enum('1','2','3') DEFAULT NULL COMMENT '1 => custom, 2 => standard, 3 => custom',
  `fee_discount` float DEFAULT NULL,
  `delivery_type` enum('1','2') DEFAULT NULL COMMENT '1 =? custom, 2 => default',
  `days` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Ind_temp_comp` (`template_id`,`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- --------------------------------------------------------

--
-- Table structure for table `shipping_template_fee_custom`
--

DROP TABLE IF EXISTS `shipping_template_fee_custom`;
CREATE TABLE `shipping_template_fee_custom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `template_company_id` int(11) DEFAULT NULL,
  `country_selected_type` varchar(255) DEFAULT NULL,
  `shipping_setting` enum('ship_to','dont_ship_to') DEFAULT NULL,
  `fee_type` enum('1','2','3') DEFAULT NULL,
  `discount` float DEFAULT NULL,
  `custom_fee_type` enum('1','2') DEFAULT NULL COMMENT '1 => ''Friight cost by Quantity'', ''2'' => ''Freight cost by Weight''',
  `min_order` int(11) DEFAULT NULL,
  `max_order` int(11) DEFAULT NULL,
  `cost_base_weight` float DEFAULT NULL,
  `extra_units` int(11) DEFAULT NULL,
  `extra_costs` float DEFAULT NULL,
  `initial_weight` float DEFAULT NULL,
  `initial_weight_price` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shipping_template_fee_custom_countries`
--

DROP TABLE IF EXISTS `shipping_template_fee_custom_countries`;
CREATE TABLE `shipping_template_fee_custom_countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `template_company_id` int(11) DEFAULT NULL,
  `shipping_template_fee_custom_id` int(11) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product_package_details`
--


DROP TABLE IF EXISTS `product_package_details`;
CREATE TABLE `product_package_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `weight` decimal(5,2) NOT NULL,
  `length` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `custom` enum('Yes','No') NOT NULL DEFAULT 'No',
  `first_qty` int(11) NOT NULL,
  `additional_qty` int(11) NOT NULL,
  `additional_weight` decimal(5,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Ind_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `shipping_template_delivery_custom`
--

DROP TABLE IF EXISTS `shipping_template_delivery_custom`;
CREATE TABLE `shipping_template_delivery_custom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `template_company_id` int(11) DEFAULT NULL,
  `country_selected_type` varchar(255) DEFAULT NULL,
  `days` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shipping_template_delivery_custom_countries`
--

DROP TABLE IF EXISTS `shipping_template_delivery_custom_countries`;
CREATE TABLE `shipping_template_delivery_custom_countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `template_company_id` int(11) DEFAULT NULL,
  `template_company_delivery_custom_id` int(11) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `shippping_template_fee_custom_weight`
--
DROP TABLE IF EXISTS `shippping_template_fee_custom_weight`;
CREATE TABLE `shippping_template_fee_custom_weight` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `template_company_id` int(11) DEFAULT NULL,
  `shipping_template_fee_custom_id` int(11) DEFAULT NULL,
  `weight_from` int(11) DEFAULT NULL,
  `weight_to` int(11) DEFAULT NULL,
  `additional_weight` int(11) DEFAULT NULL,
  `additional_weight_price` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dhl_zone_additional_price`
--

DROP TABLE IF EXISTS `dhl_zone_additional_price`;
CREATE TABLE `dhl_zone_additional_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_type` enum('pak_express_9','pak_express_12','pak_express_worldwide','pak_express_easy','china_express_9','china_express_12','china_express_worldwide') NOT NULL,
  `goods_weight_from` float NOT NULL COMMENT 'Goods weight in kg',
  `goods_weight_to` float NOT NULL COMMENT 'Goods wetght in kg',
  `zone1` decimal(15,2) NOT NULL,
  `zone2` decimal(15,2) NOT NULL,
  `zone3` decimal(15,2) NOT NULL,
  `zone4` decimal(15,2) NOT NULL,
  `zone5` decimal(15,2) NOT NULL,
  `zone6` decimal(15,2) NOT NULL,
  `zone7` decimal(15,2) NOT NULL,
  `zone8` decimal(15,2) NOT NULL,
  `zone9` decimal(15,2) NOT NULL,
  `zone10` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dhl_zone_price`
--

DROP TABLE IF EXISTS `dhl_zone_price`;
CREATE TABLE `dhl_zone_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_type` enum('pak_express_9','pak_express_12','pak_express_worldwide','pak_express_easy','china_express_9','china_express_12','china_express_worldwide') NOT NULL,
  `goods_weight` float NOT NULL COMMENT 'Goods weight in kg',
  `zone1` decimal(15,2) NOT NULL,
  `zone2` decimal(15,2) NOT NULL,
  `zone3` decimal(15,2) NOT NULL,
  `zone4` decimal(15,2) NOT NULL,
  `zone5` decimal(15,2) NOT NULL,
  `zone6` decimal(15,2) NOT NULL,
  `zone7` decimal(15,2) NOT NULL,
  `zone8` decimal(15,2) NOT NULL,
  `zone9` decimal(15,2) NOT NULL,
  `zone10` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dhl_zone_service`
--

DROP TABLE IF EXISTS `dhl_zone_service`;
CREATE TABLE `dhl_zone_service` (
  `zone_service_id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) NOT NULL,
  `country_name` varchar(255) NOT NULL,
  `dhl_pak_zone_id` int(11) NOT NULL DEFAULT '0',
  `dhl_pak_express_9` enum('0','1') NOT NULL DEFAULT '0',
  `dhl_pak_express_12` enum('0','1') NOT NULL DEFAULT '0',
  `dhl_pak_express_worldwide` enum('0','1') NOT NULL DEFAULT '0',
  `dhl_pak_express_easy` enum('0','1') NOT NULL DEFAULT '0',
  `dhl_china_zone_id` int(11) NOT NULL DEFAULT '0',
  `dhl_china_express_9` enum('0','1') NOT NULL DEFAULT '0',
  `dhl_china_express_12` enum('0','1') NOT NULL DEFAULT '0',
  `dhl_china_express_worldwide` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`zone_service_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `china_post_group_price`
--

DROP TABLE IF EXISTS `china_post_group_price`;
CREATE TABLE `china_post_group_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `china_post_category` enum('small_packet','m_bag') NOT NULL DEFAULT 'm_bag',
  `china_post_group` int(11) NOT NULL COMMENT '1-First group, 2-Second Group, 3-Third Group,4-Fourth Group',
  `upto_weight` decimal(10,2) NOT NULL COMMENT 'Goods weight in Grams',
  `upto_weight_price` decimal(10,2) NOT NULL COMMENT 'Currency code CNY or RMB',
  `additional_weight` decimal(10,2) NOT NULL COMMENT 'Eg: Additional charge for each 100g or fraction thereof above 100g',
  `additional_weight_price` decimal(10,2) NOT NULL COMMENT 'Currency code CNY or RMB',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shipping_countries_aramex`
--

DROP TABLE IF EXISTS `shipping_countries_aramex`;
CREATE TABLE `shipping_countries_aramex` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shipping_countries_ctr_land_pickup`
--

DROP TABLE IF EXISTS `shipping_countries_ctr_land_pickup`;
CREATE TABLE `shipping_countries_ctr_land_pickup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shipping_countries_hongkong_post_air_mail`
--

DROP TABLE IF EXISTS `shipping_countries_hongkong_post_air_mail`;
CREATE TABLE `shipping_countries_hongkong_post_air_mail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shipping_countries_seller_shipping_method`
--

DROP TABLE IF EXISTS `shipping_countries_seller_shipping_method`;
CREATE TABLE `shipping_countries_seller_shipping_method` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shipping_countries_sf_express`
--

DROP TABLE IF EXISTS `shipping_countries_sf_express`;
CREATE TABLE `shipping_countries_sf_express` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shipping_countries_singapore_post`
--

DROP TABLE IF EXISTS `shipping_countries_singapore_post`;
CREATE TABLE `shipping_countries_singapore_post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shipping_countries_sweden_post`
--

DROP TABLE IF EXISTS `shipping_countries_sweden_post`;
CREATE TABLE `shipping_countries_sweden_post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shipping_countries_swiss_post`
--

DROP TABLE IF EXISTS `shipping_countries_swiss_post`;
CREATE TABLE `shipping_countries_swiss_post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `collections`
--

DROP TABLE IF EXISTS `collections`;
CREATE TABLE IF NOT EXISTS `collections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `collection_name` varchar(255) DEFAULT NULL,
  `collection_slug` varchar(255) DEFAULT NULL,
  `collection_description` text,
  `collection_access` enum('Public','Private') DEFAULT 'Public',
  `collection_status` enum('Active','InActive') DEFAULT 'Active',
  `featured_collection` enum('Yes','No') DEFAULT 'No',
  `total_views` int(11) DEFAULT '0',
  `total_comments` int(11) DEFAULT '0',
  `total_clicks` int(11) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Ind_collection_status_id` (`collection_status`,`id`),
  KEY ` Ind_collection_status_totalviewa` (`collection_status`,`total_views`),
  KEY `Ind_collection_status_totalcommentst` (`collection_status`,`total_comments`),
  KEY `Ind_collection_slug` (`collection_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `collection_products`
--

DROP TABLE IF EXISTS `collection_products`;
CREATE TABLE IF NOT EXISTS `collection_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `collection_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Ind_collection_id_order` (`collection_id`,`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
CREATE TABLE IF NOT EXISTS `coupons` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `coupon_code` varchar(25) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `price_restriction` enum('less_than','greater_than','equal_to','between','none') NOT NULL,
  `price_from` float(15,2) NOT NULL,
  `price_to` float(15,2) NOT NULL,
  `offer_type` enum('Flat','Percentage') NOT NULL,
  `offer_amount` float(15,2) NOT NULL,
  `status` enum('Active','InActive') NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `collection_comments`
--

DROP TABLE IF EXISTS `collection_comments`;
CREATE TABLE `collection_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `collection_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `comment` text,
  `status` enum('Active','InActive') DEFAULT 'Active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Ind_collection_id` (`collection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `users_circle`
--

DROP TABLE IF EXISTS `users_circle`;
CREATE TABLE IF NOT EXISTS `users_circle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `circle_user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Ind_circle_user_id` (`circle_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `product_favorites`
--

DROP TABLE IF EXISTS `product_favorites`;
CREATE TABLE `product_favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `list_id` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Ind_product_id` (`product_id`),
  KEY `Ind_user_id` (`user_id`,`id`),
  KEY `Ind_user_list_id` (`user_id`,`list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `shop_favorites`
--

DROP TABLE IF EXISTS `shop_favorites`;
CREATE TABLE IF NOT EXISTS `shop_favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `shop_id` int(11) DEFAULT NULL,
  `shop_user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Ind_shop_id` (`shop_id`),
  KEY `Ind_user_id` (`user_id`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- --------------------------------------------------------

--
-- Table structure for table `collection_favorites`
--

DROP TABLE IF EXISTS `collection_favorites`;
CREATE TABLE IF NOT EXISTS `collection_favorites` (
  `id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `collection_id` int(11) DEFAULT NULL,
  `collection_owner_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `collection_favorites`
--

DROP TABLE IF EXISTS `product_invoice_feedback`;
CREATE TABLE IF NOT EXISTS `product_invoice_feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `feedback_user_id` int(11) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `buyer_id` int(11) DEFAULT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `feedback_comment` text,
  `feedback_remarks` enum('Positive','Negative','Neutral') DEFAULT 'Neutral',
  `rating` double DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Ind_seller` (`seller_id`,`feedback_remarks`),
  KEY `Ind_seller_id` (`seller_id`,`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `social`
--
DROP TABLE IF EXISTS `social`;
CREATE TABLE `social` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) DEFAULT NULL,
  `service` VARCHAR(127) COLLATE utf8_unicode_ci NOT NULL,
  `uid` VARCHAR(127) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `social_service_uid_unique` (`service`,`uid`),
  UNIQUE KEY `social_user_id_service_unique` (`user_id`,`service`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `site_logo`
--

DROP TABLE IF EXISTS `site_logo`;
CREATE TABLE `site_logo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `logo_image_name` varchar(255) DEFAULT NULL,
  `logo_image_ext` varchar(10) DEFAULT NULL,
  `logo_width` int(11) DEFAULT NULL,
  `logo_height` int(11) DEFAULT NULL,
  `logo_server_url` varchar(255) DEFAULT NULL,
  `logo_type` enum('logo','favicon') DEFAULT 'logo',
  PRIMARY KEY (`id`),
  KEY `Ind_logo_type` (`logo_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `static_pages`
--

DROP TABLE IF EXISTS `static_pages`;
CREATE TABLE `static_pages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `page_name` varchar(100) NOT NULL,
  `url_slug` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` blob,
  `main_page_content` longtext,
  `display_in` enum('Root','Member','Both') NOT NULL DEFAULT 'Both',
  `display_in_footer` enum('Yes','No') NOT NULL DEFAULT 'Yes',
  `status` enum('Active','Inactive','Main') NOT NULL DEFAULT 'Inactive',
  `page_display_order` int(11) NOT NULL COMMENT 'Static page link display order',
  `page_type` enum('static','external') NOT NULL,
  `external_link` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Ind_static_page` (`display_in_footer`,`status`,`page_display_order`),
  KEY `Ind_status_page_disp_order` (`status`,`page_display_order`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



-- --------------------------------------------------------

--
-- Table structure for table `seller_request`
--

DROP TABLE IF EXISTS `seller_request`;
CREATE TABLE IF NOT EXISTS `seller_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `request_message` text,
  `reply_sent` enum('No','Yes') DEFAULT 'No',
  `reply_message` text,
  `request_status` enum('NewRequest','Allowed','ConsiderLater','Rejected') DEFAULT 'NewRequest',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `reported_products`
--

DROP TABLE IF EXISTS `reported_products`;
CREATE TABLE IF NOT EXISTS `reported_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `report_thread` varchar(255) DEFAULT NULL COMMENT 'One or more from ''DifferThanAd'',''IncorrectData'',''Prohibited'',''InaccurateCategory''',
  `custom_message` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `banner_images`
--

DROP TABLE IF EXISTS `banner_images`;
CREATE TABLE `banner_images` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `filename` varchar(100) NOT NULL,
  `ext` varchar(4) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `large_width` int(11) NOT NULL,
  `large_height` int(11) NOT NULL,
  `server_url` varchar(255) NOT NULL,
  `display` enum('0','1') NOT NULL DEFAULT '1',
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Ind_display` (`display`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_favorites_products`
--

DROP TABLE IF EXISTS `users_favorites_products`;
CREATE TABLE `users_favorites_products` (
  `favorite_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`favorite_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_featured`
--

DROP TABLE IF EXISTS `users_featured`;
CREATE TABLE `users_featured` (
  `featured_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `date_from` datetime NOT NULL,
  `date_to` datetime NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`featured_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_top_picks`
--

DROP TABLE IF EXISTS `users_top_picks`;
CREATE TABLE `users_top_picks` (
  `top_pick_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`top_pick_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sell_page_static_content`
--

DROP TABLE IF EXISTS `sell_page_static_content`;
CREATE TABLE IF NOT EXISTS `sell_page_static_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_title` text,
  `what_can_you_sell` text,
  `how_doest_it_work` text,
  `additional_title` text,
  `additional_content` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `newsletter`
--

DROP TABLE IF EXISTS `newsletter`;
CREATE TABLE IF NOT EXISTS `newsletter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` text,
  `message` text,
  `total_sent` int(11) DEFAULT '0',
  `status` enum('Pending','Started','Finished') DEFAULT 'Pending',
  `upto_user_id` int(11) DEFAULT '0',
  `search_filter` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



-- --------------------------------------------------------

--
-- Table structure for table `newsletter_users`
--

DROP TABLE IF EXISTS `newsletter_users`;
CREATE TABLE IF NOT EXISTS `newsletter_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `newsletter_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `subscriber_id` int(11) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `is_sent` enum('Yes','No') DEFAULT 'No',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UniqueEmailNewsletter` (`newsletter_id`,`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

DROP TABLE IF EXISTS `currencies`;
CREATE TABLE `currencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_code` varchar(10) NOT NULL,
  `currency_symbol` varchar(25) NOT NULL,
  `currency_name` varchar(250) NOT NULL,
  `exchange_rate` varchar(20) NOT NULL,
  `exchange_rate_static` varchar(20) NOT NULL,
  `paypal_supported` enum('Yes','No') NOT NULL,
  `display_currency` enum('Yes','No') NOT NULL,
  `status` enum('Active','InActive') NOT NULL DEFAULT 'Active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Ind_currency_status` (`currency_code`,`status`),
  KEY `Ind_display_currency` (`display_currency`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `product_comments`
--

DROP TABLE IF EXISTS `product_comments`;
CREATE TABLE IF NOT EXISTS `product_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `comment_id` int(11) DEFAULT NULL,
  `comments` text,
  `status` enum('Active','InActive') DEFAULT 'Active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product_favorites_list`
--

DROP TABLE IF EXISTS `product_favorites_list`;
CREATE TABLE `product_favorites_list` (
  `list_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `list_name` varchar(100) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`list_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `advertisement`
--

DROP TABLE IF EXISTS `advertisement`;
CREATE TABLE `advertisement` (
  `add_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `post_from` enum('User','Admin') NOT NULL DEFAULT 'Admin',
  `block` varchar(100) NOT NULL,
  `about` text,
  `source` longtext,
  `start_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `allowed_impressions` bigint(20) NOT NULL DEFAULT '0',
  `completed_impressions` bigint(20) NOT NULL DEFAULT '0',
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`add_id`),
  KEY `post_from` (`post_from`),
  KEY `Ind_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscriber`
--

DROP TABLE IF EXISTS `newsletter_subscriber`;
CREATE TABLE `newsletter_subscriber` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `ip` varchar(16) NOT NULL,
  `date_added` date NOT NULL,
  `date_unsubscribed` date NOT NULL,
  `unsubscribe_code` varchar(255) NOT NULL,
  `status` enum('active','inactive','delete') NOT NULL DEFAULT 'active',
  `first_name` varchar(150) NOT NULL,
  `last_name` varchar(150) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `mass_mail`
--

DROP TABLE IF EXISTS `mass_mail`;
CREATE TABLE `mass_mail` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `send_on` datetime NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` text,
  `from_email` varchar(100) NOT NULL,
  `from_name` varchar(200) NOT NULL,
  `reply_to_email` varchar(100) NOT NULL,
  `upto_user_id` bigint(20) DEFAULT NULL,
  `status` enum('pending','sent','progress','cancelled') DEFAULT NULL,
  `send_to` enum('all','newsletter','picked_user') DEFAULT 'all',
  `user_id` bigint(20) DEFAULT NULL,
  `send_to_user_status` enum('all','active','inactive') NOT NULL DEFAULT 'all',
  `offer_newsletter` enum('yes','no') NOT NULL DEFAULT 'no',
  `is_deleted` enum('0','1') NOT NULL DEFAULT '0',
  `repeat_every` int(10) NOT NULL,
  `repeat_for` int(10) NOT NULL,
  `reschedule_id` bigint(20) NOT NULL,
  `reschedule_times` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Ind_is_deleted_id` (`is_deleted`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mass_mail_users`
--

DROP TABLE IF EXISTS `mass_mail_users`;
CREATE TABLE `mass_mail_users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `mass_email_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mass_mail_sent_users`
--

DROP TABLE IF EXISTS `mass_mail_sent_users`;
CREATE TABLE `mass_mail_sent_users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `mass_email_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Ind_mass_email_id1` (`mass_email_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
CREATE TABLE `languages` (
  `languages_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'language name as displayed in  the  switcher',
  `code` char(5) NOT NULL COMMENT 'language code the same as that of the folder name ',
  `sort_order` int(11) DEFAULT NULL COMMENT 'sort order',
  `status` enum('Yes','No') NOT NULL DEFAULT 'Yes',
  `is_published` enum('Yes','No') NOT NULL DEFAULT 'No' COMMENT 'Yes - if it is published ',
  `is_translated` enum('Yes','No') NOT NULL DEFAULT 'No' COMMENT 'Yes - if it is translated ',
  PRIMARY KEY (`languages_id`),
  KEY `Ind_status` (`status`),
  KEY `Ind_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `meta_details`
--

DROP TABLE IF EXISTS `meta_details`;
CREATE TABLE IF NOT EXISTS `meta_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_name` varchar(255) NOT NULL,
  `meta_title` text CHARACTER SET utf8 NOT NULL,
  `meta_description` text CHARACTER SET utf8,
  `meta_keyword` text CHARACTER SET utf8,
  `common_terms` text NOT NULL,
  `language` varchar(20) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;