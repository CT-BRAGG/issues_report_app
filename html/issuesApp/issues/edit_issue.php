<!-- 
Edit Issue Script
author: Carson Bragg; chatgpt
desc: This script manges the related code for updating
      information for issues. This also contains the 
      code responsible for running the delete script.  
-->
<?php
session_start();
require_once '/var/www/database/issDB/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['admin'];
$issue_id = $_POST['id'] ?? $_POST['issue_id'] ?? $_GET['id'] ?? null;

if (!$issue_id) {
    echo "Missing issue ID.";
    exit();
}
//echo "issue#: $issue_id"; // for testing

// Get all persons for dropdown
$people_stmt = $pdo->query("SELECT id, fname, lname FROM iss_persons ORDER BY lname, fname");
$people = $people_stmt->fetchAll(PDO::FETCH_ASSOC);

// Process update if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $short = $_POST['short_description'] ?? '';
    $long = $_POST['long_description'] ?? '';
    $open_date = $_POST['open_date'] ?? null;
    $close_date = $_POST['close_date'] ?? null;
    $priority = $_POST['priority'] ?? '';
    $org = $_POST['org'] ?? '';
    $project = $_POST['project'] ?? '';
    $per_id = $_POST['per_id'] ?? null;

    try {
        $stmt = $pdo->prepare("UPDATE iss_issues SET 
            short_description = :short,
            long_description = :long,
            open_date = :open_date,
            close_date = :close_date,
            priority = :priority,
            org = :org,
            project = :project,
            per_id = :per_id
            WHERE id = :id");

        $stmt->execute([
            'short' => $short,
            'long' => $long,
            'open_date' => $open_date,
            'close_date' => $close_date,
            'priority' => $priority,
            'org' => $org,
            'project' => $project,
            'per_id' => $per_id,
            'id' => $issue_id
        ]);

        header("Location: view_issues.php");
        exit();
    } catch (Exception $e) {
        echo "Error updating issue: " . $e->getMessage();
        exit();
    }
}

// Fetch issue (after form or on first load)
$stmt = $pdo->prepare("SELECT * FROM iss_issues WHERE id = :id");
$stmt->execute(['id' => $issue_id]);
$issue = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$issue) {
    echo "Issue not found.";
    exit();
}

// Permissions
if (!$is_admin && $issue['per_id'] != $user_id) {
    echo "Permission denied.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Issue</title>
    <link rel="stylesheet" href="../../paper-style.css">
</head>
<body>
<div class="paper">
    <h1>Edit Issue</h1>
    <form method="POST" action="edit_issue.php">
        <input type="hidden" name="id" value="<?= htmlspecialchars($issue['id']) ?>">

        <label for="short_description">Issue Title:</label>
        <input type="text" name="short_description" value="<?= htmlspecialchars($issue['short_description']) ?>" required>

        <label for="long_description">Issue Description:</label>
        <textarea name="long_description" required><?= htmlspecialchars($issue['long_description']) ?></textarea>

        <label for="open_date">Open Date:</label>
        <input type="date" name="open_date" value="<?= htmlspecialchars($issue['open_date']) ?>">

        <label for="close_date">Close Date:</label>
        <input type="date" name="close_date" value="<?= htmlspecialchars($issue['close_date']) ?>">

        <label for="priority">Priority:</label>
        <input type="text" name="priority" value="<?= htmlspecialchars($issue['priority']) ?>">

        <label for="org">Organization:</label>
        <input type="text" name="org" value="<?= htmlspecialchars($issue['org']) ?>">

        <label for="project">Project:</label>
        <input type="text" name="project" value="<?= htmlspecialchars($issue['project']) ?>">

        <label for="per_id">Owner:</label>
        <select name="per_id" required>
            <?php foreach ($people as $person): ?>
                <option value="<?= $person['id'] ?>" <?= $person['id'] == $issue['per_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($person['fname'] . ' ' . $person['lname']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <div class="formButton">
            <button type="submit">Save</button>
        </div>
    </form>

    <form method="POST" action="delete_issue_with_error_handling.php" class="formButton" style="margin-top: 20px;">
        <input type="hidden" name="issue_id" value="<?= htmlspecialchars($issue_id) ?>">
        <button type="submit" class="small-button" style="background-color: #cc241d;">Delete</button>
    </form>
</div>
</body>
</html>
