-- --------------------------------------------------------
-- Queries for core

INSERT INTO `config_data` (`file_name`, `config_var`, `config_value`, `config_type`, `config_category`, `config_section`, `editable`, `edit_order`, `description`, `date_added`) VALUES ('plugin', 'featuredsellers', '1', 'Boolean', 'plugin', 'featuredsellers', 'No', 999, 'Allow featured sellers plugin in the site', NOW());

-- Queries for plugin

DROP TABLE IF EXISTS `featured_sellers_plans`;
CREATE TABLE `featured_sellers_plans` (
  `featured_seller_plan_id` int(11) NOT NULL AUTO_INCREMENT,
  `featured_days` int(11) NOT NULL,
  `featured_price` decimal(15,2) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Inactive',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`featured_seller_plan_id`),
  KEY `Ind_status_featured_days` (`status`,`featured_days`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;