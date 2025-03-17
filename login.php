<!-- 
LOGING SCRIPT
desc: checks the entered user data against database and redirects to dashboard.php if correct 
author: Carson Bragg; chatgpt
-->

<?php
    session_start();

    // Include the database connection
    require_once 'database/db_connection.php'; // Include the PDO database connection

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Prepare the query to retrieve user details based on the email
        $stmt = $pdo->prepare("SELECT id, fname, lname, email, pwd_hash, pwd_salt, admin FROM iss_persons WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        // If the email exists in the database
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();

            // Generate MD5 hash of the provided password with the stored salt
            $password_hash = md5($password . $user['pwd_salt']);

            // Verify if the generated hash matches the stored password hash
            if ($password_hash === $user['pwd_hash']) {
                // Password is correct, create a session for the user
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['fname'] = $user['fname'];
                $_SESSION['lname'] = $user['lname'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['admin'] = $user['admin'];

                // Redirect to the issues reporting dashboard or any protected area
                header('Location: dashboard.php');
                exit();
            } else {
                // Password is incorrect
                $error_message = "Incorrect email or password.";
            }
        } else {
            // Email doesn't exist in the database
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
        <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file here -->
    </head>
    <body>
        <div class="login-container">
            <h2>Login to Report Issues</h2>
            
            <?php
            if (isset($error_message)) {
                echo '<div class="error-message">' . $error_message . '</div>';
            }
            ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <button type="submit" class="login-btn">Login</button>
            </form>

            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </body>
</html>
