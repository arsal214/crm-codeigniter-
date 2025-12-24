<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends AdminController
{
    public function __construct(){
        parent::__construct();
        $this->load->model('dashboard_model');  
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

    
    public function getAllStaffIds()
    {
        $staff_list = $this->dashboard_model->get_all_staff();
        
        $staff_ids = array_column($staff_list, 'staffid');
    
        echo json_encode([
            'status' => !empty($staff_ids) ? 'success' : 'error',
            'staff_ids' => $staff_ids
        ]);
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
        
        $status_summary = $this->dashboard_model->getStatusSummary();
        
        //staff timesheets
        $timesheets_data = getStaffTimesheets([],true);
        //staff activities on deals
        $leads_data = getActivityOnDeals([],true);
        $deals_status_data = getStatusOnDeals([], true); // Pass staff_id to getStatusOnDeals
        // echo "<pre>"; print_r(json_encode($post_data)); exit;
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
        $data['deals_status_data'] = $deals_status_data;  
        $data['deal_value_table'] = $deal_value_table;                                                                                       
        $data['proposal_data'] = $proposal_data;                                                                                       
        $data['spent_time_on_cust'] = $spent_time_on_cust;                                                                                       
        $data['reminder_list'] = $reminder_list;                                                                                       
        $data['pipelines'] = $pipelines;                                                                                       
        $data['all_staff'] = $all_staff;     
        $data['status_summary'] = $status_summary;                                                                                     
        $data['title']       = _l('sam_dashboard'); 
        $this->load->view(SAM_MODULE.'/dashboard/staff_timesheets', $data);
        //$this->load->view(SAM_MODULE.'/dashboard/test', $data);
    } 
    
    public function filter_timesheets_data(){
        // echo "<pre>"; print_r($_POST); exit;
        $post_data = $this->input->post();
        if(!empty($post_data)){
            
            //staff timesheets
            $timesheets_data = $this->dashboard_model->get_staff_timesheets($post_data);
            //staff activities on deals
            $leads_data = $this->dashboard_model->get_leads_on_customer($post_data);
            $deals_status_data = $this->dashboard_model->get_deals_status_on_customer($post_data);
            // echo "<pre>"; print_r(json_encode($spent_time_on_cust)); exit;
            $spent_time_on_cust = getSpentTimeOnCustomer2($post_data,false);
            $dashboard_data['data'] = $spent_time_on_cust;
            $spent_time_on_cust_view = $this->load->view('dashboard/spent_time_on_customer',$dashboard_data,true);
            //echo "<pre>"; print_r(json_encode($spent_time_on_cust)); exit;
            $reminder_list = $this->dashboard_model->getAllRemindersOnDashboard($post_data,false);
            // echo "<pre>"; print_r(json_encode($reminder_list)); exit;
            $dashboard_data['reminder_data'] = $reminder_list;
            $reminder_list_view = $this->load->view('dashboard/reminder_list',$dashboard_data,true);
            $chart_data = get_deal_lead_summary($post_data);
            if($chart_data){
                $chart_data = json_encode($chart_data);
            }
            else{
                $chart_data = "";
            }
            
            $table1 = "";
            $table2 = "";
            $table3 = "";
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
        if ($deals_status_data) {
            foreach ($deals_status_data as $val) {
                $table3 .= "<tr>";
                // Add the href around the status name with target="_blank"
                $table3 .= "<td style='color:" . $val['color'] . "; font-weight: bold;'>
                                <a href='https://crm.mazajnet.com/admin/sales_marketing?deal_status=" . $val['status_id'] . "' 
                                   style='color:" . $val['color'] . "; text-decoration: none;' 
                                   target='_blank'>" . $val['status_name'] . "</a>
                            </td>"; 
                $table3 .= "<td>" . $val['deal_count'] . "</td>"; // Display the count of each status
                $table3 .= "</tr>";
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
                            $table3,
                            $chart_data,
                            $proposal_amount_table,
                            $proposal_count_table,
                            $deal_value_table,
                            $pipeline_name,
                            $spent_time_on_cust_view,
                            $reminder_list_view,
            );
                    // echo "<pre>"; print_r($percent_data); exit;

            echo json_encode($all_data);  exit();
            //echo $table1.'#-#'.$table2.'#-#'.$chart_data;        
        }
        else{
            return false;
        }
    } 
     public function finance_overview()
    {
    $statuses                                   = [1, 2, 5, 3, 4];
    $statpuses                                  = [6, 4, 1, 5, 2, 3];
    $data['estimate_statuses']                  = $statuses;
    $data['proposal_statuses']                  = $statpuses;
    $this->load->view(SAM_MODULE.'/dashboard/finance_overview', $data); // Pass the data array to the view
    }

    public function statistics()
    {                              
        if (is_admin())
        {            
            $all_staff                          = $this->dashboard_model->get_all_staff();
        }
        else
        {
            $all_staff                          = '';    
        } 
        
        $groups_data                            = getGroupsOnDeals([],true);
        $data['all_staff']                      = $all_staff;     
        $data['pipelines']                      = $pipelines;                                                                                       
        $data['groups_data']                    = $groups_data; 
        $data['title']                          = _l('Statistics'); 
        $this->load->view(SAM_MODULE.'/dashboard/statistics', $data);
    } 
    
    public function filter_statistics_data()
    {
    $post_data = $this->input->post();
    
    if (!empty($post_data))
    {
        $percent_data = $this->dashboard_model->getKPIEmpPer($post_data);
        if ($percent_data && is_array($percent_data))
        {
            $total_weighted_percentage = 0;
            $total_weight = 0;

            foreach ($percent_data as &$row) {
                $row['percentage'] = $this->dashboard_model->calculateKPI($row, $post_data['range'], $post_data['periodfrom'], $post_data['periodto']);

                $actions_count = (float)$row['actions_count'];
                $formatted_actions = (float)$row['formatted_actions'];

                if ($formatted_actions > 0)
                {
                    $row['overall_percentage'] = number_format(($actions_count / $formatted_actions) * 100, 2, '.', '');
                } 
                else
                {
                    $row['overall_percentage'] = number_format(0, 2, '.', '');
                }

                // Fetch KPI Weight
                $kpi_weight                     = $this->dashboard_model->getKPIWeight($row['staffid'], $row['kpi_function_id']);
                $row['kpi_weight']              = $kpi_weight;
                $weighted_percentage            = ($kpi_weight / 100) * $row['overall_percentage'];
                $total_weighted_percentage      += $weighted_percentage;
                $total_weight                   += $kpi_weight;
            }

            $overall_performance = ($total_weight > 0) ? ($total_weighted_percentage) : 0;
        } 
        else
        {
            echo json_encode(["error" => "No data returned or invalid data format."]);
            exit;
        }

        $groups_data = $this->dashboard_model->get_groups_on_customer($post_data);
        $deals_status_data = $this->dashboard_model->get_deals_status_on_customer($post_data);
        $univeristy_data = $this->dashboard_model->getUniversityOnDeals($post_data, true);
        $school_data = $this->dashboard_model->getSchoolOnDeals($post_data, true);
        
        $table1 = "";
        if ($groups_data) 
        {
            foreach ($groups_data as $val)
            {
                $table1 .= "<tr>";
                $table1 .= "<td><a href='" . base_url('admin/sales_marketing/dashboard/view_group_details') . "?group_id=" . $val['group_id'] . "&staffid=" . $val['staffid'] . "' target='_blank'><span class='label label-default mleft5 customer-group-list pointer'>" . $val['group_name'] . "</span></a></td>";
                $table1 .= "<td><span style=\"font-size:15px; font-weight:bold; color:#3D8D7A;\">" . $val['total_customers'] . "</span></td>";
                $table1 .= "</tr>";
            }    
        }
        
        $table2 = "";
        if ($univeristy_data)
        {
            foreach($univeristy_data as $val){
                $percentage = ($val['total_universities'] > 0) ? ($val['claimed_universities'] / $val['total_universities']) * 100 : 0;
                $table2 .= "<tr>";
                $table2 .= "<td><a href='" . base_url('admin/sales_marketing/assign_employee_country') . "' target='_blank'><span class='label label-default mleft5 customer-group-list pointer'>" . $val['country_name'] . "</span></a></td>";
                $table2 .= "<td><span style=\"font-size:15px; font-weight:bold; color:orange;\">" .  $val['total_universities'] . "</span></td>";
                $table2 .= "<td><span style=\"font-size:15px; font-weight:bold; color:#3D8D7A;\">" . $val['claimed_universities'] . "</span></td>";
                $table2 .= "<td><span style=\"font-size:15px; font-weight:bold; color:red;\">" . $val['not_claimed'] . "</span></td>";
                $table2 .= "<td><span style=\"font-size:15px; font-weight:bold; color:blue;\">" . number_format($percentage, 2) . "%</span></td>";
                $table2 .= "</tr>";
            }
        }

        if ($deals_status_data)
        {
            foreach ($deals_status_data as $val)
            {
                $table3 .= "<tr>";
                $table3 .= "<td style='color:" . $val['color'] . "; font-weight: bold;'>
                                <a href='https://crm.mazajnet.com/admin/sales_marketing?deal_status=" . $val['status_id'] . "' 
                                   style='color:" . $val['color'] . "; text-decoration: none;' 
                                   target='_blank'>" . $val['status_name'] . "</a>
                            </td>"; 
                $table3 .= "<td>" . $val['deal_count'] . "</td>"; 
                $table3 .= "</tr>";
            }    
        }
        
        $table4 = "";
        if ($school_data)
        {
            foreach($school_data as $val){
                $percentage = ($val['total_schools'] > 0) ? ($val['claimed_schools'] / $val['total_schools']) * 100 : 0;
                $table4 .= "<tr>";
                $table4 .= "<td><a href='" . base_url('admin/sales_marketing/assign_employee_country') . "' target='_blank'><span class='label label-default mleft5 customer-group-list pointer'>" . $val['country_name'] . "</span></a></td>";
                $table4 .= "<td><span style=\"font-size:15px; font-weight:bold; color:orange;\">" .  $val['total_schools'] . "</span></td>";
                $table4 .= "<td><span style=\"font-size:15px; font-weight:bold; color:#3D8D7A;\">" . $val['claimed_schools'] . "</span></td>";
                $table4 .= "<td><span style=\"font-size:15px; font-weight:bold; color:red;\">" . $val['not_claimed'] . "</span></td>";
                $table4 .= "<td><span style=\"font-size:15px; font-weight:bold; color:blue;\">" . number_format($percentage, 2) . "%</span></td>";
                $table4 .= "</tr>";
            }
        }

        $all_data = [
            "percent_data" => $percent_data,
            "overall_performance" => $overall_performance, // Add overall performance to the response
            "table1" => $table1,
            "table2" => $table2,
            "table3" => $table3,
            "table4" => $table4,
        ];

        echo json_encode($all_data);
        exit();
    } else
    {
        return false;
    }
}

    public function view_group_details()
    {
        $group_id                   = $this->input->get('group_id');
        $staffid                    = $this->input->get('staffid');
    
        if (!empty($group_id) && !empty($staffid))
        {
            $user_data              = $this->dashboard_model->get_customers_by_group_and_staff($group_id, $staffid);
    
            if ($user_data)
            {
                $data               =
                [
                    'customers'     => $user_data,    // All customers from the group
                ];
                $this->load->view(SAM_MODULE.'/dashboard/view_group_details', $data);
            } 
            else
            {
                echo "No customers found for this group or staff.";
            }
        } 
        else 
        {
            echo "Invalid group_id or staffid.";
        }
    }
    public function view_customers()
    {
        $staffid                    = $this->input->get('staffid');
        $range                      = $this->input->get('range');
        $user_data                  = $this->dashboard_model->get_new_customers_by_staff($staffid,$range);
        if ($user_data)
        {
            $data                   =
            [
                'customers'         => $user_data, 
            ];
            $this->load->view(SAM_MODULE.'/dashboard/view_customers', $data);
        } 
        else
        {
            echo "No customers found for this group or staff.";
        }
    }
    public function view_contacts()
    {
        $staffid                    = $this->input->get('staffid');
        $range                      = $this->input->get('range');
        $user_data                  = $this->dashboard_model->get_new_contact_by_staff($staffid,$range);
        if ($user_data)
        {
            $data                   =
            [
                'customers'         => $user_data, 
            ];
            $this->load->view(SAM_MODULE.'/dashboard/view_contacts', $data);
        } 
        else
        {
            echo "No customers found for this group or staff.";
        }
    }
    public function view_communication()
    {
        $staffid                    = $this->input->get('staffid');
        $range                      = $this->input->get('range');
        $user_data                  = $this->dashboard_model->get_new_communication_by_staff($staffid,$range);
                                    // echo "<pre>"; print_r($user_data); exit;
        if ($user_data) 
        {
            $data                   =
            [
                'customers'         => $user_data, 
            ];
            $this->load->view(SAM_MODULE.'/dashboard/view_communication', $data);
        } 
        else
        {
            echo "No customers found for this group or staff.";
        }
    }
    public function get_chart_data()
    {
        $staff_id = $this->input->post('staff_id');
        $range = $this->input->post('range');
        $periodfrom = $this->input->post('periodfrom');
        $periodto = $this->input->post('periodto');
    
        $data = $this->dashboard_model->fetch_chart_data($staff_id, $range, $periodfrom, $periodto);
    
        if (!empty($data)) {
            echo json_encode(["status" => "success", "data" => $data]);
        } else {
            echo json_encode(["status" => "error", "message" => "No data found."]);
        }
    }


}
