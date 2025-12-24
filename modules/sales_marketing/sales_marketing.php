<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
Module Name: Sales and Marketing
Description: Sales and Marketing module for Perfex CRM will allow you to manage your channel, pipelines etc.
Version: 1.0
Requires at least: 2.3.*
*/

define('SAM_MODULE', 'sales_marketing');

$CI = &get_instance();
register_language_files(SAM_MODULE, [SAM_MODULE]);
$CI->load->helper(SAM_MODULE . '/sam');
hooks()->add_action('admin_init', 'sales_marketing_init_menu_items');
register_activation_hook(SAM_MODULE, 'sales_marketing_activation_hook');
register_deactivation_hook(SAM_MODULE, 'sales_marketing_deactivation_hook');
register_uninstall_hook(SAM_MODULE, 'sales_marketing_uninstall_hook');
hooks()->add_action('admin_init', 'sales_marketing_permissions');

hooks()->add_action('task_modal_rel_type_select', 'sales_marketing_task_modal_rel_type_select');
//hooks()->add_action('task_related_to_select', 'sales_marketing_related_to_select');
//hooks()->add_filter('init_relation_options', 'sales_marketing_init_relation_options');
//hooks()->add_filter('relation_values', 'sales_marketing_relation_values');
hooks()->add_filter('before_return_relation_data', 'sales_marketing_relation_data', 10, 4); // old
//hooks()->add_filter('get_relation_data', 'sales_marketing_get_relation_data', 10, 4); // new
//hooks()->add_filter('tasks_table_row_data', 'sales_marketing_add_table_row', 10, 3);

hooks()->add_filter('global_search_result_output', 'sales_marketing_global_search_result_output', 10, 2);
hooks()->add_filter('global_search_result_query', 'sales_marketing_global_search_result_query', 10, 3);

hooks()->add_action('after_custom_fields_select_options', 'init_sales_marketing_custom_fields');


register_merge_fields('sales_marketing/merge_fields/deal_merge_fields');


function sales_marketing_relation_values($data)
{    
    $CI = &get_instance();
    $task_id = $CI->uri->segment(4);
    $rel_type = '';
    $rel_id = '';
    if ($CI->input->get('rel_id') && $CI->input->get('rel_type')) {
        $rel_id = $CI->input->get('rel_id');
        $rel_type = $CI->input->get('rel_type');
    }

    // get id from uri segment
    if ($data['type'] == 'sam') {
        if ($task_id != '') {
            $task = $CI->tasks_model->get($task_id);
            $rel_id = $task->rel_id;
            $rel_type = $task->rel_type;
        }
        if ($rel_type == 'sam') {
            $CI->db->from('tbl_sam');
            $CI->db->where('id', $rel_id);

            $deal = $CI->db->get()->row();
            $data = [
                'id' => $deal->id,
                'name' => $deal->title,
                'link' => admin_url('sales_marketing/deal/' . $deal->id),
                'addedfrom' => get_staff_user_id(),
                'subtext' => '',
                'type' => 'sam',
            ];
        }
    }


    return $data;
}

function sales_marketing_init_relation_options($data)
{    
    $CI = &get_instance();
    $type = $CI->input->post('type');
    $rel_id = $CI->input->post('rel_id');
    $q = $CI->input->post('q');
    if ($type == 'sam') {
        $CI->db->select('id, title');
        $CI->db->from('tbl_sam');
        $CI->db->where('title LIKE "%' . $q . '%"');
        if ($rel_id != '') {
            $CI->db->where('id != ' . $rel_id);
        }
        $deals = $CI->db->get()->result_array();
        $data = [];
        foreach ($deals as $deal) {
            $data[] = [
                'id' => $deal['id'],
                'name' => $deal['title'],
                'link' => admin_url('sales_marketing/deal/' . $deal['id']),
                'addedfrom' => 0,
                'subtext' => '',
                'type' => 'sam',
            ];
        }
    }
    return $data;

}


function sales_marketing_task_modal_rel_type_select($task)
{
    $type = $task['rel_type'];
    echo ' <option value="sam" 
    ' . ($type == 'deals' ? 'selected' : '') . '
 >
    ' . _l('sam') . '
                    </option>';
}

function sales_marketing_deactivation_hook()
{
    require_once(__DIR__ . '/deactive.php');
}

function sales_marketing_uninstall_hook()
{
    require_once(__DIR__ . '/uninstall.php');
}

function sales_marketing_activation_hook()
{
    require_once(__DIR__ . '/install.php');
}

function sales_marketing_init_menu_items()
{
    /**
     * If the logged in user is administrator, add custom menu in Setup
     */
    $CI = &get_instance();
    if (has_permission('sam', '', 'view')) {
    $CI->app_menu->add_sidebar_menu_item('sales_marketing', [
        //'name' => '<span class="text-white">' . _l('deals') . '</span>',
        'name' => 'Sales and Marketing',
        'position' => 4,
        'icon' => 'fa-solid fa-receipt menu-icon',
        'href' => admin_url('sales_marketing'),
    ]);

                                                   
    $CI->app_menu->add_sidebar_children_item('sales_marketing', [
        'slug' => 'sales_marketing_dashboard',
        'name' => _l('sam_dashboard'),
        'href' => admin_url('sales_marketing/dashboard/timesheets'),
        'position' => 1,
    ]);
    
    $CI->app_menu->add_sidebar_children_item('sales_marketing', [
    'slug' => 'sales_marketing_dashboard',
    'name' => 'Sales and Marketing',
    'icon' => '',
    'href' => admin_url('sales_marketing'),
    'position' => 2,
    ]);
           
    
    $CI->app_menu->add_sidebar_children_item('sales_marketing', [
        'slug' => 'sales_marketing_dashboard',
        'name' => 'Statistics',
        'icon' => '',
        'href' => admin_url('sales_marketing/dashboard/statistics'),
        'position' => 3,
    ]);
    
    $CI->app_menu->add_setup_menu_item('sales_marketing', [
        'collapse' => true,
        'name' => _l('sam'),
        'position' => 21,
        'badge' => [],
    ]);
             if (is_admin()) {
    $CI->app_menu->add_setup_children_item('sales_marketing', [
        'slug' => 'sam-sources',
        //'name' => _l('acs_leads_sources_submenu'),
        'name' => 'Channels',
        'href' => admin_url('sales_marketing/channels'),
        'position' => 5,
        'badge' => [],
    ]);
    $CI->app_menu->add_setup_children_item('sales_marketing', [
        'slug' => 'sam-pipelines',
        'name' => _l('sam_pipelines'),
        'href' => admin_url('sales_marketing/pipelines'),
        'position' => 10,
        'badge' => [],
    ]);
    $CI->app_menu->add_setup_children_item('sales_marketing', [
        'slug' => 'sam-stages',
        'name' => _l('sam_stages'),
        'href' => admin_url('sales_marketing/stages'),
        'position' => 10,
        'badge' => [],
    ]);
    $CI->app_menu->add_setup_children_item('sales_marketing', [
        'slug' => 'sam-settings',
        'name' => _l('sam_settings'),
        'href' => admin_url('sales_marketing/settings'),
        'position' => 10,
        'badge' => [],
    ]);
    $CI->app_menu->add_setup_children_item('sales_marketing', [
        'slug' => 'sam-status',
        'name' => _l('status'),
        'href' => admin_url('sales_marketing/status'),
        'position' => 11,
        'badge' => [],
    ]);
    $CI->app_menu->add_setup_children_item('sales_marketing', [
        'slug' => 'sam-customer-kpis',
        'name' => _l('KPI Function'),
        'href' => admin_url('sales_marketing/kpi_function'),
        'position' => 12,
        'badge' => [],
    ]);
    $CI->app_menu->add_setup_children_item('sales_marketing', [
        'slug' => 'sam-customer-kpis',
        'name' => _l('Employee KPI'),
        'href' => admin_url('sales_marketing/employee_kpi'),
        'position' => 13,
        'badge' => [],
    ]);
    $CI->app_menu->add_setup_children_item('sales_marketing', [
        'slug' => 'sam-countries',
        'name' => _l('Add Country'),
        'href' => admin_url('sales_marketing/country'),
        'position' => 14,
        'badge' => [],
    ]);
    $CI->app_menu->add_setup_children_item('sales_marketing', [
        'slug' => 'sam-assign-countries',
        'name' => _l('Assign Country To Employee'),
        'href' => admin_url('sales_marketing/assign_employee_country'),
        'position' => 15,
        'badge' => [],
    ]);
    $CI->app_menu->add_setup_children_item('sales_marketing', [
        'slug' => 'sam-categories',
        'name' => _l('Add Categories'),
        'href' => admin_url('sales_marketing/categories'),
        'position' => 16,
        'badge' => [],
    ]);
    $CI->app_menu->add_setup_children_item('sales_marketing', [
        'slug' => 'sam-desktime',
        'name' => _l('Desktime'),
        'href' => admin_url('sales_marketing/desktime'),
        'position' => 17,
        'badge' => [],
    ]);
    $CI->app_menu->add_setup_children_item('sales_marketing', [
        'slug' => 'sam-overallperformance',
        'name' => _l('Overall Performance'),
        'href' => admin_url('sales_marketing/overallperformance'),
        'position' => 18,
        'badge' => [],
    ]);
 }
}
}

function sales_marketing_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'view' => _l('permission_view'),
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('sam', $capabilities, 'Sales and Marketing');
}






// ALTER TABLE `tbltask_comments` ADD `dealsid` INT(11) NOT NULL DEFAULT '0' AFTER `taskid`;


