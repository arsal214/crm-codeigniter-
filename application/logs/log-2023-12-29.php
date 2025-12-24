<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2023-12-29 05:52:24 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 05:52:24 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 05:52:24 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 05:52:24 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 05:52:26 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 05:52:26 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 05:52:26 --> Query error: Unknown column 'tblschool_partners.title' in 'field list' - Invalid query: SELECT `tblschool_partners`.`title`, `tblschool_partners`.`website`, `tblschool_partners`.`lead_value`, `tblschool_partners`.`address`, `tblschool_partners`.`city`, `tblschool_partners`.`state`, `tblschool_partners`.`country`, `tblschool_partners`.`zip`, `tblschool_partners`.`name` as `lead_name`, `tblleads_sources`.`name` as `source_name`, `tblschool_partners`.`id` as `id`, `tblschool_partners`.`assigned`, `tblschool_partners`.`email`, `tblschool_partners`.`phonenumber`, `tblschool_partners`.`company`, `tblschool_partners`.`dateadded`, `tblschool_partners`.`status`, `tblschool_partners`.`lastcontact`, (SELECT COUNT(*) FROM tblclients WHERE leadid=tblschool_partners.id) as is_lead_client, (SELECT COUNT(id) FROM tblfiles WHERE rel_id=tblschool_partners.id AND rel_type="lead") as total_files, (SELECT COUNT(id) FROM tblnotes WHERE rel_id=tblschool_partners.id AND rel_type="lead") as total_notes, (SELECT GROUP_CONCAT(name SEPARATOR ", ") FROM tbltaggables JOIN tbltags ON tbltaggables.tag_id = tbltags.id WHERE rel_id = tblschool_partners.id and rel_type="lead" ORDER by tag_order ASC) as tags
FROM `tblschool_partners`
JOIN `tblleads_sources` ON `tblleads_sources`.`id`=`tblschool_partners`.`source`
LEFT JOIN `tblstaff` ON `tblstaff`.`staffid`=`tblschool_partners`.`assigned`
WHERE `status` = '2'
ORDER BY tblschool_partners.leadorder IS NULL asc, tblschool_partners.leadorder asc
 LIMIT 50
ERROR - 2023-12-29 05:52:54 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 05:52:54 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 05:52:55 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 05:52:55 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 05:52:55 --> Query error: Unknown column 'tblschool_partners.title' in 'field list' - Invalid query: SELECT `tblschool_partners`.`title`, `tblschool_partners`.`website`, `tblschool_partners`.`lead_value`, `tblschool_partners`.`address`, `tblschool_partners`.`city`, `tblschool_partners`.`state`, `tblschool_partners`.`country`, `tblschool_partners`.`zip`, `tblschool_partners`.`name` as `lead_name`, `tblleads_sources`.`name` as `source_name`, `tblschool_partners`.`id` as `id`, `tblschool_partners`.`assigned`, `tblschool_partners`.`email`, `tblschool_partners`.`phonenumber`, `tblschool_partners`.`company`, `tblschool_partners`.`dateadded`, `tblschool_partners`.`status`, `tblschool_partners`.`lastcontact`, (SELECT COUNT(*) FROM tblclients WHERE leadid=tblschool_partners.id) as is_lead_client, (SELECT COUNT(id) FROM tblfiles WHERE rel_id=tblschool_partners.id AND rel_type="lead") as total_files, (SELECT COUNT(id) FROM tblnotes WHERE rel_id=tblschool_partners.id AND rel_type="lead") as total_notes, (SELECT GROUP_CONCAT(name SEPARATOR ", ") FROM tbltaggables JOIN tbltags ON tbltaggables.tag_id = tbltags.id WHERE rel_id = tblschool_partners.id and rel_type="lead" ORDER by tag_order ASC) as tags
FROM `tblschool_partners`
JOIN `tblleads_sources` ON `tblleads_sources`.`id`=`tblschool_partners`.`source`
LEFT JOIN `tblstaff` ON `tblstaff`.`staffid`=`tblschool_partners`.`assigned`
WHERE `status` = '2'
ORDER BY tblschool_partners.leadorder IS NULL asc, tblschool_partners.leadorder asc
 LIMIT 50
ERROR - 2023-12-29 05:53:01 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 05:53:01 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 05:53:03 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 05:53:03 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 05:53:03 --> Query error: Unknown column 'tblschool_partners.title' in 'field list' - Invalid query: SELECT `tblschool_partners`.`title`, `tblschool_partners`.`website`, `tblschool_partners`.`lead_value`, `tblschool_partners`.`address`, `tblschool_partners`.`city`, `tblschool_partners`.`state`, `tblschool_partners`.`country`, `tblschool_partners`.`zip`, `tblschool_partners`.`name` as `lead_name`, `tblleads_sources`.`name` as `source_name`, `tblschool_partners`.`id` as `id`, `tblschool_partners`.`assigned`, `tblschool_partners`.`email`, `tblschool_partners`.`phonenumber`, `tblschool_partners`.`company`, `tblschool_partners`.`dateadded`, `tblschool_partners`.`status`, `tblschool_partners`.`lastcontact`, (SELECT COUNT(*) FROM tblclients WHERE leadid=tblschool_partners.id) as is_lead_client, (SELECT COUNT(id) FROM tblfiles WHERE rel_id=tblschool_partners.id AND rel_type="lead") as total_files, (SELECT COUNT(id) FROM tblnotes WHERE rel_id=tblschool_partners.id AND rel_type="lead") as total_notes, (SELECT GROUP_CONCAT(name SEPARATOR ", ") FROM tbltaggables JOIN tbltags ON tbltaggables.tag_id = tbltags.id WHERE rel_id = tblschool_partners.id and rel_type="lead" ORDER by tag_order ASC) as tags
FROM `tblschool_partners`
JOIN `tblleads_sources` ON `tblleads_sources`.`id`=`tblschool_partners`.`source`
LEFT JOIN `tblstaff` ON `tblstaff`.`staffid`=`tblschool_partners`.`assigned`
WHERE `status` = '2'
ORDER BY tblschool_partners.leadorder IS NULL asc, tblschool_partners.leadorder asc
 LIMIT 50
ERROR - 2023-12-29 05:57:51 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 05:57:51 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 05:57:53 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 05:57:53 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 05:57:53 --> Query error: Unknown column 'tblschool_partners.title' in 'field list' - Invalid query: SELECT `tblschool_partners`.`title`, `tblschool_partners`.`website`, `tblschool_partners`.`lead_value`, `tblschool_partners`.`address`, `tblschool_partners`.`city`, `tblschool_partners`.`state`, `tblschool_partners`.`country`, `tblschool_partners`.`zip`, `tblschool_partners`.`name` as `lead_name`, `tblleads_sources`.`name` as `source_name`, `tblschool_partners`.`id` as `id`, `tblschool_partners`.`assigned`, `tblschool_partners`.`email`, `tblschool_partners`.`phonenumber`, `tblschool_partners`.`company`, `tblschool_partners`.`dateadded`, `tblschool_partners`.`status`, `tblschool_partners`.`lastcontact`, (SELECT COUNT(*) FROM tblclients WHERE leadid=tblschool_partners.id) as is_lead_client, (SELECT COUNT(id) FROM tblfiles WHERE rel_id=tblschool_partners.id AND rel_type="lead") as total_files, (SELECT COUNT(id) FROM tblnotes WHERE rel_id=tblschool_partners.id AND rel_type="lead") as total_notes, (SELECT GROUP_CONCAT(name SEPARATOR ", ") FROM tbltaggables JOIN tbltags ON tbltaggables.tag_id = tbltags.id WHERE rel_id = tblschool_partners.id and rel_type="lead" ORDER by tag_order ASC) as tags
FROM `tblschool_partners`
JOIN `tblleads_sources` ON `tblleads_sources`.`id`=`tblschool_partners`.`source`
LEFT JOIN `tblstaff` ON `tblstaff`.`staffid`=`tblschool_partners`.`assigned`
WHERE `status` = '2'
ORDER BY tblschool_partners.leadorder IS NULL asc, tblschool_partners.leadorder asc
 LIMIT 50
ERROR - 2023-12-29 05:58:05 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 05:58:05 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 05:58:05 --> Query error: Unknown column 'tblschool_partners.title' in 'field list' - Invalid query: SELECT `tblschool_partners`.`title`, `tblschool_partners`.`website`, `tblschool_partners`.`lead_value`, `tblschool_partners`.`address`, `tblschool_partners`.`city`, `tblschool_partners`.`state`, `tblschool_partners`.`country`, `tblschool_partners`.`zip`, `tblschool_partners`.`name` as `lead_name`, `tblleads_sources`.`name` as `source_name`, `tblschool_partners`.`id` as `id`, `tblschool_partners`.`assigned`, `tblschool_partners`.`email`, `tblschool_partners`.`phonenumber`, `tblschool_partners`.`company`, `tblschool_partners`.`dateadded`, `tblschool_partners`.`status`, `tblschool_partners`.`lastcontact`, (SELECT COUNT(*) FROM tblclients WHERE leadid=tblschool_partners.id) as is_lead_client, (SELECT COUNT(id) FROM tblfiles WHERE rel_id=tblschool_partners.id AND rel_type="lead") as total_files, (SELECT COUNT(id) FROM tblnotes WHERE rel_id=tblschool_partners.id AND rel_type="lead") as total_notes, (SELECT GROUP_CONCAT(name SEPARATOR ", ") FROM tbltaggables JOIN tbltags ON tbltaggables.tag_id = tbltags.id WHERE rel_id = tblschool_partners.id and rel_type="lead" ORDER by tag_order ASC) as tags
FROM `tblschool_partners`
JOIN `tblleads_sources` ON `tblleads_sources`.`id`=`tblschool_partners`.`source`
LEFT JOIN `tblstaff` ON `tblstaff`.`staffid`=`tblschool_partners`.`assigned`
WHERE `status` = '2'
ORDER BY tblschool_partners.leadorder IS NULL asc, tblschool_partners.leadorder asc
 LIMIT 50
ERROR - 2023-12-29 06:00:19 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 06:00:19 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 06:00:19 --> Query error: Unknown column 'tblschool_partners.title' in 'field list' - Invalid query: SELECT `tblschool_partners`.`title`, `tblschool_partners`.`website`, `tblschool_partners`.`lead_value`, `tblschool_partners`.`address`, `tblschool_partners`.`city`, `tblschool_partners`.`state`, `tblschool_partners`.`country`, `tblschool_partners`.`zip`, `tblschool_partners`.`name` as `lead_name`, `tblleads_sources`.`name` as `source_name`, `tblschool_partners`.`id` as `id`, `tblschool_partners`.`assigned`, `tblschool_partners`.`email`, `tblschool_partners`.`phonenumber`, `tblschool_partners`.`company`, `tblschool_partners`.`dateadded`, `tblschool_partners`.`status`, `tblschool_partners`.`lastcontact`, (SELECT COUNT(*) FROM tblclients WHERE leadid=tblschool_partners.id) as is_lead_client, (SELECT COUNT(id) FROM tblfiles WHERE rel_id=tblschool_partners.id AND rel_type="lead") as total_files, (SELECT COUNT(id) FROM tblnotes WHERE rel_id=tblschool_partners.id AND rel_type="lead") as total_notes, (SELECT GROUP_CONCAT(name SEPARATOR ", ") FROM tbltaggables JOIN tbltags ON tbltaggables.tag_id = tbltags.id WHERE rel_id = tblschool_partners.id and rel_type="lead" ORDER by tag_order ASC) as tags
FROM `tblschool_partners`
JOIN `tblleads_sources` ON `tblleads_sources`.`id`=`tblschool_partners`.`source`
LEFT JOIN `tblstaff` ON `tblstaff`.`staffid`=`tblschool_partners`.`assigned`
WHERE `status` = '2'
ORDER BY tblschool_partners.leadorder IS NULL asc, tblschool_partners.leadorder asc
 LIMIT 50
ERROR - 2023-12-29 06:04:55 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 06:04:55 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 06:04:57 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 06:04:57 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 06:06:03 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 06:06:03 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 06:06:38 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 06:06:38 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 06:06:39 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 06:06:39 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 06:07:00 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 06:07:00 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 06:07:02 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 06:07:02 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 06:07:35 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 06:07:35 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 06:07:36 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 06:07:36 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 06:07:51 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 06:07:51 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 06:08:19 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 06:08:19 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 06:08:20 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 06:08:20 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 06:09:51 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 06:09:51 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 06:09:53 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 06:09:53 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 06:10:13 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 06:10:13 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 06:10:15 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 06:10:15 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 06:10:33 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 06:10:33 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 06:10:52 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 06:10:52 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 06:10:53 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 06:10:53 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 06:11:39 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 06:11:39 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 06:11:41 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 06:11:41 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 06:11:52 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-29 06:11:52 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:52 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Undefined variable: base_currency C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\views\admin\leads\_kan_ban_card.php 36
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'symbol' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 124
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'decimal_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'thousand_separator' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 137
ERROR - 2023-12-29 06:11:53 --> Severity: Notice --> Trying to get property 'placement' of non-object C:\xampp\htdocs\crm\application\helpers\sales_helper.php 143
