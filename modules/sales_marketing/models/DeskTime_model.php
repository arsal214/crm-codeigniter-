<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DeskTime_model extends App_Model {
    private $api_key;

    public function __construct()
    {
        parent::__construct();
        $this->api_key = $this->config->item('desktime_api_key'); // Ensure your API key is set in the config
    }

    // Function to fetch DeskTime company data
    public function fetchCompanyData() {
        $url = 'https://desktime.com/api/v2/json/employees?apiKey=' . $this->api_key;

        // Setup cURL request
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json'
            ],
            CURLOPT_SSL_VERIFYPEER => false // Disable SSL verification (optional)
        ]);

        // Execute cURL request and capture the response
        $response = curl_exec($ch);

        // Get HTTP status code
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check for cURL errors
        if(curl_errno($ch)) {
            log_message('error', 'cURL error: ' . curl_error($ch));
            curl_close($ch);
            return null; // Or handle error as needed
        }

        curl_close($ch);

        // Log the response and HTTP code for debugging
        log_message('debug', 'DeskTime API Response: ' . $response);
        log_message('debug', 'HTTP Status Code: ' . $http_code);

        // Check if the API request was successful
        if ($http_code == 200) {
            return json_decode($response, true); // Decode and return response as array
        } else {
            log_message('error', "Error fetching data from DeskTime API. HTTP Status Code: " . $http_code);
            return null;
        }
    }
}
?>
