<!--
DASHBOARD
desc: contains all the various actions that user can perform
author: Carson Bragg; chatgpt
-->

<?php
    session_start();

    // Check if the user is logged in, if not, redirect to login page
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    // Get user details from session
    $fname = $_SESSION['fname'];
    $lname = $_SESSION['lname'];
    $email = $_SESSION['email'];
    $admin = $_SESSION['admin'];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard - Issue Reporting App</title>
        <link rel="stylesheet" href="../paper-style.css"> <!-- Add your CSS file here -->
	<style>
		a:link { 
			color: #d8a657; /* unvisited links */
			text-decoration: none;
		}
		a:visited { color: #543937; /*visited links */}
		a:hover {
			color: #e78a4e; /* hover */
			text-decoration: none;
		}
		a:active { 
			color: #fb4934; /*active/clicking*/
  			text-decoration: underline; /* Optional */
		}
	</style>
    </head>
    <body>
	<div class="paper">
	<div class="container">
        <div class="dashboard-container">
	    <h1>Dashboard</h1>
	    <p>The dashboard is the main hub for logged-in users, providing access to various features of the Issue Reporting App. To access it, you must be logged in with a valid account. Once authenticated, the dashboard displays your name, email, and role (either Admin or User) using information stored in your session. From here, you can view your submitted issues, account details, and access all submitted issues, all accounts, and all comments. If you try to access the dashboard without logging in, you will be redirected to the login page. Make sure to use the logout button when you are done to securely end your session; you will not be logged out automatically otherwise.</p> <br>
            <h2>Welcome to the Dashboard, <?php echo htmlspecialchars($fname . ' ' . $lname); ?>!</h2>
            
            <div class="user-info">
                <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                <p><strong>Role:</strong> <?php echo htmlspecialchars($admin === '1' ? 'Admin' : 'User'); ?></p>
            </div>
	    <br>
            
            <div class="actions">
                <h3>What would you like to do?</h3>
		<ul>
			<li><a href="">View My Account Info</a></li>
			<li><a href="">View My Submitted Issues</a></li>
			<li><a href="./issues/view_issues.php">View All Submitted Issues</a></li>
			<li><a href="">View All Accounts</a></li>
			<li><a href="">View All Comments</a></li>
		</ul>

		<!--
		<ul>
		<li>
                <form action="" method="post">
		    <div class="formButton">
                        <button type="submit">View My Issues</button>
                    </div>
                </form>
		</li>

		<li>
                <form action="" method="post">
		    <div class="formButton">
                        <button type="submit">View my Account Info</button>
                    </div>
               	</form>
		</li>

		<li>
                <form action="issues/view_issues.php" method="post">
		    <div class="formButton">
                        <button type="submit">View All Issues</button>
                    </div>
                </form>
		</li>

		<li>
                <form action="" method="post">
		    <div class="formButton">
                        <button type="submit">View All People</button>
                    </div>
                </form>
		</li>

		<li>
                <form action="" method="post">
		    <div class="formButton">
                        <button type="submit">View All Comments</button>
                    </div>
                </form>
		</li>
		</ul>
		-->

		<!--
                <ul>
                    <li><a href="issues/report_new_issue.php">Report a New Issue</a></li>
                    <li><a href="issues/view_issues.php">View All Issues</a></li>
                    <li><a href="">View My Issues</a></li>
                    <li><a href="">View All People</a></li>
                    <li><a href="">View My Comments</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
		-->
            </div> 
            <br>
            <br>
            <br>
            <br>
            <br>

            <form  action="logout.php" method="post">
                <div class="toDashboard">
                     <button type="submit">Logout</button>
                </div>
            </form>

            </div>
            </div>
        </div>
    </body>
</html>
