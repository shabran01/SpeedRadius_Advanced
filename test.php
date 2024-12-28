<?php
$new_folder = "/var/www/html/test_folder"; // Test folder name

if (!file_exists($new_folder)) {
    if (!mkdir($new_folder, 0755, true)) {
        echo "Failed to create directory: $new_folder";
        print_r(error_get_last());
    } else {
        echo "Directory created: $new_folder";
    }
}
?>
