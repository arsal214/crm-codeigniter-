<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2023-12-25 14:06:54 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:06:54 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:06:55 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:06:55 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:06:55 --> Query error: Unknown column 'tblleads.id' in 'field list' - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS 1, tblleads.id as id, tblleads.name as name, company, tblleads.email as email, tblleads.phonenumber as phonenumber, lead_value, (SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltaggables JOIN tbltags ON tbltaggables.tag_id = tbltags.id WHERE rel_id = tblleads.id and rel_type="lead" ORDER by tag_order ASC LIMIT 1) as tags, firstname as assigned_firstname, tblleads_status.name as status_name, tblleads_sources.name as source_name, lastcontact, dateadded ,junk,lost,color,status,assigned,lastname as assigned_lastname,tblleads.addedfrom as addedfrom,(SELECT count(leadid) FROM tblclients WHERE tblclients.leadid=tblleads.id) as is_converted,zip
    FROM tblleads_school
    LEFT JOIN tblstaff ON tblstaff.staffid = tblleads.assigned LEFT JOIN tblleads_status ON tblleads_status.id = tblleads.status JOIN tblleads_sources ON tblleads_sources.id = tblleads.source
    
    WHERE  lost = 0 AND junk = 0 AND status IN (2,3,6,8)
    
    ORDER BY dateadded DESC
    LIMIT 0, 25
    
ERROR - 2023-12-25 14:12:57 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:12:57 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:12:58 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:12:58 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:12:58 --> Query error: Unknown column 'tblleads.addedfrom' in 'field list' - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS 1, tblleads_school.id as id, tblleads_school.name as name, company, tblleads_school.email as email, tblleads_school.phonenumber as phonenumber, lead_value, (SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltaggables JOIN tbltags ON tbltaggables.tag_id = tbltags.id WHERE rel_id = tblleads_school.id and rel_type="lead" ORDER by tag_order ASC LIMIT 1) as tags, firstname as assigned_firstname, tblleads_status.name as status_name, tblleads_sources.name as source_name, lastcontact, dateadded ,junk,lost,color,status,assigned,lastname as assigned_lastname,tblleads.addedfrom as addedfrom,(SELECT count(leadid) FROM tblclients WHERE tblclients.leadid=tblleads.id) as is_converted,zip
    FROM tblleads_school
    LEFT JOIN tblstaff ON tblstaff.staffid = tblleads_school.assigned LEFT JOIN tblleads_status ON tblleads_status.id = tblleads_school.status JOIN tblleads_sources ON tblleads_sources.id = tblleads_school.source
    
    WHERE  lost = 0 AND junk = 0 AND status IN (2,3,6,8)
    
    ORDER BY dateadded DESC
    LIMIT 0, 25
    
ERROR - 2023-12-25 14:19:29 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:19:29 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:19:30 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:19:30 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:19:30 --> Severity: Notice --> Undefined variable: sTable C:\xampp\htdocs\crm\application\views\admin\tables\leads.php 18
ERROR - 2023-12-25 14:19:30 --> Severity: Notice --> Undefined variable: sTable C:\xampp\htdocs\crm\application\views\admin\tables\leads.php 19
ERROR - 2023-12-25 14:19:30 --> Severity: Notice --> Undefined variable: sTable C:\xampp\htdocs\crm\application\views\admin\tables\leads.php 25
ERROR - 2023-12-25 14:19:30 --> Severity: Notice --> Undefined variable: sTable C:\xampp\htdocs\crm\application\views\admin\tables\leads.php 26
ERROR - 2023-12-25 14:19:30 --> Severity: Notice --> Undefined variable: sTable C:\xampp\htdocs\crm\application\views\admin\tables\leads.php 28
ERROR - 2023-12-25 14:19:30 --> Query error: Unknown column 'tbl.id' in 'field list' - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS 1, tbl.id as id, tbl.name as name, company, tbl.email as email, tbl.phonenumber as phonenumber, lead_value, (SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltaggables JOIN tbltags ON tbltaggables.tag_id = tbltags.id WHERE rel_id = tbl.id and rel_type="lead" ORDER by tag_order ASC LIMIT 1) as tags, firstname as assigned_firstname, tblleads_status.name as status_name, tblleads_sources.name as source_name, lastcontact, dateadded ,junk,lost,color,status,assigned,lastname as assigned_lastname,tbltblleads_school.addedfrom as addedfrom,(SELECT count(leadid) FROM tblclients WHERE tblclients.leadid=tbltblleads_school.id) as is_converted,zip
    FROM tblleads_school
    LEFT JOIN tblstaff ON tblstaff.staffid = tbltblleads_school.assigned LEFT JOIN tblleads_status ON tblleads_status.id = tbltblleads_school.status JOIN tblleads_sources ON tblleads_sources.id = tbltblleads_school.source
    
    WHERE  lost = 0 AND junk = 0 AND status IN (2,3,6,8)
    
    ORDER BY dateadded DESC
    LIMIT 0, 25
    
ERROR - 2023-12-25 14:19:30 --> Severity: Warning --> Cannot modify header information - headers already sent by (output started at C:\xampp\htdocs\crm\system\core\Exceptions.php:271) C:\xampp\htdocs\crm\system\core\Common.php 574
ERROR - 2023-12-25 14:20:12 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:20:12 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:20:12 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:20:12 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:20:12 --> Severity: Notice --> Undefined variable: sTable C:\xampp\htdocs\crm\application\views\admin\tables\leads.php 18
ERROR - 2023-12-25 14:20:12 --> Severity: Notice --> Undefined variable: sTable C:\xampp\htdocs\crm\application\views\admin\tables\leads.php 19
ERROR - 2023-12-25 14:20:12 --> Severity: Notice --> Undefined variable: sTable C:\xampp\htdocs\crm\application\views\admin\tables\leads.php 25
ERROR - 2023-12-25 14:20:12 --> Severity: Notice --> Undefined variable: sTable C:\xampp\htdocs\crm\application\views\admin\tables\leads.php 26
ERROR - 2023-12-25 14:20:12 --> Severity: Notice --> Undefined variable: sTable C:\xampp\htdocs\crm\application\views\admin\tables\leads.php 28
ERROR - 2023-12-25 14:20:12 --> Query error: Unknown column 'tbl.id' in 'field list' - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS 1, tbl.id as id, tbl.name as name, company, tbl.email as email, tbl.phonenumber as phonenumber, lead_value, (SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltaggables JOIN tbltags ON tbltaggables.tag_id = tbltags.id WHERE rel_id = tbl.id and rel_type="lead" ORDER by tag_order ASC LIMIT 1) as tags, firstname as assigned_firstname, tblleads_status.name as status_name, tblleads_sources.name as source_name, lastcontact, dateadded ,junk,lost,color,status,assigned,lastname as assigned_lastname,tbltblleads_school.addedfrom as addedfrom,(SELECT count(leadid) FROM tblclients WHERE tblclients.leadid=tbltblleads_school.id) as is_converted,zip
    FROM tblleads_school
    LEFT JOIN tblstaff ON tblstaff.staffid = tbltblleads_school.assigned LEFT JOIN tblleads_status ON tblleads_status.id = tbltblleads_school.status JOIN tblleads_sources ON tblleads_sources.id = tbltblleads_school.source
    
    WHERE  lost = 0 AND junk = 0 AND status IN (2,3,6,8)
    
    ORDER BY dateadded DESC
    LIMIT 0, 25
    
ERROR - 2023-12-25 14:20:12 --> Severity: Warning --> Cannot modify header information - headers already sent by (output started at C:\xampp\htdocs\crm\system\core\Exceptions.php:271) C:\xampp\htdocs\crm\system\core\Common.php 574
ERROR - 2023-12-25 14:20:45 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:20:45 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:20:45 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:20:45 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:20:45 --> Query error: Unknown column 'tbltblleads_school.id' in 'field list' - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS 1, tbltblleads_school.id as id, tbltblleads_school.name as name, company, tbltblleads_school.email as email, tbltblleads_school.phonenumber as phonenumber, lead_value, (SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltaggables JOIN tbltags ON tbltaggables.tag_id = tbltags.id WHERE rel_id = tbltblleads_school.id and rel_type="lead" ORDER by tag_order ASC LIMIT 1) as tags, firstname as assigned_firstname, tblleads_status.name as status_name, tblleads_sources.name as source_name, lastcontact, dateadded ,junk,lost,color,status,assigned,lastname as assigned_lastname,tbltblleads_school.addedfrom as addedfrom,(SELECT count(leadid) FROM tblclients WHERE tblclients.leadid=tbltblleads_school.id) as is_converted,zip
    FROM tblleads_school
    LEFT JOIN tblstaff ON tblstaff.staffid = tbltblleads_school.assigned LEFT JOIN tblleads_status ON tblleads_status.id = tbltblleads_school.status JOIN tblleads_sources ON tblleads_sources.id = tbltblleads_school.source
    
    WHERE  lost = 0 AND junk = 0 AND status IN (2,3,6,8)
    
    ORDER BY dateadded DESC
    LIMIT 0, 25
    
ERROR - 2023-12-25 14:21:21 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:21:21 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:21:22 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:21:22 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:21:22 --> Query error: Table 'test.leads_school' doesn't exist - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS 1, tblleads_school.id as id, tblleads_school.name as name, company, tblleads_school.email as email, tblleads_school.phonenumber as phonenumber, lead_value, (SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltaggables JOIN tbltags ON tbltaggables.tag_id = tbltags.id WHERE rel_id = tblleads_school.id and rel_type="lead" ORDER by tag_order ASC LIMIT 1) as tags, firstname as assigned_firstname, tblleads_status.name as status_name, tblleads_sources.name as source_name, lastcontact, dateadded ,junk,lost,color,status,assigned,lastname as assigned_lastname,tblleads_school.addedfrom as addedfrom,(SELECT count(leadid) FROM tblclients WHERE tblclients.leadid=tblleads_school.id) as is_converted,zip
    FROM leads_school
    LEFT JOIN tblstaff ON tblstaff.staffid = tblleads_school.assigned LEFT JOIN tblleads_status ON tblleads_status.id = tblleads_school.status JOIN tblleads_sources ON tblleads_sources.id = tblleads_school.source
    
    WHERE  lost = 0 AND junk = 0 AND status IN (2,3,6,8)
    
    ORDER BY dateadded DESC
    LIMIT 0, 25
    
ERROR - 2023-12-25 14:22:04 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:22:04 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:22:05 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:22:05 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:22:27 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:22:27 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:25:15 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:25:15 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:26:43 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:26:43 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:26:44 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:26:44 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:39:31 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:39:31 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:39:46 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:39:46 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:39:47 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:39:47 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:40:03 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:40:03 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:40:04 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:40:04 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:40:48 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:40:48 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:40:48 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:40:48 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:40:51 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:40:51 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:40:55 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:40:55 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:41:25 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:41:25 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:42:37 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:42:37 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:42:40 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:42:40 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:42:54 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:42:54 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:43:11 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:43:11 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:43:12 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:43:12 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:43:19 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:43:19 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:45:02 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:45:02 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:45:04 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:45:04 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:46:30 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:46:30 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:47:19 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:47:19 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:47:28 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:47:28 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:47:41 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:47:41 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:47:42 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:47:42 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:47:48 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:47:48 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:47:49 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:47:49 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:47:57 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:47:57 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:48:17 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:48:17 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:48:19 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:48:19 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:48:21 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:48:21 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:48:22 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:48:22 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:48:34 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:48:34 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:50:02 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:50:02 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:50:03 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:50:03 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:50:08 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:50:08 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:50:38 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:50:38 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:50:47 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:50:47 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:50:49 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:50:49 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:50:52 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:50:52 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:51:48 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:51:48 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:51:48 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:51:48 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:51:49 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:51:49 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:51:53 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:51:53 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:52:29 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:52:29 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:52:30 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:52:30 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:52:33 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:52:33 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:52:51 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:52:51 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:52:52 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:52:52 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:52:55 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:52:55 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:53:01 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:53:01 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:53:01 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:53:01 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:53:06 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:53:06 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:55:58 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:55:58 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:59:50 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 14:59:50 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 14:59:50 --> Severity: Notice --> Undefined offset: 2 C:\xampp\htdocs\crm\modules\advanced_task_status_manager\advanced_task_status_manager.php 122
ERROR - 2023-12-25 14:59:50 --> Severity: Notice --> Undefined property: Dashboard::$project_status_once C:\xampp\htdocs\crm\modules\advanced_task_status_manager\advanced_task_status_manager.php 146
ERROR - 2023-12-25 14:59:50 --> Severity: Notice --> Undefined property: Dashboard::$project_status_once C:\xampp\htdocs\crm\modules\advanced_task_status_manager\advanced_task_status_manager.php 146
ERROR - 2023-12-25 20:23:24 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 20:23:24 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 20:23:26 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 20:23:26 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 20:23:29 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 20:23:29 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 20:23:46 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 20:23:46 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 20:23:46 --> Query error: Unknown column 'tblleas_school.name' in 'field list' - Invalid query: SELECT *, `tblleas_school`.`name`, `tblleas_school`.`id`, `tblleads_status`.`name` as `status_name`, `tblleads_sources`.`name` as `source_name`
FROM `tblleads`
LEFT JOIN `tblleads_status` ON `tblleads_status`.`id`=`tblleas_school`.`status`
LEFT JOIN `tblleads_sources` ON `tblleads_sources`.`id`=`tblleas_school`.`source`
WHERE `tblleas_school`.`id` = 1206
ERROR - 2023-12-25 20:25:25 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 20:25:25 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 20:25:37 --> Could not find the language line "Schools Leads"
ERROR - 2023-12-25 20:25:37 --> Could not find the language line "Project Statuses"
ERROR - 2023-12-25 20:25:37 --> Query error: Unknown column 'tblleads_school.name' in 'field list' - Invalid query: SELECT *, `tblleads_school`.`name`, `tblleads_school`.`id`, `tblleads_status`.`name` as `status_name`, `tblleads_sources`.`name` as `source_name`
FROM `tblleads`
LEFT JOIN `tblleads_status` ON `tblleads_status`.`id`=`tblleads_school`.`status`
LEFT JOIN `tblleads_sources` ON `tblleads_sources`.`id`=`tblleads_school`.`source`
WHERE `tblleads_school`.`id` = 1207
