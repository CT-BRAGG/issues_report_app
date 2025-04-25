<!--

-->
<?php
session_start();
$issue_id = $_POST['issue_id'];
echo "issue#: $issue_id";
header("Location: ../issues/view_issue_detail.php?id=$issue_id");
exit;
?>


