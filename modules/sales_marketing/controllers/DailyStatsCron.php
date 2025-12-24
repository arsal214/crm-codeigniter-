<?php

class DailyStatsCron extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dashboard_model');
    }

    public function saveDailyStats()
    {
        $today                              = date('Y-m-d');
        $all_staff                          = $this->dashboard_model->get_all_staff();
        $staffids                           = array_column($all_staff, 'staffid');
    
        if (empty($staffids)) 
        {
            echo json_encode(['status' => 'error', 'message' => 'No staff found']);
            return;
        }
        
        $api_key                            = $this->config->item('desktime_api_key');
        $url                                = 'https://desktime.com/api/v2/json/employees?apiKey=' . $api_key . '&date=' . $today; // Date pass karein
        $ch                                 = curl_init();
        
        curl_setopt_array($ch,
        [
            CURLOPT_URL                     => $url,
            CURLOPT_RETURNTRANSFER          => true,
            CURLOPT_HTTPHEADER              =>
            [
                'Accept: application/json'
            ],
            CURLOPT_SSL_VERIFYPEER          => false
        ]);
    
        $response                           = curl_exec($ch);

        $http_code                          = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        if (curl_errno($ch))
        {
            log_message('error', 'cURL error: ' . curl_error($ch));
            curl_close($ch);
            return null; 
        }
    
        curl_close($ch);
        
        $employee_data = json_decode($response, true)['employees'] ?? [];

        $staff_data = [];
        
        foreach ($employee_data as $date => $employees)
        {
            foreach ($employees as $employee)
            {
                $employee_id                    = $employee['id'];
                $arrived                        = $employee['arrived'];  
                $left                           = $employee['left']; 
                $productiveTime                 = $employee['productiveTime'] ?? 0;
        
                $this->db->where('employee_desktime_id', $employee_id);
                $query                          = $this->db->get('tbl_sam_desktime');
        
                if ($query->num_rows() > 0)
                {
                    $staff_record               = $query->row_array();
                    $staffid_from_db            = $staff_record['staffid']; // Get the staffid
                    $hours                      = floor($productiveTime / 3600);
                    $minutes                    = floor(($productiveTime % 3600) / 60);
                    $formatted_productive_time  = sprintf('%02d:%02d', $hours, $minutes);
        
                    $staff_data[]               =
                    [
                        'staffid'               => $staffid_from_db,
                        'total'                 => $formatted_productive_time,
                        'arrived'               => $arrived,
                        'left'                  => $left
                    ];
                }
            }
        }

        foreach ($staff_data as $staff) 
        {
            $staffid                            = $staff['staffid'];
            $desk_time                          = $staff['total'];
            $this->db->where('staffid', $staffid);
            $this->db->where('date_created', $today); // Assuming 'date' field exists
            $query                              = $this->db->get('tbl_sam_daily_statistics');
        
            if ($query->num_rows() > 0) 
            {
                $this->db->where('staffid', $staffid);
                $this->db->where('date_created', $today);
                $this->db->update('tbl_sam_daily_statistics', ['desk_time' => $desk_time]);
            } 
            else 
            {
                $this->db->insert('tbl_sam_daily_statistics',
                [
                    'staffid'                   => $staffid,
                    'date_created'              => $today,
                    'desk_time'                 => $desk_time
                ]);
            }
        }


    
        $uniranks_db                            = $this->load->database('uniranks_new', TRUE);
        $schools                                = $uniranks_db->get('schools')->result(); 
        $agents                                 = $uniranks_db->get('agents')->result();  
        $student_certificates                   = $uniranks_db->get('student_certificates')->result();  
        $query                                  = "SELECT * FROM users WHERE role_id = 10 AND DATE(STR_TO_DATE(created_at, '%Y-%m-%d')) = ?";
        $users                                  = $uniranks_db->query($query, [$today])->result();
        $user_bios                              = $uniranks_db->get('user_bios')->result();
        $countries                              = $uniranks_db->get('countries')->result();
        $students_test_coupon_code_log          = $uniranks_db->get('students_test_coupon_code_log')->result();
        $fairs                                  = $uniranks_db->get('fairs')->result();  
        $user_bios_lookup                       = [];
        foreach ($user_bios as $bio)
        {
            if (isset($bio->user_id) && isset($bio->country_id))
            {
                $user_bios_lookup[$bio->user_id] = $bio;
            }
        }

        $role_13_users_lookup                   = [];
        foreach ($users as $user)
        {
            if (isset($user->role_id, $user->id, $user->created_at) && $user->role_id == 13)
            {
                $role_13_users_lookup[$user->id] = $user->created_at;
            }
        }

        foreach ($staffids as $staffid) 
        {
            $query                              = "SELECT id FROM tbl_sam_daily_statistics WHERE staffid = ? AND date_created = ?";
            $existing_record                    = $this->db->query($query, [$staffid, $today])->row();
            $query                              = "SELECT COUNT(*) AS actions_count FROM tbl_sam_calls WHERE user_id = ? AND date = ?";
            $result                             = $this->db->query($query, [$staffid, $today])->row();
            $actions_count                      = $result->actions_count ?? 0;
            
            $query                              = "SELECT COUNT(*) AS meeting_count FROM tbl_sam_mettings WHERE user_id = ? AND DATE(start_date) = ?";
            $result                             = $this->db->query($query, [$staffid, $today])->row();
            $metting_count                      = $result->meeting_count ?? 0;
            
            $query                              = "SELECT COUNT(*) AS email_count FROM tbl_sam_email WHERE user_id = ? AND DATE(message_time) = ?";
            $result                             = $this->db->query($query, [$staffid, $today])->row();
            $email_count                        = $result->email_count ?? 0;
            
            $query                              = "SELECT COUNT(*) AS customer_count, MAX(userid) AS latest_customer_id 
                                                    FROM tblclients 
                                                    WHERE addedfrom = ? AND DATE(datecreated) = ?";
            $customer_result                    = $this->db->query($query, [$staffid, $today])->row();
            $customer_count                     = $customer_result->customer_count ?? 0;
            $latest_customer_id                 = $customer_result->latest_customer_id ?? 0; // Set 0 instead of NULL
            
            $query                              = "SELECT COUNT(*) AS contact_count, MAX(id) AS latest_contact_id 
                                                    FROM tblcontacts 
                                                    WHERE userid IN (SELECT userid FROM tblclients WHERE addedfrom = ?) 
                                                    AND DATE(datecreated) = ?";
            $contact_result                     = $this->db->query($query, [$staffid, $today])->row();
            $contact_count                      = $contact_result->contact_count ?? 0;
            $latest_contact_id                  = $contact_result->latest_contact_id ?? 0; // Set 0 instead of NULL
            
            $where                              = "WHERE t.staff_id = ? 
                                                    AND t.deleted = 0 
                                                    AND t.start_time BETWEEN ? AND ?";

            $query                              = "
                                                    SELECT SUM(t.time_spent) AS time_spent_action_count, 
                                                           COUNT(t.id) AS time_spent_count 
                                                    FROM tbl_sam_taskstimers AS t
                                                    JOIN tbl_sam AS s ON s.id = t.sam_id
                                                    $where";
            
            $startOfDay                         = strtotime($today . " 00:00:00"); // Start of today
            $endOfDay                           = strtotime($today . " 23:59:59"); // End of today
            
            $time_spent_result                  = $this->db->query($query, [$staffid, $startOfDay, $endOfDay])->row();
            
            $total_seconds                      = $time_spent_result->time_spent_action_count ?? 0;
            $time_spent_count                   = $time_spent_result->time_spent_count ?? 0;
            $time_spent_action_count            = gmdate("H:i", $total_seconds);


            
            $query                              = "SELECT SUM(tblinvoices.total) AS invoice_actions_count, COUNT(tblinvoices.id) AS invoice_count 
                                                    FROM tblinvoices 
                                                    JOIN tbl_sam ON tbl_sam.id = tblinvoices.sam_id
                                                    WHERE tbl_sam.default_deal_owner = ? 
                                                    AND tblinvoices.date = ?";
            $invoice_result                     = $this->db->query($query, [$staffid, $today])->row();
            $invoice_actions_count              = $invoice_result->invoice_actions_count ?? 0;
            
            $query                              = "SELECT cn.country_name FROM tbl_sam_employee_country e 
                                                    LEFT JOIN tbl_sam_employee_country ec ON e.staffid = ec.staffid
                                                    LEFT JOIN tbl_sam_employee_multiple_country emc ON ec.employee_country_id = emc.employee_country_id
                                                    LEFT JOIN tbl_sam_country cn ON emc.country_id = cn.country_id
                                                    WHERE e.staffid = ?";
            $country_result                     = $this->db->query($query, [$staffid])->row();
            $staff_country_name                 = $country_result->country_name ?? null;
            $matched_country                    = array_filter($countries, function ($country) use ($staff_country_name)
            {
                return isset($country->country_name) && $country->country_name == $staff_country_name;
            });
    
            $matched_country_id                 = !empty($matched_country) ? reset($matched_country)->id : null;
           
           $user_campus_ids = array_column($users, 'campus_id');

            if (empty($user_campus_ids))
            {
                $schools                        = [];
            }
            else
            {
                $campus_placeholders            = implode(',', array_fill(0, count($user_campus_ids), '?'));
                $query                          = "SELECT id, country_id FROM schools WHERE id IN ($campus_placeholders)";
                $schools                        = $uniranks_db->query($query, $user_campus_ids)->result();
            }

            $matched_schools                    = array_filter($schools, function ($school) use ($matched_country_id, $today)
            {
                if (isset($school->country_id) && $school->country_id != $matched_country_id)
                {
                    return false;
                }
    
                return true;
            });
            
    
            $uniranks_schools_count             = count($matched_schools);
            $query                              = "SELECT * FROM agents 
                                                  WHERE country_id          = ? 
                                                  AND DATE(created_at)      = ?";
            $matched_agents                     = $uniranks_db->query($query, [$matched_country_id, $today])->result();

    
            $uniranks_agents_count              = count($matched_agents);

            $query                              = " SELECT COUNT(*) as student_count
                                                    FROM users u
                                                    JOIN user_bios ub ON u.id = ub.user_id
                                                    WHERE u.role_id         = 13 
                                                    AND DATE(u.created_at)  = ? 
                                                    AND ub.country_id       = ?";
            $result                             = $uniranks_db->query($query, [$today, $matched_country_id])->row();
            $uniranks_students_count            = $result->student_count ?? 0;
            
            $query                              = "SELECT COUNT(*) AS certificate_count
                                                    FROM student_certificates sc
                                                    JOIN users u ON sc.user_id = u.id
                                                    JOIN user_bios ub ON u.id = ub.user_id
                                                    WHERE DATE(sc.created_at) = ? 
                                                    AND ub.country_id = ?";
            $result                             = $uniranks_db->query($query, [$today, $matched_country_id])->row();
            $uniranks_certificates_count        = $result->certificate_count ?? 0;


            
            $query                              = "SELECT COUNT(*) as progress_count
                                                  FROM users u
                                                  JOIN user_bios ub ON u.id = ub.user_id
                                                  JOIN user_profile_progress up ON u.id = up.user_id
                                                  WHERE u.role_id = 13
                                                  AND DATE(u.created_at) = ? 
                                                  AND ub.country_id = ?
                                                  AND up.progress > 0";

            $result                             = $uniranks_db->query($query, [$today, $matched_country_id])->row();
            $progress_count_total               = $result->progress_count ?? 0;

            
            $query                              = "SELECT COUNT(*) as coupon_count
                                                  FROM students_test_coupon_code_log c
                                                  JOIN users u ON c.used_by_id = u.id
                                                  JOIN user_bios ub ON u.id = ub.user_id
                                                  WHERE u.role_id = 13
                                                  AND DATE(u.created_at) = ?
                                                  AND ub.country_id = ?
                                                  AND DATE(c.created_at) = ?";

            $result                             = $uniranks_db->query($query, [$today, $matched_country_id, $today])->row();
            $coupon_count                       = $result->coupon_count ?? 0;

            
            $query                              = "SELECT COUNT(*) as fairs_count
                                                  FROM fairs f
                                                  JOIN schools s ON f.school_id = s.id
                                                  WHERE f.event_type_id = 1
                                                  AND s.country_id = ?
                                                  AND DATE(f.created_at) = ?";
            $result                             = $uniranks_db->query($query, [$matched_country_id, $today])->row();
            $matched_fairs_count                = $result->fairs_count ?? 0;
            
            $query                              = "SELECT COUNT(*) as events_count
                                                  FROM fairs f
                                                  JOIN schools s ON f.school_id = s.id
                                                  WHERE f.event_type_id = 2
                                                  AND s.country_id = ?
                                                  AND DATE(f.created_at) = ?";
            $result                             = $uniranks_db->query($query, [$matched_country_id, $today])->row();
            $matched_events_count               = $result->events_count ?? 0;


            if ($existing_record)
            {
                $this->db->where('id', $existing_record->id);
                $this->db->update('tbl_sam_daily_statistics',
                [
                    'call_count'                => $actions_count,
                    'metting_count'             => $metting_count,
                    'email_count'               => $email_count,
                    'customer_count'            => $customer_count,
                    'contact_count'             => $contact_count,
                    'total_invoice'             => $invoice_actions_count,
                    'uniranks_schools_count'    => $uniranks_schools_count, 
                    'uniranks_agents_count'     => $uniranks_agents_count, 
                    'uniranks_students_count'   => $uniranks_students_count,
                    'uniranks_students_certification_count'=> $uniranks_certificates_count,
                    'uniranks_progress_count'   => $progress_count_total,
                    'uniranks_coupon_count'     => $coupon_count,
                    'uniranks_fair_count'       => $matched_fairs_count,
                    'uniranks_events_count'     => $matched_events_count,
                    'time_sheet'                => $time_spent_action_count
                ]);
            } 
            else 
            {
                $this->db->insert('tbl_sam_daily_statistics',
                [
                    'staffid'                   => $staffid,
                    'call_count'                => $actions_count,
                    'metting_count'             => $metting_count,
                    'email_count'               => $email_count,
                    'customer_count'            => $customer_count,
                    'contact_count'             => $contact_count,
                    'total_invoice'             => $invoice_actions_count,
                    'uniranks_schools_count'    => $uniranks_schools_count,
                    'uniranks_agents_count'     => $uniranks_agents_count,
                    'uniranks_students_count'   => $uniranks_students_count,
                    'uniranks_progress_count'   => $progress_count_total,
                    'uniranks_coupon_count'     => $coupon_count,
                    'uniranks_fair_count'       => $matched_fairs_count,
                    'uniranks_events_count'     => $matched_events_count,
                    'time_sheet'                => $time_spent_action_count,
                    'date_created'              => $today
                ]);
            }
        }
    
        echo json_encode(['status' => 'success']);
    }
}

