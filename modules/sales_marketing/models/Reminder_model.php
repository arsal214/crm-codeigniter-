<?php                               

defined('BASEPATH') or exit('No direct script access allowed');

class Reminder_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('staff_model');
    }

    /**
     * Get all reminders or 1 reminder if id is passed
     * @since Version 1.0.2
     * @param  mixed $id reminder id OPTIONAL
     * @return array or object
     */
    public function get_reminders($id = '',$cond=array())
    {   
        $this->db->join(db_prefix() . 'staff', '' . db_prefix() . 'staff.staffid = ' . db_prefix() . '_sam_reminders.staff', 'left');
        if (is_numeric($id)) {
            $this->db->where(db_prefix() . '_sam_reminders.id', $id);

            return $this->db->get(db_prefix() . '_sam_reminders')->row();
        } //is_numeric($id)

        $this->db->where($cond);
        $this->db->order_by('date', 'desc');
        return $this->db->get(db_prefix() . '_sam_reminders')->result_array();
    }

    /**
     * Add reminder
     * @since  Version 1.0.2
     * @param mixed $data All $_POST data for the reminder
     * @param mixed $id   relid id
     * @return boolean
     */
    public function add_reminder($data, $id)
    {
        if (isset($data['notify_by_email'])) {
            $data['notify_by_email'] = 1;
        } //isset($data['notify_by_email'])
        else {
            $data['notify_by_email'] = 0;
        }
        $data['date']        = to_sql_date($data['date'], true);
        $data['description'] = nl2br($data['description']);
        $data['creator']     = get_staff_user_id();
        $this->db->insert(db_prefix() . '_sam_reminders', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            if ($data['rel_type'] == 'sam') {

                $this->load->model('sam_model');
                $activity = 'not_activity_new_reminder_created';
                $this->sam_model->log_deals_activity($data['rel_id'], $activity, false, serialize([
                    get_staff_full_name($data['staff']),
                    _dt($data['date'])
                ]));
            }
            log_activity('New Reminder Added [' . ucfirst($data['rel_type']) . 'ID: ' . $data['rel_id'] . ' Description: ' . $data['description'] . ']');

            return true;
        } //$insert_id
        return false;
    }

    public function edit_reminder($data, $id)
    {
        if (isset($data['notify_by_email'])) {
            $data['notify_by_email'] = 1;
        } else {
            $data['notify_by_email'] = 0;
        }

        $data['date']        = to_sql_date($data['date'], true);
        $data['description'] = nl2br($data['description']);
        
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . '_sam_reminders', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
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
        //echo "<pre>"; print_r($reminder); exit;
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