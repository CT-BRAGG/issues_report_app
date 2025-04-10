<?php
session_start();
require_once '/var/www/database/issDB/db_connection.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initial values
$short_description = '';
$long_description = '';
$priority = '';
$org = '';
$project = '';
$open_date = '';
$close_date = '';
$errors = [];
$submitted = false;

$max_lengths = [
    'short_description' => 255,
    'org' => 100,
    'project' => 100,
    'priority' => 12
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted = true;

    $short_description = trim($_POST['short_description'] ?? '');
    $long_description = trim($_POST['long_description'] ?? '');
    $priority = $_POST['priority'] ?? '';
    $org = trim($_POST['org'] ?? '');
    $project = trim($_POST['project'] ?? '');
    $open_date = trim($_POST['open_date'] ?? '');
    $close_date = trim($_POST['close_date'] ?? '');

    // Validate fields
    if ($short_description === '' || strlen($short_description) > $max_lengths['short_description']) {
        $errors['short_description'] = "Short description is required and must be under {$max_lengths['short_description']} characters.";
    }
    if ($long_description === '') {
        $errors['long_description'] = "Long description is required.";
    }
    if (!in_array($priority, ['Low', 'Medium', 'High'])) {
        $errors['priority'] = "Invalid priority selected.";
    }
    if ($org === '' || strlen($org) > $max_lengths['org']) {
        $errors['org'] = "Organization is required and must be under {$max_lengths['org']} characters.";
    }
    if ($project === '' || strlen($project) > $max_lengths['project']) {
        $errors['project'] = "Project is required and must be under {$max_lengths['project']} characters.";
    }
    if ($open_date === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $open_date)) {
        $errors['open_date'] = "Valid open date is required (YYYY-MM-DD).";
    }
    if ($close_date !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $close_date)) {
        $errors['close_date'] = "Close date must be in YYYY-MM-DD format.";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO iss_issues 
                (short_description, long_description, priority, org, project, open_date, close_date, per_id)
                VALUES (:short_description, :long_description, :priority, :org, :project, :open_date, :close_date, :per_id)
            ");
            $stmt->execute([
                'short_description' => $short_description,
                'long_description' => $long_description,
                'priority' => $priority,
                'org' => $org,
                'project' => $project,
                'open_date' => $open_date,
                'close_date' => $close_date !== '' ? $close_date : null,
                'per_id' => $_SESSION['user_id']
            ]);

            $_SESSION['issue_id'] = $pdo->lastInsertId();
            header("Location: view_issue_detail.php");
            exit();
        } catch (PDOException $e) {
            $errors['db'] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Report New Issue</title>
    <link rel="stylesheet" href="../../paper-style.css">
    <script>
        window.onload = function () {
            <?php if ($submitted && !empty($errors)): ?>
                let messages = <?= json_encode(array_values($errors)) ?>;
                alert("Please fix the following:\n\n" + messages.join("\n"));
            <?php endif; ?>
        };
    </script>
</head>
<body>
<div class="paper">
    <h2>Report a New Issue</h2>

    <form action="report_new_issue.php" method="post">
        <label for="short_description">Short Description:</label>
        <input type="text" id="short_description" name="short_description"
               maxlength="<?= $max_lengths['short_description'] ?>" required
               value="<?= htmlspecialchars($short_description) ?>">

        <label for="long_description">Long Description:</label>
        <textarea id="long_description" name="long_description" rows="6" required><?= 
            htmlspecialchars($long_description) ?></textarea>

        <label for="priority">Priority:</label>
        <select id="priority" name="priority" required>
            <option value="">Select priority</option>
            <?php foreach (['Low', 'Medium', 'High'] as $p): ?>
                <option value="<?= $p ?>" <?= $priority === $p ? 'selected' : '' ?>><?= $p ?></option>
            <?php endforeach; ?>
        </select>

        <label for="org">Organization:</label>
        <input type="text" id="org" name="org" maxlength="<?= $max_lengths['org'] ?>" required
               value="<?= htmlspecialchars($org) ?>">

        <label for="project">Project:</label>
        <input type="text" id="project" name="project" maxlength="<?= $max_lengths['project'] ?>" required
               value="<?= htmlspecialchars($project) ?>">

        <label for="open_date">Open Date (YYYY-MM-DD):</label>
        <input type="date" id="open_date" name="open_date" required
               value="<?= htmlspecialchars($open_date) ?>">

        <label for="close_date">Close Date (YYYY-MM-DD):</label>
        <input type="date" id="close_date" name="close_date"
               value="<?= htmlspecialchars($close_date) ?>">

        <div style="display: flex; gap: 12px;">
            <button type="submit">Save Issue</button>
            <button type="button" onclick="window.location.href='view_issues.php'">Cancel</button>
        </div>
    </form>
</div>
</body>
</html>

