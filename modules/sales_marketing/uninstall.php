<?php defined('BASEPATH') or exit('No direct script access allowed');
$CI = &get_instance();

//$CI->db->query("ALTER TABLE " . db_prefix() . "`files` DROP `deal_id`;");
$CI->db->query("DROP TABLE IF EXISTS `tbl_sam`;");
$CI->db->query("DROP TABLE IF EXISTS `tbl_sam_comments`;");
$CI->db->query("DROP TABLE IF EXISTS `tbl_sam_email`;");
$CI->db->query("DROP TABLE IF EXISTS `tbl_sam_items`;");
$CI->db->query("DROP TABLE IF EXISTS `tbl_sam_mettings`;");
$CI->db->query("DROP TABLE IF EXISTS `tbl_sam_pipelines`;");
$CI->db->query("DROP TABLE IF EXISTS `tbl_sam_source`;");
$CI->db->query("DROP TABLE IF EXISTS `tbl_sam_stages`;");
$CI->db->query("DROP TABLE IF EXISTS `tbl_sam_activity_log`;");
$CI->db->query("DROP TABLE IF EXISTS `tbl_sam_calls`;");
$CI->db->query("DROP TABLE IF EXISTS `tbl_sam_taskstimers`;");
$CI->db->query("DROP TABLE IF EXISTS `tbl_sam_proposals`;");
$CI->db->query("DROP TABLE IF EXISTS `tbl_sam_proposal_comments`;");
$CI->db->query("DROP TABLE IF EXISTS `tbl_sam_notes`;");
$CI->db->query("DROP TABLE IF EXISTS `tbl_sam_item_tax`;");
$CI->db->query("DROP TABLE IF EXISTS `tbl_sam_itemable`;");
$CI->db->query("DROP TABLE IF EXISTS `tbl_sam_transactions`;");
$CI->db->query("DROP TABLE IF EXISTS `tbl_sam_reminders`;");



