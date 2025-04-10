<?php
session_start();
require_once '/var/www/database/issDB/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch issues with comment counts and creator ID
$query = "
    SELECT 
        i.id,
        i.short_description,
        LEFT(i.long_description, 100) AS summary,
        i.per_id,
        (
            SELECT COUNT(*) 
            FROM iss_comments c 
            WHERE c.iss_id = i.id
        ) AS comment_count
    FROM iss_issues i
    ORDER BY i.open_date DESC
";

$stmt = $pdo->prepare($query);
$stmt->execute();
$issues = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>i
<head>
    <title>All Issues</title>
    <link rel="stylesheet" href="../../paper-style.css">
    <!--
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #504945;
            text-align: left;
        }
        .rows:hover {
            background-color: #3c3836;
    	    color: #d4be98;  
        }
        th {
            background-color: #3c3836;
        }
        form {
            display: inline;
            margin: 0;
        }
        .action-buttons button {
            margin-right: 6px;
        }
.header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.small-button {
    background-color: #504945;
    color: #ebdbb2;
    border: none;
    padding: 6px 10px;
    font-size: 0.9em;
    border-radius: 4px;
    cursor: pointer;
}

.small-button:hover {
    background-color: #665c54;
    color: #d4be98;  
}

    </style>
    -->
</head>
<body>
<div class="paper">
<div class="container">

    <!-- Report New Issue Button 
    <div style="margin-bottom: 20px;">
        <form action="report_new_issue.php" method="get">
            <button type="submit" class="back-button">+ Report New Issue</button>
        </form>
    </div> -->
<div class="header-row">
    <h2>All Reported Issues</h2>
    <form action="report_new_issue.php" method="get">
        <button type="submit" class="small-button">+ Report New Issue</button>
    </form>
</div>


    <table>
        <thead>
            <tr>
                <th>Issue</th>
                <th>Description</th>
                <th>Comments</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($issues as $issue): ?>
            <tr class='rows'>
                <td><?= htmlspecialchars($issue['short_description']) ?></td>
                <td><?= htmlspecialchars($issue['summary']) ?>...</td>
                <td><?= $issue['comment_count'] ?></td>
                <td class="action-buttons">
                    <form action="set_issue_session.php" method="post">
                        <input type="hidden" name="issue_id" value="<?= $issue['id'] ?>">
                        <button type="submit">View</button>
                    </form>

                    <?php if ($_SESSION['user_id'] == $issue['per_id']): ?>
                        <form action="edit_issue.php" method="post">
                            <input type="hidden" name="issue_id" value="<?= $issue['id'] ?>">
                            <button type="submit">Edit</button>
                        </form>
			<!--
                        <form action="delete_issue.php" method="post" onsubmit="return confirm('Are you sure you want to delete this issue?');">
                            <input type="hidden" name="issue_id" value="<?= $issue['id'] ?>">
                            <button type="submit" style="color: #fb4934;">Delete</button>
                        </form>
			-->
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <form action="../dashboard.php" method="post">
        <div class="toDashboard">
           <input type="hidden" name="issue_id" value="<?= $issue['id'] ?>">
           <button type="submit">Back to Dashboard</button>
       </div>
    </form>

</div>
</div>
</body>
</html>
