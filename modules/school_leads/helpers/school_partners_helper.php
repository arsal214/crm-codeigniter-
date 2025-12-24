<?php
defined('BASEPATH') or exit('No direct script access allowed');


function get_partners_user_id()
{
    if (!is_client_logged_in()) {
        return false;
    }

    return get_instance()->session->userdata('client_user_id');
}