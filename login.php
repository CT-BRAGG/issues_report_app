<!-- 
LOGING SCRIPT
desc: checks the entered user data against database and redirects to dashboard.php if correct 
author: Carson Bragg; chatgpt
-->

<?php
    session_start();
    require_once 'database/db_connection.php'; 

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Prepare and execute query to retrieve user details based on the email
        $stmt = $conn->prepare("SELECT id, fname, lname, email, pwd_hash, pwd_salt, admin FROM iss_persons WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        // If the email exists in the database
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $fname, $lname, $db_email, $db_pwd_hash, $db_pwd_salt, $admin);
            $stmt->fetch();

            // Generate MD5 hash of the provided password with the stored salt
            $password_hash = md5($password . $db_pwd_salt);

            // Verify if the generated hash matches the stored password hash
            if ($password_hash === $db_pwd_hash) {
                // Password is correct, create a session for the user
                $_SESSION['user_id'] = $id;
                $_SESSION['fname'] = $fname;
                $_SESSION['lname'] = $lname;
                $_SESSION['email'] = $email;
                $_SESSION['admin'] = $admin;

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

        $stmt->close();
    }

    // Close the database connection
    $conn->close();
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

            <p>Don't have an account? <a href="register_new_user.php">Register here</a></p>
        </div>
    </body>
</html>
