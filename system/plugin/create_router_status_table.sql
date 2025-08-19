-- SQL file to create router status monitor table
CREATE TABLE IF NOT EXISTS `tbl_router_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `router_id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'unknown',
  `last_check` datetime DEFAULT NULL,
  `last_online` datetime DEFAULT NULL,
  `last_offline` datetime DEFAULT NULL,
  `notification_number` varchar(20) DEFAULT NULL,
  `notification_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `last_notification_sent` datetime DEFAULT NULL,
  `notification_status` varchar(255) DEFAULT NULL,
  `last_uptime` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `router_id` (`router_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
