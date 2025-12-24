<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DeskTime extends AdminController {

    public function __construct() {
        parent::__construct();
        $this->load->model('DeskTime_model'); // Load the DeskTime_model
    }

    public function index() {
        // Fetch DeskTime company data using the model
        $desktime_data = $this->DeskTime_model->fetchCompanyData();

        // Check if data is retrieved successfully
        if ($desktime_data) {
            // Pass data to view
            $data['desktime_data'] = $desktime_data;
            $this->load->view('desktime_view', $data);
        } else {
            // Handle the error if no data is retrieved
            $data['error'] = "Unable to fetch data from DeskTime API.";
            $this->load->view('desktime_view', $data);
        }
    }
}
?>
