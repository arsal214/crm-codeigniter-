<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Get proposal short_url
 * @since  Version 2.7.3
 * @param  object $proposal
 * @return string Url
 */
function get_proposal_shortlink($proposal)
{
    $long_url = site_url(SAM_MODULE."/proposal/{$proposal->sam_id}/{$proposal->id}/{$proposal->hash}");
    if (!get_option('bitly_access_token')) {
        return $long_url;
    }

    // Check if proposal has short link, if yes return short link
    if (!empty($proposal->short_link)) {
        return $proposal->short_link;
    }

    // Create short link and return the newly created short link
    $short_link = app_generate_short_link([
        'long_url' => $long_url,
        'title'    => format_proposal_number($proposal->id),
    ]);

    if ($short_link) {
        $CI = &get_instance();
        $CI->db->where('id', $proposal->id);
        $CI->db->update(db_prefix() . '_sam_proposals', [
            'short_link' => $short_link,
        ]);

        return $short_link;
    }

    return $long_url;
}

/**
 * Check if proposal email template for expiry reminders is enabled
 * @return boolean
 */
function is_proposals_email_expiry_reminder_enabled()
{
    return total_rows(db_prefix() . 'emailtemplates', ['slug' => 'proposal-expiry-reminder', 'active' => 1]) > 0;
}

/**
 * Check if there are sources for sending proposal expiry reminders
 * Will be either email or SMS
 * @return boolean
 */
function is_proposals_expiry_reminders_enabled()
{
    return is_proposals_email_expiry_reminder_enabled() || is_sms_trigger_active(SMS_TRIGGER_PROPOSAL_EXP_REMINDER);
}

/**
 * Return proposal status color class based on twitter bootstrap
 * @param  mixed  $id
 * @param  boolean $replace_default_by_muted
 * @return string
 */
function proposal_status_color_class($id, $replace_default_by_muted = false)
{
    if ($id == 1) {
        $class = 'default';
    } elseif ($id == 2) {
        $class = 'danger';
    } elseif ($id == 3) {
        $class = 'success';
    } elseif ($id == 4 || $id == 5) {
        // status sent and revised
        $class = 'info';
    } elseif ($id == 6) {
        $class = 'default';
    }
    if ($class == 'default') {
        if ($replace_default_by_muted == true) {
            $class = 'muted';
        }
    }

    return $class;
}
/**
 * Format proposal status with label or not
 * @param  mixed  $status  proposal status id
 * @param  string  $classes additional label classes
 * @param  boolean $label   to include the label or return just translated text
 * @return string
 */
function format_proposal_status($status, $classes = '', $label = true)
{
    $id = $status;
    if ($status == 1) {
        $status      = _l('proposal_status_open');
        $label_class = 'default';
    } elseif ($status == 2) {
        $status      = _l('proposal_status_declined');
        $label_class = 'danger';
    } elseif ($status == 3) {
        $status      = _l('proposal_status_accepted');
        $label_class = 'success';
    } elseif ($status == 4) {
        $status      = _l('proposal_status_sent');
        $label_class = 'info';
    } elseif ($status == 5) {
        $status      = _l('proposal_status_revised');
        $label_class = 'info';
    } elseif ($status == 6) {
        $status      = _l('proposal_status_draft');
        $label_class = 'default';
    }

    if ($label == true) {
        return '<span class="label label-' . $label_class . ' ' . $classes . ' s-status proposal-status-' . $id . '">' . $status . '</span>';
    }

    return $status;
}

/**
 * Function that format proposal number based on the prefix option and the proposal id
 * @param  mixed $id proposal id
 * @return string
 */
function format_proposal_number($id)
{
    $format = get_option('proposal_number_prefix') . str_pad($id, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);

    return hooks()->apply_filters('proposal_number_format', $format, $id);
}

/**
 * Calculate proposal percent by status
 * @param  mixed $status          proposal status
 * @param  mixed $total_estimates in case the total is calculated in other place
 * @return array
 */
function get_proposals_percent_by_status($status, $total_proposals = '')
{
    $has_permission_view                 = staff_can('view',  'proposals');
    $has_permission_view_own             = staff_can('view_own',  'proposals');
    $allow_staff_view_proposals_assigned = get_option('allow_staff_view_proposals_assigned');
    $staffId                             = get_staff_user_id();

    $whereUser = '';
    if (!$has_permission_view) {
        if ($has_permission_view_own) {
            $whereUser = '(addedfrom=' . $staffId;
            if ($allow_staff_view_proposals_assigned == 1) {
                $whereUser .= ' OR assigned=' . $staffId;
            }
            $whereUser .= ')';
        } else {
            $whereUser .= 'assigned=' . $staffId;
        }
    }

    if (!is_numeric($total_proposals)) {
        $total_proposals = total_rows(db_prefix() . '_sam_proposals', $whereUser);
    }

    $data            = [];
    $total_by_status = 0;
    $where           = 'status=' . get_instance()->db->escape_str($status);
    if (!$has_permission_view) {
        $where .= ' AND (' . $whereUser . ')';
    }

    $total_by_status = total_rows(db_prefix() . '_sam_proposals', $where);
    $percent         = ($total_proposals > 0 ? number_format(($total_by_status * 100) / $total_proposals, 2) : 0);

    $data['total_by_status'] = $total_by_status;
    $data['percent']         = $percent;
    $data['total']           = $total_proposals;

    return $data;
}

/**
 * Function that will search possible proposal templates in applicaion/views/admin/proposal/templates
 * Will return any found files and user will be able to add new template
 * @return array
 */
function get_proposal_templates()
{
    $proposal_templates = [];
    if (is_dir(VIEWPATH . 'admin/proposals/templates')) {
        foreach (list_files(VIEWPATH . 'admin/proposals/templates') as $template) {
            $proposal_templates[] = $template;
        }
    }

    return $proposal_templates;
}

/**
 * Check if staff member have assigned proposals / added as sale agent
 * @param  mixed $staff_id staff id to check
 * @return boolean
 */
function staff_has_assigned_proposals($staff_id = '')
{
    $CI       = &get_instance();
    $staff_id = is_numeric($staff_id) ? $staff_id : get_staff_user_id();
    $cache    = $CI->app_object_cache->get('staff-total-assigned-proposals-' . $staff_id);
    if (is_numeric($cache)) {
        $result = $cache;
    } else {
        $result = total_rows(db_prefix() . 'proposals', ['assigned' => $staff_id]);
        $CI->app_object_cache->add('staff-total-assigned-proposals-' . $staff_id, $result);
    }

    return $result > 0 ? true : false;
}

function get_proposals_sql_where_staff($staff_id)
{
    $has_permission_view_own            = staff_can('view_own',  'proposals');
    $allow_staff_view_invoices_assigned = get_option('allow_staff_view_proposals_assigned');
    $CI                                 = &get_instance();

    $whereUser = '';
    if ($has_permission_view_own) {
        $whereUser = '((' . db_prefix() . '_sam_proposals.addedfrom=' . $CI->db->escape_str($staff_id) . ' AND ' . db_prefix() . '_sam_proposals.addedfrom IN (SELECT staff_id FROM ' . db_prefix() . 'staff_permissions WHERE feature = "proposals" AND capability="view_own"))';
        if ($allow_staff_view_invoices_assigned == 1) {
            $whereUser .= ' OR assigned=' . $CI->db->escape_str($staff_id);
        }
        $whereUser .= ')';
    } else {
        $whereUser .= 'assigned=' . $CI->db->escape_str($staff_id);
    }

    return $whereUser;
}



################# NEW METHODS #################


/**
 * Check if staff member can view proposal
 * @param  mixed $id proposal id
 * @param  mixed $staff_id
 * @return boolean
 */
function sam_user_can_view_proposal($id, $staff_id = false)
{
    $CI = &get_instance();

    $staff_id = $staff_id ? $staff_id : get_staff_user_id();

/*    if (has_permission('proposals', $staff_id, 'view')) {
        return true;
    }*/

    $CI->db->select('id, addedfrom, assigned');
    //$CI->db->from(db_prefix() . '_sam_proposals');
    $CI->db->from(db_prefix() . 'proposals');
    $CI->db->where('id', $id);
    $proposal = $CI->db->get()->row();

    //if($proposal->addedfrom == $staff_id && get_option('allow_staff_view_proposals_assigned') == 1)
    if($proposal->addedfrom == $staff_id)
    {
        return true;
    }

    return false;
}

/**
 * Add new item do database, used for proposals,estimates,credit notes,invoices
 * This is repetitive action, that's why this function exists
 * @param array $item     item from $_POST
 * @param mixed $rel_id   relation id eq. invoice id
 * @param string $rel_type relation type eq invoice
 */
function sam_add_new_sales_item_post($item, $rel_id, $rel_type)
{
    $custom_fields = false;

    if (isset($item['custom_fields'])) {
        //$custom_fields = $item['custom_fields'];
    }

    $CI = &get_instance();

    $CI->db->insert(db_prefix() . '_sam_itemable', [
                    'description'      => $item['description'],
                    'long_description' => nl2br($item['long_description']),
                    'qty'              => $item['qty'],
                    'rate'             => number_format($item['rate'], get_decimal_places(), '.', ''),
                    'rel_id'           => $rel_id,
                    'rel_type'         => $rel_type,
                    'item_order'       => $item['order'],
                    'unit'             => $item['unit'],
                ]);

    $id = $CI->db->insert_id();

    if ($custom_fields !== false) {
        //handle_custom_fields_post($id, $custom_fields);
    }

    return $id;
}

/**
 * Function used for sales eq. invoice, estimate, proposal, credit note
 * @param  mixed $item_id   item id
 * @param  array $post_item $item from $_POST
 * @param  mixed $rel_id    rel_id
 * @param  string $rel_type  where this item tax is related
 */
function sam_maybe_insert_post_item_tax($item_id, $post_item, $rel_id, $rel_type)
{
    $affectedRows = 0;
    if (isset($post_item['taxname']) && is_array($post_item['taxname'])) {
        $CI = &get_instance();
        foreach ($post_item['taxname'] as $taxname) {
            if ($taxname != '') {
                $tax_array = explode('|', $taxname);
                if (isset($tax_array[0]) && isset($tax_array[1])) {
                    $tax_name = trim($tax_array[0]);
                    $tax_rate = trim($tax_array[1]);
                    if (total_rows(db_prefix() . '_sam_item_tax', [
                        'itemid' => $item_id,
                        'taxrate' => $tax_rate,
                        'taxname' => $tax_name,
                        'rel_id' => $rel_id,
                        'rel_type' => $rel_type,
                    ]) == 0) {
                        $CI->db->insert(db_prefix() . '_sam_item_tax', [
                                'itemid'   => $item_id,
                                'taxrate'  => $tax_rate,
                                'taxname'  => $tax_name,
                                'rel_id'   => $rel_id,
                                'rel_type' => $rel_type,
                        ]);
                        $affectedRows++;
                    }
                }
            }
        }
    }

    return $affectedRows > 0 ? true : false;
}

/**
 * Get all items by type eq. invoice, proposal, estimates, credit note
 * @param  string $type rel_type value
 * @return array
 */
function sam_get_items_by_type($type, $id)
{
    $CI = &get_instance();
    $CI->db->select();
    $CI->db->from(db_prefix() . '_sam_itemable');
    $CI->db->where('rel_id', $id);
    $CI->db->where('rel_type', $type);
    $CI->db->order_by('item_order', 'asc');

    return $CI->db->get()->result_array();
}

function sam_parse_proposal_content_merge_fields($proposal)
{
    $id = is_array($proposal) ? $proposal['id'] : $proposal->id;
    $CI = &get_instance();

    $CI->load->library('merge_fields/proposals_merge_fields');
    $CI->load->library('merge_fields/other_merge_fields');

    $content = is_array($proposal) ? $proposal['content'] : $proposal->content;
    if ($content === null) {
        return $proposal;
    }
    
    if ($proposal->currency != 0) {
        $currency = get_currency($proposal->currency);
    } else {
        $currency = get_base_currency();
    }

    $merge_fields = [];
    $merge_fields['{proposal_id}']          = $proposal->id;
    $merge_fields['{proposal_number}']      = e(format_proposal_number($proposal->id));
    $merge_fields['{proposal_link}']        = site_url(SAM_MODULE.'/proposal/' .$proposal->sam_id .'/'. $proposal->id . '/' . $proposal->hash);
    $merge_fields['{proposal_subject}']     = e($proposal->subject);
    $merge_fields['{proposal_total}']       = e(app_format_money($proposal->total, $currency));
    $merge_fields['{proposal_subtotal}']    = e(app_format_money($proposal->subtotal, $currency));
    $merge_fields['{proposal_open_till}']   = e(_d($proposal->open_till));
    $merge_fields['{proposal_proposal_to}'] = e($proposal->proposal_to);
    $merge_fields['{proposal_address}']     = e($proposal->address);
    $merge_fields['{proposal_email}']       = e($proposal->email);
    $merge_fields['{proposal_phone}']       = e($proposal->phone);

    $merge_fields['{proposal_city}']       = e($proposal->city);
    $merge_fields['{proposal_state}']      = e($proposal->state);
    $merge_fields['{proposal_zip}']        = e($proposal->zip);
    //$merge_fields['{proposal_country}']    = e($proposal->short_name);
    $merge_fields['{proposal_assigned}']   = e(get_staff_full_name($proposal->assigned));
    $merge_fields['{proposal_short_url}']  = get_proposal_shortlink($proposal);
    $merge_fields['{proposal_created_at}'] = e(_dt($proposal->datecreated));
    $merge_fields['{proposal_date}']       = e(_d($proposal->date));
    
    //$merge_fields = array_merge($merge_fields, $CI->proposals_merge_fields->format($id));
    $merge_fields = array_merge($merge_fields, $CI->other_merge_fields->format());
    //echo "<pre>"; print_r($merge_fields); exit;
    foreach ($merge_fields as $key => $val) {
        if (stripos($content, $key) !== false) {
            $content = str_ireplace($key, $val, $content);
        } else {
            $content = str_ireplace($key, '', $content);
        }
    }
    //echo "<pre>"; print_r($content); exit;
    if (is_array($proposal)) {
        $proposal['content'] = $content;
    } else {
        $proposal->content = $content;
    }
    return $proposal;
}

/**
* Function that update total tax in sales table eq. invoice, proposal, estimates, credit note
* @param  mixed $id
* @return void
*/
function sam_update_sales_total_tax_column($id, $type, $table)
{
    $CI = &get_instance();
    $CI->db->select('discount_percent, discount_type, discount_total, subtotal');
    $CI->db->from($table);
    $CI->db->where('id', $id);

    $data = $CI->db->get()->row();

    $items = sam_get_items_by_type($type, $id);

    $total_tax         = 0;
    $taxes             = [];
    $_calculated_taxes = [];

    $func_taxes = 'sam_get_' . $type . '_item_taxes';

    foreach ($items as $item) {
        $item_taxes = call_user_func($func_taxes, $item['id']);
        if (count($item_taxes) > 0) {
            foreach ($item_taxes as $tax) {
                $calc_tax     = 0;
                $tax_not_calc = false;
                if (!in_array($tax['taxname'], $_calculated_taxes)) {
                    array_push($_calculated_taxes, $tax['taxname']);
                    $tax_not_calc = true;
                }

                if ($tax_not_calc == true) {
                    $taxes[$tax['taxname']]          = [];
                    $taxes[$tax['taxname']]['total'] = [];
                    array_push($taxes[$tax['taxname']]['total'], (($item['qty'] * $item['rate']) / 100 * $tax['taxrate']));
                    $taxes[$tax['taxname']]['tax_name'] = $tax['taxname'];
                    $taxes[$tax['taxname']]['taxrate']  = $tax['taxrate'];
                } else {
                    array_push($taxes[$tax['taxname']]['total'], (($item['qty'] * $item['rate']) / 100 * $tax['taxrate']));
                }
            }
        }
    }

    foreach ($taxes as $tax) {
        $total = array_sum($tax['total']);
        if ($data->discount_percent != 0 && $data->discount_type == 'before_tax') {
            $total_tax_calculated = ($total * $data->discount_percent) / 100;
            $total                = ($total - $total_tax_calculated);
        } elseif ($data->discount_total != 0 && $data->discount_type == 'before_tax') {
            $t     = ($data->discount_total / $data->subtotal) * 100;
            $total = ($total - $total * $t / 100);
        }
        $total_tax += $total;
    }

    $CI->db->where('id', $id);
    $CI->db->update($table, [
            'total_tax' => $total_tax,
    ]);
}

/**
 * Function that return proposal item taxes based on passed item id
 * @param  mixed $itemid
 * @return array
 */
function get_sam_proposal_item_taxes($itemid)
{
    $CI = &get_instance();
    $CI->db->where('itemid', $itemid);
    $CI->db->where('rel_type', 'sam_proposal');
    $taxes = $CI->db->get(db_prefix() . '_sam_item_tax')->result_array();
    $i     = 0;
    foreach ($taxes as $tax) {
        $taxes[$i]['taxname'] = $tax['taxname'] . '|' . $tax['taxrate'];
        $i++;
    }

    return $taxes;
}

/**
 * Check if proposal hash is equal
 * @param  mixed $id   proposal id
 * @param  string $hash proposal hash
 * @return void
 */
function sam_check_proposal_restrictions($id, $hash)
{
    $CI = &get_instance();
    $CI->load->model('sam_proposals_model');
    if (!$hash || !$id) {
        show_404();
    }
    $proposal = $CI->sam_proposals_model->get($id);
    if (!$proposal || ($proposal->hash != $hash)) {
        show_404();
    }
}

/**
 * Function that generates proposal pdf for admin and clients area
 * @param  object $proposal
 * @param  string $tag      tag for bulk pdf exporter
 * @return object
 */
function sam_proposal_pdf($proposal, $tag = '')
{    
    //return app_pdf('proposal', LIBSPATH . 'pdf/Proposal_pdf1', $proposal, $tag);
    $dir_path = substr(__DIR__,0,strlen(__DIR__)-7);
    return app_pdf('proposal', $dir_path.'/libraries/pdf/Proposal_pdf', $proposal, $tag);
}

/**
 * Function that return proposal item taxes based on passed item id
 * @param  mixed $itemid
 * @return array
 */
function sam_get_proposal_item_taxes($itemid)
{
    $CI = &get_instance();
    $CI->db->where('itemid', $itemid);
    //$CI->db->where('rel_type', 'sam_proposal');
    $CI->db->where('rel_type', 'proposal');
    //$taxes = $CI->db->get(db_prefix() . '_sam_item_tax')->result_array();
    $taxes = $CI->db->get(db_prefix() . 'item_tax')->result_array();
    $i     = 0;
    foreach ($taxes as $tax) {
        $taxes[$i]['taxname'] = $tax['taxname'] . '|' . $tax['taxrate'];
        $i++;
    }

    return $taxes;
}

/**
 * Function that return estimate item taxes based on passed item id
 * @param  mixed $itemid
 * @return array
 */
function sam_get_estimate_item_taxes($itemid)
{
    $CI = &get_instance();
    $CI->db->where('itemid', $itemid);
    $CI->db->where('rel_type', 'estimate');
    $taxes = $CI->db->get(db_prefix() . '_sam_item_tax')->result_array();
    $i     = 0;
    foreach ($taxes as $tax) {
        $taxes[$i]['taxname'] = $tax['taxname'] . '|' . $tax['taxrate'];
        $i++;
    }

    return $taxes;
}

/**
 * When item is removed eq from invoice will be stored in removed_items in $_POST
 * With foreach loop this function will remove the item from database and it's taxes
 * @param  mixed $id       item id to remove
 * @param  string $rel_type item relation eq. invoice, estimate
 * @return boolena
 */
function sam_handle_removed_sales_item_post($id, $rel_type)
{
    $CI = &get_instance();

    $CI->db->where('id', $id);
    $CI->db->delete(db_prefix() . '_sam_itemable');
    if ($CI->db->affected_rows() > 0) {
        sam_delete_taxes_from_item($id, $rel_type);

        $CI->db->where('relid', $id);
        $CI->db->where('fieldto', 'items');
        //$CI->db->delete(db_prefix() . 'customfieldsvalues');

        return true;
    }

    return false;
}

/**
 * Remove taxes from item
 * @param  mixed $item_id  item id
 * @param  string $rel_type relation type eq. invoice, estimate etc.
 * @return boolean
 */
function sam_delete_taxes_from_item($item_id, $rel_type)
{
    $CI = &get_instance();
    $CI->db->where('itemid', $item_id)
    ->where('rel_type', $rel_type)
    ->delete(db_prefix() . '_sam_item_tax');

    return $CI->db->affected_rows() > 0 ? true : false;
}

/**
 * Update sales item from $_POST, eq invoice item, estimate item
 * @param  mixed $item_id item id to update
 * @param  array $data    item $_POST data
 * @param  string $field   field is require to be passed for long_description,rate,item_order to do some additional checkings
 * @return boolean
 */
function sam_update_sales_item_post($item_id, $data, $field = '')
{
    $update = [];
    if ($field !== '') {
        if ($field == 'long_description') {
            $update[$field] = nl2br($data[$field]);
        } elseif ($field == 'rate') {
            $update[$field] = number_format($data[$field], get_decimal_places(), '.', '');
        } elseif ($field == 'item_order') {
            $update[$field] = $data['order'];
        } else {
            $update[$field] = $data[$field];
        }
    } else {
        $update = [
            'item_order'       => $data['order'],
            'description'      => $data['description'],
            'long_description' => nl2br($data['long_description']),
            'rate'             => number_format($data['rate'], get_decimal_places(), '.', ''),
            'qty'              => $data['qty'],
            'unit'             => $data['unit'],
        ];
    }

    $CI = &get_instance();
    $CI->db->where('id', $item_id);
    $CI->db->update(db_prefix() . '_sam_itemable', $update);

    return $CI->db->affected_rows() > 0 ? true : false;
}