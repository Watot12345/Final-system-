<?php
session_start();
include 'connect.php';

// Cooldown for spam protection
if (!isset($_SESSION['last_attempt'])) {
    $_SESSION['last_attempt'] = 0;
}

$current_time = time();
$cooldown = 30;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    if ($current_time - $_SESSION['last_attempt'] < $cooldown) {
        $_SESSION['error'] = "Please wait ".($cooldown - ($current_time - $_SESSION['last_attempt']))."seconds before trying again.";
        header("Location: signup.php");
        exit();
    }

    $_SESSION['last_attempt'] = $current_time;

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate fields
    if (empty($name) || empty($email) || empty($username) || empty($password)) {
        $_SESSION['error'] = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format!";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,64}$/', $password)) {
        $_SESSION['error'] = "Password must be 8-64 characters with uppercase, lowercase, number, and special character!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $con->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $_SESSION['error'] = "Username already taken!";
        } else {
            $stmt->close();
            $stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $_SESSION['error'] = "Email already in use!";
            } else {
                $stmt->close();
                $qr_secret = bin2hex(random_bytes(16));

                $stmt = $con->prepare("INSERT INTO users (full_name, username, password, email, qr_secret) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $name, $username, $hashed_password, $email, $qr_secret);

                if ($stmt->execute()) {
                    $_SESSION['success'] = "Successfully registered!";
                    $_SESSION['qr_secret'] = $qr_secret;
                    $_SESSION['username'] = $username;
                } else {
                    $_SESSION['error'] = "Error: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }

    header("Location: signup.php");
    exit();
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Siglatrolap Innovation Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styled.css">
    <link rel="shortcut icon" href="Icon.png" type="image/x-icon">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .input-group {
             position: relative; 
             margin-bottom: 15px;
             }
        .input-group i:not(.password-toggle) {
            position: absolute;
            left: 10px; 
            top: 40px; 
            color: rgb(0, 255, 0);
        }
        .input-group input {
            padding-left: 35px !important;
            padding-right: 35px !important;
        }
        .password-toggle {
            position: absolute; 
            right: 10px; 
            top: 48px;
            cursor: pointer;
            color: rgb(0, 255, 0);
        }
        .input-group.password-field i.fa-lock { 
            left: 10px;
        }
        .qr-dlbtn {
            width: 145px;
            color: green;
            background: transparent;
            margin-bottom: 20px;
            border:none;
            border-top: 1px solid green;
            border-bottom: 1px solid green;
            padding: 7px 7px;
        }
    </style>
</head>
<body>
    <div class="logo-section">
        <img src="images/Logo.png" alt="">
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <p style="color:red; text-align:center;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <p style="color:green; text-align:center;"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>

    <div id="qrcode">
        <button id="downloadBtn" class="qr-dlbtn">Download your QR</button>
    </div>

    <section class="form-signup">
        <form action="signup.php" method="POST">
            <div>
                <div class="input-group">
                    <label for="name">Name</label>
                    <i class="fas fa-user"></i>
                    <input type="text" id="name" name="name" placeholder="Name...">
                </div>

                <div class="input-group">
                    <label for="email">Email</label>
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="Enter email...">
                </div>

                <div class="input-group">
                    <label for="username">Username</label>
                    <i class="fas fa-user-tag"></i>
                    <input type="text" id="username" name="username" placeholder="Enter Username...">
                </div>

                <div class="input-group password-field">
                    <label for="password">Password</label>
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Enter Password...">
                    <i class="fas fa-eye-slash password-toggle" id="togglePassword"></i>
                </div>
            </div>
            <div class="signup-button">
                <button type="submit" name="register"><i class="fas fa-user-plus"></i>Sign Up</button>
                <br><br><hr><br>
                <button type="submit"><i class="fab fa-google"></i> Connect to Gmail</button>
                <p><i class="fas fa-user-check"></i> Have an account? <a href="login.php">Log In Here</a></p>
            </div>
        </form>
    </section>

    <script>
        // Password toggle
        document.getElementById('togglePassword').addEventListener('click', function () {
            const pass = document.getElementById('password');
            const type = pass.getAttribute('type') === 'password' ? 'text' : 'password';
            pass.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
            this.classList.toggle('fa-eye');
        });

        // QR Code display logic
        window.onload = function () {
            <?php if (isset($_SESSION['qr_secret'])): ?>
                const secret = "<?php echo $_SESSION['qr_secret']; ?>";
                const username = "<?php echo $_SESSION['username'] ?? 'Guest'; ?>";
                document.getElementById("qrcode").style.display = "block";
                new QRCode(document.getElementById("qrcode"), {
                    text: secret,
                    width: 128,
                    height: 128,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });

                document.getElementById('downloadBtn').addEventListener('click', function () {
                    const qrImg = document.querySelector('#qrcode img') || document.querySelector('#qrcode canvas');
                    const qrUrl = qrImg.src || qrImg.toDataURL();

                    const tempCard = document.createElement('div');
                    tempCard.style = "padding:20px; border:1px solid #ccc; border-radius:10px; max-width:600px; background:#f9f9f9; display:flex; flex-direction:column; align-items:center;";
                    tempCard.innerHTML = `
                        <h2>${username}</h2>
                        <p>Software Engineer</p>
                        <img src="${qrUrl}" style="width:128px; height:128px;">
                    `;
                    document.body.appendChild(tempCard);

                    html2canvas(tempCard).then(canvas => {
                        const imgData = canvas.toDataURL('image/png');
                        const { jsPDF } = window.jspdf;
                        const pdf = new jsPDF();
                        const pdfWidth = pdf.internal.pageSize.getWidth();
                        const pdfHeight = (canvas.height * pdfWidth) / canvas.width;
                        pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                        pdf.save('profile_card.pdf');
                        document.body.removeChild(tempCard);
                    });
                });
                <?php unset($_SESSION['qr_secret'], $_SESSION['username']); ?>
            <?php endif; ?>
        }
    </script>
</body>
</html>
