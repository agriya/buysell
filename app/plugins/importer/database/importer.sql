-- --------------------------------------------------------

--
-- Table structure for table `importer_csv_file`
--

DROP TABLE IF EXISTS `importer_csv_file`;
CREATE TABLE IF NOT EXISTS `importer_csv_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_from` enum('etsy','general') DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `server_url` varchar(255) DEFAULT NULL,
  `status` enum('InActive','Active','Progress','Completed') DEFAULT 'InActive',
  `user_id` int(11) DEFAULT NULL,
  `file_original_name` varchar(255) DEFAULT NULL,
  `item_count` int(11) DEFAULT '0',
  `parsed_item_count` int(11) DEFAULT '0',
  `zip_file_name` varchar(255) DEFAULT NULL,
  `zip_org_name` varchar(255) DEFAULT NULL,
  `zip_server_url` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `importer_etsy_product_details`
--

DROP TABLE IF EXISTS `importer_etsy_product_details`;
CREATE TABLE IF NOT EXISTS `importer_etsy_product_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `csv_file_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `url_slug` varchar(255) DEFAULT NULL,
  `description` text,
  `price` decimal(15,2) DEFAULT NULL,
  `currency_code` varchar(10) DEFAULT NULL,
  `quantity` int(11) DEFAULT '0',
  `tags` text,
  `materials` varchar(255) DEFAULT NULL,
  `image1_path` varchar(255) DEFAULT NULL,
  `image2_path` varchar(255) DEFAULT NULL,
  `image3_path` varchar(255) DEFAULT NULL,
  `image4_path` varchar(255) DEFAULT NULL,
  `image5_path` varchar(255) DEFAULT NULL,
  `status` enum('InActive','Active','Progress','Completed') DEFAULT 'InActive',
  `product_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `importer_general_product_details`
--

DROP TABLE IF EXISTS `importer_general_product_details`;
CREATE TABLE IF NOT EXISTS `importer_general_product_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `csv_file_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `url_slug` varchar(255) DEFAULT NULL,
  `description` text,
  `summary` text,
  `price` decimal(15,2) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `is_downloadable` enum('No','Yes') DEFAULT 'No',
  `tags` text,
  `demo_url` varchar(255) DEFAULT NULL,
  `stock_available` int(11) DEFAULT '0',
  `shipping_template` varchar(255) DEFAULT NULL,
  `image_attached` enum('No','Yes') DEFAULT 'No',
  `thumb_image` varchar(255) DEFAULT NULL,
  `default_image` varchar(255) DEFAULT NULL,
  `preview_image1` varchar(255) DEFAULT NULL,
  `preview_image2` varchar(255) DEFAULT NULL,
  `preview_image3` varchar(255) DEFAULT NULL,
  `preview_image4` varchar(255) DEFAULT NULL,
  `preview_image5` varchar(255) DEFAULT NULL,
  `status` enum('InActive','Active','Progress','Completed') DEFAULT 'InActive',
  `product_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES
('importer', 'max_csv_file_size', '5120', 'Int', 'plugin', 'importer', 'No', 999, 'Maximum file size allowed for csv file', NOW()),
('importer', 'max_zip_file_size', '5120', 'Int', 'plugin', 'importer', 'No', 999, 'Maximum file size for zip file for mass importer plugin', NOW()),
('importer', 'importer_file_folder', 'files/importer_files', 'String', 'plugin', 'importer', 'No', 999, 'Importer folder path', NOW()),
('plugin', 'importer', '1', 'Boolean', 'plugin', 'importer', 'Yes', 999, 'Allow importer plugin in the site', NOW());

INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES
('importer', 'temp_extrac_file_folder', 'files/temp_extract_files', 'String', 'plugin', 'importer', 'No', 999, 'Importer temprory extracted files will be stored in this path', NOW());

ALTER TABLE `importer_general_product_details` ADD `error_reasons` TEXT NOT NULL;
ALTER TABLE `importer_etsy_product_details` ADD `error_reasons` TEXT NOT NULL;

ALTER TABLE `importer_general_product_details` CHANGE `status` `status` ENUM( 'InActive', 'Active', 'Progress', 'Completed', 'Failed' ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'InActive';

ALTER TABLE `importer_etsy_product_details` CHANGE `status` `status` ENUM( 'InActive', 'Active', 'Progress', 'Completed', 'Failed' ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'InActive';