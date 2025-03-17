<!--
DASHBOARD
desc: contains all the various actions that user can perform
author: Carson Bragg; chatgpt
-->

<?php
    session_start();

    // Check if the user is logged in, if not, redirect to login page
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    // Get user details from session
    $fname = $_SESSION['fname'];
    $lname = $_SESSION['lname'];
    $email = $_SESSION['email'];
    $admin = $_SESSION['admin'];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard - Issue Reporting App</title>
        <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file here -->
    </head>
    <body>
        <div class="dashboard-container">
            <h2>Welcome to the Dashboard, <?php echo htmlspecialchars($fname . ' ' . $lname); ?>!</h2>
            
            <div class="user-info">
                <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                <p><strong>Role:</strong> <?php echo htmlspecialchars($admin === '1' ? 'Admin' : 'User'); ?></p>
            </div>
            
            <div class="actions">
                <h3>What would you like to do?</h3>
                <ul>
                    <li><a href="report_new_issue.php">Report a New Issue</a></li>
                    <li><a href="view_issues.php">View My Issues</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </body>
</html>
