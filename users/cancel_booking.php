<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['booking_id'])) {
    header("Location: my_bookings.php");
    exit();
}

$booking_id = $_POST['booking_id'];
$user_id = $_SESSION['user_id'];

$sql = "DELETE FROM bookings WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();

header("Location: my_bookings.php");
exit();
