<?php
// Include database settings
require_once("settings.php");

// Try to connect
$conn = @mysqli_connect($host, $user, $pwd, $sql_db);

// Check connection
if (!$conn) {
    echo "<p>Database connection failed: " . mysqli_connect_error() . "</p>";
} else {
    echo "<p>✅ Successfully connected to the database!</p>";
    mysqli_close($conn); // Always close connection when done
}
?>
