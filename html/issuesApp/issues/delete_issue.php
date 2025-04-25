<!--
Delete Issues Script
author: Carson Bragg; chatgpt
desc: deletes the give issue and
      all of the associated comments. 
-->

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '/var/www/database/issDB/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['admin'];
$issue_id = $_POST['issue_id'] ?? null;

if (!$issue_id) {
	header("Location: view_issues.php?error=Issue id  not found");
	exit();
}
echo "issue#: $issue_id";

// Fetch issue to verify ownership/admin rights
$stmt = $pdo->prepare("SELECT per_id FROM iss_issues WHERE id = :id");
$stmt->execute([':id' => $issue_id]);
$issue = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$issue) {
	 header("Location: view_issues.php?error=Issue not found");
        exit();
}
// Check if user is allowed to delete the issue
if (!$is_admin && $issue['per_id'] != $user_id) {
    die('Permission denied.');
}

try {
    $pdo->beginTransaction();

    // Delete associated comments using correct column name `iss_id`
    $delComments = $pdo->prepare("DELETE FROM iss_comments WHERE iss_id = :issue_id");
    $delComments->execute([':issue_id' => $issue_id]);

    // Delete the issue
    $delIssue = $pdo->prepare("DELETE FROM iss_issues WHERE id = :id");
    $delIssue->execute([':id' => $issue_id]);

    $pdo->commit();
    header("Location view_issues.php");
    exit();
} catch (Exception $e) {
    $pdo->rollBack();
    die("Error deleting issue: " . $e->getMessage());
}


?>
