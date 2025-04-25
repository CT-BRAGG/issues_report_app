<!-- 
this might be an outdated piece of code
-->

<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['issue_id'])) {
    $_SESSION['issue_id'] = $_POST['issue_id'];
    //header("Location: view_issue_detail_with_comments.php");
    //header("Location: view_issue_detail_updated.php");
    //header("Location: view_issue_detail_with_comment_layout.php");
    header("Location: view_issue_detail_with_comment_owner_handling.php");
    exit();
} else {
    header("Location: view_issues.php");
    exit();
}
