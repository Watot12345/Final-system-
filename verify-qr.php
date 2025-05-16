<?php
session_start();
include 'connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$qr_secret = $data['qr_secret'] ?? '';

if (empty($qr_secret)) {
    echo json_encode(['success' => false]);
    exit;
}

$stmt = $con->prepare("SELECT id, full_name, username, role FROM users WHERE qr_secret = ?");
$stmt->bind_param("s", $qr_secret);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Set session variables
    $_SESSION["user_id"] = $user['id'];
    $_SESSION["full_name"] = $user['full_name'];
    $_SESSION["username"] = $user['username'];
    $_SESSION["role"] = $user['role'];
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}

$stmt->close();
$con->close();
?>