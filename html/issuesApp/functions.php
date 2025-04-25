<!-- 
FUNCTIONS SCRIPTS
author: Carson Bragg; chatgpt
desc: This contains a few global functions
      used in the website. These might
      be outdated. 
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
