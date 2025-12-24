<?php

use app\services\proposals\ProposalsPipeline;

defined('BASEPATH') or exit('No direct script access allowed');

class Proposals extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('sam_proposals_model');
        $this->load->model('sam_model');
        $this->load->model('misc_model');
        $this->load->model('contracts_model');
        $this->load->model('currencies_model');
    }

    public function index($proposal_id = '')
    {
        $this->list_proposals($proposal_id);
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

            $this->load->view(SAM_MODULE.'/proposals/pipeline/manage', $data);
        } else {

            // Pipeline was initiated but user click from home page and need to show table only to filter
            if ($this->input->get('status') && $isPipeline) {
                $this->pipeline(0, true);
            }

            $data['proposal_id']           = $proposal_id;
            $data['switch_pipeline']       = true;
            $data['title']                 = _l('proposals');
            $data['proposal_statuses']     = $this->sam_proposals_model->get_statuses();
            $data['proposals_sale_agents'] = $this->sam_proposals_model->get_sale_agents();
            $data['years']                 = $this->sam_proposals_model->get_proposals_years();
            $data['table'] = App_table::find('proposals'); 
            $data['proposal'] = $this->sam_proposals_model->get($proposal_id);
            $this->load->view(SAM_MODULE.'/proposals/manage', $data);
        }
    }

    public function table()
    {
        if (
            staff_cant('view', 'proposals')
            && staff_cant('view_own', 'proposals')
            && get_option('allow_staff_view_proposals_assigned') == 0
        ) {
            ajax_access_denied();
        }

        App_table::find('proposals')->output();
    }

    public function proposal_relations($rel_id, $rel_type)
    {
        $this->app->get_table_data('proposals_relations', [
            'rel_id'   => $rel_id,
            'rel_type' => $rel_type,
        ]);
    }

    public function delete_attachment($id)
    {
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo $this->sam_proposals_model->delete_attachment($id);
        } else {
            ajax_access_denied();
        }
    }

    public function clear_signature($id)
    {
        if (staff_can('delete',  'proposals')) {
            $this->sam_proposals_model->clear_signature($id);
        }

        redirect(admin_url('proposals/list_proposals/' . $id));
    }

    public function sync_data()
    {
        if (staff_can('create',  'proposals') || staff_can('edit',  'proposals')) {
            $has_permission_view = staff_can('view',  'proposals');

            $this->db->where('rel_id', $this->input->post('rel_id'));
            $this->db->where('rel_type', $this->input->post('rel_type'));

            if (!$has_permission_view) {
                $this->db->where('addedfrom', get_staff_user_id());
            }

            $address = trim($this->input->post('address'));
            $address = nl2br($address);
            $this->db->update(db_prefix() . 'proposals', [
                'phone'   => $this->input->post('phone'),
                'zip'     => $this->input->post('zip'),
                'country' => $this->input->post('country'),
                'state'   => $this->input->post('state'),
                'address' => $address,
                'city'    => $this->input->post('city'),
            ]);

            if ($this->db->affected_rows() > 0) {
                echo json_encode([
                    'message' => _l('all_data_synced_successfully'),
                ]);
            } else {
                echo json_encode([
                    'message' => _l('sync_proposals_up_to_date'),
                ]);
            }
        }
    }

    public function proposal($id = '',$sam_id = '')
    {       
        $cond = array('id' => $sam_id);
        $sam_rec = $this->sam_model->getAllRecords("tbl_sam","*",$cond);
        $customer_id = "";
        if($sam_rec){
            $sam_rec = $sam_rec[0];
            $customer_id = $sam_rec['rel_id'];          
        }                                                      
        if ($this->input->post()) { 
            $proposal_data = $this->input->post();  
            $proposal_data['sam_id'] = $sam_id;     
            if ($id == '' || $id == 0) {
                if (!is_admin() && staff_cant('create', 'proposals')) {
                    access_denied('proposals');
                } 
                $proposal_data['sam_id'] = $sam_id;                                   
                $id = $this->sam_proposals_model->add($proposal_data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('proposal')));
                  
                    //redirect(admin_url(SAM_MODULE.'/proposals/list_proposals/' . $id));
                    redirect('admin/'.SAM_MODULE.'/details/' . $sam_id . '/' . 'proposals');
                }
            } else {  
                if (!is_admin() && staff_cant('edit', 'proposals')) {
                    access_denied('proposals');
                }                                                
                $success = $this->sam_proposals_model->update($proposal_data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('proposal')));
                }                          
                redirect('admin/'.SAM_MODULE.'/details/' . $sam_id . '/' . 'proposals');
                //redirect(admin_url(SAM_MODULE.'/proposals/list_proposals/' . $sam_id .'/'. $id));
            }
        }
        if ($id == '' || $id == 0) {   
            $title = _l('add_new', _l('proposal_lowercase'));
        } else {
            $data['proposal'] = $this->sam_proposals_model->get($id);

            if (!$data['proposal'] || !sam_user_can_view_proposal($id)) {
                //blank_page(_l('proposal_not_found'));
            }

            $data['estimate']    = $data['proposal'];
            $data['is_proposal'] = true;
            $title               = _l('edit', _l('proposal_lowercase'));
        }

        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();
        $this->load->model('invoice_items_model');
        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->invoice_items_model->get_grouped();
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }
        $data['items_groups'] = $this->invoice_items_model->get_groups();

        $data['statuses']      = $this->sam_proposals_model->get_statuses();
        $data['staff']         = $this->staff_model->get('', ['active' => 1]);
        $data['currencies']    = $this->currencies_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['title'] = $title;
        $data['sam_id'] = $sam_id;
        $data['customer_id'] = $customer_id;
        $this->load->view(SAM_MODULE.'/proposals/proposal', $data);
    }

    public function get_template()
    {
        $name = $this->input->get('name');
        echo $this->load->view(SAM_MODULE.'/proposals/templates/' . $name, [], true);
    }

    public function send_expiry_reminder($id)
    {
        if(!is_admin()){
            $canView = sam_user_can_view_proposal($id);
            if (!$canView) {
                access_denied('proposals');
            } else {
                if (staff_cant('view', 'proposals') && staff_cant('view_own', 'proposals') && $canView == false) {
                    access_denied('proposals');
                }
            }
        }

        $success = $this->sam_proposals_model->send_expiry_reminder($id);
        if ($success) {
            set_alert('success', _l('sent_expiry_reminder_success'));
        } else {
            set_alert('danger', _l('sent_expiry_reminder_fail'));
        }
        if ($this->set_proposal_pipeline_autoload($id)) {
            redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url(SAM_MODULE.'/proposals/#' . $id));
        }
    }

    public function clear_acceptance_info($id)
    {
        if (is_admin()) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'proposals', get_acceptance_info_array(true));
        }

        redirect(admin_url('proposals/list_proposals/' . $id));
    }

    public function pdf($id)
    {
        if (!$id) {
            redirect(admin_url('proposals'));
        }
        if(!is_admin()){
            $canView = sam_user_can_view_proposal($id);
            if (!$canView) {
                access_denied('proposals');
            } else {
                if (staff_cant('view', 'proposals') && staff_cant('view_own', 'proposals') && $canView == false) {
                    access_denied('proposals');
                }
            }
        }

        $proposal = $this->sam_proposals_model->get($id);

        try {
            $pdf = proposal_pdf($proposal);  
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $proposal_number = format_proposal_number($id);
        $pdf->Output($proposal_number . '.pdf', $type);
    }

    public function get_proposal_data_ajax($id, $to_return = false)
    {
        if (staff_cant('view', 'proposals') && staff_cant('view_own', 'proposals') && get_option('allow_staff_view_proposals_assigned') == 0) {
            echo _l('access_denied');
            die;
        }

        $proposal = $this->sam_proposals_model->get($id, [], true);
        
        if (!$proposal || !sam_user_can_view_proposal($id)) {
            //echo _l('proposal_not_found');
            //die;
        }

        $this->app_mail_template->set_rel_id($proposal->id);
        $data = prepare_mail_preview_data('proposal_send_to_customer', $proposal->email);

        $merge_fields = [];

        $merge_fields[] = [
            [
                'name' => 'Items Table',
                'key'  => '{proposal_items}',
            ],
        ];

        $merge_fields = array_merge($merge_fields, $this->app_merge_fields->get_flat('proposals', 'other', '{email_signature}'));

        $data['proposal_statuses']     = $this->sam_proposals_model->get_statuses();
        $data['members']               = $this->staff_model->get('', ['active' => 1]);
        $data['proposal_merge_fields'] = $merge_fields;
        $data['proposal']              = $proposal;
        $data['totalNotes']            = total_rows(db_prefix() . 'notes', ['rel_id' => $id, 'rel_type' => 'proposal']);
        if ($to_return == false) {
            $this->load->view(SAM_MODULE.'/proposals/proposals_preview_template', $data);
        } else {
            return $this->load->view(SAM_MODULE.'/proposals/proposals_preview_template', $data, true);
        }
    }

    public function add_note($rel_id)
    {
        if(is_admin() || sam_user_can_view_proposal($rel_id)){
            if ($this->input->post()) {
                $this->misc_model->add_note($this->input->post(), 'proposal', $rel_id);
                echo $rel_id;
            }
        }
    }

    public function get_notes($id)
    {
        if (is_admin() || sam_user_can_view_proposal($id)) {
            $data['notes'] = $this->misc_model->get_notes($id, 'proposal');
            $this->load->view('admin/includes/sales_notes_template', $data);
        }
    }

    public function convert_to_estimate($id)
    {
        if (staff_cant('create', 'estimates')) {
            access_denied('estimates');
        }
        if ($this->input->post()) {
            $this->load->model('estimates_model');
            $estimate_id = $this->estimates_model->add($this->input->post());
            if ($estimate_id) {
                set_alert('success', _l('proposal_converted_to_estimate_success'));
                $this->db->where('id', $id);
                $this->db->update(db_prefix() . 'proposals', [
                    'estimate_id' => $estimate_id,
                    'status'      => 3,
                ]);
                log_activity('Proposal Converted to Estimate [EstimateID: ' . $estimate_id . ', ProposalID: ' . $id . ']');

                hooks()->do_action('proposal_converted_to_estimate', ['proposal_id' => $id, 'estimate_id' => $estimate_id]);

                redirect(admin_url('estimates/estimate/' . $estimate_id));
            } else {
                set_alert('danger', _l('proposal_converted_to_estimate_fail'));
            }
            if ($this->set_proposal_pipeline_autoload($id)) {
                redirect(admin_url('proposals'));
            } else {
                redirect(admin_url('proposals/list_proposals/' . $id));
            }
        }
    }

    public function convert_to_invoice($id)
    {
        if (staff_cant('create', 'invoices')) {
            access_denied('invoices');
        }
        if ($this->input->post()) {
            $this->load->model('invoices_model');
            $invoice_id = $this->invoices_model->add($this->input->post());
            if ($invoice_id) {
                set_alert('success', _l('proposal_converted_to_invoice_success'));
                $this->db->where('id', $id);
                $this->db->update(db_prefix() . 'proposals', [
                    'invoice_id' => $invoice_id,
                    'status'     => 3,
                ]);
                log_activity('Proposal Converted to Invoice [InvoiceID: ' . $invoice_id . ', ProposalID: ' . $id . ']');

                do_action_deprecated('proposal_converted_to_invoice', ['proposal_id' => $id, 'invoice_id' => $invoice_id], '3.1.6', 'after_proposal_converted_to_invoice');
                hooks()->do_action('after_proposal_converted_to_invoice', ['proposal_id' => $id, 'invoice_id' => $invoice_id]);

                redirect(admin_url('invoices/invoice/' . $invoice_id));
            } else {
                set_alert('danger', _l('proposal_converted_to_invoice_fail'));
            }
            if ($this->set_proposal_pipeline_autoload($id)) {
                redirect(admin_url('proposals'));
            } else {
                redirect(admin_url('proposals/list_proposals/' . $id));
            }
        }
    }

    public function get_invoice_convert_data($id="",$sam_id="")
    {
        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', [
            'expenses_only !=' => 1,
        ]);
        $this->load->model('taxes_model');
        $data['taxes']         = $this->taxes_model->get();
        $data['currencies']    = $this->currencies_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $this->load->model('invoice_items_model');
        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->invoice_items_model->get_grouped();
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }
        $data['items_groups'] = $this->invoice_items_model->get_groups();

        $data['staff']          = $this->staff_model->get('', ['active' => 1]);
        $data['proposal']       = $this->sam_proposals_model->get($id);
        $data['billable_tasks'] = [];
        $data['add_items']      = $this->_parse_items($data['proposal']);

        if ($data['proposal']->rel_type == 'lead') {
            $this->db->where('leadid', $data['proposal']->rel_id);
            $data['customer_id'] = $this->db->get(db_prefix() . 'clients')->row()->userid;
        } else {
            $data['customer_id'] = $data['proposal']->rel_id;
            $data['project_id'] = $data['proposal']->project_id;
        }
        $data['custom_fields_rel_transfer'] = [
            'belongs_to' => 'proposal',
            'rel_id'     => $id,
        ];
        $this->load->view(SAM_MODULE.'/proposals/invoice_convert_template', $data);
    }

    public function get_estimate_convert_data($id)
    {
        $this->load->model('taxes_model');
        $data['taxes']         = $this->taxes_model->get();
        $data['currencies']    = $this->currencies_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $this->load->model('invoice_items_model');
        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->invoice_items_model->get_grouped();
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }
        $data['items_groups'] = $this->invoice_items_model->get_groups();

        $data['staff']     = $this->staff_model->get('', ['active' => 1]);
        $data['proposal']  = $this->sam_proposals_model->get($id);
        $data['add_items'] = $this->_parse_items($data['proposal']);

        $this->load->model('estimates_model');
        $data['estimate_statuses'] = $this->estimates_model->get_statuses();
        if ($data['proposal']->rel_type == 'lead') {
            $this->db->where('leadid', $data['proposal']->rel_id);
            $data['customer_id'] = $this->db->get(db_prefix() . 'clients')->row()->userid;
        } else {
            $data['customer_id'] = $data['proposal']->rel_id;
            $data['project_id'] = $data['proposal']->project_id;
        }

        $data['custom_fields_rel_transfer'] = [
            'belongs_to' => 'proposal',
            'rel_id'     => $id,
        ];

        $this->load->view('admin/proposals/estimate_convert_template', $data);
    }

    private function _parse_items($proposal)
    {
        $items = [];
        foreach ($proposal->items as $item) {
            $taxnames = [];
            $taxes    = sam_get_proposal_item_taxes($item['id']);
            foreach ($taxes as $tax) {
                array_push($taxnames, $tax['taxname']);
            }
            $item['taxname']        = $taxnames;
            $item['parent_item_id'] = $item['id'];
            $item['id']             = 0;
            $items[]                = $item;
        }

        return $items;
    }

    /* Send proposal to email */
    public function send_to_email($id)
    {
        if(!is_admin()){
            $canView = sam_user_can_view_proposal($id);
            if (!$canView) {
                access_denied('proposals');
            } else {
                if (staff_cant('view', 'proposals') && staff_cant('view_own', 'proposals') && $canView == false) {
                    access_denied('proposals');
                }
            }
        }

        if ($this->input->post()) {
            try {
                $success = $this->sam_proposals_model->send_proposal_to_email(
                    $id,
                    $this->input->post('attach_pdf'),
                    $this->input->post('cc')
                );
            } catch (Exception $e) {
                $message = $e->getMessage();
                echo $message;
                if (strpos($message, 'Unable to get the size of the image') !== false) {
                    show_pdf_unable_to_get_image_size_error();
                }
                die;
            }

            if ($success) {
                set_alert('success', _l('proposal_sent_to_email_success'));
            } else {
                set_alert('danger', _l('proposal_sent_to_email_fail'));
            }

            if ($this->set_proposal_pipeline_autoload($id)) {
                redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);
            } else {
                redirect(admin_url(SAM_MODULE.'/proposals/#' . $id));
            }
        }
    }

    public function copy($id="",$sam_id="")
    {
        if (staff_cant('create', 'proposals')) {
            access_denied('proposals');
        }
        $new_id = $this->sam_proposals_model->copy($id);
        if ($new_id) {
            set_alert('success', _l('proposal_copy_success'));
            $this->set_proposal_pipeline_autoload($new_id);
            redirect(admin_url(SAM_MODULE.'/proposals/proposal/' . $new_id.'/'.$sam_id));
        } else {
            set_alert('success', _l('proposal_copy_fail'));
        }
        if ($this->set_proposal_pipeline_autoload($id)) {
            redirect(admin_url(SAM_MODULE.'/proposals'));
        } else {
            redirect(admin_url(SAM_MODULE.'/proposals/list_proposals/' . $id));
        }
    }

    public function mark_action_status($status, $id)
    {
        if (staff_cant('edit', 'proposals')) {
            access_denied('proposals');
        }
        $success = $this->sam_proposals_model->mark_action_status($status, $id);
        if ($success) {
            set_alert('success', _l('proposal_status_changed_success'));
        } else {
            set_alert('danger', _l('proposal_status_changed_fail'));
        }
        if ($this->set_proposal_pipeline_autoload($id)) {
            redirect(admin_url(SAM_MODULE.'/proposals/#'.$id));
        } else {
            //redirect(admin_url(SAM_MODULE.'/proposals/list_proposals/' . $id));
            redirect(admin_url(SAM_MODULE.'/proposals/#'.$id));
        }
    }

    public function delete($id="",$sam_id="")
    {
        if (staff_cant('delete', 'proposals')) {
            access_denied('proposals');
        }
        
        //check proposal related forms like contract and invoices
        if(IsRelatedDataExist("tblcontracts","id",array("pro_id"=>$id))){
            set_alert('warning', "First delete its related contract");
            redirect(admin_url(SAM_MODULE.'/proposals/#'.$id));    
        }

        $response = $this->sam_proposals_model->delete($id,$sam_id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('proposal')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('proposal_lowercase')));
        }
        redirect(admin_url(SAM_MODULE.'/details/'.$sam_id.'/proposals'));
    }

    public function get_relation_data_values($rel_id, $rel_type)
    {
        echo json_encode($this->sam_proposals_model->get_relation_data_values($rel_id, $rel_type));
    }

    public function add_proposal_comment()
    {                                                                
        if ($this->input->post()) {
            echo json_encode([
                'success' => $this->sam_proposals_model->add_comment($this->input->post()),
            ]);
        }
    }

    public function edit_comment($id)
    {
        if ($this->input->post()) {
            echo json_encode([
                'success' => $this->sam_proposals_model->edit_comment($this->input->post(), $id),
                'message' => _l('comment_updated_successfully'),
            ]);
        }
    }

    public function get_proposal_comments($id)
    {
        $data['comments'] = $this->sam_proposals_model->get_comments($id);
        $this->load->view('admin/proposals/comments_template', $data);
    }

    public function remove_comment($id)
    {
        $this->db->where('id', $id);
        $comment = $this->db->get(db_prefix() . 'proposal_comments')->row();
        if ($comment) {
            if ($comment->staffid != get_staff_user_id() && !is_admin()) {
                echo json_encode([
                    'success' => false,
                ]);
                die;
            }
            echo json_encode([
                'success' => $this->sam_proposals_model->remove_comment($id),
            ]);
        } else {
            echo json_encode([
                'success' => false,
            ]);
        }
    }

    public function save_proposal_data()
    {
        if (staff_cant('edit', 'proposals') && staff_cant('create', 'proposals')) {
            header('HTTP/1.0 400 Bad error');
            echo json_encode([
                'success' => false,
                'message' => _l('access_denied'),
            ]);
            die;
        }
        $success = false;
        $message = '';

        $this->db->where('id', $this->input->post('proposal_id'));
        $this->db->update(db_prefix() . 'proposals', [
            'content' => html_purify($this->input->post('content', false)),
        ]);

        $success = $this->db->affected_rows() > 0;
        $message = _l('updated_successfully', _l('proposal'));

        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]);
    }

    // Pipeline
    public function pipeline($set = 0, $manual = false)
    {
        if ($set == 1) {
            $set = 'true';
        } else {
            $set = 'false';
        }
        $this->session->set_userdata([
            'proposals_pipeline' => $set,
        ]);
        if ($manual == false) {
            redirect(admin_url('proposals'));
        }
    }

    public function pipeline_open($id)
    {
        if (staff_can('view',  'proposals') || staff_can('view_own',  'proposals') || get_option('allow_staff_view_proposals_assigned') == 1) {
            $data['proposal']      = $this->get_proposal_data_ajax($id, true);
            $data['proposal_data'] = $this->sam_proposals_model->get($id);
            $this->load->view('admin/proposals/pipeline/proposal', $data);
        }
    }

    public function update_pipeline()
    {
        if (staff_can('edit',  'proposals')) {
            $this->sam_proposals_model->update_pipeline($this->input->post());
        }
    }

    public function get_pipeline()
    {
        if (staff_can('view',  'proposals') || staff_can('view_own',  'proposals') || get_option('allow_staff_view_proposals_assigned') == 1) {
            $data['statuses'] = $this->sam_proposals_model->get_statuses();
            $this->load->view('admin/proposals/pipeline/pipeline', $data);
        }
    }

    public function pipeline_load_more()
    {
        $status = $this->input->get('status');
        $page   = $this->input->get('page');

        $proposals = (new ProposalsPipeline($status))
        ->search($this->input->get('search'))
        ->sortBy(
            $this->input->get('sort_by'),
            $this->input->get('sort')
        )
        ->page($page)->get();

        foreach ($proposals as $proposal) {
            $this->load->view('admin/proposals/pipeline/_kanban_card', [
                'proposal' => $proposal,
                'status'   => $status,
            ]);
        }
    }

    public function set_proposal_pipeline_autoload($id)
    {
        if ($id == '') {
            return false;
        }

        if ($this->session->has_userdata('proposals_pipeline') && $this->session->userdata('proposals_pipeline') == 'true') {
            $this->session->set_flashdata('proposalid', $id);

            return true;
        }

        return false;
    }

    public function get_due_date()
    {
        if ($this->input->post()) {
            $date    = $this->input->post('date');
            $duedate = '';
            if (get_option('proposal_due_after') != 0) {
                $date    = to_sql_date($date);
                $d       = date('Y-m-d', strtotime('+' . get_option('proposal_due_after') . ' DAY', strtotime($date)));
                $duedate = _d($d);
                echo $duedate;
            }
        }
    }
    
    public function get_contract_convert_data($id="",$sam_id="")
    {
        //$data['add_items']      = $this->_parse_items($data['proposal']);

        $this->load->model('sam_model');  
        $this->load->model('contracts_model');  
        $cond = array('id' => $sam_id);
        $sam_rec = $this->sam_model->getAllRecords("tbl_sam","*",$cond);
        $customer_id = "";
        if($sam_rec){
            $sam_rec = $sam_rec[0];
            $customer_id = $sam_rec['rel_id'];
            //print_r(get_client($customer_id)); exit;
        } 
        
        $title = _l('add_new', _l('contract_lowercase'));
        $data['sam_id'] = $sam_id;
        $data['customer_id'] = $customer_id;
        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['types']         = $this->contracts_model->get_contract_types();
        $data['proposal_data'] = getDealProposals(array('sam_id'=>$sam_id,'tblproposals.id'=>$id,'status'=>3));
        $data['title']         = $title;
        $data['bodyclass']     = 'contract';
        $this->load->view(SAM_MODULE.'/proposals/contract_convert_template', $data);
        //$this->load->view(SAM_MODULE.'/proposals/invoice_convert_template', $data);
    }
    
    public function convert_to_contract($p_id = '0',$sam_id = '')
    {
        $cond = array('id' => $sam_id);
        $sam_rec = $this->sam_model->getAllRecords("tbl_sam","*",$cond);
        $customer_id = "";
        if($sam_rec){
            $sam_rec = $sam_rec[0];
            $customer_id = $sam_rec['rel_id'];          
        } 
        if ($this->input->post()) { 
            //echo "<pre>"; print_r($this->input->post()); exit;
            if ($p_id != '' || $p_id != 0) {
                
                if (staff_cant('create', 'contracts')) {
                    access_denied('contracts');
                }
                
                $post_data = $this->input->post();
                $post_data['sam_id'] = $sam_id;
                //echo "<pre>"; print_r($post_data); exit;
                $id = $this->contracts_model->add($post_data);
                if ($id) {
                    add_activity_transactions($sam_id,'added new contract');
                    
                    $this->db->where('id', $p_id);
                    $this->db->update(db_prefix() . 'proposals', [
                        'contract_id' => $id
                    ]);
                    log_activity('Proposal Converted to Contract [ContractID: ' . $id . ', ProposalID: ' . $p_id . ']');

                    $success = true;
                    $msg = _l('added_successfully', _l('contract'));
                    //set_alert('success', _l('added_successfully', _l('contract')));
                    redirect(admin_url(SAM_MODULE.'/details/'.$sam_id.'/contracts'));
                }
                else{
                    $success = false;
                    $msg = "Contract not added";
                }
            } 
        }
    }
}
