<?php
session_start();
include 'connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch record
    $result = $con->query("SELECT * FROM users WHERE id = '$id'");
    $record = $result->fetch_assoc();

    if (isset($_POST['update'])) {
        $date = $_POST['date'];
        $check_in_time = $_POST['check_in_time'];
        $check_out_time = $_POST['check_out_time'];
        $status = $_POST['status'];

        // Update record
        $con->query("UPDATE users SET date = '$date', check_in_time = '$check_in_time', check_out_time = '$check_out_time', status = '$status' WHERE id = '$id'");

        header('Location: records.php');
        exit;
    }
} else {
    header('Location: records.php');
    exit;
}

function convertToFullDate($date) {
    if (empty($date)) {
        return 'N/A';
    }
    return date('Y-m-d', strtotime($date));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Record</title>
    <link rel="stylesheet" href="css/styled.css">
    <style>
        .edit-container {
    max-width: 500px;
    margin: 8% auto;
    background-color: transparent;
    border: 1px solid green;
    border-radius: 10px;
    padding: 25px 30px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.edit-container h2 {
    text-align: center;
    margin-bottom: 20px;
    font-size: 24px;
    color: white;
}

.edit-container form label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: white;
}

.edit-container form input[type="date"],
.edit-container form input[type="time"],
.edit-container form input[type="text"] {
    width: 100%;
    color: white;
    padding: 10px 12px;
    background: green;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
    box-sizing: border-box;
    transition: 0.3s border-color ease-in-out;
}

.edit-container form input[type="date"]:focus,
.edit-container form input[type="time"]:focus,
.edit-container form input[type="text"]:focus {
    border-color: green;
    outline: none;
}

.edit-container form input[type="submit"] {
    background-color: green;
    color: white;
    padding: 10px 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    transition: 0.3s background-color ease-in-out;
}

.edit-container form input[type="submit"]:hover {
    background-color: #0056b3;
}

    </style>
</head>
<body>
<div class="logo-left">
        <img src="images/Iconwhite.png" alt="Siglatrolap">
    </div>

    <div class="sidebar">
  <p style="text-align: center; margin-top: -10px ; margin-left: 98.3%; position: absolute; font-size: 27px;">
                <a href="status.php" style="color: rgb(0, 255, 0); text-decoration none;">🔔</a>
            </p>
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



    <div class="edit-container">
        <h2>Edit Record</h2>
        <form action="" method="post">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" value="<?php echo $record['date']; ?>"><br><br>
            <label for="check_in_time">Check In Time:</label>
            <input type="time" id="check_in_time" name="check_in_time" value="<?php echo $record['check_in_time']; ?>"><br><br>
            <label for="check_out_time">Check Out Time:</label>
            <input type="time" id="check_out_time" name="check_out_time" value="<?php echo $record['check_out_time']; ?>"><br><br>
            <label for="status">Status:</label>
            <input type="text" id="status" name="status" value="<?php echo $record['status']; ?>"><br><br>
            <input type="submit" name="update" value="Update">
        </form>
    </div>
</body>
</html>