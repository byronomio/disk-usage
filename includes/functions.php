<?php
// Display disk usage page
function disk_usage_page() {
    $scanDir = get_option('disk_usage_scan_directory', WP_CONTENT_DIR);

    echo '<h2>Disk Usage</h2>';
    echo '<table id="disk-usage-table">';
    echo '<thead><tr><th>Path</th><th>Size</th><th>Actions</th></tr></thead><tbody>';

    getDirContents($scanDir);

    echo '</tbody></table>';
    echo <<<EOT
    <script>
        jQuery(document).ready(function() {
            // Apply DataTables library to our table
            jQuery('#disk-usage-table').DataTable({ "order": [[1, 'desc']] });

            // Confirm before deleting a file or directory
            jQuery('a.delete-link').click(function(e) {
                if (!confirm('Are you sure you want to delete this file or directory? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
        });
    </script>
    EOT;
}

// Get the directory contents and echo as table rows
function getDirContents($dir) {
    $sizeList = [];
    $files = scandir($dir);
    $displayType = get_option('disk_usage_display_type', 'both');

    // Gather files and directories as per user selection
    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!in_array($value, ['.', '..']) && is_valid_file_or_dir($displayType, $path)) {
            $sizeList[] = ['path' => $path, 'size' => (is_dir($path) ? getDirectorySize($path) : filesize($path))];
        }
    }

    // Sort and limit the size list
    usort($sizeList, function($a, $b) { return $b['size'] - $a['size']; });
    $sizeList = array_slice($sizeList, 0, intval(get_option('disk_usage_max_directories', 50)));

    // Echo the table rows
    foreach ($sizeList as $item) {
        echo "<tr><td>{$item['path']}</td><td data-sort='{$item['size']}'>" . formatSizeUnits($item['size']) . "</td>";
        echo "<td><a class='delete-link' href='" . get_admin_url(null, 'admin.php?page=disk-usage&delete=' . urlencode($item['path'])) . "'>Delete</a></td></tr>";
    }
}

// Check if the path is a valid file or directory based on the user selection
function is_valid_file_or_dir($displayType, $path) {
    return ($displayType == 'files' && is_file($path)) || ($displayType == 'directories' && is_dir($path)) || $displayType == 'both';
}

// Calculate the total size of a directory
function getDirectorySize($dir) {
    $size = 0;
    $files = glob(rtrim($dir, '/').'/*', GLOB_NOSORT);

    foreach($files as $file){
        $size += (is_file($file) ? filesize($file) : getDirectorySize($file));
    }

    return $size;
}

// Format size from bytes to human readable string
function formatSizeUnits($bytes) {
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}

// Delete a file or directory if it is within wp-content
if (isset($_GET['delete'])) {
    $pathToDelete = realpath(urldecode($_GET['delete']));

    // Check if the path is within wp-content and not the wp-content directory itself
    if (strpos($pathToDelete, WP_CONTENT_DIR) === 0 && $pathToDelete != WP_CONTENT_DIR) {
        // Check if the path exists before attempting to delete
        if (file_exists($pathToDelete)) {
            if (is_dir($pathToDelete)) {
                rrmdir($pathToDelete);
            } else {
                unlink($pathToDelete);
            }
        }
    }
}


// Recursively remove a directory
function rrmdir($dir) {
   if (is_dir($dir)) {
     $objects = scandir($dir);

     foreach ($objects as $object) {
       if (!in_array($object, ['.', '..'])) {
         $file = $dir . "/" . $object;
         (is_dir($file) ? rrmdir($file) : unlink($file));
       }
     }

     rmdir($dir);
   }
}

// Enqueue DataTables CSS and JS
function load_datatables() {
    wp_register_style('datatables', 'https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css');
    wp_enqueue_style('datatables');

    wp_register_script('datatables', 'https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js', array('jquery'), null, true);
    wp_enqueue_script('datatables');
}

add_action('admin_enqueue_scripts', 'load_datatables');
