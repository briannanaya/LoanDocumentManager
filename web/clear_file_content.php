<?php
$directory = "/var/www/html/view";

if (is_dir($directory)) {
    $files = glob($directory . '/*'); // get all files in view
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file); // delete file 
            echo "Deleted: " . $file . "<br>";
        }
    }
    echo "All files in the directory have been deleted.";
} else {
    echo "Directory does not exist.";
}
?>

