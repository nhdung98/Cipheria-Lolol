<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("settings.php");

// Sanitize input
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Connect to DB
$conn = @mysqli_connect($host, $user, $pwd, $sql_db);
if (!$conn) {
    die("<p>Database connection failed: " . mysqli_connect_error() . "</p>");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize all fields
    $jobRef     = sanitize_input($_POST['jobRef'] ?? '');
    $firstName  = sanitize_input($_POST['firstName'] ?? '');
    $lastName   = sanitize_input($_POST['lastName'] ?? '');
    $dob        = sanitize_input($_POST['dob'] ?? '');
    $gender     = sanitize_input($_POST['gender'] ?? '');
    $address    = sanitize_input($_POST['address'] ?? '');
    $suburb     = sanitize_input($_POST['suburb'] ?? '');
    $state      = sanitize_input($_POST['state'] ?? '');
    $postcode   = sanitize_input($_POST['postcode'] ?? '');
    $email      = sanitize_input($_POST['email'] ?? '');
    $phone      = sanitize_input($_POST['phone'] ?? '');
    $otherSkills = sanitize_input($_POST['otherSkills'] ?? '');
    $skills     = $_POST['skills'] ?? [];

    // Skills checkboxes
    $skillHTML   = in_array("HTML", $skills) ? 1 : 0;
    $skillCSS    = in_array("CSS", $skills) ? 1 : 0;
    $skillJS     = in_array("JavaScript", $skills) ? 1 : 0;
    $skillPython = in_array("Python", $skills) ? 1 : 0;
    $skillOther  = in_array("Other", $skills) ? 1 : 0;

    $status = "New";

    // 🌟 VALIDATION RULES 🌟

    $errors = [];

    // Job reference: 5 alphanumeric characters
    if (!preg_match("/^[A-Za-z0-9]+$/", $jobRef) || strlen($jobRef) !== 5) {
        $errors[] = "Job reference must be exactly 5 alphanumeric characters.";
    }
    
    // First name: 1-20 alphabetic characters
    if (!preg_match("/^[A-Za-z]{1,20}$/", $firstName)) {
        $errors[] = "First name must be up to 20 alphabetic characters.";
    }

    // Last name: 1-20 alphabetic characters
    if (!preg_match("/^[A-Za-z]{1,20}$/", $lastName)) {
        $errors[] = "Last name must be up to 20 alphabetic characters.";
    }

    // DOB → Age between 15 and 80
    if ($dob) {
        $dobDate = DateTime::createFromFormat('Y-m-d', $dob);
        $now = new DateTime();
        $age = $now->diff($dobDate)->y;
        if ($age < 15 || $age > 80) {
            $errors[] = "Age must be between 15 and 80 years.";
        }
    } else {
        $errors[] = "Date of birth is required.";
    }
    
    
    if (!in_array($gender, ['Male', 'Female', 'Other'])) {
        $errors[] = "Please select a valid gender.";
    }

    if (strlen($address) > 40) {
        $errors[] = "Address must be 40 characters or less.";
    }

    if (strlen($suburb) > 40) {
        $errors[] = "Suburb must be 40 characters or less.";
    }

    $validStates = ['VIC', 'NSW', 'QLD', 'NT', 'WA', 'SA', 'TAS', 'ACT'];
    if (!in_array($state, $validStates)) {
        $errors[] = "State must be one of VIC,NSW,QLD,NT,WA,SA,TAS,ACT.";
    }

    // Postcode: 4 digits, must match state
    $statePrefixes = [
        'VIC' => ['3', '8'],
        'NSW' => ['1', '2'],
        'QLD' => ['4', '9'],
        'NT'  => ['0'],
        'WA'  => ['6'],
        'SA'  => ['5'],
        'TAS' => ['7'],
        'ACT' => ['0']
    ];
    if (!preg_match("/^\d{4}$/", $postcode)) {
        $errors[] = "Postcode must be exactly 4 digits.";
    } elseif (!in_array(substr($postcode, 0, 1), $statePrefixes[$state])) {
        $errors[] = "Postcode does not match the selected state.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }

    if (!preg_match("/^[0-9 ]{8,12}$/", $phone)) {
        $errors[] = "Phone number must be 8–12 digits or digits with spaces.";
    }

    if ($skillOther && empty($otherSkills)) {
        $errors[] = "Please describe your 'Other' skills.";
    }

    // ❌ If there are errors → show error page
    if (!empty($errors)) {
        echo "<h2>There was a problem with your submission:</h2>";
        echo "<ul style='color:red;'>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
        echo "<p><a href='applynew.html'>← Go back to the application form</a></p>";
        exit();
    }

    // ✅ Insert into database
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

    if ($result) {
        $eoi_id = mysqli_insert_id($conn);
        echo "<h2>Application Submitted</h2>";
        echo "<p>Thank you, <strong>$firstName $lastName</strong>.</p>";
        echo "<p>You applied for job reference: <strong>$jobRef</strong>.</p>";
        echo "<p>Your EOI Number is: <strong>$eoi_id</strong></p>";
    } else {
        echo "<p>Error submitting application: " . mysqli_error($conn) . "</p>";
    }

    mysqli_close($conn);

} else {
    header("Location: apply.html");
    exit();
}
?>
