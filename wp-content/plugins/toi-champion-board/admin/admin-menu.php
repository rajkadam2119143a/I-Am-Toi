<?php
defined('ABSPATH') || exit;

add_action('admin_menu', 'tcb_register_admin_menu');

function tcb_register_admin_menu() {
    // Main menu
    add_menu_page(
        'TOI Champion Board',
        'Champion Board',
        'manage_options',
        'tcb-import-data',
        'tcb_admin_import_page',
        'dashicons-awards',
        26
    );

    // Submenu: Import Data (default)
    add_submenu_page(
        'tcb-import-data',
        'Import Data',
        'Import Data',
        'manage_options',
        'tcb-import-data',
        'tcb_admin_import_page'
    );

    // Submenu: Success Stories
    add_submenu_page(
        'tcb-import-data',
        'Success Stories',
        'Success Stories',
        'manage_options',
        'tcb-success-stories',
        'tcb_admin_success_stories_page'
    );

    // Submenu: Campaign Events
    add_submenu_page(
        'tcb-import-data',
        'Campaign Events',
        'Campaign Events',
        'manage_options',
        'tcb-campaign-events',
        'tcb_admin_campaign_events_page'
    );
}

// Include individual page renderers
require_once plugin_dir_path(__FILE__) . 'dashboard-import-page.php';
require_once plugin_dir_path(__FILE__) . 'success-stories-page.php';
require_once plugin_dir_path(__FILE__) . 'campaign-events-page.php';
