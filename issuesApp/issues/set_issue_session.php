<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['issue_id'])) {
    $_SESSION['issue_id'] = $_POST['issue_id'];
    header("Location: view_issue_detail.php");
    exit();
} else {
    header("Location: view_issues.php");
    exit();
}
