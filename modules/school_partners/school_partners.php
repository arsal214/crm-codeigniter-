<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: School Partners
Description: Module will Generate Filters for Lead and save filters as Templates for future use.
Author: <a href="https://www.linkedin.com/in/muhammad-sufyan-11bb93b1/" target="_blank">Muhammad Sufyan</a>
Version: 1.0
Requires at least: 1.0.*
*/

define('SCHOOL_PARTNERS_MODULE_NAME', 'school_partners');



$CI = &get_instance();

hooks()->add_action('admin_init', 'school_partners_init_menu_items');
hooks()->add_action('admin_init', 'school_partners_permissions');

/**
* Load the module helper
*/
// echo "";

$CI->load->helper(SCHOOL_PARTNERS_MODULE_NAME . '/school_partners');

/**
* Load the module Model
*/
$CI->load->model(SCHOOL_PARTNERS_MODULE_NAME . '/school_partners_model');

/**
* Register activation module hook
*/
register_activation_hook(SCHOOL_PARTNERS_MODULE_NAME, 'school_partners_activation_hook');

function school_partners_activation_hook()
{
	$CI = &get_instance();
	require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(SCHOOL_PARTNERS_MODULE_NAME, [SCHOOL_PARTNERS_MODULE_NAME]);

/**
 * Init menu setup module menu items in setup in admin_init hook
 * @return null
 */
function school_partners_init_menu_items()
{
	/**
	* If the logged in user is administrator, add custom Reports in Sidebar, if want to add menu in Setup then Write Setup instead of sidebar in menu ceation
	*/
	if (is_admin() || has_permission('school_partners', '', 'view')) {
	
		$CI = &get_instance();
		// $CI->app_menu->add_sidebar_menu_item('school_partners', [
		// 	'collapse'	=> true,
		// 	'icon'		=> 'fa fa-filter',
		// 	'name'		=> _l('Schools Leads'),
		// 	'position'	=> 46,
		// ]);
		// $CI->app_menu->add_sidebar_children_item('school_partners', [
        //     'name'     => _l('Leads'),
        //     'href'     => admin_url('school_partners'),
        //     'icon'     => 'fa fa-tty',
        //     'position' => 11,
        //     'badge'    => [],
        // ]);
        // $CI->app_menu->add_sidebar_children_item("school_partners", [
        //     'name'     => "Source",
        //     'href'     => admin_url('school_partners/sources'),
        //     'position' => 12,
        //     'icon'     => 'fa-regular fa-user',
        //     'badge'    => [],
        // ]);
		// $CI->app_menu->add_sidebar_children_item("school_partners", [
        //     'name'     => "Statuses",
        //     'href'     => admin_url('school_partners/statuses'),
        //     'position' => 12,
        //     'icon'     => 'fa-regular fa-user',
        //     'badge'    => [],
        // ]);
		$CI->app_menu->add_sidebar_menu_item("school_partners", [
            'name'     => "School Partners",
            'href'     => admin_url('school_partners'),
            'position' => 5,
            'icon'     => 'fa-regular fa-user',
            'badge'    => [],
        ]);
	}
     if (is_admin()) {
		$CI->app_menu->add_setup_menu_item('Schools Partners', [
            'collapse' => true,
            'name'     => 'Schools Partners',
            'position' => 10,
            'badge'    => [],
        ]);

        $CI->app_menu->add_setup_children_item('Schools Partners', [
            'slug'     => 'customer-groups',
            'name'     => 'Schools Partners Groups',
            'href'     => admin_url('school_partners/groups'),
            'position' => 5,
            'badge'    => [],
        ]);
		// $CI->app_menu->add_setup_children_item('school_partners', [
        //     'slug'     => 'customer-groups',
        //     'name'     => 'Schools Partners Groups',
        //     'href'     => admin_url('school_partners/school_partner_group'),
        //     'position' => 6,
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
function school_partners_permissions()
{
	$capabilities = [];
	$capabilities['capabilities'] = [
		'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
		'create' => _l('permission_create'),
	];
	register_staff_capabilities('school_partners', $capabilities, _l('school_partners'));
}
