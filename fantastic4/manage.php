<?php
require_once("settings.php");
$conn = @mysqli_connect($host, $user, $pwd, $sql_db);
if (!$conn) {
    die("<p>Database connection failed: " . mysqli_connect_error() . "</p>");
}

function showResults($result) {
    if (mysqli_num_rows($result) > 0) {
        echo "<table border='1' cellpadding='8'><tr>
            <th>EOInumber</th><th>JobRef</th><th>First Name</th><th>Last Name</th><th>Status</th>
            <th>Email</th><th>Phone</th><th>Skills</th>
        </tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            $skills = [];
            if ($row['skillHTML']) $skills[] = "HTML";
            if ($row['skillCSS']) $skills[] = "CSS";
            if ($row['skillJS']) $skills[] = "JavaScript";
            if ($row['skillPython']) $skills[] = "Python";
            if ($row['skillOther']) $skills[] = "Other";

            echo "<tr>
                <td>{$row['EOInumber']}</td>
                <td>{$row['jobRef']}</td>
                <td>{$row['firstName']}</td>
                <td>{$row['lastName']}</td>
                <td>{$row['status']}</td>
                <td>{$row['email']}</td>
                <td>{$row['phone']}</td>
                <td>" . implode(", ", $skills) . "</td>
            </tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color:red;'>No matching records found.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage EOIs</title>
</head>
<body>
    <h1>Manage EOIs</h1>
    <form method="post">
        <h3>1. List All EOIs</h3>
        <button name="action" value="list_all">List All</button>

        <h3>2. Sort EOIs by:</h3>
        <select name="sortField">
            <option value="EOInumber">EOI Number</option>
            <option value="firstName">First Name</option>
            <option value="lastName">Last Name</option>
            <option value="jobRef">Job Reference</option>
            <option value="status">Status</option>
        </select>
        <button name="action" value="list_sorted">Sort</button>

        <h3>3. Search by Job Reference</h3>
        <input type="text" name="jobRef" placeholder="Enter Job Reference">
        <button name="action" value="search_jobref">Search</button>

        <h3>4. Search by Applicant Name</h3>
        <input type="text" name="firstName" placeholder="First Name">
        <input type="text" name="lastName" placeholder="Last Name">
        <button name="action" value="search_name">Search</button>

        <h3>5. Delete EOIs by Job Reference</h3>
        <input type="text" name="delete_jobRef" placeholder="Enter Job Ref to Delete">
        <button name="action" value="delete_jobref" onclick="return confirm('Are you sure?')">Delete</button>

        <h3>6. Change EOI Status</h3>
        <input type="number" name="eoiNumber" placeholder="EOI Number">
        <select name="newStatus">
            <option value="">Select Status</option>
            <option value="New">New</option>
            <option value="Current">Current</option>
            <option value="Final">Final</option>
        </select>
        <button name="action" value="update_status">Update Status</button>
    </form>

    <hr>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    if ($action == "list_all") {
        $query = "SELECT * FROM eoi";
        $result = mysqli_query($conn, $query);
        showResults($result);

    } elseif ($action == "list_sorted") {
        $allowedFields = ['EOInumber', 'firstName', 'lastName', 'jobRef', 'status'];
        $sortField = $_POST['sortField'];
        if (in_array($sortField, $allowedFields)) {
            $query = "SELECT * FROM eoi ORDER BY $sortField";
            $result = mysqli_query($conn, $query);
            showResults($result);
        } else {
            echo "<p style='color:red;'>Invalid sort option.</p>";
        }

    } elseif ($action == "search_jobref") {
        $jobRef = trim($_POST['jobRef'] ?? '');
        $query = "SELECT * FROM eoi WHERE jobRef='$jobRef'";
        $result = mysqli_query($conn, $query);
        showResults($result);

    } elseif ($action == "search_name") {
        $first = trim($_POST['firstName'] ?? '');
        $last = trim($_POST['lastName'] ?? '');
        $conditions = [];

        if ($first) $conditions[] = "firstName LIKE '%$first%'";
        if ($last)  $conditions[] = "lastName LIKE '%$last%'";

        if (!empty($conditions)) {
            $query = "SELECT * FROM eoi WHERE " . implode(" AND ", $conditions);
            $result = mysqli_query($conn, $query);
            showResults($result);
        } else {
            echo "<p style='color:red;'>Please enter at least a first or last name.</p>";
        }

    } elseif ($action == "delete_jobref") {
        $jobRef = trim($_POST['delete_jobRef'] ?? '');
        if ($jobRef) {
            $deleteQuery = "DELETE FROM eoi WHERE jobRef='$jobRef'";
            if (mysqli_query($conn, $deleteQuery)) {
                echo "<p style='color:green;'>EOIs with job reference '$jobRef' deleted.</p>";
            } else {
                echo "<p style='color:red;'>Error deleting EOIs: " . mysqli_error($conn) . "</p>";
            }
        } else {
            echo "<p style='color:red;'>Please enter a job reference to delete.</p>";
        }

    } elseif ($action == "update_status") {
        $eoiNumber = $_POST['eoiNumber'] ?? '';
        $newStatus = $_POST['newStatus'] ?? '';

        if ($eoiNumber && in_array($newStatus, ['New', 'Current', 'Final'])) {
            $updateQuery = "UPDATE eoi SET status='$newStatus' WHERE EOInumber=$eoiNumber";
            if (mysqli_query($conn, $updateQuery)) {
                echo "<p style='color:green;'>Status updated successfully for EOI #$eoiNumber</p>";
            } else {
                echo "<p style='color:red;'>Error updating status: " . mysqli_error($conn) . "</p>";
            }
        } else {
            echo "<p style='color:red;'>Please enter valid EOI number and status.</p>";
        }
    }
}
mysqli_close($conn);
?>
</body>
</html>
