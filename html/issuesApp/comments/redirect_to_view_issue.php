<!--
Redirect to View Issues Script
author: Carson Bragg; chatgpt
desc: This script converts the issue id to a parameter in
      the URL and redirects to the view issue details script. 
-->
<?php
session_start();
$issue_id = $_POST['issue_id'];
echo "issue#: $issue_id";
header("Location: ../issues/view_issue_detail.php?id=$issue_id");
exit;
?>


