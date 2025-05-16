<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Error: Login First"; // Store error message in session
    header("Location: login-ad-use.php"); // Redirect to login page
    exit();
}

// Database connection
include 'connect.php';
// Get all leave requests for this user
$user_id = $_SESSION['user_id'];
$stmt = $con->prepare("SELECT leave_type, start_date, end_date, reason, stat  FROM users WHERE user_id = ? AND leave_type IS NOT NULL ORDER BY start_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Leave Requests</title>
    <link rel="stylesheet" href="css/styled.css">
    <link rel="shortcut icon" href="images/Icon.png" type="image/x-icon">
    <style>
        /* Additional styles for leave status table */
        .leave-status-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        
        .leave-status-table th {
            background-color: rgb(0, 255, 0);
            color: black;
        }
        
        .leave-status-table th, 
        .leave-status-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid rgb(0, 255, 0);
            color: white;
        }
        
        .pending { color: orange !important; }
        .approved { color: rgb(0, 255, 0) !important; }
        .rejected { color: red !important; }
        
        .no-requests {
            color: white;
            text-align: center;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="logo">
        <img src="images/Iconwhite.png" alt="">
    </div>
    <div class="container">
        <h1 style="color: white; border-bottom: 2px solid rgb(0, 255, 0); padding-bottom: 10px; margin-bottom: 20px; text-align: center;">
            My Leave Requests
        </h1>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message" style="color: rgb(0, 255, 0); padding: 10px; margin-bottom: 20px; border: 1px solid rgb(0, 255, 0);">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($result->num_rows > 0): ?>
            <table class="leave-status-table">
                <thead>
                    <tr>
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Reason</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo ucfirst($row['leave_type'] ?? ''); ?></td>
                            <td><?php echo date('M j, Y', strtotime($row['start_date'] ?? '')); ?></td>
                            <td><?php echo date('M j, Y', strtotime($row['end_date'] ?? '')); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($row['reason'] ?? '')); ?></td>
                            <td class="<?php echo $row['stat'] ?? ''; ?>">
                                <?php echo ucfirst($row['stat'] ?? ''); ?>
                                <?php if (($row['stat'] ?? '') == 'rejected' && !empty($row['manager_comment'] ?? '')): ?>
                                    <br><small style="color: white;">Reason: <?php echo htmlspecialchars($row['manager_comment'] ?? ''); ?></small>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-requests">You have no leave requests yet.</p>
        <?php endif; ?>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="submit.php" class="toggle-button" style="display: inline-block; width: auto; padding: 10px 20px; text-decoration: none; color: green;">
                Submit New Request
            </a>
        </div>
    </div>
</body>
</html>