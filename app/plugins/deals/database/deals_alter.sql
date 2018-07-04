-- Boobathi N
-- Date added: 19/05/2014

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

-- Boobathi N
-- Date added: 10/07/2015

ALTER TABLE `deal` ENGINE=InnoDB;
ALTER TABLE `deal_item` ENGINE=InnoDB;
ALTER TABLE `deal_featured_request` ENGINE=InnoDB;
ALTER TABLE `deal_featured` ENGINE=InnoDB;
ALTER TABLE `deal_item_purchased_details` ENGINE=InnoDB;