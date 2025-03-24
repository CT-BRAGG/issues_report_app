<?php
// set_issue_detail.php
session_start();

if (isset($_POST['issue_id'])) {
    $_SESSION['issue_id'] = $_POST['issue_id'];
    header("Location: issue_detail.php");
    exit();
} else {
    header("Location: list_issues.php");
    exit();
}

