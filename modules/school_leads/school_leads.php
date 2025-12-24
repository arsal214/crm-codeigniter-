<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: School Leads
Description: Module will Generate Filters for Lead and save filters as Templates for future use.
Author: <a href="https://www.linkedin.com/in/muhammad-sufyan-11bb93b1/" target="_blank">Muhammad Sufyan</a>
Version: 1.0
Requires at least: 1.0.*
*/

define('SCHOOL_LEADS_MODULE_NAME', 'school_leads');

$CI = &get_instance();

hooks()->add_action('admin_init', 'school_leads_init_menu_items');
hooks()->add_action('admin_init', 'school_leads_permissions');

/**
* Load the module helper
*/
// echo "";

$CI->load->helper(SCHOOL_LEADS_MODULE_NAME . '/school_leads');

/**
* Load the module Model
*/
$CI->load->model(SCHOOL_LEADS_MODULE_NAME . '/school_leads_model');

/**
* Register activation module hook
*/
register_activation_hook(SCHOOL_LEADS_MODULE_NAME, 'school_leads_activation_hook');

function school_leads_activation_hook()
{
	$CI = &get_instance();
	require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(SCHOOL_LEADS_MODULE_NAME, [SCHOOL_LEADS_MODULE_NAME]);

/**
 * Init menu setup module menu items in setup in admin_init hook
 * @return null
 */
function school_leads_init_menu_items()
{
	/**
	* If the logged in user is administrator, add custom Reports in Sidebar, if want to add menu in Setup then Write Setup instead of sidebar in menu ceation
	*/
	if (is_admin() || has_permission('school_leads', '', 'view')) {
		$CI = &get_instance();
		// $CI->app_menu->add_sidebar_menu_item('school_leads', [
		// 	'collapse'	=> true,
		// 	'icon'		=> 'fa fa-filter',
		// 	'name'		=> _l('Schools Leads'),
		// 	'position'	=> 46,
		// ]);
		$CI->app_menu->add_sidebar_menu_item('school_leads', [
            'name'     => "School Leads",
            'href'     => admin_url('school_leads'),
            'icon'     => 'fa fa-tty',
            'position' => 46,
            'badge'    => [],
        ]);
	}
        	    if (is_admin()) {
		$CI->app_menu->add_setup_menu_item('School Leads', [
            'collapse' => true,
            'name'     => 'School Leads',
            'position' => 20,
            'badge'    => [],
        ]);
        $CI->app_menu->add_setup_children_item('School Leads', [
            'name'     => "Source",
            'href'     => admin_url('school_leads/sources'),
            'position' => 5,
            'icon'     => 'fa-regular fa-user',
            'badge'    => [],
        ]);
        $CI->app_menu->add_setup_children_item('School Leads', [
            'name'     => "Statuses",
            'href'     => admin_url('school_leads/statuses'),
            'position' => 10,
            'icon'     => 'fa-regular fa-user',
            'badge'    => [],
        ]);
        // $CI->app_menu->add_sidebar_children_item("school_leads", [
        //     'name'     => "Source",
        //     'href'     => admin_url('school_leads/sources'),
        //     'position' => 12,
        //     'icon'     => 'fa-regular fa-user',
        //     'badge'    => [],
        // ]);
		// $CI->app_menu->add_sidebar_children_item("school_leads", [
        //     'name'     => "Statuses",
        //     'href'     => admin_url('school_leads/statuses'),
        //     'position' => 12,
        //     'icon'     => 'fa-regular fa-user',
        //     'badge'    => [],
        // ]);
		// $CI->app_menu->add_sidebar_menu_item("school_partners", [
        //     'name'     => "qwertyuiop",
        //     'href'     => admin_url('school_partners'),
        //     'position' => 5,
        //     'icon'     => 'fa-regular fa-user',
        //     'badge'    => [],
        // ]);
		// $CI->app_menu->add_sidebar_children_item('lead-filters', [
		// 	'slug'		=> 'si-lead-tmplate-options',
		// 	'name'		=> _l('si_lf_submenu_filter_templates'),
		// 	'href'		=> admin_url('si_lead_filters/list_filters'),
		// 	'position'	=> 10,
		// ]);
	// }
	    }
	    
}
function school_leads_permissions()
{
	$capabilities = [];
	$capabilities['capabilities'] = [
		'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
		'create' => _l('permission_create'),
	];
	register_staff_capabilities('school_leads', $capabilities, _l('school_leads'));
}
