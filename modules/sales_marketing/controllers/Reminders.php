<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Reminders extends AdminController
{
    public function __construct(){
        parent::__construct();
        $this->load->model('reminder_model');
        $this->load->model('dashboard_model');
    }

    public function get_reminders($sam_id=""){
        if($sam_id!=""){

            $cond = array('rel_id' => $sam_id, 'rel_type' => 'sam');
            $reminder_res = $this->reminder_model->get_reminders('',$cond);   
            $reminder_table = "";
            if($reminder_res){
                foreach($reminder_res as $k => $val){
                    $reminder_table .= "<tr>";
                    $reminder_table .= "<td>".$val['description']."";
                    $reminder_table .= "<div class='row-options'>";
                    $reminder_table .= "<a href='#' class='edit-reminder' onclick='edit_reminder_form(".$val['id'].",".$val['rel_id'].")'>"._l('sam_edit')."</a> | <a href='#' class='text-danger delete-reminder1' onclick='delete_reminder_record(".$val['id'].",".$val['rel_id'].")'>"._l('sam_delete')."</a>";
                    $reminder_table .= "</div></td>";
                    $reminder_table .= "<td>".$val['date']."</td>";
                    $reminder_table .= "<td><a href='".admin_url(SAM_MODULE.'/staff/profile/'.$val['staff'])."'><img src='".site_url().'/assets/images/user-placeholder.jpg'."' class='staff-profile-image-small'>".get_staff_full_name($val['staff'])."</a></td>";
                    if($val['isnotified']==1){
                        //$reminder_table .= "<td>"._l('sam_yes')."</td>";
                    }
                    else{
                        //$reminder_table .= "<td>"._l('sam_no')."</td>";
                    }

                    $reminder_table .= "</tr>";
                }
            }
            echo $reminder_table;
        } 
        echo "";
    }

    public function add_reminder($rel_id, $rel_type)
    {
        $message    = 'Reminder is not added';
        $alert_type = 'warning';
        //echo "<pre>"; print_r($this->input->post()); exit;
        if ($this->input->post()) {
            $success = $this->reminder_model->add_reminder($this->input->post(), $rel_id);
            if ($success) {
                add_activity_transactions($rel_id,'added reminder'); 
                $alert_type = 'success';
                $message    = 'Reminder Added Successfully';
            }
        }
        $alert_data = array($alert_type,$message);
        echo json_encode($alert_data);
    }  

    public function edit_reminder($id="",$sam_id=""){
        $post_data = $this->input->post();
        if($post_data){
            $message    = 'Reminder Updation Failed';
            $alert_type = 'warning';
            //echo "<pre>"; print_r($this->input->post()); exit;
            $success = $this->reminder_model->edit_reminder($post_data, $id);
            if ($success) {
                $alert_type = 'success';
                $message    = 'Reminder Updated Sucessfully';
            }
            
            $alert_data = array($alert_type,$message);
            echo json_encode($alert_data);            
        }
        else{
            if($id!="" && $sam_id!=""){
                if (is_admin()) {            
                    $all_staff = $this->dashboard_model->get_all_staff();
                }
                else{
                    $all_staff = '';    
                }
    
                $cond = array('id' => $id,'rel_id' => $sam_id, 'rel_type' => 'sam');
                $reminder_res = $this->reminder_model->get_reminders('',$cond);   
                //echo "<pre>"; print_r($all_staff); exit;
                $data['reminder_data'] = $reminder_res;
                $data['all_staff'] = $all_staff;
                echo $this->load->view('deals_details/edit_reminder',$data);
            } 
        }
    }
    /* Since version 1.0.2 delete client reminder */
    public function delete_reminder($id,$rel_id)
    {
        if (!$id && !$rel_id) {
            die('No reminder found');
        }
        $success    = $this->reminder_model->delete_reminder($id);
        $alert_type = 'warning';
        $message    = 'Not Deleted';
        if ($success) {
            $alert_type = 'success';
            $message    = 'Reminder Deleted';
        }

        $alert_data = array($alert_type,$message);
        echo json_encode($alert_data);
        //set_alert($alert_type, $message);
        //redirect(admin_url(SAM_MODULE."/details/".rel_id."/reminders"));
    }
}
