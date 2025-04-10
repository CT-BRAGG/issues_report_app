<?php
session_start();
require_once '/var/www/database/issDB/db_connection.php'; // This should create $pdo (PDO instance)

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $mobile = trim($_POST['mobile'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM iss_persons WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo "An account with this email already exists.";
        exit;
    }

    // Generate salt and hash
    $salt = bin2hex(random_bytes(16));
    $pwd_hash = hash('sha256', $password . $salt);

    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO iss_persons (fname, lname, mobile, email, pwd_hash, pwd_salt, admin)
                           VALUES (?, ?, ?, ?, ?, ?, '0')");
    if ($stmt->execute([$fname, $lname, $mobile, $email, $pwd_hash, $salt])) {
        echo "Registration successful. You can now <a href='login.php'>log in</a>.";
    } else {
        echo "Registration failed.";
    }
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
   <link rel="stylesheet" href="../paper-style.css">
</head>
<body>
<div class="paper">
    <div class="container">
	<h1>Register</h1>
	<p>To register a new account, you need to provide your first name, last name, a valid email address, and a password. You may also include a mobile number, but it is optional. The email address must be in a proper format (e.g., name@example.com) and must not already be associated with an existing account. When you submit the form, your password is secured using a process that combines it with a randomly generated salt (a unique string) and hashes the result using the SHA-256 algorithm. This ensures your actual password is never stored directly. Make sure to choose a strong, memorable password, as you will need to enter the exact same one to log in later.</p>
	<p>*Please do not use any passwords from other websites</p>
	<br>
	<br>
        <h2>Register a new Account</h2>
	<div class="form-group">
        <form method="POST" action="">
            First Name: <input type="text" name="fname" required><br>
            Last Name: <input type="text" name="lname" required><br>
            Mobile: <input type="text" name="mobile"><br>
            Email: <input type="email" name="email" required><br>
            Password: <input type="password" name="password" required><br>

    	    <div class="formButton">
		<!-- <input type="submit" value="Register"> 
		hopefull this button below works	
		-->
	        <button type="submit">Register</button>
    	    </div>
        </form>
	</div>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>

    <form action="login.php" method="post">
    <div class="toDashboard">
           <button type="submit">Cancel Registration</button>
    </div>
    </form>
	
    </div>
</div>
</body>
</html>

