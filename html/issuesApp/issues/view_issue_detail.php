<!--
View Issue Details Script
author: Carson Bragg; chatgpt
desc: This script displays the related database
      information for the given issue id; its 
      passed via parameters in the URL. Users 
      can also create, update, and delete comments
      in this menu as well. 
-->
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '/var/www/database/issDB/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['admin'] ?? false;
$issue_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['issue_id'])) {
    $_SESSION['issue_id'] = $_POST['issue_id'];
}

if(!$issue_id) {
//if (!isset($_SESSION['issue_id'])) {
    echo "No issue selected.";
    exit();
}

//echo "issue# $issue_id "; // for testing

// Fetch issue details
$query = "
    SELECT i.*, p.fname, p.lname
    FROM iss_issues i
    LEFT JOIN iss_persons p ON i.per_id = p.id
    WHERE i.id = :issue_id
";
$stmt = $pdo->prepare($query);
$stmt->execute([':issue_id' => $issue_id]);
$issue = $stmt->fetch(PDO::FETCH_ASSOC);

if ($issue['close_date'] == '0000-00-00' || $issue['close_date'] == '0001-01-01') {
	$status = 'open';
} else {
	$status = 'closed';
}

if (!$issue) {
    echo "Issue not found.";
    exit();
}

// Handle comment form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_action'])) {
    $comment_id = $_POST['comment_id'] ?? null;
    $short = $_POST['short_comment'];
    $long = $_POST['long_comment'];
    $posted = $_POST['posted_date'];
    $per_id = $is_admin ? $_POST['per_id'] : $user_id;

    if ($_POST['comment_action'] === 'add') {
        $stmt = $pdo->prepare("INSERT INTO iss_comments (iss_id, per_id, short_comment, long_comment, posted_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$issue_id, $per_id, $short, $long, $posted]);
    } elseif ($_POST['comment_action'] === 'edit' && $comment_id) {
        $stmt = $pdo->prepare("UPDATE iss_comments SET per_id = ?, short_comment = ?, long_comment = ?, posted_date = ? WHERE id = ?");
        $stmt->execute([$per_id, $short, $long, $posted, $comment_id]);
    } elseif ($_POST['comment_action'] === 'delete' && $comment_id) {
        $stmt = $pdo->prepare("DELETE FROM iss_comments WHERE id = ?");
        $stmt->execute([$comment_id]);
    }
}

// Fetch all comments
$comment_query = "
    SELECT c.*, p.fname, p.lname
    FROM iss_comments c
    LEFT JOIN iss_persons p ON c.per_id = p.id
    WHERE c.iss_id = :issue_id
    ORDER BY c.posted_date ASC
";
$comment_stmt = $pdo->prepare($comment_query);
$comment_stmt->execute(['issue_id' => $issue_id]);
$comments = $comment_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch persons for dropdown (if admin)
$persons = [];
if ($is_admin) {
    $persons_stmt = $pdo->query("SELECT id, fname, lname FROM iss_persons ORDER BY lname, fname");
    $persons = $persons_stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Issue Detail</title>
    <link rel="stylesheet" href="../../paper-style.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            left: 0; top: 0; width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: #f2e5bc;
            padding: 20px;
            border-radius: 12px;
            max-width: 600px;
            width: 90%;
        }
        .comment-block {
            background-color: #d4be98;
            margin-bottom: 20px;
            padding: 12px;
            border-radius: 10px;
        }
        .comment-header {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
        }
        .comment-meta {
            font-size: 0.9em;
            color: #665c54;
        }
    </style>
    <script>
        function openModal(id=null, short='', long='', posted='', per_id='') {
            document.getElementById('comment_id').value = id || '';
            document.getElementById('short_comment').value = short || '';
            document.getElementById('long_comment').value = long || '';
            document.getElementById('posted_date').value = posted || '';
            <?php if ($is_admin): ?>
            document.getElementById('per_id').value = per_id || '';
            <?php endif; ?>
            document.getElementById('comment_action').value = id ? 'edit' : 'add';
            document.getElementById('commentModal').style.display = 'flex';
        }
        function closeModal() {
            document.getElementById('commentModal').style.display = 'none';
        }
    </script>
</head>
<body>
    <div class="paper">
        <h1><?= htmlspecialchars($issue['short_description']) ?></h1>

        <p><?= nl2br(htmlspecialchars($issue['long_description'])) ?></p>

        <table>
            <tr><th>ID</th><td><?= htmlspecialchars($issue['id']) ?></td></tr>
            <tr><th>Reporter</th><td><?= htmlspecialchars($issue['fname'] . ' ' . $issue['lname']) ?></td></tr>
            <tr><th>Status</th><td><?= htmlspecialchars($status) ?></td></tr>
            <tr><th>Open Date</th><td><?= htmlspecialchars($issue['open_date']) ?></td></tr>
            <tr><th>Close Date</th><td><?= htmlspecialchars($issue['close_date']) ?></td></tr>
            <tr><th>Priority</th><td><?= htmlspecialchars($issue['priority']) ?></td></tr>
            <tr><th>Org</th><td><?= htmlspecialchars($issue['org']) ?></td></tr>
            <tr><th>Project</th><td><?= htmlspecialchars($issue['project']) ?></td></tr>
        </table>

        
        <hr>

        <div class="header-row">
            <h2>Comments</h2>
            <button class="small-button" onclick="openModal()">+</button>
        </div>

        <?php foreach ($comments as $c): ?>
            <div class="comment-block">
                <div class="comment-header">
                    <span><?= htmlspecialchars($c['short_comment']) ?></span>
                    <button class="small-button" onclick="openModal('<?= $c['id'] ?>', '<?= htmlspecialchars($c['short_comment'], ENT_QUOTES) ?>', '<?= htmlspecialchars($c['long_comment'], ENT_QUOTES) ?>', '<?= $c['posted_date'] ?>', '<?= $c['per_id'] ?>')">Edit</button>
                </div>
                <div class="comment-meta">
                    <?= htmlspecialchars($c['fname'] . ' ' . $c['lname']) ?> on <?= htmlspecialchars($c['posted_date']) ?>
                </div>
                <p><?= nl2br(htmlspecialchars($c['long_comment'])) ?></p>
            </div>
        <?php endforeach; ?>

        <!-- Modal -->
        <div id="commentModal" class="modal" onclick="if(event.target==this)closeModal()">
            <div class="modal-content">
                <h3>Comment</h3>
                <form method="POST">
                    <input type="hidden" name="comment_id" id="comment_id">
                    <input type="hidden" name="comment_action" id="comment_action">
                    <label>Comment Title:</label>
                    <input type="text" name="short_comment" id="short_comment" required>
                    <label>Comment Body:</label>
                    <textarea name="long_comment" id="long_comment"></textarea>
                    <label>Posted Date:</label>
                    <input type="date" name="posted_date" id="posted_date" required>

                    <?php if ($is_admin): ?>
                    <label>Posted By:</label>
                    <select name="per_id" id="per_id" required>
                        <?php foreach ($persons as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['fname'] . ' ' . $p['lname']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php endif; ?>

                    <div class="formButton">
                        <button type="submit">Save</button>
                        <button type="submit" name="comment_action" value="delete">Delete</button>
                        <button type="button" onclick="closeModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

	<div class="toDashboard">
	<form action="view_issues.php" method="post">
		<div class="toDashbaord">
			<button type="submit"> Back to View all Issues</button>
		</div>
	</form>	
	</div>

    </div>
</body>
</html>
