<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['last_attempt'])) {
    $_SESSION['last_attempt'] = 0;
}

$current_time = time();
$cooldown = 0;

if ($current_time - $_SESSION['last_attempt'] < $cooldown) {
    $remaining = $cooldown - ($current_time - $_SESSION['last_attempt']);
    die("Please wait $remaining seconds before trying again.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $_SESSION['last_attempt'] = $current_time;
}

function confirmPassword($password, $confirmPassword) {
    return $password === $confirmPassword;
}

if (isset($_SESSION['error'])) {
    echo '<p style="color: red; text-align: center; font-weight: bold; margin-top: 11%; position: absolute; left: 44%;">' . htmlspecialchars($_SESSION['error']) . '</p>';
    unset($_SESSION['error']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($password) || empty($confirmPassword)) {
        $_SESSION['error'] = "All fields are required!";
        header("Location: login.php");
        exit();
    }

    if (!confirmPassword($password, $confirmPassword)) {
        $_SESSION['error'] = "Passwords do not match!";
        header("Location: login.php");
        exit();
    }

    $stmt = $con->prepare("SELECT id, full_name, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $full_name, $hashed_password, $role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["full_name"] = $full_name;
            $_SESSION["username"] = $username;
            $_SESSION["role"] = $role;

            header("Location: home.php");
            exit();
        } else {
            $_SESSION['error'] = "Incorrect Email or Password";
        }
    } else {
        $_SESSION['error'] = "User not found!";
    }

    $stmt->close();
    $con->close();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siglatrolap Innovation Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="Icon.png" type="image/x-icon">
    <script src="https://unpkg.com/html5-qrcode"></script>
    <link rel="stylesheet" href="css/styled.css">

    <style>
        

        .input-group i:not(.password-toggle) {
            position: absolute;
            left: 15px;
            top: 76%;
            transform: translateY(-50%);
            color: rgb(0, 255, 0);
            pointer-events: none;
        }


        .input-group.password-field i.fa-lock {
            left: 15px;
        }

        .qr-dlbtn {
            width: 145px;
            color: green;
            background: transparent;
            margin-bottom: 20px;
            border: none;
            border-top: 1px solid green;
            border-bottom: 1px solid green;
            padding: 7px 7px;
        }
        
    </style>
</head>
<body>

    <div class="logo-section">
        <img src="images/Logo.png" alt="Company Logo">
    </div>

    <div class="login-options">
        <button id="manualLoginBtn" class="active"><i class="fas fa-keyboard"></i>Manual Login</button>
        <button id="qrLoginBtn"><i class="fas fa-qrcode"></i>QR Code Login</button>
    </div>

<section class="form-login">
    <form action="login.php" method="post" id="manualLoginForm" class="fade show">
        <div class="form-styling">
                <div class="input-group">
                    <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Enter Username..." autocomplete="off">
                    <i class="fas fa-user"></i>
                </div>  

                <div class="input-group">
                    <label for="password">Password</label>
                        <i class="fas fa-user-tag"></i>
                    <input type="password" id="password" name="password" placeholder="Enter Password..." autocomplete="off">
                        <i class="fas fa-eye-slash password-toggle" id="togglePassword"></i>
                </div>

                <div class="input-group">
                    <label for="confirm_password">Confirm Password</label>
                        <i class="fas fa-user-tag"></i>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password..." autocomplete="off">
                        <i class="fas fa-eye-slash password-toggle" id="toggleConfirmPassword"></i>
                </div>
                    <a href="forgot.php" class="forgot-password">Forgot password?</a>
                <div class="form-button">
                    <button type="submit" name="submit" value="Login"><i class="fas fa-user-plus"></i>&nbsp;Log In</button>
                            <br><hr style="margin-bottom: 15px; margin-top: 15px;">
                    <button type="button"><i class="fab fa-google"></i>&nbsp;&nbsp;Connect to Gmail</button>
                            <p><i class="fas fa-user-check"></i>&nbsp;Not Registered?<a href="signup.php">Create Account</a></p>
                </div>
        </div>
    </form>
        <div id="qrLoginForm" class="hidden fade">
            <div id="reader"></div>
            <div id="qrMessage"></div>
        </div>
</section>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        let html5QrcodeScanner;

        function initQRScanner() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear();
            }

            html5QrcodeScanner = new Html5QrcodeScanner(
                "reader",
                { 
                    fps: 10, qrbox: { 
                        width: 250, height: 250 
                    }, 
                    rememberLastUsedCamera: true }
            );

            html5QrcodeScanner.render((qrCodeMessage) => {
                fetch('verify-qr.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ qr_secret: qrCodeMessage })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'home.php';
                    } else {
                        document.getElementById('qrMessage').innerHTML = '<p style="color: red;">Invalid QR Code</p>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('qrMessage').innerHTML = '<p style="color: red;">Error scanning QR code</p>';
                });
            }, (error) => {
                console.warn(`QR error: ${error}`);
            });
        }

const manualBtn = document.getElementById('manualLoginBtn');
const qrBtn = document.getElementById('qrLoginBtn');
const manualForm = document.getElementById('manualLoginForm');
const qrForm = document.getElementById('qrLoginForm');

    qrBtn.addEventListener('click', function () {
            this.classList.add('active');
            manualBtn.classList.remove('active');

            manualForm.classList.add('hidden');
            manualForm.classList.remove('show');

            qrForm.classList.remove('hidden');
            qrForm.classList.add('show');

            initQRScanner();
        });

        manualBtn.addEventListener('click', function () {
            this.classList.add('active');
            qrBtn.classList.remove('active');

            qrForm.classList.add('hidden');
            qrForm.classList.remove('show');

            manualForm.classList.remove('hidden');
            manualForm.classList.add('show');

            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear();
            }
        });
    });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
        const confirmPassword = document.querySelector('#confirm_password');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
            this.classList.toggle('fa-eye');
        });

        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPassword.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
            this.classList.toggle('fa-eye');
        });
    });
    </script>

</body>
</html>
