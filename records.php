<?php
session_start();
include 'connect.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test database connection
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Count employees
$employeeQuery = $con->query("SELECT COUNT(DISTINCT full_name) as emp_count FROM users WHERE role = 'employee'");
$employeeResult = $employeeQuery->fetch_assoc();
$employeeCount = $employeeResult['emp_count'] ?? 0;

// Helper functions
function convertTo12HourFormat($time) {
    if (empty($time) || $time == '00:00:00') return 'N/A';
    return date('h:i:s A', strtotime($time));
}

function convertToFullDate($date) {
    if (empty($date) || $date == '0000-00-00') return 'N/A';
    return date('F j, Y', strtotime($date));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Attendance Record</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styled.css">
    <link rel="shortcut icon" href="images/Icon.png" type="image/x-icon">
    <style>
        .employee-list{
            color: green;
            margin-top: 10px;
            text-align: center;
            border-bottom: 1px solid green ;
        }
        .employee-list h4{
            font-size: 26px;
        }

    .emp {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin: 20px 0;
    justify-content: center;
    align-items: center; /* vertically center items if they have different heights */
    width: 100%; /* ensure full width */
}

/* Style for the employee buttons */
.emp .employee-button {
    display: inline-block;
    padding: 8px 15px;
    background-color: #333;
    color: #00ff00;
    text-decoration: none;
    border: 1px solid #00ff00;
    border-radius: 8px;
    transition: all 0.3s ease;
    text-align: center;
    min-width: 120px; /* ensures buttons have consistent width */
}

.emp .employee-button:hover {
    background-color: #00ff00;
    color: #000;
    font-weight: bold;
}

/* Active/current employee button */
.emp .employee-button.active {
    background-color: #00ff00;
    color: #000;
    font-weight: bold;
}
.attendance-record{
    padding: 20px;
    text-align: center;
}
.attendance-record h3{
    color: green;
}

    </style>
</head>
<body>
<div class="logo-left">
        <img src="images/Iconwhite.png" alt="Siglatrolap">
    </div>

    <div class="sidebar">
        <?php
        // Check the role stored in the session
        if ($_SESSION['role'] == 'employee') {
        ?>
            <!-- Employee Navigation -->
            <ul>
                <li><a href="home.php">HOME</a></li>
                <li><a href="attendance.php">ATTENDANCE</a></li>
                <li><a href="payroll.php">PAYSLIP</a></li>
                 <li><a href="submit.php">SUBMIT LEAVE</a></li>
                <li><a href="logout.php">LOGOUT</a></li>
            </ul>
        <?php } else { ?>
            <!-- Admin Navigation -->
            <ul>
                <li><a href="home.php">HOME</a></li>
                <li><a href="records.php">RECORDS</a></li>
                <li><a href="manage.php">MANAGE PAYSLIP</a></li>
                <li><a href="request.php">LEAVE REQUEST</a></li>
                <li><a href="logout.php">LOGOUT</a></li>
            </ul>
        <?php } ?>
  </div>
    <div class="container">
        <h1>Employee Attendance Record</h1>
            <br><br>
        <div class="employee-list">
            <h4>Employee List:</h4>
        <div>   
    </div>
            <?php if ($employeeCount > 0): ?>
                <?php
                // Fetch employees with attendance records
                $employees = $con->query("
                    SELECT full_name, MIN(id) as id, COUNT(*) as record_count
                    FROM users
                    WHERE role = 'employee' AND date IS NOT NULL AND date != '0000-00-00'
                    GROUP BY full_name
                    ORDER BY full_name
                ");
                ?>

                <?php if ($employees && $employees->num_rows > 0): ?>
                    <div class="emp">
                        <?php while ($emp = $employees->fetch_assoc()): ?>
                            <a href="?user_id=<?php echo (int)$emp['id']; ?>" class="employee-button">
                                <?php echo htmlspecialchars($emp['full_name']); ?> (<?php echo $emp['record_count']; ?>)
                            </a>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="no-employees">No employees found.</p>
                <?php endif; ?>
            <?php else: ?>
                <p class="no-employees">No employees in database.</p>
            <?php endif; ?>
        </div>

        <?php if (isset($_GET['user_id'])): ?>
            <?php
            $employee_id = (int)$_GET['user_id'];

            // Fetch employee full name
            $employee = $con->query("SELECT full_name FROM users WHERE id = $employee_id AND role = 'employee' LIMIT 1")->fetch_assoc();

            if ($employee):
                // Fetch attendance records from users table itself
                $records = $con->query("
                    SELECT * FROM users
                    WHERE full_name = '" . $con->real_escape_string($employee['full_name']) . "' 
                      AND role = 'employee' 
                      AND date IS NOT NULL 
                      AND date != '0000-00-00'
                    ORDER BY date DESC
                ");
            ?>
            <div class="attendance-record" style="margin-top: 30px;">
                <h3>
                    <?php echo htmlspecialchars($employee['full_name']); ?>'s Attendance Records
                </h3>

                <?php if ($records && $records->num_rows > 0): ?>
                    <table style="width: 100%; color: white; margin-top: 20px;">
                        <thead style="background-color: #333;">
                            <tr>
                                <th style="padding: 10px;">Date</th>
                                <th style="padding: 10px;">Time In</th>
                                <th style="padding: 10px;">Time Out</th>
                                <th style="padding: 10px;">Status</th>
                                <th style="padding: 10px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($record = $records->fetch_assoc()): ?>
                                <tr style="border-bottom: 1px solid #444;">
                                    <td style="padding: 10px;"><?php echo convertToFullDate($record['date']); ?></td>
                                    <td style="padding: 10px;"><?php echo convertTo12HourFormat($record['check_in_time']); ?></td>
                                    <td style="padding: 10px;"><?php echo convertTo12HourFormat($record['check_out_time']); ?></td>
                                    <td style="padding: 10px;"><?php echo htmlspecialchars($record['status']); ?></td>
                                    <td style="padding: 10px;">
                                        <a href="edit.php?id=<?php echo (int)$record['id']; ?>" style="color: #00ff00;">Edit</a> | 
                                        <a href="delete.php?id=<?php echo (int)$record['id']; ?>" style="color: #ff0000;" onclick="return confirm('Are you sure?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color: white;">No attendance records found for this employee.</p>
                <?php endif; ?>
            </div>
            <?php else: ?>
                <p class="no-employees">Employee not found.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>