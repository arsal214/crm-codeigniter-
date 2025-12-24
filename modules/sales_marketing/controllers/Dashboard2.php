<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard2 extends AdminController
{
    public function __construct(){
        parent::__construct();
        $this->load->model('dashboard_model');
        $this->load->helper('dashboard_setting');
    }
    /* List all staff members */
    public function index()
    {
        if (staff_cant('view', 'staff')) {
            access_denied('staff');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('staff');
        }
        $data['staff_members'] = $this->staff_model->get('', ['active' => 1]);
        $data['title']         = _l('staff_members');
        $this->load->view('admin/staff/manage', $data);
    }
    
    public function timesheets()
    {                              
        if (is_admin()) {            
            $all_staff = $this->dashboard_model->get_all_staff();
        }
        else{
            $all_staff = '';    
        } 
        
        $pipelines = $this->dashboard_model->get_pipelines();
        
        //staff timesheets
        $timesheets_data = getStaffTimesheets([],true);
        //staff activities on deals
        $leads_data = getActivityOnDeals([],true);
        //proposals
        $proposal_data = getProposalsDashboard([],true);
        //total spent time on each customer
        $spent_time_on_cust = getSpentTimeOnCustomer([],true);
        //get all reminders
        $reminder_list = getAllRemindersOnDashboard([],true);
        //get deals status summary
        $chart_data = get_deal_lead_summary([],1);
        if($chart_data){
            $data['leads_status_stats'] = json_encode($chart_data);
        }
        
        //get total deal value
        $deal_value_table = "";
        $deal_val_res = $this->dashboard_model->getTotalDealValue([],true);
        if($deal_val_res){
            foreach($deal_val_res as $val){
                $total_deal_value = $val['total'];
                if($total_deal_value==''){
                    $total_deal_value = 0;
                }
                $deal_value_table .= "<tr style='font-size:3em;text-align:center'><td>$".$total_deal_value."</td></tr>";
            }    
        }
        
        $data['timesheets_data'] = $timesheets_data;
        $data['leads_data'] = $leads_data;  
        $data['deal_value_table'] = $deal_value_table;                                                                                       
        $data['proposal_data'] = $proposal_data;                                                                                       
        $data['spent_time_on_cust'] = $spent_time_on_cust;                                                                                       
        $data['reminder_list'] = $reminder_list;                                                                                       
        $data['pipelines'] = $pipelines;                                                                                       
        $data['all_staff'] = $all_staff;                                                                                       
        $data['title']       = _l('sam_dashboard');  
        
        $data['user_dashboard_visibility'] = get_staff_meta2(get_staff_user_id(), 'dashboard_widgets_visibility');
        
        if (! $data['user_dashboard_visibility']) {
            $data['user_dashboard_visibility'] = [];
        } else {
            $data['user_dashboard_visibility'] = unserialize($data['user_dashboard_visibility']);
        }
        $data['user_dashboard_visibility'] = json_encode($data['user_dashboard_visibility']);
        //echo "<pre>"; print_r($data['user_dashboard_visibility']); exit;
                                 
        //$this->load->view(SAM_MODULE.'/dashboard/staff_timesheets', $data);
        $this->load->view(SAM_MODULE.'/dashboard/test', $data);
    } 
    
    public function filter_timesheets_data(){
        $post_data = $this->input->post();
        if(!empty($post_data)){
            
            //staff timesheets
            $timesheets_data = $this->dashboard_model->get_staff_timesheets($post_data);
            //staff activities on deals
            $leads_data = $this->dashboard_model->get_leads_on_customer($post_data);
            //total spent time on each customer
            $spent_time_on_cust = getSpentTimeOnCustomer2($post_data,false);
            $dashboard_data['data'] = $spent_time_on_cust;
            $spent_time_on_cust_view = $this->load->view('dashboard/spent_time_on_customer',$dashboard_data,true);
            //echo "<pre>"; print_r(json_encode($spent_time_on_cust)); exit;
            $reminder_list = getAllRemindersOnDashboard2($post_data,false);
            $dashboard_data['reminder_data'] = $reminder_list;
            $reminder_list_view = $this->load->view('dashboard/reminder_list',$dashboard_data,true);
            //all deals group by status
            $chart_data = get_deal_lead_summary($post_data);
            if($chart_data){
                $chart_data = json_encode($chart_data);
            }
            else{
                $chart_data = "";
            }
            
            $table1 = "";
            $table2 = "";
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
            if($leads_data){
                foreach($leads_data as $val){
                    $table2 .= "<tr>";
                    $table2 .= "<td>".get_staff_full_name($val['staff_id'])."</td>"; 
                    $table2 .= "<td>".$val['total_activities']."</td>";
                    $table2 .= "</tr>";
                }    
            }
            
            //get proposals
            $proposal_amount_table = "<tr style='font-size:3em'>";
            $proposal_count_table = "<tr style='font-size:3em'>";
            $proposal_res = $this->dashboard_model->getDealProposals($post_data);
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
            
            //get total deal value
            $pipeline_name = getPipelineNameById($post_data['pipeline_id']);
            if($pipeline_name==''){
                $pipeline_name = 'All Pipeline';
            }
            $deal_value_table = "";
            $deal_val_res = $this->dashboard_model->getTotalDealValue($post_data,false);
            if($deal_val_res){
                foreach($deal_val_res as $val){
                    $total_deal_value = $val['total'];
                    if($total_deal_value==''){
                        $total_deal_value = 0;
                    }
                    $deal_value_table .= "<tr style='font-size:3em;text-align:center'><td>$".$total_deal_value."</td></tr>";
                }    
            }
                                                
            $all_data = array(
                            $table1,
                            $table2,
                            $chart_data,
                            $proposal_amount_table,
                            $proposal_count_table,
                            $deal_value_table,
                            $pipeline_name,
                            $spent_time_on_cust_view,
                            $reminder_list_view
            );
            echo json_encode($all_data);  exit();
            //echo $table1.'#-#'.$table2.'#-#'.$chart_data;        
        }
        else{
            return false;
        }
    }
    
    public function save_dashboard_widgets_order()
    {
        hooks()->do_action('before_save_dashboard_widgets_order');

        $post_data = $this->input->post();
        //echo "<pre>"; print_r($post_data); exit;
        foreach ($post_data as $container => $widgets) {
            if ($widgets == 'empty') {
                $post_data[$container] = [];
            }
        }
        update_staff_meta2(get_staff_user_id(), 'dashboard_widgets_order', serialize($post_data));
    }

    public function save_dashboard_widgets_visibility()
    {
        hooks()->do_action('before_save_dashboard_widgets_visibility');

        $post_data = $this->input->post();
        update_staff_meta2(get_staff_user_id(), 'dashboard_widgets_visibility', serialize($post_data['widgets']));
    }

    public function reset_dashboard()
    {
        update_staff_meta2(get_staff_user_id(), 'dashboard_widgets_visibility', null);
        update_staff_meta2(get_staff_user_id(), 'dashboard_widgets_order', null);

        redirect(admin_url());
    }
      
}
