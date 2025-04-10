<!-- 
LOGING SCRIPT
desc: checks the entered user data against database and redirects to dashboard.php if correct 
author: Carson Bragg; chatgpt
-->

<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the PDO database connection
require_once '/var/www/database/issDB/db_connection.php';

if (isset($_SESSION['user_id'])) {
    echo "Already logged in. Redirecting...";
    header('Refresh: 3; URL=dashboard.php');
    exit();
}
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare the query to retrieve user details based on the email
    $stmt = $pdo->prepare("SELECT id, fname, lname, email, pwd_hash, pwd_salt, admin FROM iss_persons WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Generate SHA-256 hash of the provided password + stored salt
        $password_hash = hash('sha256', $password . $user['pwd_salt']);

        // Compare hashes
        if ($password_hash === $user['pwd_hash']) {
            // Password is correct, start session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fname'] = $user['fname'];
            $_SESSION['lname'] = $user['lname'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['admin'] = $user['admin'];

            header('Location: dashboard.php');
            exit();
        } else {
            $error_message = "Incorrect email or password.";
        }
    } else {
        $error_message = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Issue Reporting App</title>
    <link rel="stylesheet" href="../paper-style.css">
</head>
<body>
<div class="paper">
    <div class="container">
        <div class="login-container">
	    <h1>Login</h1>
	    <p>To successfully log in, you must enter the exact same password you used when registering your account. The system secures your password by combining it with a unique salt (a random string) and hashing the result using the SHA-256 algorithm. This means your actual password is never stored—only the hashed version is saved. When you log in, the password you enter is combined with your saved salt and hashed again. If this new hash matches the one stored in the database, you are granted access. If not, the login will fail. Make sure there are no typos, extra spaces, or differences from the password you originally registered with.</p> <!-- generate with ai -->
	    <br>

            <h2>Login to Manage Issues</h2>

            <?php if (isset($error_message)): ?>
                <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" required>
                </div>
		<div class="formButton">
                    <button type="submit" class="login-btn">Login</button>
		</div>
            </form>

            <p>Don't have an account? <a href="register_new_user.php">Register here</a></p>

            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <form  action="../index.html" method="post">
                <div class="toDashboard">
                     <button type="submit">Back to Landing Page</button>
                </div>
            </form>

        </div>
    </div>
</div>
</body>
</html>
