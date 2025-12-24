<?php                               

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('projects_model');
        $this->load->model('staff_model');
    }
    
    public function get_pipelines(){
        $res = $this->db->select('*')->get('tbl_sam_pipelines')->result_array();
        if($res){
            return $res;
        }
        else{
            return false;
        }
    }
    
    public function getPipelineNameById($id=0){
        $res = $this->db->select('pipeline_name as name')->where('pipeline_id',$id)->get('tbl_sam_pipelines')->row();
        if($res){
            return $res->name;
        }
        else{
            return false;
        }
    }
    
  public function get_all_staff()
    {
        $this->db->select("CONCAT(s.firstname, ' ', s.lastname) as fullname, s.staffid");
        $this->db->from('tblstaff s');
        $this->db->join('tblstaff_departments sd', 'sd.staffid = s.staffid');
        $this->db->join('tbldepartments d', 'd.departmentid = sd.departmentid');
        $this->db->where('d.departmentid', 9);
        
        $res = $this->db->get()->result_array();
    
        return $res ? $res : false;
    }

    
    public function get_staff_timesheets($post_data=array(),$default=false){
        if($default){
            $userid_cond = 1;
            if(!is_admin()){
                $userid_cond = "t.staff_id=".get_staff_user_id();
            }
            $beginThisMonth = date('Y-m-01 00:00:00');
            $endThisMonth   = date('Y-m-t 23:59:59');    
            $where = 'WHERE '.$userid_cond.' AND t.deleted=0 AND t.start_time BETWEEN ' . strtotime($beginThisMonth) . ' AND ' . strtotime($endThisMonth);
            $query = '
                SELECT t.staff_id, t.pipeline_id, sum(time_spent) as total_time_spent 
                FROM tbl_sam_taskstimers as t
                JOIN tbl_sam as t2 on t.sam_id = t2.id
                '."$where".'
                group by t.staff_id
            
            ';
            //echo $query; exit;
            $result = $this->db->query($query)->result_array();
            if($result){
                //echo "<pre>"; print_r($result);exit;
                return $result;
            }
            else{
                return false;
            }
        }
        else{
            if(is_array($post_data)){
                $where = [];
                if(isset($post_data['staff_id']) && $post_data['staff_id']!=""){
                        $where = [
                            'AND t.staff_id=' . $this->db->escape_str($post_data['staff_id']),
                        ];
                }
                if(isset($post_data['pipeline_id']) && $post_data['pipeline_id']!=""){
                        $where = [
                            'AND t.pipeline_id=' . $this->db->escape_str($post_data['pipeline_id']),
                        ];
                }
                if(isset($post_data['range'])){
                    $range = $post_data['range'];
                    //today
                    if($range=='today'){
                        $beginOfDay = strtotime('midnight');
                        $endOfDay   = strtotime('tomorrow', $beginOfDay) - 1;
                        array_push($where, ' AND t.start_time BETWEEN ' . $beginOfDay . ' AND ' . $endOfDay);
                    }
                    //seven days
                    elseif($range=='last_seven_days'){
                        $beginThisWeek = date('Y-m-d 00:00:00', strtotime('-7 DAYS'));
                        $endThisWeek   = date('Y-m-d 23:59:59');
                        array_push($where, ' AND t.start_time BETWEEN ' . strtotime($beginThisWeek) . ' AND ' . strtotime($endThisWeek));
                    }
                    //this week
                    elseif($range=='this_week'){
                        $beginThisWeek = date('Y-m-d 00:00:00', strtotime('monday this week'));
                        $endThisWeek   = date('Y-m-d 23:59:59', strtotime('sunday this week'));
                        array_push($where, ' AND t.start_time BETWEEN ' . strtotime($beginThisWeek) . ' AND ' . strtotime($endThisWeek));
                    }
                    //last week
                    elseif($range=='last_week'){
                        $beginLastWeek = date('Y-m-d 00:00:00', strtotime('monday last week'));
                        $endLastWeek   = date('Y-m-d 23:59:59', strtotime('sunday last week'));
                        array_push($where, ' AND t.start_time BETWEEN ' . strtotime($beginLastWeek) . ' AND ' . strtotime($endLastWeek));
                    }
                    //this month
                    elseif($range=='this_month'){
                        $beginThisMonth = date('Y-m-01 00:00:00');
                        $endThisMonth   = date('Y-m-t 23:59:59');
                        array_push($where, ' AND t.start_time BETWEEN ' . strtotime($beginThisMonth) . ' AND ' . strtotime($endThisMonth));
                    }
                    //last month
                    elseif($range=='last_month'){
                        $beginLastMonth = date('Y-m-01 00:00:00', strtotime('-1 MONTH'));
                        $endLastMonth   = date('Y-m-t 23:59:59', strtotime('-1 MONTH'));
                        array_push($where, ' AND t.start_time BETWEEN ' . strtotime($beginLastMonth) . ' AND ' . strtotime($endLastMonth));
                    }
                    //this year
                    elseif($range=='this_year'){
                        $beginOfDay = date('Y-01-01 00:00:00');
                        $endOfDay   = date('Y-12-t 23:59:59');
                        array_push($where, ' AND t.start_time BETWEEN ' . strtotime($beginOfDay) . ' AND ' . strtotime($endOfDay));
                    }
                    //last year
                    elseif($range=='last_year'){
                        $beginOfDay = date('Y-01-01 00:00:00', strtotime('-1 YEAR'));
                        $endOfDay   = date('Y-12-t 23:59:59', strtotime('-1 YEAR'));
                        array_push($where, ' AND t.start_time BETWEEN ' . strtotime($beginOfDay) . ' AND ' . strtotime($endOfDay));
                    }
                    //search by periods
                    elseif($range=='period'){
                        $start_date = to_sql_date($post_data['periodfrom']);
                        $end_date   = to_sql_date($post_data['periodto']);
                        array_push($where, ' AND t.start_time BETWEEN ' . strtotime($start_date . ' 00:00:00') . ' AND ' . strtotime($end_date . ' 23:59:59'));
                    }
                    //all
                    elseif($range=='all'){
                        
                    }
                }
                
                //echo "<pre>"; print_r($where); exit;
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
                    $where = 'WHERE t.deleted=0 AND ' . $where;    
                }
                else{
                    $where = 'WHERE t.deleted=0';
                }
                $query = '
                    SELECT t.staff_id, t.pipeline_id, sum(time_spent) as total_time_spent 
                    FROM tbl_sam_taskstimers as t
                    JOIN tbl_sam as t2 on t.sam_id = t2.id
                    '."$where".'
                    group by t.staff_id
                
                ';
                //echo $query; exit;
                $result = $this->db->query($query)->result_array();
                if($result){
                    //echo "<pre>"; print_r($result);exit;
                    return $result;
                }
                else{
                    return false;
                }
                
            }        
        }
    }
    
    public function get_leads_on_customer($post_data=array(),$default=false){
        if($default){
            $userid_cond = 1;
            if(!is_admin()){
                $userid_cond = "t.staff_id=".get_staff_user_id();
            }
            $beginThisMonth = date('Y-m-01');
            $endThisMonth   = date('Y-m-d');
            //array_push($where, ' AND t.t_date BETWEEN "' . $beginThisMonth . '" AND "' . $endThisMonth.'"');    
            $where = 'where '.$userid_cond.' AND t.t_date BETWEEN "' . $beginThisMonth . '" AND "' . $endThisMonth.'"';
            $query = '
                SELECT count(t.staff_id) as total_activities, t.sam_id, t.staff_id, t.pipeline_id 
                FROM tbl_sam_transactions as t
                JOIN tbl_sam as t2 on t.sam_id = t2.id                               
                '."$where".'
                group by t.staff_id
            
            ';
            //echo $query; exit;
            $result = $this->db->query($query)->result_array();
            if($result){
                //echo "<pre>"; print_r($result);exit;
                return $result;
            }
            else{
                return false;
            }
        }
        else{
            if(is_array($post_data)){
                $where = [];
                if(isset($post_data['staff_id']) && $post_data['staff_id']!=""){
                        $where = [
                            'AND t.staff_id=' . $this->db->escape_str($post_data['staff_id']),
                        ];
                }
                if(isset($post_data['range'])){
                    $range = $post_data['range'];
                    //today
                    if($range=='today'){
                        $beginOfDay = date('Y-m-d',strtotime('TODAY'));       
                        array_push($where, ' AND t.t_date="' . $beginOfDay.'"');
                    }
                    //seven days
                    elseif($range=='last_seven_days'){
                        $beginThisWeek = date('Y-m-d', strtotime('-7 DAYS'));
                        $endThisWeek   = date('Y-m-d');
                        array_push($where, ' AND t.t_date BETWEEN "' . $beginThisWeek . '" AND "' . $endThisWeek.'"');
                    }
                    //this week
                    elseif($range=='this_week'){
                        $beginThisWeek = date('Y-m-d', strtotime('monday this week'));
                        $endThisWeek   = date('Y-m-d', strtotime('sunday this week'));
                        array_push($where, ' AND t.t_date BETWEEN "' . $beginThisWeek . '" AND "' . $endThisWeek.'"');
                    }
                    //last week
                    elseif($range=='last_week'){
                        $beginLastWeek = date('Y-m-d', strtotime('monday last week'));
                        $endLastWeek   = date('Y-m-d', strtotime('sunday last week'));
                        array_push($where, ' AND t.t_date BETWEEN "' . $beginLastWeek . '" AND "' . $endLastWeek.'"');
                    }
                    //this month
                    elseif($range=='this_month'){
                        $beginThisMonth = date('Y-m-01');
                        $endThisMonth   = date('Y-m-d');
                        array_push($where, ' AND t.t_date BETWEEN "' . $beginThisMonth . '" AND "' . $endThisMonth.'"');
                    }
                    //last month
                    elseif($range=='last_month'){
                        $beginLastMonth = date('Y-m-01', strtotime('-1 MONTH'));
                        $endLastMonth   = date('Y-m-t', strtotime('-1 MONTH'));
                        array_push($where, ' AND t.t_date BETWEEN "' . $beginLastMonth . '" AND "' . $endLastMonth.'"');
                    }
                    //this year
                    elseif($range=='this_year'){
                        $beginOfDay = date('Y-01-01');
                        $endOfDay   = date('Y-12-31');
                        array_push($where, ' AND t.t_date BETWEEN "' . $beginOfDay . '" AND "' . $endOfDay.'"');
                    }
                    //last year
                    elseif($range=='last_year'){
                        $beginOfDay = date('Y-01-01', strtotime('-1 YEAR'));
                        $endOfDay   = date('Y-12-t', strtotime('-1 YEAR'));
                        array_push($where, ' AND t.t_date BETWEEN "' . $beginOfDay . '" AND "' . $endOfDay.'"');
                    }
                    //search by periods
                    elseif($range=='period'){
                        $start_date = to_sql_date($post_data['periodfrom']);
                        $end_date   = to_sql_date($post_data['periodto']);
                        array_push($where, ' AND t.t_date BETWEEN "' . $start_date . '" AND "' . $end_date.'"');
                    }
                    //all
                    elseif($range=='all'){
                        
                    }
                }
                
                //echo "<pre>"; print_r($where); exit;
                $where = implode(' ', $where);
                $where = trim($where);
                if (startsWith($where, 'AND') || startsWith($where, 'OR')) {
                    if (startsWith($where, 'OR')) {
                        $where = substr($where, 2);
                    } else {
                        $where = substr($where, 3);
                    }
                    $where = 'WHERE ' . $where;
                }
                $query = '
                    SELECT count(t.staff_id) as total_activities, t.sam_id, t.staff_id, t.pipeline_id 
                    FROM tbl_sam_transactions as t
                    JOIN tbl_sam as t2 on t.sam_id = t2.id                               
                    '."$where".'
                    group by t.staff_id
                
                ';
                //echo $query; exit;
                $result = $this->db->query($query)->result_array();
                if($result){
                    //echo "<pre>"; print_r($result);exit;
                    return $result;
                }
                else{
                    return false;
                }
                
            }    
        }
    }
    public function getKPIEmpPer($post_data = array(), $default = false)
    {
        $CI                                     = &get_instance();
        $kpi_data                               = [];
        $staff_id                               = isset($post_data['staff_id']) ? $post_data['staff_id'] : '';
        $range                                  = isset($post_data['range']) ? $post_data['range'] : '';
        $beginThisMonth                         = date('Y-m-01');
        $endThisMonth                           = date('Y-m-d');
        $beginThisWeek                          = date('Y-m-d', strtotime('monday this week'));
        $endThisWeek                            = date('Y-m-d', strtotime('sunday this week'));
        $beginThisYear                          = date('Y-01-01');
        $beginLastYear                          = date('Y-01-01', strtotime('-1 YEAR')); 
        $endLastYear                            = date('Y-12-31', strtotime('-1 YEAR'));
        $endThisYear                            = date('Y-m-d');
        $beginToday                             = date('Y-m-d');
        $endToday                               = date('Y-m-d');
        $beginlastsevendays                     = date('Y-m-d 00:00:00', strtotime('-7 DAYS'));
        $endlastsevendays                       = date('Y-m-d 23:59:59');
        $beginYesterday                         = date('Y-m-d', strtotime('-1 day'));
        $endYesterday                           = date('Y-m-d', strtotime('-1 day'));
        $start_date                             = null;
        $end_date                               = null;

    
        $where = [];
        if (!empty($staff_id)) 
        {
            $where[]                        = 'e.staffid = ' . $this->db->escape_str($staff_id);
        }
        $where_clause                       = !empty($where) ? ' AND ' . implode(' AND ', $where) : ''; 
    
     $query                                 = "SELECT 
                                                 e.staffid, 
                                                 CONCAT(s.firstname, ' ', s.lastname) AS staff_name, 
                                                 s.datecreated,
                                                 e.kpi_function_id, 
                                                 k.kpi_name, 
                                                 k.kpi_count, 
                                                 e.currencyid,
                                                 e.category_id,
                                                 e.no_of_actions, 
                                                 e.day, 
                                                 e.week, 
                                                 e.month, 
                                                 e.year, 
                                                 ct.category_name AS category_name,
                                                 c.symbol AS currency_symbol,
                                                 cn.country_name 
                                              FROM 
                                                 tbl_sam_employee_kpi e
                                              JOIN 
                                                 tbl_sam_kpi_function k ON e.kpi_function_id = k.kpi_function_id
                                              LEFT JOIN
                                                 tblcurrencies c ON e.currencyid = c.id
                                              LEFT JOIN
                                                 tbl_sam_category ct ON e.category_id = ct.category_id
                                              LEFT JOIN 
                                                 tblstaff s ON e.staffid = s.staffid
                                              LEFT JOIN 
                                                 tbl_sam_employee_country ec ON e.staffid = ec.staffid
                                              LEFT JOIN 
                                                 tbl_sam_employee_multiple_country emc ON ec.employee_country_id = emc.employee_country_id
                                              LEFT JOIN 
                                                 tbl_sam_country cn ON emc.country_id = cn.country_id
                                              WHERE 1=1 " . $where_clause;
    
    
    
                $result                     = $this->db->query($query)->result_array();


                if ($result) {
                    $is_staff_null = empty($post_data['staff_id']);
                    $kpi_data = [];
                
                    foreach ($result as &$row)
                    {
                        $start_date                 = null;
                        $end_date                   = null;
                
                        if ($range == 'period' && !empty($post_data['periodfrom']) && !empty($post_data['periodto'])) 
                        {
                            $start_date             = to_sql_date($post_data['periodfrom']);
                            $end_date               = to_sql_date($post_data['periodto']);
                        }
                
                        $kpi_calculation            = $this->calculateKPI($row, $range, $start_date, $end_date);
                        $date_created               = new DateTime($row['datecreated']);
                        $current_date               = new DateTime();
                        $interval                   = $date_created->diff($current_date);
                        $all_data                   = ($interval->y * $row['year']);
                        $percentage                 = $kpi_calculation['percentage'];
                        $actions_count              = ($kpi_calculation['actions_count'] !== null) ? (int) rtrim(rtrim($kpi_calculation['actions_count'], '0'), '.') : 0;
                        if (in_array($row['kpi_name'], ['Time Sheet', 'Desktime']))
                        {
                            $actions_count          = $kpi_calculation['actions_count']; 
                        }
                        if ($range == 'this_month' || $range == 'last_month')
                        {
                            $actions                = $row['month'];
                        } 
                        elseif ($range == 'this_week' || $range == 'last_week' || $range == 'last_seven_days') 
                        {
                            $actions                = $row['week'];
                        } 
                        elseif ($range == 'this_year' || $range == 'last_year')
                        {
                            $actions                = $row['year'];
                        } 
                        elseif ($range == 'today' || $range == 'yesterday')
                        {
                            $actions                = $row['day'];
                        } 
                        elseif ($range == 'all') 
                        {
                            $actions                = number_format($all_data, 1, '.', '');
                        }
                        elseif ($range == 'period' && !empty($post_data['periodfrom']) && !empty($post_data['periodto']))
                        {
                            $created_at             = new DateTime($row['datecreated']);
                            if ($created_at >= new DateTime($start_date) && $created_at <= new DateTime($end_date)) 
                            {
                                $actions            = number_format(($interval->y * $row['year']), 1, '.', '');
                            } 
                            else 
                            {
                                $actions            = number_format($this->calculateActionsFromCreatedDateToEnd($row, $start_date, $end_date), 1, '.', '');
                            }
                        }


                        if (in_array($row['kpi_name'], ['Time Sheet', 'Desktime']))
                        {
                            $row['formatted_actions'] = $actions . ":00";
                        } else {
                            $row['formatted_actions'] = $actions;
                        }                                            

                        if ($is_staff_null) 
                        {
                            $key                    = $row['kpi_name'];
                            
                            if ($row['kpi_name'] === 'Sales') 
                            {
                                $key .= '_' . $row['staff_name']; 
                            }
                        
                            if (in_array($row['kpi_name'], ['Time Sheet', 'Desktime'])) 
                            {
                                $formatted_actions  = $actions . ":00";
                            } 
                            else
                            {
                                $formatted_actions  = $actions;
                            }
                        
                            if (!isset($kpi_data[$key])) 
                            {
                                $kpi_data[$key]     = $row;
                                $kpi_data[$key]['actions_count']     = $actions_count;
                                $kpi_data[$key]['formatted_actions'] = $formatted_actions;
                            } 
                            else
                            {
                                if ($row['kpi_name'] !== 'Sales') {
                                    $kpi_data[$key]['actions_count']        += $actions_count;
                                    $kpi_data[$key]['formatted_actions']    += $formatted_actions;
                                }
                            }
                        }

                        else
                        {
                            $kpi_data[]             = array_merge($row, ['percentage' => $percentage], ['actions_count' => $actions_count]);
                        }
                    }
                
                    return $is_staff_null ? array_values($kpi_data) : $kpi_data;
                }

        return false;
    }
   private function calculateActionsFromCreatedDateToEnd($row, $start_date, $end_date)
    {
        $created_at                                 = new DateTime($row['datecreated']);
        $period_end                                 = new DateTime($end_date);
        $period_start                               = new DateTime($start_date);
    
        if ($created_at <= $period_end) 
        {
            $interval                               = $created_at->diff($period_end);
            $total_days                             = max($interval->days, 1); 
            $date_diff                              = max($period_end->diff($period_start)->days, 1);
    
            if ($date_diff == 1)
            {
                $actions                            = $row['day'];
            } 
            elseif ($date_diff <= 7) 
            {
                $actions                            = $row['week']; 
            } 
            elseif ($date_diff > 30 && $date_diff <= 365) 
            {
                $actions                            = $row['year']; 
            } 
            else
            {
                $actions                            = ($total_days * $row['no_of_actions']) / $date_diff; 
            }
    
            return $actions;
        }
    
        return 0;
    }
    
    public function getKPIWeight($staffid, $kpi_function_id)
    {
        $this->db->select('kpi_weight');
        $this->db->from('tbl_sam_overallperformance');
        $this->db->where('staffid', $staffid);
        $this->db->where('kpi_function_id', $kpi_function_id);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->row()->kpi_weight : 0;
    }



    
    public function calculateKPI($row, $range, $start_date = null, $end_date = null)
    {
        $today                              = date('Y-m-d');
        $range                              = $range ?? 'today';
        $staffid                            = $row['staffid'] ?? 0;
        $date_ranges
        =
        [
            'this_month'                    => [date('Y-m-01'), date('Y-m-d')],
            'last_month'                    => [date('Y-m-01', strtotime('-1 month')), 
                                                date('Y-m-t', strtotime('-1 month'))],
            'this_week'                     => [date('Y-m-d', strtotime('last saturday')),
                                                date('Y-m-d', strtotime('friday this week'))],
            // 'last_week'                     => [date('Y-m-d', strtotime('saturday -2 weeks')),
            //                                     date('Y-m-d', strtotime('friday last week'))],
            'last_week'                     => [date('Y-m-d', strtotime('monday last week')), 
                                                date('Y-m-d', strtotime('sunday last week'))],
            'this_year'                     => [date('Y-01-01'), date('Y-m-d')],
            'last_year'                     => [date('Y-01-01', strtotime('-1 year')), 
                                                date('Y-12-31', strtotime('-1 year'))],
            'last_seven_days'               => [date('Y-m-d', strtotime('-7 days')), 
                                                date('Y-m-d')],
            'today'                         => [date('Y-m-d'), date('Y-m-d')],
            'yesterday'                     => [date('Y-m-d', strtotime('-1 day')), 
                                                date('Y-m-d', strtotime('-1 day'))],
            'all'                           => [isset($row['datecreated']) ? $row['datecreated'] : '1970-01-01', date('Y-m-d')]
        ];
        
        if ($range == 'period' && !empty($start_date) && !empty($end_date)) 
        {
            $date_ranges['period'] = [$start_date, $end_date];
        }
        
        [$beginDate, $endDate]              = $date_ranges[$range] ?? $date_ranges['today'];
        $this->db->select
        ("
            SUM(customer_count)                                                                 as customer_count, 
            SUM(call_count)                                                                     as call_count, 
            SUM(metting_count)                                                                  as metting_count, 
            SUM(email_count)                                                                    as email_count, 
            SUM(contact_count)                                                                  as contact_count, 
            SUM(total_invoice)                                                                  as total_invoice, 
            SUM(uniranks_schools_count)                                                         as uniranks_schools_count, 
            SUM(uniranks_agents_count)                                                          as uniranks_agents_count, 
            SUM(uniranks_students_count)                                                        as uniranks_students_count, 
            SUM(uniranks_students_certification_count)                                          as uniranks_students_certification_count, 
            SUM(uniranks_progress_count)                                                        as uniranks_progress_count, 
            SUM(uniranks_coupon_count)                                                          as uniranks_coupon_count, 
            SUM(uniranks_fair_count)                                                            as uniranks_fair_count, 
            SUM(uniranks_events_count)                                                          as uniranks_events_count,
            DATE_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(COALESCE(desk_time, '00:00')))), '%H:%i')   as desk_time,
            DATE_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(COALESCE(time_sheet, '00:00')))), '%H:%i')  as time_sheet
        ");
    
    
        
        $this->db->from('tbl_sam_daily_statistics');
        $this->db->where('staffid', $staffid);
        $this->db->where("date_created BETWEEN '$beginDate' AND '$endDate'");
        
        $query                              = $this->db->get();
        $statistics                         = $query->row_array();
                        // echo "<pre>"; print_r($statistics);exit;
        $matched_schools_count              = $statistics['uniranks_schools_count']                                                     ?? 0;
        $matched_agent_count                = $statistics['uniranks_agents_count']                                                      ?? 0;
        $matched_students_count             = $statistics['uniranks_students_count']                                                    ?? 0;
        $matched_students_certification_count= $statistics['uniranks_students_certification_count']                                     ?? 0;
        $matched_progress_count             = $statistics['uniranks_progress_count']                                                    ?? 0;
        $matched_fairs_count                = $statistics['uniranks_fair_count']                                                        ?? 0;
        $matched_events_count               = $statistics['uniranks_events_count']                                                      ?? 0;
        $matched_desk_time                  = $statistics['desk_time']                                                                  ?? 0;
        $matched_time_sheet                 = $statistics['time_sheet']                                                                 ?? 0;
        $matched_coupon_count               = $statistics['uniranks_coupon_count']                                                      ?? 0;
        $matched_customer_count             = $statistics['customer_count']                                                             ?? 0;
        $matched_contact_count              = $statistics['contact_count']                                                              ?? 0;
        $matched_total_invoice              = $statistics['total_invoice']                                                              ?? 0;
        $matched_communication_count        = ($statistics['call_count']                                                                ?? 0) + 
                                              ($statistics['metting_count']                                                             ?? 0) + 
                                              ($statistics['email_count']                                                               ?? 0);
    
        
        if ($row['kpi_count'] == 'Number' && $row['kpi_name'] == 'Communication')
        {
            if ($range == 'this_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_communication_count;
    
                if ($month_data > 0)
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range 
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'range'         => $range 
                        
                    ];
                }
            } 
            elseif ($range == 'last_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_communication_count;
    
                if ($month_data > 0)
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range 
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'range'         => $range
                    ];
                }
            } 
            elseif ($range == 'last_seven_days')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_communication_count;
    
                if ($week_data > 0)
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                }
            } 
            elseif ($range == 'this_week')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_communication_count;
    
                if ($week_data > 0)
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                }
            } 
            elseif ($range == 'last_week')
            {
                $week_data              = $row['week'];
                 $actions_count          = $matched_communication_count;
    
                if ($week_data > 0)
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                }
            } 
            elseif ($range == 'this_year') 
            {
                $year_data              = $row['year'];
                $actions_count          = $matched_communication_count;
    
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                }
            } 
            elseif ($range == 'last_year') 
            {
                $year_data              = $row['year'];
                 $actions_count          = $matched_communication_count;
    
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            } 
            elseif ($range == 'today')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_communication_count;
    
                if ($day_data > 0)
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            }
            elseif ($range == 'yesterday')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_communication_count;
    
                if ($day_data > 0)
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            }
            elseif ($range == 'all')
            {
                $date_created           = new DateTime($row['datecreated']);
                $current_date           = new DateTime();
                $interval               = $date_created->diff($current_date);
                $all_data               = ($interval->y * $row['year']);
                $actions_count          = $matched_communication_count;
    
                if ($all_data > 0)
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            }
            elseif ($range == 'period' && $start_date && $end_date) {
                $start_date     = new DateTime($start_date);
                $end_date       = new DateTime($end_date);
                
                $date_created   = new DateTime($row['datecreated']);
                $interval_start = $date_created->diff($start_date); 
                $interval_end   = $start_date->diff($end_date);
                
                $all_data       = ($interval_start->y * $row['year']) + 
                                  ($interval_start->m * $row['month']) + 
                                  ($interval_start->d * $row['day']) + 
                                  ($interval_start->days * $row['week']);
                $actions_count          = $matched_communication_count;
    
                if ($all_data > 0)
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'range'         => $range
                    ];
                }
            }
        }
        else if ($row['kpi_count'] == 'Number' && $row['kpi_name'] == 'New Customers')
        {
            if ($range == 'this_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_customer_count;
    
                if ($month_data > 0)
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range 
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'range'         => $range 
                        
                    ];
                }
            } 
            elseif ($range == 'last_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_customer_count;
    
                if ($month_data > 0)
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range 
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'range'         => $range
                    ];
                }
            } 
            elseif ($range == 'last_seven_days')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_customer_count;
    
                if ($week_data > 0)
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                }
            } 
            elseif ($range == 'this_week')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_customer_count;
    
                if ($week_data > 0)
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                }
            } 
            elseif ($range == 'last_week')
            {
                $week_data              = $row['week'];
                 $actions_count          = $matched_customer_count;
    
                if ($week_data > 0)
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                }
            } 
            elseif ($range == 'this_year') 
            {
                $year_data              = $row['year'];
                $actions_count          = $matched_customer_count;
    
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                }
            } 
            elseif ($range == 'last_year') 
            {
                $year_data              = $row['year'];
                 $actions_count          = $matched_customer_count;
    
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            } 
            elseif ($range == 'today')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_customer_count;
    
                if ($day_data > 0)
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            }
            elseif ($range == 'yesterday')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_customer_count;
    
                if ($day_data > 0)
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            }
            elseif ($range == 'all')
            {
                $date_created           = new DateTime($row['datecreated']);
                $current_date           = new DateTime();
                $interval               = $date_created->diff($current_date);
                $all_data               = ($interval->y * $row['year']);
                $actions_count          = $matched_customer_count;
    
                if ($all_data > 0)
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            }
            elseif ($range == 'period' && $start_date && $end_date) {
                $start_date     = new DateTime($start_date);
                $end_date       = new DateTime($end_date);
                
                $date_created   = new DateTime($row['datecreated']);
                $interval_start = $date_created->diff($start_date); 
                $interval_end   = $start_date->diff($end_date);
                
                $all_data       = ($interval_start->y * $row['year']) + 
                                  ($interval_start->m * $row['month']) + 
                                  ($interval_start->d * $row['day']) + 
                                  ($interval_start->days * $row['week']);
                $actions_count          = $matched_customer_count;
    
                if ($all_data > 0)
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'range'         => $range
                    ];
                }
            }
        }
        else if ($row['kpi_count'] == 'Number' && $row['kpi_name'] == 'New Contacts')
        {
            if ($range == 'this_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_contact_count;
    
                if ($month_data > 0)
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range 
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'range'         => $range 
                        
                    ];
                }
            } 
            elseif ($range == 'last_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_contact_count;
    
                if ($month_data > 0)
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range 
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'range'         => $range
                    ];
                }
            } 
            elseif ($range == 'last_seven_days')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_contact_count;
    
                if ($week_data > 0)
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                }
            } 
            elseif ($range == 'this_week')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_contact_count;
    
                if ($week_data > 0)
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                }
            } 
            elseif ($range == 'last_week')
            {
                $week_data              = $row['week'];
                 $actions_count          = $matched_contact_count;
    
                if ($week_data > 0)
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                }
            } 
            elseif ($range == 'this_year') 
            {
                $year_data              = $row['year'];
                $actions_count          = $matched_contact_count;
    
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                }
            } 
            elseif ($range == 'last_year') 
            {
                $year_data              = $row['year'];
                 $actions_count          = $matched_contact_count;
    
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            } 
            elseif ($range == 'today')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_contact_count;
    
                if ($day_data > 0)
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            }
            elseif ($range == 'yesterday')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_contact_count;
    
                if ($day_data > 0)
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            }
            elseif ($range == 'all')
            {
                $date_created           = new DateTime($row['datecreated']);
                $current_date           = new DateTime();
                $interval               = $date_created->diff($current_date);
                $all_data               = ($interval->y * $row['year']);
                $actions_count          = $matched_contact_count;
    
                if ($all_data > 0)
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            }
            elseif ($range == 'period' && $start_date && $end_date) {
                $start_date     = new DateTime($start_date);
                $end_date       = new DateTime($end_date);
                
                $date_created   = new DateTime($row['datecreated']);
                $interval_start = $date_created->diff($start_date); 
                $interval_end   = $start_date->diff($end_date);
                
                $all_data       = ($interval_start->y * $row['year']) + 
                                  ($interval_start->m * $row['month']) + 
                                  ($interval_start->d * $row['day']) + 
                                  ($interval_start->days * $row['week']);
                $actions_count          = $matched_contact_count;
    
                if ($all_data > 0)
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'range'         => $range
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'range'         => $range
                    ];
                }
            }
        }
        else if ($row['kpi_count'] == 'Time' && $row['kpi_name'] == 'Time Sheet')
        {
            if ($range == 'this_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_time_sheet;
    
                if ($month_data > 0)
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            } 
            elseif ($range == 'last_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_time_sheet;
    
                if ($month_data > 0)
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            } 
            elseif ($range == 'last_seven_days')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_time_sheet;
    
                if ($week_data > 0)
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            } 
            elseif ($range == 'this_week')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_time_sheet;
    
                if ($week_data > 0)
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            } 
            elseif ($range == 'last_week')
            {
                $week_data              = $row['week'];
                 $actions_count          = $matched_time_sheet;
    
                if ($week_data > 0)
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            } 
            elseif ($range == 'this_year') 
            {
                $year_data              = $row['year'];
                $actions_count          = $matched_time_sheet;
    
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            } 
            elseif ($range == 'last_year') 
            {
                $year_data              = $row['year'];
                 $actions_count          = $matched_time_sheet;
    
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            } 
            elseif ($range == 'today')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_time_sheet;
    
                if ($day_data > 0)
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            }
            elseif ($range == 'yesterday')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_time_sheet;
    
                if ($day_data > 0)
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            }
            elseif ($range == 'all')
            {
                $date_created           = new DateTime($row['datecreated']);
                $current_date           = new DateTime();
                $interval               = $date_created->diff($current_date);
                $all_data               = ($interval->y * $row['year']);
                $actions_count          = $matched_time_sheet;
    
                if ($all_data > 0)
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            }
            elseif ($range == 'period' && $start_date && $end_date) {
                $start_date     = new DateTime($start_date);
                $end_date       = new DateTime($end_date);
                
                $date_created   = new DateTime($row['datecreated']);
                $interval_start = $date_created->diff($start_date); 
                $interval_end   = $start_date->diff($end_date);
                
                $all_data       = ($interval_start->y * $row['year']) + 
                                  ($interval_start->m * $row['month']) + 
                                  ($interval_start->d * $row['day']) + 
                                  ($interval_start->days * $row['week']);
                $actions_count          = $matched_time_sheet;
    
                if ($all_data > 0)
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            }
        }
        else if ($row['kpi_count'] == 'Time' && $row['kpi_name'] == 'Desktime')
        {
            if ($range == 'this_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_desk_time;
    
                if ($month_data > 0)
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            } 
            elseif ($range == 'last_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_desk_time;
    
                if ($month_data > 0)
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            } 
            elseif ($range == 'last_seven_days')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_desk_time;
    
                if ($week_data > 0)
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            } 
            elseif ($range == 'this_week')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_desk_time;
    
                if ($week_data > 0)
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            } 
            elseif ($range == 'last_week')
            {
                $week_data              = $row['week'];
                 $actions_count          = $matched_desk_time;
    
                if ($week_data > 0)
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            } 
            elseif ($range == 'this_year') 
            {
                $year_data              = $row['year'];
                $actions_count          = $matched_desk_time;
    
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            } 
            elseif ($range == 'last_year') 
            {
                $year_data              = $row['year'];
                 $actions_count          = $matched_desk_time;
    
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            } 
            elseif ($range == 'today')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_desk_time;
    
                if ($day_data > 0)
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            }
            elseif ($range == 'yesterday')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_desk_time;
    
                if ($day_data > 0)
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            }
            elseif ($range == 'all')
            {
                $date_created           = new DateTime($row['datecreated']);
                $current_date           = new DateTime();
                $interval               = $date_created->diff($current_date);
                $all_data               = ($interval->y * $row['year']);
                $actions_count          = $matched_desk_time;
    
                if ($all_data > 0)
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            }
            elseif ($range == 'period' && $start_date && $end_date) {
                $start_date     = new DateTime($start_date);
                $end_date       = new DateTime($end_date);
                
                $date_created   = new DateTime($row['datecreated']);
                $interval_start = $date_created->diff($start_date); 
                $interval_end   = $start_date->diff($end_date);
                
                $all_data       = ($interval_start->y * $row['year']) + 
                                  ($interval_start->m * $row['month']) + 
                                  ($interval_start->d * $row['day']) + 
                                  ($interval_start->days * $row['week']);
                $actions_count          = $matched_desk_time;
    
                if ($all_data > 0)
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            }
    
    
    
        }
        else if ($row['kpi_count'] == 'Number' && $row['kpi_name'] == 'Schools Registrations')
        {
            if ($range == 'this_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_schools_count;
    
                if ($month_data > 0)
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_schools_count
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_schools_count
                    ];
                }
            } 
            elseif ($range == 'last_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_schools_count;
    
                if ($month_data > 0) 
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_schools_count
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_schools_count
                    ];
                }
            } 
            elseif ($range == 'last_seven_days')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_schools_count;
    
                if ($week_data > 0) 
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_schools_count
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_schools_count
                    ];
                }
            } 
            elseif ($range == 'this_week')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_schools_count;
    
                if ($week_data > 0) 
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_schools_count
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_schools_count
                    ];
                }
            } 
            elseif ($range == 'last_week')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_schools_count;
    
                if ($week_data > 0) 
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_schools_count
                    ];
                }
                else
                {
                    return 
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_schools_count
                    ];
                }
            } 
            elseif ($range == 'this_year') 
            {
                $year_data              = $row['year'];
                $actions_count          = $matched_schools_count;
    
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_schools_count
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_schools_count
                    ];
                }
            } 
            elseif ($range == 'last_year') 
            {
                $year_data              = $row['year'];
                $actions_count          = $matched_schools_count;
    
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_schools_count
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_schools_count
                    ];
                }
            } 
            elseif ($range == 'today')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_schools_count;
    
                if ($day_data > 0)
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_schools_count
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_schools_count
                    ];
                }
            }
            elseif ($range == 'yesterday')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_schools_count;
    
                if ($day_data > 0)
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_schools_count
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_schools_count
                    ];
                }
            }
            elseif ($range == 'all')
            {
                $date_created           = new DateTime($row['datecreated']);
                $current_date           = new DateTime();
                $interval               = $date_created->diff($current_date);
                $all_data               = ($interval->y * $row['year']);
                $actions_count          = $matched_schools_count;
            
                if ($all_data > 0)
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_schools_count
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_schools_count
                    ];
                }
            }
            elseif ($range == 'period' && $start_date && $end_date) {
                $start_date     = new DateTime($start_date);
                $end_date       = new DateTime($end_date);
                
                $date_created   = new DateTime($row['datecreated']);
                $interval_start = $date_created->diff($start_date); 
                $interval_end   = $start_date->diff($end_date);
                
                $all_data       = ($interval_start->y * $row['year']) + 
                                  ($interval_start->m * $row['month']) + 
                                  ($interval_start->d * $row['day']) + 
                                  ($interval_start->days * $row['week']);
                $actions_count  = $matched_schools_count;
            
                if ($all_data > 0)
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_schools_count
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_schools_count
                    ];
                }
            }
    
    
    
        }
        else if ($row['kpi_count'] == 'Number' && $row['kpi_name'] == 'Registerd Agents')
        {
            if ($range == 'this_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_agent_count;
    
                if ($month_data > 0)
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_agent_count
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_agent_count
                    ];
                }
            } 
            elseif ($range == 'last_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_agent_count;
    
                if ($month_data > 0) 
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_agent_count
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_agent_count
                    ];
                }
            } 
            elseif ($range == 'last_seven_days')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_agent_count;
    
                if ($week_data > 0) 
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_agent_count
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_agent_count
                    ];
                }
            } 
            elseif ($range == 'this_week')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_agent_count;
    
                if ($week_data > 0) 
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_agent_count
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_agent_count
                    ];
                }
            } 
            elseif ($range == 'last_week')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_agent_count;
    
                if ($week_data > 0) 
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_agent_count
                    ];
                }
                else
                {
                    return 
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_agent_count
                    ];
                }
            } 
            elseif ($range == 'this_year') 
            {
                $year_data              = $row['year'];
                $actions_count          = $matched_agent_count;
    
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_agent_count
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_agent_count
                    ];
                }
            } 
            elseif ($range == 'last_year') 
            {
                $year_data              = $row['year'];
                $actions_count          = $matched_agent_count;
    
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_agent_count
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_agent_count
                    ];
                }
            } 
            elseif ($range == 'today')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_agent_count;
    
                if ($day_data > 0)
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_agent_count
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_agent_count
                    ];
                }
            }
            elseif ($range == 'yesterday')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_agent_count;
    
                if ($day_data > 0)
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_agent_count
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_agent_count
                    ];
                }
            }
            elseif ($range == 'all')
            {
                $date_created           = new DateTime($row['datecreated']);
                $current_date           = new DateTime();
                $interval               = $date_created->diff($current_date);
                $all_data               = ($interval->y * $row['year']);
                $actions_count          = $matched_agent_count;
            
                if ($all_data > 0)
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_agent_count
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_agent_count
                    ];
                }
            }
            elseif ($range == 'period' && $start_date && $end_date) {
                $start_date     = new DateTime($start_date);
                $end_date       = new DateTime($end_date);
                
                $date_created   = new DateTime($row['datecreated']);
                $interval_start = $date_created->diff($start_date); 
                $interval_end   = $start_date->diff($end_date);
                
                $all_data       = ($interval_start->y * $row['year']) + 
                                  ($interval_start->m * $row['month']) + 
                                  ($interval_start->d * $row['day']) + 
                                  ($interval_start->days * $row['week']);
                $actions_count  = $matched_agent_count;
            
                if ($all_data > 0)
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_agent_count
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_agent_count
                    ];
                }
            }
    
    
    
        }
        else if ($row['kpi_count'] == 'Number' && $row['kpi_name'] == 'Students Registrations')
        {
            if ($range == 'this_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_students_count;
    
                if ($month_data > 0)
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'          => round($percentage, 2),
                        'actions_count'       => $actions_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'            => 0,
                        'actions_count'         => $actions_count,
                    ];
                }
            } 
            elseif ($range == 'last_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_students_count;
                if ($month_data > 0) 
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            } 
            elseif ($range == 'last_seven_days')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_students_count;
    
                if ($week_data > 0) 
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            } 
            elseif ($range == 'this_week')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_students_count;
    
                if ($week_data > 0) 
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            } 
            elseif ($range == 'last_week')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_students_count;
    
                if ($week_data > 0) 
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                    ];
                }
                else
                {
                    return 
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            } 
            elseif ($range == 'this_year') 
            {
                $year_data              = $row['year'];
                $actions_count          = $matched_students_count;
    
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            } 
            elseif ($range == 'last_year') 
            {
                $year_data              = $row['year'];
                $actions_count          = $matched_students_count;
    
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            } 
            elseif ($range == 'today')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_students_count;
    
                if ($day_data > 0)
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            }
            elseif ($range == 'yesterday')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_students_count;
    
                if ($day_data > 0)
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            }
            elseif ($range == 'all')
            {
                $date_created           = new DateTime($row['datecreated']);
                $current_date           = new DateTime();
                $interval               = $date_created->diff($current_date);
                $all_data               = ($interval->y * $row['year']);
                $actions_count          = $matched_students_count;
    
                if ($all_data > 0)
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            }
            elseif ($range == 'period' && $start_date && $end_date) {
                $start_date     = new DateTime($start_date);
                $end_date       = new DateTime($end_date);
                $date_created   = new DateTime($row['datecreated']);
                $interval_start = $date_created->diff($start_date); 
                $interval_end   = $start_date->diff($end_date);
                $all_data       = ($interval->y * $row['year']) + ($interval->m * $row['month']) + ($interval->d * $row['day']) + ($interval->days * $row['week']);
                $actions_count  = $matched_students_count;
    
                if ($all_data > 0)
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
    
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            }
        }
        else if ($row['kpi_count'] == 'Number' && $row['kpi_name'] == 'Students Certification')
        {
            if ($range == 'this_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_students_certification_count;
    
                if ($month_data > 0)
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'          => round($percentage, 2),
                        'actions_count'       => $actions_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'            => 0,
                        'actions_count'         => $actions_count,
                    ];
                }
            } 
            elseif ($range == 'last_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_students_certification_count;
                if ($month_data > 0) 
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            } 
            elseif ($range == 'last_seven_days')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_students_certification_count;
    
                if ($week_data > 0) 
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            } 
            elseif ($range == 'this_week')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_students_certification_count;
    
                if ($week_data > 0) 
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            } 
            elseif ($range == 'last_week')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_students_certification_count;
    
                if ($week_data > 0) 
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                    ];
                }
                else
                {
                    return 
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            } 
            elseif ($range == 'this_year') 
            {
                $year_data              = $row['year'];
                $actions_count          = $matched_students_certification_count;
    
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            } 
            elseif ($range == 'last_year') 
            {
                $year_data              = $row['year'];
                $actions_count          = $matched_students_certification_count;
    
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            } 
            elseif ($range == 'today')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_students_certification_count;
    
                if ($day_data > 0)
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            }
            elseif ($range == 'yesterday')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_students_certification_count;
    
                if ($day_data > 0)
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            }
            elseif ($range == 'all')
            {
                $date_created           = new DateTime($row['datecreated']);
                $current_date           = new DateTime();
                $interval               = $date_created->diff($current_date);
                $all_data               = ($interval->y * $row['year']);
                $actions_count          = $matched_students_certification_count;
    
                if ($all_data > 0)
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            }
            elseif ($range == 'period' && $start_date && $end_date) {
                $start_date     = new DateTime($start_date);
                $end_date       = new DateTime($end_date);
                $date_created   = new DateTime($row['datecreated']);
                $interval_start = $date_created->diff($start_date); 
                $interval_end   = $start_date->diff($end_date);
                $all_data       = ($interval->y * $row['year']) + ($interval->m * $row['month']) + ($interval->d * $row['day']) + ($interval->days * $row['week']);
                $actions_count  = $matched_students_certification_count;
    
                if ($all_data > 0)
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
    
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            }
        }
        else if ($row['kpi_count'] == 'Number' && $row['kpi_name'] == 'Profile Progress')
        {
            if ($range == 'this_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_progress_count;
    
                if ($month_data > 0)
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'          => round($percentage, 2),
                        'actions_count'       => $actions_count,
                        'schools_count'       => $matched_progress_count,
                        'progress_data'       => $matched_progress_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'            => 0,
                        'actions_count'         => $actions_count,
                        'schools_count'         => $matched_progress_count,
                        'progress_data'         => $matched_progress_count,
                    ];
                }
            } 
            elseif ($range == 'last_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_progress_count;
                if ($month_data > 0) 
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        'schools_count' => $matched_progress_count,
                        'progress_data' => $matched_progress_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                           'schools_count' => $matched_progress_count,
                        'progress_data' => $matched_progress_count,
                    ];
                }
            } 
            elseif ($range == 'last_seven_days')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_progress_count;
    
                if ($week_data > 0) 
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                           'schools_count' => $matched_progress_count,
                        'progress_data' => $matched_progress_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                           'schools_count' => $matched_progress_count,
                        'progress_data' => $matched_progress_count,
                    ];
                }
            } 
            elseif ($range == 'this_week')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_progress_count;
    
                if ($week_data > 0) 
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                          'schools_count' => $matched_progress_count,
                        'progress_data' => $matched_progress_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                          'schools_count' => $matched_progress_count,
                        'progress_data' => $matched_progress_count,
                    ];
                }
            } 
            elseif ($range == 'last_week')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_progress_count;
    
                if ($week_data > 0) 
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                          'schools_count' => $matched_progress_count,
                        'progress_data' => $matched_progress_count,
                    ];
                }
                else
                {
                    return 
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                          'schools_count' => $matched_progress_count,
                        'progress_data' => $matched_progress_count,
                    ];
                }
            } 
            elseif ($range == 'this_year') 
            {
                $year_data              = $row['year'];
                $actions_count          = $matched_progress_count;
    
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                         'schools_count' => $matched_progress_count,
                        'progress_data' => $matched_progress_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                         'schools_count' => $matched_progress_count,
                        'progress_data' => $matched_progress_count,
                    ];
                }
            } 
            elseif ($range == 'last_year') 
            {
                $year_data              = $row['year'];
                $actions_count          = $matched_progress_count;
    
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                                'schools_count' => $matched_progress_count,
                        'progress_data' => $matched_progress_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                               'schools_count' => $matched_progress_count,
                        'progress_data' => $matched_progress_count,
                    ];
                }
            } 
            elseif ($range == 'today')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_progress_count;
    
                if ($day_data > 0)
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                              'schools_count' => $matched_progress_count,
                        'progress_data' => $matched_progress_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                              'schools_count' => $matched_progress_count,
                        'progress_data' => $matched_progress_count,
                    ];
                }
            }
            elseif ($range == 'yesterday')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_progress_count;
    
                if ($day_data > 0)
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                              'schools_count' => $matched_progress_count,
                        'progress_data' => $matched_progress_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                              'schools_count' => $matched_progress_count,
                        'progress_data' => $matched_progress_count,
                    ];
                }
            }
            elseif ($range == 'all')
            {
                $date_created           = new DateTime($row['datecreated']);
                $current_date           = new DateTime();
                $interval               = $date_created->diff($current_date);
                $all_data               = ($interval->y * $row['year']);
                $actions_count          = $matched_progress_count;
    
                if ($all_data > 0)
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                             'schools_count' => $matched_progress_count,
                        'progress_data' => $matched_progress_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                             'schools_count' => $matched_progress_count,
                        'progress_data' => $matched_progress_count,
                    ];
                }
            }
            elseif ($range == 'period' && $start_date && $end_date) {
                $start_date     = new DateTime($start_date);
                $end_date       = new DateTime($end_date);
                
                $date_created   = new DateTime($row['datecreated']);
                $interval_start = $date_created->diff($start_date); 
                $interval_end   = $start_date->diff($end_date);
                
                $all_data       = ($interval_start->y * $row['year']) + 
                                  ($interval_start->m * $row['month']) + 
                                  ($interval_start->d * $row['day']) + 
                                  ($interval_start->days * $row['week']);
                $actions_count          = $matched_progress_count;
    
                if ($all_data > 0)
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                            'schools_count' => $matched_progress_count,
                        'progress_data' => $matched_progress_count,
    
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                            'schools_count' => $matched_progress_count,
                        'progress_data' => $matched_progress_count,
                    ];
                }
            }
        }
        else if ($row['kpi_count'] == 'Number' && $row['kpi_name'] == 'Used Coupons')
        {
            if ($range == 'this_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_coupon_count;
    
                if ($month_data > 0)
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count, // Total coupon count
                        'coupon_data'   => $matched_coupon_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'coupon_data'   => $matched_coupon_count,
                    ];
                }
            } 
            elseif ($range == 'last_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_coupon_count;
                if ($month_data > 0) 
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count, // Total coupon count
                        'coupon_data'   => $matched_coupon_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count, // Total coupon count
                        'coupon_data'   => $matched_coupon_count,
                    ];
                }
            } 
            elseif ($range == 'last_seven_days')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_coupon_count;
    
                if ($week_data > 0) 
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count, // Total coupon count
                        'coupon_data'   => $matched_coupon_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'coupon_data'   => $matched_coupon_count,
                    ];
                }
            } 
            elseif ($range == 'this_week')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_coupon_count;
    
                if ($week_data > 0) 
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count, // Total coupon count
                        'coupon_data'   => $matched_coupon_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'coupon_data'   => $matched_coupon_count,
                    ];
                }
            } 
            elseif ($range == 'last_week')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_coupon_count;
    
                if ($week_data > 0) 
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count, // Total coupon count
                        'coupon_data'   => $matched_coupon_count,
                    ];
                }
                else
                {
                    return 
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'coupon_data'   => $matched_coupon_count,
                    ];
                }
            } 
            elseif ($range == 'this_year') 
            {
                $year_data              = $row['year'];
                $actions_count          = $matched_coupon_count;
    
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count, // Total coupon count
                        'coupon_data'   => $matched_coupon_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'coupon_data'   => $matched_coupon_count,
                    ];
                }
            } 
            elseif ($range == 'last_year') 
            {
                $year_data              = $row['year'];
                $actions_count          = $matched_coupon_count;
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count, // Total coupon count
                        'coupon_data'   => $matched_coupon_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'coupon_data'   => $matched_coupon_count,
                    ];
                }
            } 
            elseif ($range == 'today')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_coupon_count;
    
                if ($day_data > 0)
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count, // Total coupon count
                        'coupon_data'   => $matched_coupon_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'coupon_data'   => $matched_coupon_count,
                    ];
                }
            }
            elseif ($range == 'yesterday')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_coupon_count;
    
                if ($day_data > 0)
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count, // Total coupon count
                        'coupon_data'   => $matched_coupon_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'coupon_data'   => $matched_coupon_count,
                    ];
                }
            }
            elseif ($range == 'all')
            {
                $date_created           = new DateTime($row['datecreated']);
                $current_date           = new DateTime();
                $interval               = $date_created->diff($current_date);
                $all_data               = ($interval->y * $row['year']);
                $actions_count          = $matched_coupon_count;
    
                if ($all_data > 0)
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count, // Total coupon count
                        'coupon_data'   => $matched_coupon_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'coupon_data'   => $matched_coupon_count,
                    ];
                }
            }
            elseif ($range == 'period' && $start_date && $end_date) {
                $start_date     = new DateTime($start_date);
                $end_date       = new DateTime($end_date);
                
                $date_created   = new DateTime($row['datecreated']);
                $interval_start = $date_created->diff($start_date); 
                $interval_end   = $start_date->diff($end_date);
                
                $all_data       = ($interval_start->y * $row['year']) + 
                                  ($interval_start->m * $row['month']) + 
                                  ($interval_start->d * $row['day']) + 
                                  ($interval_start->days * $row['week']);
                $actions_count  = $matched_coupon_count;
    
                if ($all_data > 0)
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count, // Total coupon count
                        'coupon_data'   => $matched_coupon_count,
    
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'coupon_data'   => $matched_coupon_count,
    
                    ];
                }
            }
    
    
    
        }
        else if ($row['kpi_count'] == 'Number' && $row['kpi_name'] == 'University Fair')
        {
            if ($range == 'this_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_fairs_count;
    
                if ($month_data > 0)
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            } 
            elseif ($range == 'last_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_fairs_count;
    
                if ($month_data > 0)
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            } 
            elseif ($range == 'last_seven_days')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_fairs_count;
    
                if ($week_data > 0)
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            } 
            elseif ($range == 'this_week')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_fairs_count;
    
                if ($week_data > 0)
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            } 
            elseif ($range == 'last_week')
            {
                $week_data              = $row['week'];
                 $actions_count          = $matched_fairs_count;
    
                if ($week_data > 0)
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
    
                    ];
                }
            } 
            elseif ($range == 'this_year') 
            {
                $year_data              = $row['year'];
                $actions_count          = $matched_fairs_count;
    
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
    
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
    
                    ];
                }
            } 
            elseif ($range == 'last_year') 
            {
                $year_data              = $row['year'];
                 $actions_count          = $matched_fairs_count;
    
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
    
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        'fair_count'    => $matched_fairs_count
                    ];
                }
            } 
            elseif ($range == 'today')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_fairs_count;
    
                if ($day_data > 0)
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
    
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
    
                    ];
                }
            }
            elseif ($range == 'yesterday')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_fairs_count;
    
                if ($day_data > 0)
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
    
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
    
                    ];
                }
            }
            elseif ($range == 'all')
            {
                $date_created           = new DateTime($row['datecreated']);
                $current_date           = new DateTime();
                $interval               = $date_created->diff($current_date);
                $all_data               = ($interval->y * $row['year']);
                $actions_count          = $matched_fairs_count;
    
                if ($all_data > 0)
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
    
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
    
                    ];
                }
            }
            elseif ($range == 'period' && $start_date && $end_date) {
                $start_date     = new DateTime($start_date);
                $end_date       = new DateTime($end_date);
                
                $date_created   = new DateTime($row['datecreated']);
                $interval_start = $date_created->diff($start_date); 
                $interval_end   = $start_date->diff($end_date);
                
                $all_data       = ($interval_start->y * $row['year']) + 
                                  ($interval_start->m * $row['month']) + 
                                  ($interval_start->d * $row['day']) + 
                                  ($interval_start->days * $row['week']);
                $actions_count          = $matched_fairs_count;
    
                if ($all_data > 0)
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
    
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
    
                    ];
                }
            }
    
    
    
        }
        else if ($row['kpi_count'] == 'Number' && $row['kpi_name'] == 'Career Talk')
        {
            if ($range == 'this_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_events_count;
    
                if ($month_data > 0)
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            } 
            elseif ($range == 'last_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_events_count;
    
                if ($month_data > 0)
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            } 
            elseif ($range == 'last_seven_days')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_events_count;
    
                if ($week_data > 0)
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            } 
            elseif ($range == 'this_week')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_events_count;
    
                if ($week_data > 0)
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            } 
            elseif ($range == 'last_week')
            {
                $week_data              = $row['week'];
                 $actions_count          = $matched_events_count;
    
                if ($week_data > 0)
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            } 
            elseif ($range == 'this_year') 
            {
                $year_data              = $row['year'];
                $actions_count          = $matched_events_count;
    
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            } 
            elseif ($range == 'last_year') 
            {
                $year_data              = $row['year'];
                 $actions_count          = $matched_events_count;
    
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                    ];
                }
            } 
            elseif ($range == 'today')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_events_count;
    
                if ($day_data > 0)
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            }
            elseif ($range == 'yesterday')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_events_count;
    
                if ($day_data > 0)
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            }
            elseif ($range == 'all')
            {
                $date_created           = new DateTime($row['datecreated']);
                $current_date           = new DateTime();
                $interval               = $date_created->diff($current_date);
                $all_data               = ($interval->y * $row['year']);
                $actions_count          = $matched_events_count;
    
                if ($all_data > 0)
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            }
            elseif ($range == 'period' && $start_date && $end_date) {
                $start_date     = new DateTime($start_date);
                $end_date       = new DateTime($end_date);
                
                $date_created   = new DateTime($row['datecreated']);
                $interval_start = $date_created->diff($start_date); 
                $interval_end   = $start_date->diff($end_date);
                
                $all_data       = ($interval_start->y * $row['year']) + 
                                  ($interval_start->m * $row['month']) + 
                                  ($interval_start->d * $row['day']) + 
                                  ($interval_start->days * $row['week']);
                $actions_count          = $matched_events_count;
    
                if ($all_data > 0)
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count,
                        
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count,
                        
                    ];
                }
            }
    
    
    
        }
        else if ($row['kpi_count'] == 'Amount')
        {
            $currency_symbol            = '';
            $currency_query             = "SELECT symbol FROM tblcurrencies WHERE id = " . $row['currencyid'];
            $currency_symbol            = $this->db->query($currency_query)->row()->symbol;
    
            if ($range == 'this_month')
            {
                $month_data             = $row['month'];
                $actions_count          = $matched_total_invoice;
                if ($month_data > 0)
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count
                    ];
                } 
                else
                {
                    return [
                        'percentage'    => 0,
                        'actions_count' => $actions_count
                    ];
                }
            } 
            elseif ($range == 'last_month') {
                $month_data             = $row['month'];
                $actions_count          = $matched_total_invoice;
                if ($month_data > 0) 
                {
                    $percentage         = ($actions_count / $month_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count
                    ];
                }
                else
                {
                    return 
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count
                    ];
                }
            } 
            elseif ($range == 'last_seven_days')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_total_invoice;
                if ($week_data > 0)
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count
                    ];
                } 
                else 
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count
                    ];
                }
            } 
            elseif ($range == 'this_week')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_total_invoice;
                if ($week_data > 0)
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return 
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count
                    ];
                } 
                else 
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count
                    ];
                }
            } 
            elseif ($range == 'last_week')
            {
                $week_data              = $row['week'];
                $actions_count          = $matched_total_invoice;
                if ($week_data > 0)
                {
                    $percentage         = ($actions_count / $week_data) * 100;
                    return
                    [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count
                    ];
                }
            }
            elseif ($range == 'this_year')
            {
                $year_data              = $row['year'];
                $actions_count          = $matched_total_invoice;
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count
                    ];
                }
            } 
            elseif ($range == 'last_year')
            {
                $year_data              = $row['year'];
                $actions_count          = $matched_total_invoice;
                if ($year_data > 0)
                {
                    $percentage         = ($actions_count / $year_data) * 100;
                    return [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count
                    ];
                } 
                else
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count
                    ];
                }
            } 
            elseif ($range == 'today')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_total_invoice;
                if ($day_data > 0) 
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count
                    ];
                } else 
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count
                    ];
                }
            }
            elseif ($range == 'yesterday')
            {
                $day_data               = $row['day'];
                $actions_count          = $matched_total_invoice;
                if ($day_data > 0) 
                {
                    $percentage         = ($actions_count / $day_data) * 100;
                    return [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count
                    ];
                } else 
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count
                    ];
                }
            }
            elseif ($range == 'all')
            { 
               $date_created            = new DateTime($row['datecreated']);
               $current_date            = new DateTime();
               $interval                = $date_created->diff($current_date);
               $all_data               = ($interval->y * $row['year']);
                $actions_count          = $matched_total_invoice;
                if ($all_data > 0) 
                {
                    $percentage         = ($actions_count / $all_data) * 100;
                    return [
                        'percentage'    => round($percentage, 2),
                        'actions_count' => $actions_count
                    ];
                } else 
                {
                    return
                    [
                        'percentage'    => 0,
                        'actions_count' => $actions_count
                    ];
                }
            }
            elseif ($range == 'period' && $start_date && $end_date) {
                $start_date     = new DateTime($start_date);
                $end_date       = new DateTime($end_date);
                
                $date_created   = new DateTime($row['datecreated']);
                $interval_start = $date_created->diff($start_date); 
                $interval_end   = $start_date->diff($end_date);
                
                $all_data       = ($interval_start->y * $row['year']) + 
                                    ($interval_start->m * $row['month']) + 
                                    ($interval_start->d * $row['day']) + 
                                    ($interval_start->days * $row['week']);
                $actions_count          = $matched_total_invoice;
            
                if ($all_data > 0) 
                {
                    $percentage = ($actions_count / $all_data) * 100;
                    return [
                        'percentage' => round($percentage, 2),
                        'actions_count' => $actions_count
                    ];
                } 
                else 
                {
                    return [
                        'percentage'    => 0,
                        'actions_count' => $actions_count
                    ];
                }
            }
    
            }
        return 0; 
    }
    public function fetch_chart_data($staff_id, $range, $start_date = null, $end_date = null)
    {
    $this->db->select("date_created, 
                   SUM(call_count) as call_count, 
                   SUM(metting_count) as metting_count, 
                   SUM(email_count) as email_count, 
                   (SUM(call_count) + SUM(metting_count) + SUM(email_count)) as total_activity_count,
                   SUM(contact_count) as contact_count, 
                   SUM(customer_count) as customer_count, 
                   SUM(uniranks_schools_count) as uniranks_schools_count, 
                   SUM(uniranks_agents_count) as uniranks_agents_count, 
                   SUM(uniranks_students_count) as uniranks_students_count, 
                   SUM(uniranks_progress_count) as uniranks_progress_count, 
                   SUM(uniranks_coupon_count) as uniranks_coupon_count, 
                   SUM(uniranks_fair_count) as uniranks_fair_count, 
                   SUM(uniranks_events_count) as uniranks_events_count, 
                   SUM(total_invoice) as total_invoice,
                   SUM(TIME_TO_SEC(desk_time)) AS total_desk_time_sec,
                   SEC_TO_TIME(SUM(TIME_TO_SEC(desk_time))) AS total_desk_time_hhmm,
                   SUM(TIME_TO_SEC(time_sheet)) AS total_time_sheet_sec,
                   SEC_TO_TIME(SUM(TIME_TO_SEC(time_sheet))) AS total_time_sheet_hhmm");

        $this->db->from("tbl_sam_daily_statistics");
    
        // Check if a specific staff_id is provided, otherwise fetch all
        if (!empty($staff_id)) {
            $this->db->where("staffid", $staff_id);
        }
        // Set the date range
        if ($range == 'this_month') {
            $beginDate = date('Y-m-01');
            $endDate = date('Y-m-d');
        } elseif ($range == 'last_month') {
            $beginDate = date('Y-m-01', strtotime('-1 MONTH'));
            $endDate = date('Y-m-t', strtotime('-1 MONTH'));
        } elseif ($range == 'this_week') {
            $beginDate = date('Y-m-d', strtotime('last saturday'));
            $endDate = date('Y-m-d', strtotime('friday this week'));
        } elseif ($range == 'last_seven_days') {
            $beginDate = date('Y-m-d', strtotime('-7 DAYS'));
            $endDate = date('Y-m-d');
        } elseif ($range == 'last_week') {
            $beginDate = date('Y-m-d', strtotime('monday last week'));
            $endDate = date('Y-m-d', strtotime('sunday last week'));
        } elseif ($range == 'this_year') {
            $beginDate = date('Y-01-01');
            $endDate = date('Y-m-d');
        } elseif ($range == 'today') {
            $beginDate = $endDate = date('Y-m-d');
        } elseif ($range == 'yesterday') {
            $beginDate = $endDate = date('Y-m-d', strtotime('-1 day'));
        } elseif ($range == 'last_year') {
            $beginDate = date('Y-01-01', strtotime('-1 YEAR'));
            $endDate = date('Y-12-31', strtotime('-1 YEAR'));
        } elseif ($range == 'all') {
            $beginDate = '2000-01-01'; // Retrieve all records
            $endDate = date('Y-m-d');
        } elseif ($range == 'period' && !empty($start_date) && !empty($end_date)) {
            $beginDate = $start_date;
            $endDate = $end_date;
        } else {
            return false; // Invalid range
        }
    
        // Apply the date filter
        $this->db->where("date_created >=", $beginDate);
        $this->db->where("date_created <=", $endDate);
    
        $this->db->group_by("date_created");

        return $this->db->get()->result_array();
    }



    public function get_groups_on_customer($post_data = array(), $default = false) {
        if ($default) {
            $userid_cond = is_admin() ? 1 : "c.addedfrom=" . get_staff_user_id();
    
            $query = "SELECT 
                        g.id AS group_id,
                        g.name AS group_name,
                        c.addedfrom AS staffid,
                        COUNT(cg.customer_id) AS total_customers
                      FROM tblclients c
                      INNER JOIN tblcustomer_groups cg ON c.userid = cg.customer_id
                      INNER JOIN tblcustomers_groups g ON cg.groupid = g.id
                      WHERE $userid_cond
                      GROUP BY g.id, g.name";
    
            return $this->db->query($query)->result_array() ?: false;
        } else {
            if (is_array($post_data)) {
                $where = [];
    
                // Staff ID filter
                if (!empty($post_data['staff_id'])) {
                    $where[] = 'c.addedfrom=' . $this->db->escape_str($post_data['staff_id']);
                }
    
                $where_clause = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';
    
                $query = "SELECT 
                            g.id AS group_id,
                            g.name AS group_name,
                            c.addedfrom AS staffid,
                            COUNT(cg.customer_id) AS total_customers
                          FROM tblclients c
                          INNER JOIN tblcustomer_groups cg ON c.userid = cg.customer_id
                          INNER JOIN tblcustomers_groups g ON cg.groupid = g.id
                          $where_clause
                          GROUP BY g.id, g.name";
    
                return $this->db->query($query)->result_array() ?: false;
            }
        }
    }
    public function getUniversityOnDeals($post_data = array(), $default = false)
{
    if ($default) {
        $uniranks_db = $this->load->database('uniranks_new', TRUE);
        $result = [];

        // If staff_id is null, fetch data for all staff members except staffid = 38
        if (empty($post_data['staff_id'])) {
            $staff_list = $this->get_all_staff();
            // Filter out staffid = 38
            $staff_ids = array_filter(array_column($staff_list, 'staffid'), function($id) {
                return $id != 38;
            });
        } else {
            $staff_ids = [$post_data['staff_id']];
        }

        foreach ($staff_ids as $staff_id) {
            $this->db->select('employee_country_id');
            $this->db->where('staffid', $staff_id);
            $employee_country = $this->db->get('tbl_sam_employee_country')->row();

            if (!$employee_country) {
                continue;
            }

            $this->db->select('country_id');
            $this->db->where('employee_country_id', $employee_country->employee_country_id);
            $country_ids = $this->db->get('tbl_sam_employee_multiple_country')->result_array();

            if (empty($country_ids)) {
                continue;
            }

            $country_ids = array_column($country_ids, 'country_id');

            $this->db->select('country_name');
            $this->db->where_in('country_id', $country_ids);
            $local_countries = $this->db->get('tbl_sam_country')->result_array();

            if (empty($local_countries)) {
                continue;
            }

            $local_country_names = [];
            foreach ($local_countries as $row) {
                $local_country_names[$row['country_id']] = $row['country_name'];
            }

            $uniranks_db->select('id, country_name');
            $uniranks_db->where_in('country_name', array_values($local_country_names));
            $unirank_countries = $uniranks_db->get('countries')->result_array();

            if (empty($unirank_countries)) {
                continue;
            }

            foreach ($unirank_countries as $u_country) {
                $country_id = $u_country['id'];
                $country_name = $u_country['country_name'];

                $uniranks_db->select('id');
                $uniranks_db->where('country_id', $country_id);
                $universities = $uniranks_db->get('universities')->result_array();

                $total_universities = count($universities);

                if ($total_universities == 0) {
                    $claimed_count = 0;
                } else {
                    $university_ids = array_column($universities, 'id');
                    $uniranks_db->distinct();
                    $uniranks_db->select('campus_id');
                    $uniranks_db->where_in('campus_id', $university_ids);
                    $claimed_rows = $uniranks_db->get('users')->result_array();
                    $claimed_ids = !empty($claimed_rows) ? array_unique(array_column($claimed_rows, 'campus_id')) : [];
                    $claimed_count = count($claimed_ids);
                }

                $not_claimed = $total_universities - $claimed_count;
                $result[] = [
                    'country_name'         => $country_name,
                    'total_universities'   => $total_universities,
                    'claimed_universities' => $claimed_count,
                    'not_claimed'          => $not_claimed,
                ];
            }
        }

        return $result;
    }

    return [];
}
public function getSchoolOnDeals($post_data = array(), $default = false)
{
    if ($default) {
        $uniranks_db = $this->load->database('uniranks_new', TRUE);
        $result = [];

        // If staff_id is null, fetch data for all staff members except staffid = 38
        if (empty($post_data['staff_id'])) {
            $staff_list = $this->get_all_staff();
            // Filter out staffid = 38
            $staff_ids = array_filter(array_column($staff_list, 'staffid'), function($id) {
                return $id != 38;
            });
        } else {
            $staff_ids = [$post_data['staff_id']];
        }

        foreach ($staff_ids as $staff_id) {
            $this->db->select('employee_country_id');
            $this->db->where('staffid', $staff_id);
            $employee_country = $this->db->get('tbl_sam_employee_country')->row();

            if (!$employee_country) {
                continue;
            }

            $this->db->select('country_id');
            $this->db->where('employee_country_id', $employee_country->employee_country_id);
            $country_ids = $this->db->get('tbl_sam_employee_multiple_country')->result_array();

            if (empty($country_ids)) {
                continue;
            }

            $country_ids = array_column($country_ids, 'country_id');

            $this->db->select('country_name');
            $this->db->where_in('country_id', $country_ids);
            $local_countries = $this->db->get('tbl_sam_country')->result_array();

            if (empty($local_countries)) {
                continue;
            }

            $local_country_names = [];
            foreach ($local_countries as $row) {
                $local_country_names[$row['country_id']] = $row['country_name'];
            }

            $uniranks_db->select('id, country_name');
            $uniranks_db->where_in('country_name', array_values($local_country_names));
            $unirank_countries = $uniranks_db->get('countries')->result_array();

            if (empty($unirank_countries)) {
                continue;
            }

            foreach ($unirank_countries as $u_country) {
                $country_id = $u_country['id'];
                $country_name = $u_country['country_name'];

                $uniranks_db->select('id');
                $uniranks_db->where('country_id', $country_id);
                $schools = $uniranks_db->get('schools')->result_array();

                $total_schools = count($schools);

                if ($total_schools == 0) {
                    $claimed_count = 0;
                } else {
                    $school_ids = array_column($schools, 'id');
                    $uniranks_db->distinct();
                    $uniranks_db->select('campus_id');
                    $uniranks_db->where_in('campus_id', $school_ids);
                    $uniranks_db->where('role_id', 10);
                    $claimed_rows = $uniranks_db->get('users')->result_array();
                    $claimed_ids = !empty($claimed_rows) ? array_unique(array_column($claimed_rows, 'campus_id')) : [];
                    $claimed_count = count($claimed_ids);
                }

                $not_claimed = $total_schools - $claimed_count;
                $result[] = [
                    'country_name'     => $country_name,
                    'total_schools'    => $total_schools,
                    'claimed_schools'  => $claimed_count,
                    'not_claimed'      => $not_claimed,
                ];
            }
        }

        return $result;
    }

    return [];
}


public function get_deals_status_on_customer($post_data = array(), $default = false) {
    $CI = &get_instance();
    $status_data = [];

    // Check if 'staff_id' is set in $post_data
    $staff_id = isset($post_data['staff_id']) ? $post_data['staff_id'] : ''; // Default to empty if not set

    if ($default) {
        $userid_cond = 1;
        if (!is_admin()) {
            $userid_cond = "t.staff_id=" . get_staff_user_id();
        }
        
        // Modified query with condition for staff_id
        $query = "
            SELECT
                COALESCE(ss.status_id, t.deal_status) AS status_id,
                COALESCE(ss.status_name, 'Uncategorized') AS status_name,
                ss.color,
                COUNT(t.deal_status) AS deal_count
            FROM
                tbl_sam t
            LEFT JOIN tbl_sam_status ss ON ss.status_id = t.deal_status
            WHERE t.default_deal_owner = ?
            GROUP BY COALESCE(ss.status_id, t.deal_status)
            ORDER BY status_name
        ";

        $result = $this->db->query($query, [$staff_id])->result_array();

        if ($result) {
            foreach ($result as $row) {
                $status_data[] = [
                    'status_id' => $row['status_id'],
                    'status_name' => $row['status_name'],
                    'color' => $row['color'],
                    'deal_count' => $row['deal_count'],
                ];
            }
            return $status_data;
        } else {
            return false;
        }
    } else {
        // Non-default case, always fetch all records for the given staff_id
        if (is_array($post_data)) {
            $query = "
                SELECT
                    COALESCE(ss.status_id, 0) AS status_id,
                    COALESCE(ss.status_name, 'Not Selected') AS status_name,
                    COALESCE(ss.color, '#107e94') AS color,
                    COUNT(t.deal_status) AS deal_count 
                FROM
                    tbl_sam t
                LEFT JOIN tbl_sam_status ss ON ss.status_id = t.deal_status
                WHERE t.default_deal_owner = ?
                GROUP BY COALESCE(ss.status_id, 0) 
                ORDER BY status_name
            ";

            $result = $this->db->query($query, [$staff_id])->result_array();

            if ($result) {
                $status_data = [];
                foreach ($result as $row) {
                    $status_data[] = [
                        'status_id' => $row['status_id'],
                        'status_name' => $row['status_name'],
                        'color' => $row['color'],
                        'deal_count' => $row['deal_count'],
                    ];
                }
                return $status_data;
            } else {
                return false;
            }
        }
    }
}









public function get_customers_by_group_and_staff($group_id, $staffid)
{
    $this->db->select('tblclients.userid, tblclients.company, tblcustomer_groups.customer_id, tblcustomers_groups.name');
    $this->db->from('tblcustomer_groups');
    
    // Join with tblclients to get company and ensure the staffid is matching in tblclients
    $this->db->join('tblclients', 'tblclients.userid = tblcustomer_groups.customer_id AND tblclients.addedfrom = ' . $this->db->escape($staffid), 'inner');
    
    // Join with tblcustomers_groups to fetch the name based on group_id
    $this->db->join('tblcustomers_groups', 'tblcustomers_groups.id = tblcustomer_groups.groupid', 'inner');
    
    // Filter by the group_id
    $this->db->where('tblcustomer_groups.groupid', $group_id);
    
    $query = $this->db->get();

    // Return the results as an array of customers with user info and group name
    if ($query->num_rows() > 0) {
        return $query->result_array();  // This will give you an array of customers with user info and group name
    }
    return false;
}

public function get_new_customers_by_staff($staffid, $range)
{
     if ($range == 'this_month') 
    {
        $beginDate = date('Y-m-01');
        $endDate = date('Y-m-d');
    } 
    elseif ($range == 'last_month')
    {
        $beginDate = date('Y-m-01', strtotime('-1 MONTH'));
        $endDate = date('Y-m-t', strtotime('-1 MONTH'));
    }
    elseif ($range == 'this_week')
    {
        $beginDate = date('Y-m-d', strtotime('monday this week'));
        $endDate = date('Y-m-d', strtotime('sunday this week'));
    } 
     elseif($range=='last_seven_days')
     {
        $beginDate = date('Y-m-d 00:00:00', strtotime('-7 DAYS'));
        $endDate   = date('Y-m-d 23:59:59');
    }
    elseif ($range == 'last_week') 
    {
        $beginDate = date('Y-m-d', strtotime('monday last week'));
        $endDate = date('Y-m-d', strtotime('sunday last week'));
    }
    elseif ($range == 'this_year')
    {
        $beginDate = date('Y-01-01');
        $endDate = date('Y-m-d');
    } 
    elseif ($range == 'today') 
    {
        $beginDate = $endDate = date('Y-m-d');
    } 
    elseif ($range == 'yesterday')
    {
        $beginDate = $endDate = date('Y-m-d', strtotime('-1 day'));
    } 
    elseif ($range == 'last_year')
    {
        $beginDate = date('Y-01-01', strtotime('-1 YEAR'));
        $endDate = date('Y-12-31', strtotime('-1 YEAR'));
    } 
    elseif ($range == 'all')
    {
        $beginDate = '2000-01-01'; // Purane records ke liye ek chhoti date
        $endDate = date('Y-m-d');
    } 
    elseif ($range == 'period' && $start_date && $end_date)
    {
        $beginDate = $start_date;
        $endDate = $end_date;
    } 
    else
    {
        return false; // Agar koi valid range na ho toh function fail ho jaye
    }
    
    $this->db->select('
        tblclients.*, 
        GROUP_CONCAT(tblcustomer_groups.groupid) AS group_ids, 
        GROUP_CONCAT(tblcustomers_groups.name) AS group_names,
        tblcurrencies.symbol AS currency_symbol,
        tblcountries.short_name AS country_short_name
    ');
    $this->db->from('tblclients');
    $this->db->join('tblcustomer_groups', 'tblcustomer_groups.customer_id = tblclients.userid', 'left');
    $this->db->join('tblcustomers_groups', 'tblcustomers_groups.id = tblcustomer_groups.groupid', 'left');
    $this->db->join('tblcurrencies', 'tblcurrencies.id = tblclients.default_currency', 'left');
    $this->db->join('tblcountries', 'tblcountries.country_id = tblclients.country', 'left');
    $this->db->where('tblclients.addedfrom', $staffid);
    $this->db->where('DATE(tblclients.datecreated) >=', $beginDate);
    $this->db->where('DATE(tblclients.datecreated) <=', $endDate);

    $this->db->group_by('tblclients.userid');

    $query = $this->db->get();

    if ($query->num_rows() > 0) {
        return $query->result_array();
    }
    return false;
}
public function get_new_contact_by_staff($staffid, $range)
{
    if ($range == 'this_month') 
    {
        $beginDate = date('Y-m-01');
        $endDate = date('Y-m-d');
    } 
    elseif ($range == 'last_month')
    {
        $beginDate = date('Y-m-01', strtotime('-1 MONTH'));
        $endDate = date('Y-m-t', strtotime('-1 MONTH'));
    }
    elseif ($range == 'this_week')
    {
        $beginDate = date('Y-m-d', strtotime('monday this week'));
        $endDate = date('Y-m-d', strtotime('sunday this week'));
    } 
     elseif($range=='last_seven_days')
     {
        $beginDate = date('Y-m-d 00:00:00', strtotime('-7 DAYS'));
        $endDate   = date('Y-m-d 23:59:59');
    }
    elseif ($range == 'last_week') 
    {
        $beginDate = date('Y-m-d', strtotime('monday last week'));
        $endDate = date('Y-m-d', strtotime('sunday last week'));
    }
    elseif ($range == 'this_year')
    {
        $beginDate = date('Y-01-01');
        $endDate = date('Y-m-d');
    } 
    elseif ($range == 'today') 
    {
        $beginDate = $endDate = date('Y-m-d');
    } 
    elseif ($range == 'yesterday')
    {
        $beginDate = $endDate = date('Y-m-d', strtotime('-1 day'));
    } 
    elseif ($range == 'last_year')
    {
        $beginDate = date('Y-01-01', strtotime('-1 YEAR'));
        $endDate = date('Y-12-31', strtotime('-1 YEAR'));
    } 
    elseif ($range == 'all')
    {
        $beginDate = '2000-01-01'; // Purane records ke liye ek chhoti date
        $endDate = date('Y-m-d');
    } 
    elseif ($range == 'period' && $start_date && $end_date)
    {
        $beginDate = $start_date;
        $endDate = $end_date;
    } 
    else
    {
        return false; // Agar koi valid range na ho toh function fail ho jaye
    }
    
    $this->db->select('tblcontacts.*, tblclients.company, tblclients.datecreated');
    $this->db->from('tblcontacts');
    $this->db->join('tblclients', 'tblclients.userid = tblcontacts.userid', 'inner');
    $this->db->where('tblclients.addedfrom', $staffid);
    $this->db->where('DATE(tblcontacts.datecreated) >=', $beginDate);
    $this->db->where('DATE(tblcontacts.datecreated) <=', $endDate);
    $query = $this->db->get();

    if ($query->num_rows() > 0) {
        return $query->result_array();
    }
    return false;
}

public function get_new_communication_by_staff($staffid, $range, $start_date = null, $end_date = null)
{
    if ($range == 'this_month') 
    {
        $beginDate = date('Y-m-01');
        $endDate = date('Y-m-d');
    } 
    elseif ($range == 'last_month')
    {
        $beginDate = date('Y-m-01', strtotime('-1 MONTH'));
        $endDate = date('Y-m-t', strtotime('-1 MONTH'));
    }
    elseif ($range == 'this_week')
    {
        $beginDate = date('Y-m-d', strtotime('last saturday'));
        $endDate = date('Y-m-d', strtotime('friday this week'));
    } 
     elseif($range=='last_seven_days')
     {
        $beginDate = date('Y-m-d 00:00:00', strtotime('-7 DAYS'));
        $endDate   = date('Y-m-d 23:59:59');
    }
    elseif ($range == 'last_week') 
    {
        $beginDate = date('Y-m-d', strtotime('monday last week'));
        $endDate = date('Y-m-d', strtotime('sunday last week'));
    }
    elseif ($range == 'this_year')
    {
        $beginDate = date('Y-01-01');
        $endDate = date('Y-m-d');
    } 
    elseif ($range == 'today') 
    {
        $beginDate = $endDate = date('Y-m-d');
    } 
    elseif ($range == 'yesterday')
    {
        $beginDate = $endDate = date('Y-m-d', strtotime('-1 day'));
    } 
    elseif ($range == 'last_year')
    {
        $beginDate = date('Y-01-01', strtotime('-1 YEAR'));
        $endDate = date('Y-12-31', strtotime('-1 YEAR'));
    } 
    elseif ($range == 'all')
    {
        $beginDate = '2000-01-01'; // Purane records ke liye ek chhoti date
        $endDate = date('Y-m-d');
    } 
    elseif ($range == 'period' && $start_date && $end_date)
    {
        $beginDate = $start_date;
        $endDate = $end_date;
    } 
    else
    {
        return false; // Agar koi valid range na ho toh function fail ho jaye
    }

    // Query
     $this->db->select('tbl_sam_calls.client_id, tbl_sam_calls.module_field_id, tblclients.company, 
        GROUP_CONCAT(DISTINCT tbl_sam_calls.date ORDER BY tbl_sam_calls.date ASC) as dates, 
        COUNT(tbl_sam_calls.client_id) as call_count');
    $this->db->from('tbl_sam_calls');
    $this->db->join('tblclients', 'tblclients.userid = tbl_sam_calls.client_id', 'left');
    $this->db->where('tbl_sam_calls.user_id', $staffid);
    $this->db->where('tbl_sam_calls.date >=', $beginDate);
    $this->db->where('tbl_sam_calls.date <=', $endDate);
    $this->db->group_by('tbl_sam_calls.client_id'); // Group by client only
    $query = $this->db->get();


    if ($query->num_rows() > 0) {
        return $query->result_array();
    }
    return false;
}







public function get_related_id_from_tbl_sam($rel_id)
{
    $this->db->select('id');
    $this->db->from('tbl_sam');
    $this->db->where('rel_id', $rel_id);
    $query = $this->db->get();

    if ($query->num_rows() > 0) {
        return $query->row()->id;
    }

    return null;  // Return null if no matching record is found
}





    public function deals_leads_status_stats($post_data=array(),$default=false)
    {   
        if($default){
            $userid_cond = 1;
            if(!is_admin()){
                $userid_cond = "t.default_deal_owner=".get_staff_user_id();
            }
            //this month
            $beginThisMonth = date('Y-m-01 00:00:00');
            $endThisMonth   = date('Y-m-d 23:59:59');
            //array_push($where, ' AND t.created_at BETWEEN "' . $beginThisMonth . '" AND "' . $endThisMonth.'"');

            $where1 = 'WHERE '.$userid_cond.' AND t.status="open" AND t.created_at BETWEEN "' . $beginThisMonth . '" AND "' . $endThisMonth.'"';
            $where2 = 'WHERE '.$userid_cond.' AND t.status="won" AND t.created_at BETWEEN "' . $beginThisMonth . '" AND "' . $endThisMonth.'"';
            $where3 = 'WHERE '.$userid_cond.' AND t.status="lost" AND t.created_at BETWEEN "' . $beginThisMonth . '" AND "' . $endThisMonth.'"';
            $where4 = 'WHERE '.$userid_cond.' AND t.created_at BETWEEN "' . $beginThisMonth . '" AND "' . $endThisMonth.'"';
            $query = '
                SELECT COUNT(*) as total, SUM(deal_value) as value, "open" as name, GROUP_CONCAT(t.id) as sam_ids 
                FROM tbl_sam as t                               
                '."$where1".'
                UNION ALL
                SELECT COUNT(*) as total,SUM(deal_value) as value, "won" as name, GROUP_CONCAT(t.id) as sam_ids 
                FROM tbl_sam as t                               
                '."$where2".'
                UNION ALL
                SELECT COUNT(*) as total,SUM(deal_value) as value, "lost" as name, GROUP_CONCAT(t.id) as sam_ids 
                FROM tbl_sam as t                               
                '."$where3".'
                UNION ALL
                SELECT COUNT(*) as total,SUM(deal_value) as value, "total" as name, GROUP_CONCAT(t.id) as sam_ids 
                FROM tbl_sam as t                               
                '."$where4".'
            
            ';
            //echo $query; exit;
            $result = $this->db->query($query)->result_array();
            if($result){
                //echo "<pre>"; print_r($result);exit;
                return $result;
            }
            else{
                return false;
            }    
        }
        else{
            if(is_array($post_data)){
                $where = [];
                if(isset($post_data['staff_id']) && $post_data['staff_id']!=""){
                        $where = [
                            'AND t.default_deal_owner=' . $this->db->escape_str($post_data['staff_id']),
                        ];
                }
                if(isset($post_data['range'])){
                    $range = $post_data['range'];
                    //today
                    if($range=='today'){
                        $beginOfDay = date('Y-m-d 00:00:00',strtotime('TODAY'));       
                        $endOfDay = date('Y-m-d 23:59:59');       
                        array_push($where, ' AND t.created_at BETWEEN "' . $beginOfDay.'" AND "'.$endOfDay.'"');
                    }
                    //seven days
                    elseif($range=='last_seven_days'){
                        $beginThisWeek = date('Y-m-d 00:00:00', strtotime('-7 DAYS'));
                        $endThisWeek   = date('Y-m-d 23:59:59');
                        array_push($where, ' AND t.created_at BETWEEN "' . $beginThisWeek . '" AND "' . $endThisWeek.'"');
                    }
                    //this week
                    elseif($range=='this_week'){
                        $beginThisWeek = date('Y-m-d 00:00:00', strtotime('monday this week'));
                        $endThisWeek   = date('Y-m-d 23:59:59', strtotime('sunday this week'));
                        array_push($where, ' AND t.created_at BETWEEN "' . $beginThisWeek . '" AND "' . $endThisWeek.'"');
                    }
                    //last week
                    elseif($range=='last_week'){
                        $beginLastWeek = date('Y-m-d 00:00:00', strtotime('monday last week'));
                        $endLastWeek   = date('Y-m-d 23:59:59', strtotime('sunday last week'));
                        array_push($where, ' AND t.created_at BETWEEN "' . $beginLastWeek . '" AND "' . $endLastWeek.'"');
                    }
                    //this month
                    elseif($range=='this_month'){
                        $beginThisMonth = date('Y-m-01 00:00:00');
                        $endThisMonth   = date('Y-m-d 23:59:59');
                        array_push($where, ' AND t.created_at BETWEEN "' . $beginThisMonth . '" AND "' . $endThisMonth.'"');
                    }
                    //last month
                    elseif($range=='last_month'){
                        $beginLastMonth = date('Y-m-01 00:00:00', strtotime('-1 MONTH'));
                        $endLastMonth   = date('Y-m-t', strtotime('-1 MONTH'));
                        array_push($where, ' AND t.created_at BETWEEN "' . $beginLastMonth . '" AND "' . $endLastMonth.'"');
                    }
                    //this year
                    elseif($range=='this_year'){
                        $beginOfDay = date('Y-01-01 00:00:00');
                        $endOfDay   = date('Y-12-31 23:59:59');
                        array_push($where, ' AND t.created_at BETWEEN "' . $beginOfDay . '" AND "' . $endOfDay.'"');
                    }
                    //last year
                    elseif($range=='last_year'){
                        $beginOfDay = date('Y-01-01 00:00:00', strtotime('-1 YEAR'));
                        $endOfDay   = date('Y-12-t 23:59:59', strtotime('-1 YEAR'));
                        array_push($where, ' AND t.created_at BETWEEN "' . $beginOfDay . '" AND "' . $endOfDay.'"');
                    }
                    //search by periods
                    elseif($range=='period' && $post_data['periodfrom']!="" && $post_data['periodto']){
                        $start_date = to_sql_date($post_data['periodfrom']);
                        $end_date   = to_sql_date($post_data['periodto']);
                        array_push($where, ' AND t.created_at BETWEEN "' . $start_date . ' 00:00:00" AND "' . $end_date.' 23:59:59"');
                    }
                    //all
                    elseif($range=='all'){
                        
                    }
                }
                
                //echo "<pre>"; print_r($where); exit;
                $where1='';$where2='';$where3='';$where4='';
                $where = implode(' ', $where);
                $where = trim($where);
                $where1 = 'WHERE status="open" ' . $where;
                $where2 = 'WHERE status="won" ' . $where;
                $where3 = 'WHERE status="lost" ' . $where;
                $where4 = 'WHERE 1 ' . $where;
                $query = '
                    SELECT COUNT(*) as total, SUM(deal_value) as value, "open" as name, GROUP_CONCAT(t.id) as sam_ids 
                    FROM tbl_sam as t                               
                    '."$where1".'
                    UNION ALL
                    SELECT COUNT(*) as total,SUM(deal_value) as value, "won" as name, GROUP_CONCAT(t.id) as sam_ids 
                    FROM tbl_sam as t                               
                    '."$where2".'
                    UNION ALL
                    SELECT COUNT(*) as total,SUM(deal_value) as value, "lost" as name, GROUP_CONCAT(t.id) as sam_ids 
                    FROM tbl_sam as t                               
                    '."$where3".'
                    UNION ALL
                    SELECT COUNT(*) as total,SUM(deal_value) as value, "total" as name, GROUP_CONCAT(t.id) as sam_ids 
                    FROM tbl_sam as t                               
                    '."$where4".'
                
                ';
                //echo $query; exit;
                $result = $this->db->query($query)->result_array();
                if($result){
                    //echo "<pre>"; print_r($result);exit;
                    return $result;
                }
                else{
                    return false;
                }
                
            }    
        }
    }


    
    //get proposals
    public function getDealProposals($post_data=array(),$default=false){
        if($default){
            $userid_cond = 1;
            if(!is_admin()){
                $userid_cond = "t.addedfrom=".get_staff_user_id();
            }
            $beginThisMonth = date('Y-m-01 00:00:00');
            $endThisMonth   = date('Y-m-d 23:59:59');    
            $date = 'AND t.datecreated BETWEEN "' . $beginThisMonth . '" AND "' . $endThisMonth.'"';
            //sent proposal condition
            $where1 = 'WHERE '.$userid_cond.' AND t.status=4 ' . $date;
            //accepted proposal condition
            $where2 = 'WHERE '.$userid_cond.' AND t.status=3 ' . $date;         
            $query = '
                SELECT SUM(t.total) as total, "total-sent-amount" as status 
                FROM tbl_sam_proposals as t
                JOIN tbl_sam as t2 on t.rec_id = t2.id                              
                '."$where1".'
                UNION ALL 
                SELECT SUM(t.total) as total, "total-accepted-amount" as status 
                FROM tbl_sam_proposals as t 
                JOIN tbl_sam as t2 on t.rec_id = t2.id                               
                '."$where2".'
                UNION ALL 
                SELECT COUNT(*) as total, "total-sent-proposal" as status 
                FROM tbl_sam_proposals as t
                JOIN tbl_sam as t2 on t.rec_id = t2.id                                
                '."$where1".'
                UNION ALL 
                SELECT COUNT(*) as total, "total-accepted-proposal" as status 
                FROM tbl_sam_proposals as t
                JOIN tbl_sam as t2 on t.rec_id = t2.id                                
                '."$where2".'
                    
            ';
            //echo $query; exit;
            $result = $this->db->query($query)->result_array();
            if($result){
                //echo "<pre>"; print_r($result);exit;
                return $result;
            }
            else{
                return false;
            }
        }
        else{
            if(is_array($post_data)){
                $where = [];
                if(isset($post_data['staff_id']) && $post_data['staff_id']!=""){
                        $where = [
                            'AND t.addedfrom=' . $this->db->escape_str($post_data['staff_id']),
                        ];
                }
                if(isset($post_data['range'])){
                    $range = $post_data['range'];
                    //today
                    if($range=='today'){
                        $beginOfDay = date('Y-m-d 00:00:00',strtotime('TODAY'));       
                        $endOfDay = date('Y-m-d 23:59:59');       
                        array_push($where, ' AND t.datecreated BETWEEN "' . $beginOfDay.'" AND "'.$endOfDay.'"');
                    }
                    //seven days
                    elseif($range=='last_seven_days'){
                        $beginThisWeek = date('Y-m-d 00:00:00', strtotime('-7 DAYS'));
                        $endThisWeek   = date('Y-m-d 23:59:59');
                        array_push($where, ' AND t.datecreated BETWEEN "' . $beginThisWeek . '" AND "' . $endThisWeek.'"');
                    }
                    //this week
                    elseif($range=='this_week'){
                        $beginThisWeek = date('Y-m-d 00:00:00', strtotime('monday this week'));
                        $endThisWeek   = date('Y-m-d 23:59:59', strtotime('sunday this week'));
                        array_push($where, ' AND t.datecreated BETWEEN "' . $beginThisWeek . '" AND "' . $endThisWeek.'"');
                    }
                    //last week
                    elseif($range=='last_week'){
                        $beginLastWeek = date('Y-m-d 00:00:00', strtotime('monday last week'));
                        $endLastWeek   = date('Y-m-d 23:59:59', strtotime('sunday last week'));
                        array_push($where, ' AND t.datecreated BETWEEN "' . $beginLastWeek . '" AND "' . $endLastWeek.'"');
                    }
                    //this month
                    elseif($range=='this_month'){
                        $beginThisMonth = date('Y-m-01 00:00:00');
                        $endThisMonth   = date('Y-m-d 23:59:59');
                        array_push($where, ' AND t.datecreated BETWEEN "' . $beginThisMonth . '" AND "' . $endThisMonth.'"');
                    }
                    //last month
                    elseif($range=='last_month'){
                        $beginLastMonth = date('Y-m-01 00:00:00', strtotime('-1 MONTH'));
                        $endLastMonth   = date('Y-m-t 23:59:59', strtotime('-1 MONTH'));
                        array_push($where, ' AND t.datecreated BETWEEN "' . $beginLastMonth . '" AND "' . $endLastMonth.'"');
                    }
                    //this year
                    elseif($range=='this_year'){
                        $beginOfDay = date('Y-01-01 00:00:00');
                        $endOfDay   = date('Y-12-31 23:59:59');
                        array_push($where, ' AND t.datecreated BETWEEN "' . $beginOfDay . '" AND "' . $endOfDay.'"');
                    }
                    //last year
                    elseif($range=='last_year'){
                        $beginOfDay = date('Y-01-01 00:00:00', strtotime('-1 YEAR'));
                        $endOfDay   = date('Y-12-t 23:59:59', strtotime('-1 YEAR'));
                        array_push($where, ' AND t.datecreated BETWEEN "' . $beginOfDay . '" AND "' . $endOfDay.'"');
                    }
                    //search by periods
                    elseif($range=='period' && $post_data['periodfrom']!="" && $post_data['periodto']){
                        $start_date = to_sql_date($post_data['periodfrom']);
                        $end_date   = to_sql_date($post_data['periodto']);
                        array_push($where, ' AND t.datecreated BETWEEN "' . $start_date . ' 00:00:00" AND "' . $end_date.' 23:59:59"');
                    }
                    //all
                    elseif($range=='all'){
                        
                    }
                }
                
                //echo "<pre>"; print_r($where); exit;
                $where1='';$where2='';
                $where = implode(' ', $where);
                $where = trim($where);
                //sent proposal condition
                $where1 = 'WHERE t.status=4 ' . $where;
                //accepted proposal condition
                $where2 = 'WHERE t.status=3 ' . $where;         
                $query = '
                    SELECT SUM(t.total) as total, "total-sent-amount" as status 
                    FROM tbl_sam_proposals as t 
                    JOIN tbl_sam as t2 on t.rec_id = t2.id                               
                    '."$where1".'
                    UNION ALL 
                    SELECT SUM(t.total) as total, "total-accepted-amount" as status 
                    FROM tbl_sam_proposals as t 
                    JOIN tbl_sam as t2 on t.rec_id = t2.id                               
                    '."$where2".'
                    UNION ALL 
                    SELECT COUNT(*) as total, "total-sent-proposal" as status 
                    FROM tbl_sam_proposals as t
                    JOIN tbl_sam as t2 on t.rec_id = t2.id                                
                    '."$where1".'
                    UNION ALL 
                    SELECT COUNT(*) as total, "total-accepted-proposal" as status 
                    FROM tbl_sam_proposals as t
                    JOIN tbl_sam as t2 on t.rec_id = t2.id                                
                    '."$where2".'
                        
                ';
                //echo $query; exit;
                $result = $this->db->query($query)->result_array();
                if($result){
                    //echo "<pre>"; print_r($result);exit;
                    return $result;
                }
                else{
                    return false;
                }
            
            }      
        }
    } 
    
    public function getTotalDealValue($post_data=array(),$default=false){
        if($default){
            $userid_cond = 1;
            if(!is_admin()){
                $userid_cond = "t.default_deal_owner=".get_staff_user_id();
            }
            $where = [];
            //this month
            $beginThisMonth = date('Y-m-01 00:00:00');
            $endThisMonth   = date('Y-m-d 23:59:59');
            $where = 'where '.$userid_cond.' AND t.created_at BETWEEN "' . $beginThisMonth . '" AND "' . $endThisMonth.'"';
            $query = '
                SELECT SUM(t.deal_value) as total 
                FROM tbl_sam as t                               
                '."$where".'        
            ';
            //echo $query; exit;
            $result = $this->db->query($query)->result_array();
            if($result){
                //echo "<pre>"; print_r($result);exit;
                return $result;
            }
            else{
                return false;
            }
       
        }
        else{
            if(is_array($post_data)){
                $where = [];
                if(isset($post_data['staff_id']) && $post_data['staff_id']!=""){
                        $where = [
                            'AND t.default_deal_owner=' . $this->db->escape_str($post_data['staff_id']),
                        ];
                }
                if(isset($post_data['pipeline_id']) && $post_data['pipeline_id']!=""){
                        array_push($where, ' AND t.pipeline_id=' . $post_data['pipeline_id']);
                }
                if(isset($post_data['range'])){
                    $range = $post_data['range'];
                    //today
                    if($range=='today'){
                        $beginOfDay = date('Y-m-d 00:00:00',strtotime('TODAY'));       
                        $endOfDay = date('Y-m-d 23:59:59');       
                        array_push($where, ' AND t.created_at BETWEEN "' . $beginOfDay.'" AND "'.$endOfDay.'"');
                    }
                    //seven days
                    elseif($range=='last_seven_days'){
                        $beginThisWeek = date('Y-m-d 00:00:00', strtotime('-7 DAYS'));
                        $endThisWeek   = date('Y-m-d 23:59:59');
                        array_push($where, ' AND t.created_at BETWEEN "' . $beginThisWeek . '" AND "' . $endThisWeek.'"');
                    }
                    //this week
                    elseif($range=='this_week'){
                        $beginThisWeek = date('Y-m-d 00:00:00', strtotime('monday this week'));
                        $endThisWeek   = date('Y-m-d 23:59:59', strtotime('sunday this week'));
                        array_push($where, ' AND t.created_at BETWEEN "' . $beginThisWeek . '" AND "' . $endThisWeek.'"');
                    }
                    //last week
                    elseif($range=='last_week'){
                        $beginLastWeek = date('Y-m-d 00:00:00', strtotime('monday last week'));
                        $endLastWeek   = date('Y-m-d 23:59:59', strtotime('sunday last week'));
                        array_push($where, ' AND t.created_at BETWEEN "' . $beginLastWeek . '" AND "' . $endLastWeek.'"');
                    }
                    //this month
                    elseif($range=='this_month'){
                        $beginThisMonth = date('Y-m-01 00:00:00');
                        $endThisMonth   = date('Y-m-d 23:59:59');
                        array_push($where, ' AND t.created_at BETWEEN "' . $beginThisMonth . '" AND "' . $endThisMonth.'"');
                    }
                    //last month
                    elseif($range=='last_month'){
                        $beginLastMonth = date('Y-m-01', strtotime('-1 MONTH'));
                        $endLastMonth   = date('Y-m-t', strtotime('-1 MONTH'));
                        array_push($where, ' AND t.created_at BETWEEN "' . $beginLastMonth . '" AND "' . $endLastMonth.'"');
                    }
                    //this year
                    elseif($range=='this_year'){
                        $beginOfDay = date('Y-01-01 00:00:00');
                        $endOfDay   = date('Y-12-31 23:59:59');
                        array_push($where, ' AND t.created_at BETWEEN "' . $beginOfDay . '" AND "' . $endOfDay.'"');
                    }
                    //last year
                    elseif($range=='last_year'){
                        $beginOfDay = date('Y-01-01 00:00:00', strtotime('-1 YEAR'));
                        $endOfDay   = date('Y-12-t 23:59:59', strtotime('-1 YEAR'));
                        array_push($where, ' AND t.created_at BETWEEN "' . $beginOfDay . '" AND "' . $endOfDay.'"');
                    }
                    //search by periods
                    elseif($range=='period'){
                        $start_date = to_sql_date($post_data['periodfrom']);
                        $end_date   = to_sql_date($post_data['periodto']);
                        array_push($where, ' AND t.created_at BETWEEN "' . $start_date . ' 00:00:00" AND "' . $end_date.' 23:59:59"');
                    }
                    //all
                    elseif($range=='all'){
                        
                    }
                }
                
                //echo "<pre>"; print_r($where); exit;
                $where = implode(' ', $where);
                $where = trim($where);
                if (startsWith($where, 'AND') || startsWith($where, 'OR')) {
                    if (startsWith($where, 'OR')) {
                        $where = substr($where, 2);
                    } else {
                        $where = substr($where, 3);
                    }
                    $where = 'WHERE ' . $where;
                }
                $query = '
                    SELECT SUM(t.deal_value) as total 
                    FROM tbl_sam as t                               
                    '."$where".'        
                ';
                //echo $query; exit;
                $result = $this->db->query($query)->result_array();
                if($result){
                    //echo "<pre>"; print_r($result);exit;
                    return $result;
                }
                else{
                    return false;
                }
                
            }    
        }
        
    }   
    
    public function getStatusSummary()
    {
        $this->db->select('s.status_name, s.color, s.status_id,COUNT(d.deal_status) as total_count');
        $this->db->from('tbl_sam_status s');
        $this->db->join('tbl_sam d', 's.status_id = d.deal_status', 'left');
        $this->db->group_by('s.status_name');
        return $this->db->get()->result_array();
    }
    

    
    public function getSpentTimeOnCustomer($post_data=array(),$default=false){
        if($default){
            $userid_cond = 1;
            if(!is_admin()){
                $userid_cond = "t.staff_id=".get_staff_user_id();
            }
            $beginThisMonth = date('Y-m-01 00:00:00');
            $endThisMonth   = date('Y-m-t 23:59:59');    
            $where = 'WHERE '.$userid_cond.' AND t2.rel_type="customer" AND t.deleted=0 AND t.start_time BETWEEN ' . strtotime($beginThisMonth) . ' AND ' . strtotime($endThisMonth);
            $query = '
                SELECT t.staff_id, t.pipeline_id, t.sam_id, sum(time_spent) as total_time_spent, sum(t2.deal_value) as total_deal_value, t2.rel_id as customer_id 
                FROM tbl_sam_taskstimers as t
                JOIN tbl_sam as t2 on t.sam_id = t2.id
                '."$where".'       
                group by t2.rel_id
            
            ';
            //echo $query; exit;
            $result = $this->db->query($query)->result_array();
            if($result){
                //echo "<pre>"; print_r($result);exit;
                return $result;
            }
            else{
                return false;
            }
        }
        else{
            if(is_array($post_data)){
                $where = [];
                if(isset($post_data['staff_id']) && $post_data['staff_id']!=""){
                        $where = [
                            'AND t.staff_id=' . $this->db->escape_str($post_data['staff_id']),
                        ];
                }
                if(isset($post_data['pipeline_id']) && $post_data['pipeline_id']!=""){
/*                        $where = [
                            'AND t.pipeline_id=' . $this->db->escape_str($post_data['pipeline_id']),
                        ];*/
                }
                if(isset($post_data['range'])){
                    $range = $post_data['range'];
                    //today
                    if($range=='today'){
                        $beginOfDay = strtotime('midnight');
                        $endOfDay   = strtotime('tomorrow', $beginOfDay) - 1;
                        array_push($where, ' AND t.start_time BETWEEN ' . $beginOfDay . ' AND ' . $endOfDay);
                    }
                    //seven days
                    elseif($range=='last_seven_days'){
                        $beginThisWeek = date('Y-m-d', strtotime('-7 DAYS'));
                        $endThisWeek   = date('Y-m-d 23:59:59');
                        array_push($where, ' AND t.start_time BETWEEN ' . strtotime($beginThisWeek) . ' AND ' . strtotime($endThisWeek));
                    }
                    //this week
                    elseif($range=='this_week'){
                        $beginThisWeek = date('Y-m-d', strtotime('monday this week'));
                        $endThisWeek   = date('Y-m-d 23:59:59', strtotime('sunday this week'));
                        array_push($where, ' AND t.start_time BETWEEN ' . strtotime($beginThisWeek) . ' AND ' . strtotime($endThisWeek));
                    }
                    //last week
                    elseif($range=='last_week'){
                        $beginLastWeek = date('Y-m-d', strtotime('monday last week'));
                        $endLastWeek   = date('Y-m-d 23:59:59', strtotime('sunday last week'));
                        array_push($where, ' AND t.start_time BETWEEN ' . strtotime($beginLastWeek) . ' AND ' . strtotime($endLastWeek));
                    }
                    //this month
                    elseif($range=='this_month'){
                        $beginThisMonth = date('Y-m-01');
                        $endThisMonth   = date('Y-m-t 23:59:59');
                        array_push($where, ' AND t.start_time BETWEEN ' . strtotime($beginThisMonth) . ' AND ' . strtotime($endThisMonth));
                    }
                    //last month
                    elseif($range=='last_month'){
                        $beginLastMonth = date('Y-m-01', strtotime('-1 MONTH'));
                        $endLastMonth   = date('Y-m-t 23:59:59', strtotime('-1 MONTH'));
                        array_push($where, ' AND t.start_time BETWEEN ' . strtotime($beginLastMonth) . ' AND ' . strtotime($endLastMonth));
                    }
                    //this year
                    elseif($range=='this_year'){
                        $beginOfDay = date('Y-01-01');
                        $endOfDay   = date('Y-12-t 23:59:59');
                        array_push($where, ' AND t.start_time BETWEEN ' . strtotime($beginOfDay) . ' AND ' . strtotime($endOfDay));
                    }
                    //last year
                    elseif($range=='last_year'){
                        $beginOfDay = date('Y-01-01', strtotime('-1 YEAR'));
                        $endOfDay   = date('Y-12-t 23:59:59', strtotime('-1 YEAR'));
                        array_push($where, ' AND t.start_time BETWEEN ' . strtotime($beginOfDay) . ' AND ' . strtotime($endOfDay));
                    }
                    //search by periods
                    elseif($range=='period'){
                        $start_date = to_sql_date($post_data['periodfrom']);
                        $end_date   = to_sql_date($post_data['periodto']);
                        array_push($where, ' AND t.start_time BETWEEN ' . strtotime($start_date . ' 00:00:00') . ' AND ' . strtotime($end_date . ' 23:59:59'));
                    }
                    //all
                    elseif($range=='all'){
                        
                    }
                }
                
                //echo "<pre>"; print_r($where); exit;
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
                    $where = 'WHERE t.deleted=0 AND t2.rel_type="customer" AND ' . $where;    
                }
                else{
                    $where = 'WHERE t.deleted=0 AND t2.rel_type="customer"';
                }
                $query = '
                    SELECT t.staff_id, t.pipeline_id, t.sam_id, sum(time_spent) as total_time_spent, sum(t2.deal_value) as total_deal_value, t2.rel_id as customer_id 
                    FROM tbl_sam_taskstimers as t
                    JOIN tbl_sam as t2 on t.sam_id = t2.id
                    '."$where".'
                    group by t2.rel_id
                
                ';
                //echo $query; exit;
                $result = $this->db->query($query)->result_array();
                if($result){
                    //echo "<pre>"; print_r($result);exit;
                    return $result;
                }
                else{
                    return false;
                }
                
            }        
        }
    }
    
    public function getAllRemindersOnDashboard($post_data=array(),$default=false){
        if($default){
            $userid_cond = 1;
/*            if(!is_admin()){
                $userid_cond = "t.staff=".get_staff_user_id();
            }*/
            $beginThisMonth = date('Y-m-01 00:00:00');
            $endThisMonth   = date('Y-m-t 23:59:59');    
            $where = 'WHERE '.$userid_cond.' AND t.rel_type="sam" AND t.date BETWEEN "' . $beginThisMonth . '" AND "' . $endThisMonth.'" AND t.isnotified = 0';
            $query = '
                SELECT t.title, t.description, t.staff, t.creator, t.rel_id as sam_id, t.date, t2.rel_id as customer_id  
                FROM tbl_sam_reminders as t
                JOIN tbl_sam as t2 on t.rel_id = t2.id
                '."$where".'  
                group by t2.rel_id
            
            ';
            
            
            //echo $query; exit;
            $result = $this->db->query($query)->result_array();
            if($result){
                //echo "<pre>"; print_r($result);exit;
                return $result;
            }
            else{
                return false;
            }
        }
        else{
            if(is_array($post_data)){
                $where = [];
                if(isset($post_data['staff_id']) && $post_data['staff_id']!=""){
                        $where = [
                            'AND t.staff=' . $this->db->escape_str($post_data['staff_id']),
                        ];
                }
                if(isset($post_data['range'])){
                    $range = $post_data['range'];
                    //today
                    if($range=='today'){
                        $beginOfDay = date('Y-m-d 00:00:00',strtotime('TODAY'));       
                        $endOfDay = date('Y-m-d 23:59:59'); 
                        array_push($where, ' AND t.date BETWEEN "' . $beginOfDay . '" AND "' . $endOfDay.'"');
                    }
                    //seven days
                    elseif($range=='last_seven_days'){
                        $beginThisWeek = date('Y-m-d 00:00:00', strtotime('-7 DAYS'));
                        $endThisWeek   = date('Y-m-d 23:59:59');
                        array_push($where, ' AND t.date BETWEEN "' . $beginThisWeek . '" AND "' . $endThisWeek.'"');
                    }
                    //this week
                    elseif($range=='this_week'){
                        $beginThisWeek = date('Y-m-d 00:00:00', strtotime('monday this week'));
                        $endThisWeek   = date('Y-m-d 23:59:59', strtotime('sunday this week'));
                        array_push($where, ' AND t.date BETWEEN "' . $beginThisWeek . '" AND "' . $endThisWeek.'"');
                    }
                    //last week
                    elseif($range=='last_week'){
                        $beginLastWeek = date('Y-m-d 00:00:00', strtotime('monday last week'));
                        $endLastWeek   = date('Y-m-d 23:59:59', strtotime('sunday last week'));
                        array_push($where, ' AND t.date BETWEEN "' . $beginLastWeek . '" AND "' . $endLastWeek.'"');
                    }
                    //this month
                    elseif($range=='this_month'){
                        $beginThisMonth = date('Y-m-01 00:00:00');
                        $endThisMonth   = date('Y-m-t 23:59:59');
                        array_push($where, ' AND t.date BETWEEN "' . $beginThisMonth . '" AND "' . $endThisMonth.'"');
                    }
                    //last month
                    elseif($range=='last_month'){
                        $beginLastMonth = date('Y-m-01 00:00:00', strtotime('-1 MONTH'));
                        $endLastMonth   = date('Y-m-t 23:59:59', strtotime('-1 MONTH'));
                        array_push($where, ' AND t.date BETWEEN "' . $beginLastMonth . '" AND "' . $endLastMonth.'"');
                    }
                    //this year
                    elseif($range=='this_year'){
                        $beginOfDay = date('Y-01-01 00:00:00');
                        $endOfDay   = date('Y-12-t 23:59:59');
                        array_push($where, ' AND t.date BETWEEN "' . $beginOfDay . '" AND "' . $endOfDay.'"');
                    }
                    //last year
                    elseif($range=='last_year'){
                        $beginOfDay = date('Y-01-01 00:00:00', strtotime('-1 YEAR'));
                        $endOfDay   = date('Y-12-t 23:59:59', strtotime('-1 YEAR'));
                        array_push($where, ' AND t.date BETWEEN "' . $beginOfDay . '" AND "' . $endOfDay.'"');
                    }
                    //search by periods
                    elseif($range=='period'){
                        $start_date = to_sql_date($post_data['periodfrom']);
                        $end_date   = to_sql_date($post_data['periodto']);
                        array_push($where, ' AND t.date BETWEEN "' . $start_date . ' 00:00:00' . '" AND "' . $end_date . ' 23:59:59'.'"');
                    }
                    //all
                    elseif($range=='all'){
                        
                    }
                }
                
                //echo "<pre>"; print_r($where); exit;
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
                $where = 'WHERE t.rel_type="sam" AND t.isnotified = 0 AND ' . $where;    
            }
            else{
                $where = 'WHERE t.rel_type="sam" AND t.isnotified = 0';
            }
                $query = '
                    SELECT t.title, t.description, t.staff, t.creator, t.rel_id as sam_id, t.date, t2.rel_id as customer_id 
                    FROM tbl_sam_reminders as t
                    JOIN tbl_sam as t2 on t.rel_id = t2.id
                    '."$where".'
                group by t2.rel_id
                
                ';
                //echo $query; exit;
                $result = $this->db->query($query)->result_array();
                if($result){
                    //echo "<pre>"; print_r($result);exit;
                    return $result;
                }
                else{
                    return false;
                }
                
            }        
        }
    }
}