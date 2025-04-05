<?php
require_once("settings.php");

$conn = @mysqli_connect($host, $user, $pwd, $sql_db);
if (!$conn) {
    die("<p>Database connection failed: " . mysqli_connect_error() . "</p>");
}

$query = "SELECT * FROM jobs";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Listings</title>
    <link rel="stylesheet" href="your-style.css"> <!-- replace with actual CSS if needed -->
</head>
<body>
    <h1>Available Job Listings</h1>

    <?php
    if (mysqli_num_rows($result) > 0) {
        while ($job = mysqli_fetch_assoc($result)) {
            echo "<div class='job-details'>";
            echo "<section>";
            echo "<h2>{$job['title']}</h2>";
            echo "<p><strong>Job Reference:</strong> {$job['jobRef']}</p>";
            echo "<p><strong>Salary Range:</strong> {$job['salary']}</p>";
            echo "<p><strong>Reports To:</strong> {$job['reportsTo']}</p>";
            echo "<h3>Position Description</h3>";
            echo "<p>{$job['description']}</p>";
            echo "<h3>Key Responsibilities</h3><ol>{$job['responsibilities']}</ol>";
            echo "<h4>Essential Skills</h4><ul>{$job['essentialSkills']}</ul>";
            echo "<h4>Preferable Skills</h4><ul>{$job['preferableSkills']}</ul>";
            echo "<p><strong>Closing Date:</strong> {$job['closingDate']}</p>";
            echo "</section>";
            echo "<a href='#' class='close-button'>Close</a>";
            echo "</div>";
        }
    } else {
        echo "<p>No jobs found.</p>";
    }

    mysqli_close($conn);
    ?>
</body>
</html>
