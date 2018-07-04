-- --------------------------------------------------------
-- Queries for core

INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'sudopay', '1', 'Boolean', 'plugin', 'sudopay', 'No', 999, 'Allow sudopay plugin in the site', NOW());

INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'sudopay_payment', '1', 'Boolean', 'plugin', 'sudopay', 'No', 999, 'Payment through SudoPay?', NOW());
INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'sudopay_payment_test_mode', '1', 'Boolean', 'plugin', 'sudopay', 'No', 999, 'Enable SudoPay payment test mode?', NOW());
INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'sudopay_payment_used_addtowallet', '1', 'Boolean', 'plugin', 'sudopay', 'No', 999, 'Allow SudoPay payment used for add to wallet?', NOW());
INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'sudopay_payment_used_product_purchase', '1', 'Boolean', 'plugin', 'sudopay', 'No', 999, 'Allow SudoPay payment used for product purchase?', NOW());

INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'sudopay_live_merchant_id', '', 'String', 'plugin', 'sudopay', 'No', 999, 'Live - Sudopay Merchant Id', NOW());
INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'sudopay_live_website_id', '', 'String', 'plugin', 'sudopay', 'No', 999, 'Live - Sudopay Website Id', NOW());
INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'sudopay_live_secret_string', '', 'String', 'plugin', 'sudopay', 'No', 999, 'Live - Sudopay Secret String', NOW());
INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'sudopay_live_api_key', '', 'String', 'plugin', 'sudopay', 'No', 999, 'Live - Sudopay Api Key', NOW());
INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'sudopay_test_merchant_id', '', 'String', 'plugin', 'sudopay', 'No', 999, 'Test - Sudopay Merchant Id', NOW());
INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'sudopay_test_website_id', '', 'String', 'plugin', 'sudopay', 'No', 999, 'Test - Sudopay Website Id', NOW());
INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'sudopay_test_secret_string', '', 'String', 'plugin', 'sudopay', 'No', 999, 'Test - Sudopay Secret String', NOW());
INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'sudopay_test_api_key', '', 'String', 'plugin', 'sudopay', 'No', 999, 'Test - Sudopay Api Key', NOW());


-- Queries for plugin

DROP TABLE IF EXISTS `sudopay_ipn_logs`;
CREATE TABLE IF NOT EXISTS `sudopay_ipn_logs` (
  `id` bigint(20) NOT NULL auto_increment,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `ip` bigint(20) default NULL,
  `post_variable` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sudopay_payment_gateways`;
CREATE TABLE IF NOT EXISTS `sudopay_payment_gateways` (
  `id` bigint(20) NOT NULL auto_increment,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `sudopay_gateway_name` varchar(255) collate utf8_unicode_ci default NULL,
  `sudopay_gateway_id` bigint(20) default NULL,
  `sudopay_payment_group_id` bigint(20) NOT NULL,
  `sudopay_gateway_details` text collate utf8_unicode_ci,
  `days_after_amount_paid` bigint(20) default NULL,
  `is_marketplace_supported` tinyint(1) default '1',
  `name` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`),
  KEY `sudopay_gateway_id` (`sudopay_gateway_id`),
  KEY `sudopay_payment_group_id` (`sudopay_payment_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sudopay_payment_gateways_users`;
CREATE TABLE IF NOT EXISTS `sudopay_payment_gateways_users` (
  `id` bigint(20) NOT NULL auto_increment,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `sudopay_payment_gateway_id` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `sudopay_payment_gateway_id` (`sudopay_payment_gateway_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sudopay_payment_groups`;
CREATE TABLE IF NOT EXISTS `sudopay_payment_groups` (
  `id` int(11) NOT NULL auto_increment,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `sudopay_group_id` bigint(20) NOT NULL,
  `name` varchar(200) collate utf8_unicode_ci default NULL,
  `thumb_url` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`),
  KEY `sudopay_group_id` (`sudopay_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sudopay_transaction_logs`;
CREATE TABLE IF NOT EXISTS `sudopay_transaction_logs` (
  `id` bigint(20) NOT NULL auto_increment,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `amount` double(10,2) NOT NULL,
  `payment_id` bigint(20) default NULL,
  `class` varchar(50) collate utf8_unicode_ci default NULL,
  `foreign_id` bigint(20) default NULL,
  `sudopay_pay_key` varchar(255) collate utf8_unicode_ci default NULL,
  `merchant_id` bigint(20) default NULL,
  `gateway_id` int(11) default NULL,
  `gateway_name` varchar(255) collate utf8_unicode_ci default NULL,
  `status` varchar(50) collate utf8_unicode_ci default NULL,
  `payment_type` varchar(50) collate utf8_unicode_ci default NULL,
  `buyer_id` bigint(20) default NULL,
  `buyer_email` varchar(255) collate utf8_unicode_ci default NULL,
  `buyer_address` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`),
  KEY `payment_id` (`payment_id`),
  KEY `class` (`class`),
  KEY `foreign_id` (`foreign_id`),
  KEY `merchant_id` (`merchant_id`),
  KEY `gateway_id` (`gateway_id`),
  KEY `buyer_id` (`buyer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;