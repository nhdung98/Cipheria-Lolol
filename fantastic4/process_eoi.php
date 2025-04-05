<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("settings.php");

// Connect to the database
$conn = @mysqli_connect($host, $user, $pwd, $sql_db);
if (!$conn) {
    die("<p>Database connection failed: " . mysqli_connect_error() . "</p>");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $jobRef     = $_POST['jobRef'] ?? '';
    $firstName  = $_POST['firstName'] ?? '';
    $lastName   = $_POST['lastName'] ?? '';
    $dob        = $_POST['dob'] ?? '';
    $gender     = $_POST['gender'] ?? '';
    $address    = $_POST['address'] ?? '';
    $suburb     = $_POST['suburb'] ?? '';
    $state      = $_POST['state'] ?? '';
    $postcode   = $_POST['postcode'] ?? '';
    $email      = $_POST['email'] ?? '';
    $phone      = $_POST['phone'] ?? '';
    
    // Skills checkboxes
    $skills     = $_POST['skills'] ?? [];
    $skillHTML   = in_array("HTML", $skills) ? 1 : 0;
    $skillCSS    = in_array("CSS", $skills) ? 1 : 0;
    $skillJS     = in_array("JavaScript", $skills) ? 1 : 0;
    $skillPython = in_array("Python", $skills) ? 1 : 0;
    $skillOther  = in_array("Other", $skills) ? 1 : 0;

    $otherSkills = $_POST['otherSkills'] ?? '';
    $status      = "New";

    // Insert into database
    $query = "INSERT INTO eoi (
        jobRef, firstName, lastName, dob, gender, address, suburb, state, postcode,
        email, phone, skillHTML, skillCSS, skillJS, skillPython, skillOther,
        otherSkills, status
    ) VALUES (
        '$jobRef', '$firstName', '$lastName', '$dob', '$gender', '$address', '$suburb',
        '$state', '$postcode', '$email', '$phone', $skillHTML, $skillCSS, $skillJS,
        $skillPython, $skillOther, '$otherSkills', '$status'
    )";

    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("<p>Query failed: " . mysqli_error($conn) . "</p>");
    }    

    if ($result) {
        echo "<h2>Application Submitted</h2>";
        echo "<p>Thank you, <strong>$firstName $lastName</strong>.</p>";
        echo "<p>You applied for job reference: <strong>$jobRef</strong>.</p>";
    } else {
        echo "<p>Error submitting application: " . mysqli_error($conn) . "</p>";
    }

    mysqli_close($conn);
} else {
    header("Location: apply.html");
    exit();
}
?>
