<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Error: Login First"; // Store error message in session
    header("Location: login.php"); // Redirect to login page
    exit();
}
include 'connect.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styled.css">
    <link rel="shortcut icon" href="images/Icon.png" type="image/x-icon">
    <title>Dashboard</title>
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

    <div class="land-text">
        <p>HI <span class="one-name">
            <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';?>
              </span>!, WELCOME TO SIGLATROLAP</p>
            <h1>THE SPARKS THAT<span class="solo-one">&nbsp;TRANSFORMS</span> IDEAS INTO <span class="solo-two">REALITY</span></h1>
        <button class="land-page-btn">
            <a href="about.html">ABOUT US</a></a>
        </button>
    </div>

  <footer class="footer">
    <hr>
    <div class="footer-links">
    <h1>&copy; 2025 Siglatrolap Innovations Inc</h1>
        <a href="About.html">About Us</a>
        <a href="#">Need Help?</a>
        <a href="#">Content Guide</a>
        <a href="#">Terms of Use</a>
    </div>
  </footer>
  

    <script>
document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('.sidebar-toggle');
  const sidebar = document.querySelector('.sidebar');
  const closeBtn = document.querySelector('.sidebar-close');

  toggle.addEventListener('click', () => {
    sidebar.classList.toggle('active');
  });

  closeBtn.addEventListener('click', () => {
    sidebar.classList.remove('active');
  });

  document.addEventListener('click', (event) => {
    if (!sidebar.contains(event.target) && event.target !== toggle) {
      sidebar.classList.remove('active');
    }
  });
});
</script>
</body>
</html>

