<?php
defined('BASEPATH') or exit('No direct script access allowed');


function get_partners_table_data($table, $params = [])
{
    $params = hooks()->apply_filters('table_params', $params, $table);

    foreach ($params as $key => $val) {
        $$key = $val;
    }

    $customFieldsColumns = [];

    $path = FCPATH . 'modules/school_partners/views/tables/' . $table . EXT;

    if (!file_exists($path)) {

        $path = $table;
        if (!endsWith($path, EXT)) {
            $path .= EXT;
        }
    } else {

        $myPrefixedPath = VIEWPATH . 'admin/tables/my_' . $table . EXT;
        if (file_exists($myPrefixedPath)) {
            $path = $myPrefixedPath;
        }
    }

    include_once($path);

    echo json_encode($output);
    die;
}
function generate_partners_tables(){
    $CI = &get_instance();
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
        $CI->db->query("CREATE TABLE ".db_prefix()."school_partners_groups (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(191) NOT NULL,
            `type` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `name` (`name`)
           ) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
    }
}
    function get_partner_table_data($table, $params = [])
    {
        $params = hooks()->apply_filters('table_params', $params, $table);

        foreach ($params as $key => $val) {
            $$key = $val;
        }

        $customFieldsColumns = [];

        $path = FCPATH . 'modules/school_partners/views/tables/' . $table . EXT;
        // echo $path;
        // exit();
        if (!file_exists($path)) {
            $path = $table;
            if (!endsWith($path, EXT)) {
                $path .= EXT;
            }
        } else {
            $myPrefixedPath = VIEWPATH . 'admin/tables/my_' . $table . EXT;
            if (file_exists($myPrefixedPath)) {
                $path = $myPrefixedPath;
            }
        }

        include_once($path);

        echo json_encode($output);
        die;
    }
function is_empty_parnters_company($id)
{

    $CI = &get_instance();

    // if($table == ''){
    //     $table = 'clients';
    // }else{
        $table = 'school_partners';
    // }
    $CI = &get_instance();
    $CI->db->select('company');
    $CI->db->from(db_prefix() . $table);
    $CI->db->where('userid', $id);
    $row = $CI->db->get()->row();
    if ($row) {
        if ($row->company == '') {
            return true;
        }

        return false;
    }

    return true;
}