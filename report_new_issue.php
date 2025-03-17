<!-- 
REPORT NEW ISSUE SCRIPT
desc: reports a new issue
author: Carson Bragg; chatpgt 
-->

<?php
// Include database connection
require_once 'database/db_connection.php';

// Start session
session_start();

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $short_description = $_POST['short_description'];
    $long_description = $_POST['long_description'];
    $priority = $_POST['priority'];
    $org = $_POST['org'];
    $project = $_POST['project'];
    $open_date = date('Y-m-d');
    $per_id = $_SESSION['user_id']; // User ID from session

    // Insert the issue into the database
    $query = "INSERT INTO iss_issues (short_description, long_description, open_date, priority, org, project, per_id) 
              VALUES (:short_description, :long_description, :open_date, :priority, :org, :project, :per_id)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'short_description' => $short_description,
        'long_description' => $long_description,
        'open_date' => $open_date,
        'priority' => $priority,
        'org' => $org,
        'project' => $project,
        'per_id' => $per_id
    ]);

    // Redirect to view issues page after successful submission
    header("Location: view_issues.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report New Issue</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Report a New Issue</h1>
        <form action="report_issue.php" method="POST">
            <label for="short_description">Short Description</label>
            <input type="text" name="short_description" id="short_description" required><br>

            <label for="long_description">Long Description</label>
            <textarea name="long_description" id="long_description" required></textarea><br>

            <label for="priority">Priority</label>
            <select name="priority" id="priority" required>
                <option value="High">High</option>
                <option value="Medium">Medium</option>
                <option value="Low">Low</option>
            </select><br>

            <label for="org">Organization</label>
            <input type="text" name="org" id="org" required><br>

            <label for="project">Project</label>
            <input type="text" name="project" id="project" required><br>

            <button type="submit">Submit Issue</button>
        </form>
    </div>
    <div class="container">
        <br>
        <form method="POST">
            <button type="submit" name="redirect_to_dashboard" class="btn">Go to Dashboard</button>
        </form>
    </div>
</body>
</html>
`
