<?php
defined('BASEPATH') or exit('No direct script access allowed');
if(!$CI->db->table_exists(db_prefix() . 'leads_school')) {
	$CI->db->query('CREATE TABLE `'.db_prefix().'leads_school` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`hash` varchar(65) DEFAULT NULL,
		`name` varchar(191) NOT NULL,
		`title` varchar(100) DEFAULT NULL,
		`company` varchar(191) DEFAULT NULL,
		`description` text DEFAULT NULL,
		`country` int(11) NOT NULL DEFAULT 0,
		`zip` varchar(15) DEFAULT NULL,
		`city` varchar(100) DEFAULT NULL,
		`state` varchar(50) DEFAULT NULL,
		`address` varchar(100) DEFAULT NULL,
		`assigned` int(11) NOT NULL DEFAULT 0,
		`dateadded` datetime NOT NULL,
		`from_form_id` int(11) NOT NULL DEFAULT 0,
		`status` int(11) NOT NULL,
		`source` int(11) NOT NULL,
		`lastcontact` datetime DEFAULT NULL,
		`dateassigned` date DEFAULT NULL,
		`last_status_change` datetime DEFAULT NULL,
		`addedfrom` int(11) NOT NULL,
		`email` varchar(100) DEFAULT NULL,
		`website` varchar(150) DEFAULT NULL,
		`leadorder` int(11) DEFAULT 1,
		`phonenumber` varchar(50) DEFAULT NULL,
		`date_converted` datetime DEFAULT NULL,
		`lost` tinyint(1) NOT NULL DEFAULT 0,
		`junk` int(11) NOT NULL DEFAULT 0,
		`last_lead_status` int(11) NOT NULL DEFAULT 0,
		`is_imported_from_email_integration` tinyint(1) NOT NULL DEFAULT 0,
		`email_integration_uid` varchar(30) DEFAULT NULL,
		`is_public` tinyint(1) NOT NULL DEFAULT 0,
		`default_language` varchar(40) DEFAULT NULL,
		`client_id` int(11) NOT NULL DEFAULT 0,
		`lead_value` decimal(15,2) DEFAULT NULL,
		`type` varchar(255) NOT NULL DEFAULT "sl",
		PRIMARY KEY (`id`),
		KEY `name` (`name`),
		KEY `company` (`company`),
		KEY `email` (`email`),
		KEY `assigned` (`assigned`),
		KEY `status` (`status`),
		KEY `source` (`source`),
		KEY `lastcontact` (`lastcontact`),
		KEY `dateadded` (`dateadded`),
		KEY `leadorder` (`leadorder`),
		KEY `from_form_id` (`from_form_id`)
	   ) ENGINE=InnoDB AUTO_INCREMENT=1214 DEFAULT CHARSET=utf8');
}
if(!$CI->db->table_exists(db_prefix() . 'school_partners')) {
	$CI->db->query("CREATE TABLE ".db_prefix()."school_partners (
		`userid` int(11) NOT NULL AUTO_INCREMENT,
		`company` varchar(191) DEFAULT NULL,
		`vat` varchar(50) DEFAULT NULL,
		`phonenumber` varchar(30) DEFAULT NULL,
		`country` int(11) NOT NULL DEFAULT 0,
		`city` varchar(100) DEFAULT NULL,
		`zip` varchar(15) DEFAULT NULL,
		`state` varchar(50) DEFAULT NULL,
		`address` varchar(191) DEFAULT NULL,
		`website` varchar(150) DEFAULT NULL,
		`datecreated` datetime NOT NULL,
		`active` int(11) NOT NULL DEFAULT 1,
		`leadid` int(11) DEFAULT NULL,
		`billing_street` varchar(200) DEFAULT NULL,
		`billing_city` varchar(100) DEFAULT NULL,
		`billing_state` varchar(100) DEFAULT NULL,
		`billing_zip` varchar(100) DEFAULT NULL,
		`billing_country` int(11) DEFAULT 0,
		`shipping_street` varchar(200) DEFAULT NULL,
		`shipping_city` varchar(100) DEFAULT NULL,
		`shipping_state` varchar(100) DEFAULT NULL,
		`shipping_zip` varchar(100) DEFAULT NULL,
		`shipping_country` int(11) DEFAULT 0,
		`longitude` varchar(191) DEFAULT NULL,
		`latitude` varchar(191) DEFAULT NULL,
		`default_language` varchar(40) DEFAULT NULL,
		`default_currency` int(11) NOT NULL DEFAULT 0,
		`show_primary_contact` int(11) NOT NULL DEFAULT 0,
		`stripe_id` varchar(40) DEFAULT NULL,
		`registration_confirmed` int(11) NOT NULL DEFAULT 1,
		`addedfrom` int(11) NOT NULL DEFAULT 0,
		PRIMARY KEY (`userid`),
		KEY `country` (`country`),
		KEY `leadid` (`leadid`),
		KEY `company` (`company`),
		KEY `active` (`active`)
	   ) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8
	   ");
}
if(!$CI->db->table_exists(db_prefix() . 'school_leads_status')) {
	$CI->db->query("CREATE TABLE  ".db_prefix()."school_leads_status (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`name` varchar(50) NOT NULL,
		`statusorder` int(11) DEFAULT NULL,
		`color` varchar(10) DEFAULT '#28B8DA',
		`isdefault` int(11) NOT NULL DEFAULT 0,
		`type` varchar(255) DEFAULT NULL,
		PRIMARY KEY (`id`),
		KEY `name` (`name`)
	   ) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8");
}
if(!$CI->db->table_exists(db_prefix() . 'school_leads_status')) {
	$CI->db->query("	CREATE TABLE ".db_prefix()."school_leads_sources (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`name` varchar(150) NOT NULL,
		`type` varchar(255) DEFAULT NULL,
		PRIMARY KEY (`id`),
		KEY `name` (`name`)
	   ) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8");
}