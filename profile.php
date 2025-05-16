<?php
session_start();
include "db.php";
if(isset($_SESSION["role"]) && isset($_SESSION["id"])){
?>
<?php
$name = $email = $phone = $address = "";
$update = false;
$id = 0;

// CREATE: Add New Profile
if (isset($_POST['save'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $sql = "INSERT INTO profiles (name, email, phone, address) VALUES ('$name', '$email', '$phone', '$address')";
    if ($conn->query($sql) === TRUE) {
        header('Location: profile.php');
    } else {
        echo "Error: " . $conn->error;
    }
}

// READ: Get Profile Data for Edit
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $update = true;
    $sql = "SELECT * FROM profiles WHERE id=$id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $name = $row['name'];
    $email = $row['email'];
    $phone = $row['phone'];
    $address = $row['address'];
}

// UPDATE: Save Edited Profile
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $sql = "UPDATE profiles SET name='$name', email='$email', phone='$phone', address='$address' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        header('Location: profile.php');
    } else {
        echo "Error: " . $conn->error;
    }
}

// DELETE: Remove Profile
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM profiles WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        header('Location: profile.php');
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Profile</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
	<script src="https://kit.fontawesome.com/4079c191f4.js" crossorigin="anonymous"></script>
	
</head>    
<body>
	<input type="checkbox" id="checkbox">
	<?php include "header.php"?>
	<div class="body">
	<?php include "nav.php"?>
		<section class="section-1">
    <div class="container mt-4">
    <div class="user-p">
				<img src="3.png">
				<h2>@<?=$_SESSION['role']?></h2>
			</div>
        
        
        <!-- Profile Form -->
        <form action="profile.php" method="POST">
            <input type="hidden" name="id" value="<?= $id; ?>">
            <div class="mb-3">
                <label>Employee Id:</label>
                <input type="text" name="id" class="form-control" value="<?= $name; ?>" required>
            </div>
            <div class="mb-3">
                <label>Name:</label>
                <input type="text" name="name" class="form-control" value="<?= $name; ?>" required>
            </div>
            <div class="mb-3">
                <label>Email:</label>
                <input type="email" name="email" class="form-control" value="<?= $email; ?>" required>
            </div>
            <div class="mb-3">
                <label>Phone:</label>
                <input type="text" name="phone" class="form-control" value="<?= $phone; ?>" required>
            </div>
            <div class="mb-3">
                <label>Birthdate:</label>
                <input type="date" name="date" class="form-control" value="<?= $name; ?>" required>
            </div>
            <div class="mb-3">
                <label>Status:</label>
                <input type="text" class="form-control" required><?= $address; ?></input>
            </div>
            <div class="mb-3">
                <label>Address:</label>
                <textarea name="address" class="form-control" required><?= $address; ?></textarea>
            </div>
            <div class="mb-3">
                <label>Incase of emergency notify:</label>
                <input type="text" class="form-control" required><?= $address; ?></input>
            </div>
            <div class="mb-3">
                <label>Contact no. :</label>
                <input type="text" name="phone" class="form-control" value="<?= $phone; ?>" required>
            </div>
            <?php if ($update): ?>
                <button type="submit" name="update" class="btn btn-primary">Update Profile</button>
            <?php else: ?>
                <button type="submit" name="save" class="btn btn-success">Add Profile</button>
            <?php endif; ?>
        </form>

        <!-- Profile Table -->
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM profiles";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['phone']}</td>
                        <td>{$row['address']}</td>
                        <td>
                            <a href='profile.php?edit={$row['id']}' class='btn btn-primary btn-sm'>Edit</a>
                            <a href='profile.php?delete={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
		</section>
	</div>

</body>
</html>
<?php }else {
 $em = "error occur!!!";
 header("Location: login.php?error=$em");
 exit();
}	
?>
