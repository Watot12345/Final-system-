<?php
session_start();
include 'connect.php';

// Prevent any output before JSON response
ob_clean();

// Ensure only JSON is sent
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$qr_secret = $data['qr_secret'] ?? '';
$user_id = $data['user_id'] ?? '';
$scan_mode = $data['scan_mode'] ?? '';

if (empty($qr_secret) || empty($user_id) || empty($scan_mode)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit;
}

try {
    // Verify QR code matches user
    $stmt = $con->prepare("SELECT id, full_name FROM users WHERE qr_secret = ? AND id = ?");
    $stmt->bind_param("si", $qr_secret, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $date = date('Y-m-d');
        $current_time = date('H:i:s');
        
        if ($scan_mode === 'in') {
            // Check if already timed in today
            $check_query = "SELECT * FROM users WHERE user_id = ? AND date = ? AND check_in_time IS NOT NULL";
            $check_stmt = $con->prepare($check_query);
            $check_stmt->bind_param("is", $user_id, $date);
            $check_stmt->execute();
            
            if ($check_stmt->get_result()->num_rows > 0) {
                echo json_encode(['success' => false, 'message' => 'Already timed in today']);
                exit;
            }

            $status = ($current_time <= '08:00:00') ? 'On Time' : 'Late';
            $insert_query = "INSERT INTO users (user_id, full_name, date, check_in_time, status) VALUES (?, ?, ?, ?, ?)";
            $insert_stmt = $con->prepare($insert_query);
            $insert_stmt->bind_param("issss", $user_id, $user['full_name'], $date, $current_time, $status);
            
            if ($insert_stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Time in successful']);
            } else {
                throw new Exception('Error recording time in');
            }
        } else {
            // Time out logic
            $update_query = "UPDATE users SET check_out_time = ? WHERE user_id = ? AND date = ? AND check_in_time IS NOT NULL AND check_out_time IS NULL";
            $update_stmt = $con->prepare($update_query);
            $update_stmt->bind_param("sis", $current_time, $user_id, $date);
            
            if ($update_stmt->execute() && $update_stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Time out successful']);
            } else {
                throw new Exception('No valid time-in record found for today');
            }
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid QR code']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$con->close();