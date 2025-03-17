<!-- 
FUNCTIONS SCRIPTS
desc: global functions
-->
<?php
    function redirectToDashboard() {
        header("Location: dashboard.php"); // Redirect to the dashboard
        exit(); // Terminate the script to prevent further execution
    }
?>