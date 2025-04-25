<?php
session_start();
require_once '/var/www/database/issDB/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['admin'] ?? false;

// Check for person_id via POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['person_id'])) {
    header("Location: view_people.php");
    exit();
}

$person_id = $_POST['person_id'];

// Fetch person info
$stmt = $pdo->prepare("SELECT * FROM iss_persons WHERE id = ?");
$stmt->execute([$person_id]);
$person = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$person) {
    echo "Person not found.";
    exit();
}

$can_edit = $is_admin || $user_id == $person_id;

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['profile_update']) && $can_edit) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];

    $update_stmt = $pdo->prepare("UPDATE iss_persons SET fname = ?, lname = ?, email = ?, mobile = ? WHERE id = ?");
    $update_stmt->execute([$fname, $lname, $email, $mobile, $person_id]);

    $stmt->execute([$person_id]);
    $person = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Handle password update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password_update']) && $can_edit) {
    $old = $_POST['old_password'];
    $new1 = $_POST['new_password'];
    $new2 = $_POST['confirm_password'];

    $check_stmt = $pdo->prepare("SELECT pwd_hash, pwd_salt FROM iss_persons WHERE id = ?");
    $check_stmt->execute([$person_id]);
    $data = $check_stmt->fetch(PDO::FETCH_ASSOC);
    $valid = password_verify($old . $data['pwd_salt'], $data['pwd_hash']);

    if (!$valid) {
        $error = "Old password is incorrect.";
    } elseif ($new1 !== $new2) {
        $error = "New passwords do not match.";
    } else {
        $salt = bin2hex(random_bytes(8));
        $hash = password_hash($new1 . $salt, PASSWORD_DEFAULT);
        $update_pwd = $pdo->prepare("UPDATE iss_persons SET pwd_hash = ?, pwd_salt = ? WHERE id = ?");
        $update_pwd->execute([$hash, $salt, $person_id]);
        $success = "Password updated.";
    }
}

$issue_count = $pdo->prepare("SELECT COUNT(*) FROM iss_issues WHERE per_id = ?");
$issue_count->execute([$person_id]);
$issues = $issue_count->fetchColumn();

$comment_count = $pdo->prepare("SELECT COUNT(*) FROM iss_comments WHERE per_id = ?");
$comment_count->execute([$person_id]);
$comments = $comment_count->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Person Detail</title>
    <link rel="stylesheet" href="../../paper-style.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            left: 0; top: 0; width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: #f2e5bc;
            padding: 20px;
            border-radius: 12px;
            max-width: 600px;
            width: 90%;
        }
    </style>
    <script>
        function openEditModal() {
            document.getElementById('editModal').style.display = 'flex';
        }
        function openPasswordModal() {
            document.getElementById('passwordModal').style.display = 'flex';
        }
        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }
    </script>
</head>
<body>
<div class="paper">
    <h1><?= htmlspecialchars($person['fname'] . ' ' . $person['lname']) ?></h1>
    <table>
        <tr><th>Email</th><td><?= htmlspecialchars($person['email']) ?></td></tr>
        <tr><th>Mobile</th><td><?= htmlspecialchars($person['mobile']) ?></td></tr>
        <tr><th>Admin</th><td><?= $person['admin'] ? 'Yes' : 'No' ?></td></tr>
        <tr><th>Issues Reported</th><td><?= $issues ?></td></tr>
        <tr><th>Comments Made</th><td><?= $comments ?></td></tr>
    </table>

    <?php if (!empty($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php elseif (!empty($success)): ?>
        <p style="color: green;"><?= $success ?></p>
    <?php endif; ?>

    <?php if ($can_edit): ?>
    <div class="formButton">
        <button class="small-button" onclick="openEditModal()">Edit Profile</button>
        <button class="small-button" onclick="openPasswordModal()">Change Password</button>

    </div>

    <div class="formButton">
	<form method="POST" action="delete_person.php" style="margin-top: 20px;">
        	<input type="hidden" name="per_id" value="<?= htmlspecialchars($person['id']) ?>">
        	<button type="submit" class="small-button" style="background-color: #cc241d;">Delete Account</button>
    	</form>

    </div>
    <?php endif; ?>

    <br>
    <br>
    <br>
    <br>
    <div class="toDashboard">
        <a href="view_people.php"><button class="small-button">Back to People</button></a>
    </div>
</div>

<?php if ($can_edit): ?>
<!-- Edit Modal -->
<div class="modal" id="editModal" onclick="if(event.target==this)closeModal('editModal')">
    <div class="modal-content">
        <h3>Edit Profile</h3>
        <form method="POST">
            <input type="hidden" name="profile_update" value="1">
            <input type="hidden" name="person_id" value="<?= $person_id ?>">
            <label>First Name:</label>
            <input type="text" name="fname" value="<?= htmlspecialchars($person['fname']) ?>" required>
            <label>Last Name:</label>
            <input type="text" name="lname" value="<?= htmlspecialchars($person['lname']) ?>" required>
            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($person['email']) ?>" required>
            <label>Mobile:</label>
            <input type="text" name="mobile" value="<?= htmlspecialchars($person['mobile']) ?>" required>
            <div class="formButton">
                <button type="submit">Save</button>
                <button type="button" onclick="closeModal('editModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Password Modal -->
<div class="modal" id="passwordModal" onclick="if(event.target==this)closeModal('passwordModal')">
    <div class="modal-content">
        <h3>Change Password</h3>
        <form method="POST">
            <input type="hidden" name="password_update" value="1">
            <input type="hidden" name="person_id" value="<?= $person_id ?>">
            <label>Old Password:</label>
            <input type="password" name="old_password" required>
            <label>New Password:</label>
            <input type="password" name="new_password" required>
            <label>Confirm New Password:</label>
            <input type="password" name="confirm_password" required>
            <div class="formButton">
                <button type="submit">Update Password</button>
                <button type="button" onclick="closeModal('passwordModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

    
    
</body>
</html>
