<?php
/**
 * Plugin Name: TOI Champion Board
 * Description: Custom dashboard with dynamic data for TOI campaign.
 * Version: 1.1
 * Author: Raj Kadam
 */

defined('ABSPATH') || exit;

// FRONTEND ASSETS
add_action('wp_enqueue_scripts', 'load_dashboard_assets');
function load_dashboard_assets() {
    wp_enqueue_style('dashboard-css', plugin_dir_url(__FILE__) . 'assets/css/dashboard.css', [], '1.0');
    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', [], null, true);
    wp_enqueue_script('dashboard-js', plugin_dir_url(__FILE__) . 'assets/js/dashboard.js', ['chart-js'], '1.0', true);
}

// SHORTCODE TO DISPLAY FRONTEND DASHBOARD
add_action('init', function () {
    add_shortcode('champion_board', 'tcb_render_dashboard');
});

// LOAD FRONTEND RENDER FUNCTION
require_once plugin_dir_path(__FILE__) . 'includes/dashboard.php';

// LOAD ADMIN PAGES
if (is_admin()) {
    require_once plugin_dir_path(__FILE__) . 'admin/admin-menu.php';
}
