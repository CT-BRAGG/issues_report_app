<!--
Delete Person Script
author: Carson Bragg; chatgpt
desc: This script, when given a person id, will
      delete all of the information in all of the 
      database tables for that id. 
-->
<?php
session_start();
require_once '/var/www/database/issDB/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$current_user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['admin'];
$target_person_id = $_POST['per_id'] ?? null;

if (!$target_person_id) {
    die('No person ID provided.');
}

// Fetch target person to validate existence
$stmt = $pdo->prepare("SELECT * FROM iss_persons WHERE id = :id");
$stmt->execute(['id' => $target_person_id]);
$person = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$person) {
    die('Person not found.');
}

// Permission check
if (!$is_admin && $current_user_id != $target_person_id) {
    die('Permission denied.');
}

try {
    $pdo->beginTransaction();

    // Step 1: Get all issue IDs owned by the person
    $issue_ids_stmt = $pdo->prepare("SELECT id FROM iss_issues WHERE per_id = :pid");
    $issue_ids_stmt->execute(['pid' => $target_person_id]);
    $issue_ids = $issue_ids_stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($issue_ids)) {
        // Step 2: Delete comments on those issues
        $placeholders = implode(',', array_fill(0, count($issue_ids), '?'));
        $del_issue_comments = $pdo->prepare("DELETE FROM iss_comments WHERE iss_id IN ($placeholders)");
        $del_issue_comments->execute($issue_ids);
    }

    // Step 3: Delete person's own comments
    $del_own_comments = $pdo->prepare("DELETE FROM iss_comments WHERE per_id = :pid");
    $del_own_comments->execute(['pid' => $target_person_id]);

    // Step 4: Delete person's issues
    $del_issues = $pdo->prepare("DELETE FROM iss_issues WHERE per_id = :pid");
    $del_issues->execute(['pid' => $target_person_id]);

    // Step 5: Delete person
    $del_person = $pdo->prepare("DELETE FROM iss_persons WHERE id = :id");
    $del_person->execute(['id' => $target_person_id]);

    $pdo->commit();
    header("Location: ../login.php");
    exit();
} catch (Exception $e) {
    $pdo->rollBack();
    die("Error deleting person: " . $e->getMessage());
}
?>
