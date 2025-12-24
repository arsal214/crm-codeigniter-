<?php

use app\services\AbstractKanban;

defined('BASEPATH') or exit('No direct script access allowed');

class Sam_model extends App_Model
{
    public $_table_name;
    public $_order_by;
    public $_primary_key;
    protected $_primary_filter = 'intval';

    public function dealInfo($id)
    {
        $deal = sam_join_data('tbl_sam', 'tbl_sam.*,CONCAT(firstname, " ", lastname) as full_name,tbl_sam_source.source_name,tbl_sam_pipelines.pipeline_name,tbl_sam_stages.stage_name', array('id' => $id), array(db_prefix() . 'staff' => db_prefix() . 'staff.staffid = tbl_sam.default_deal_owner', 'tbl_sam_stages' => 'tbl_sam_stages.stage_id = tbl_sam.stage_id', 'tbl_sam_pipelines' => 'tbl_sam.pipeline_id = tbl_sam_pipelines.pipeline_id', 'tbl_sam_source' => 'tbl_sam.source_id = tbl_sam_source.source_id'));
        $deal->comments = $this->get_deal_comments($id);
        $deal->assignees = $this->get_deal_assignees($deal);
        $deal->contacts = $this->get_deal_contacts($deal);
        $deal->customers = $this->get_deal_customers($deal);
        $deal->attachments = $this->get_deal_attachments($id);
        return $deal;
    }



 public function update_deal_status($deal_id, $status_id) {
    $this->db->where('id', $deal_id);
    return $this->db->update('tbl_sam', ['deal_status' => $status_id]);
}


    public function get_deal_customers($deal)
    {

        // if $deal is numeric, then it's an id, otherwise it's an object
        if (is_numeric($deal)) {
            $deal = $this->get($deal);
        }

        if (!empty($deal->rel_id) && !empty($deal->rel_type)) {
            $task_rel_data = get_relation_data($deal->rel_type, $deal->rel_id);
            $task_rel_value = get_relation_values($task_rel_data, $deal->rel_type);
            return $task_rel_value;
        }
    }


    /**
     * Get all task attachments
     * @param mixed $deal_id deal_id
     * @return array
     */

    public function get_deal_attachments($deal_id, $where = [])
    {
        $this->db->select(implode(', ', prefixed_table_fields_array(db_prefix() . 'files')) . ', ' . db_prefix() . '_sam_comments.id as comment_file_id');
        $this->db->where(db_prefix() . 'files.rel_id', $deal_id);
        $this->db->where(db_prefix() . 'files.rel_type', 'sam');

        if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
            $this->db->where($where);
        }

        $this->db->join(db_prefix() . '_sam_comments', db_prefix() . '_sam_comments.file_id = ' . db_prefix() . 'files.id', 'left');
        $this->db->join(db_prefix() . '_sam', db_prefix() . '_sam.id = ' . db_prefix() . 'files.rel_id');
        $this->db->order_by(db_prefix() . 'files.dateadded', 'desc');

        return $this->db->get(db_prefix() . 'files')->result_array();
    }



    public function get_deal_contacts($deal)
    {
        if (empty($deal->rel_id)) {
            return [];
        }
        $user_id = json_decode($deal->rel_id);
        $this->db->select('userid,active,last_login,email,phonenumber,CONCAT(firstname, " ", lastname) as full_name');
        $this->db->from(db_prefix() . 'contacts');
        $this->db->where_in('userid', $user_id);
        $result = $this->db->get()->result_array();
        return $result;
    }
    public function get_deal_assignees($deal)
    {
        if (empty($deal->user_id)) {
            return [];
        }
        $user_id = json_decode($deal->user_id);
        $this->db->select('staffid,firstname,lastname,CONCAT(firstname, " ", lastname) as full_name');
        $this->db->from(db_prefix() . 'staff');
        $this->db->where_in('staffid', $user_id);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function get_deal_comments($id)
    {
        $task_comments_order = hooks()->apply_filters('task_comments_order', 'DESC');
        $this->db->select('id,dateadded,content,' . db_prefix() . 'staff.firstname,' . db_prefix() . 'staff.lastname,' . db_prefix() . '_sam_comments.staffid,' . db_prefix() . '_sam_comments.contact_id as contact_id,file_id,CONCAT(firstname, " ", lastname) as staff_full_name');
        $this->db->from(db_prefix() . '_sam_comments');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . '_sam_comments.staffid', 'left');
        $this->db->where('deal_id', $id);
        $this->db->order_by('dateadded', $task_comments_order);

        $comments = $this->db->get()->result_array();

        $ids = [];
        foreach ($comments as $key => $comment) {
            array_push($ids, $comment['id']);
            $comments[$key]['attachments'] = [];
        }

        if (count($ids) > 0) {
            $allAttachments = $this->get_deal_attachments($id, 'deal_comment_id IN (' . implode(',', $ids) . ')');
            foreach ($comments as $key => $comment) {
                foreach ($allAttachments as $attachment) {
                    if ($comment['id'] == $attachment['deal_comment_id']) {
                        $comments[$key]['attachments'][] = $attachment;
                    }
                }
            }
        }

        return $comments;
    }


    public function add_attachment_to_database($rel_id, $rel_type, $attachment, $external = false)
    {
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['rel_id'] = $rel_id;
        if (!isset($attachment[0]['staffid'])) {
            $data['staffid'] = get_staff_user_id();
        } else {
            $data['staffid'] = $attachment[0]['staffid'];
        }

        if (isset($attachment[0]['deal_comment_id'])) {
            $data['deal_comment_id'] = $attachment[0]['deal_comment_id'];
        }

        $data['rel_type'] = $rel_type;

        if (isset($attachment[0]['contact_id'])) {
            $data['contact_id'] = $attachment[0]['contact_id'];
            $data['visible_to_customer'] = 1;
            if (isset($data['staffid'])) {
                unset($data['staffid']);
            }
        }

        $data['attachment_key'] = app_generate_hash();

        if ($external == false) {
            $data['file_name'] = $attachment[0]['file_name'];
            $data['filetype'] = $attachment[0]['filetype'];
        } else {
            $path_parts = pathinfo($attachment[0]['name']);
            $data['file_name'] = $attachment[0]['name'];
            $data['external_link'] = $attachment[0]['link'];
            $data['filetype'] = !isset($attachment[0]['mime']) ? get_mime_by_extension('.' . $path_parts['extension']) : $attachment[0]['mime'];
            $data['external'] = $external;
            if (isset($attachment[0]['thumbnailLink'])) {
                $data['thumbnail_link'] = $attachment[0]['thumbnailLink'];
            }
        }

        $this->db->insert(db_prefix() . 'files', $data);
        $insert_id = $this->db->insert_id();

        if ($data['rel_type'] == 'customer' && isset($data['contact_id'])) {
            if (get_option('only_own_files_contacts') == 1) {
                $this->db->insert(db_prefix() . 'shared_customer_files', [
                    'file_id' => $insert_id,
                    'contact_id' => $data['contact_id'],
                ]);
            } else {
                $this->db->select('id');
                $this->db->where('userid', $data['rel_id']);
                $contacts = $this->db->get(db_prefix() . 'contacts')->result_array();
                foreach ($contacts as $contact) {
                    $this->db->insert(db_prefix() . 'shared_customer_files', [
                        'file_id' => $insert_id,
                        'contact_id' => $contact['id'],
                    ]);
                }
            }
        }

        return $insert_id;
    }

    public function edit_comment($data)
    {
        // Check if user really creator
        $this->db->where('id', $data['id']);
        $comment = $this->db->get('tbl_sam_comments')->row();
        if ($comment->staffid == get_staff_user_id() || has_permission('sam', '', 'edit') || $comment->contact_id == get_contact_user_id()) {
            $comment_added = strtotime($comment->dateadded);
            $minus_1_hour = strtotime('-1 hours');
            if (get_option('client_staff_add_edit_delete_task_comments_first_hour') == 0 || (get_option('client_staff_add_edit_delete_task_comments_first_hour') == 1 && $comment_added >= $minus_1_hour) || is_admin()) {
                if (total_rows(db_prefix() . 'files', ['deal_comment_id' => $comment->id]) > 0) {
                    $data['content'] .= '[deal_attachment]';
                }

                $this->db->where('id', $data['id']);
                $this->db->update('tbl_sam_comments', [
                    'content' => $data['content'],
                ]);
                if ($this->db->affected_rows() > 0) {
                    return true;
                }
            } else {
                return false;
            }

            return false;
        }
    }

    /**
     * Remove task comment from database
     * @param mixed $id task id
     * @return boolean
     */
    public function remove_comment($id, $force = false)
    {
        // Check if user really creator
        $this->db->where('id', $id);
        $comment = $this->db->get('tbl_sam_comments')->row();

        if (!$comment) {
            return true;
        }

        if ($comment->staffid == get_staff_user_id() || has_permission('sam', '', 'delete') || $comment->contact_id == get_contact_user_id() || $force === true) {
            $comment_added = strtotime($comment->dateadded);
            $minus_1_hour = strtotime('-1 hours');
            if (
                get_option('client_staff_add_edit_delete_task_comments_first_hour') == 0 || (get_option('client_staff_add_edit_delete_task_comments_first_hour') == 1 && $comment_added >= $minus_1_hour)
                || (is_admin() || $force === true)
            ) {
                $this->db->where('id', $id);
                $this->db->delete('tbl_sam_comments');

                if ($this->db->affected_rows() > 0) {
                    if ($comment->file_id != 0) {
                        $this->remove_deal_attachment($comment->file_id);
                    }

                    $commentAttachments = $this->get_deal_attachments($comment->deal_id, 'deal_comment_id=' . $id);
                    foreach ($commentAttachments as $attachment) {
                        $this->remove_deal_attachment($attachment['id']);
                    }

                    return true;
                }
            } else {
                return false;
            }
        }

        return false;
    }

    /**
     * Remove task attachment from server and database
     * @param mixed $id attachmentid
     * @return boolean
     */
    public function remove_deal_attachment($id)
    {
        $comment_removed = false;
        $deleted = false;
        // Get the attachment
        $this->db->where('id', $id);
        $attachment = $this->db->get(db_prefix() . 'files')->row();

        if ($attachment) {
            if (empty($attachment->external)) {
                $relPath = get_upload_path_for_sam() . $attachment->rel_id . '/';
                $fullPath = $relPath . $attachment->file_name;
                unlink($fullPath);
                $fname = pathinfo($fullPath, PATHINFO_FILENAME);
                $fext = pathinfo($fullPath, PATHINFO_EXTENSION);
                $thumbPath = $relPath . $fname . '_thumb.' . $fext;
                if (file_exists($thumbPath)) {
                    unlink($thumbPath);
                }
            }

            $this->db->where('id', $attachment->id);
            $this->db->delete(db_prefix() . 'files');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
                log_activity('Deal Attachment Deleted [DealID: ' . $attachment->rel_id . ']');
            }

            if (is_dir(get_upload_path_for_sam() . $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(get_upload_path_for_sam() . $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(get_upload_path_for_sam() . $attachment->rel_id);
                }
            }
        }

        if ($deleted) {
            if ($attachment->deal_comment_id != 0) {
                $total_comment_files = total_rows(db_prefix() . 'files', ['deal_comment_id' => $attachment->deal_comment_id]);
                if ($total_comment_files == 0) {
                    $this->db->where('id', $attachment->deal_comment_id);
                    $comment = $this->db->get('tbl_sam_comments')->row();

                    if ($comment) {
                        // Comment is empty and uploaded only with attachments
                        // Now all attachments are deleted, we need to delete the comment too
                        if (empty($comment->content) || $comment->content === '[deal_attachment]') {
                            $this->db->where('id', $attachment->deal_comment_id);
                            $this->db->delete('tbl_sam_comments');
                            $comment_removed = $comment->id;
                        } else {
                            $this->db->query("UPDATE tbl_sam_comments
                            SET content = REPLACE(content, '[deal_attachment]', '')
                            WHERE id = " . $attachment->deal_comment_id);
                        }
                    }
                }
            }

            $this->db->where('file_id', $id);
            $comment_attachment = $this->db->get('tbl_sam_comments')->row();

            if ($comment_attachment) {
                $this->remove_comment($comment_attachment->id);
            }
        }

        return ['success' => $deleted, 'comment_removed' => $comment_removed];
    }

    function get_deal_cost($deals_id)
    {
        $this->db->select_sum('total_cost');
        $this->db->where('deals_id', $deals_id);
        $this->db->from('tbl_sam_items');
        $query_result = $this->db->get();
        $cost = $query_result->row();
        if (!empty($cost->total_cost)) {
            $result = $cost->total_cost;
        } else {
            $result = '0';
        }
        return $result;
    }

    public function staff_can_access_deal($id, $staff_id = '')
    {
        $staff_id = $staff_id == '' ? get_staff_user_id() : $staff_id;

        if (has_permission('sam', $staff_id, 'view')) {
            return true;
        }

        $CI = &get_instance();

        if (total_rows(db_prefix() . 'sam', 'id="' . $CI->db->escape_str($id) . '" AND (assigned=' . $CI->db->escape_str($staff_id) . ' OR is_public=1 OR addedfrom=' . $CI->db->escape_str($staff_id) . ')') > 0) {
            return true;
        }

        return false;
    }

    public function get_comment_details($module_id, $module = null, $relpy_id = null)
    {
        // get all data from tbltask_comments and tbl_users and assign the data to array and order by comment_datetime
        $this->commentsJoinStart();
        if (empty($module)) {
            if ($relpy_id) {
                $this->db->where('tbltask_comments.id', $module_id);
            } else {
                $this->db->where('tbltask_comments.contact_id', $module_id);
            }
        } else {
            // $this->db->where('tbltask_comments.module', $module);
            // $this->db->where('tbltask_comments.module_field_id', $module_id);
            // $this->db->where('tbltask_comments.id', '0');
            // $this->db->where('tbltask_comments.attachments_id', '0');
            // $this->db->where('tbltask_comments.file_id', '0');
        }
        $result = $this->commentsJoinEnd();
        return $result;
    }

    private function commentsJoinEnd()
    {
        $this->db->order_by('tbltask_comments.id', 'desc');
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    private function commentsJoinStart()
    {
        $this->db->select('tbltask_comments.*', FALSE);
        $this->db->select('tblstaff.firstname,tblstaff.lastname,tblstaff.profile_image', FALSE);
        $this->db->from('tbltask_comments');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbltask_comments.deal_id', 'left');
    }

    public
    function check_deals_update($table, $where, $id = Null)
    {
        $this->db->select('*', FALSE);
        $this->db->from($table);
        if ($id != null) {
            $this->db->where($id);
        }
        $this->db->where($where);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public
    function deals_array_from_post($fields)
    {
        $data = array();
        foreach ($fields as $field) {
            $data[$field] = $this->input->post($field, true);
        }
        return $data;
    }

    public
    function save_sam($data, $id = NULL)
    {
        // Insert
        if ($id === NULL) {
            !isset($data[$this->_primary_key]) || $data[$this->_primary_key] = NULL;
            $this->db->set($data);
            $this->db->insert($this->_table_name);
            $id = $this->db->insert_id();
        } // Update
        else {
            $filter = $this->_primary_filter;
            $id = $filter($id);
            $this->db->set($data);
            $this->db->where($this->_primary_key, $id);
            $this->db->update($this->_table_name);
        }
        return $id;
    }

    function check_by_deals($where, $tbl_name)
    {

        $this->db->select('*');
        $this->db->from($tbl_name);
        $this->db->where($where);
        $query_result = $this->db->get();
        $result = $query_result->row();
        return $result;
    }

    public function staff_query($table)
    {
        $role = $this->session->userdata('user_type');
        $userid = $this->input->post('user_id', true);
        if ($role == 3 || !empty($userid)) {
            if (empty($userid)) {
                $userid = my_id();
            }
            if (!empty($this->db->field_exists('permission', $table))) {
                $this->db->group_start();
                if ($this->db->version() >= 8) {
                    $sq = $this->db->escape('\\b' . ($userid) . '\\b');
                } else {
                    $sq = $this->db->escape('[[:<:]]' . ($userid) . '[[:>:]]');
                }
                $this->db->where($table . '.permission REGEXP', $sq, false);
                $this->db->or_where(array($table . '.permission' => 'all'));
                $this->db->or_where(array($table . '.permission' => NULL));
                $this->db->group_end(); //close bracket
            }
        }
    }

    public function check_deals_by($where, $tbl_name)
    {

        $this->db->select('*');
        $this->db->from($tbl_name);
        $this->db->where($where);
        $query_result = $this->db->get();
        $result = $query_result->row();
        return $result;
    }

    public function delete_sam($id)
    {
        $filter = $this->_primary_filter;
        $id = $filter($id);
        if (!$id) {
            return FALSE;
        }
        $this->db->where($this->_primary_key, $id);
        $this->db->limit(1);
        $this->db->delete($this->_table_name);
    }

    public function getItemsInfo($term = null, $warehouse_id = null, $limit = 10)
    {
        $for_purcahse = $this->input->get('for', true);

        $table = db_prefix() . 'items';
        $this->db->select('*');
        if (!empty($term)) {
            $this->db->where("(description LIKE '%" . $term . "%' OR long_description LIKE '%" . $term . "%' OR  concat(description, ' (', long_description, ')') LIKE '%" . $term . "%')");
        }
        $this->db->limit($limit);
        $this->db->order_by('id', 'DESC');
        $q = $this->db->get($table);
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return FALSE;
    }

    public function get_attach_file($id, $module = null, $files_id = null)
    {

        // get all data from tbl_attachments and tbl_attachments_files and assign the data to array
        // $this->db->select('tbl_attachments.*', FALSE);
        $this->db->select('tblfiles.*', FALSE);
        $this->db->select('tblstaff.firstname,tblstaff.lastname', FALSE);
        $this->db->from('tblfiles');
        // $this->db->join('tbl_attachments_files', 'tbl_attachments_files.attachments_id = tbl_attachments.attachments_id', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblfiles.rel_id', 'left');
        if (!empty($module) && empty($files_id)) {

            if ($module == 'g') {
                $this->db->where('tblfiles.attachments_id', $id);
            } else {
                $this->db->where('tblfiles.rel_type', $module);
                $this->db->where('tblfiles.rel_id', $id);
            }
            $query_result = $this->db->get();
            $result = $query_result->result();
            // assign the data to array using attachments_id as key
            if ($module != 'g') {
                $data = array();
                foreach ($result as $row) {
                    $data[$row->attachments_id][] = $row;
                }
            } else {
                $data = $result;
            }
        } else {
            $this->db->where('tblfiles.rel_id', $id);
            // if (!empty($files_id)) {
            //     $this->db->where('tbl_attachments_files.uploaded_files_id', $files_id);
            // }
            $query_result = $this->db->get();
            if (!empty($module) && $module == 'r') {
                $data[] = $query_result->row();
            } else {
                $data = $query_result->result();
            }
        }

        return $data;
    }

    public function count_rows($table, $where = null)
    {
        if (!empty($where)) {
            $this->db->where($where);
        }
        $query = $this->db->get($table);
        if ($query->num_rows() > 0) {
            return $query->num_rows();
        } else {
            return 0;
        }
    }

    public function staff_can_access_deals($id, $staff_id = '')
    {
        $staff_id = $staff_id == '' ? get_staff_user_id() : $staff_id;

        if (has_permission('sam', $staff_id, 'view')) {
            return true;
        }

        $CI = &get_instance();

        if (total_rows('tbl_sam', 'id="' . $CI->db->escape_str($id) . '" AND (assigned=' . $CI->db->escape_str($staff_id) . ' OR is_public=1 OR addedfrom=' . $CI->db->escape_str($staff_id) . ')') > 0) {
            return true;
        }

        return false;
    }

    public function delete_deals_attachment($id)
    {
        $attachment = $this->get_deals_attachments('', $id);
        $deleted = false;

        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(get_upload_path_for_sam() . $attachment->rel_id . '/' . $attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete(db_prefix() . 'files');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
                log_activity('SAM Attachment Deleted [ID: ' . $attachment->rel_id . ']');
            }

            if (is_dir(get_upload_path_for_sam() . $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(get_upload_path_for_sam() . $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(get_upload_path_for_sam() . $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }

    public function get($id = '', $where = [])
    {
        if (is_numeric($id)) {
            $this->db->where('tbl_sam.id', $id);
            $deals = $this->db->get('tbl_sam')->row();
            if ($deals) {
                $deals->attachments = $this->get_deals_attachments($id);
            }

            return $deals;
        }

        return $this->db->get('tbl_sam')->result_array();
    }

    public function get_deals_attachments($id = '', $attachment_id = '', $where = [])
    {
        $this->db->where($where);
        $idIsHash = !is_numeric($attachment_id) && strlen($attachment_id) == 32;
        if (is_numeric($attachment_id) || $idIsHash) {
            $this->db->where($idIsHash ? 'attachment_key' : 'id', $attachment_id);

            return $this->db->get(db_prefix() . 'files')->row();
        }
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'sam');
        $this->db->order_by('dateadded', 'DESC');

        return $this->db->get(db_prefix() . 'files')->result_array();
    }

    public function add_attachment_deals_database($deals_id, $attachment, $external = false, $form_activity = false)
    {

        $this->misc_model->add_attachment_to_database($deals_id, 'sam', $attachment, $external);

        if ($form_activity == false) {
            $this->log_deals_activity($deals_id, 'not_sam_activity_added_attachment');
        } else {
            $this->log_deals_activity($deals_id, 'not_sam_activity_log_attachment', true, serialize([
                $form_activity,
            ]));
        }

        // No notification when attachment is imported from web to deals form
        if ($form_activity == false) {
            $deals = $this->get($deals_id);
            $not_user_ids = [];
            foreach (json_decode($deals->user_id) as $userId) {

                if ($userId != get_staff_user_id()) {
                    array_push($not_user_ids, $userId);
                }
                if ($userId != get_staff_user_id() && $userId != 0) {
                    array_push($not_user_ids, $userId);
                }
            }
            $notifiedUsers = [];
            foreach ($not_user_ids as $uid) {
                $notified = add_notification([
                    'description' => 'not_sam_added_attachment',
                    'touserid' => $uid,
                    'link' => '#dealsid=' . $deals_id,
                    'additional_data' => serialize([
                        $deals->title,
                    ]),
                ]);
                if ($notified) {
                    array_push($notifiedUsers, $uid);
                }
            }
            pusher_trigger_notification($notifiedUsers);
        }
    }

    /**
     * Add new task comment
     * @param array $data comment $_POST data
     * @return boolean
     */
    public function add_deal_comment($data)
    {
        if (is_client_logged_in()) {
            $data['staffid'] = 0;
            $data['contact_id'] = get_contact_user_id();
        } else {
            $data['staffid'] = get_staff_user_id();
            $data['contact_id'] = 0;
        }

        $this->db->insert('tbl_sam_comments', [
            'deal_id' => $data['deal_id'],
            'content' => is_client_logged_in() ? _strip_tags($data['content']) : $data['content'],
            'staffid' => $data['staffid'],
            'contact_id' => $data['contact_id'],
            'dateadded' => date('Y-m-d H:i:s'),
        ]);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {


            return $insert_id;
        }

        return false;
    }

    private function _send_deal_mentioned_users_notification($description, $deal_id, $staff, $email_template, $notification_data, $comment_id)
    {
        $staff = array_unique($staff, SORT_NUMERIC);

        $this->load->model('staff_model');
        $notifiedUsers = [];

        foreach ($staff as $staffId) {
            if (!is_client_logged_in()) {
                if ($staffId == get_staff_user_id()) {
                    continue;
                }
            }

            $member = $this->staff_model->get($staffId);

            $link = '#deal_id=' . $deal_id;

            if ($comment_id) {
                $link .= '#comment_' . $comment_id;
            }

            $notified = add_notification([
                'description' => $description,
                'touserid' => $member->staffid,
                'link' => $link,
                'additional_data' => $notification_data,
            ]);

            if ($notified) {
                array_push($notifiedUsers, $member->staffid);
            }

            if ($email_template != '') {
                send_mail_template($email_template, $member->email, $member->staffid, $deal_id);
            }
        }

        pusher_trigger_notification($notifiedUsers);
    }

    public function log_deals_activity($id, $description, $integration = false, $additional_data = '')
    {
        $log = [
            'date' => date('Y-m-d H:i:s'),
            'description' => $description,
            'deal_id' => $id,
            'staffid' => get_staff_user_id(),
            'additional_data' => $additional_data,
            'full_name' => get_staff_full_name(get_staff_user_id()),
        ];
        if ($integration == true) {
            $log['staffid'] = 0;
            $log['full_name'] = '[CRON]';
        }

        $this->db->insert('tbl_sam_activity_log', $log);

        return $this->db->insert_id();
    }

    public function get_lead_activity_log($id)
    {
        $sorting = hooks()->apply_filters('deal_activity_log_default_sort', 'DESC');

        $this->db->where('deal_id', $id);
        $this->db->order_by('date', $sorting);

        return $this->db->get('tbl_sam_activity_log')->result_array();
    }

    public function array_from_post($fields)
    {
        $data = array();
        foreach ($fields as $field) {
            $data[$field] = $this->input->post($field, true);
        }
        return $data;
    }

    public function send_email($params)
    {
        $template = mail_template('deal_send_email', 'deals', array_to_object($params));
        $template->send();
    }

    public function update_deal_satges($data)
    {
        $this->db->select('stage_id');
        $this->db->where('id', $data['leadid']);
        $_old = $this->db->get(db_prefix() . '_sam')->row();
        $old_status = '';

        if ($_old) {
            $old_status = get_sam_row('tbl_sam_stages', ['stage_id' => $_old->stage_id]);
            if ($old_status) {
                $old_status = $old_status->stage_name;
            }
        }

        $affectedRows = 0;
        $current_status = get_sam_row('tbl_sam_stages', ['stage_id' => $data['status']])->stage_name;

        $this->db->where('id', $data['leadid']);
        $this->db->update(db_prefix() . '_sam', [
            'stage_id' => $data['status'],
        ]);

        $_log_message = '';

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            if ($current_status != $old_status && $old_status != '') {
                $_log_message = 'not_sam_activity_status_updated';
                $additional_data = serialize([
                    get_staff_full_name(),
                    $old_status,
                    $current_status,
                ]);
            }
            $this->db->where('id', $data['leadid']);
            $this->db->update(db_prefix() . 'leads', [
                'last_status_change' => date('Y-m-d H:i:s'),
            ]);
        }

        if (isset($data['order'])) {
            AbstractKanban::updateOrder($data['order'], 'dealorder', '_sam', $data['status']);
        }

        if ($affectedRows > 0) {
            if ($_log_message == '') {
                return true;
            }

            $this->log_deals_activity($data['leadid'], $_log_message, false, $additional_data);

            return true;
        }

        return false;
    }

    public function update_stage_order($data)
    {
        foreach ($data['order'] as $status) {
            $this->db->where('stage_id', $status[0]);
            $this->db->update('tbl_sam_stages', [
                'stage_order' => $status[1],
            ]);
        }
    }
    
    public function add_activity_transaction($data=array()){
        
        if(is_array($data)){
            $res = $this->db->insert('tbl_sam_transactions',$data);
            if($res){
                return true;
            }
            else{
                return false;
            }
        }    
    }
    
    public function getOneRecord($tablename='',$cond=array()){
        if($tablename!="" && is_array($cond)){
            $res = $this->db->select("*")->where($cond)->get($tablename)->result_array();
            if($res){
                return $res;
            }
            else{
                return false;
            }
        }
    }
    
    public function get_lead_activity_log2($id)
    {
        $sorting = hooks()->apply_filters('deal_activity_log_default_sort', 'DESC');

        $this->db->where('sam_id', $id);
        $this->db->order_by('t_date', $sorting);

        return $this->db->get('tbl_sam_transactions')->result_array();
    }
    
    public function getAllRecords($tablename='',$fields="",$cond=array()){
        if($tablename!="" && $fields!=""){
            $this->db->select($fields);
            if(is_array($cond) && count($cond)>0){
                $this->db->where($cond);
            }
            $res = $this->db->get($tablename)->result_array();
            if($res){
                return $res;
            }
            else{
                return false;
            }
        }
    }
    
    public function addClientInGroup($data=array())
    {   
        $this->db->insert(db_prefix().'customer_groups', $data);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New Customer Group Created [ID:' . $insert_id . '');

            return $insert_id;
        }

        return false;
    }
    
    
    /**
     * Add new contact
     * @param array  $data               $_POST data
     * @param mixed  $customer_id        customer id
     * @param boolean $not_manual_request is manual from admin area customer profile or register, convert to lead
     */
    public function add_contact($data, $customer_id, $not_manual_request = false)
    {
        $send_set_password_email = isset($data['send_set_password_email']) ? true : false;

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        if (isset($data['permissions'])) {
            $permissions = $data['permissions'];
            unset($data['permissions']);
        }

        $data['email_verified_at'] = date('Y-m-d H:i:s');

        $send_welcome_email = true;

        if (isset($data['donotsendwelcomeemail'])) {
            $send_welcome_email = false;
        }

        if (defined('CONTACT_REGISTERING')) {
            $send_welcome_email = true;

            // Do not send welcome email if confirmation for registration is enabled
            if (get_option('customers_register_require_confirmation') == '1') {
                $send_welcome_email = false;
            }

            // If client register set this contact as primary
            $data['is_primary'] = 1;

            if (is_email_verification_enabled() && !empty($data['email'])) {
                // Verification is required on register
                $data['email_verified_at']      = null;
                $data['email_verification_key'] = app_generate_hash();
            }
        }

        if (isset($data['is_primary'])) {
            $data['is_primary'] = 1;
            $this->db->where('userid', $customer_id);
            $this->db->update(db_prefix() . 'contacts', [
                'is_primary' => 0,
            ]);
        } else {
            $data['is_primary'] = 0;
        }

        $password_before_hash = '';
        $data['userid']       = $customer_id;
        if (isset($data['password'])) {
            $password_before_hash = $data['password'];
            $data['password']     = app_hash_password($data['password']);
        }

        $data['datecreated'] = date('Y-m-d H:i:s');

        if (!$not_manual_request) {
            $data['invoice_emails']     = isset($data['invoice_emails']) ? 1 :0;
            $data['estimate_emails']    = isset($data['estimate_emails']) ? 1 :0;
            $data['credit_note_emails'] = isset($data['credit_note_emails']) ? 1 :0;
            $data['contract_emails']    = isset($data['contract_emails']) ? 1 :0;
            $data['task_emails']        = isset($data['task_emails']) ? 1 :0;
            $data['project_emails']     = isset($data['project_emails']) ? 1 :0;
            $data['ticket_emails']      = isset($data['ticket_emails']) ? 1 :0;
        }

        $data['email'] = trim($data['email']);

        $data = hooks()->apply_filters('before_create_contact', $data);

        $this->db->insert(db_prefix() . 'contacts', $data);
        $contact_id = $this->db->insert_id();

        if ($contact_id) {
            if (isset($custom_fields)) {
                handle_custom_fields_post($contact_id, $custom_fields);
            }
            // request from admin area
            if (!isset($permissions) && $not_manual_request == false) {
                $permissions = [];
            } elseif ($not_manual_request == true) {
                $permissions         = [];
                $_permissions        = get_contact_permissions();
                $default_permissions = @unserialize(get_option('default_contact_permissions'));
                if (is_array($default_permissions)) {
                    foreach ($_permissions as $permission) {
                        if (in_array($permission['id'], $default_permissions)) {
                            array_push($permissions, $permission['id']);
                        }
                    }
                }
            }

            if ($not_manual_request == true) {
                // update all email notifications to 0
                $this->db->where('id', $contact_id);
                $this->db->update(db_prefix() . 'contacts', [
                    'invoice_emails'     => 0,
                    'estimate_emails'    => 0,
                    'credit_note_emails' => 0,
                    'contract_emails'    => 0,
                    'task_emails'        => 0,
                    'project_emails'     => 0,
                    'ticket_emails'      => 0,
                ]);
            }
            foreach ($permissions as $permission) {
                $this->db->insert(db_prefix() . 'contact_permissions', [
                    'userid'        => $contact_id,
                    'permission_id' => $permission,
                ]);

                // Auto set email notifications based on permissions
                if ($not_manual_request == true) {
                    if ($permission == 6) {
                        $this->db->where('id', $contact_id);
                        $this->db->update(db_prefix() . 'contacts', ['project_emails' => 1, 'task_emails' => 1]);
                    } elseif ($permission == 3) {
                        $this->db->where('id', $contact_id);
                        $this->db->update(db_prefix() . 'contacts', ['contract_emails' => 1]);
                    } elseif ($permission == 2) {
                        $this->db->where('id', $contact_id);
                        $this->db->update(db_prefix() . 'contacts', ['estimate_emails' => 1]);
                    } elseif ($permission == 1) {
                        $this->db->where('id', $contact_id);
                        $this->db->update(db_prefix() . 'contacts', ['invoice_emails' => 1, 'credit_note_emails' => 1]);
                    } elseif ($permission == 5) {
                        $this->db->where('id', $contact_id);
                        $this->db->update(db_prefix() . 'contacts', ['ticket_emails' => 1]);
                    }
                }
            }

            if ($send_welcome_email == true && !empty($data['email'])) {
                send_mail_template(
                    'customer_created_welcome_mail',
                    $data['email'],
                    $data['userid'],
                    $contact_id,
                    $password_before_hash
                );
            }

            if ($send_set_password_email) {
                $this->authentication_model->set_password_email($data['email'], 0);
            }

            if (defined('CONTACT_REGISTERING')) {
                $this->send_verification_email($contact_id);
            } else {
                // User already verified because is added from admin area, try to transfer any tickets
                $this->load->model('tickets_model');
                $this->tickets_model->transfer_email_tickets_to_contact($data['email'], $contact_id);
            }

            log_activity('Contact Created [ID: ' . $contact_id . ']');

            hooks()->do_action('contact_created', $contact_id);

            return $contact_id;
        }

        return false;
    }
    
    //get payment records
    public function getPaymentRecords($sam_id="",$clientid="",$cond=array()){
        $where = [];
        if ($clientid != '') {
            array_push($where, 'AND t3.userid=' . $this->ci->db->escape_str($clientid));
        }
        if (staff_cant('view', 'payments')) {
            $whereUser = '';
            $whereUser .= 'AND (invoiceid IN (SELECT id FROM tblinvoices WHERE (addedfrom=' . get_staff_user_id() . ' AND addedfrom IN (SELECT staff_id FROM tblstaff_permissions WHERE feature = "invoices" AND capability="view_own")))';
            if (get_option('allow_staff_view_invoices_assigned') == 1) {
                $whereUser .= ' OR invoiceid IN (SELECT id FROM tblinvoices WHERE sale_agent=' . get_staff_user_id() . ')';
            }
            $whereUser .= ')';
            array_push($where, $whereUser);
        }
        
        $where = implode(' ', $where);
        $where = trim($where);
        if (startsWith($where, 'AND') || startsWith($where, 'OR')) {
            if (startsWith($where, 'OR')) {
                $where = substr($where, 2);
            } else {
                $where = substr($where, 3);
            }                
        }
        if($where!=''){
            $where = 'WHERE t2.sam_id='.$sam_id.' AND ' . $where;    
        }
        else{
            $where = 'WHERE t2.sam_id='.$sam_id;
        }
        
        $query = "
            SELECT 
                t1.id as id, 
                t1.invoiceid, 
                t5.name as paymentmode, 
                t1.transactionid,
                t2.clientid,
                t2.currency as currency, 
                CASE t3.company WHEN ' ' THEN (SELECT CONCAT(firstname, ' ', lastname) 
                FROM tblcontacts WHERE userid = t3.userid and is_primary = 1) ELSE t3.company END as company, 
                t1.amount, 
                t1.date 
            FROM tblinvoicepaymentrecords as t1
            LEFT JOIN tblinvoices as t2 ON t2.id = t1.invoiceid
            LEFT JOIN tblclients as t3 ON t3.userid = t2.clientid
            LEFT JOIN tblcurrencies as t4 ON t4.id = t2.currency
            LEFT JOIN tblpayment_modes as t5 ON t5.id = t1.paymentmode
            $where 
        ";
        //echo $query; exit;
        $result = $this->db->query($query);
        //print_r($this->db->last_query()); exit;
        if($result){                   
            return $result->result_array();
        }
        else{
            return false;
        }
    }

}
