<?php
// Start the session
session_start();
include 'connect.php';
// Check if the user is logged in and if the full_name session variable is set
if (!isset($_SESSION['full_name']) || empty($_SESSION['full_name'])) {
    die("Error: User is not logged in or full name is not set.");
}
// At the TOP of your script, after session_start()
$user_id = $_SESSION['user_id'] ?? $_POST['user_id'] ?? null;

// Validate it exists
if (empty($user_id)) {
    die("Error: User ID is required!");
}
// Fetch the full name from the session
$full_name = $_SESSION['full_name'];

$user_query = "SELECT id FROM users WHERE full_name = '$full_name' LIMIT 1";
$user_result = $con->query($user_query);
if($user_result->num_rows > 0){
  $user_row  = $user_result->fetch_assoc();
}else{
  die("error user not found in database");
}

// Handling Time In
if (isset($_POST['time_in'])) {
    $date = date('Y-m-d'); // Current date
    $check_in_time = date('H:i:s'); // Current time

    // Check if the user is already checked in for today
    $check_in_query = "SELECT * FROM users WHERE user_id = '$user_id' AND date = '$date' AND check_in_time IS NOT NULL";
    $check_in_result = $con->query($check_in_query);
    
    if ($check_in_result->num_rows > 0) {
        // User has already checked in today, so check if they are checking out
    
        // Step 2: Check if the user is trying to check out
        $check_out_result = $con->query("SELECT * FROM users WHERE user_id = '$user_id' AND date = '$date' AND check_out_time IS NOT NULL");
    
        if ($check_out_result->num_rows > 0) {
            echo '<span style="color: red; position: absolute;color: red; margin-left: 42%; margin-top: 10%;" >You have already checked out today!</span>';
        } else {
            // User has not checked out yet, check if it's after 5:00 PM (time out logic)
            $check_out_time = '17:00:00'; // The expected check-out time
            $status = "On Time";
    
            if ($check_out_time > $check_in_time && $check_in_time <= '17:30:00') {
                // Check-out time is valid (before 5:30 PM)
                $status = "On Time";
            } else if ($check_out_time > $check_in_time && $check_out_time > '17:30:00') {
                // Check-out time logic: after 5:30 PM (overtime)
                $status = "Overtime";
                $update_overtime = "UPDATE users SET check_out_time = '$check_out_time', status = '$status' WHERE user_id = '$user_id' AND date = '$date'";
                $con->query($update_overtime);
                echo '<span style="color: yellow;">You are checking out overtime!</span>';
            }
        }
    } else {
        // No check-in record found, so allow check-in
        if ($check_in_time == '08:00:00') {
            // Time-in is exactly 8:00 AM
            $status = 'On Time';
            echo '<span style="color: rgb(0, 255, 0) ; position: absolute; margin-left: 45%; margin-top: 10%;">time-in successfully</span>';
        } else if ($check_in_time < '08:00:00') {
            // Time-in is before 8:00 AM (Under Time)
            $status = 'Under Time';
            echo '<span style="color: rgb(0, 255, 0) ; position: absolute; margin-left: 40%; margin-top: 10%;">time-in successfully</span>';
            
        } else if ($check_in_time > '08:00:00') {
          $status = 'late';
          echo '<span style="color: rgb(0, 255, 0) ; position: absolute; margin-left: 40%; margin-top: 10%;">time-in successfully</span>';
        }
    
        // Insert the check-in record into the database
        $insert_time_in = "INSERT INTO users (user_id, password, username,full_name, date, check_in_time, status, leave_type, start_date, end_date, reason)
                           VALUES ('$user_id', '', '', '$full_name', '$date', '$check_in_time', '$status',NULL,NULL,NULL,NULL)";
    
        if ($con->query($insert_time_in) === TRUE) {
            echo '<span style="color: rgb(0, 255, 0); position: absolute; margin-left: 45%; margin-top: 10%;"><br>time-in recorded successfully</span>';
        } else {
            echo '<span style="color: red;">Error: ' . $con->error . '</span>'; // Output error message if query fails
        }
    }
    
}

// Handling Time Out
if (isset($_POST['time_out'])) {
    $date = date('Y-m-d'); // Current date
    $check_out_time = date('H:i:s'); // Current time

    // Check if the user has already checked out today
    $check_out_query = "SELECT * FROM users WHERE user_id = '$user_id' AND date = '$date' AND check_out_time IS NULL";
    $check_out_result = $con->query($check_out_query);

    if ($check_out_result->num_rows == 0) {
      echo '<span style="position: absolute; color: red; margin-left: 40%; margin-top: 10%;">You are not time in today or already time out!</span>';
    } else {
        // Update check-out time
        $update_time_out = "UPDATE users SET check_out_time = '$check_out_time' WHERE user_id = '$user_id' AND date = '$date'";
        if ($con->query($update_time_out) === TRUE) {
            echo '<span style="color: rgb(0, 255, 0) ;position: absolute; margin-left: 44%; margin-top: 10%;">time out recorded successfully</span>';
        } else {
          echo '<span style="color: red;">Error: ' . $con->error . '</span>'; // Output error message if query fails
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siglatrolap Innovations Inc</title>
    <link rel="stylesheet" href="css/styled.css">
    <link rel="shortcut icon" href="images/Icon.png" type="image/x-icon">
    <script src="https://unpkg.com/html5-qrcode"></script>
    <style>
        /* Additional styles for attendance page */
        .attendance-container {
            max-width: 500px;
            top: 20%;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }
        
        .live-clock {
            font-size: 2rem;
            color: rgb(0, 255, 0);
            margin: 20px 0;
            font-family: monospace;
        }
        
        .attendance-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 15px;
        }
        
        .attendance-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        

        #time_in_btn {
            background-color: rgb(0, 255, 0);
            color: black;
        }
        
        #time_out_btn {
            background-color: #ff3333;
            color: white;
        }
        
        .attendance-btn:hover {
            transform: translateY(-3px);
            background: green;
            color: black;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .footer {
            margin-top: 40px;
            color: white;
            font-size: 0.9rem;
            opacity: 0.8;
        }
        .attendance-options {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .option-btn {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        background: #f0f0f0;
        transition: all 0.3s;
        font-size: 1rem;
    }

    .option-btn.active {
        background: rgb(0, 255, 0);
        color: black;
    }

    #qrAttendanceForm {
        max-width: 400px;
        margin: 0 auto;
    }

    #reader {
        background: white;
        padding: 10px;
        border-radius: 8px;
    }

    #qrMessage {
        margin-top: 15px;
        color: rgb(0, 255, 0);
        font-weight: bold;
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

    <div class="attendance-container" style=" margin-top: 8%;">
        <div id="messageContainer">
        <?php 
        if (isset($_SESSION['attendance_message'])) {
            echo '<p style="color: rgb(0, 255, 0);">' . $_SESSION['attendance_message'] . '</p>';
            unset($_SESSION['attendance_message']); 
        }
        ?>
    </div>
    
    <div class="container">

        <div class="attendance-container">
            <h1 style="color: white; border-bottom: 2px solid rgb(0, 255, 0); padding-bottom: 10px; margin-bottom: 30px;">Attendance Tracking</h1>
    
        <div class="live-clock" id="liveClock"></div>
        <div class="attendance-options">
            <button id="manualAttendanceBtn" class="option-btn active">Manual Attendance</button>
            <button id="qrAttendanceBtn" class="option-btn">QR Code Scan</button>
    </div>

    <form method="POST" action="attendance.php" id="manualAttendanceForm">
        <div class="attendance-buttons">
            <button type="submit" name="time_in" id="time_in_btn" class="attendance-btn">Time In</button>
            <button type="submit" name="time_out" id="time_out_btn" class="attendance-btn">Time Out</button>
        </div>
    </form>

    <div id="qrAttendanceForm" style="display: none;">
        <div class="attendance-buttons">
            <button type="button" id="qrTimeInBtn" class="attendance-btn">QR Time In</button>
            <button type="button" id="qrTimeOutBtn" class="attendance-btn">QR Time Out</button>
        </div>
        <div id="reader" style="display: none;"></div>
        <div id="qrMessage"></div>
    </div>

<script>
// Check for stored message on page load
window.addEventListener('load', function() {
    const storedMessage = sessionStorage.getItem('attendance_message');
    if (storedMessage) {
        document.getElementById('messageContainer').innerHTML = 
            '<p style="color: rgb(0, 255, 0);">' + storedMessage + '</p>';
        sessionStorage.removeItem('attendance_message');
    }
});

</script>
    <script>
    // ...existing clock code...
    let html5QrcodeScanner;

function initQRScanner() {
    if (html5QrcodeScanner) {
        html5QrcodeScanner.clear();
    }
    
    html5QrcodeScanner = new Html5QrcodeScanner(
        "reader",
        { 
            fps: 60,  // Increased for faster scanning
            qrbox: 250,
            rememberLastUsedCamera: true,
            showTorchButtonIfSupported: true,
        }
    );
        
    html5QrcodeScanner.render(onScanSuccess, onScanError);
}

function onScanSuccess(qrCodeMessage) {
    fetch('verify-attendance.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ 
            qr_secret: qrCodeMessage,
            user_id: '<?php echo $user_id; ?>'
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json().catch(err => {
            throw new Error('Invalid JSON response from server');
        });
    })
    .then(data => {
        console.log('Server response:', data); // Debug line
        if (data.success) {
            document.getElementById('qrMessage').innerHTML = 
                '<p style="color: rgb(0, 255, 0);">' + data.message + '</p>';
            sessionStorage.setItem('attendance_message', data.message);
            setTimeout(() => {
                window.location.href = 'attendance.php';
            }, 2000);
        } else {
            document.getElementById('qrMessage').innerHTML = 
                '<p style="color: red;">' + (data.message || 'Unknown error occurred') + '</p>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('qrMessage').innerHTML = 
            '<p style="color: red;">Error: ' + error.message + '</p>';
    });
}

function onScanError(error) {
    // Only log serious errors
    if (error?.message?.includes('NotFound') || error?.message?.includes('NotAllowed')) {
        console.warn(`QR Scanner Error: ${error}`);
    }
}

let scanMode = ''; // 'in' or 'out'

document.getElementById('qrTimeInBtn').addEventListener('click', function() {
    scanMode = 'in';
    document.getElementById('reader').style.display = 'block';
    this.style.backgroundColor = 'rgb(0, 255, 0)';
    document.getElementById('qrTimeOutBtn').style.backgroundColor = '#f0f0f0';
    initQRScanner();
});

document.getElementById('qrTimeOutBtn').addEventListener('click', function() {
    scanMode = 'out';
    document.getElementById('reader').style.display = 'block';
    this.style.backgroundColor = '#ff3333';
    document.getElementById('qrTimeInBtn').style.backgroundColor = '#f0f0f0';
    initQRScanner();
});

function onScanSuccess(qrCodeMessage) {
    fetch('verify-attendance.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ 
            qr_secret: qrCodeMessage,
            user_id: '<?php echo $user_id; ?>',
            scan_mode: scanMode
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('qrMessage').innerHTML = 
                '<p style="color: rgb(0, 255, 0);">' + data.message + '</p>';
            setTimeout(() => {
                window.location.href = 'attendance.php';
            }, 2000);
        } else {
            document.getElementById('qrMessage').innerHTML = 
                '<p style="color: red;">' + data.message + '</p>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('qrMessage').innerHTML = 
            '<p style="color: red;">Error processing attendance</p>';
    });
}
</script>



<script>    // Switch between attendance methods
    document.getElementById('qrAttendanceBtn').addEventListener('click', function() {
        this.classList.add('active');
        document.getElementById('manualAttendanceBtn').classList.remove('active');
        document.getElementById('manualAttendanceForm').style.display = 'none';
        document.getElementById('qrAttendanceForm').style.display = 'block';
        initQRScanner();
    });

    document.getElementById('manualAttendanceBtn').addEventListener('click', function() {
        this.classList.add('active');
        document.getElementById('qrAttendanceBtn').classList.remove('active');
        document.getElementById('manualAttendanceForm').style.display = 'block';
        document.getElementById('qrAttendanceForm').style.display = 'none';
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear();
        }
    });
    </script>























    <script>
        // Live clock functionality with AM/PM format
        function updateClock() {
            const now = new Date();
            let hours = now.getHours();
            let minutes = now.getMinutes();
            let seconds = now.getSeconds();
            const ampm = hours >= 12 ? 'PM' : 'AM';

            // Convert 24-hour format to 12-hour format
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;

            const timeString = `${hours}:${minutes}:${seconds} ${ampm}`;
            document.getElementById('liveClock').textContent = timeString;
        }

        // Update clock every second
        setInterval(updateClock, 1000);

        // Initial clock update
        updateClock();
    </script>
</body>
</html>