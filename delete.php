<?php
session_start();
include 'connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete record
    $con->query("DELETE FROM users WHERE id = '$id'");

    header('Location: records.php');
    exit;
} else {
    header('Location: records.php');
    exit;
}
?>