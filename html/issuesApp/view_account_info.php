<!--
View Account Information Script
author: Carson Bragg; Chatgpt
desc: This is a wrapper script for the script that
      views a user's information. This redirects to
      the current user's acount information. 
-->

<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$current_user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Redirecting...</title>
</head>
	<body>
		<form id="redirectForm" method="POST" action="./person/view_person_detail_with_modal_with_delete.php">
    			<input type="hidden" name="person_id" value="<?= htmlspecialchars($current_user_id) ?>">
		</form>
		<script>document.getElementById('redirectForm').submit();</script>
	</body>
</html>
