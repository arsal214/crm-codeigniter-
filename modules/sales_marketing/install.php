<?php
defined('BASEPATH') or exit('No direct script access allowed');
$CI = &get_instance();


// check if column exists in table
if (!$CI->db->field_exists('sam_comment_id', db_prefix() . 'files')) {
    //$CI->db->query("ALTER TABLE " . db_prefix() . "files ADD `sam_comment_id` INT NULL DEFAULT NULL;");
}

// check if column exists in table
if (!$CI->db->field_exists('sam_id', db_prefix() . 'invoices')) {
    $CI->db->query("ALTER TABLE " . db_prefix() . "invoices ADD `sam_id` INT(11) NULL DEFAULT NULL;");
}
if (!$CI->db->field_exists('sam_id', db_prefix() . 'contracts')) {
    $CI->db->query("ALTER TABLE " . db_prefix() . "contracts ADD `sam_id` INT(11) NULL DEFAULT NULL;");
}

$CI->db->query("CREATE TABLE IF NOT EXISTS `tbl_sam` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(191) DEFAULT NULL,
  `deal_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `source_id` int DEFAULT NULL,
  `status` varchar(100) DEFAULT 'open',
  `notes` text,
  `pipeline_id` int DEFAULT NULL,
  `currency` varchar(64) NOT NULL DEFAULT 'USD',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `days_to_close` date DEFAULT NULL,
  `user_id` text,
  `project_id` int DEFAULT NULL,
  `invoice_id` int DEFAULT NULL,
  `client_id` text,
  `stage_id` int NOT NULL,
  `default_deal_owner` int NOT NULL,
  `convert_to_project` varchar(100) DEFAULT NULL,
  `lost_reason` text,
  `tax` decimal(18,2) DEFAULT NULL,
  `total_tax` text,
   `dealorder` INT NULL DEFAULT '1',
   `contact_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;");

$CI->db->query("CREATE TABLE IF NOT EXISTS `tbl_sam_comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `deal_id` int NOT NULL,
  `staffid` int NOT NULL,
  `contact_id` int NOT NULL DEFAULT '0',
  `file_id` int NOT NULL DEFAULT '0',
  `dateadded` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `file_id` (`file_id`),
  KEY `deal_id` (`deal_id`)
) ENGINE=InnoDB;");
$CI->db->query("CREATE TABLE IF NOT EXISTS `tbl_sam_email` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email_to` text,
  `email_cc` varchar(100) DEFAULT NULL,
  `deals_id` int NOT NULL,
  `subject` varchar(230) DEFAULT NULL,
  `message_body` varchar(512) DEFAULT NULL,
  `uploads` varchar(512) DEFAULT NULL,
  `user_id` int NOT NULL,
  `files` text NOT NULL,
  `uploaded_path` text NOT NULL,
  `file_name` text NOT NULL,
  `size` int NOT NULL,
  `ext` varchar(100) NOT NULL,
  `is_image` int NOT NULL,
  `message_time` datetime NOT NULL,
  `attach_file` text,
  `email_from` varchar(120) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;");
$CI->db->query("CREATE TABLE IF NOT EXISTS `tbl_sam_items` (
  `items_id` int NOT NULL AUTO_INCREMENT,
  `deals_id` int NOT NULL,
  `tax_rates_id` text,
  `item_tax_rate` decimal(18,2) NOT NULL DEFAULT '0.00',
  `item_tax_name` text,
  `item_tax_total` decimal(18,2) NOT NULL DEFAULT '0.00',
  `quantity` decimal(18,2) DEFAULT '0.00',
  `total_cost` decimal(18,2) DEFAULT '0.00',
  `item_name` varchar(255) DEFAULT 'Item Name',
  `item_desc` longtext,
  `unit_cost` decimal(18,2) DEFAULT '0.00',
  `order` int DEFAULT '0',
  `date_saved` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `unit` varchar(200) DEFAULT NULL,
  `hsn_code` text,
  `item_id` int DEFAULT '0',
  PRIMARY KEY (`items_id`)
) ENGINE=InnoDB;");
$CI->db->query("CREATE TABLE IF NOT EXISTS `tbl_sam_mettings` (
  `mettings_id` int NOT NULL AUTO_INCREMENT,
  `leads_id` int DEFAULT NULL,
  `opportunities_id` int DEFAULT NULL,
  `meeting_subject` varchar(200) NOT NULL,
  `attendees` varchar(300) NOT NULL,
  `user_id` int NOT NULL,
  `module` varchar(50) DEFAULT NULL,
  `module_field_id` int DEFAULT NULL,
  `start_date` varchar(100) NOT NULL,
  `end_date` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `description` mediumtext NOT NULL,
  PRIMARY KEY (`mettings_id`)
) ENGINE=InnoDB;");

//$CI->db->query(" TABLE IF EXISTS `tbl_sam_pipelines`;");
$CI->db->query("CREATE TABLE IF NOT EXISTS `tbl_sam_pipelines` (
  `pipeline_id` int NOT NULL AUTO_INCREMENT,
  `pipeline_name` varchar(100) NOT NULL,
  `description` varchar(512) DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`pipeline_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4;");

$CI->db->query("INSERT INTO `tbl_sam_pipelines` (`pipeline_id`, `pipeline_name`, `description`, `order`) VALUES
(1, 'Sales', NULL, 0),
(2, 'Interview', NULL, 0),
(3, 'Store', NULL, 0);");
//$CI->db->query("DROP TABLE IF EXISTS `tbl_sam_source`;");
$CI->db->query("CREATE TABLE IF NOT EXISTS `tbl_sam_source` (
  `source_id` int NOT NULL AUTO_INCREMENT,
  `source_name` varchar(100) NOT NULL,
  PRIMARY KEY (`source_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11;");

$CI->db->query("INSERT INTO `tbl_sam_source` (`source_id`, `source_name`) VALUES
(1, 'Facebook'),
(2, 'Google Organic'),
(3, 'Web'),
(4, 'Twitter'),
(6, 'Youtube'),
(7, 'Mailchimp'),
(8, 'Previous Client'),
(9, 'Email List'),
(10, 'Google Ads');");

//$CI->db->query("DROP TABLE IF EXISTS `tbl_sam_stages`;");
$CI->db->query("CREATE TABLE IF NOT EXISTS `tbl_sam_stages` (
  `stage_id` int NOT NULL AUTO_INCREMENT,
  `stage_name` varchar(512) DEFAULT NULL,
  `pipeline_id` int NOT NULL,
  `stage_order` int NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`stage_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8;");
$CI->db->query("INSERT INTO `tbl_sam_stages` (`stage_id`, `stage_name`, `pipeline_id`, `stage_order`, `date`) VALUES
(1, 'Qualified To Buy', 1, 1, '2023-08-23 09:22:38'),
(2, 'Contact Made', 1, 2, '2023-08-23 09:41:24'),
(3, 'Presentation Scheduled', 1, 3, '2023-08-23 09:41:47'),
(4, 'Proposal Made', 1, 4, '2023-08-23 09:41:55'),
(5, 'Appointment Scheduled', 1, 5, '2023-08-23 09:42:13');");

$CI->db->query("CREATE TABLE IF NOT EXISTS `tbl_sam_activity_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `deal_id` int NOT NULL,
  `description` mediumtext NOT NULL,
  `additional_data` text,
  `date` datetime NOT NULL,
  `staffid` int NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `custom_activity` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;");

$CI->db->query("CREATE TABLE IF NOT EXISTS `tbl_sam_calls` (
  `calls_id` int NOT NULL AUTO_INCREMENT,
  `leads_id` int DEFAULT NULL,
  `opportunities_id` int DEFAULT NULL,
  `client_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `module` varchar(50) DEFAULT NULL,
  `module_field_id` int DEFAULT NULL,
  `date` varchar(20) DEFAULT NULL,
  `call_summary` varchar(200) NOT NULL,
  `call_type` varchar(50) DEFAULT NULL,
  `outcome` varchar(50) DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`calls_id`)
) ENGINE=InnoDB;");

$CI->db->query("CREATE TABLE IF NOT EXISTS `tbl_sam_taskstimers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sam_id` int(11) DEFAULT NULL,
  `pipeline_id` int(11) DEFAULT NULL,
  `stage_id` int(11) DEFAULT NULL,
  `start_time` varchar(64) NOT NULL,
  `end_time` varchar(64) DEFAULT NULL,
  `timesheet_duration` varchar(255) DEFAULT NULL,
  `time_spent` varchar(255) DEFAULT NULL,
  `is_timer` int(11) DEFAULT 0,
  `staff_id` int(11) NOT NULL,
  `hourly_rate` decimal(15,2) NOT NULL DEFAULT 0.00,
  `note` mediumtext DEFAULT NULL,
  `task_id` int(11) NOT NULL DEFAULT 0,
  `created_date` varchar(30) DEFAULT NULL,
  `updated_date` varchar(30) DEFAULT NULL,
  `deleted` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`),
  KEY `staff_id` (`staff_id`)
) ENGINE=InnoDB;");

$CI->db->query("CREATE TABLE IF NOT EXISTS `tbl_sam_proposals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rec_id` int(11) DEFAULT 0,
  `rec_type` varchar(50) DEFAULT NULL,
  `subject` varchar(191) DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `addedfrom` int(11) NOT NULL,
  `datecreated` datetime NOT NULL,
  `total` decimal(15,2) DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `total_tax` decimal(15,2) NOT NULL DEFAULT 0.00,
  `adjustment` decimal(15,2) DEFAULT NULL,
  `discount_percent` decimal(15,2) NOT NULL,
  `discount_total` decimal(15,2) NOT NULL,
  `discount_type` varchar(30) DEFAULT NULL,
  `show_quantity_as` int(11) NOT NULL DEFAULT 1,
  `currency` int(11) NOT NULL,
  `open_till` date DEFAULT NULL,
  `date` date NOT NULL,
  `rel_id` int(11) DEFAULT NULL,
  `rel_type` varchar(40) DEFAULT NULL,
  `assigned` int(11) DEFAULT NULL,
  `hash` varchar(32) NOT NULL,
  `proposal_to` varchar(191) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `country` int(11) NOT NULL DEFAULT 0,
  `zip` varchar(50) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `address` varchar(200) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `allow_comments` tinyint(1) NOT NULL DEFAULT 1,
  `status` int(11) NOT NULL,
  `estimate_id` int(11) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `date_converted` datetime DEFAULT NULL,
  `pipeline_order` int(11) DEFAULT 1,
  `is_expiry_notified` int(11) NOT NULL DEFAULT 0,
  `acceptance_firstname` varchar(50) DEFAULT NULL,
  `acceptance_lastname` varchar(50) DEFAULT NULL,
  `acceptance_email` varchar(100) DEFAULT NULL,
  `acceptance_date` datetime DEFAULT NULL,
  `acceptance_ip` varchar(40) DEFAULT NULL,
  `signature` varchar(40) DEFAULT NULL,
  `short_link` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB;");

$CI->db->query("CREATE TABLE IF NOT EXISTS `tbl_sam_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rel_id` int(11) NOT NULL,
  `rel_type` varchar(20) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `date_contacted` datetime DEFAULT NULL,
  `addedfrom` int(11) NOT NULL,
  `dateadded` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rel_id` (`rel_id`),
  KEY `rel_type` (`rel_type`)
) ENGINE=InnoDB;");

$CI->db->query("CREATE TABLE IF NOT EXISTS `tbl_sam_item_tax` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) NOT NULL,
  `rel_id` int(11) NOT NULL,
  `rel_type` varchar(20) NOT NULL,
  `taxrate` decimal(15,2) NOT NULL,
  `taxname` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `itemid` (`itemid`),
  KEY `rel_id` (`rel_id`)
) ENGINE=InnoDB;");

$CI->db->query("CREATE TABLE IF NOT EXISTS `tbl_sam_itemable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rel_id` int(11) NOT NULL,
  `rel_type` varchar(15) NOT NULL,
  `description` longtext NOT NULL,
  `long_description` longtext DEFAULT NULL,
  `qty` decimal(15,2) NOT NULL,
  `rate` decimal(15,2) NOT NULL,
  `unit` varchar(40) DEFAULT NULL,
  `item_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rel_id` (`rel_id`),
  KEY `rel_type` (`rel_type`),
  KEY `qty` (`qty`),
  KEY `rate` (`rate`)
) ENGINE=InnoDB;");  

$CI->db->query("CREATE TABLE IF NOT EXISTS `tbl_sam_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sam_id` int(11) DEFAULT NULL,
  `pipeline_id` int(11) DEFAULT NULL,
  `stage_id` int(11) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `rel_type` varchar(100) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL COMMENT 'customer id',
  `transaction_type` varchar(100) DEFAULT NULL,
  `t_date` date DEFAULT NULL,
  `reg_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;");

$CI->db->query("CREATE TABLE IF NOT EXISTS `tbl_sam_reminders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` mediumtext DEFAULT NULL,
  `date` datetime NOT NULL,
  `isnotified` int(11) NOT NULL DEFAULT 0,
  `rel_id` int(11) NOT NULL,
  `staff` int(11) NOT NULL,
  `rel_type` varchar(40) NOT NULL,
  `notify_by_email` int(11) NOT NULL DEFAULT 1,
  `creator` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rel_id` (`rel_id`),
  KEY `rel_type` (`rel_type`),
  KEY `staff` (`staff`)
) ENGINE=InnoDB;");


if (!$CI->db->field_exists('rel_type', 'tbl_sam')) {
    $CI->db->query("ALTER TABLE `tbl_sam` ADD `rel_type` VARCHAR(30) NULL DEFAULT NULL AFTER `client_id`, ADD `rel_id` INT(11) NULL DEFAULT NULL AFTER `rel_type`;");
}
$deal_send_email = [
    'type' => 'deal',
    'slug' => 'deal_send_email',
    'name' => 'Deal Send Email',
    'subject' => '{subject}',
    'message' => '{message}'
];
create_email_template($deal_send_email['subject'], $deal_send_email['message'], $deal_send_email['type'], $deal_send_email['name'], $deal_send_email['slug']);
