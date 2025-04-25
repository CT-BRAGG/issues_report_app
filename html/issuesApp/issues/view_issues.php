<!--
View Issues Script
author: Carson Bragg; chatgpt
desc: Displays a list of open issues. There is an option to also view all
      issues closed and open. There are also actions avaiable for each item
      depending on the user's relation to the issue. 
-->

<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '/var/www/database/issDB/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Determine whether to show open or all tickets
$show = $_GET['show'] ?? 'open'; // Default to 'open'

// Build query based on toggle
if ($show === 'all') {
    $stmt = $pdo->query("SELECT * FROM iss_issues ORDER BY open_date DESC");
} else {
    $stmt = $pdo->query("SELECT * FROM iss_issues WHERE close_date IS NULL OR close_date = '0000-00-00' OR close_date = '0001-01-01' ORDER BY open_date DESC");
}

$issues = $stmt->fetchAll(PDO::FETCH_ASSOC);



?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>View Issues</title>
    <link rel="stylesheet" href="../../paper-style.css">
</head>
<body>
<div class="paper">
    <h1>Issues</h1>

    <div class="formButton">
        <a href="report_new_issue.php" class="small-button">Register New Issue</a>

        <?php if ($show === 'all'): ?>
            <a href="view_issues.php?show=open" class="small-button" style="margin-left:10px;">Show Open Tickets</a>
        <?php else: ?>
            <a href="view_issues.php?show=all" class="small-button" style="margin-left:10px;">Show All Tickets</a>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <!-- <th>Short Description</th> -->
		<th>Status</th>
                <th>Priority</th>
                <th>Open Date</th>
                <th>Close Date</th>
                <th>Actions</th> 
            </tr>
        </thead>
        <tbody>
            <?php foreach ($issues as $issue): ?>
	    	<?php 
			if ($issue['close_date'] == '0000-00-00' || $issue['close_date'] == '0001-01-01') {
			        $status = 'open';
			} else {
			        $status = 'closed';
			}
		?>
                <tr class="rows">
                    <!-- <td><?= htmlspecialchars($issue['title'] ?? 'No Title') ?></td> -->
                    <td><?= htmlspecialchars($issue['short_description']) ?></td>
		    <td><?= $status ?></td>
                    <td><?= htmlspecialchars($issue['priority']) ?></td>
                    <td><?= htmlspecialchars($issue['open_date']) ?></td>
                    <td><?= htmlspecialchars($issue['close_date']) ?></td>
<td>
    <a href="view_issue_detail.php?id=<?= $issue['id'] ?>" class="small-button">View</a>
    
    <?php if ($_SESSION['admin'] || $_SESSION['user_id'] == $issue['per_id']): ?> <!-- Check if user is admin -->
        <a href="edit_issue.php?id=<?= $issue['id'] ?>" class="small-button" style="background-color: #7f9f7f;">Edit</a>
    <?php endif; ?>
</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="bottomPageButton">
        <a href="../dashboard.php"><button class="small-button">Back to Dashboard</button></a>
    </div>


</div>
</body>
</html>
