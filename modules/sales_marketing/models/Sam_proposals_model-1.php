<?php

use app\services\utilities\Arr;
use app\services\AbstractKanban;
use app\services\proposals\ProposalsPipeline;

defined('BASEPATH') or exit('No direct script access allowed');

class Sam_proposals_model extends App_Model
{
    private $statuses;

    private $copy = false;

    public function __construct()
    {
        parent::__construct();
        $this->statuses = hooks()->apply_filters('before_set_proposal_statuses', [
            6,
            4,
            1,
            5,
            2,
            3,
        ]);
    }

    public function get_statuses()
    {
        return $this->statuses;
    }

    public function get_sale_agents()
    {
        return $this->db->query('SELECT DISTINCT(assigned) as sale_agent FROM ' . db_prefix() . '_sam_proposals WHERE assigned != 0')->result_array();
    }

    public function get_proposals_years()
    {
        return $this->db->query('SELECT DISTINCT(YEAR(date)) as year FROM ' . db_prefix() . '_sam_proposals')->result_array();
    }

    /**
     * Inserting new proposal function
     * @param mixed $data $_POST data
     */
    public function add($data)
    {
        $data['allow_comments'] = isset($data['allow_comments']) ? 1 : 0;

        $save_and_send = isset($data['save_and_send']);

        $tags = isset($data['tags']) ? $data['tags'] : '';

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $estimateRequestID = false;
        if (isset($data['estimate_request_id'])) {
            $estimateRequestID = $data['estimate_request_id'];
            unset($data['estimate_request_id']);
        }

        //$data['address'] = trim($data['address']);
        //$data['address'] = nl2br($data['address']);

        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['addedfrom']   = get_staff_user_id();
        $data['hash']        = app_generate_hash();

        if (empty($data['rel_type'])) {
            unset($data['rel_type']);
            unset($data['rel_id']);
        } else {
            if (empty($data['rel_id'])) {
                unset($data['rel_type']);
                unset($data['rel_id']);
            }
        }

        $items = [];
        if (isset($data['newitems'])) {
            $items = $data['newitems'];
            unset($data['newitems']);
        }

        if ($this->copy == false) {
            $data['content'] = '{proposal_items}';
        }

        if (isset($data['rel_id'], $data['rel_type']) && $data['rel_type'] !== 'customer') {
            //$data['project_id'] = null;
        }

        $hook = hooks()->apply_filters('before_create_proposal', [
            'data'  => $data,
            'items' => $items,
        ]);

        $data  = $hook['data'];
        $items = $hook['items'];

        $this->db->insert(db_prefix() . '_sam_proposals', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            /*if ($estimateRequestID !== false && $estimateRequestID != '') {
                $this->load->model('estimate_request_model');
                $completedStatus = $this->estimate_request_model->get_status_by_flag('completed');
                $this->estimate_request_model->update_request_status([
                    'requestid' => $estimateRequestID,
                    'status'    => $completedStatus->id,
                ]);
            }*/

            if (isset($custom_fields)) {
                //handle_custom_fields_post($insert_id, $custom_fields);
            }

            handle_tags_save($tags, $insert_id, 'sam_proposal');

            foreach ($items as $key => $item) {
                if ($itemid = sam_add_new_sales_item_post($item, $insert_id, 'sam_proposal')) {
                    sam_maybe_insert_post_item_tax($itemid, $item, $insert_id, 'sam_proposal');
                }
            }

            $proposal = $this->get($insert_id);
            /*if ($proposal->assigned != 0) {
                if ($proposal->assigned != get_staff_user_id()) {
                    $notified = add_notification([
                        'description'     => 'not_proposal_assigned_to_you',
                        'touserid'        => $proposal->assigned,
                        'fromuserid'      => get_staff_user_id(),
                        'link'            => 'proposals/list_proposals/' . $insert_id,
                        'additional_data' => serialize([
                            $proposal->subject,
                        ]),
                    ]);
                    if ($notified) {
                        pusher_trigger_notification([$proposal->assigned]);
                    }
                }
            }*/

            /*if ($data['rel_type'] == 'lead') {
                $this->load->model('leads_model');
                $this->leads_model->log_lead_activity($data['rel_id'], 'not_lead_activity_created_proposal', false, serialize([
                    '<a href="' . admin_url('proposals/list_proposals/' . $insert_id) . '" target="_blank">' . $data['subject'] . '</a>',
                ]));
            }*/

            sam_update_sales_total_tax_column($insert_id, 'sam_proposal', db_prefix() . '_sam_proposals');

            log_activity('New Sales and Marketing Proposal Created [ID: ' . $insert_id . ']');

            /*if ($save_and_send === true) {
                $this->send_proposal_to_email($insert_id);
            }*/

            hooks()->do_action('proposal_created', $insert_id);

            return $insert_id;
        }

        return false;
    }

    /**
     * Update proposal
     * @param  mixed $data $_POST data
     * @param  mixed $id   proposal id
     * @return boolean
     */
    public function update($data, $id)
    {
        $affectedRows = 0;

        $data['allow_comments'] = isset($data['allow_comments']) ? 1 : 0;

        $current_proposal = $this->get($id);

        $save_and_send = isset($data['save_and_send']);

        if (empty($data['rel_type'])) {
            $data['rel_id']   = null;
            $data['rel_type'] = '';
        } else {
            if (empty($data['rel_id'])) {
                $data['rel_id']   = null;
                $data['rel_type'] = '';
            }
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }

        $items = [];
        if (isset($data['items'])) {
            $items = $data['items'];
            unset($data['items']);
        }

        $newitems = [];
        if (isset($data['newitems'])) {
            $newitems = $data['newitems'];
            unset($data['newitems']);
        }

        if (isset($data['tags'])) {
            if (handle_tags_save($data['tags'], $id, 'sam_proposal')) {
                $affectedRows++;
            }
        }

        //$data['address'] = trim($data['address']);
        //$data['address'] = nl2br($data['address']);

        $hook = hooks()->apply_filters('before_proposal_updated', [
            'data'          => $data,
            'items'         => $items,
            'newitems'      => $newitems,
            'removed_items' => isset($data['removed_items']) ? $data['removed_items'] : [],
        ], $id);

        $data                  = $hook['data'];
        $data['removed_items'] = $hook['removed_items'];
        $newitems              = $hook['newitems'];
        $items                 = $hook['items'];

        // Delete items checked to be removed from database
        foreach ($data['removed_items'] as $remove_item_id) {
            if (sam_handle_removed_sales_item_post($remove_item_id, 'sam_proposal')) {
                $affectedRows++;
            }
        }

        unset($data['removed_items']);

        $this->db->where('addedfrom', get_staff_user_id());
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . '_sam_proposals', $data);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            $proposal_now = $this->get($id);
/*            if ($current_proposal->assigned != $proposal_now->assigned) {
                if ($proposal_now->assigned != get_staff_user_id()) {
                    $notified = add_notification([
                        'description'     => 'not_proposal_assigned_to_you',
                        'touserid'        => $proposal_now->assigned,
                        'fromuserid'      => get_staff_user_id(),
                        'link'            => 'proposals/list_proposals/' . $id,
                        'additional_data' => serialize([
                            $proposal_now->subject,
                        ]),
                    ]);
                    if ($notified) {
                        pusher_trigger_notification([$proposal_now->assigned]);
                    }
                }
            } */
        }

        foreach ($items as $key => $item) {
            if (sam_update_sales_item_post($item['itemid'], $item)) {
                $affectedRows++;
            }

            if (isset($item['custom_fields'])) {
                if (handle_custom_fields_post($item['itemid'], $item['custom_fields'])) {
                    $affectedRows++;
                }
            }

            if (!isset($item['taxname']) || (isset($item['taxname']) && count($item['taxname']) == 0)) {
                if (sam_delete_taxes_from_item($item['itemid'], 'sam_proposal')) {
                    $affectedRows++;
                }
            } else {
                $item_taxes        = sam_get_proposal_item_taxes($item['itemid']);
                $_item_taxes_names = [];
                foreach ($item_taxes as $_item_tax) {
                    array_push($_item_taxes_names, $_item_tax['taxname']);
                }
                $i = 0;
                foreach ($_item_taxes_names as $_item_tax) {
                    if (!in_array($_item_tax, $item['taxname'])) {
                        $this->db->where('id', $item_taxes[$i]['id'])
                        ->delete(db_prefix() . '_sam_item_tax');
                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                    $i++;
                }
                if (sam_maybe_insert_post_item_tax($item['itemid'], $item, $id, 'sam_proposal')) {
                    $affectedRows++;
                }
            }
        }

        foreach ($newitems as $key => $item) {
            if ($new_item_added = sam_add_new_sales_item_post($item, $id, 'sam_proposal')) {
                sam__maybe_insert_post_item_tax($new_item_added, $item, $id, 'sam_proposal');
                $affectedRows++;
            }
        }

        if ($affectedRows > 0) {
            sam_update_sales_total_tax_column($id, 'sam_proposal', db_prefix() . '_sam_proposals');
            log_activity('Proposal Updated [ID:' . $id . ']');
        }

        if ($save_and_send === true) {
            //$this->send_proposal_to_email($id);
        }

        if ($affectedRows > 0) {
            hooks()->do_action('after_proposal_updated', $id);

            return true;
        }

        return false;
    }

    public function clear_signature($id)
    {
        $this->db->select('signature');
        $this->db->where('id', $id);
        $proposal = $this->db->get(db_prefix() . '_sam_proposals')->row();

        if ($proposal) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . '_sam_proposals', ['signature' => null]);

            if (!empty($proposal->signature)) {
                unlink(get_upload_path_by_type('proposal') . $id . '/sam-' . $proposal->signature);
            }

            return true;
        }

        return false;
    }

    public function update_pipeline($data)
    {
        $this->mark_action_status($data['status'], $data['proposalid']);
        AbstractKanban::updateOrder($data['order'], 'pipeline_order', 'proposals', $data['status']);
    }

    /**
     *  Delete proposal attachment
     * @param   mixed $id  attachmentid
     * @return  boolean
     */
    public function delete_attachment($id)
    {
        $attachment = $this->get_attachments('', $id);
        $deleted    = false;
        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(get_upload_path_by_type('proposal') . $attachment->rel_id . '/' . $attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete(db_prefix() . 'files');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
                log_activity('Proposal Attachment Deleted [ID: ' . $attachment->rel_id . ']');
            }
            if (is_dir(get_upload_path_by_type('proposal') . $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(get_upload_path_by_type('proposal') . $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(get_upload_path_by_type('proposal') . $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }

    /**
     * Add proposal comment
     * @param mixed  $data   $_POST comment data
     * @param boolean $client is request coming from the client side
     */
    public function add_comment($data, $client = false)
    {
        if (is_staff_logged_in()) {
            $client = false;
        }

        if (isset($data['action'])) {
            unset($data['action']);
        }
        $data['dateadded'] = date('Y-m-d H:i:s');
        if ($client == false) {
            $data['staffid'] = get_staff_user_id();
        }
        $data['content'] = nl2br($data['content']);
        $this->db->insert(db_prefix() . '_sam_proposal_comments', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            $proposal = $this->get($data['proposalid']);

            // No notifications client when proposal is with draft status
            if ($proposal->status == '6' && $client == false) {
                return true;
            }

            if ($client == true) {
                // Get creator and assigned
                /*
                $this->db->select('staffid,email,phonenumber');
                $this->db->where('staffid', $proposal->addedfrom);
                $this->db->or_where('staffid', $proposal->assigned);
                $staff_proposal = $this->db->get(db_prefix() . 'staff')->result_array();
                
                $notifiedUsers  = [];
                foreach ($staff_proposal as $member) {
                    $notified = add_notification([
                        'description'     => 'not_proposal_comment_from_client',
                        'touserid'        => $member['staffid'],
                        'fromcompany'     => 1,
                        'fromuserid'      => 0,
                        'link'            => SAM_MODULE.'/proposals/list_proposals/' . $data['proposalid'],
                        'additional_data' => serialize([
                            $proposal->subject,
                        ]),
                    ]);

                    if ($notified) {
                        array_push($notifiedUsers, $member['staffid']);
                    }

                    $template     = mail_template('proposal_comment_to_staff', $proposal->id, $member['email']);
                    $merge_fields = $template->get_merge_fields();
                    $template->send();
                    // Send email/sms to admin that client commented
                    $this->app_sms->trigger(SMS_TRIGGER_PROPOSAL_NEW_COMMENT_TO_STAFF, $member['phonenumber'], $merge_fields);
                }
                
                hooks()->do_action('after_proposal_client_add_comment', $proposal->id);
                pusher_trigger_notification($notifiedUsers);
                */
            } else {
                // Send email/sms to client that admin commented
/*                $template     = mail_template('proposal_comment_to_customer', $proposal);
                $merge_fields = $template->get_merge_fields();
                $template->send();
                $this->app_sms->trigger(SMS_TRIGGER_PROPOSAL_NEW_COMMENT_TO_CUSTOMER, $proposal->phone, $merge_fields);
                hooks()->do_action('after_proposal_staff_add_comment', $proposal->id); */
            }

            return true;
        }
        

        return false;
    }

    public function edit_comment($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . '_sam_proposal_comments', [
            'content' => nl2br($data['content']),
        ]);
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Get proposal comments
     * @param  mixed $id proposal id
     * @return array
     */
    public function get_comments($id)
    {
        $this->db->where('proposalid', $id);
        $this->db->order_by('dateadded', 'ASC');

        return $this->db->get(db_prefix() . '_sam_proposal_comments')->result_array();
    }

    /**
     * Get proposal single comment
     * @param  mixed $id  comment id
     * @return object
     */
    public function get_comment($id)
    {
        $this->db->where('id', $id);

        return $this->db->get(db_prefix() . 'proposal_comments')->row();
    }

    /**
     * Remove proposal comment
     * @param  mixed $id comment id
     * @return boolean
     */
    public function remove_comment($id)
    {
        $comment = $this->get_comment($id);
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . '_sam_proposal_comments');
        if ($this->db->affected_rows() > 0) {
            log_activity('Sales and Marketing Proposal Comment Removed [ProposalID:' . $comment->proposalid . ', Comment Content: ' . $comment->content . ']');

            return true;
        }

        return false;
    }

    /**
     * Copy proposal
     * @param  mixed $id proposal id
     * @return mixed
     */
    public function copy($id)
    {
        $this->copy      = true;
        $proposal        = $this->get($id, [], true);
        $not_copy_fields = [
            'addedfrom',
            'id',
            'datecreated',
            'hash',
            'status',
            'invoice_id',
            'estimate_id',
            'is_expiry_notified',
            'date_converted',
            'signature',
            'acceptance_firstname',
            'acceptance_lastname',
            'acceptance_email',
            'acceptance_date',
            'acceptance_ip',
        ];
        $fields      = $this->db->list_fields(db_prefix() . '_sam_proposals');
        $insert_data = [];
        foreach ($fields as $field) {
            if (!in_array($field, $not_copy_fields)) {
                $insert_data[$field] = $proposal->$field;
            }
        }

        $insert_data['addedfrom']   = get_staff_user_id();
        $insert_data['datecreated'] = date('Y-m-d H:i:s');
        $insert_data['date']        = _d(date('Y-m-d'));
        $insert_data['status']      = 6;
        $insert_data['hash']        = app_generate_hash();

        // in case open till is expired set new 7 days starting from current date
        if ($insert_data['open_till'] && get_option('proposal_due_after') != 0) {
            $insert_data['open_till'] = _d(date('Y-m-d', strtotime('+' . get_option('proposal_due_after') . ' DAY', strtotime(date('Y-m-d')))));
        } elseif ($insert_data['open_till']) {
            $dDate                    = new DateTime(date('Y-m-d'));
            $dOpenTill                = new DateTime($insert_data['open_till']);
            $dDiff                    = $dDate->diff($dOpenTill);
            $insert_data['open_till'] = _d($dDate->modify('+ ' . $dDiff->days . ' DAY')->format('Y-m-d'));
        }

        $insert_data['newitems'] = [];
        $custom_fields_items     = get_custom_fields('items');
        $key                     = 1;
        foreach ($proposal->items as $item) {
            $insert_data['newitems'][$key]['description']      = $item['description'];
            $insert_data['newitems'][$key]['long_description'] = clear_textarea_breaks($item['long_description']);
            $insert_data['newitems'][$key]['qty']              = $item['qty'];
            $insert_data['newitems'][$key]['unit']             = $item['unit'];
            $insert_data['newitems'][$key]['taxname']          = [];
            $taxes                                             = get_proposal_item_taxes($item['id']);
            foreach ($taxes as $tax) {
                // tax name is in format TAX1|10.00
                array_push($insert_data['newitems'][$key]['taxname'], $tax['taxname']);
            }
            $insert_data['newitems'][$key]['rate']  = $item['rate'];
            $insert_data['newitems'][$key]['order'] = $item['item_order'];
            foreach ($custom_fields_items as $cf) {
                $insert_data['newitems'][$key]['custom_fields']['items'][$cf['id']] = get_custom_field_value($item['id'], $cf['id'], 'items', false);

                if (!defined('COPY_CUSTOM_FIELDS_LIKE_HANDLE_POST')) {
                    define('COPY_CUSTOM_FIELDS_LIKE_HANDLE_POST', true);
                }
            }
            $key++;
        }

        $id = $this->add($insert_data);

        if ($id) {
            $custom_fields = get_custom_fields('proposal');
            foreach ($custom_fields as $field) {
                $value = get_custom_field_value($proposal->id, $field['id'], 'proposal', false);
                if ($value == '') {
                    continue;
                }
                $this->db->insert(db_prefix() . 'customfieldsvalues', [
                    'relid'   => $id,
                    'fieldid' => $field['id'],
                    'fieldto' => 'proposal',
                    'value'   => $value,
                ]);
            }

            $tags = get_tags_in($proposal->id, 'proposal');
            handle_tags_save($tags, $id, 'proposal');

            log_activity('Copied Proposal ' . format_proposal_number($proposal->id));

            return $id;
        }

        return false;
    }

    /**
     * Take proposal action (change status) manually
     * @param  mixed $status status id
     * @param  mixed  $id     proposal id
     * @param  boolean $client is request coming from client side or not
     * @return boolean
     */
    public function mark_action_status($status, $id, $client = false)
    {
        $original_proposal = $this->get($id);
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . '_sam_proposals', [
            'status' => $status,
        ]);

        if ($this->db->affected_rows() > 0) {
            $sam_id = $original_proposal->rec_id;
            // Client take action
            if ($client == true) {
                $revert = false;
                // Declined
                if ($status == 2) {
                    $message = 'not_proposal_proposal_declined';
                } elseif ($status == 3) {
                    // Accepted
                    if (get_option('proposal_auto_convert_to_invoice_on_client_accept') == '1') {
                        $this->convert_to_invoice($id);
                    }
                    $message = 'not_proposal_proposal_accepted';


                } else {
                    $revert = true;
                }
                // This is protection that only 3 and 4 statuses can be taken as action from the client side
                if ($revert == true) {
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . '_sam_proposals', [
                        'status' => $original_proposal->status,
                    ]);

                    return false;
                }

                // Get creator and assigned;
                $this->db->where('staffid', $original_proposal->addedfrom);
                $this->db->or_where('staffid', $original_proposal->assigned);
                $staff_proposal = $this->db->get(db_prefix() . 'staff')->result_array();
                $notifiedUsers  = [];
                foreach ($staff_proposal as $member) {
                    $notified = add_notification([
                            'fromcompany'     => true,
                            'touserid'        => $member['staffid'],
                            'description'     => $message,
                            'link'            => SAM_MODULE.'/proposals/list_proposals/' .$sam_id .'/'. $id,
                            'additional_data' => serialize([
                                format_proposal_number($id),
                            ]),
                        ]);
                    if ($notified) {
                        array_push($notifiedUsers, $member['staffid']);
                    }
                }

                pusher_trigger_notification($notifiedUsers);

                // Send thank you to the customer email template
                if ($status == 3) {
                    foreach ($staff_proposal as $member) {
                        send_mail_template('proposal_accepted_to_staff', $original_proposal, $member['email']);
                    }

                    send_mail_template('proposal_accepted_to_customer', $original_proposal);

                    hooks()->do_action('proposal_accepted', $id);
                } else {

                    // Client declined send template to admin
                    foreach ($staff_proposal as $member) {
                        send_mail_template('proposal_declined_to_staff', $original_proposal, $member['email']);
                    }

                    hooks()->do_action('proposal_declined', $id);
                }
            } else {
                // in case admin mark as open the the open till date is smaller then current date set open till date 7 days more
                if ((date('Y-m-d', strtotime($original_proposal->open_till)) < date('Y-m-d')) && $status == 1) {
                    $open_till = date('Y-m-d', strtotime('+7 DAY', strtotime(date('Y-m-d'))));
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . '_sam_proposals', [
                        'open_till' => $open_till,
                    ]);
                }
            }

            log_activity('Proposal Status Changes [ProposalID:' . $id . ', Status:' . format_proposal_status($status, '', false) . ',Client Action: ' . (int) $client . ']');

            return true;
        }

        return false;
    }

    /**
     * Delete proposal
     * @param  mixed $id proposal id
     * @return boolean
     */
    public function delete($sam_id, $id)
    {
        hooks()->do_action('before_proposal_deleted', $id);

        $this->clear_signature($id);
        $proposal = $this->get($id);

        $this->db->where('addedfrom', get_staff_user_id());
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . '_sam_proposals');
        if ($this->db->affected_rows() > 0) {
            if (!is_null($proposal->short_link)) {
                app_archive_short_link($proposal->short_link);
            }

            delete_tracked_emails($id, 'proposal');

            $this->db->where('proposalid', $id);
            $this->db->delete(db_prefix() . '_sam_proposal_comments');
            // Get related tasks
            $this->db->where('rel_type', 'sam_proposal');
            $this->db->where('rel_id', $id);

/*            $tasks = $this->db->get(db_prefix() . 'tasks')->result_array();
            foreach ($tasks as $task) {
                $this->tasks_model->delete_task($task['id']);
            }*/

       /*     $attachments = $this->get_attachments($id);
            foreach ($attachments as $attachment) {
                $this->delete_attachment($attachment['id']);
            }*/

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'sam_proposal');
            $this->db->delete(db_prefix() . '_sam_notes');

            /*$this->db->where('relid IN (SELECT id from ' . db_prefix() . 'itemable WHERE rel_type="proposal" AND rel_id="' . $this->db->escape_str($id) . '")');
            $this->db->where('fieldto', 'items');
            $this->db->delete(db_prefix() . 'customfieldsvalues'); */

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'sam_proposal');
            $this->db->delete(db_prefix() . '_sam_itemable');


            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'sam_proposal');
            $this->db->delete(db_prefix() . '_sam_item_tax');

            /*
            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'proposal');
            $this->db->delete(db_prefix() . 'taggables');

            // Delete the custom field values
            $this->db->where('relid', $id);
            $this->db->where('fieldto', 'sam_proposal');
            $this->db->delete(db_prefix() . 'customfieldsvalues');

            $this->db->where('rel_type', 'proposal');
            $this->db->where('rel_id', $id);
            $this->db->delete(db_prefix() . 'reminders'); 

            $this->db->where('rel_type', 'proposal');
            $this->db->where('rel_id', $id);
            $this->db->delete(db_prefix() . 'views_tracking');
            
            */

            log_activity('Proposal Deleted [ProposalID:' . $id . ']');

            hooks()->do_action('after_proposal_deleted', $id);

            return true;
        }

        return false;
    }

    /**
     * Get relation proposal data. Ex lead or customer will return the necesary db fields
     * @param  mixed $rel_id
     * @param  string $rel_type customer/lead
     * @return object
     */
    public function get_relation_data_values($rel_id, $rel_type)
    {
        $data = new StdClass();
        if ($rel_type == 'customer') {
            $this->db->where('userid', $rel_id);
            $_data = $this->db->get(db_prefix() . 'clients')->row();

            $primary_contact_id = get_primary_contact_user_id($rel_id);

            if ($primary_contact_id) {
                $contact     = $this->clients_model->get_contact($primary_contact_id);
                $data->email = $contact->email;
            }

            $data->phone            = $_data->phonenumber;
            $data->is_using_company = false;
            if (isset($contact)) {
                $data->to = $contact->firstname . ' ' . $contact->lastname;
            } else {
                if (!empty($_data->company)) {
                    $data->to               = $_data->company;
                    $data->is_using_company = true;
                }
            }
            $data->company = $_data->company;
            $data->address = clear_textarea_breaks($_data->address);
            $data->zip     = $_data->zip;
            $data->country = $_data->country;
            $data->state   = $_data->state;
            $data->city    = $_data->city;

            $default_currency = $this->clients_model->get_customer_default_currency($rel_id);
            if ($default_currency != 0) {
                $data->currency = $default_currency;
            }
        } elseif ($rel_type = 'lead') {
            $this->db->where('id', $rel_id);
            $_data       = $this->db->get(db_prefix() . 'leads')->row();
            $data->phone = $_data->phonenumber;

            $data->is_using_company = false;

            if (empty($_data->company)) {
                $data->to = $_data->name;
            } else {
                $data->to               = $_data->company;
                $data->is_using_company = true;
            }

            $data->company = $_data->company;
            $data->address = $_data->address;
            $data->email   = $_data->email;
            $data->zip     = $_data->zip;
            $data->country = $_data->country;
            $data->state   = $_data->state;
            $data->city    = $_data->city;
        }

        return $data;
    }

    /**
     * Sent proposal to email
     * @param  mixed  $id        proposalid
     * @param  string  $template  email template to sent
     * @param  boolean $attachpdf attach proposal pdf or not
     * @return boolean
     */
    public function send_expiry_reminder($id)
    {
        $proposal = $this->get($id);

        // For all cases update this to prevent sending multiple reminders eq on fail
        $this->db->where('id', $proposal->id);
        $this->db->update(db_prefix() . '_sam_proposals', [
            'is_expiry_notified' => 1,
        ]);

        $template     = mail_template('proposal_expiration_reminder', $proposal);
        $merge_fields = $template->get_merge_fields();

        $template->send();

        if (can_send_sms_based_on_creation_date($proposal->datecreated)) {
            $sms_sent = $this->app_sms->trigger(SMS_TRIGGER_PROPOSAL_EXP_REMINDER, $proposal->phone, $merge_fields);
        }

        hooks()->do_action('after_proposal_expiry_reminder_sent', $id);

        return true;
    }

    public function send_proposal_to_email($id, $attachpdf = true, $cc = '')
    {
        // Proposal status is draft update to sent
        if (total_rows(db_prefix() . '_sam_proposals', ['id' => $id, 'status' => 6]) > 0) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . '_sam_proposals', ['status' => 4]);
        }

        $proposal = $this->get($id);

        $sent = send_mail_template('proposal_send_to_customer', $proposal, $attachpdf, $cc);

        if ($sent) {

            // Set to status sent
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . '_sam_proposals', [
                'status' => 4,
            ]);

            hooks()->do_action('proposal_sent', $id);

            return true;
        }

        return false;
    }

    public function do_kanban_query($status, $search = '', $page = 1, $sort = [], $count = false)
    {
        _deprecated_function('Proposal_model::do_kanban_query', '2.9.2', 'ProposalsPipeline class');

        $kanBan = (new ProposalsPipeline($status))
            ->search($search)
            ->page($page)
            ->sortBy($sort['sort'] ?? null, $sort['sort_by'] ?? null);

        if ($count) {
            return $kanBan->countAll();
        }

        return $kanBan->get();
    }

    /**
     * Convert proposal to invoice
     * @param mixed $id proposal id
     * @return mixed     New invoice ID
     */
    public function convert_to_invoice($id)
    {
        // Recurring invoice date is okey lets convert it to new invoice
        $proposal = $this->get($id);

        if ($proposal->rel_type != 'customer') {
            return false;
        }

        $new_invoice_data = [];
        $new_invoice_data['clientid']   = $proposal->rel_id;
        $new_invoice_data['project_id'] = $proposal->project_id;
        $new_invoice_data['number']     = get_option('next_invoice_number');
        $new_invoice_data['date']       = _d(date('Y-m-d'));
        $new_invoice_data['duedate']    = _d(date('Y-m-d'));
        if (get_option('invoice_due_after') != 0) {
            $new_invoice_data['duedate'] = _d(date('Y-m-d', strtotime('+' . get_option('invoice_due_after') . ' DAY', strtotime(date('Y-m-d')))));
        }
        $new_invoice_data['show_quantity_as'] = $proposal->show_quantity_as;
        $new_invoice_data['currency']         = $proposal->currency;
        $new_invoice_data['subtotal']         = $proposal->subtotal;
        $new_invoice_data['total']            = $proposal->total;
        $new_invoice_data['adjustment']       = $proposal->adjustment;
        $new_invoice_data['discount_percent'] = $proposal->discount_percent;
        $new_invoice_data['discount_total']   = $proposal->discount_total;
        $new_invoice_data['discount_type']    = $proposal->discount_type;
        $new_invoice_data['sale_agent']       = $proposal->assigned;

        $new_invoice_data['billing_street']   = clear_textarea_breaks($proposal->address);
        $new_invoice_data['billing_city']     = $proposal->city;
        $new_invoice_data['billing_state']    = $proposal->state;
        $new_invoice_data['billing_zip']      = $proposal->zip;
        $new_invoice_data['billing_country']  = $proposal->country;
        $new_invoice_data['shipping_street']  = '';
        $new_invoice_data['shipping_city']    = '';
        $new_invoice_data['shipping_state']   = '';
        $new_invoice_data['shipping_zip']     = '';
        $new_invoice_data['shipping_country'] = '';
        $new_invoice_data['include_shipping'] = 0;
        $new_invoice_data['show_shipping_on_invoice'] = 0;

        $new_invoice_data['terms']                    = get_option('predefined_terms_invoice');
        $new_invoice_data['clientnote']               = get_option('predefined_clientnote_invoice');
        // Set to unpaid status automatically
        $new_invoice_data['status']    = 1;
        $new_invoice_data['adminnote'] = '';

        $this->load->model('payment_modes_model');
        $modes = $this->payment_modes_model->get('', [
            'expenses_only !=' => 1,
        ]);
        $temp_modes = [];
        foreach ($modes as $mode) {
            if ($mode['selected_by_default'] == 0) {
                continue;
            }
            $temp_modes[] = $mode['id'];
        }
        $new_invoice_data['allowed_payment_modes'] = $temp_modes;
        $new_invoice_data['newitems']              = [];
        $key                                       = 1;

        foreach ($proposal->items as $item) {
            $new_invoice_data['newitems'][$key]['description']      = $item['description'];
            $new_invoice_data['newitems'][$key]['long_description'] = clear_textarea_breaks($item['long_description']);
            $new_invoice_data['newitems'][$key]['qty']              = $item['qty'];
            $new_invoice_data['newitems'][$key]['unit']             = $item['unit'];
            $new_invoice_data['newitems'][$key]['taxname']          = [];
            $taxes                                                  = sam_get_proposal_item_taxes($item['id']);
            foreach ($taxes as $tax) {
                // tax name is in format TAX1|10.00
                array_push($new_invoice_data['newitems'][$key]['taxname'], $tax['taxname']);
            }
            $new_invoice_data['newitems'][$key]['rate']  = $item['rate'];
            $new_invoice_data['newitems'][$key]['order'] = $item['item_order'];
            $key++;
        }
        $this->load->model('invoices_model');
        $invoice_id = $this->invoices_model->add($new_invoice_data);
        if ($invoice_id) {
            // Customer accepted the estimate and is auto converted to invoice
            if (!is_staff_logged_in()) {
                $this->db->where('rel_type', 'invoice');
                $this->db->where('rel_id', $invoice_id);
                $this->db->delete(db_prefix() . 'sales_activity');
                $this->invoices_model->log_invoice_activity($id, 'invoice_activity_auto_converted_from_proposal', true, serialize([
                    '<a href="' . admin_url('proposals#' . $proposal->id) . '">' . format_proposal_number($proposal->id) . '</a>',
                ]));
            }
            // For all cases update addefrom and sale agent from the invoice
            // May happen staff is not logged in and these values to be 0
            $this->db->where('id', $invoice_id);
            $this->db->update(db_prefix() . 'invoices', [
                'addedfrom'  => $proposal->addedfrom,
                'sale_agent' => $proposal->assigned,
            ]);

            // Update estimate with the new invoice data and set to status accepted
            $this->db->where('id', $proposal->id);
            $this->db->update(db_prefix() . '_sam_proposals', [
                'invoice_id'     => $invoice_id,
                'status'     => 3,
            ]);


            if (is_custom_fields_smart_transfer_enabled()) {
                $this->db->where('fieldto', 'proposal');
                $this->db->where('active', 1);
                $cfProposals = $this->db->get(db_prefix() . 'customfields')->result_array();
                foreach ($cfProposals as $field) {
                    $tmpSlug = explode('_', $field['slug'], 2);
                    if (isset($tmpSlug[1])) {
                        $this->db->where('fieldto', 'invoice');

                        $this->db->group_start();
                        $this->db->like('slug', 'invoice_' . $tmpSlug[1], 'after');
                        $this->db->where('type', $field['type']);
                        $this->db->where('options', $field['options']);
                        $this->db->where('active', 1);
                        $this->db->group_end();

                        // $this->db->where('slug LIKE "invoice_' . $tmpSlug[1] . '%" AND type="' . $field['type'] . '" AND options="' . $field['options'] . '" AND active=1');
                        $cfTransfer = $this->db->get(db_prefix() . 'customfields')->result_array();

                        // Don't make mistakes
                        // Only valid if 1 result returned
                        // + if field names similarity is equal or more then CUSTOM_FIELD_TRANSFER_SIMILARITY%
                        if (count($cfTransfer) == 1 && ((similarity($field['name'], $cfTransfer[0]['name']) * 100) >= CUSTOM_FIELD_TRANSFER_SIMILARITY)) {
                            $value = get_custom_field_value($proposal->id, $field['id'], 'estimate', false);

                            if ($value == '') {
                                continue;
                            }

                            $this->db->insert(db_prefix() . 'customfieldsvalues', [
                                'relid'   => $id,
                                'fieldid' => $cfTransfer[0]['id'],
                                'fieldto' => 'invoice',
                                'value'   => $value,
                            ]);
                        }
                    }
                }
            }

            hooks()->do_action('after_proposal_converted_to_invoice', ['proposal_id' => $id, 'invoice_id' => $invoice_id]);
            log_activity('Proposal Converted to Invoice [InvoiceID: ' . $invoice_id . ', ProposalID: ' . $id . ']');
        }

        return $id;
    }
    
    
    
    
    /**
     * Get proposals
     * @param  mixed $id proposal id OPTIONAL
     * @return mixed
     */
    public function get($id = '', $where = [], $for_editor = false)
    {
        $this->db->where($where);

        if (is_client_logged_in()) {
            $this->db->where('status !=', 0);
        }

        $this->db->select('*,' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . '_sam_proposals.id as id, ' . db_prefix() . 'currencies.name as currency_name');
        $this->db->from(db_prefix() . '_sam_proposals');
        $this->db->join(db_prefix() . 'currencies', db_prefix() . 'currencies.id = ' . db_prefix() . '_sam_proposals.currency', 'left');

        if (is_numeric($id)) {
            $this->db->where(db_prefix() . '_sam_proposals.id', $id);
            $proposal = $this->db->get()->row();
            if ($proposal) {
                $proposal->attachments                           = $this->get_attachments($id);
                $proposal->items                                 = sam_get_items_by_type('sam_proposal', $id);
                $proposal->visible_attachments_to_customer_found = false;
                foreach ($proposal->attachments as $attachment) {
                    if ($attachment['visible_to_customer'] == 1) {
                        $proposal->visible_attachments_to_customer_found = true;

                        break;
                    }
                }

                if ($proposal->project_id) {
                    //$this->load->model('projects_model');
                    //$proposal->project_data = $this->projects_model->get($proposal->project_id);
                }

                if ($for_editor == false) {
                    $proposal = sam_parse_proposal_content_merge_fields($proposal);
                }
            }

            return $proposal;
        }

        return $this->db->get()->result_array();
    }
    
    public function get_attachments($proposal_id, $id = '')
    {
        // If is passed id get return only 1 attachment
        if (is_numeric($id)) {
            $this->db->where('id', $id);
        } else {
            $this->db->where('rel_id', $proposal_id);
        }
        $this->db->where('rel_type', 'sam_proposal');
        $result = $this->db->get(db_prefix() . 'files');
        if (is_numeric($id)) {
            return $result->row();
        }

        return $result->result_array();
    }
    
    public function get_taxes_dropdown_template($name, $taxname, $type = '', $item_id = '', $is_edit = false, $manual = false)
    {
        // if passed manually - like in proposal convert items or project
        if ($manual == true) {
            // + is no longer used and is here for backward compatibilities
            if (is_array($taxname) || strpos($taxname, '+') !== false) {
                if (!is_array($taxname)) {
                    $__tax = explode('+', $taxname);
                } else {
                    $__tax = $taxname;
                }
                // Multiple taxes found // possible option from default settings when invoicing project
                $taxname = [];
                foreach ($__tax as $t) {
                    $tax_array = explode('|', $t);
                    if (isset($tax_array[0]) && isset($tax_array[1])) {
                        array_push($taxname, $tax_array[0] . '|' . $tax_array[1]);
                    }
                }
            } else {
                $tax_array = explode('|', $taxname);
                // isset tax rate
                if (isset($tax_array[0]) && isset($tax_array[1])) {
                    $tax = get_tax_by_name($tax_array[0]);
                    if ($tax) {
                        $taxname = $tax->name . '|' . $tax->taxrate;
                    }
                }
            }
        }
        // First get all system taxes
        $this->load->model('taxes_model');
        $taxes = $this->taxes_model->get();
        $i     = 0;
        foreach ($taxes as $tax) {
            unset($taxes[$i]['id']);
            $taxes[$i]['name'] = $tax['name'] . '|' . $tax['taxrate'];
            $i++;
        }
        if ($is_edit == true) {

            // Lets check the items taxes in case of changes.
            // Separate functions exists to get item taxes for Invoice, Estimate, Proposal, Credit Note
            $func_taxes = 'get_' . $type . '_item_taxes';
            if (function_exists($func_taxes)) {
                $item_taxes = call_user_func($func_taxes, $item_id);
            }

            foreach ($item_taxes as $item_tax) {
                $new_tax            = [];
                $new_tax['name']    = $item_tax['taxname'];
                $new_tax['taxrate'] = $item_tax['taxrate'];
                $taxes[]            = $new_tax;
            }
        }

        // In case tax is changed and the old tax is still linked to estimate/proposal when converting
        // This will allow the tax that don't exists to be shown on the dropdowns too.
        if (is_array($taxname)) {
            foreach ($taxname as $tax) {
                // Check if tax empty
                if ((!is_array($tax) && $tax == '') || is_array($tax) && $tax['taxname'] == '') {
                    continue;
                };
                // Check if really the taxname NAME|RATE don't exists in all taxes
                if (!value_exists_in_array_by_key($taxes, 'name', $tax)) {
                    if (!is_array($tax)) {
                        $tmp_taxname = $tax;
                        $tax_array   = explode('|', $tax);
                    } else {
                        $tax_array   = explode('|', $tax['taxname']);
                        $tmp_taxname = $tax['taxname'];
                        if ($tmp_taxname == '') {
                            continue;
                        }
                    }
                    $taxes[] = ['name' => $tmp_taxname, 'taxrate' => $tax_array[1]];
                }
            }
        }

        // Clear the duplicates
        $taxes = Arr::uniqueByKey($taxes, 'name');

        $select = '<select class="selectpicker display-block tax" data-width="100%" name="' . $name . '" multiple data-none-selected-text="' . _l('no_tax') . '">';

        foreach ($taxes as $tax) {
            $selected = '';
            if (is_array($taxname)) {
                foreach ($taxname as $_tax) {
                    if (is_array($_tax)) {
                        if ($_tax['taxname'] == $tax['name']) {
                            $selected = 'selected';
                        }
                    } else {
                        if ($_tax == $tax['name']) {
                            $selected = 'selected';
                        }
                    }
                }
            } else {
                if ($taxname == $tax['name']) {
                    $selected = 'selected';
                }
            }

            $select .= '<option value="' . $tax['name'] . '" ' . $selected . ' data-taxrate="' . $tax['taxrate'] . '" data-taxname="' . $tax['name'] . '" data-subtext="' . $tax['name'] . '">' . $tax['taxrate'] . '%</option>';
        }
        $select .= '</select>';

        return $select;
    }
    
    public function get_notes($rel_id, $rel_type)
    {
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid=' . db_prefix() . '_sam_notes.addedfrom');
        $this->db->where('rel_id', $rel_id);
        $this->db->where('rel_type', $rel_type);
        $this->db->order_by('dateadded', 'desc');

        $notes = $this->db->get(db_prefix() . '_sam_notes')->result_array();

        return hooks()->apply_filters('get_notes', $notes, ['rel_id' => $rel_id, 'rel_type' => $rel_type]);
    }
    
    public function add_note($data, $rel_type, $rel_id)
    {
        $data['dateadded']   = date('Y-m-d H:i:s');
        $data['addedfrom']   = get_staff_user_id();
        $data['rel_type']    = $rel_type;
        $data['rel_id']      = $rel_id;
        $data['description'] = nl2br($data['description']);

        $data = hooks()->apply_filters('create_note_data', $data, $rel_type, $rel_id);

        $this->db->insert(db_prefix() . '_sam_notes', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            hooks()->do_action('note_created', $insert_id, $data);

            return $insert_id;
        }

        return false;
    }

    public function edit_note($data, $id)
    {
        hooks()->do_action('before_update_note', [
            'data' => $data,
            'id'   => $id,
        ]);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . '_sam_notes', $data = [
            'description' => nl2br($data['description']),
        ]);

        if ($this->db->affected_rows() > 0) {
            hooks()->do_action('note_updated', $id, $data);

            return true;
        }

        return false;
    }
    
    public function delete_note($note_id)
    {
        hooks()->do_action('before_delete_note', $note_id);

        $this->db->where('id', $note_id);
        $note = $this->db->get(db_prefix() . '_sam_notes')->row();

        if ($note->addedfrom != get_staff_user_id() && !is_admin()) {
            return false;
        }

        $this->db->where('id', $note_id);
        $this->db->delete(db_prefix() . '_sam_notes');
        if ($this->db->affected_rows() > 0) {
            hooks()->do_action('note_deleted', $note_id, $note);

            return true;
        }

        return false;
    }

    /**
     * Get all reminders or 1 reminder if id is passed
     * @since Version 1.0.2
     * @param  mixed $id reminder id OPTIONAL
     * @return array or object
     */
    public function get_reminders($id = '')
    {
        $this->db->join(db_prefix() . 'staff', '' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'reminders.staff', 'left');
        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'reminders.id', $id);

            return $this->db->get(db_prefix() . '_sam_reminders')->row();
        } //is_numeric($id)
        $this->db->order_by('date', 'desc');

        return $this->db->get(db_prefix() . '_sam_reminders')->result_array();
    }

    /**
     * Remove client reminder from database
     * @since Version 1.0.2
     * @param  mixed $id reminder id
     * @return boolean
     */
    public function delete_reminder($id)
    {
        $reminder = $this->get_reminders($id);
        if ($reminder->creator == get_staff_user_id() || is_admin()) {
            $this->db->where('id', $id);
            $this->db->delete(db_prefix() . '_sam_reminders');
            if ($this->db->affected_rows() > 0) {
                log_activity('Reminder Deleted [' . ucfirst($reminder->rel_type) . 'ID: ' . $reminder->id . ' Description: ' . $reminder->description . ']');

                return true;
            } //$this->db->affected_rows() > 0
            return false;
        } //$reminder->creator == get_staff_user_id() || is_admin()
        return false;
    }
}

