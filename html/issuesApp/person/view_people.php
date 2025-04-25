<!--
View People Script
author: Carson Bragg; chatgpt
desc: displays a list of all users with links to some 
      information about them. 
-->

<?php
session_start();
require_once '/var/www/database/issDB/db_connection.php'; // Adjust this path as needed

// Fetch all people
$query = "SELECT id, fname, lname, email, mobile, admin FROM iss_persons ORDER BY lname, fname";
$stmt = $pdo->query($query);
$people = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All People - Issue Tracker</title>
    <link rel="stylesheet" href="../../paper-style.css">
    <style>
        tr.formRow {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="paper">
        <h1>All People</h1>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Admin</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($people as $person): ?>
                    <tr class="rows formRow" onclick="this.querySelector('form').submit();">
                        <td><?= htmlspecialchars($person['fname'] . ' ' . $person['lname']) ?></td>
                        <td><?= htmlspecialchars($person['email']) ?></td>
                        <td><?= htmlspecialchars($person['mobile']) ?></td>
			<td><?= (htmlspecialchars($person['admin']) == 1) ? "Yes" : "No" ?></td>
                        <!--<td><?= htmlspecialchars($person['admin']) ?></td> -->
                        <form method="POST" action="view_person_detail_with_modal_with_delete.php">
                            <input type="hidden" name="person_id" value="<?= $person['id'] ?>">
                        </form>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="toDashboard">
            <a href="../dashboard.php"><button class="small-button">Back to Dashboard</button></a>
        </div>
    </div>
</body>
</html>
