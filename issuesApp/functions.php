<!-- 
FUNCTIONS SCRIPTS
desc: global functions
-->

<?php
    function redirectToDashboardFromIssues() {
        header("Location: ../dashboard.php"); // Redirect to the dashboard
        exit(); // Terminate the script to prevent further execution
    }
    funciton redirectToReportNewIssues() {
    	header("./report_new_issue.php");
	exit();
    }
?>
