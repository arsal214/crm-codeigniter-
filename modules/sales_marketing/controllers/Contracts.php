<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Contracts extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('contracts_model');
        $this->load->model('sam_model');
        $this->load->model('sam_proposals_model');
        $this->load->model('currencies_model');      
    }
    
    /* List all contracts */
    public function index()
    {
        close_setup_menu();

        if (staff_cant('view', 'contracts') && staff_cant('view_own', 'contracts')) {
            access_denied('contracts');
        }

        $data['expiring']               = $this->contracts_model->get_contracts_about_to_expire(get_staff_user_id());
        $data['count_active']           = count_active_contracts();
        $data['count_expired']          = count_expired_contracts();
        $data['count_recently_created'] = count_recently_created_contracts();
        $data['count_trash']            = count_trash_contracts();
        $data['chart_types']            = json_encode($this->contracts_model->get_contracts_types_chart_data());
        $data['chart_types_values']     = json_encode($this->contracts_model->get_contracts_types_values_chart_data());
        $data['contract_types']         = $this->contracts_model->get_contract_types();
        $data['years']                  = $this->contracts_model->get_contracts_years();
        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['title']         = _l('contracts');
        $data['table'] = App_table::find('contracts');
        $this->load->view(SAM_MODULE.'/contracts/manage', $data);
    }

    public function table($clientid = '')
    {
        if (staff_cant('view', 'contracts') && staff_cant('view_own', 'contracts')) {
            ajax_access_denied();
        }

       App_table::find('contracts')->output([
            'clientid' => $clientid,
       ]);
    }

    /* Edit contract or add new contract */
    public function contract($id = '0',$sam_id = '')
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
            if ($id == '' || $id == 0) {
                
                if (staff_cant('create', 'contracts')) {
                    access_denied('contracts');
                }
                
                $post_data = $this->input->post();
                $post_data['sam_id'] = $sam_id;
                //echo "<pre>"; print_r($post_data); exit;
                $id = $this->contracts_model->add($post_data);
                if ($id) {
                    add_activity_transactions($sam_id,'added new contract');
                    $success = true;
                    $msg = _l('added_successfully', _l('contract'));
                    //set_alert('success', _l('added_successfully', _l('contract')));
                
                }
                else{
                    $success = false;
                    $msg = "Contract not added";
                }
                echo json_encode([
                    'success'           => $success,
                    'message'           => $msg,                                                             
                ]);
                die;
            } 
            else {   
                if (staff_cant('edit', 'contracts')) {
                    access_denied('contracts');
                }
                 
                $contract = $this->contracts_model->get($id);
                $data     = $this->input->post();

                if ($contract->signed == 1) {
                    unset($data['contract_value'],$data['clientid'], $data['datestart'], $data['dateend']);
                }

                $success = $this->contracts_model->update($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('contract')));
                }
                redirect(admin_url(SAM_MODULE.'/contracts/contract/' . $id. '/'. $sam_id));
            }
        }
        if ($id == '' || $id == 0) {
            $title = _l('add_new', _l('contract_lowercase'));
        } else {
            $data['contract']                 = $this->contracts_model->get($id, [], true);
            $data['contract_renewal_history'] = $this->contracts_model->get_contract_renewal_history($id);
            $data['totalNotes']               = total_rows(db_prefix() . 'notes', ['rel_id' => $id, 'rel_type' => 'contract']);
            if (!$data['contract'] || (staff_cant('view', 'contracts') && $data['contract']->addedfrom != get_staff_user_id())) {
                blank_page(_l('contract_not_found'));
            }

            $data['contract_merge_fields'] = $this->app_merge_fields->get_flat('contract', ['other', 'client'], '{email_signature}');

            $title = $data['contract']->subject;

            $data = array_merge($data, prepare_mail_preview_data('contract_send_to_customer', $data['contract']->client));
        }
        $data['sam_id'] = $sam_id;
        $data['customer_id'] = $customer_id;
        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['types']         = $this->contracts_model->get_contract_types();
        $data['proposal_data'] = getDealProposals(array('sam_id'=>$sam_id,'status'=>3));
        //echo "<pre>"; print_r($data['proposal_data']); exit;
        $data['title']         = $title;
        $data['bodyclass']     = 'contract';
        $this->load->view(SAM_MODULE.'/contracts/contract', $data);
    }
    
    /* Delete contract from database */
    public function delete($id='',$sam_id='')
    {
        if (staff_cant('delete', 'contracts')) {
            access_denied('contracts');
        }
        if (!$id) {
            //redirect(admin_url('contracts'));
            redirect(admin_url(SAM_MODULE.'/details/'.$sam_id.'/contracts'));
        }
        
        //check contract related form like invoices
        if(IsRelatedDataExist("tblinvoices","id",array("contract_id"=>$id))){
            set_alert('warning', "First delete its related invoice");
            redirect(admin_url(SAM_MODULE.'/details/'.$sam_id.'/contracts'));   
        }
        
        $response = $this->contracts_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('contract')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('contract_lowercase')));
        }
        
        redirect(admin_url(SAM_MODULE.'/details/'.$sam_id.'/contracts'));
    }
    
    
    
    public function get_template()
    {
        $name = $this->input->get('name');
        echo $this->load->view(SAM_MODULE.'/contracts/templates/' . $name, [], true);
    }

    public function mark_as_signed($id,$sam_id='')
    {
        if (staff_cant('edit', 'contracts')) {
            access_denied('mark contract as signed');
        }

        $this->contracts_model->mark_as_signed($id);

        redirect(admin_url(SAM_MODULE.'/contracts/contract/' . $id. '/'. $sam_id));
    }

    public function unmark_as_signed($id,$sam_id='')
    {
        if (staff_cant('edit', 'contracts')) {
            access_denied('mark contract as signed');
        }

        $this->contracts_model->unmark_as_signed($id);

        redirect(admin_url(SAM_MODULE.'/contracts/contract/' . $id. '/'. $sam_id));
    }

    public function pdf($id,$sam_id='')
    {
        if (staff_cant('view', 'contracts') && staff_cant('view_own', 'contracts')) {
            access_denied('contracts');
        }

        if (!$id) {
            redirect(admin_url(SAM_MODULE.'/details/'.$sam_id.'/contracts'));
        }

        $contract = $this->contracts_model->get($id);

        try {
            $pdf = contract_pdf($contract);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }
        ob_end_clean();
        $pdf->Output(slug_it($contract->subject) . '.pdf', $type);
    }

    public function send_to_email($id,$sam_id="")
    {
        if (staff_cant('view', 'contracts') && staff_cant('view_own', 'contracts')) {
            access_denied('contracts');
        }
        $success = $this->contracts_model->send_contract_to_client($id, $this->input->post('attach_pdf'), $this->input->post('cc'));
        if ($success) {
            set_alert('success', _l('contract_sent_to_client_success'));
        } else {
            set_alert('danger', _l('contract_sent_to_client_fail'));
        }
        redirect(admin_url(SAM_MODULE.'/contracts/contract/' . $id. '/'. $sam_id));
    }

    public function add_note($rel_id)
    {
        if ($this->input->post() && (staff_can('view',  'contracts') || staff_can('view_own',  'contracts'))) {
            $this->misc_model->add_note($this->input->post(), 'contract', $rel_id);
            echo $rel_id;
        }
    }

    public function get_notes($id)
    {
        if ((staff_can('view',  'contracts') || staff_can('view_own',  'contracts'))) {
            $data['notes'] = $this->misc_model->get_notes($id, 'contract');
            $this->load->view('admin/includes/sales_notes_template', $data);
        }
    }

    public function clear_signature($id,$sam_id='')
    {
        if (staff_can('delete',  'contracts')) {
            $this->contracts_model->clear_signature($id);
        }

        redirect(admin_url(SAM_MODULE.'/contracts/contract/' . $id.'/'.$sam_id));
    }

    public function save_contract_data()
    {
        if (staff_cant('edit', 'contracts')) {
            header('HTTP/1.0 400 Bad error');
            echo json_encode([
                'success' => false,
                'message' => _l('access_denied'),
            ]);
            die;
        }

        $success = false;
        $message = '';

        $this->db->where('id', $this->input->post('contract_id'));
        $this->db->update(db_prefix() . 'contracts', [
                'content' => html_purify($this->input->post('content', false)),
        ]);

        $success = $this->db->affected_rows() > 0;
        $message = _l('updated_successfully', _l('contract'));

        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]);
    }

    public function add_comment()
    {
        if ($this->input->post()) {
            echo json_encode([
                'success' => $this->contracts_model->add_comment($this->input->post()),
            ]);
        }
    }

    public function edit_comment($id)
    {
        if ($this->input->post()) {
            echo json_encode([
                'success' => $this->contracts_model->edit_comment($this->input->post(), $id),
                'message' => _l('comment_updated_successfully'),
            ]);
        }
    }

    public function get_comments($id)
    {
        $data['comments'] = $this->contracts_model->get_comments($id);
        $this->load->view(SAM_MODULE.'/contracts/comments_template', $data);
    }

    public function remove_comment($id)
    {
        $this->db->where('id', $id);
        $comment = $this->db->get(db_prefix() . 'contract_comments')->row();
        if ($comment) {
            if ($comment->staffid != get_staff_user_id() && !is_admin()) {
                echo json_encode([
                    'success' => false,
                ]);
                die;
            }
            echo json_encode([
                'success' => $this->contracts_model->remove_comment($id),
            ]);
        } else {
            echo json_encode([
                'success' => false,
            ]);
        }
    }

    public function renew()
    {
        if (staff_cant('edit', 'contracts')) {
            access_denied('contracts');
        }
        if ($this->input->post()) {
            $data    = $this->input->post();
            $sam_id  = $data['sam_id'];
            unset($data['sam_id']);
            $success = $this->contracts_model->renew($data);
            if ($success) {
                set_alert('success', _l('contract_renewed_successfully'));
            } else {
                set_alert('warning', _l('contract_renewed_fail'));
            }
            redirect(admin_url(SAM_MODULE.'/contracts/contract/' . $data['contractid'] . '/'. $sam_id. '?tab=renewals'));
        }
    }

    public function delete_renewal($renewal_id, $contractid, $sam_id = '')
    {
        $success = $this->contracts_model->delete_renewal($renewal_id, $contractid);
        if ($success) {
            set_alert('success', _l('contract_renewal_deleted'));
        } else {
            set_alert('warning', _l('contract_renewal_delete_fail'));
        }
        //redirect(admin_url(SAM_MODULE.'/contracts/contract/' . $contractid . '?tab=renewals'));
        redirect(admin_url(SAM_MODULE.'/contracts/contract/' . $contractid .'/'. $sam_id.'?tab=renewals'));
    }

    public function copy($id,$sam_id='')
    {
        if (staff_cant('create', 'contracts')) {
            access_denied('contracts');
        }
        if (!$id) {
            redirect(admin_url(SAM_MODULE.'/contracts'));
        }
        $newId = $this->contracts_model->copy($id);
        if ($newId) {
            set_alert('success', _l('contract_copied_successfully'));
        } else {
            set_alert('warning', _l('contract_copied_fail'));
        }
        redirect(admin_url(SAM_MODULE.'/contracts/contract/' . $newId.'/'.$sam_id));
    }

    /* Manage contract types Since Version 1.0.3 */
    public function type($id = '')
    {
        if (!is_admin() && get_option('staff_members_create_inline_contract_types') == '0') {
            access_denied('contracts');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->contracts_model->add_contract_type($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('contract_type'));
                }
                echo json_encode([
                    'success' => $success,
                    'message' => $message,
                    'id'      => $id,
                    'name'    => $this->input->post('name'),
                ]);
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->contracts_model->update_contract_type($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('contract_type'));
                }
                echo json_encode([
                    'success' => $success,
                    'message' => $message,
                ]);
            }
        }
    }

    public function types()
    {
        if (!is_admin()) {
            access_denied('contracts');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('contract_types');
        }
        $data['title'] = _l('contract_types');
        $this->load->view(SAM_MODULE.'/contracts/manage_types', $data);
    }

    /* Delete announcement from database */
    public function delete_contract_type($id)
    {
        if (!$id) {
            redirect(admin_url(SAM_MODULE.'/contracts/types'));
        }
        if (!is_admin()) {
            access_denied('contracts');
        }
        $response = $this->contracts_model->delete_contract_type($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('contract_type_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('contract_type')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('contract_type_lowercase')));
        }
        redirect(admin_url(SAM_MODULE.'/contracts/types'));
    }

    public function add_contract_attachment($id)
    {
        handle_contract_attachment($id);
    }

    public function add_external_attachment()
    {
        if ($this->input->post()) {
            $this->misc_model->add_attachment_to_database(
                $this->input->post('contract_id'),
                'contract',
                $this->input->post('files'),
                $this->input->post('external')
            );
        }
    }

    public function delete_contract_attachment($attachment_id)
    {
        $file = $this->misc_model->get_file($attachment_id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo json_encode([
                'success' => $this->contracts_model->delete_contract_attachment($attachment_id),
            ]);
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
        $this->load->view(SAM_MODULE.'/contracts/invoice_convert_template', $data);
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
    
    public function convert_to_invoice($id,$sam_id="")
    {
        if (staff_cant('create', 'invoices')) {
            access_denied('invoices');
        }
        if ($this->input->post()) {
            //echo "<pre>"; print_r($this->input->post()); exit;
            $sam_id = $this->input->post('sam_id');
            $contract_id = $this->input->post('contract_id');
            $this->load->model('invoices_model');
            $invoice_id = $this->invoices_model->add($this->input->post());
            if ($invoice_id) {
                set_alert('success', _l('proposal_converted_to_invoice_success'));
                $this->db->where('sam_id', $sam_id);
                $this->db->where('id', $contract_id);
                $this->db->update(db_prefix() . 'contracts', [
                    'invoice_id' => $invoice_id,
                    //'status'     => 3,
                ]);
                $this->db->where('sam_id', $sam_id);
                $this->db->where('id', $id);
                $this->db->update(db_prefix() . 'proposals', [
                    'invoice_id' => $invoice_id,
                    //'status'     => 3,
                ]);
                log_activity('Contract Converted to Invoice [InvoiceID: ' . $invoice_id . ', ProposalID: ' . $id . ']');

                do_action_deprecated('proposal_converted_to_invoice', ['proposal_id' => $id, 'invoice_id' => $invoice_id], '3.1.6', 'after_proposal_converted_to_invoice');
                hooks()->do_action('after_proposal_converted_to_invoice', ['proposal_id' => $id, 'invoice_id' => $invoice_id]);

                redirect(admin_url(SAM_MODULE.'/invoices/#' . $invoice_id));
                //redirect(admin_url(SAM_MODULE.'/details/'.$sam_id.'/invoices'));
            } else {
                set_alert('danger', _l('proposal_converted_to_invoice_fail'));
            }
            if ($this->set_proposal_pipeline_autoload($id)) {
                //redirect(admin_url(SAM_MODULE.'/details/'.$sam_id.'/invoices'));
            } else {
                //redirect(admin_url(SAM_MODULE.'/details/'.$sam_id.'/invoices'));
            }
            redirect(admin_url(SAM_MODULE.'/details/'.$sam_id.'/invoices'));
        }
    }

}
