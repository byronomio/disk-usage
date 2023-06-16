<?php
// Register the admin menu and its sub-menu for the disk usage plugin
add_action('admin_menu', 'disk_usage_register_menus');

function disk_usage_register_menus() {
    add_menu_page('Disk Usage', 'View Disk Usage', 'manage_options', 'disk-usage', 'disk_usage_page', 'dashicons-chart-area', 99);
    add_submenu_page('disk-usage', 'Disk Usage Settings', 'Settings', 'manage_options', 'disk-usage-settings', 'disk_usage_settings_page');
}
