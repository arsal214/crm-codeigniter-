<?php
defined('BASEPATH') or exit('No direct script access allowed');

function generate_table(){
    $CI = &get_instance();
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
}


function get_leads_school_summary()
{
    $CI = &get_instance();
    if (!class_exists('leads_model')) {
        $CI->load->model('leads_model');
    }
    $statuses = $CI->leads_model->get_status();

    $totalStatuses         = count($statuses);
    $has_permission_view   = has_permission('leads', '', 'view');
    $sql                   = '';
    $whereNoViewPermission = '(addedfrom = ' . get_staff_user_id() . ' OR assigned=' . get_staff_user_id() . ' OR is_public = 1)';

    $statuses[] = [
        'lost'  => true,
        'name'  => _l('lost_leads'),
        'color' => '#fc2d42',
    ];

/*    $statuses[] = [
        'junk'  => true,
        'name'  => _l('junk_leads'),
        'color' => '',
    ];*/

    foreach ($statuses as $status) {
        $sql .= ' SELECT COUNT(*) as total';
        $sql .= ',SUM(lead_value) as value';
        $sql .= ' FROM ' . db_prefix() . 'leads_school';

        if (isset($status['lost'])) {
            $sql .= ' WHERE lost=1';
        } elseif (isset($status['junk'])) {
            $sql .= ' WHERE junk=1';
        } else {
            $sql .= ' WHERE status=' . $status['id'];
        }
        if (!$has_permission_view) {
            $sql .= ' AND ' . $whereNoViewPermission;
        }
        $sql .= ' UNION ALL ';
        $sql = trim($sql);
    }

    $result = [];

    // Remove the last UNION ALL
    $sql    = substr($sql, 0, -10);
    $result = $CI->db->query($sql)->result();

    if (!$has_permission_view) {
        $CI->db->where($whereNoViewPermission);
    }

    $total_leads = $CI->db->count_all_results(db_prefix() . 'leads_school');

    foreach ($statuses as $key => $status) {
        if (isset($status['lost']) || isset($status['junk'])) {
            $statuses[$key]['percent'] = ($total_leads > 0 ? number_format(($result[$key]->total * 100) / $total_leads, 2) : 0);
        }

        $statuses[$key]['total'] = $result[$key]->total;
        $statuses[$key]['value'] = $result[$key]->value;
    }

    return $statuses;
}
function get_table_school_leads_data($table, $params = [])
{
        $params = hooks()->apply_filters('table_params', $params, $table);

        foreach ($params as $key => $val) {
            $$key = $val;
        }

        $customFieldsColumns = [];

        $path = FCPATH.'modules/school_leads/views/tables/' . $table . EXT;
        // print($path);
        // exit();
        if (!file_exists($path)) {
            // print($path);
        // exit();
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
function render_school_leads_datatable($headings = [], $class = '', $additional_classes = [''], $table_attributes = [])
{
    $_additional_classes = '';
    $_table_attributes   = ' ';
    if (count($additional_classes) > 0) {
        $_additional_classes = ' ' . implode(' ', $additional_classes);
    }
    $CI      = & get_instance();
    $browser = $CI->agent->browser();
    $IEfix   = '';
    if ($browser == 'Internet Explorer') {
        $IEfix = 'ie-dt-fix';
    }

    foreach ($table_attributes as $key => $val) {
        $_table_attributes .= $key . '=' . '"' . $val . '" ';
    }

    $table = '<div class="' . $IEfix . '"><table' . $_table_attributes . 'class="dt-table-loading table table-' . $class . '' . $_additional_classes . '">';
    $table .= '<thead>';
    $table .= '<tr>';
    foreach ($headings as $heading) {
        if (!is_array($heading)) {
            $table .= '<th>' . $heading . '</th>';
        } else {
            $th_attrs = '';
            if (isset($heading['th_attrs'])) {
                foreach ($heading['th_attrs'] as $key => $val) {
                    $th_attrs .= $key . '=' . '"' . $val . '" ';
                }
            }
            $th_attrs = ($th_attrs != '' ? ' ' . $th_attrs : $th_attrs);
            $table .= '<th' . $th_attrs . '>' . $heading['name'] . '</th>';
        }
    }
    $table .= '</tr>';
    $table .= '</thead>';
    $table .= '<tbody></tbody>';
    $table .= '</table></div>';
    echo $table;
}