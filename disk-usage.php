<?php
/**
 * Plugin Name: Disk Usage
 * Description: Disk Usage is a powerful space management plugin for WordPress. It provides a detailed view of the disk usage by different directories within the 'wp-content' directory, helping website administrators effectively manage their storage space. With its in-built functionality to sort by file size, it allows quick identification of the biggest files and directories. An additional deletion feature is also available for managing space, with a safety confirmation check to prevent accidental file deletions. Disk Usage - keeping your WordPress storage clean and efficient.
 * Version: 1.0
 * Author: Byron Jacobs
 * Author URI: https://byronjacobs.co.za
 * License: GPLv2 or later
 * Text Domain: disk-usage
 */

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/menus.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/settings.php';
