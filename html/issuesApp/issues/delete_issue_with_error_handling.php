<!--
Delete Issue Script
author: Carson Bragg; chatgpt
desc: This script, when given an issue id, 
      will delete all the related information 
      in all the database tables. 
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

$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['admin'];
$issue_id = $_POST['issue_id'] ?? null;

if (!$issue_id) {
    header("Location: view_issues.php?error=Invalid issue ID");
    exit();
}

// Fetch the issue to verify permissions
$stmt = $pdo->prepare("SELECT per_id FROM iss_issues WHERE id = :id");
$stmt->execute([':id' => $issue_id]);
$issue = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$issue) {
    header("Location: view_issues.php?error=Issue not found");
    exit();
}

// Check if user is allowed to delete the issue
if (!$is_admin && $issue['per_id'] != $user_id) {
    header("Location: view_issues.php?error=Permission denied");
    exit();
}

try {
    $pdo->beginTransaction();

    // Delete associated comments on this issue
    $delComments = $pdo->prepare("DELETE FROM iss_comments WHERE iss_id = :issue_id");
    $delComments->execute([':issue_id' => $issue_id]);

    // Delete the issue itself
    $delIssue = $pdo->prepare("DELETE FROM iss_issues WHERE id = :id");
    $delIssue->execute([':id' => $issue_id]);

    $pdo->commit();
    header("Location: view_issues.php?success=Issue deleted successfully");
    exit();
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Error deleting issue: " . $e->getMessage());  // Log the error for debugging
    header("Location: view_issues.php?error=Error deleting issue");
    exit();
}
?>
