--
-- Table structure for table `variation`
--
DROP TABLE IF EXISTS `variation`;
CREATE TABLE `variation` (
  `variation_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `help_text` text,
  `user_id` bigint(20) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`variation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `variation_attributes`
--

DROP TABLE IF EXISTS `variation_attributes`;
CREATE TABLE `variation_attributes` (
  `attribute_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `variation_id` bigint(20) NOT NULL,
  `attribute_key` varchar(250) NOT NULL,
  `label` varchar(250) NOT NULL,
  `position` bigint(20) NOT NULL,
  PRIMARY KEY (`attribute_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `variation_group`
--

DROP TABLE IF EXISTS `variation_group`;
CREATE TABLE `variation_group` (
  `variation_group_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `variation_group_name` varchar(250) NOT NULL,
  `short_description` text NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `date_added` datetime NOT NULL,
  UNIQUE KEY `variation_group_id` (`variation_group_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `variation_group_items`
--

DROP TABLE IF EXISTS `variation_group_items`;
CREATE TABLE `variation_group_items` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `variation_group_id` bigint(20) NOT NULL,
  `variation_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `variation_uniq` (`variation_group_id`,`variation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `item_variation_attributes`
--

DROP TABLE IF EXISTS `item_variation_attributes`;
CREATE TABLE `item_variation_attributes` (
  `item_id` bigint(20) NOT NULL,
  `variation_id` bigint(20) NOT NULL,
  `attribute_id` bigint(20) NOT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT '1',
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `item_var_matrix_attributes`
--

DROP TABLE IF EXISTS `item_var_matrix_attributes`;
CREATE TABLE `item_var_matrix_attributes` (
  `item_id` bigint(20) NOT NULL,
  `matrix_id` bigint(20) NOT NULL,
  `attribute_id` bigint(20) NOT NULL,
  KEY `matrix_id` (`matrix_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `item_swap_image`
--

DROP TABLE IF EXISTS `item_swap_image`;
CREATE TABLE IF NOT EXISTS `item_swap_image` (
  `swap_image_id` bigint(20) NOT NULL auto_increment,
  `item_id` bigint(20) NOT NULL,
  `filename` varchar(100)  NOT NULL,
  `ext` varchar(4)  NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `title` varchar(255)  NOT NULL,
  `l_width` int(11) NOT NULL,
  `l_height` int(11) NOT NULL,
  `t_width` int(11) NOT NULL,
  `t_height` int(11) NOT NULL,
  `server_url` varchar(255)  NOT NULL,
  PRIMARY KEY  (`swap_image_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `item_variation`
--

DROP TABLE IF EXISTS `item_variation`;
CREATE TABLE `item_variation` (
  `item_id` bigint(20) NOT NULL,
  `variation_id` bigint(20) NOT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT '1',
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `item_var_matrix_details`
--

DROP TABLE IF EXISTS `item_var_matrix_details`;
CREATE TABLE `item_var_matrix_details` (
  `matrix_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) NOT NULL,
  `content` text NOT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`matrix_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `item_variation_details`
--

DROP TABLE IF EXISTS `item_variation_details`;
CREATE TABLE `item_variation_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) NOT NULL,
  `matrix_id` bigint(20) NOT NULL,
  `price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `price_impact` enum('','increase','decrease') NOT NULL,
  `giftwrap_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `giftwrap_price_impact` enum('','increase','decrease') NOT NULL,
  `shipping_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `shipping_price_impact` enum('','increase','decrease') NOT NULL,
  `stock` bigint(20) NOT NULL,
  `swap_img_id` bigint(20) NOT NULL DEFAULT '0',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'variations', '1', 'Boolean', 'plugin', 'variations', 'Yes', 999, 'Allow variations plugin in the site', NOW());

INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'allowusers_to_use_giftwrap', '1', 'Boolean', 'plugin', 'variations', 'Yes', 999, 'Allow users to use giftwrap', NOW());