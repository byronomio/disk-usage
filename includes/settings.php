<?php
// Initialize disk usage settings
function disk_usage_settings_init() {
    // Register new settings
    register_setting('disk-usage-settings', 'disk_usage_max_directories');
    register_setting('disk-usage-settings', 'disk_usage_scan_directory');
    register_setting('disk-usage-settings', 'disk_usage_display_type');


    // Add a new section to the settings page
    add_settings_section('disk_usage_settings_section', 'Disk Usage Settings', null, 'disk-usage-settings');

    // Add settings fields to the section
    add_settings_field('disk_usage_max_directories_field', 'Max Directories to Display in Disk Usage', 'disk_usage_max_directories_render', 'disk-usage-settings', 'disk_usage_settings_section');
    add_settings_field('disk_usage_scan_directory_field', 'Directory to scan', 'disk_usage_scan_directory_render', 'disk-usage-settings', 'disk_usage_settings_section');
    add_settings_field('disk_usage_display_type_field', 'Display Files, Directories, or Both', 'disk_usage_display_type_render', 'disk-usage-settings', 'disk_usage_settings_section');
}

// Render the max directories field
function disk_usage_max_directories_render() {
    $setting = get_option('disk_usage_max_directories');
    echo "<input type='text' name='disk_usage_max_directories' value='$setting'>";
}

// Render the scan directory field
function disk_usage_scan_directory_render() {
    $setting = get_option('disk_usage_scan_directory', WP_CONTENT_DIR);
    echo "<input type='text' name='disk_usage_scan_directory' value='$setting'>";
}

// Render the display type field
function disk_usage_display_type_render() {
    $setting = get_option('disk_usage_display_type', 'both');
    $options = ['files' => 'Files', 'directories' => 'Directories', 'both' => 'Both'];

    echo "<select name='disk_usage_display_type'>";
    foreach ($options as $value => $label) {
        $selected = ($setting == $value) ? 'selected' : '';
        echo "<option value='{$value}' {$selected}>{$label}</option>";
    }
    echo "</select>";
}

// Display the settings page
function disk_usage_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    if (isset($_GET['settings-updated'])) {
        add_settings_error('disk_usage_messages', 'disk_usage_message', __('Settings Saved', 'disk-usage'), 'updated');
    }

    settings_errors('disk_usage_messages');

    echo '<div class="wrap">';
    echo '<form action="options.php" method="post">';
    
    settings_fields('disk-usage-settings');
    do_settings_sections('disk-usage-settings');

    submit_button('Save Settings');
    
    echo '</form>';
    echo '</div>';
}

// Initiate settings
add_action('admin_init', 'disk_usage_settings_init');
