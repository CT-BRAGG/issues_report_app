<!-- 
VIEW ISSUES SCRIPT
desc: displays issues which are relevant to the current user
author: Carson Bragg; chatgpt 
-->

<?php
// Include database connection
require_once 'database/db_connection.php';
require 'functions.php';

// Start the session (if not already started)
session_start();

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch issues from the database
$query = "SELECT * FROM iss_issues";
$stmt = $pdo->prepare($query);
$stmt->execute();
$issues = $stmt->fetchAll();

// Function to fetch comments for a given issue
function getComments($issueId, $pdo) {
    $query = "SELECT * FROM iss_comments WHERE iss_id = :issue_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['issue_id' => $issueId]);
    return $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>View Issues</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <div class="container">
            <h1>View Issues</h1>

            <table>
                <thead>
                    <tr>
                        <th>Issue ID</th>
                        <th>Short Description</th>
                        <th>Priority</th>
                        <th>Organization</th>
                        <th>Project</th>
                        <th>Open Date</th>
                        <th>Comments</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($issues as $issue): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($issue['id']); ?></td>
                            <td><?php echo htmlspecialchars($issue['short_description']); ?></td>
                            <td><?php echo htmlspecialchars($issue['priority']); ?></td>
                            <td><?php echo htmlspecialchars($issue['org']); ?></td>
                            <td><?php echo htmlspecialchars($issue['project']); ?></td>
                            <td><?php echo htmlspecialchars($issue['open_date']); ?></td>
                            <td>
                                <button onclick="toggleComments(<?php echo $issue['id']; ?>)">View Comments</button>
                                <div id="comments-<?php echo $issue['id']; ?>" style="display:none;">
                                    <ul>
                                        <?php 
                                        $comments = getComments($issue['id'], $pdo);
                                        foreach ($comments as $comment): 
                                        ?>
                                            <li>
                                                <strong>Posted on <?php echo htmlspecialchars($comment['posted_date']); ?>:</strong>
                                                <p><strong><?php echo htmlspecialchars($comment['short_comment']); ?></strong></p>
                                                <p><?php echo nl2br(htmlspecialchars($comment['long_comment'])); ?></p>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <form action="add_comment.php" method="post">
                                        <input type="hidden" name="issue_id" value="<?php echo $issue['id']; ?>">
                                        <textarea name="short_comment" placeholder="Short comment" required></textarea>
                                        <textarea name="long_comment" placeholder="Long comment" required></textarea>
                                        <button type="submit">Add Comment</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="container">
            <h1>Redirect to Dashboard</h1>
            <!-- Form with a button to redirect to the dashboard -->
            <form method="POST">
                <button type="submit" name="redirect_to_dashboard" class="btn">Go to Dashboard</button>
            </form>
        </div>

        <script>
            function toggleComments(issueId) {
                var commentsDiv = document.getElementById("comments-" + issueId);
                commentsDiv.style.display = (commentsDiv.style.display === "none" ? "block" : "none");
            }
        </script>
    </body>
</html>
