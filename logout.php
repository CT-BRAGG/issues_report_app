<!-- 
LOGOUT SCRIPT
desc: removes session data and redirects to login.php
author: Carson Bragg; chatgpt
-->

<?php
    session_start();
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
?>
