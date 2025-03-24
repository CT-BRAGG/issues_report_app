<?php
// list_issues.php

session_start();

// Redirect to login page if user not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db_connection.php'; // database connection

// Fetch issues with comment counts
$sql = "
    SELECT 
        i.id,
        i.short_description,
        i.long_description,
        i.close_date,
        (
            SELECT COUNT(*) 
            FROM iss_comments c 
            WHERE c.iss_id = i.id
        ) AS comment_count
    FROM iss_issues i
    ORDER BY i.open_date DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Issues</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .issue-box {
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
        }
        .issue-box a {
            text-decoration: none;
            color: black;
            display: block;
        }
        .issue-box:hover {
            background-color: #f9f9f9;
        }
        .resolved {
            color: green;
            font-weight: bold;
        }
        .unresolved {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>Issue List</h2>

    <?php while ($row = $result->fetch_assoc()): ?>
        <form action="set_issue_detail.php" method="post" style="margin:0;">
            <input type="hidden" name="issue_id" value="<?= $row['id'] ?>">
            <button type="submit" style="all: unset; width: 100%;">
                <div class="issue-box">
                    <h3><?= htmlspecialchars($row['short_description']) ?></h3>
                    <p><?= nl2br(htmlspecialchars(substr($row['long_description'], 0, 100))) ?>...</p>
                    <p><strong>Comments:</strong> <?= $row['comment_count'] ?></p>
                    <p class="<?= ($row['close_date'] !== '0000-00-00') ? 'resolved' : 'unresolved' ?>">
                        <?= ($row['close_date'] !== '0000-00-00') ? 'Resolved' : 'Unresolved' ?>
                    </p>
                </div>
            </button>
        </form>
    <?php endwhile; ?>

</body>
</html>

