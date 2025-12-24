<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sales_marketing extends AdminController
{ 
    public function __construct()
    {   
        parent::__construct();
        $this->load->model('sam_model');
        $this->load->model('staff_model');
        $this->load->model('dashboard_model');  
    }

    public function index($id = null,$sam_ids=0)
    {   

        close_setup_menu();
        if (!is_staff_member()) {
            access_denied('Deals');
        }
        $data['deal_status'] = $this->input->get('deal_status');
        //echo $data['deal_status']; exit;
        $data['switch_kanban'] = true;

        $data['piplines'] = get_sam_result('tbl_sam_pipelines', null, 'array');
        $default_pipeline = get_option('default_pipeline');
        if (empty($default_pipeline)) {
            $default_pipeline = $data['piplines'][0]['pipeline_id'];
        } 
        $data['default_pipeline'] = $default_pipeline;

        $data['sources'] = get_sam_result('tbl_sam_source', null, 'array');

        if ($this->session->userdata('sam_kanban_view') == 'true') {
            $data['switch_kanban'] = false;
            $data['bodyclass'] = 'kan-ban-body';
        }

        $data['staff'] = $this->staff_model->get('', ['active' => 1]);
        $data['customers'] = get_sam_result(db_prefix() . 'clients', null, 'array');
        if (is_gdpr() && get_option('gdpr_enable_consent_for_deals') == '1') {
            $this->load->model('gdpr_model');
            $data['consent_purposes'] = $this->gdpr_model->get_consent_purposes();
        }
        $data['dealid'] = $id;  
        $data['isKanBan'] = $this->session->has_userdata('sam_kanban_view') &&
            $this->session->userdata('sam_kanban_view') == 'true';
        //$data['title'] = _l('deals');
        $data['title'] = 'Sales and Marketing';
        $data['sam_ids'] = $sam_ids;
        $this->load->view('all_sam', $data);
    }

    public function switch_kanban($set = 0)
    {
        if ($set == 1) {
            $set = 'true';
        } else {
            $set = 'false';
        }
        $this->session->set_userdata([
            'sam_kanban_view' => $set,
        ]);
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function kanban()
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }
        $pipeline_id = $this->input->get('pipeline_id');
        $data['base_currency'] = get_base_currency();
        // get stages by pipeline
        $data['stages'] = get_sam_order_by('tbl_sam_stages', ['pipeline_id' => $pipeline_id], 'stage_order', 'asc');
        $data['sources'] = get_sam_result('tbl_sam_source', null, 'array');
        echo $this->load->view('kan-ban', $data, true);
    }

    public function sam_kanban_load_more()
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }

        $status = $this->input->get('status');
        $page = $this->input->get('page');

        $this->db->where('stage_id', $status);
        $status = $this->db->get('tbl_sam_stages')->row_array();

        $leads = (new  \modules\sales_marketing\libraries\DealsKanban($status['stage_id']))
            ->search($this->input->get('search'))
            ->sortBy(
                $this->input->get('sort_by'),
                $this->input->get('sort')
            )
            ->page($page)->get();

        foreach ($leads as $lead) {
            $this->load->view('_kan_ban_card', [
                'deal' => $lead,
                'stage' => $status,
            ]);
        }
    }

    public function update_deal_satges()
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            $this->sam_model->update_deal_satges($this->input->post());
        }
    }

    public function update_stage_order()
    {
        if ($this->input->post()) {
            $this->sam_model->update_stage_order($this->input->post());
        }
    }


    public function add_deals_attachment()
    {

        $id = $this->input->post('id');
        $lastFile = $this->input->post('last_file');
        if (!is_staff_member() || !$this->sam_model->staff_can_access_deals($id)) {
            ajax_access_denied();
        }
        $files = handle_sam_attachments_array($id, 'file');
        if ($files) {
            $i = 0;
            $len = count($files);
            foreach ($files as $file) {
                $success = $this->sam_model->add_attachment_to_database($id, 'sam', [$file]);
                $i++;
            }
        }
    }

    public function delete_attachment($id, $lead_id)
    {
        if (!is_staff_member() || !$this->sam_model->staff_can_access_deals($lead_id)) {
            ajax_access_denied();
        }
        $this->sam_model->delete_deals_attachment($id);
        //

        $msg = _l('sam_attachment_delete');
        $type = "success";
        set_alert($type, $msg);
        redirect('admin/sales_marketing/details/' . $lead_id . '/attachments');
    }

    public function dealsList($sam_ids=0)
    {   
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatabless');
            $deal_owner = $this->input->post('deal_owner');
            $company = $this->input->post('company');
            $pipeline = $this->input->post('pipeline');
            $source = $this->input->post('source');
            $custom_view = $this->input->post('custom_view');
            
            $select = 'tbl_sam.*,tbl_sam_stages.stage_name,tbl_sam_source.source_name,tbl_sam_pipelines.pipeline_name,tbl_sam_status.status_name,tbl_sam_status.color, (SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . '_sam.id and rel_type="sam" ORDER by tag_order ASC) as tags';
            $this->datatabless->table = 'tbl_sam';
            $join_table = array('tbl_sam_stages', 'tbl_sam_source', 'tbl_sam_pipelines', 'tbl_sam_status');
            $join_where = array(
                'tbl_sam_stages.stage_id=tbl_sam.stage_id',
                'tbl_sam_source.source_id=tbl_sam.source_id',
                'tbl_sam_pipelines.pipeline_id=tbl_sam.pipeline_id',
                'tbl_sam_status.status_id=tbl_sam.deal_status'  // Join with deal status table
            );

            $custom_fields = get_custom_fields('sam', [
                'show_on_table' => 1,
            ]);

            $action_array = array('tbl_sam.id');
            $main_column = array('title', 'tbl_sam_stages.stage_name', 'tbl_sam_source.source_name', 'tbl_sam_pipelines.pipeline_name', 'deal_value', 'tbl_sam_status.status_name'); // Include deal status in main columns

            $i = 0;
            foreach ($custom_fields as $field) {
                $select_as = 'cvalue_' . $i;
                $join_table[] = db_prefix() . 'customfieldsvalues as ctable_' . $i;
                $join_where[] = 'tbl_sam.id = ctable_' . $i . '.relid AND ctable_' . $i . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $i . '.fieldid=' . $field['id'];
                $select .= ',ctable_' . $i . '.value as ' . $select_as;
                $main_column[] = 'ctable_' . $i . '.value';
                $i++;
            }

            $this->datatabless->select = $select;
            $this->datatabless->join_table = $join_table;
            $this->datatabless->join_where = $join_where;

            $result = array_merge($main_column, $action_array);
            $this->datatabless->column_order = $result;
            $this->datatabless->column_search = $result;
            $this->datatabless->order = array('tbl_sam.id' => 'desc');
            $deal_status = $this->input->post('deal_status');  // Get deal status filter

            $where = array();
            if(!is_admin()){
                $where['tbl_sam.default_deal_owner'] = get_staff_user_id();    
            }
            if (!empty($deal_owner)) {
                $where['tbl_sam.default_deal_owner'] = $deal_owner;
            }
            if (!empty($pipeline)) {
                $where['tbl_sam.pipeline_id'] = $pipeline;
            }
            if (!empty($source)) {
                $where['tbl_sam.source_id'] = $source;
            }
            if (!empty($deal_status)) {
                $where['tbl_sam.deal_status'] = $deal_status;  // Filter by deal_status
            }
            
            if (!empty($custom_view)) {
                if ($custom_view == 'created_today') {
                    $where['tbl_sam.created_at >='] = date('Y-m-d') . ' 00:00:00';
                } elseif ($custom_view == 'created_this_week') {
                    $where['tbl_sam.created_at >='] = date('Y-m-d', strtotime('monday this week')) . ' 00:00:00';
                } elseif ($custom_view == 'created_last_week') {
                    $where['tbl_sam.created_at >='] = date('Y-m-d', strtotime('monday last week')) . ' 00:00:00';
                    $where['tbl_sam.created_at <='] = date('Y-m-d', strtotime('sunday last week')) . ' 23:59:59';
                } elseif ($custom_view == 'created_this_month') {
                    $where['tbl_sam.created_at >='] = date('Y-m-01') . ' 00:00:00';
                } elseif ($custom_view == 'created_last_month') {
                    $where['tbl_sam.created_at >='] = date('Y-m-01', strtotime('last month')) . ' 00:00:00';
                    $where['tbl_sam.created_at <='] = date('Y-m-t', strtotime('last month')) . ' 23:59:59';
                } else if ($custom_view == 'customer') {
                    $where['tbl_sam.rel_type'] = 'customer';
                } else if ($custom_view == 'lead') {
                    $where['tbl_sam.rel_type'] = 'lead';
                } else if ($custom_view == 'contract') {
                    $where['tbl_sam.rel_type'] = 'contract';
                } else if ($custom_view == 'proposal') {
                    $where['tbl_sam.rel_type'] = 'proposal';
                } else {
                    $where['tbl_sam.status'] = $custom_view;
                }
            }
            $where_in = array();
            if($sam_ids!="" && $sam_ids!=0){
                
                $deal_ids = $sam_ids;         
                $where_in[0] = "tbl_sam.id";
                $where_in[1] = explode(',',urldecode($deal_ids));   
                //echo "<pre>"; print_r($where_in[1]); exit;         
                $fetch_data = make_sam_datatables($where,$where_in);
            }
            else{
                $fetch_data = make_sam_datatables($where);    
            }                         
            
            $edited = has_permission('sam', '', 'edit');
            $deleted = has_permission('sam', '', 'delete');
            $data = array();
            //echo "<pre>"; print_r($fetch_data); exit;
            foreach ($fetch_data as $_key => $v_deals) {
                $action = null;
                $sub_array = array();
                $assignee = $v_deals->user_id;
                $assignee_name = "";
                if($assignee!=""){
                    $assignee = json_decode($assignee);
                    for($i=0;$i<count($assignee);$i++){
                        $assignee_name .= get_staff_full_name($assignee[$i]).'<br>';
                    }                                                                       
                }
                $sub_array[] = '<a  ' . ' class="text-info" href="' . base_url() . 'admin/sales_marketing/details/' . $v_deals->id . '">' . $v_deals->title . '</a>';
                $sub_array[] = sam_display_money($v_deals->deal_value, sam_default_currency());
                //$sub_array[] = render_tags($v_deals->tags);
                $sub_array[] = $assignee_name;
                $sub_array[] = $v_deals->pipeline_name;
                $sub_array[] = $v_deals->stage_name;
                $sub_array[] = _d($v_deals->days_to_close);
                $sub_array[] = (!empty($v_deals->status) ? _l($v_deals->status) : '');
                $sub_array[] = (!empty($v_deals->status_name) && !empty($v_deals->color)) ?     
                    '<span style="color: ' . $v_deals->color . '"><strong>' . _l($v_deals->status_name) . '</strong></span>' : '';  
                $cvalue = 'cvalue_' . $_key;
                if (!empty($v_deals->$cvalue) && $v_deals->$cvalue) {
                    $sub_array[] = $v_deals->$cvalue;
                }
                $action .= btn_view_sam('admin/sales_marketing/details/' . $v_deals->id) . ' ';
                if (!empty($edited)) {
                    $action .= sam_btn_edit_deals('admin/sales_marketing/new_sam/' . $v_deals->id) . ' ';
                }
                if (!empty($deleted)) {
                    $action .= btn_delete_sam('admin/sales_marketing/delete_sam/' . $v_deals->id) . ' ';
                }

                $sub_array[] = $action;
                $data[] = $sub_array;
            }
            if($sam_ids!="" && $sam_ids!=0){
                sam_render_table2($data, $where, $where_in);    
            }
            else{
                sam_render_table($data, $where);
            }
            
        } else {
            redirect('admin/dashboard');
        }
    }

    public function new_sam($id = NULL)
    {
        //$data['title'] = _l('deals'); //Page title
        $data['title'] = 'Sales and Marketing'; //Page title

        if (!empty($id)) { 
            $edited = has_permission('sam', get_staff_user_id(), 'edit');
            if (!empty($edited)) {
                $data['deals'] = $this->db->where('id', $id)->get('tbl_sam')->row();
            }
            if (empty($data['deals'])) {
                $type = "error";
                $message = _l("no_record_found");
                set_alert($type, $message);
                redirect('admin/sales_marketing/new_deals');
            }
            
        }                                                                    
        $data['sources'] = get_sam_result('tbl_sam_source', null, 'array');
        $data['customers'] = get_sam_result(db_prefix() . 'clients', null, 'array');
        // echo "<pre>"; print_r($data['customers']); exit;
        $data['pipelines'] = get_sam_result('tbl_sam_pipelines', null, 'array');
        $data['staff'] = $this->staff_model->get('', ['active' => 1]);
        $this->load->view('new_sam', $data);                   
    }

    public function channels()
    {
        if (!is_admin() && get_option('staff_members_create_inline_deal_source') == '0') {
            access_denied('Deals Sources');
        }
        $data['sources'] = get_sam_result('tbl_sam_source', null, 'array');
        //$data['title'] = _l('deals_sources');
        $data['title'] = 'Sales and Marketing Channel';
        $this->load->view('sources', $data);
    }

    public function channel_id($id = null)
    {
        if (!is_admin() && get_option('staff_members_create_inline_deal_source') == '0') {
            access_denied('Deals Sources');
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            if (!$this->input->post('id')) {
                $inline = isset($data['inline']);
                if (isset($data['inline'])) {
                    unset($data['inline']);
                }
            } else {
                $id = $data['id'];
                unset($data['id']);
            }
            $pdata['source_name'] = $data['name'];

            $this->sam_model->_table_name = "tbl_sam_source"; // table name
            $this->sam_model->_primary_key = "source_id"; // $id
            $return_id = $this->sam_model->save_sam($pdata, $id);
            if (!$inline) {
                if ($return_id) {
                    //set_alert('success', _l('added_successfully', _l('deal_source')));
                    set_alert('success', _l('added_successfully', _l('sales_and_marketing_channel')));
                }
            } else {
                echo json_encode(['success' => $return_id ? true : false, 'id' => $return_id]);
            }
            if ($id) {
                //set_alert('success', _l('updated_successfully', _l('deal_source')));
                set_alert('success', _l('updated_successfully', _l('sales_and_marketing_channel')));
            }

        }
    }

    public function delete_channel($id)
    {
        if (!is_admin() && get_option('staff_members_create_inline_deal_source') == '0') {
            access_denied('Deals Sources');
        }
        $this->sam_model->_table_name = "tbl_sam_source"; // table name
        $this->sam_model->_primary_key = "source_id"; // $id
        $this->sam_model->delete_sam($id);
        //set_alert('success', _l('delete_successfully', _l('deal_source')));
        set_alert('success', _l('delete_successfully', _l('sales_and_marketing_channel')));
        redirect('admin/sales_marketing/channels');
    }

    public function pipelines()
    {
        if (!is_admin() && get_option('staff_members_create_inline_deal_pipeline') == '0') {
            access_denied('Deals Pipelines');
        }
        $data['pipelines'] = get_sam_result('tbl_sam_pipelines', null, 'array');
        //$data['title'] = _l('deals_pipelines');
        $data['title'] = 'Pipelines';
        $this->load->view('pipelines', $data);
    }

    public function pipeline($id = null)
    {
        if (!is_admin() && get_option('staff_members_create_inline_deal_pipeline') == '0') {
            access_denied('Deals Pipeline');
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            if (!$this->input->post('id')) {
                $inline = isset($data['inline']);
                if (isset($data['inline'])) {
                    unset($data['inline']);
                }
            } else {
                $id = $data['id'];
                unset($data['id']);
            }
            $pdata['pipeline_name'] = $data['name'];

            $this->sam_model->_table_name = "tbl_sam_pipelines"; // table name
            $this->sam_model->_primary_key = "pipeline_id"; // $id
            $return_id = $this->sam_model->save_sam($pdata, $id);
            if (!$inline) {
                if ($return_id) {
                    //set_alert('success', _l('added_successfully', _l('deals_pipeline')));
                    set_alert('success', _l('added_successfully', _l('sales_and_marketing_pipeline')));
                }
            } else {
                echo json_encode(['success' => $return_id ? true : false, 'id' => $return_id]);
            }
            if ($id) {
                //set_alert('success', _l('updated_successfully', _l('deals_pipeline')));
                set_alert('success', _l('updated_successfully', _l('sales_and_marketing_pipeline')));

            }
        }
    }


    public function delete_pipeline($id)
    {
        if (!is_admin() && get_option('staff_members_create_inline_deal_pipeline') == '0') {
            access_denied('Deals Pipeline');
        }
        $this->sam_model->_table_name = "tbl_sam_pipelines"; // table name
        $this->sam_model->_primary_key = "pipeline_id"; // $id
        $this->sam_model->delete_sam($id);
        //set_alert('success', _l('delete_successfully', _l('deal_pipeline')));
        set_alert('success', _l('delete_successfully', _l('sales_and_marketing_pipeline')));
        redirect('admin/sales_marketing/pipelines');
    }

    public function stages()
    {
        if (!is_admin() && get_option('staff_members_create_inline_deal_stage') == '0') {
            access_denied('Deals Stages');
        }
        $data['stages'] = sam_join_data('tbl_sam_stages', '*', null, [
            'tbl_sam_pipelines' => 'tbl_sam_pipelines.pipeline_id = tbl_sam_stages.pipeline_id',
        ], 'array', ['tbl_sam_stages.stage_order' => 'ASC']);

        $data['pipelines'] = get_sam_result('tbl_sam_pipelines', null, 'array');
        //$data['title'] = _l('deals_stages');
        $data['title'] = 'Stages';
        $this->load->view('stages', $data);
    }


    public function stage($id = null)
    {
        if (!is_admin() && get_option('staff_members_create_inline_deal_stage') == '0') {
            access_denied('Deals stages');
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            if (!$this->input->post('id')) {
                $inline = isset($data['inline']);
                if (isset($data['inline'])) {
                    unset($data['inline']);
                }
            } else {
                $id = $data['id'];
                unset($data['id']);
            }
            $pdata['stage_name'] = $data['name'];
            $pdata['pipeline_id'] = $data['pipeline_id'];
            $pdata['stage_order'] = $data['stage_order'];

            $this->sam_model->_table_name = "tbl_sam_stages"; // table name
            $this->sam_model->_primary_key = "stage_id"; // $id
            $return_id = $this->sam_model->save_sam($pdata, $id);
            if (!$inline) {
                if ($return_id) {
                    //set_alert('success', _l('added_successfully', _l('deals_pipeline')));
                    set_alert('success', _l('added_successfully', 'sales_and_marketing_stage'));
                }
            } else {
                echo json_encode(['success' => $return_id ? true : false, 'id' => $return_id]);
            }
            if ($id) {
                //set_alert('success', _l('updated_successfully', _l('deals_pipeline')));
                set_alert('success', _l('updated_successfully', 'sales_and_marketing_stage'));

            }
        }
    }


    



    public function status()
    {
        if (!is_admin() && get_option('staff_members_create_inline_deal_status') == '0') {
            access_denied('Deals Status');
        }
        $data['statuses'] = get_sam_result('tbl_sam_status', null);

        $data['title'] = 'Status';
        $this->load->view('status', $data);
    }
    
     public function assign_employee_country()
    {
        if (!is_admin() && get_option('staff_members_create_inline_deal_status') == '0')
        {
            access_denied('Country');
        }
    
        $this->db->select('ec.employee_country_id, ec.staffid, 
                           GROUP_CONCAT(mc.country_id) as country_ids, 
                           GROUP_CONCAT(cn.country_name) as country_names, 
                           CONCAT(s.firstname, " ", s.lastname) as employee_name');
        $this->db->from('tbl_sam_employee_country ec');
        $this->db->join('tbl_sam_employee_multiple_country mc', 'ec.employee_country_id = mc.employee_country_id', 'left');
        $this->db->join('tbl_sam_country cn', 'mc.country_id = cn.country_id', 'left');
        $this->db->join('tblstaff s', 'ec.staffid = s.staffid', 'left');
        $this->db->group_by('ec.employee_country_id');
        
        $data['employeecountries']              = $this->db->get()->result();
        $data['countries']                      = $this->db->select('country_id, country_name')->get('tbl_sam_country')->result_array();
        $data['staff']                          = $this->dashboard_model->get_all_staff();
        // echo "<pre>"; print_r($data['staff']); exit;

        $data['title']                          = 'Assign Country To Employee';
        // echo "<pre>"; print_r($data); exit;
        $this->load->view('assign_employee_country', $data);
    }
     public function overallperformance()
    {
        if (!is_admin() && get_option('staff_members_create_inline_deal_status') == '0')
        {
            access_denied('Overallperformance');
        }
    
        $this->db->select('ec.staffid,ec.overallperformance_id,ec.kpi_function_id,kp.kpi_name,ec.kpi_weight, 
                           CONCAT(s.firstname, " ", s.lastname) as employee_name');
        $this->db->from('tbl_sam_overallperformance ec');
        $this->db->join('tblstaff s', 'ec.staffid = s.staffid', 'left');
        $this->db->join('tbl_sam_kpi_function kp', 'ec.kpi_function_id = kp.kpi_function_id', 'left');

        $data['employeeoverallperformances']              = $this->db->get()->result();
        $data['staff']                                    = $this->dashboard_model->get_all_staff();
        $data['kpi_function']                             = get_sam_result('tbl_sam_kpi_function', null, 'array');
        // echo "<pre>"; print_r($data['staff']); exit;

        $data['title']                          = 'Overallperformance';
        // echo "<pre>"; print_r($data); exit;
        $this->load->view('assign_employee_overallperformance', $data);
    }
    public function add_overall($id = null)
    {
        if (!is_admin() && get_option('staff_members_create_inline_deal_stage') == '0') {
            access_denied('Overallperformance');
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            if (!$this->input->post('id')) {
                $inline = isset($data['inline']);
                if (isset($data['inline'])) {
                    unset($data['inline']);
                }
            } else {
                $id = $data['id'];
                unset($data['id']);
            }
            $pdata['staffid'] = $data['staffid'];
            $pdata['kpi_weight'] = $data['kpi_weight'];
            $pdata['kpi_function_id'] = $data['kpi_function_id'];

            $this->sam_model->_table_name = "tbl_sam_overallperformance"; // table name
            $this->sam_model->_primary_key = "overallperformance_id"; // $id
            $return_id = $this->sam_model->save_sam($pdata, $id);
            if (!$inline) {
                if ($return_id) {
                    set_alert('success', _l('added_successfully', 'Performance'));
                }
            } else {
                echo json_encode(['success' => $return_id ? true : false, 'id' => $return_id]);
            }
            if ($id) {
                set_alert('success', _l('updated_successfully', 'Performance'));

            }
        }
    }
       
     public function delete_overallperformance($id)
    {

        $this->sam_model->_table_name = 'tbl_sam_overallperformance';
        $this->sam_model->_primary_key = 'overallperformance_id';
        $this->sam_model->delete_sam($id);

        $type = "success";
        $message = _l('Performance Delete Successfully');
        set_alert($type, $message);
        redirect('admin/sales_marketing/overallperformance');
    }
     public function desktime()
    {
        if (!is_admin() && get_option('staff_members_create_inline_deal_status') == '0')
        {
            access_denied('Desktime');
        }
    
        $this->db->select('ec.staffid,ec.employee_desktime_id,ec.desktime_id, 
                           CONCAT(s.firstname, " ", s.lastname) as employee_name');
        $this->db->from('tbl_sam_desktime ec');
        $this->db->join('tblstaff s', 'ec.staffid = s.staffid', 'left');

        $data['employeedesktimes']              = $this->db->get()->result();
        $data['staff']                          = $this->dashboard_model->get_all_staff();
        // echo "<pre>"; print_r($data['staff']); exit;

        $data['title']                          = 'Desktime';
        // echo "<pre>"; print_r($data); exit;
        $this->load->view('assign_employee_desktime', $data);
    }
    
     public function add_desktime($id = null)
    {
        if (!is_admin() && get_option('staff_members_create_inline_deal_stage') == '0') {
            access_denied('Desktime');
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            if (!$this->input->post('id')) {
                $inline = isset($data['inline']);
                if (isset($data['inline'])) {
                    unset($data['inline']);
                }
            } else {
                $id = $data['id'];
                unset($data['id']);
            }
            $pdata['staffid'] = $data['staffid'];
            $pdata['employee_desktime_id'] = $data['employee_desktime_id'];

            $this->sam_model->_table_name = "tbl_sam_desktime"; // table name
            $this->sam_model->_primary_key = "desktime_id "; // $id
            $return_id = $this->sam_model->save_sam($pdata, $id);
            if (!$inline) {
                if ($return_id) {
                    set_alert('success', _l('added_successfully', 'Desktime'));
                }
            } else {
                echo json_encode(['success' => $return_id ? true : false, 'id' => $return_id]);
            }
            if ($id) {
                set_alert('success', _l('updated_successfully', 'Desktime'));

            }
        }
    }

    public function employee_countries($id = null)
    {
        if (!is_admin() && get_option('staff_members_create_inline_deal_stage') == '0') {
            access_denied('Country');
        }
    
        if ($this->input->post()) {
            $data = $this->input->post();
            if (!$this->input->post('id')) {
                $inline = isset($data['inline']) ? true : false;
                if (isset($data['inline'])) {
                    unset($data['inline']);
                }
            } else {
                $id = $data['id'];
                unset($data['id']);
            }
    
            $pdata = [
                'staffid' => $data['staffid'],
            ];
    
            $this->sam_model->_table_name = "tbl_sam_employee_country"; // Table Name
            $this->sam_model->_primary_key = "employee_country_id"; // Primary Key
            $employee_country_id = $this->sam_model->save_sam($pdata, $id); // Save Record
            if ($employee_country_id && isset($data['multiple_country_id']) && is_array($data['multiple_country_id'])) {
                $this->db->where('employee_country_id', $employee_country_id);
                $this->db->delete('tbl_sam_employee_multiple_country');
                foreach ($data['multiple_country_id'] as $country_id) {
                    $multi_country_data = [
                        'employee_country_id' => $employee_country_id,
                        'country_id' => $country_id,
                    ];
                    $this->db->insert('tbl_sam_employee_multiple_country', $multi_country_data);
                }
            }
    
            if (!$inline) {
                if ($employee_country_id) {
                    set_alert('success', _l('added_successfully', 'Countries Assigned to Employee'));
                }
            } else {
                echo json_encode(['success' => $employee_country_id ? true : false, 'id' => $employee_country_id]);
            }
    
            if ($id) {
                set_alert('success', _l('updated_successfully', 'Countries Assigned to Employee'));
            }
        }
    }

    public function categories()
    {
        if (!is_admin() && get_option('staff_members_create_inline_deal_status') == '0') {
            access_denied('Categories');
        }
        $data['categories'] = get_sam_result('tbl_sam_category', null);
        $data['title'] = 'Categories';
        $this->load->view('categories', $data);
    }
    
    public function category($id = null)
    {
    if (!is_admin() && get_option('staff_members_create_inline_deal_status') == '0') {
            access_denied('Categories');
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            if (!$this->input->post('id')) {
                $inline = isset($data['inline']);
                if (isset($data['inline'])) {
                    unset($data['inline']);
                }
            } else {
                $id = $data['id'];
                unset($data['id']);
            }
            $pdata['category_name'] = $data['name'];
    
            $this->sam_model->_table_name = "tbl_sam_category"; // table name
            $this->sam_model->_primary_key = "category_id"; // $id
            $return_id = $this->sam_model->save_sam($pdata, $id);
            if (!$inline) {
                if ($return_id) {
                    set_alert('success', _l('added_successfully', 'sales_and_marketing_category'));
                }
            } else {
                echo json_encode(['success' => $return_id ? true : false, 'id' => $return_id]);
            }
            if ($id) {
                set_alert('success', _l('updated_successfully', 'sales_and_marketing_category'));
            }
        }
    }

    public function country()
    {
        if (!is_admin() && get_option('staff_members_create_inline_deal_status') == '0') {
            access_denied('Country');
        }
        $data['countries'] = get_sam_result('tbl_sam_country', null);
        // echo "<pre>"; print_r($data['countries']); exit;

        $data['title'] = 'Country';
        $this->load->view('country', $data);
    }
    
     public function delete_desktime($id)
    {

        $this->sam_model->_table_name = 'tbl_sam_desktime';
        $this->sam_model->_primary_key = 'desktime_id';
        $this->sam_model->delete_sam($id);

        $type = "success";
        $message = _l('Desktime Delete Successfully');
        set_alert($type, $message);
        redirect('admin/sales_marketing/desktime');
    }
     public function delete_category($id)
    {

        $this->sam_model->_table_name = 'tbl_sam_category';
        $this->sam_model->_primary_key = 'category_id';
        $this->sam_model->delete_sam($id);

        $type = "success";
        $message = _l('Category Delete Successfully');
        set_alert($type, $message);
        redirect('admin/sales_marketing/categories');
    }
    
    public function countries($id = null)
    {
    if (!is_admin() && get_option('staff_members_create_inline_deal_status') == '0') {
        access_denied('Country');
    }
    if ($this->input->post()) {
        $data = $this->input->post();
        if (!$this->input->post('id')) {
            $inline = isset($data['inline']);
            if (isset($data['inline'])) {
                unset($data['inline']);
            }
        } else {
            $id = $data['id'];
            unset($data['id']);
        }
        $pdata['country_name'] = $data['name'];

        $this->sam_model->_table_name = "tbl_sam_country"; // table name
        $this->sam_model->_primary_key = "country_id"; // $id
        $return_id = $this->sam_model->save_sam($pdata, $id);
        if (!$inline) {
            if ($return_id) {
                set_alert('success', _l('added_successfully', 'sales_and_marketing_country'));
            }
        } else {
            echo json_encode(['success' => $return_id ? true : false, 'id' => $return_id]);
        }
        if ($id) {
            set_alert('success', _l('updated_successfully', 'sales_and_marketing_country'));
        }
    }
}
  public function delete_country($id)
    {

        $this->sam_model->_table_name = 'tbl_sam_country';
        $this->sam_model->_primary_key = 'country_id';
        $this->sam_model->delete_sam($id);

        $type = "success";
        $message = _l('Country Delete Successfully');
        set_alert($type, $message);
        redirect('admin/sales_marketing/country');
    }
  public function delete_employee_country($id)
    {

        $this->sam_model->_table_name = 'tbl_sam_employee_country';
        $this->sam_model->_primary_key = 'employee_country_id';
        $this->sam_model->delete_sam($id);

        $type = "success";
        $message = _l('Country Delete Successfully From That Employee');
        set_alert($type, $message);
        redirect('admin/sales_marketing/assign_employee_country');
    }
    

  public function employee_kpi()
    {
        if (!is_admin() && get_option('staff_members_create_inline_deal_status') == '0') {
            access_denied('Customer Kpi');
        }
    
        // Fetch employee KPIs with joined names
        $this->db->select('ek.employee_kpi_id, ek.kpi_function_id, ek.staffid, ek.no_of_actions,ek.day,ek.week,ek.month,ek.year, 
                           kf.kpi_name,tblcurrencies.symbol,tbl_sam_category.category_name,ek.currencyid,ek.category_id,
                           CONCAT(s.firstname, " ", s.lastname) as employee_name');
        $this->db->from('tbl_sam_employee_kpi ek');
        $this->db->join('tblcurrencies', 'tblcurrencies.id = ek.currencyid', 'left');
        $this->db->join('tbl_sam_category', 'tbl_sam_category.category_id = ek.category_id', 'left');
        $this->db->join('tbl_sam_kpi_function kf', 'ek.kpi_function_id = kf.kpi_function_id', 'left');
        $this->db->join('tblstaff s', 'ek.staffid = s.staffid', 'left');
        $data['employeekpis'] = $this->db->get()->result();
                // echo "<pre>"; print_r($data['employeekpis']); exit;
        $data['currencies'] = $this->db->select('id, symbol as symbol')->get('tblcurrencies')->result_array();
        
        $data['categories'] = $this->db->select('category_id, category_name as name')->get('tbl_sam_category')->result_array();
    
        // Fetch KPI functions and staff for dropdowns
        $kpi_functions = $this->db->select('kpi_function_id, kpi_name')
                                   ->get('tbl_sam_kpi_function')
                                   ->result_array();
        $data['kpi_functions'] = $kpi_functions;
        $data['staff']                          = $this->dashboard_model->get_all_staff();
        $data['title'] = 'Employee KPI';
        $this->load->view('employee_kpi', $data);
    }

    
 
  public function employee_kpis($id = null)
{
    if (!is_admin() && get_option('staff_members_create_inline_deal_stage') == '0') {
        access_denied('Customer Kpi');
    }

    if ($this->input->post()) {
        $data = $this->input->post();
        
        // Ensure no_of_actions is set
        $no_of_actions = isset($data['no_of_actions']) ? $data['no_of_actions'] : 0;

        if (!$this->input->post('id')) {
            $inline = isset($data['inline']) ? true : false;
            if (isset($data['inline'])) {
                unset($data['inline']);
            }
        } else {
            $id = $data['id'];
            unset($data['id']);
        }

        // Calculate KPI values
        $day = $no_of_actions;
        $week = $no_of_actions * 5;
        $month = $no_of_actions * 22;
        $year = $no_of_actions * 260;

        // Prepare data to be saved
        $pdata = [
            'currencyid' => $data['currencyid'],
            'category_id' => $data['category_id'],
            'kpi_function_id' => $data['kpi_function_id'],
            'staffid' => $data['staffid'],
            'no_of_actions' => $data['no_of_actions'],
            'day' => $day,
            'week' => $week,
            'month' => $month,
            'year' => $year,
        ];

        // Save to database
        $this->sam_model->_table_name = "tbl_sam_employee_kpi"; // table name
        $this->sam_model->_primary_key = "employee_kpi_id"; // $id
        $return_id = $this->sam_model->save_sam($pdata, $id);
        
        if (!$inline) {
            if ($return_id) {
                set_alert('success', _l('added_successfully', 'sales_and_marketing_employee_kpi'));
            }
        } else {
            echo json_encode(['success' => $return_id ? true : false, 'id' => $return_id]);
        }

        if ($id) {
            set_alert('success', _l('updated_successfully', 'sales_and_marketing_employee_kpi'));
        }
    }
}


public function get_kpi_count() {
    $kpi_function_id = $this->input->post('kpi_function_id');

    if (!$kpi_function_id) {
        echo json_encode(['status' => false, 'message' => 'Invalid KPI Function ID']);
        return;
    }

    $this->db->select('kpi_count');
    $this->db->where('kpi_function_id', $kpi_function_id);
    $query = $this->db->get('tbl_sam_kpi_function');

    if ($query->num_rows() > 0) {
        $result = $query->row();
        echo json_encode(['status' => true, 'kpi_count' => $result->kpi_count]);
    } else {
        echo json_encode(['status' => false, 'message' => 'No KPI count found']);
    }
}




    public function kpi_function()
    {
        if (!is_admin() && get_option('staff_members_create_inline_deal_status') == '0') {
            access_denied('Customer Kpi');
        }
        $data['kpifunctions'] = $this->db->select('tbl_sam_kpi_function.*') // Select necessary fields
            ->from('tbl_sam_kpi_function')
            ->get()
            ->result();
        $data['title'] = 'KPI Function';
        $this->load->view('customer_kpi', $data);
    }
    
     public function kpi_functions($id = null)
    {
        
        if (!is_admin() && get_option('staff_members_create_inline_deal_stage') == '0') {
            access_denied('Customer Kpi');
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            if (!$this->input->post('id')) {
                $inline = isset($data['inline']);
                if (isset($data['inline'])) {
                    unset($data['inline']);
                }
            } else {
                $id = $data['id'];
                unset($data['id']);
            }
            $pdata['kpi_name'] = $data['kpi_name']; // Update 'name' to 'kpi_name'
            $pdata['kpi_count'] = $data['kpi_count']; // Ensure this matches the input field name

            $this->sam_model->_table_name = "tbl_sam_kpi_function"; // table name
            $this->sam_model->_primary_key = "kpi_function_id"; // $id
            $return_id = $this->sam_model->save_sam($pdata, $id);
            if (!$inline) {
                if ($return_id) {
                    //set_alert('success', _l('added_successfully', _l('deals_pipeline')));
                    set_alert('success', _l('added_successfully', 'sales_and_marketing_kpi_function'));
                }
            } else {
                echo json_encode(['success' => $return_id ? true : false, 'id' => $return_id]);
            }
            if ($id) {
                //set_alert('success', _l('updated_successfully', _l('deals_pipeline')));
                set_alert('success', _l('updated_successfully', 'sales_and_marketing_kpi_function'));

            }
        }
    }

    
      public function delete_kpi_function($id)
    {
        $this->sam_model->_table_name = 'tbl_sam_kpi_function';
        $this->sam_model->_primary_key = 'kpi_function_id';
        $this->sam_model->delete_sam($id);

        $type = "success";
        $message = _l('KPI Function Deleted Successfully');
        set_alert($type, $message);
        redirect('admin/sales_marketing/kpi_function');
    }
    
      public function delete_employee_kpi($id)
    {
        $this->sam_model->_table_name = 'tbl_sam_employee_kpi';
        $this->sam_model->_primary_key = 'employee_kpi_id';
        $this->sam_model->delete_sam($id);

        $type = "success";
        $message = _l('Employee KPI Deleted Successfully');
        set_alert($type, $message);
        redirect('admin/sales_marketing/employee_kpi');
    }





   public function statuses($id = null)
{
    if (!is_admin() && get_option('staff_members_create_inline_deal_status') == '0') {
        access_denied('Deals Status');
    }
    if ($this->input->post()) {
        $data = $this->input->post();
        if (!$this->input->post('id')) {
            $inline = isset($data['inline']);
            if (isset($data['inline'])) {
                unset($data['inline']);
            }
        } else {
            $id = $data['id'];
            unset($data['id']);
        }
        $pdata['status_name'] = $data['name'];
        $pdata['color'] = $data['color']; // Add the color to the data array

        $this->sam_model->_table_name = "tbl_sam_status"; // table name
        $this->sam_model->_primary_key = "status_id"; // $id
        $return_id = $this->sam_model->save_sam($pdata, $id);
        if (!$inline) {
            if ($return_id) {
                set_alert('success', _l('added_successfully', 'sales_and_marketing_stage'));
            }
        } else {
            echo json_encode(['success' => $return_id ? true : false, 'id' => $return_id]);
        }
        if ($id) {
            set_alert('success', _l('updated_successfully', 'sales_and_marketing_status'));
        }
    }
}


    public
    function settings()
    {
        //$data['title'] = _l('deals_settings');
        $data['title'] = 'Sales and Marketing Settings';
        $data['staff'] = $this->staff_model->get('', ['active' => 1]);
        $data['pipelines'] = get_sam_result('tbl_sam_pipelines', null, 'array');
        $data['sources'] = get_sam_result('tbl_sam_source', null, 'array');
        $data['stages'] = get_sam_result('tbl_sam_stages', null, 'array');
        $this->load->view('sam_settings', $data);
    }

    public function save_settings()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $post_data['settings']['default_sam_owner'] = $data['default_sam_owner'] ?? null;
            $post_data['settings']['default_sam_pipeline'] = $data['default_sam_pipeline'] ?? null;
            $post_data['settings']['default_sam_source'] = $data['default_sam_source'] ?? null;
            $post_data['settings']['default_sam_stage'] = $data['stage_id'] ?? null;
            $post_data['settings']['sam_kanban_limit'] = $data['sam_kanban_limit'] ?? 50;
            $post_data['settings']['default_sam_kanban_sort_type'] = $data['default_sam_kanban_sort_type'] ?? 'dealorder';
            $post_data['settings']['default_sam_kanban_sort_by'] = $data['default_sam_kanban_sort_by'] ?? 'asc';
            $post_data['settings']['sam_select_company_multiple_or_single'] = $data['select_company_multiple_or_single'] ?? 'multiple';

            // load settings_model
            $this->load->model('payment_modes_model');
            $this->load->model('settings_model');
            $success = $this->settings_model->update($post_data);
            if ($success > 0) {
                //set_alert('success', _l('updated_successfully', _l('deals_settings')));
                set_alert('success', _l('updated_successfully', 'Sales and Marketing Settings'));
            }
            redirect('admin/sales_marketing/settings');
        }
    }

    public function getStateByID($id, $stage_id = null)
    {
        $stages = get_sam_order_by('tbl_sam_stages', array('pipeline_id' => $id), 'stage_order', true, null, 'array');
        if (!empty($stage_id)) {
            $selected = $stage_id;
        } else {
            $selected = get_option('default_sam_stage');
        }
        //$select = render_select('stage_id', $stages, ['stage_id', 'stage_name'], _l('stage'), $selected);
        $select = render_select('stage_id', $stages, ['stage_id', 'stage_name'], 'Stage', $selected);
        echo json_encode($select);
    }

    public function edit_comment()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $data['content'] = html_purify($this->input->post('content', false));
            if ($this->input->post('no_editor')) {
                $data['content'] = nl2br(clear_textarea_breaks($this->input->post('content')));
            }
            $success = $this->sam_model->edit_comment($data);
            $message = '';
            if ($success) { 
                $message = _l('task_comment_updated');
            }
            echo json_encode([
                'success' => $success,
                'message' => $message,
            ]);
        }
    }

    public function add_deals_comment()
    {
        $data = $this->input->post();

        $data['content'] = html_purify($this->input->post('content', false));
        if ($this->input->post('no_editor')) {
            $data['content'] = nl2br($this->input->post('content'));
        }
        $comment_id = false;
        if (
            $data['content'] != ''
            || (isset($_FILES['file']['name']) && is_array($_FILES['file']['name']) && count($_FILES['file']['name']) > 0)
        ) {
            $comment_id = $this->sam_model->add_deal_comment($data);
            if ($comment_id) { 
            
                $activity = 'created comment';
                $this->sam_model->log_deals_activity($data['deal_id'], $activity, false, serialize([
                    $data['content'],
                ]));
               
                add_activity_transactions($data['deal_id'],'comment');
                $commentAttachments = handle_sam_attachments_array($data['deal_id'], 'file');
                if ($commentAttachments && is_array($commentAttachments)) {
                    foreach ($commentAttachments as $file) {
                        $file['deal_comment_id'] = $comment_id;
                        $this->sam_model->add_attachment_to_database($data['deal_id'], 'sam', [$file]);
                    }

                    if (count($commentAttachments) > 0) {
                        $this->db->query("UPDATE tbl_sam_comments  SET content = CONCAT(content, '[sam_attachment]')
                            WHERE id = " . $this->db->escape_str($comment_id));
                    }
                }
            }
        }
        echo json_encode([
            'success' => $comment_id ? true : false,
            // 'taskHtml' => $this->get_task_data($data['deal_id'], true),
        ]);
    }

    public function save_sam($id = NULL)
    {

        $created = has_permission('sam', '', 'create');
        $edited = has_permission('sam', '', 'edit');
        //echo "<pre>"; print_r($this->input->post()); exit;
        if (!empty($created) || !empty($edited) && !empty($id)) {
            $data = $this->sam_model->deals_array_from_post(array(
                'title',
                'deal_value',
                'source_id',
                'days_to_close',
                'pipeline_id',
                'stage_id',
                'default_deal_owner',
                'rel_type',
                'rel_id'
            ));

            //echo "<pre>"; print_r($data); exit;
            if($data['rel_type']!="" && $data['rel_type']=="customer"){
                $data['rel_id'] = $this->input->post('rel_id2');
                $data['contact_id'] = $this->input->post('contact_id');
                //unset($data['rel_id2']);
            }
            $custom_fields = $this->input->post('custom_fields');

            $tags = $this->input->post('tags');
            if (empty($id)) {
                $data['status'] = 'open';
            }
            $data['client_id'] = json_encode($this->input->post('client_id', true));
            $data['user_id'] = json_encode($this->input->post('user_id', true));

            $where = array('title' => $data['title']);
            // duplicate value check in DB
            if (!empty($id)) { // if id exist in db update data
                $deal_id = array('id !=' => $id);
            } else { // if id is not exist then set id as null
                $deal_id = null;
            }
            // check whether this input data already exist or not
            $check_users = $this->sam_model->check_deals_update('tbl_sam', $where, $deal_id);
            if (!empty($check_users)) { // if input data already exist show error alert
                // massage for user
                $type = 'warning';
                $msg = _l('SAM_already_exist');
            } else {
                $this->sam_model->_table_name = "tbl_sam"; // table name
                $this->sam_model->_primary_key = "id"; // $id
                $return_id = $this->sam_model->save_sam($data, $id);
                if (!empty($custom_fields)) {
                    handle_custom_fields_post($return_id, $custom_fields);
                }

                if (!empty($tags)) {
                    handle_tags_save($tags, $return_id, 'sam');
                }
                if (!empty($notifyUser)) {
                    foreach ($notifyUser as $v_user) {
                        if (!empty($v_user)) {
                            if ($v_user != $this->session->userdata('user_id')) {
                                add_notification(array(
                                    'to_user_id' => $v_user,
                                    'description' => 'sam',
                                    'icon' => 'clock-o',
                                    'link' => 'admin/sales_marketing/details/' . $return_id,
                                ));
                            }
                        }
                    }
                }
                if (!empty($notifyUser)) {
                    pusher_trigger_notification($notifyUser);
                }

                if (!empty($id)) {
                    $msg = _l('sam_information_update');
                    $activity = 'activity_sam_information_update';
                } else {
                    $msg = _l('sam_information_saved');
                    $activity = 'activity_sam_information_saved';
                }
                log_activity($activity . ' - ' . $data['title'] . ' [ID:' . $return_id . ']');

                $this->sam_model->log_deals_activity($return_id, 'not_sam_activity');
                // messages for user
                $type = "success";
            }
        }

        set_alert($type, $msg);
        redirect('admin/sales_marketing');
    }

    /**
     * { update customfield po }
     *
     * @param        $id     The identifier
     */
    public function update_customfield_po($id)
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $success = $this->purchase_model->update_customfield_po($id, $data);
            if ($success) {
                $message = _l('updated_successfully', _l('vendor_category'));
                set_alert('success', $message);
            }
            redirect(admin_url('purchase/purchase_order/' . $id));
        }
    }

    public function send_promotions_email($data)
    {
        $all_clients = get_sam_row('tbl_client', array('client_id' => $data['client_name']));
        $users_email = get_sam_row('tbl_users', array('user_id' => $data['user_id']));
        $deals = get_sam_row('tbl_sam', array('user_id' => $data['user_id']));
        $deals_email = config_item('deals_email');
        if (!empty($deals_email) && $deals_email == 1) {
            $email_template = email_templates(array('email_group' => 'deals_email'));
            $message = $email_template->template_body;
            $subject = $email_template->subject;
            $title = str_replace("{NAME}", $all_clients->name, $message);
            $designation = str_replace("{DEALS_TITLE}", $deals->title, $title);
            $message = str_replace("{SITE_NAME}", config_item('company_name'), $designation);
            $data['message'] = $message;
            $message = $this->load->view('email_template', $data, TRUE);
            $params['subject'] = $subject;
            $params['message'] = $message;
            $params['resourceed_file'] = '';
            $params['recipient'] = $users_email->email;
            $this->sam_model->send_email($params);
        }
        return true;
    }

    public function delete_sam($id = NULL)
    {

        $deleted = has_permission('sam', '', 'delete');

        if (!empty($deleted)) {
            $all_deals = $this->sam_model->check_by_deals(array('id' => $id), 'tbl_sam');
            if (empty($all_deals)) {
                $type = "error";
                $message = _l("no_record_found");
                set_alert($type, $message);
                redirect('admin/sales_marketing');
            }

            $all_comments = get_sam_result('tbl_sam_comments', array('deal_id' => $id));
            if (!empty($all_comments)) {
                foreach ($all_comments as $v_comments) {
                    $this->sam_model->remove_comment($v_comments->id);
                }
            }
            $all_attachments = get_sam_result(db_prefix() . 'files', array('rel_id' => $id, 'rel_type' => 'sam'));
            if (!empty($all_attachments)) {
                foreach ($all_attachments as $v_attachments) {
                    $this->sam_model->delete_deals_attachment($v_attachments->id);
                }
            }

            // check data is exist in tbl_sam_email
            $deal_email = get_sam_row('tbl_sam_email', array('deals_id' => $id));
            if (!empty($deal_email)) {
                $this->db->where('deals_id', $id);
                $this->db->delete('tbl_sam_email');
            }
            // check data is exist in tbl_sam_items
            $deal_items = get_sam_row('tbl_sam_items', array('deals_id' => $id));
            if (!empty($deal_items)) {
                $this->db->where('deals_id', $id);
                $this->db->delete('tbl_sam_items');
            }
            // check data is exist in tbl_sam_activity_log
            $deal_activity_log = get_sam_row('tbl_sam_activity_log', array('deal_id' => $id));


            if (!empty($deal_activity_log)) {
                $this->db->where('deal_id', $id);
                $this->db->delete('tbl_sam_activity_log');
            }

            // check data is exist in tbl_sam_mettings
            $deal_meeting = get_sam_row('tbl_sam_mettings', array('module_field_id' => $id, 'module' => 'sam'));

            if (!empty($deal_meeting)) {
                $this->db->where('module_field_id', $id);
                $this->db->delete('tbl_sam_mettings');

            }

            // check data is exist in tbl_sam_calls
            $deal_meeting = get_sam_row('tbl_sam_calls', array('module_field_id' => $id, 'module' => 'sam'));
            if (!empty($deal_meeting)) {
                $this->db->where('module_field_id', $id);
                $this->db->delete('tbl_sam_calls');
            }

            $this->db->where('fieldto', 'sam');
            $this->db->delete(db_prefix() . 'customfieldsvalues');


            $this->sam_model->_table_name = "tbl_sam";
            $this->sam_model->_primary_key = "id";
            $this->sam_model->delete_sam($id);;
            $type = "success";
            $message = _l('sam_information_delete');
            set_alert($type, $message);
            redirect('admin/sales_marketing');
        }
    }


    public function save_sorting_stages()
    {
        $ids = $this->input->post('page_id_array', TRUE);
        $arr = explode(',', $ids);
        for ($i = 1; $i <= count($arr); $i++) {
            $this->sam_model->_table_name = 'tbl_stage_name';
            $this->sam_model->_primary_key = 'stage_name_id';
            $cate_data['order'] = $i;
            $this->sam_model->save_sam($cate_data, $arr[$i - 1]);
        }
    }

    public function save_sorting_pipelines()
    {
        $ids = $this->input->post('page_id_array', TRUE);
        $arr = explode(',', $ids);
        for ($i = 1; $i <= count($arr); $i++) {
            $this->sam_model->_table_name = 'tbl_sam_pipelines';
            $this->sam_model->_primary_key = 'pipeline_id';
            $cate_data['order'] = $i;
            $this->sam_model->save_sam($cate_data, $arr[$i - 1]);
        }
    }

    public function saved_stages($id = null)
    {
        $this->sam_model->_table_name = 'tbl_sam_stages';
        $this->sam_model->_primary_key = 'stage_id';

        $cate_data['stage_name'] = $this->input->post('stage_name', TRUE);
        $cate_data['pipeline_id'] = $this->input->post('description', TRUE);

        // $cate_data['type'] = 'stages';
        $this->sam_model->save_sam($cate_data, $id);
        if (!empty($id)) {
            $msg = _l('successfully_stages_update');
            $activity = 'activity_successfully_updated_added';
        } else {
            $msg = _l('successfully_stages_added');
            $activity = 'activity_successfully_stages_added';
        }
        log_activity($activity . ' - ' . $cate_data['stage_name'] . ' [ID:' . $id . ']');
        // messages for user
        $type = "success";

        $message = $msg;
        set_alert($type, $message);
        redirect('admin/sales_marketing/stages');
    }

    public function delete_stages($id)
    {

        $this->sam_model->_table_name = 'tbl_sam_stages';
        $this->sam_model->_primary_key = 'stage_id	';
        $this->sam_model->delete_sam($id);

        $type = "success";
        $message = _l('stages_successfully_deleted');
        set_alert($type, $message);
        redirect('admin/sales_marketing/stages');
    }

  public function delete_status($id)
    {

        $this->sam_model->_table_name = 'tbl_sam_status';
        $this->sam_model->_primary_key = 'status_id	';
        $this->sam_model->delete_sam($id);

        $type = "success";
        $message = _l('status_successfully_deleted');
        set_alert($type, $message);
        redirect('admin/sales_marketing/status');
    }


    public function save_deals_notes($id)
    {

        $data = $this->sam_model->deals_array_from_post(array('notes'));

        //save data into table.
        $this->sam_model->_table_name = 'tbl_sam';
        $this->sam_model->_primary_key = 'id';
        $id = $this->sam_model->save_sam($data, $id);
        if($id){
            add_activity_transactions($id,'notes');    
        }
        // save into activities
        if (!empty($id)) {
            $msg = _l('update_sam_notes');
            $activity = 'activity_update_sam_notes';
        } else {
            $msg = _l('sam_notes_added');
            $activity = 'activity_update_sam_notes';
        }
        log_activity($activity . ' - ' . $data['notes'] . ' [ID:' . $id . ']');

        $this->sam_model->log_deals_activity($id, $activity, false, serialize(
            array(
                $data['notes'],
            )
        ));
        // messages for user
        $type = "success";
        set_alert($type, $msg);
        redirect('admin/sales_marketing/details/' . $id . '/' . 'notes');
    }

    public function saved_call($deals_id, $id = NULL)
{
    $data = $this->sam_model->deals_array_from_post(array('date', 'call_summary', 'client_id', 'user_id', 'call_type', 'outcome', 'duration1','duration2','duration3'));
    $data['module'] = 'sam';
    $data['module_field_id'] = $deals_id;
    
    $this->sam_model->_table_name = 'tbl_sam_calls';
    $this->sam_model->_primary_key = 'calls_id';
    
    $call_hour='00'; $call_min='00';$call_sec='00';
    if(isset($data['duration3']) && $data['duration3']!=""){
        $call_sec = $data['duration3'];
    }
    if(isset($data['duration2']) && $data['duration2']!=""){
        $call_min = $data['duration2'];
    }
    if(isset($data['duration1']) && $data['duration1']!=""){
        $call_hour = $data['duration1'];
    }
    unset($data['duration1']);
    unset($data['duration2']);
    unset($data['duration3']);
    $call_duration = $call_hour.':'.$call_min.':'.$call_sec;
    $data['duration'] = $call_duration;
    
    $return_id = $this->sam_model->save_sam($data, $id);
    
    // Add entry to tbl_sam_reminders for calls
    if($return_id) {
        $reminder_data = array(
            'date' => $data['date'],
            'staff' => $data['user_id'],
            'description' => $data['call_summary'],
            'title' => $data['call_summary'],
            'isnotified' => 0,
            'rel_id' => $deals_id,
            'rel_type' => 'sam',
            'notify_by_email' => 0,
            'creator' => $data['user_id'],
            'ismeeting' => 2 // Set to 2 for Calls as per your requirement
        );
        
        $this->sam_model->_table_name = 'tbl_sam_reminders';
        $this->sam_model->_primary_key = 'reminder_id';
        $this->sam_model->save_sam($reminder_data);
        
        add_activity_transactions($deals_id,'call');    
    }
    
    if (!empty($id)) {
        $id = $id;
        $activity = 'activity_update_sam_call';
        $msg = _l('update_sam_call');
    } else {
        $id = $return_id;
        $activity = 'not_sam_activity_call';
        $msg = _l('save_sam_call');
    }

    $this->sam_model->log_deals_activity($deals_id, $activity, false, serialize([
        $data['call_summary'],
    ]));

    log_activity($activity . ' - ' . $data['date'] . ' [ID:' . $id . ']');
    
    // messages for user
    $deals_info = $this->sam_model->check_by_deals(array('id' => $deals_id), 'tbl_sam');
    $notifiedUsers = array();
    if (!empty($deals_info->permission) && $deals_info->permission != 'all') {
        $permissionUsers = json_decode($deals_info->permission);
        foreach ($permissionUsers as $user => $v_permission) {
            array_push($notifiedUsers, $user);
        }
    } else {
        // $notifiedUsers = $this->sam_model->allowed_user_id('55');
    }
    if (!empty($notifiedUsers)) {
        foreach ($notifiedUsers as $users) {
            if ($users != $this->session->userdata('user_id')) {
                add_notification(array(
                    'to_user_id' => $users,
                    'from_user_id' => true,
                    'description' => 'not_add_call',
                    'link' => 'admin/sales_marketing/details/' . $deals_info->id . '/call',
                    'value' => _l('lead') . ' ' . $deals_info->title,
                ));
            }
        }
        pusher_trigger_notification($notifiedUsers);
    }
    
    $type = "success";
    $message = $msg;
    set_alert($type, $message);
    redirect('admin/sales_marketing/details/' . $deals_id . '/' . 'call');
}

    public function delete_items($id, $deals_id)
    {
        $all_items = $this->sam_model->check_by_deals(array('items_id' => $id), 'tbl_sam_items');
        if (empty($all_items)) {
            $data['type'] = 'error';
            $data['msg'] = _l('no_record_found');
        } else {

            $this->sam_model->_table_name = "tbl_sam_items";
            $this->sam_model->_primary_key = "items_id";
            $this->sam_model->delete_sam($id);
            $_data['id'] = $deals_id;
            $data['subview'] = $this->load->view('sales_marketing/deals_details/dealItems', $_data, true);
            $data['type'] = 'success';
            $data['msg'] = _l('sam_items_delete');
        }

        // set message
        set_alert($data['type'], $data['msg']);
        redirect('admin/sales_marketing/details/' . $deals_id . '/products');
    }

    public
    function delete_deals_email($deals_id, $id)
    {
        $email_info = $this->sam_model->check_by_deals(array('id' => $id), 'tbl_sam_email');
        if (empty($email_info)) {
            $data['type'] = 'error';
            $data['msg'] = _l('no_record_found');
        } else {
            $this->sam_model->_table_name = 'tbl_sam_email';
            $this->sam_model->_primary_key = 'id';
            $this->sam_model->delete_sam($id);
            $data['type'] = 'success';
            $data['msg'] = _l('sam_email_deleted');
        }
        set_alert($data['type'], $data['msg']);
        redirect('admin/sales_marketing/details/' . $deals_id . '/email');
    }

    public
    function delete_deals_call($id, $calls_id)
    {
        $calls_info = $this->sam_model->check_by_deals(array('calls_id' => $calls_id), 'tbl_sam_calls');
        if (empty($calls_info)) {
            $data['type'] = 'error';
            $data['msg'] = _l('no_record_found');
        } else {
            $this->sam_model->_table_name = 'tbl_sam_calls';
            $this->sam_model->_primary_key = 'calls_id';
            $success = $this->sam_model->delete_sam($calls_id);
            $data['type'] = 'success';
            $data['msg'] = _l('sam_call_deleted');
        }
        set_alert($data['type'], $data['msg']);
        redirect('admin/sales_marketing/details/' . $id . '/call');
    }

    public function delete_deals_mettsam_modelings($id, $mettings_id)
    {
        $mettings_info = $this->sam_model->check_by_deals(array('mettings_id' => $mettings_id), 'tbl_sam_mettings');
        if (empty($mettings_info)) {
            $data['type'] = 'error';
            $data['msg'] = _l('no_record_found');
        } else {
            $this->sam_model->_table_name = 'tbl_sam_mettings';
            $this->sam_model->_primary_key = 'mettings_id';
            $this->sam_model->delete_sam($mettings_id);
            $data['type'] = 'success';
            $data['msg'] = _l('mettings_deleted');
        }
        set_alert($data['type'], $data['msg']);
        redirect('admin/sales_marketing/details/' . $id . '/mettings');
    }


    public function meeting_details($mettings_id = null)
    {
        $data['title'] = _l('meeting_details');
        $data['details'] = get_sam_row('tbl_sam_mettings', array('mettings_id' => $mettings_id));
        $data['subview'] = $this->load->view('sales_marketing/meeting_details', $data, FALSE);
        $this->load->view('sales_marketing/_layout_modal', $data);
    }

    public function saved_metting($id = NULL) {
    $this->sam_model->_table_name = 'tbl_sam_mettings';
    $this->sam_model->_primary_key = 'mettings_id';
    $deals_id = $this->input->post('deals_id', true);
    $data = $this->sam_model->deals_array_from_post(array('meeting_subject', 'user_id', 'location', 'description'));
    
    $data['module'] = 'sam';
    $data['module_field_id'] = $deals_id;
    $data['start_date'] = $this->input->post('start_date', true);
    $data['end_date'] = $this->input->post('end_date', true);
    $data['format'] = $this->input->post('format');
    
    $user_id = serialize($this->sam_model->deals_array_from_post(array('attendees')));
    if (!empty($user_id)) {
        $data['attendees'] = $user_id;
    } else {
        $data['attendees'] = '-';
    }

    $return_id = $this->sam_model->save_sam($data, $id);
    
    // Add entry to tbl_sam_reminders
    if($return_id) {
        $reminder_data = array(
            'date' => $data['start_date'],
            'staff' => $data['user_id'],
            'description' => $data['description'],
            'title' => $data['meeting_subject'],
            'isnotified' => 0,
            'ismeeting' => 1,
            'rel_id' => $data['module_field_id'],
            'rel_type' => $data['module'],
            'notify_by_email' => 0,
            'creator' => $data['user_id']
        );
        
        $this->sam_model->_table_name = 'tbl_sam_reminders';
        $this->sam_model->_primary_key = 'reminder_id';
        $this->sam_model->save_sam($reminder_data);
        
        add_activity_transactions($deals_id,'meetings');
    }
    
    if (!empty($id)) {
        $id = $id;
        $activity = 'sam_activity_metting_updated';
        $msg = _l('update_sam_metting');
    } else {
        $activity = 'not_sam_activity_metting';
        $msg = _l('save_sam_metting');
    }
    
    $this->sam_model->log_deals_activity($return_id, $activity, false, serialize(
        array(
            $data['meeting_subject'],
        )
    ));

    log_activity($activity . ' - ' . $data['meeting_subject'] . ' [ID:' . $id . ']');
    
    $deals_info = $this->sam_model->check_by_deals(array('id' => $data['deals_id']), 'tbl_sam');
    $notifiedUsers = array();
    if (!empty($deals_info->permission) && $deals_info->permission != 'all') {
        $permissionUsers = json_decode($deals_info->permission);
        foreach ($permissionUsers as $user => $v_permission) {
            array_push($notifiedUsers, $user);
        }
    }

    $type = "success";
    set_alert($type, $msg);
    redirect('admin/sales_marketing/details/' . $deals_id . '/mettings');
}


    public function save_task($id = null)
    {

        $created = has_permission('sam', '', 'create');
        $edited = has_permission('sam', '', 'edit');
        if (!empty($created) || !empty($edited) && !empty($id)) {
            $data = $this->tasks_model->deals_array_from_post(array(
                'module',
                'module_field_id',
                'task_name',
                'stage_id',
                'task_description',
                'task_start_date',
                'due_date',
                'module_field_id',
                'task_progress',
                'calculate_progress',
                'client_visible',
                'task_status',
                'hourly_rate',
                'tags',
                'billable'
            ));
            $estimate_hours = $this->input->post('task_hour', true);
            $check_flot = explode('.', $estimate_hours);
            if (!empty($check_flot[0])) {
                if (!empty($check_flot[1])) {
                    $data['task_hour'] = $check_flot[0] . ':' . $check_flot[1];
                } else {
                    $data['task_hour'] = $check_flot[0] . ':00';
                }
            } else {
                $data['task_hour'] = '0:00';
            }


            if ($data['task_status'] == 'completed') {
                $data['task_progress'] = 100;
            }
            if ($data['task_progress'] == 100) {
                $data['task_status'] = 'completed';
            }
            if (empty($id)) {
                $data['created_by'] = $this->session->userdata('user_id');
            }
            if (empty($data['billable'])) {
                $data['billable'] = 'No';
            }
            if (empty($data['hourly_rate'])) {
                $data['hourly_rate'] = '0';
            }
            $result = 0;

            $data['project_id'] = null;
            $data['milestones_id'] = null;
            $data['goal_tracking_id'] = null;
            $data['bug_id'] = null;
            $data['leads_id'] = null;
            $data['sub_task_id'] = null;
            $data['transactions_id'] = null;


            $permission = $this->input->post('permission', true);
            if (!empty($permission)) {
                if ($permission == 'everyone') {
                    $assigned = 'all';
                    $assigned_to['assigned_to'] = $this->tasks_model->allowed_user_id('54');
                } else {
                    $assigned_to = $this->tasks_model->deals_array_from_post(array('assigned_to'));
                    if (!empty($assigned_to['assigned_to'])) {
                        foreach ($assigned_to['assigned_to'] as $assign_user) {
                            $assigned[$assign_user] = $this->input->post('action_' . $assign_user, true);
                        }
                    }
                }
                if (!empty($assigned)) {
                    if ($assigned != 'all') {
                        $assigned = json_encode($assigned);
                    }
                } else {
                    $assigned = 'all';
                }
                $data['permission'] = $assigned;
            } else {
                set_alert('error', _l('assigned_to') . ' Field is required');
                if (empty($_SERVER['HTTP_REFERER'])) {
                    redirect('admin/tasks/all_task');
                } else {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }

            //save data into table.
            $this->tasks_model->_table_name = "tbl_task"; // table name
            $this->tasks_model->_primary_key = "task_id"; // $id
            $id = $this->tasks_model->save_deals($data, $id);

            $this->tasks_model->set_task_progress($id);

            $u_data['index_no'] = $id;
            $id = $this->tasks_model->save_deals($u_data, $id);
            $u_data['index_no'] = $id;
            $id = $this->tasks_model->save_deals($u_data, $id);
            save_custom_field(3, $id);

            if ($assigned == 'all') {
                $assigned_to['assigned_to'] = $this->tasks_model->allowed_user_id('54');
            }

            if (!empty($id)) {
                $msg = _l('update_task');
                $activity = 'activity_update_task';
                $id = $id;
                if (!empty($assigned_to['assigned_to'])) {
                    // send update
                    $this->notify_assigned_tasks($assigned_to['assigned_to'], $id, true);
                }
            } else {
                $msg = _l('save_task');
                $activity = 'activity_new_task';
                if (!empty($assigned_to['assigned_to'])) {
                    $this->notify_assigned_tasks($assigned_to['assigned_to'], $id);
                }
            }

            $url = 'admin/' . $data['module'] . '/tasks/' . $id;
            // save into activities

            log_activity($activity . ' - ' . $data['task_name'] . ' [ID:' . $id . ']');
            // messages for use

            if (!empty($data['project_id'])) {
                $this->tasks_model->set_progress($data['project_id']);
            }

            $type = "success";
            $message = $msg;
            set_alert($type, $message);
            redirect('admin/tasks/details/' . $id);
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function new_category()
    {
        $data['title'] = _l('new') . ' ' . _l('categories');
        $data['type'] = 'sam';
        $data['subview'] = $this->load->view('sales_marketing/new_category', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function update_category($id = null)
    {
        $this->sam_model->_table_name = 'tbl_stage_name';
        $this->sam_model->_primary_key = 'stage_name_id';

        $cate_data['stage_name'] = $this->input->post('stage_name', TRUE);
        $cate_data['description'] = $this->input->post('description', TRUE);
        $type = $this->input->post('type', TRUE);
        if (!empty($type)) {
            $cate_data['type'] = $type;
        } else {
            $cate_data['type'] = 'client';
        }
        // update root category
        $where = array('type' => $cate_data['type'], 'stage_name' => $cate_data['stage_name']);
        // duplicate value check in DB
        if (!empty($id)) { // if id exist in db update data
            $stage_name_id = array('stage_name_id !=' => $id);
        } else { // if id is not exist then set id as null
            $stage_name_id = null;
        }
        // check whether this input data already exist or not
        $check_category = $this->sam_model->check_deals_update('tbl_stage_name', $where, $stage_name_id);
        if (!empty($check_category)) { // if input data already exist show error alert
            // massage for user
            $type = 'error';
            $msg = "<strong style='color:#000'>" . $cate_data['stage_name'] . '</strong>  ' . _l('already_exist');
        } else { // save and update query
            $id = $this->sam_model->save_sam($cate_data, $id);

            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'settings',
                'module_field_id' => $id,
                'activity' => ('stage_name_added'),
                'value1' => $cate_data['stage_name']
            );
            $this->sam_model->_table_name = 'tbl_activities';
            $this->sam_model->_primary_key = 'activities_id';
            $this->sam_model->save_sam($activity);

            // messages for user
            $type = "success";
            $msg = _l('category_added');
        }
        if (!empty($id)) {
            $result = array(
                'id' => $id,
                'group' => $cate_data['stage_name'],
                'status' => $type,
                'message' => $msg,
            );
        } else {
            $result = array(
                'status' => $type,
                'message' => $msg,
            );
        }
        echo json_encode($result);
        exit();
    }

    public function details($id, $active = NULL, $edit = NULL)
    {
        $data['title'] = _l('sam_details'); //Page title
        $data['deals_details'] = $this->sam_model->dealInfo($id); 
        // echo "<pre>"; print_r($data['deals_details']); exit;
        $data['dropzone'] = true;
        //get all task information
        if (empty($active)) {
            $data['active'] = 'details';
        } else {
            $data['active'] = $active;
        }
       
        $data['all_tabs'] = sam_details_tabs($id); 
        //  echo "<pre>"; print_r($data['all_tabs']); exit;

        $data['activity_log'] = $this->sam_model->get_lead_activity_log($id); 
        $data['activity_log2'] = $this->sam_model->get_lead_activity_log2($id); 
        $data['module'] = 'sam';
        $data['id'] = $id;
        
        if($active=='proposals'){
            $this->load->model('sam_proposals_model');            
        }
        
        $data['staff'] = $this->staff_model->get('', ['active' => 1]);
        $data['global'] = $this->load->view('sales_marketing/deals_details/global', $data, TRUE); 
        $data['subview'] = $this->load->view('deals_details/tab_view', $data, TRUE);
        // echo "<pre>"; print_r($data['subview']); exit;

        $this->load->view('sales_marketing/_layout_main', $data);
    }
    
       public function update_deal_status() {
        $deal_id = $this->input->post('deal_id');
        $status_id = $this->input->post('status_id');
        $update = $this->sam_model->update_deal_status($deal_id, $status_id);
        if ($update) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function add_deals_assignees($dealid)
    {
        $assignees = $this->input->post('assignee');

        $deal_info = get_sam_row('tbl_sam', array('id' => $dealid));
        if (!empty($deal_info)) {
            if (staff_can('edit', 'deals') ||
                ($deal_info->current_user_is_creator && staff_can('create', 'deals'))) {
                $this->sam_model->_table_name = 'tbl_sam';
                $this->sam_model->_primary_key = 'id';

                $data = array(
                    'user_id' => json_encode($assignees),
                );

                //

                $success = $this->sam_model->save_sam($data, $dealid);
                $message = '';
                if ($success) {
                    $message = _l('sam_assignee_added');
                }
                $this->sam_model->log_deals_activity($dealid, 'not_sam_assignee', false, serialize([
                    implode(', ', array_map(function ($assignee) {
                        return get_staff_full_name($assignee);
                    }, $assignees)),
                ]));
                set_alert('success', $message);
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function remove_assignee($id, $deal_id)
    {
        $deal_info = get_sam_row('tbl_sam', array('id' => $deal_id));
        if (!empty($deal_info)) {
            if (staff_can('edit', 'deals') ||
                ($deal_info->current_user_is_creator && staff_can('create', 'deals'))) {
                $permission = json_decode($deal_info->user_id);
                if (!empty($permission)) {
                    // remove $id from array
                    if (($key = array_search($id, $permission)) !== false) {
                        unset($permission[$key]);
                    }
                    $this->sam_model->_table_name = 'tbl_sam';
                    $this->sam_model->_primary_key = 'id';
                    // reset array index
                    $permission = array_values($permission);
                    if (!empty($permission)) {
                        $data = array(
                            'user_id' => json_encode($permission),
                        );
                    } else {
                        $data = array(
                            'user_id' => null,
                        );
                    }
                    $success = $this->sam_model->save_sam($data, $deal_id);
                    $message = '';
                    if ($success) {
                        $message = _l('sam_assignee_removed');
                    }
                    $this->sam_model->log_deals_activity($deal_id, 'not_sam_assignee_removed', false, serialize([
                        get_staff_full_name($id),
                    ]));
                    echo json_encode([
                        'success' => $success,
                        'message' => $message,
                    ]);
                }
            }

        }
    }

    public function change_deal_owner($staffId, $dealId)
    {
        $deal_info = get_sam_row('tbl_sam', array('id' => $dealId));
        if (!empty($deal_info)) {
            if (staff_can('edit', 'deals') ||
                ($deal_info->current_user_is_creator && staff_can('create', 'deals'))) {
                $this->sam_model->_table_name = 'tbl_sam';
                $this->sam_model->_primary_key = 'id';

                $data = array(
                    'default_deal_owner' => $staffId,
                );

                $success = $this->sam_model->save_sam($data, $dealId);
                $message = '';
                if ($success) {
                    $message = _l('deal_owner_changed');
                }
                $this->sam_model->log_deals_activity($dealId, 'not_sam_owner_changed', false, serialize([
                    get_staff_full_name($staffId),
                ]));

                echo json_encode([
                    'success' => true,
                    'message' => $message,
                ]);
            }
        }
    }

    public
    function new_attachment($module, $id)
    {
        $data['title'] = lang('new_attachment');
        $data['dropzone'] = true;
        $data['module'] = $module;
        $data['module_field_id'] = $id;
        $data['subview'] = $this->load->view('sales_marketing/deals_details/new_attachment', $data, FALSE);
        $this->load->view('sales_marketing/_layout_modal', $data);
    }

    function validate_project_file()
    {
        return validate_post_file($this->input->post("file_name", true));
    }

    function upload_file()
    {
        upload_file_to_temp();
    }

    public function download_all_attachment($type, $id)
    {

        $attachment_info = get_sam_result('tbl_attachments', array('module' => $type, 'module_field_id' => $id));
        $FileName = $type . '_attachment';
        $this->load->library('zip');
        if (!empty($attachment_info) && !empty($FileName)) {
            foreach ($attachment_info as $v_attach) {
                $uploaded_files_info = $this->db->where('attachments_id', $v_attach->attachments_id)->get('tbl_attachments_files')->result();
                $filename = slug_it($FileName);
                foreach ($uploaded_files_info as $v_files) {
                    $down_data = ($v_files->files); // Read the file's contents
                    $this->zip->read_file($down_data);
                }
                $this->zip->download($filename . '.zip');
            }
        } else {
            $type = "error";
            $message = lang('operation_failed');
            // set_message($type, $message);
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/dashboard');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }


    public function download_files($deal_id, $comment_id = null)
    {
        $taskWhere = 'external IS NULL';
        if ($comment_id) {
            $taskWhere .= ' AND deal_comment_id=' . $this->db->escape_str($comment_id);
        }

        $files = $this->sam_model->get_deal_attachments($deal_id, $taskWhere);

        if (count($files) == 0) {
            redirect($_SERVER['HTTP_REFERER']);
        }

        $path = get_upload_path_for_sam() . $deal_id;

        $this->load->library('zip');

        foreach ($files as $file) {
            $this->zip->read_file($path . '/' . $file['file_name']);
        }

        $this->zip->download('files.zip');
        $this->zip->clear_data();
    }


    /* Remove task comment / ajax */
    public function remove_comment($id)
    {
        echo json_encode([
            'success' => $this->sam_model->remove_comment($id),
        ]);
    }

    public function downloadd_files($uploaded_files_id, $comments = null)
    {
        $this->load->helper('download');
        if (!empty($comments)) {
            if ($uploaded_files_id) {
                $down_data = file_get_contents('uploads/' . $uploaded_files_id); // Read the file's contents
                if (!empty($down_data)) {
                    force_download($uploaded_files_id, $down_data);
                } else {
                    $type = "error";
                    $message = lang('operation_failed');
                    set_message($type, $message);
                    if (empty($_SERVER['HTTP_REFERER'])) {
                        redirect('admin/dashboard');
                    } else {
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                }
            } else {
                if (empty($_SERVER['HTTP_REFERER'])) {
                    redirect('admin/dashboard');
                } else {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }
        } else {
            $uploaded_files_info = $this->sam_model->check_by(array('uploaded_files_id' => $uploaded_files_id), 'tbl_attachments_files');
            if ($uploaded_files_info->uploaded_path) {
                $data = file_get_contents($uploaded_files_info->uploaded_path); // Read the file's contents
                force_download($uploaded_files_info->file_name, $data);
            } else {
                if (empty($_SERVER['HTTP_REFERER'])) {
                    redirect('admin/dashboard');
                } else {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }
        }
    }

    public function changeStatus($id, $status)
    {
        $data['title'] = 'Change Status';
        $data['id'] = $id;
        $data['btn'] = $status;
        $data['status'] = $status;
        $data['deals_details'] = $this->sam_model->dealInfo($id);
        $data['subview'] = $this->load->view('sales_marketing/deals_details/_modal_change_status', $data, FALSE);
        $this->load->view('sales_marketing/_layout_modal', $data);

    }

    public function changedStatus($id, $status)
    {

        $pdata['status'] = $status;
        if ($status == 'won') {
            $pdata['convert_to_project'] = $this->input->post('convert_to_project', true);
            $create_invoice = $this->input->post('create_invoice', true);

            if ($create_invoice === 'on') {
                $this->createInvoice($id);
            }
        } else if ($status == 'lost') {
            $pdata['lost_reason'] = $this->input->post('lost_reason');
        }
        $this->sam_model->_table_name = 'tbl_sam';
        $this->sam_model->_primary_key = 'id';
        $this->sam_model->save_sam($pdata, $id);

        $this->sam_model->log_deals_activity($id, 'not_sam_status_change', false, serialize([
            $status,
        ]));

        $type = "success";
        $message = _l('sam_status_change');
        set_alert($type, $message);
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function createInvoice($deal_id)
    {
        $deal_info = $this->sam_model->check_by_deals(array('id' => $deal_id), 'tbl_sam');

        $next_invoice_number = get_option('next_invoice_number');
        $format = get_option('invoice_number_format');
        $prefix = get_option('invoice_prefix');
        $__number = $next_invoice_number;

        $all_client = json_decode($deal_info->client_id, true);
        $sales_info = get_sam_result('tbl_sam_items', array('deals_id' => $deal_id));


        $new_items = array();
        $sub_total = 0;
        $item_tax_total = 0;
        if ($deal_info->deal_value > 0) {
            $sub_total += $deal_info->deal_value;
            $new_items[] = array(
                'description' => $deal_info->title,
                'long_description' => $deal_info->notes,
                'qty' => 1,
                'unit' => '',
                'rate' => $deal_info->deal_value,
                'taxname' => [],
                'order' => 1
            );
        }


        if (!empty($sales_info)) {
            foreach ($sales_info as $or => $item) {
                $sub_total += $item->unit_cost * $item->quantity;
                $item_tax_total += $item->item_tax_total;
                $new_items[] = array(
                    'description' => $item->item_name,
                    'long_description' => $item->item_desc,
                    'qty' => $item->quantity,
                    'unit' => $item->unit,
                    'rate' => $item->unit_cost,
                    'taxname' => (!empty($item->item_tax_name) ? json_decode($item->item_tax_name) : []),
                    'order' => $or + 1
                );
            }
        }


        if (get_option('invoice_due_after') != 0) {
            $duedate = (date('Y-m-d', strtotime('+' . get_option('invoice_due_after') . ' DAY', strtotime(date('Y-m-d')))));
        } else {
            $duedate = (date('Y-m-d'));
        }
        $this->load->model('payment_modes_model');
        $this->load->model('currencies_model');
        $payment_modes = $this->payment_modes_model->get('', [
            'expenses_only !=' => 1,
        ]);

        $allowed_payment_modes = [];
        foreach ($payment_modes as $payment_mode) {
            $allowed_payment_modes[] = $payment_mode['id'];
        }
        $currency = $this->currencies_model->get_base_currency()->id;


        if (!empty($all_client)) {
            foreach ($all_client as $client) {
                $clientInfo = $this->clients_model->get($client);
                $__number = $__number + 1;
                $_invoice_number = str_pad($__number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                $new_invoice = array(
                    'clientid' => $client,
                    'billing_street' => $clientInfo->billing_street,
                    'billing_city' => $clientInfo->billing_city,
                    'billing_state' => $clientInfo->billing_state,
                    'billing_zip' => $clientInfo->billing_zip,
                    'shipping_street' => $clientInfo->shipping_street,
                    'shipping_city' => $clientInfo->shipping_city,
                    'shipping_state' => $clientInfo->shipping_state,
                    'shipping_zip' => $clientInfo->shipping_zip,
                    'number' => $_invoice_number,
                    'date' => date('Y-m-d'),
                    'duedate' => $duedate,
                    'allowed_payment_modes' => $allowed_payment_modes,
                    'currency' => (!empty($clientInfo->default_currency) ? $clientInfo->default_currency : $currency),
                    'sale_agent' => $deal_info->default_deal_owner,
                    'recurring' => 0,
                    'show_quantity_as' => 1,
                    'description' => $deal_info->notes ?: '',
                    'quantity' => 1,
                    'unit' => '',
                    'discount_type' => '',
                    'repeat_every_custom' => 1,
                    'repeat_type_custom' => 'day',
                    'rate' => '',
                    'newitems' => $new_items,
                    'subtotal' => $sub_total,
                    'discount_percent' => 0,
                    'discount_total' => 0.00,
                    'adjustment' => 0,
                    'total' => $sub_total + $item_tax_total,
                    'task_id' => '',
                    'project_id' => '',
                    'expense_id' => '',
                    'clientnote' => '',
                    'terms' => '',
                );

                $this->load->model('invoices_model');
                $invoice_id = $this->invoices_model->add($new_invoice);

                $this->sam_model->_table_name = 'tbl_sam';
                $this->sam_model->_primary_key = 'id';
                $this->sam_model->save_sam(array('invoice_id' => $invoice_id), $deal_id);
            }
        }
        return true;
    }

    public function createProjects($deal_id)
    {
        $deal_info = $this->sam_model->check_by_deals(array('id' => $deal_id), 'tbl_sam');

        $projects = '';
        if (empty(config_item('projects_number_format'))) {
            $projects .= config_item('projects_prefix');
        }
        $projects .= $this->items_model->generate_projects_number();

        $propability = 0;

        $all_stages = get_order_by('tbl_stage_name', array('type' => 'stages', 'description' => $deal_info->pipeline), 'order', true);
        // total stages
        if (!empty($all_stages)) {
            $total_stages = count($all_stages);
            foreach ($all_stages as $stage) {
                $res = round(100 / $total_stages);
                $propability += $res;
                if ($stage->stage_name_id == $deal_info->stage_id) {
                    break;
                }
            }
        }
        if ($deal_info->status === 'won') {
            $propability = 100;
        }
        if ($deal_info->status === 'lost') {
            $propability = 0;
        }

        $permission = array();
        $all_user = json_decode($deal_info->user_id, true);
        if (!empty($all_user)) {
            foreach ($all_user as $user) {
                $permission[$user] = array('view', 'edit', 'delete');
            }
        }
        $permission = json_encode($permission);
        $all_client = json_decode($deal_info->client_id, true);

        if (!empty($all_client)) {
            foreach ($all_client as $client) {
                $new_project = array(
                    'project_no' => $projects,
                    'project_name' => $deal_info->title,
                    'client_id' => $client,
                    'progress' => $propability,
                    'calculate_progress' => '',
                    'start_date' => date('Y-m-d'),
                    'end_date' => $deal_info->days_to_close,
                    'billing_type' => 'fixed_rate',
                    'project_cost' => $deal_info->deal_value,
                    'hourly_rate' => 0,
                    'project_status' => 'started',
                    'estimate_hours' => 0,
                    'description' => $deal_info->notes,
                    'permission' => $permission,
                );
                $this->sam_model->_table_name = "tbl_project"; //table name
                $this->sam_model->_primary_key = "project_id";
                $new_project_id = $this->sam_model->save_sam($new_project);

                $tasks = $this->input->post('tasks', true);
                if (!empty($tasks)) {
                    //get tasks info by id
                    foreach ($tasks as $task_id) {
                        $task_info = get_sam_row('tbl_task', array('task_id' => $task_id));
                        $task = array(
                            'task_name' => $task_info->task_name,
                            'project_id' => $new_project_id,
                            'milestones_id' => $task_info->milestones_id,
                            'permission' => $task_info->permission,
                            'task_description' => $task_info->task_description,
                            'task_start_date' => $task_info->task_start_date,
                            'due_date' => $task_info->due_date,
                            'task_created_date' => $task_info->task_created_date,
                            'task_status' => $task_info->task_status,
                            'task_progress' => $task_info->task_progress,
                            'task_hour' => $task_info->task_hour,
                            'tasks_notes' => $task_info->tasks_notes,
                            'timer_status' => $task_info->timer_status,
                            'client_visible' => $task_info->client_visible,
                            'timer_started_by' => $task_info->timer_started_by,
                            'start_time' => $task_info->start_time,
                            'logged_time' => $task_info->logged_time,
                            'created_by' => $task_info->created_by
                        );
                        $this->sam_model->_table_name = "tbl_task"; //table name
                        $this->sam_model->_primary_key = "task_id";
                        $this->sam_model->save_sam($task);
                    }
                }

                $projects_email = config_item('projects_email');
                if (!empty($projects_email) && $projects_email == 1) {
                    $this->send_project_notify_client($new_project_id);
                    if (!empty($all_user)) {
                        $this->send_project_notify_assign_user($new_project_id, $all_user);
                    }
                }
            }
        }
    }


    public
    function changeStage($deals_id, $stage_id)
    {
        $data['stage_id'] = $stage_id;
        $this->sam_model->_table_name = 'tbl_sam';
        $this->sam_model->_primary_key = 'id';
        $this->sam_model->save_sam($data, $deals_id);
        $type = "success";
        $message = _l('sam_updated');

        $this->sam_model->log_deals_activity($deals_id, 'not_sam_stage_change', false, serialize([
            $stage_id,
        ]));
        set_alert($type, $message);
        redirect($_SERVER['HTTP_REFERER']);
    }

    public
    function itemsSuggestions($id = null)
    {
        $term = $this->input->get('term', TRUE);
        $rows = $this->sam_model->getItemsInfo($term);
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $row->qty = 1;
                $row->rate = $row->rate;
                $row->unit = $row->unit;
                $row->item_id = $row->id;
                $tax = 0;
                $result = (object)array_merge((array)$row, (array)$tax);
                $pr[] = array('item_id' => $row->id, 'label' => $row->description, 'row' => $result);
            }
            echo json_encode($pr);
            die();
        } else {
            echo json_encode(array(array('item_id' => 0, 'label' => _l('no_match_found'), 'value' => $term)));
            die();
        }
    }


    public
    function itemAddedManualy()
    {
        $items_info = (object)$this->input->post();

        $deals_id = $this->input->post('deals_id', true);
        $items_id = $this->input->post('items_id', true);

        if (!empty($items_info)) {
            $saved_items_id = 0;
            $items_info->saved_items_id = $saved_items_id;
            $items_info->code = '';
            $items_info->new_item_id = $saved_items_id;
            $tax_info = $items_info->tax_rates_id;
            $total_cost = $items_info->unit_cost * $items_info->quantity;
            if (!empty($tax_info)) {
                foreach ($tax_info as $v_tax) {
                    $all_tax = $this->db->where('id', $v_tax)->get(db_prefix() . 'taxes')->row();
                    $tax_name[] = $all_tax->name . '|' . $all_tax->taxrate;
                    $item_tax_total[] = ($total_cost / 100 * $all_tax->taxrate);
                }
            }

            $item_tax_total = (!empty($item_tax_total) ? array_sum($item_tax_total) : 0);

            $data['tax_rates_id'] = (!empty($items_info->tax_rates_id) ? json_encode($items_info->tax_rates_id) : '');
            $data['quantity'] = $items_info->quantity;
            $data['deals_id'] = $deals_id;
            $data['item_name'] = $items_info->item_name;
            $data['item_desc'] = $items_info->item_desc;
            $data['hsn_code'] = (!empty($items_info->hsn_code) ? $items_info->hsn_code : '');
            $data['unit_cost'] = $items_info->unit_cost;
            $data['unit'] = $items_info->unit;
            $data['item_tax_rate'] = '0.00';
            $data['item_tax_name'] = (!empty($tax_name) ? json_encode($tax_name) : '');
            $data['item_tax_total'] = (!empty($item_tax_total) ? $item_tax_total : '0.00');
            $data['total_cost'] = $total_cost;
            $data['item_id'] = $items_info->saved_items_id;

            $this->sam_model->_table_name = 'tbl_sam_items';
            $this->sam_model->_primary_key = 'items_id';
            $items_id = $this->sam_model->save_sam($data, $items_id);
            $msg = _l('deals_item_added');
            $activity = 'activity_sam_items_added';
            log_activity($activity . ' - ' . $data['item_name']);
            // messages for user
            $type = "success";

            $_data['id'] = $deals_id;
            $data['subview'] = $this->load->view('sales_marketing/deals_details/dealItems', $_data, true);
        } else {
            $type = "error";
            $msg = 'please Select an items';
        }
        set_alert($type, $msg);
        redirect('admin/sales_marketing/details/' . $deals_id . '/products');
    }

    public
    function add_insert_items($deals_id)
    {
        $edited = has_permission('sam', '', 'edit');
        if (!empty($edited)) {
            $v_items_id = $this->input->post('item_id', TRUE) ?? 3;
            if (!empty($v_items_id)) {
                $where = array('deals_id' => $deals_id, 'item_id' => $v_items_id);
                $items_info = $this->sam_model->check_by_deals(array('id' => $v_items_id), db_prefix() . 'items');

                // check whether this input data already exist or not
                $check_users = get_sam_row('tbl_sam_items', $where);
                if (!empty($check_users)) { // if input data already exist show error alert
                    // massage for user
                    $cdata['quantity'] = $check_users->quantity + 1;
                    $cdata['total_cost'] = $items_info->rate + $check_users->total_cost;

                    $this->sam_model->_table_name = 'tbl_sam_items';
                    $this->sam_model->_primary_key = 'items_id';
                    $items_id = $this->sam_model->save_sam($cdata, $check_users->items_id);
                } else {
                    $tax_name = array();
                    $total_tax = array();
                    $tax_id = array();


                    if (!empty($items_info->tax)) {
                        $tax_info = $this->db->where('id', $items_info->tax)->get(db_prefix() . 'taxes')->row();
                        $tax_name[] = $tax_info->name . '|' . $tax_info->taxrate;
                        $tax_id[] = $tax_info->id;
                        $total_tax[] = ($items_info->rate / 100 * $tax_info->taxrate);
                    } else {
                        $tax_info = '';
                    }
                    // tax2
                    if (!empty($items_info->tax2)) {
                        $tax_info = $this->db->where('id', $items_info->tax2)->get(db_prefix() . 'taxes')->row();
                        $tax_name[] = $tax_info->name . '|' . $tax_info->taxrate;
                        $tax_id[] = $tax_info->id;
                        $total_tax[] = ($items_info->rate / 100 * $tax_info->taxrate);
                    } else {
                        $tax_info = '';
                    }
                    $item_tax_total = (!empty($total_tax) ? array_sum($total_tax) : 0);


                    $data['quantity'] = 1;
                    $data['deals_id'] = $deals_id;
                    $data['tax_rates_id'] = (!empty($tax_id) ? json_encode($tax_id) : '');
                    $data['item_name'] = $items_info->description;
                    $data['item_desc'] = $items_info->long_description;
                    $data['unit_cost'] = $items_info->rate;
                    $data['unit'] = $items_info->unit;
                    $data['item_tax_rate'] = '0.00';
                    $data['item_tax_name'] = (!empty($tax_name) ? json_encode($tax_name) : '');
                    $data['item_tax_total'] = (!empty($item_tax_total) ? $item_tax_total : '0.00');
                    $data['total_cost'] = $items_info->rate;
                    $data['item_id'] = $items_info->id;

                    // get all client
                    $this->sam_model->_table_name = 'tbl_sam_items';
                    $this->sam_model->_primary_key = 'items_id';
                    $items_id = $this->sam_model->save_sam($data);
                }
                $action = ('activity_sam_items_added');
                $this->sam_model->log_deals_activity($deals_id, $action, false, serialize([
                    $items_info->description,
                ]));
                $type = "success";
                $msg = _l('sam_item_added');
                $_data['id'] = $deals_id;
                $data['subview'] = $this->load->view('sales_marketing/deals_details/dealItems', $_data, true);
            } else {
                $type = "error";
                $msg = 'please Select an items';
            }
            $message = $msg;
            $data['type'] = $type;
            $data['msg'] = $msg;
            echo json_encode($data);
            exit();
        } else {
            set_alert('error', _l('there_in_no_value'));
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/sales_marketing/details');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public
    function send_mail($id = null)
    {

        $data = $this->sam_model->deals_array_from_post(array('subject', 'message_body', 'deals_id', 'email_to', 'email_cc'));

        $user_id = get_staff_user_id();
        $user_info = $this->sam_model->check_by_deals(array('staffid' => $user_id), db_prefix() . 'staff');
        // get company name
        $name = $user_info->firstname . ' ' . $user_info->lastname;
        $params['subject'] = $data['subject'];
        $params['message'] = $data['message_body'];
        $params['recipient'] = $data['email_to'];
        $params['cc'] = $data['email_cc'];
        $params['fullname'] = $name;
        $params['recipient'] = $data['email_to'];
        $params['deal_id'] = $data['deals_id'];


        // save into inbox table procees
        $idata['email_to'] = $data['email_to'];
        $idata['email_cc'] = $data['email_cc'];

        $idata['email_from'] = $user_info->email;
        $idata['user_id'] = $user_id;
        $idata['deals_id'] = $data['deals_id'];
        $idata['subject'] = $data['subject'];
        $idata['message_body'] = $data['message_body'];
        $idata['message_time'] = date('Y-m-d H:i:s');
        // save into inbox
        $this->sam_model->_table_name = 'tbl_sam_email';
        $this->sam_model->_primary_key = 'id';
        $deal_id = $this->sam_model->save_sam($idata, $id);
        $attachments = handle_sam_attachments_array($deal_id);
        $params['attachments'] = $attachments;
        $params['template'] = $params;

        $send_email = $this->sam_model->send_email($params);


        // update deal $attachments using json_encode
        $this->sam_model->_table_name = 'tbl_sam_email';
        $this->sam_model->_primary_key = 'id';
        $this->sam_model->save_sam(array('attach_file' => json_encode($attachments)), $deal_id);
        
        add_activity_transactions($data['deals_id'],'send-email');
        
        $this->sam_model->log_deals_activity($data['deals_id'], 'not_sam_email_sent', false, serialize([
            $data['subject'],
        ]));
        $type = "success";
        $message = _l('msg_sent');
        set_alert($type, $message);

        if (!empty($id)) {
            $msg = _l('msg_sent');
            $activity = 'activity_msg_sent';
        } else {
            $msg = _l('msg_sent');
            $activity = 'activity_msg_sent';
        }
        log_activity($activity . ' - ' . $data['subject'] . ' [ID:' . $id . ']');
        // messages for user
        $type = "success";
        set_alert($type, $msg);
        redirect('admin/sales_marketing/details/' . $data['deals_id'] . '/email');
    }


    public
    function dealsManuallyItems()
    {
        $data['title'] = _l('added') . ' ' . _l('manually');
        $data['subview'] = $this->load->view('sales_marketing/deals_manually_items', $data, false);
        $this->load->view('admin/_layout_modal', $data);
    }

    public
    function email_details($deals_email_id = null)
    {
        $data['title'] = _l('email_details');
        $data['details'] = get_sam_row('tbl_sam_email', array('id' => $deals_email_id));
        $data['subview'] = $this->load->view('sales_marketing/email_details', $data, false);
        $this->load->view('sales_marketing/_layout_modal', $data);
    }

    public
    function call_details($deals_email_id = null)
    {
        $data['title'] = _l('call_details');
        $data['details'] = get_sam_row('tbl_sam_calls', array('calls_id' => $deals_email_id));
        $data['subview'] = $this->load->view('sales_marketing/call_details', $data, FALSE);
        // $this->load->view('admin/_layout_modal', $data);
        $this->load->view('sales_marketing/_layout_modal', $data);
    }

    public
    function manuallyItems($deals_id = null, $items_id = null)
    {
        $data['deals_id'] = $deals_id;
        if (!empty($items_id)) {
            $data['items_info'] = get_sam_row('tbl_sam_items', array('items_id' => $items_id));
        }
        $data['subview'] = $this->load->view('sales_marketing/deals_manually_items', $data, FALSE);
        $this->load->view('sales_marketing/_layout_modal', $data);
    }

    public
    function download_file($file)
    {
        $this->load->helper('download');
        if (file_exists(('uploads/' . $file))) {
            $down_data = file_get_contents('uploads/' . $file); // Read the file's contents
            force_download($file, $down_data);
        } else {
            $type = "error";
            $message = 'Operation Fieled !';
            set_alert($type, $message);
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/mailbox');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }


    public
    function updateUsers($deals_id, $type)
    {
        // post data
        $data['deals'] = $this->sam_model->check_by_deals(array('id' => $deals_id), 'tbl_sam');
        $type_id = $this->input->post($type . '_id', true);
        if (!empty($type_id)) {
            $_data[$type . '_id'] = json_encode($type_id);
            $this->sam_model->_table_name = 'tbl_sam';
            $this->sam_model->_primary_key = 'id';
            $this->sam_model->save_sam($_data, $deals_id);

            if ($type == 'user') {

                foreach ($type_id as $v_user) {
                    if ($v_user != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $v_user,
                            'from_user_id' => true,
                            'description' => 'not_sam_added_you',
                            'link' => 'admin/sales_marketing/details/' . $deals_id,
                            'value' => $data['deals']->title,
                        ));
                    }
                }
                pusher_trigger_notification($type_id);
            }
            $type = "success";
            $message = _l('sam_update_user');
            set_alert($type, $message);
            redirect('admin/sales_marketing/details/' . $deals_id);
        }
        $data['title'] = _l('update_' . $type);
        $data['type'] = $type;

        $data['subview'] = $this->load->view('sales_marketing/_modal_users', $data, FALSE);
        $this->load->view('sales_marketing/_layout_modal', $data);
    }


    public
    function save_deals_email_integration()
    {
        $input_data = $this->sam_model->deals_array_from_post(array(
            'encryption_deals', 'default_pipeline', 'default_stage', 'default_deal_owner', 'config_deals_host', 'config_deals_username', 'config_deals_mailbox', 'unread_deals_email', 'delete_mail_after_deals_import'
        ));

        $config_password = $this->input->post('config_deals_password', true);
        if (!empty($config_password)) {
            $input_data['config_deals_password'] = encrypt($config_password);
        }
        if ($input_data['encryption_deals'] == 'on') {
            $input_data['encryption_deals'] = null;
        }
        if (empty($input_data['unread_deals_email'])) {
            $input_data['unread_deals_email'] = 'on';
        }
        if (empty($input_data['delete_mail_after_deals_import'])) {
            $input_data['delete_mail_after_deals_import'] = null;
        }
        foreach ($input_data as $key => $value) {
            $data = array('value' => $value);
            $this->db->where('config_key', $key)->update('tbl_config', $data);
            $exists = $this->db->where('config_key', $key)->get('tbl_config');
            if ($exists->num_rows() == 0) {
                $this->db->insert('tbl_config', array("config_key" => $key, "value" => $value));
            }
        }
        $msg = _l('save_deals_email_integration');
        $activity = 'activity_save_deals_email_integration';

        log_activity($activity . ' - ' . $data['notes']);
        // messages for user
        $type = "success";
        set_alert($type, $msg);
        redirect('admin/sales_marketing/deals_setting');
    }

    public
    function sales_pipelines($id = NULL, $opt = null)
    {
        $data['title'] = _l('new_pipeline');
        if (!empty($id)) {
            $data['pipeline'] = $this->sam_model->check_by_deals(array('pipeline_id' => $id), 'tbl_sam_pipelines');
        }
        $this->load->view('sales_pipelines', $data);
    }

    public
    function saved_pipelines($id = null)
    {

        $cate_data['pipeline_name'] = $this->input->post('pipeline_name', TRUE);

        $this->sam_model->_table_name = 'tbl_sam_pipelines';
        $this->sam_model->_primary_key = 'pipeline_id';
        $id = $this->sam_model->save_sam($cate_data, $id);


        if (!empty($id)) {
            $msg = _l('successfully_pipelines_update');
            $activity = 'activity_successfully_pipelines_added_update';
        } else {
            $msg = _l('successfully_pipelines_added');
            $activity = 'activity_successfully_pipelines_added';
        }
        log_activity($activity . ' - ' . $cate_data['pipeline_name'] . ' [ID:' . $id . ']');
        $type = "success";

        set_alert($type, $msg);
        redirect('admin/sales_marketing/sales_pipelines');
    }

    public function delete_email_attachment($id, $file_name)
    {
        $this->db->where('id', $id);
        $deal_email_info = $this->db->get('tbl_sam_email')->row();
        if (!empty($deal_email_info)) {


            $deals_id = $deal_email_info->deals_id;
            $attachment = $deal_email_info->attach_file;

            // remove file from folder if exist
            $path = get_upload_path_for_sam() . $deals_id . '/' . $file_name;
            if (file_exists($path)) {
                unlink($path);
            }

            $attachment = json_decode($attachment);
            $new_attachment = array();
            foreach ($attachment as $v_attachment) {
                if ($v_attachment->file_name != $file_name) {
                    $new_attachment[] = $v_attachment;
                }
            }

            $this->db->where('id', $id);
            $this->db->update('tbl_sam_email', array('attach_file' => json_encode($new_attachment)));
            $type = "success";
            $message = _l('successfully_delete');
            set_alert($type, $message);
            redirect('admin/sales_marketing/details/' . $deals_id . '/email');
        } else {
            show_404();
        }


    }

    public function file_download($folder_indicator, $attachmentid = '', $file_name = '')
    {
        if (!empty($folder_indicator == 'deals_attachment' || $folder_indicator == 'deals_comments')) {
            if (!is_staff_logged_in()) {
                show_404();
            }
            // admin area

            if ($folder_indicator == 'deals_attachment') {
                $this->db->where('id', $attachmentid);
            } else {
                // Lead public form
                $this->db->where('attachment_key', $attachmentid);
            }

            $attachment = $this->db->get(db_prefix() . 'files')->row();

            if (!$attachment) {
                show_404();
            }

            $path = get_upload_path_for_sam() . $attachment->rel_id . '/' . $attachment->file_name;
            force_download($path, null);
        } else if ($folder_indicator == 'deals_email') {
            if (!is_staff_logged_in()) {
                show_404();
            }
            // client area
            $this->db->where('id', $attachmentid);
            $deal_email_info = $this->db->get('tbl_sam_email')->row();
            if (!empty($deal_email_info)) {
                $deals_id = $deal_email_info->deals_id;
                $attachment = $deal_email_info->attach_file;
                $attachment = json_decode($attachment);

                // if file_name is empty then download all attachments
                if (empty($file_name)) {
                    $path = get_upload_path_for_sam() . $deals_id . '/';
                    $zipname = 'sam_' . $deals_id . '_attachments.zip';
                    $zip = new ZipArchive;
                    $zip->open($zipname, ZipArchive::CREATE);
                    foreach ($attachment as $v_attachment) {
                        $zip->addFile($path . $v_attachment->file_name, $v_attachment->file_name);
                    }
                    $zip->close();
                    header('Content-Type: application/zip');
                    header('Content-disposition: attachment; filename=' . $zipname);
                    header('Content-Length: ' . filesize($zipname));
                    readfile($zipname);
                    unlink($zipname);
                } else {
                    // get attachments file according to file_name
                    foreach ($attachment as $v_attachment) {
                        if ($v_attachment->file_name == $file_name) {
                            $path = get_upload_path_for_sam() . $deals_id . '/' . $v_attachment->file_name;
                            force_download($path, null);
                        }
                    }
                }


            } else {
                show_404();
            }

        } else {
            show_404();
        }
    }

    public function add_activity()
    {
        $deal_id = $this->input->post('deal_id');

        if (!is_staff_member()) {
            ajax_access_denied();
        }
        if ($this->input->post()) {
            $message = $this->input->post('activity');
            $aId = $this->sam_model->log_deals_activity($deal_id, $message);

            if ($aId) {
                $this->db->where('id', $aId);
                $this->db->update('tbl_sam_activity_log', ['custom_activity' => 1]);
            }

        }
    }
    
    public function list_proposals($proposal_id = '')
    {          
        close_setup_menu();

        if (staff_cant('view', 'proposals') && staff_cant('view_own', 'proposals') && get_option('allow_staff_view_estimates_assigned') == 0) {
            access_denied('proposals');
        }

        $isPipeline = $this->session->userdata('proposals_pipeline') == 'true';
        
        if ($isPipeline && !$this->input->get('status')) {    
            $data['title']           = _l('proposals_pipeline');
            $data['bodyclass']       = 'proposals-pipeline';
            $data['switch_pipeline'] = false;
            // Direct access
            if (is_numeric($proposal_id)) {
                $data['proposalid'] = $proposal_id;
            } else {
                $data['proposalid'] = $this->session->flashdata('proposalid');
            }

            $this->load->view('admin/proposals/pipeline/manage', $data);
        } else {

            // Pipeline was initiated but user click from home page and need to show table only to filter
            if ($this->input->get('status') && $isPipeline) {
                $this->pipeline(0, true);
            }

            $data['proposal_id']           = $proposal_id;
            $data['switch_pipeline']       = true;
            $data['title']                 = _l('proposals');
            $data['proposal_statuses']     = $this->proposals_model->get_statuses();
            $data['proposals_sale_agents'] = $this->proposals_model->get_sale_agents();
            $data['years']                 = $this->proposals_model->get_proposals_years();
            $data['table'] = App_table::find('proposals');                                 
            return $this->load->view('sales_marketing/proposals/manage', $data);
        }
    }
    
    public function CalculateTime(){
        $data = $this->input->post();
        if($data['start_date']!="" && $data['end_date']!=""){
            $start_date = strtotime($data['start_date']);
            $end_date = strtotime($data['end_date']);
            $diff = $end_date - $start_date;
            $calculated_time = seconds_to_time_format($diff,true);
            echo $calculated_time;
        }
        else{
            echo "";
        }
    }
    
    public function getClientContacts(){
        $postdata = $this->input->post(); 
        $contact_id='';
        if(isset($postdata['deal_id'])){
            $deal_rec = $this->sam_model->getAllRecords('tbl_sam','*',array('id'=>$postdata['deal_id'],'rel_type' => 'customer'));
            if($deal_rec){
                foreach($deal_rec as $value){
                    $customer_id = $value['rel_id'];    
                    $contact_id = $value['contact_id'];    
                }
            }    
        }
        $result = $this->sam_model->getAllRecords('tblcontacts','id,firstname,lastname,title',array('userid'=>$postdata['client_id']));
        //echo "<pre>"; print_r($postdata); exit;
        $option = "<option value=''>"._l('dropdown_non_selected_tex')."</option>";
        if($result){
            foreach($result as $k => $v){
                if($contact_id==$v['id']){
                    $option .= "<option value='".$v['id']."' selected='selected'>".$v['firstname'].' '.$v['lastname']."</option>";
                }
                else{
                    $option .= "<option value='".$v['id']."'>".$v['firstname'].' '.$v['lastname']."</option>";    
                }
                
            }
        }  
        echo $option;
    }
    public function update_isnotified() {
    $reminder_id = $this->input->post('reminder_id');
    $isnotified = $this->input->post('isnotified');

    $this->db->where('id', $reminder_id);
    $this->db->update('tbl_sam_reminders', ['isnotified' => $isnotified]);

    echo json_encode(['success' => true]);
}

}
