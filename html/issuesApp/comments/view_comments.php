<!--
View Comments Script
author: Carson Bragg; chatgpt
desc: Displays a list of every comment with links to the
      details of the related issue.
-->

<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '/var/www/database/issDB/db_connection.php'; // Adjust as needed

// Fetch all comments with their corresponding ticket info
$query = "
    SELECT 
        c.id AS comment_id,
        c.iss_id AS issue_id,
        c.short_comment,
        c.posted_date,
        i.short_description AS title
    FROM iss_comments c
    LEFT JOIN iss_issues i ON c.iss_id = i.id
    ORDER BY c.posted_date DESC
";
$stmt = $pdo->query($query);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Comments - Issue Tracker</title>
    <link rel="stylesheet" href="../../paper-style.css">
    <style>
        tr.formRow {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="paper">
        <h1>All Comments</h1>
        <table>
            <thead>
                <tr>
                    <th>Ticket ID</th>
                    <th>Ticket Title</th>
                    <th>Comment Title</th>
                    <th>Date Posted</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $comment): ?>
                    <tr class="rows formRow" onclick="this.querySelector('form').submit();">
                        <td><?= htmlspecialchars($comment['issue_id']) ?></td>
                        <td><?= htmlspecialchars($comment['title']) ?></td>
                        <td><?= htmlspecialchars($comment['short_comment']) ?></td>
                        <td><?= htmlspecialchars(date('Y-m-d', strtotime($comment['posted_date']))) ?></td>
                        <form method="POST" action="redirect_to_view_issue.php">
                            <input type="hidden" name="issue_id" value="<?= $comment['issue_id'] ?>">
                        </form>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="toDashboard">
            <a href="../dashboard.php"><button class="small-button">Back to Dashboard</button></a>
        </div>
    </div>
</body>
</html>
