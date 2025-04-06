<?php
$host = "feenix-mariadb.swin.edu.au";
$user = "s105724554";
$pwd  = "190198";
$sql_db = "s105724554_db";

$conn = @mysqli_connect($host, $user, $pwd, $sql_db);

if (!$conn) {
    die("<p style='color:red;'>Connection failed: " . mysqli_connect_error() . "</p>");
} else {
    echo "<p style='color:green;'>Connected to Feenix successfully!</p>";
    mysqli_close($conn);
}
?>
