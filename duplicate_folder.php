<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the new folder name from the form
    $newFolderName = escapeshellcmd($_POST['folder_name']);

    // Define the source and destination paths
    $sourcePath = '/var/www/html/ISP';
    $destinationPath = "/var/www/html/$newFolderName";

    // Check if the new folder already exists
    if (file_exists($destinationPath)) {
        $message = "Error: Folder already exists.";
    } else {
        // Create the new directory
        if (mkdir($destinationPath, 0755, true)) {
            // Copy the contents of the source directory to the new directory
            $command = "cp -r $sourcePath/* $destinationPath/";
            $output = shell_exec($command);

            if ($output === null) {
                $message = "Folder duplicated successfully.";
            } else {
                $message = "Error: " . htmlspecialchars($output);
            }
        } else {
            $message = "Error: Could not create the new folder.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Duplicate Folder</title>
</head>
<body>
    <h1>Duplicate ISP Folder</h1>
    <?php
    if (isset($message)) {
        echo "<p>$message</p>";
    }
    ?>
    <form action="index.php" method="post">
        <label for="folder_name">Enter new folder name:</label>
        <input type="text" id="folder_name" name="folder_name" required>
        <input type="submit" value="Duplicate Folder">
    </form>
</body>
</html>
