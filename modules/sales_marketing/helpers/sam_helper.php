<?php defined('BASEPATH') or exit('No direct script access allowed');


function sam_display_money($amount, $currency = null)
{
    return app_format_money($amount, get_base_currency());
}

function sam_default_currency()
{
    return get_base_currency()->name;
}

function sam_display_time($value, $no_str = null)
{
    return _dt($value);
}

function sam_join_data($table, $select = '*', $where = null, $join = null, $row = null, $order = null)
{
    $CI = &get_instance();
    if ($select == '*') {
        $CI->db->select('*', false);
    } else {
        $CI->db->select("$select", false);
    }
    $CI->db->from($table);
    if (!empty($join)) {
        foreach ($join as $tbl => $wh) {
            $CI->db->join($tbl, $wh, 'left');
        }
    }
    if (!empty($where)) {
        $CI->db->where($where);
    }
    if (!empty($order)) {
        // is array
        if (is_array($order)) {
            foreach ($order as $key => $value) {
                $CI->db->order_by($key, $value);
            }
        } else {
            $CI->db->order_by($order);
        }
    }

    $query = $CI->db->get();
    if (!empty($row) && $row === 'array') {
        $result = $query->result_array();
    } else if (!empty($row) && $row === 'object') {
        $result = $query->result();
    } else {
        $result = $query->row();
    }
    return $result;
}

function get_sam_row($table, $where, $fields = null)
{
    $CI = &get_instance();
    $query = $CI->db->where($where)->get($table);
    if ($query->num_rows() > 0) {
        $row = $query->row();
        if (!empty($fields)) {
            return $row->$fields;
        } else {
            return $row;
        }
    }
}

function sam_my_id()
{
    $CI = &get_instance();
    return $CI->session->userdata('user_id');
}

function sam_render_table($data, $where = null, $where_in = null)
{

    $CI = &get_instance();
    $CI->load->model('datatabless');
    $output = array(
        "draw" => intval($_POST["draw"]),
        "iTotalRecords" => $CI->datatabless->get_all_data($where, $where_in),
        "iTotalDisplayRecords" => $CI->datatabless->get_filtered_data($where, $where_in),
        "aaData" => $data
    );
    echo json_encode($output);
    exit();
}


function make_sam_datatables($where = null, $where_in = null, $old = null)
{
    $CI = &get_instance();
    $CI->load->model('datatabless');
    $CI->datatabless->make_deals_query();
    if (!empty($where)) {
        $CI->db->where($where);
    }
    $company = $CI->input->post('company');
    if (!empty($company)) {
        $CI->db->group_start();
        if ($CI->db->version() >= 8) {
            $sq = $CI->db->escape('\\b' . ($company) . '\\b');
        } else {
            $sq = $CI->db->escape('[[:<:]]' . ($company) . '[[:>:]]');
        }
        $CI->db->where('tbl_sam.client_id REGEXP', $sq, false);
        $CI->db->group_end();
    }
    if (!empty($where_in)) {
        $CI->db->where_in($where_in[0], $where_in[1]);
    }
    if ($_POST["length"] != -1) {
        $CI->db->limit($_POST['length'], $_POST['start']);
    }
    $query = $CI->db->get();
    //print_r($CI->db->last_query()); exit;
    return $query->result();
}

function get_sam_staff_details($user_id = null, $type = null, $where = null)
{
    $CI = &get_instance();
    $CI->db->select('tblstaff.*', FALSE);
    $CI->db->from('tblstaff');
    if (!empty($where)) {
        $CI->db->where($where);
    }
    if (!empty($user_id)) {
        $CI->db->where('tblstaff.staffid', $user_id);
        $query_result = $CI->db->get();
        $result = $query_result->row();
    } else {
        $CI->db->where('tblstaff.role !=', 2);
        $CI->db->where('tblstaff.activated', 1);
        $query_result = $CI->db->get();
        if (!empty($type)) {
            $result = $query_result->result_array();
        } else {
            $result = $query_result->result();
        }
    }
    return $result;
}

function get_sam_order_by($tbl, $where = null, $order_by = null, $ASC = null, $limit = null, $type = null)
{

    $CI = &get_instance();
    $CI->db->from($tbl);
    if (!empty($where) && $where != 0) {
        $CI->db->where($where);
    }
    if (!empty($ASC)) {
        $order = 'ASC';
    } else {
        $order = 'DESC';
    }
    $CI->db->order_by($order_by, $order);
    if (!empty($limit)) {
        $CI->db->limit($limit);
    }
    $query_result = $CI->db->get();
    if (!empty($type) && $type == 'array') {
        $result = $query_result->result_array();
    } else if (!empty($type)) {
        $result = $query_result->row();
    } else {
        $result = $query_result->result();
    }
    return $result;
}

function get_sam_result($tbl, $where = null, $type = null)
{   
    $CI = &get_instance();
    $CI->db->select('*');
    $CI->db->from($tbl);
    if (!empty($where) && $where != 0) {
        $CI->db->where($where);
    }
    if (!empty($_POST["length"]) && $_POST["length"] != -1) {
        $CI->db->limit($_POST['length'], $_POST['start']);
    }
    $query_result = $CI->db->get();
    if (!empty($type) && $type == 'array') {
        $result = $query_result->result_array();
    } else if (!empty($type)) {
        $result = $query_result->row();
    } else {
        $result = $query_result->result();
    }
    return $result;
}

function sam_client_name($client_id = null)
{
    $CI = &get_instance();
    if (empty($client_id)) {
        $client_id = $CI->session->userdata('client_id');
    }
    if (is_numeric($client_id)) {
        $clientInfo = $CI->db->where('userid', $client_id)->get('tblclients')->row();
    }
    if (!empty($clientInfo)) {
        return $clientInfo->company;
    } else {
        return lang('undefined_client');
    }
}

function sam_fullname($user_id = null)
{
    $CI = &get_instance();
    if (empty($user_id)) {
        $user_id = $CI->session->userdata('staffid');
    }

    $userInfo = $CI->db->where('staffid', $user_id)->get('tblstaff')->row();
    if (!empty($userInfo)) {
        return $userInfo->firstname . ' ' . $userInfo->lastname;
    } else {
        return 'Undefined user';
    }
}

function sam_move_temp_file($file_name, $target_path, $related_to = "", $source_path = NULL, $static_file_name = "")
{
    $new_filename = unique_filename($target_path, $file_name);
    //if not provide any source path we'll fi   nd the default path
    if (!$source_path) {
        $source_path = getcwd() . "/uploads/temp/" . $file_name;
    }

    //check destination directory. if not found try to create a new one
    if (!is_dir($target_path)) {
        if (!mkdir($target_path, 0777, true)) {
            die('Failed to create file folders.');
        }
    }

    //overwrite extisting logic and use static file name
    if ($static_file_name) {
        $new_filename = $static_file_name;
    }

    //check the file type is data or file. then copy to destination and remove temp file
    if (starts_sam_with($source_path, "data")) {
        copy_text_based_sam_image($source_path, $target_path . $new_filename);
        return $new_filename;
    } else {
        if (file_exists($source_path)) {
            copy($source_path, $target_path . $new_filename);
            unlink($source_path);
            return $new_filename;
        }
    }
    return false;
}

function copy_text_based_sam_image($image)
{
    $images_extentions = array("jpg", "JPG", "jpeg", "JPEG", "png", "PNG", "gif", "GIF", "bmp", "BMP");
    $image_parts = explode(".", $image);
    $image_end_part = end($image_parts);

    if (in_array($image_end_part, $images_extentions) == true) {
        return 1;
    } else {
        return 0;
    }
}

function check_image_sam_extension($image)
{
    $images_extentions = array("jpg", "JPG", "jpeg", "JPEG", "png", "PNG", "gif", "GIF", "bmp", "BMP");
    $image_parts = explode(".", $image);
    $image_end_part = end($image_parts);

    if (in_array($image_end_part, $images_extentions) == true) {
        return 1;
    } else {
        return 0;
    }
}

function starts_sam_with($string, $needle)
{
    $string = $string;
    return $needle === "" || strrpos($string, $needle, -strlen($string)) !== false;
}

function sam_copy_text_based_image($source_path, $target_path)
{
    $buffer_size = 3145728;
    $byte_number = 0;
    $file_open = fopen($source_path, "rb");
    $file_wirte = fopen($target_path, "w");
    while (!feof($file_open)) {
        $byte_number += fwrite($file_wirte, fread($file_open, $buffer_size));
    }
    fclose($file_open);
    fclose($file_wirte);
    return $byte_number;
}

function send_sam_later($params)
{
    $emails = array(
        'sent_to' => $params['recipient'],
        // 'sent_cc' => $params['cc'],
        'sent_from' => config_item('company_email') . ' ' . config_item('company_name'),
        'subject' => $params['subject'],
        'message' => $params['message']
    );
    $CI = &get_instance();
    $CI->db->insert('tbl_outgoing_emails', $emails);
    return TRUE;
}

function sam_details_tabs($id)
{
    $staff_id = get_staff_user_id();
    // make details tab array and assign order,name,url,count
    $url = 'admin/sales_marketing/details/';
    $tabs = array(
        'details' => [
            'position' => 1,
            'name' => 'Details',
            'url' => $url . $id,
            'count' => '',
            'view' => 'sales_marketing/deals_details/index'
        ],
        'contact' => [
            'position' => 2,
            'name' => 'Contact',
            'url' => $url . $id . '/contact',
            'count' => total_rows('tbl_sam_calls', array('module' => "sam", 'module_field_id' => $id)),
            'view' => 'sales_marketing/deals_details/contact'
        ],
        'call' => [
            'position' => 2,
            'name' => 'Call',
            'url' => $url . $id . '/call',
            'count' => total_rows('tbl_sam_calls', array('module' => "sam", 'module_field_id' => $id)),
            'view' => 'sales_marketing/deals_details/call'
        ],
        'comments' => [
            'position' => 3,
            'name' => 'Comments',
            'url' => $url . $id . '/comments',
            'count' => total_rows('tbl_sam_comments', array('deal_id' => $id)),
            'view' => 'sales_marketing/deals_details/comments'
        ],
        'attachments' => [
            'position' => 4,
            'name' => 'Attachments',
            'url' => $url . $id . '/attachments',
            'count' => total_rows(db_prefix() . 'files', array('rel_type' => 'sam', 'rel_id' => $id)),
            'view' => 'sales_marketing/deals_details/attachments'
        ],
        'notes' => [
            'position' => 5,
            'name' => 'Notes',
            'url' => $url . $id . '/notes',
            'count' => '',
            'view' => 'sales_marketing/deals_details/notes'
        ],

        'tasks' => [
            'position' => 6,
            'name' => 'Tasks',
            'url' => $url . $id . '/tasks',
            'count' => total_rows(db_prefix() . 'tasks', array('rel_id' => $id, 'rel_type' => 'sam')),
            'view' => 'sales_marketing/deals_details/tasks'
        ],
        'mettings' => array(
            'position' => 7,
            'name' => 'Meetings',
            'url' => $url . $id . '/mettings',
            'count' => total_rows('tbl_sam_mettings', array('module_field_id' => $id)),
            'view' => 'sales_marketing/deals_details/mettings',
        ),
        'email' => array(
            'position' => 9,
            'name' => 'Email',
            'url' => $url . $id . '/email',
            'count' => total_rows('tbl_sam_email', array('deals_id' => $id)),
            'view' => 'sales_marketing/deals_details/email',
        ),
        'products' => array(
            'position' => 10,
            'name' => 'Products',
            'url' => $url . $id . '/products',
            'count' => total_rows('tbl_sam_items', array('deals_id' => $id)),
            'view' => 'sales_marketing/deals_details/deals_items_details',
        ),
        'reminders' => array(
            'position' => 11,
            'name' => 'Reminders',
            'url' => $url . $id . '/reminders',
            'count' => total_rows('tbl_sam_reminders', array('rel_id' => $id)),
            'view' => 'sales_marketing/deals_details/reminders',
        ),
        'proposals' => array(
            'position' => 12,
            'name' => 'Proposals',
            'url' => $url . $id . '/proposals',
            'count' => total_rows('tblproposals', (!is_admin() && staff_cant('view', 'proposals')) ? array('sam_id' => $id,'proposals.addedfrom'=>$staff_id) : array('sam_id' => $id)),
            'view' => 'sales_marketing/proposals/proposal_list',
        ),
        'contracts' => array(
            'position' => 13,
            'name' => 'Contracts',
            'url' => $url . $id . '/contracts',
            'count' => total_rows('tblcontracts', (!is_admin() && staff_cant('view', 'contracts')) ? array('sam_id' => $id,'contracts.addedfrom'=>$staff_id) : array('sam_id' => $id)),
            'view' => 'sales_marketing/contracts/contract_list',
        ),
        'invoices' => array(
            'position' => 14,
            'name' => 'Invoices',
            'url' => $url . $id . '/invoices',
            'count' => total_rows('tblinvoices', (!is_admin() && staff_cant('view', 'invoices')) ? array('sam_id' => $id, 'invoices.addedfrom'=>$staff_id) : array('sam_id' => $id)),
            'view' => 'sales_marketing/invoices/invoice_list',
        ),
        'payments' => array(
            'position' => 15,
            'name' => 'Payments',
            'url' => $url . $id . '/payments',
            'count' => getTotalPayments($id),
            'view' => 'sales_marketing/payments/payment_list',
        ),  
        'activites' => [
            'position' => 16,
            'name' => 'Activites',
            'url' => $url . $id . '/activites',
            'count' => total_rows('tbl_sam_activity_log', array('deal_id' => $id)),
            'view' => 'sales_marketing/deals_details/activites'
        ],
/*        'activites2' => [
            'position' => 12,
            'name' => 'Activity on Deal',
            'url' => $url . $id . '/activites2',
            'count' => total_rows('tbl_sam_transactions', array('sam_id' => $id)),
            'view' => 'sales_marketing/deals_details/deal_activities'
        ]                */
    );
    return apply_sam_filters('deals_details_tabs', $tabs);
}

function handle_sam_attachments($deal_id, $index_name = 'file', $form_activity = false): bool
{

    $uploaded_files = [];
    $path = get_upload_path_for_sam() . $deal_id . '/';
    $CI = &get_instance();
    $CI->load->model('sam_model');
    if (
        isset($_FILES[$index_name]['name'])
        && ($_FILES[$index_name]['name'] != ''
            || is_array($_FILES[$index_name]['name']) && count($_FILES[$index_name]['name']) > 0)
    ) {
        if (!is_array($_FILES[$index_name]['name'])) {
            $_FILES[$index_name]['name'] = [$_FILES[$index_name]['name']];
            $_FILES[$index_name]['type'] = [$_FILES[$index_name]['type']];
            $_FILES[$index_name]['tmp_name'] = [$_FILES[$index_name]['tmp_name']];
            $_FILES[$index_name]['error'] = [$_FILES[$index_name]['error']];
            $_FILES[$index_name]['size'] = [$_FILES[$index_name]['size']];
        }

        _file_attachments_index_fix($index_name);

        for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
            // Get the temp file path
            $tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];

            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                if (
                    _perfex_upload_error($_FILES[$index_name]['error'][$i])
                    || !_upload_extension_allowed($_FILES[$index_name]['name'][$i])
                ) {
                    continue;
                }

                _maybe_create_upload_path($path);
                $filename = unique_filename($path, $_FILES[$index_name]['name'][$i]);

                $newFilePath = $path . $filename;

                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $CI->sam_model->add_attachment_deals_database($deal_id, [[
                        'file_name' => $filename,
                        'filetype' => $_FILES[$index_name]['type'][$i],
                    ]], false, $form_activity);
                }
            }
        }
    }

    return true;
}

function sam_get_file_extension($file_name)
{
    return substr(strrchr($file_name, '.'), 1);
}

if (!function_exists('sam_validate_post_file')) {

    function sam_validate_post_file($file_name = "")
    {
        if (is_valid_file_to_upload($file_name)) {
            echo json_encode(array("success" => true));
            exit();
        } else {
            echo json_encode(array("success" => false, 'message' => lang('invalid_file_type') . " ($file_name)"));
            exit();
        }
    }
}
if (!function_exists('sam_is_valid_file_to_upload')) {

    function sam_is_valid_file_to_upload($file_name = "")
    {

        if (!$file_name)
            return false;

        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $file_formates = explode('|', config_item('allowed_files'));
        if (in_array($file_ext, $file_formates)) {
            return true;
        }
    }
}

if (!function_exists('sam_upload_file_to_temp')) {

    function sam_upload_file_to_temp()
    {
        if (!empty($_FILES)) {
            $temp_file = $_FILES['file']['tmp_name'];
            $file_name = $_FILES['file']['name'];

            if (!is_valid_file_to_upload($file_name))
                return false;

            $target_path = getcwd() . '/uploads/temp/';
            if (!is_dir($target_path)) {
                if (!mkdir($target_path, 0777, true)) {
                    die('Failed to create file folders.');
                }
            }
            $target_file = $target_path . $file_name;
            copy($temp_file, $target_file);
        }
    }
}
if (!function_exists('sam_move_temp_file')) {

    function sam_move_temp_file($file_name, $target_path, $related_to = "", $source_path = NULL, $static_file_name = "")
    {
        $new_filename = unique_filename($target_path, $file_name);
        //if not provide any source path we'll fi   nd the default path
        if (!$source_path) {
            $source_path = getcwd() . "/uploads/temp/" . $file_name;
        }

        //check destination directory. if not found try to create a new one
        if (!is_dir($target_path)) {
            if (!mkdir($target_path, 0777, true)) {
                die('Failed to create file folders.');
            }
        }

        //overwrite extisting logic and use static file name
        if ($static_file_name) {
            $new_filename = $static_file_name;
        }

        //check the file type is data or file. then copy to destination and remove temp file
        if (starts_with($source_path, "data")) {
            copy_text_based_image($source_path, $target_path . $new_filename);
            return $new_filename;
        } else {
            if (file_exists($source_path)) {
                copy($source_path, $target_path . $new_filename);
                unlink($source_path);
                return $new_filename;
            }
        }
        return false;
    }
}
if (!function_exists('sam_starts_with')) {

    function sam_starts_with($string, $needle)
    {
        $string = $string;
        return $needle === "" || strrpos($string, $needle, -strlen($string)) !== false;
    }
}
function sam_check_image_extension($image)
{
    $images_extentions = array("jpg", "JPG", "jpeg", "JPEG", "png", "PNG", "gif", "GIF", "bmp", "BMP");
    $image_parts = explode(".", $image);
    $image_end_part = end($image_parts);

    if (in_array($image_end_part, $images_extentions) == true) {
        return 1;
    } else {
        return 0;
    }
}

function sam_mime_content_type($filename)
{
    if (function_exists('mime_content_type'))
        return mime_content_type($filename);
    else if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME);
        $mimetype = finfo_file($finfo, $filename);
        return $mimetype;
    } else
        return get_mime_by_extension($filename);
}

function apply_sam_filters($hook_name, $value)
{
    return hooks()->apply_filters($hook_name, $value);
}

function sam_btn_edit_deals($uri)
{
    return anchor($uri, 'Edit', array('class' => "btn btn-primary btn-xs", 'title' => 'Edit', 'data-toggle' => 'tooltip', 'data-placement' => 'top'));
}

function sam_btn_update_deals()
{
    return "<button data-toggle='tooltip' title=" . lang('update') . " data-placement='top' type='submit'  class='btn btn-xs btn-success'><i class='fa fa-check'></i></button>";
}

function btn_cancel_sam($uri)
{
    return anchor($uri, '<i class="fa fa-times"></i>', array('class' => "btn btn-danger btn-xs", 'title' => lang('cancel'), 'data-toggle' => 'tooltip', 'data-placement' => 'top'));
}

function btn_add_sam()
{
    return "<button type='submit' name='add' value='1' class='btn btn-info'>" . lang('add') . "</button>";
}


function ajax_sam_anchor($url, $title = '', $attributes = '')
{
    $attributes["data-act"] = "ajax-request";
    $attributes["data-action-url"] = $url;
    return js_sam_anchor($title, $attributes);
}


function js_sam_anchor($title = '', $attributes = '')
{
    $title = (string)$title;

    $html_attributes = "";
    if (is_array($attributes)) {
        foreach ($attributes as $key => $value) {
            $html_attributes .= ' ' . $key . '="' . $value . '"';
        }
    }
    return '<strong data-toggle="tooltip" data-placement="top" style="cursor:pointer"' . $html_attributes . '>' . $title . '</strong>';
}


function sam_force_download($filename = '', $data = '', $set_mime = FALSE)
{
    if ($filename === '' or $data === '') {
        return;
    } elseif ($data === NULL) {
        if (!@is_file($filename) or ($filesize = @filesize($filename)) === FALSE) {
            return;
        }

        $filepath = $filename;
        $filename = explode('/', str_replace(DIRECTORY_SEPARATOR, '/', $filename));
        $filename = end($filename);
    } else {
        $filesize = strlen($data);
    }

    // Set the default MIME type to send
    $mime = 'application/octet-stream';

    $x = explode('.', $filename);
    $extension = end($x);

    if ($set_mime === TRUE) {
        if (count($x) === 1 or $extension === '') {
            /* If we're going to detect the MIME type,
             * we'll need a file extension.
             */
            return;
        }

        // Load the mime types
        $mimes =& get_mimes();

        // Only change the default MIME if we can find one
        if (isset($mimes[$extension])) {
            $mime = is_array($mimes[$extension]) ? $mimes[$extension][0] : $mimes[$extension];
        }
    }

    /* It was reported that browsers on Android 2.1 (and possibly older as well)
     * need to have the filename extension upper-cased in order to be able to
     * download it.
     *
     * Reference: http://digiblog.de/2011/04/19/android-and-the-download-file-headers/
     */
    if (count($x) !== 1 && isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/Android\s(1|2\.[01])/', $_SERVER['HTTP_USER_AGENT'])) {
        $x[count($x) - 1] = strtoupper($extension);
        $filename = implode('.', $x);
    }

    if ($data === NULL && ($fp = @fopen($filepath, 'rb')) === FALSE) {
        return;
    }

    // Clean output buffer
    if (ob_get_level() !== 0 && @ob_end_clean() === FALSE) {
        @ob_clean();
    }

    // Generate the server headers
    header('Content-Type: ' . $mime);
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Expires: 0');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . $filesize);
    header('Cache-Control: private, no-transform, no-store, must-revalidate');

    // If we have raw data - just dump it
    if ($data !== NULL) {
        exit($data);
    }

    // Flush 1MB chunks of data
    while (!feof($fp) && ($data = fread($fp, 1048576)) !== FALSE) {
        echo $data;
    }

    fclose($fp);
    exit;
}


/**
 * Task attachments upload array
 * Multiple task attachments can be upload if input type is array or dropzone plugin is used
 * @param mixed $deal_id task id
 * @param string $index_name attachments index, in different forms different index name is used
 * @return mixed
 */

function get_upload_path_for_sam()
{
    $dir = FCPATH . 'uploads/sales_marketing/';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    return $dir;
}

function handle_sam_attachments_array($deal_id, $index_name = 'attachments')
{
    $uploaded_files = [];

    $path = get_upload_path_for_sam() . $deal_id . '/';

    if (isset($_FILES[$index_name]['name'])
        && ($_FILES[$index_name]['name'] != '' || is_array($_FILES[$index_name]['name']) && count($_FILES[$index_name]['name']) > 0)) {
        if (!is_array($_FILES[$index_name]['name'])) {
            $_FILES[$index_name]['name'] = [$_FILES[$index_name]['name']];
            $_FILES[$index_name]['type'] = [$_FILES[$index_name]['type']];
            $_FILES[$index_name]['tmp_name'] = [$_FILES[$index_name]['tmp_name']];
            $_FILES[$index_name]['error'] = [$_FILES[$index_name]['error']];
            $_FILES[$index_name]['size'] = [$_FILES[$index_name]['size']];
        }

        _file_attachments_index_fix($index_name);
        for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
            // Get the temp file path
            $tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];

            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                if (_perfex_upload_error($_FILES[$index_name]['error'][$i])
                    || !_upload_extension_allowed($_FILES[$index_name]['name'][$i])) {
                    continue;
                }

                _maybe_create_upload_path($path);
                $filename = unique_filename($path, $_FILES[$index_name]['name'][$i]);
                $newFilePath = $path . $filename;

                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    array_push($uploaded_files, [
                        'file_name' => $filename,
                        'filetype' => $_FILES[$index_name]['type'][$i],
                    ]);

                    if (is_image($newFilePath)) {
                        create_img_thumb($path, $filename);
                    }
                }
            }
        }
    }

    if (count($uploaded_files) > 0) {
        return $uploaded_files;
    }

    return false;
}


function btn_view_sam($uri)
{
    return anchor($uri, '<span class="fa fa-list-alt"></span>', array('class' => "btn btn-info btn-xs", 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'View'));
}


function btn_delete_sam($uri, $text = null, $icon = null)
{
    $icons = '<i class="fa fa-trash-o"></i>';
    $title = _l('delete');
    $btn = 'btn';
    if (!empty($text) && empty($icon)) {
        $icons = '';
        $title = $text;
        $btn = 'text';
    }
    if (!empty($icon) && empty($text)) {
        $title = '';
    }
    return anchor($uri, $icons . ' ' . $title, array(
        'class' => "btn $btn-danger btn-xs deleteBtn", 'title' => $text, 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'onclick' => "return confirm('" . _l('delete_alert') . "');"
    ));
}


function tab_load_view_sam($all_tab, $active)
{
    $tab = array_filter($all_tab, function ($key) use ($active) {
        return $key == $active;
    }, ARRAY_FILTER_USE_KEY);
    if (count(array($tab)) > 0) {
        return $tab[$active]['view'];
    } else {
        return false;
    }
}


/**
 * Initializes the vendor customfield.
 *
 * @param string $custom_field The custom field
 */
function init_sam_custom_fields($custom_field = '')
{
    $select = '';
    if ($custom_field != '') {
        if ($custom_field->fieldto == 'sam') {
            $select = 'selected';
        }
    }

    $html = '<option value="sam" ' . $select . '>' . _l('deals') . '</option>';

    echo html_entity_decode($html);
}


/**
 * PO add table row
 * @param string $row
 * @param string $aRow
 * @return [type]
 */
function sam_add_table_row($row, $aRow)
{
    $CI = &get_instance();
    if ($aRow['rel_type'] == 'sam') {
        $deal = get_sam_row('tbl_sam', array('id' => $aRow['rel_id']));
        if ($deal) {
            $str = '<span class="hide"> - </span><a class="text-muted task-table-related" data-toggle="tooltip" title="' . _l('task_related_to') . '" href="' . admin_url(SAM_MODULE.'/details/' . $deal->id) . '">' . $deal->title . '</a><br />';
            $row[2] = $row[2] . $str;
        }
    }
    return $row;
}


function sam_get_relation_data($data, $obj)
{
    $type = $obj['type'];
    $rel_id = $obj['rel_id'];
    if ($type == 'sam') {
        if ($rel_id != '') {
            $data = get_sam_row('tbl_sam', array('id' => $rel_id));
        } else {
            $data = [];
        }
    }
    return $data;
}

/**
 * PO relation data
 * @param array $data
 * @param string $type
 * @param id $rel_id
 * @param array $q
 * @return array
 */
function sam_relation_data($data, $type, $rel_id, $q)
{

    if ($type == 'sam') {
        if ($rel_id != '') {
            $data = get_sam_row('tbl_sam', array('id' => $rel_id));
        } else {
            $data = [];
        }
    }
    return $data;
}

/**
 * task related to select
 * @param string $value
 * @return string
 */
function sam_related_to_select($value)
{

    $selected = '';
    if ($value == 'sam') {
        $selected = 'selected';
    }
    echo "<option value='sam' $selected>" . _l('deals') . '</option>';

}

function sam_global_search_result_output($output, $data)
{
    if ($data['type'] == 'sam') {
        $output = '<a href="' . admin_url(SAM_MODULE.'/details/' . $data['result']['id']) . '">' . $data['result']['title'] . '</a>';
    }

    return $output;
}

function sam_global_search_result_query($result, $q, $limit)
{
    $CI = &get_instance();
    if (has_permission('sam', '', 'view')) {
        // Goals
        $CI->db->select()->from('tbl_sam')->like('title', $q)->or_like('notes', $q)->limit($limit);
        $CI->db->order_by('title', 'ASC');

        $result[] = [
            'result' => $CI->db->get()->result_array(),
            'type' => 'sam',
            'search_heading' => _l('deals'),
        ];
    }

    return $result;
}

function check_timer_status($sam_id=null,$pipeline_id=null,$stage_id=null){
    $CI = &get_instance();
    $result = $CI->db->select('*')
                ->where('end_time', null)
                ->where('sam_id',$sam_id)
                ->where('pipeline_id',$pipeline_id)
                ->where('stage_id',$stage_id)
                ->where('staff_id',get_staff_user_id())
                ->where('deleted',0)
                ->get('tbl_sam_taskstimers')->row();
    if($result){
        return true;
    }            
    else{
        return false;
    }   
}

function get_one_timer($sam_id=null,$pipeline_id=null,$stage_id=null){
    $CI = &get_instance();
    $result = $CI->db->select('*')
                ->where('end_time', null)
                ->where('sam_id',$sam_id)
                ->where('pipeline_id',$pipeline_id)
                ->where('stage_id',$stage_id)
                ->where('staff_id',get_staff_user_id())
                ->where('deleted',0)
                ->get('tbl_sam_taskstimers')->row();
    if($result){
        return $result;
    }            
    else{
        return false;
    }   
}

//Method for creating the user's activity transactions
function add_activity_transactions($sam_id=0,$transaction_type=''){
    $CI = &get_instance();
    if($sam_id!=0){
        $staff_id = get_staff_user_id();
        $cond = array(
            'id'    => $sam_id
        );
        $sam_res = $CI->sam_model->getOneRecord("tbl_sam",$cond); 
        if($sam_res){
            $sam_res = $sam_res[0];
            //first check the transaction done for same day
            $cond = array(
                'sam_id'        => $sam_id,
                'staff_id'      => $staff_id,
                'customer_id'   => $sam_res['rel_id'],
                't_date'        => date('Y-m-d')
            );
            $record = $CI->sam_model->getOneRecord("tbl_sam_transactions",$cond);
            if(!$record){
                $transaction_data = array(
                    'sam_id'        => $sam_id,
                    'pipeline_id'   => $sam_res['pipeline_id'],
                    'stage_id'      => $sam_res['stage_id'],
                    'rel_type'      => $sam_res['rel_type'],
                    'customer_id'   => $sam_res['rel_id'],
                    'staff_id'      => $staff_id,
                    'transaction_type' => $transaction_type,
                    't_date'        => date("Y-m-d"),
                    'reg_date'      => date("Y-m-d h:i:s")
                );
                $CI->sam_model->add_activity_transaction($transaction_data);     
            }   
        }
        
    }
}

/**
 * Get deals and leads summary
 * @return array
 */
function get_deal_lead_summary($post_data=array(),$default=false)
{
    $CI = &get_instance();
    //Open
    $deals_statuses['open'] = array(   
        'name'          => 'Open',
        'statusorder'   => 1000,
        'color'         => '#0000FF',
        'isdefault'     => 1
    );
    //won
    $deals_statuses['won'] = array(
        'name'          => 'Won',
        'statusorder'   => 1001,
        'color'         => '#00FF00',
        'isdefault'     => 0
    );
     
    //lost
    $deals_statuses['lost'] = array(   
        'name'          => 'Lost',    
        'statusorder'   => 1002,
        'color'         => '#FF0000',
        'isdefault'     => 0
    ); 
    //lost
/*    $deals_statuses['total'] = array(   
        'name'          => 'Total',    
        'statusorder'   => 1003,
        'color'         => '#FF0000',
        'isdefault'     => 0
    ); */
    $chart_data = "";
    $deal_lead_status  = $CI->dashboard_model->deals_leads_status_stats($post_data,$default);
    //echo "<pre>"; print_r($deal_lead_status); exit;
    if($deal_lead_status){
        
        $chart = [
            'labels'   => [],
            'datasets' => [],
        ];

        $_data                         = [];
        $_data['data']                 = [];
        $_data['backgroundColor']      = [];
        $_data['hoverBackgroundColor'] = [];
        $_data['statusLink']           = [];
        

        //$result = get_deal_lead_summary();
        //echo "<pre>"; print_r($deal_lead_status); exit;
        $i=0;
        foreach($deal_lead_status as $status) { 
            $name = $status['name'];
            $total = $status['total'];
            if($name!='total'){
                if($status['sam_ids']!=""){
                    $sam_ids = urlencode($status['sam_ids']);
                } 
                else{
                    $sam_ids = "";
                }
                //$CI->session->set_userdata('sam_ids',$sam_ids);
                $color = $deals_statuses[$name]['color'];
                array_push($_data['statusLink'], admin_url(SAM_MODULE.'/index/null/'.$sam_ids)); 
                //array_push($_data['statusLink'], admin_url(SAM_MODULE.'?deal_status='.$name)); 
                array_push($chart['labels'], $name.': '.$total); 
                array_push($_data['backgroundColor'], $color);
                array_push($_data['hoverBackgroundColor'], adjust_color_brightness($color, -20));
                array_push($_data['data'], $total);
            }
            $i++;
        }

        $chart['datasets'][] = $_data;           
        //$chart_data = json_encode($chart);
        return $chart;
    }
}

function getPipelineNameById($id){
    $CI = &get_instance();
    if($id!=""){
        $res = $CI->dashboard_model->getPipelineNameById($id);
        if($res){
            return $res;
        }
        else{
            return "";
        }
    }
    else{
        return "";
    }
}

function getStaffTimesheets($post_data=array(),$default=false){
    $CI = &get_instance();
    $timesheets_data = $CI->dashboard_model->get_staff_timesheets($post_data,$default);
    $table1 = "";
    if($timesheets_data){
        foreach($timesheets_data as $val){
            $table1 .= "<tr>";
            $table1 .= "<td>".get_staff_full_name($val['staff_id'])."</td>"; 
            //$table .= "<td>".$val['pipeline_name']."</td>";
            $table1 .= "<td>"._l('time_h') . ": ".e(seconds_to_time_format($val['total_time_spent']))."</td>";
            //$table .= "<td></td>";
            $table1 .= "</tr>";
        }
    }
    return $table1;
    
}

function getActivityOnDeals($post_data=array(),$default=false){
    $CI = &get_instance();
    $leads_data = $CI->dashboard_model->get_leads_on_customer($post_data,$default);
    $table2 = "";
    if($leads_data){
        foreach($leads_data as $val){
            $table2 .= "<tr>";
            $table2 .= "<td>".get_staff_full_name($val['staff_id'])."</td>"; 
            $table2 .= "<td>".$val['total_activities']."</td>";
            $table2 .= "</tr>";
        }    
    }
    return $table2;
    
}
function getGroupsOnDeals($post_data=array(),$default=false){
    $CI = &get_instance();
    $groups_data = $CI->dashboard_model->get_groups_on_customer($post_data,$default);
    $table1 = "";
    if($groups_data){
        foreach($groups_data as $val){
            $table1 .= "<tr>";
            $table1 .= "<td>".$val['group_name']."</td>"; 
            $table1 .= "<td>".$val['total_customers']."</td>";
            $table1 .= "</tr>";
        }    
    }
    return $table1;
    
}

function getStatusOnDeals($post_data = array(), $default = false) {
    $CI = &get_instance();
    $deals_status_data = $CI->dashboard_model->get_deals_status_on_customer($post_data, $default);
    $table3 = ""; // Correct the variable name to $table3

    if ($deals_status_data) {
        foreach ($deals_status_data as $val) {
            $table3 .= "<tr>";
            // Apply color in the style attribute and make text bold
            $table3 .= "<td>" . $val['status_name'] . "</td>"; 
            $table3 .= "<td>" . $val['deal_count'] . "</td>"; // Display the count of each status
            $table3 .= "</tr>";
        }    
    }
    return $table3; 
}


function getProposalsDashboard($post_data=array(),$default=false){
    $CI = &get_instance();
    //get proposals
    $proposal_amount_table = "<tr style='font-size:3em'>";
    $proposal_count_table = "<tr style='font-size:3em'>";
    $proposal_res = $CI->dashboard_model->getDealProposals($post_data,$default);
    if($proposal_res){
        foreach($proposal_res as $val){
            $pro_status = $val['status'];
            $total = $val['total'];
            if($total==""){
                $total = 0;
            }
            if($pro_status=='total-sent-amount'){
                $proposal_amount_table .= "<td>$".$total."</td>";             
            }
            elseif($pro_status=='total-accepted-amount'){
                $proposal_amount_table .= "<td>$".$total."</td>";
            }
            elseif($pro_status=='total-sent-proposal'){
                $proposal_count_table .= "<td>".(int)$total."</td>";                                          
            }
            elseif($pro_status=='total-accepted-proposal'){
                $proposal_count_table .= "<td>".(int)$total."</td>";     
            }                           
        }     
    }
    $proposal_amount_table .= "</tr>";
    $proposal_count_table .= "</tr>"; 
    
    return $proposal_amount_table.'###'.$proposal_count_table;
    
}

function getTotalSpentTimeOnDeal($sam_id=0){
    $CI = &get_instance();
    $CI->load->model('sam_tasks_model');
    $result = $CI->sam_tasks_model->getTotalSpentTimeOnDeal($sam_id);
    if($result){
        return $result->total_time_spent;    
    }
    else{
        return "";
    }
    
}

function getSpentTimeOnCustomer($post_data=array(),$default=false){
    $CI = &get_instance();
    
    $timesheets_data = $CI->dashboard_model->getSpentTimeOnCustomer($post_data,$default);
    $table_data = "";
    if($timesheets_data){
        foreach($timesheets_data as $val){
            $client_data = get_client($val['customer_id']);
            $client_name = "";
            if($client_data){
                $client_name = $client_data->company; 
            }
            $table_data .= "<tr>";
            $table_data .= "<td><a href=\"".admin_url(SAM_MODULE.'/details/'.$val['sam_id'])."\" target=\"_blank\">".$client_name."</a></td>"; 
            //$table .= "<td>".$val['pipeline_name']."</td>";
            $table_data .= "<td>"._l('time_h') . ": ".e(seconds_to_time_format($val['total_time_spent']))."</td>";
            $table_data .= "<td>AED".$val['total_deal_value']."</td>";
            $table_data .= "</tr>";
        }
    }
    return $table_data;
    
}

function getSpentTimeOnCustomer2($post_data=array(),$default=false){
    $CI = &get_instance();
    
    $data = $CI->dashboard_model->getSpentTimeOnCustomer($post_data,$default);
    return $data;
    
}

function timeRemainString($date, $localization = [])
{
    $defaultLocalization['time_just_now']  = 'just now';
    $defaultLocalization['time_minute']    = 'one minute left';
    $defaultLocalization['time_minutes']   = '%s minutes left';
    $defaultLocalization['time_hour']      = 'an hour left';
    $defaultLocalization['time_hours']     = '%s hrs left';
    $defaultLocalization['time_yesterday'] = 'yesterday';
    $defaultLocalization['time_days']      = '%s days left';
    $defaultLocalization['time_week']      = 'a week left';
    $defaultLocalization['time_weeks']     = '%s weeks left';
    $defaultLocalization['time_month']     = 'a month left';
    $defaultLocalization['time_months']    = '%s months left';
    $defaultLocalization['time_year']      = 'one year left';
    $defaultLocalization['time_years']     = '%s years left';
    
    $defaultLocalization['time_ago_just_now']  = '';
    $defaultLocalization['time_ago_minute']    = 'one minute ago';
    $defaultLocalization['time_ago_minutes']   = '%s minutes ago';
    $defaultLocalization['time_ago_hour']      = 'an hour ago';
    $defaultLocalization['time_ago_hours']     = '%s hrs ago';
    $defaultLocalization['time_ago_yesterday'] = 'yesterday';
    $defaultLocalization['time_ago_days']      = '%s days ago';
    $defaultLocalization['time_ago_week']      = 'a week ago';
    $defaultLocalization['time_ago_weeks']     = '%s weeks ago';
    $defaultLocalization['time_ago_month']     = 'a month ago';
    $defaultLocalization['time_ago_months']    = '%s months ago';
    $defaultLocalization['time_ago_year']      = 'one year ago';
    $defaultLocalization['time_ago_years']     = '%s years ago';
    

    $localization = $defaultLocalization;

    $time_ago     = strtotime($date);
    $cur_time     = time();
    $time_elapsed = $time_ago - $cur_time;
    $seconds      = $time_elapsed;
    $minutes      = round($time_elapsed / 60);
    $hours        = round($time_elapsed / 3600);
    $days         = round($time_elapsed / 86400);
    $weeks        = round($time_elapsed / 604800);
    $months       = round($time_elapsed / 2600640);
    $years        = round($time_elapsed / 31207680);

    //check for the passed time
    if($time_elapsed < 0){ 
        $time_elapsed = $cur_time - $time_ago;
        $seconds      = $time_elapsed; 
        $minutes      = round($time_elapsed / 60);
        $hours        = round($time_elapsed / 3600);
        $days         = round($time_elapsed / 86400);
        $weeks        = round($time_elapsed / 604800);
        $months       = round($time_elapsed / 2600640);
        $years        = round($time_elapsed / 31207680); 
        //return $days;
        if($days <= 0){
            // Seconds
            if ($seconds <= 60) {
                return $localization['time_ago_just_now'];
            }

            //Minutes
            elseif ($minutes <= 60) {
                if ($minutes == 1) {
                    return $localization['time_ago_minute'];
                }

                return sprintf($localization['time_ago_minutes'], $minutes);
            }
            //Hours
            elseif ($hours <= 24) {
                if ($hours == 1) {
                    return $localization['time_ago_hour'];
                }

                return sprintf($localization['time_ago_hours'], $hours);
            }    
        }   
        else{
            if ($days == 1) {
                return $localization['time_yesterday'];
            }

            return sprintf($localization['time_ago_days'], $days);    
        } 
    }
    //remain time
    else{
        if($days <= 0){
            // Seconds
            if ($seconds <= 60) {
                return $localization['time_just_now'];
            }

            //Minutes
            elseif ($minutes <= 60) {
                if ($minutes == 1) {
                    return $localization['time_minute'];
                }

                return sprintf($localization['time_minutes'], $minutes);
            }
            //Hours
            elseif ($hours <= 24) {
                if ($hours == 1) {
                    return $localization['time_hour'];
                }

                return sprintf($localization['time_hours'], $hours);
            }    
        }
        else{
            if ($days == 1) {
                return $localization['time_yesterday'];
            }

            return sprintf($localization['time_days'], $days);            
        } 
    }
}


function getAllRemindersOnDashboard($post_data=array(),$default=false){
    $CI = &get_instance();
    
    $reminder_data = $CI->dashboard_model->getAllRemindersOnDashboard($post_data,$default);
    $table_data = "";
    $current_time = time();
    if($reminder_data){
        foreach($reminder_data as $val){
            $client_data = get_client($val['customer_id']);
            $client_name = "";
            if($client_data){
                $client_name = $client_data->company; 
            }
            $date = $val['date'];
            $trstyle = "";
            $tdstyle = "";
            if($current_time > strtotime($date)){
                $trstyle = 'style="background-color:#ff0000c4;font-weight:bold"';
                $tdstyle = 'style="color:#fff"';
            }
            
            $table_data .= "<tr $trstyle>";
            $table_data .= "<td><a href=\"".admin_url(SAM_MODULE.'/details/'.$val['sam_id'])."\" target=\"_blank\" $tdstyle>".$client_name."</a></td>"; 
            $table_data .= "<td $tdstyle>".get_staff_full_name($val['staff'])."</td>"; 
            $table_data .= "<td $tdstyle>".$val['title']."</td>"; 
            $table_data .= "<td $tdstyle>".$val['date']."</td>"; 
            $table_data .= "<td $tdstyle>".timeRemainString($date)."</td>";
            $table_data .= "</tr>";
        }
    }
    return $table_data;
    
}

function getAllRemindersOnDashboard2($post_data=array(),$default=false){
    $CI = &get_instance();
    $reminder_data = $CI->dashboard_model->getAllRemindersOnDashboard($post_data,$default);
    return $reminder_data;
    
}

function getTotalPayments($sam_id=""){
    $CI = &get_instance();
    $result = $CI->sam_model->getPaymentRecords($sam_id,'', []);
    if($result){
        return count($result);
    }
    else{
        return "";
    }
    
}

function sam_render_table2($data,$where = null, $where_in = null)
{

    $CI = &get_instance();
    $CI->load->model('datatabless');
    $output = array(
        "draw" => intval($_POST["draw"]),
        "iTotalRecords" => $CI->datatabless->get_all_data($where, $where_in),
        "iTotalDisplayRecords" => $CI->datatabless->get_filtered_data($where, $where_in),
        "aaData" => $data
    );
    echo json_encode($output);
    exit();
}

function isMobileDevice(){
    $useragent=$_SERVER['HTTP_USER_AGENT'];

    if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
    {
        return 1;    
    }
    else{
        return 0;
    }
}

function getDealProposals($where=[]){
    $CI = &get_instance();
    $CI->load->model('sam_proposals_model');
    $result = $CI->sam_proposals_model->get('',$where);
    if($result){
        return $result[0];
    }
    else{
        return false;
    }      
}          

function IsRelatedDataExist($tablename='',$fields="",$cond=[]){
    $CI = &get_instance();
    $CI->load->model('sam_model');
    $result = $CI->sam_model->getAllRecords($tablename,$fields,$cond);
    if($result){     
        return true;
    }
    else{
        return false;
    }
    
}
    