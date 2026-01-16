<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Clients extends AdminController
{    
    /* add new client*/
    public function client($id = '')
    {
        // echo"gothere";
        // die;
        $data['staff'] = $this->staff_model->get('', ['active' => 1]);
        
        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();
        //echo "<pre>"; print_r($data['currencies']); exit;
        // Customer groups
        $data['groups'] = $this->clients_model->get_groups();
        //echo "<pre>"; print_r($data['groups']); exit;
        $this->load->view(SAM_MODULE.'/add_client', $data);
    }
    
   public function save_client()
{
    $this->load->model('sam_model');
    $data = $this->input->post();
    $groupid = $this->input->post('groups_in');
    unset($data['groups_in']);
    $data['addedfrom'] = get_staff_user_id();
    $data['datecreated'] = date('Y-m-d h:i:s');

    $this->sam_model->_table_name = "tblclients"; // table name
    $this->sam_model->_primary_key = "userid"; // primary key
    $id = 0;
    $company_name = trim($data['company']); 
    $id = $this->sam_model->save_sam($data);
    $success = false;

    if ($id) {
        // Add groups if provided
        if (is_array($groupid)) {
            for ($i = 0; $i < count($groupid); $i++) {
                $group_data[$i]['groupid'] = $groupid[$i];
                $group_data[$i]['customer_id'] = $id;
                $this->sam_model->addClientInGroup($group_data[$i]);
            }
        }

        // Insert into tblcustomer_admins
        $customer_admin_data = [
            'staff_id' => $data['addedfrom'],
            'customer_id'   => $id,
            'date_assigned' => $data['datecreated']
        ];
        $this->sam_model->_table_name = "tblcustomer_admins"; // Set table for customer admins
        $this->sam_model->save_sam($customer_admin_data); // Save data into tblcustomer_admins

        $type = 'success';
        $msg = 'Added New Customer Successfully';
        $success = true;
    } else {
        $type = 'danger';
        $msg = 'Added New Customer Failed';
        $success = false;
    }

    set_alert($type, $msg);
    echo json_encode([
        'userid'  => $id,
        'company' => $company_name,
        'success' => $success,
        'type'    => $type,
        'message' => $msg
    ]);
}

    
    function addContact($client_id=0, $sam_id=0)
    {
        $this->load->model('sam_model');
        //print_r($this->input->post());exit;
        //$client_id = $this->input->post('client_id');
        //get client information
        $cond = array('userid' => $client_id);
        $client_res = $this->sam_model->getAllRecords('tblclients','*',$cond);
        //echo "<pre>"; print_r($client_res); exit;
        if($client_res && isset($client_res[0])){
            $client_res = $client_res[0];
            $data['client_res'] = $client_res;
            $data['sam_id'] = $sam_id;
            echo $this->load->view(SAM_MODULE.'/add_contact', $data);
        }
    }
    
    function save_contact($customer_id)
    {
        
        $data             = $this->input->post();
        $data['password'] = $this->input->post('password', false);  
        //echo "<pre>"; print_r($_FILES); exit;
        if (is_automatic_calling_codes_enabled()) {
            $clientCountryId = $this->db->select('country')
                ->where('userid', $customer_id)
                ->get('clients')->row()->country ?? null;

            $clientCountry = get_country($clientCountryId);
            $callingCode   = $clientCountry ? '+' . ltrim($clientCountry->calling_code, '+') : null;
        } else {
            $callingCode = null;
        } 
        
        if ($callingCode && !empty($data['phonenumber']) && $data['phonenumber'] == $callingCode) {
            $data['phonenumber'] = '';
        }

        unset($data['contactid']); 
        
        $sam_id = $this->input->post('sam_id');

        $id      = $this->clients_model->add_contact($data, $customer_id);
        $message = '';
        $success = false;
        if ($id) {
            handle_contact_profile_image_upload($id);
            $success = true;
            $message = _l('added_successfully', _l('contact'));

            // Log activity transaction if contact is added from a lead
            if (!empty($sam_id) && $sam_id > 0) {
                add_activity_transactions($sam_id, 'added contact');
            }
        }
        echo json_encode([
            'success'           => $success,
            'message'           => $message, 
            'contact_id'        => $id,
            'contact_name'      => $data['firstname'].' '.$data['lastname'],                             
        ]);
        die; 
    }
    
    function contact_email_exists()
    {
        $this->db->where('email', $this->input->post('email'));
        $total_rows = $this->db->count_all_results(db_prefix() . 'contacts');
        if ($total_rows > 0) {
            echo json_encode(false);
        } else {
            echo json_encode(true);
        }
        die();
        
    }
    
}
