<?php
session_start();
include 'con.php';
// Register
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validate input fields
    if (empty($name) || empty($email) || empty($username) || empty($password)) {
        echo '<p style="color: red;">All fields are required!</p>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '<p style="color: red;">Invalid email format!</p>';
    } elseif (strlen($password) > 8) {
        echo '<p style="color: red; position: absolute; margin-left: 45.3%; margin-top: 30%;">Must at least 8 characters!</p>';
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Check if username already exists
        $stmt = $con->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            echo '<p style="color: red;position: absolute; margin-left: 49.6%; margin-top: 24%;">Username taken!</p>';
            $stmt->close();
        } else {
            $stmt->close();

            // Check if email already exists
            $stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                echo '<p style="color: red; position: absolute; margin-left: 47.8%; margin-top: 19%;">Email already in use!</p>';
            } else {
                $stmt->close();

                // Insert new user
                $stmt = $con->prepare("INSERT INTO users (full_name, username, password, email, user_id,leave_type,start_date, end_date, stat, reason) VALUES (?, ?, ?, ?,NULL,NULL,NULL,NULL,NULL,NULL)");
                $stmt->bind_param("ssss", $name, $username, $hashed_password, $email);

                if ($stmt->execute()) {
                    echo '<p style="color: green; position: absolute; margin-left: 50.6%; margin-top: 18%;">Succseful Signin</p>';
                } else {
                    echo '<p style="color: red;">Error: ' . $stmt->error . '</p>';
                }
                $stmt->close();
            }
        }
    }
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siglatrolap Innovation Login</title>
    <link rel="stylesheet" href="styled.css">
    <link rel="shortcut icon" href="Icon.png" type="image/x-icon">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Additional styles for icons */
        .input-group {
            position: relative;
            margin-bottom: 15px;
        }
        
        .input-group i:not(.password-toggle) {
            position: absolute;
            left: 10px;
            top: 38px;
            color: rgb(0, 255, 0);
        }
        
        .input-group input {
            padding-left: 35px !important;
            padding-right: 35px !important; /* Added for toggle icon */
        }
        
        /* Password toggle on the right side */
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 45px;
            cursor: pointer;
            color: rgb(0, 255, 0);
        }
        
        /* Special case for password field - lock on left, eye on right */
        .input-group.password-field i.fa-lock {
            left: 10px;
            right: auto;
        }
    </style>
</head>

<body>
    <div class="logo-sections">
        <img src="Logo.png" alt="">
    </div>

    <!-- Form Sign in -->
    <section class="form-signup">
        <form action="signup-ad-use.php" method="POST">
            <div>
                <div class="input-group">
                    <label for="name">Name:</label>
                    <i class="fas fa-user"></i>
                    <input type="text" id="name" name="name" placeholder="Name...">
                </div>
                
                <div class="input-group">
                    <label for="email">Email:</label>
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="Enter email...">
                </div>
                
                <div class="input-group">
                    <label for="username">Username:</label>
                    <i class="fas fa-user-tag"></i>
                    <input type="text" id="username" name="username" placeholder="Enter Username...">
                </div>
                
                <div class="input-group password-field">
                    <label for="password">Password:</label>
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Enter Password...">
                    <i class="fas fa-eye-slash password-toggle" id="togglePassword"></i>
                </div>
            </div>
            <div class="signup-button">
                <button type="submit" name="register">
                    <i class="fas fa-user-plus"></i> Sign Up
                </button>
                <br><br>
                <hr>
                <br>
                <button type="submit">
                    <i class="fab fa-google"></i> Connect to Gmail
                </button>
                <p>&nbsp;<i class="fas fa-user-check"></i> Have An Account?<a href="login-ad-use.php">&nbsp;Log In Here</a></p>
            </div>
        </form>
    </section>

    <script>
        // Password visibility toggle
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
            this.classList.toggle('fa-eye');
        });
    </script>
</body>
</html>
