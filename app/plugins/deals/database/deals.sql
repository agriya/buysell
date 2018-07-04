-- --------------------------------------------------------

INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'deals', '1', 'Boolean', 'plugin', 'deals', 'Yes', 999, 'Allow deals plugin in the site', NOW());

DROP TABLE IF EXISTS `deal`;
CREATE TABLE IF NOT EXISTS `deal` (
  `deal_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `deal_title` varchar(255) NOT NULL,
  `url_slug` varchar(150) NOT NULL COMMENT 'used for seoURL',
  `deal_short_description` text NOT NULL,
  `deal_description` text NOT NULL,
  `meta_title` varchar(255) NOT NULL COMMENT 'title for meta tag',
  `meta_keyword` varchar(255) NOT NULL COMMENT 'keyword for meta tag',
  `meta_description` varchar(255) NOT NULL COMMENT 'description for meta tag',
  `img_name` varchar(100) NOT NULL,
  `img_ext` varchar(4) NOT NULL,
  `img_width` int(11) NOT NULL,
  `img_height` int(11) NOT NULL,
  `l_width` int(11) NOT NULL,
  `l_height` int(11) NOT NULL,
  `t_width` int(11) NOT NULL,
  `t_height` int(11) NOT NULL,
  `server_url` varchar(255) NOT NULL,
  `discount_percentage` decimal(15,2) NOT NULL,
  `date_deal_from` date NOT NULL,
  `date_deal_to` date NOT NULL,
  `applicable_for` enum('single_item','selected_items','all_items') NOT NULL,
  `tipping_qty_for_deal` int(11) NOT NULL DEFAULT '0',
  `deal_status` enum('to_activate','active','deactivated','expired','closed') NOT NULL,
  `date_added` datetime NOT NULL,
  `listing_fee_paid` enum('Yes','No') NOT NULL DEFAULT 'No',
  `deal_tipping_status` enum('','pending_tipping','tipping_reached','tipping_failed') NOT NULL COMMENT 'Deal tipping status if applicable for this deal.',
  `tipping_notified` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Tipping notification sent or not',
  PRIMARY KEY (`deal_id`),
  KEY `Ind_discount_percentage` (`discount_percentage`),
  KEY `Ind_status_percentage` (`deal_status`,`discount_percentage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `deal_item`;
CREATE TABLE IF NOT EXISTS `deal_item` (
  `deal_item_id` bigint(20) NOT NULL auto_increment,
  `deal_id` bigint(20) NOT NULL,
  `item_id` bigint(20) NOT NULL,
  PRIMARY KEY  (`deal_item_id`),
  KEY `Ind_deal_id` (`deal_id`),
  KEY `Ind_item_id` (`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `deal_featured_request`;
CREATE TABLE IF NOT EXISTS `deal_featured_request` (
  `request_id` bigint(20) NOT NULL auto_increment,
  `deal_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `date_featured_from` date NOT NULL,
  `date_featured_to` date NOT NULL,
  `deal_featured_days` int(11) NOT NULL,
  `fee_paid_status` enum('No','Yes') NOT NULL default 'No',
  `date_added` datetime NOT NULL,
  `date_approved_on` datetime NOT NULL,
  `request_status` enum('pending_for_approval','approved','un_approved') NOT NULL default 'pending_for_approval',
  `admin_comment` text NOT NULL,
  PRIMARY KEY  (`request_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `deal_featured`;
CREATE TABLE IF NOT EXISTS `deal_featured` (
  `deal_featured_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `deal_id` bigint(20) NOT NULL,
  `date_featured_from` date NOT NULL,
  `date_featured_to` date NOT NULL,
  `request_id` bigint(20) NOT NULL,
  PRIMARY KEY (`deal_featured_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `deal_item_purchased_details`;
CREATE TABLE IF NOT EXISTS `deal_item_purchased_details` (
  `deal_item_purchased_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `deal_id` bigint(20) NOT NULL,
  `item_id` bigint(20) NOT NULL,
  `order_id` bigint(20) NOT NULL,
  `qty` bigint(20) NOT NULL,
  `seller_amount_credited` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is seller received this order amount',
  `is_refund_processed` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is refund processed this order amount',
  PRIMARY KEY (`deal_item_purchased_id`),
  KEY `deal_id` (`deal_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'deal_highlighttext_max_chars', '500', 'Int', 'plugin', 'deals', 'Yes', 999, 'Deal short description max. character length', NOW());

INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'deal_expiring_soon_days', '10', 'Int', 'plugin', 'deals', 'Yes', 999, 'Deal expiring soon days', NOW());

INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'deal_auto_approval', '0', 'Boolean', 'plugin', 'deals', 'Yes', 999, 'Allow deal auto approval?', NOW());

INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'deal_listing_fee', '1', 'Int', 'plugin', 'deals', 'Yes', 999, 'Listing fee to feature the deal in $', NOW());

INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'deal_img_format_arr', 'jpg,jpeg,png,gif', 'String', 'plugin', 'deals', 'Yes', 999, 'Deal image allowed formats', NOW());

INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'deal_img_folder', 'files/deal_image/', 'String', 'plugin', 'deals', 'Yes', 999, 'Deal image folder path', NOW());

INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'deal_img_width', '450', 'Int', 'plugin', 'deals', 'Yes', 999, 'Deal image optimum width', NOW());

INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'deal_img_height', '350', 'Int', 'plugin', 'deals', 'Yes', 999, 'Deal image optimum height', NOW());

INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'deal_img_max_size', '5', 'Int', 'plugin', 'deals', 'Yes', 999, 'Maximum file size allowed for deal image (In KB)', NOW());

INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'deal_img_thumb_width', '170', 'Int', 'plugin', 'deals', 'Yes', 999, 'Deal thumbnail image width', NOW());

INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'deal_img_thumb_height', '170', 'Int', 'plugin', 'deals', 'Yes', 999, 'Deal thumbnail image height', NOW());

INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'deal_img_large_width', '446', 'Int', 'plugin', 'deals', 'Yes', 999, 'Deal image large width', NOW());

INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'deal_img_large_height', '353', 'Int', 'plugin', 'deals', 'Yes', 999, 'Deal image large height', NOW());

INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'deals_tipping_only_for_single_item', '1', 'Boolean', 'plugin', 'deals', 'Yes', 999, 'Tipping allowed only for single item', NOW());

INSERT INTO `meta_details` (`id`, `page_name`, `meta_title`, `meta_description`, `meta_keyword`, `common_terms`, `language`, `date_added`, `date_updated`, `status`) VALUES
(2, 'deals', 'A Deals - VAR_SITE_NAME', 'Deals - VAR_SITE_NAME', 'Deals - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '2015-05-19 20:45:46', 0),
(3, 'recent-deals', 'Recent deals - VAR_SITE_NAME', 'Recent deals - VAR_SITE_NAME', 'Recent deals - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(4, 'expiring-deals', 'Expiring soon deals- VAR_SITE_NAME', 'My Deals - VAR_SITE_NAME', 'My Deals - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(5, 'expired-deals', 'Expired deals - VAR_SITE_NAME', 'Expired deals - VAR_SITE_NAME', 'Expired deals - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(6, 'my-deal-future-request', 'My Feature Deal Request - VAR_SITE_NAME', 'My Feature Deal Request - VAR_SITE_NAME', 'My Feature Deal Request - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(7, 'my-deal-list', 'My Deals - VAR_SITE_NAME', 'My Deals - VAR_SITE_NAME', 'My Deals - VAR_SITE_NAME', 'VAR_SITE_NAME,VAR_LIST', 'en', '0000-00-00 00:00:00', '2015-05-15 13:22:57', 1),
(8, 'add-deal', 'Deals - Add Deal - VAR_SITE_NAME', 'Deals - Add Deal - VAR_SITE_NAME', 'Deals - Add Deal - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(9, 'update-deal', 'Deals - Update Deal - VAR_SITE_NAME', 'Deals - Update Deal - VAR_SITE_NAME', 'Deals - Update Deal - VAR_SITE_NAME', 'VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(10, 'view-deal', 'Deal - DEAL_TITLE - VAR_SITE_NAME', 'Deal - DEAL_TITLE - VAR_SITE_NAME', 'Deal - DEAL_TITLE - VAR_SITE_NAME', 'DEAL_TITLE, VAR_SITE_NAME', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1);