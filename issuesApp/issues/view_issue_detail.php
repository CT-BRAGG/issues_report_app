<?php
session_start();
require_once '/var/www/database/issDB/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['issue_id'])) {
    header("Location: view_issues.php");
    exit();
}

$issue_id = $_SESSION['issue_id'];

// Fetch issue details with reporter's name
$query = "
    SELECT 
        i.*,
        p.fname,
        p.lname
    FROM iss_issues i
    LEFT JOIN iss_persons p ON i.per_id = p.id
    WHERE i.id = :issue_id
";
$stmt = $pdo->prepare($query);
$stmt->execute(['issue_id' => $issue_id]);
$issue = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$issue) {
    echo "Issue not found.";
    exit();
}

// Fetch comments
$comment_query = "
    SELECT 
        c.short_comment,
        c.long_comment,
        c.posted_date,
        u.fname,
        u.lname
    FROM iss_comments c
    LEFT JOIN iss_persons u ON c.per_id = u.id
    WHERE c.iss_id = :issue_id
    ORDER BY c.posted_date DESC
";
$comment_stmt = $pdo->prepare($comment_query);
$comment_stmt->execute(['issue_id' => $issue_id]);
$comments = $comment_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Issue Detail</title>
    <link rel="stylesheet" href="../../paper-style.css">
</head>
<body>
<div class="paper">
    <div class="container">
        <h2>Issue: <?= htmlspecialchars($issue['short_description']) ?></h2>
        <p><strong>Reported by:</strong> <?= htmlspecialchars($issue['fname'] . ' ' . $issue['lname']) ?></p>
        <p><strong>Priority:</strong> <?= htmlspecialchars($issue['priority']) ?></p>
        <p><strong>Organization:</strong> <?= htmlspecialchars($issue['org']) ?></p>
        <p><strong>Project:</strong> <?= htmlspecialchars($issue['project']) ?></p>
        <p><strong>Open Date:</strong> <?= htmlspecialchars($issue['open_date']) ?></p>
        <p><strong>Close Date:</strong> <?= $issue['close_date'] !== '0000-00-00' ? htmlspecialchars($issue['close_date']) : 'Unresolved' ?></p>
        <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($issue['long_description'])) ?></p>

        <h3>Comments (<?= count($comments) ?>)</h3>
        <?php if ($comments): ?>
            <ul>
            <?php foreach ($comments as $comment): ?>
                <li>
                    <strong><?= htmlspecialchars($comment['fname'] . ' ' . $comment['lname']) ?> (<?= $comment['posted_date'] ?>)</strong><br>
                    <em><?= htmlspecialchars($comment['short_comment']) ?></em><br>
                    <?= nl2br(htmlspecialchars($comment['long_comment'])) ?>
                </li>
                <hr>
            <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No comments yet.</p>
        <?php endif; ?>

        <button onclick="window.location.href='view_issues.php'">← Back to Issues</button>
    </div>
</div>
</body>
</html>

