<?php
session_start();

// Connect to database
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if event ID is provided
if (!isset($_GET['event_id']) || empty($_GET['event_id'])) {
    header("Location: dashboard.php");
    exit();
}

$event_id = intval($_GET['event_id']);

// Check if booking already exists
$check_sql = "SELECT * FROM bookings WHERE user_id = ? AND event_id = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("ii", $user_id, $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Already booked
    $_SESSION['message'] = "You have already booked this event.";
    header("Location: dashboard.php");
    exit();
}

// Proceed with booking
$book_sql = "INSERT INTO bookings (user_id, event_id, booked_at) VALUES (?, ?, NOW())";
$stmt = $conn->prepare($book_sql);
$stmt->bind_param("ii", $user_id, $event_id);

if ($stmt->execute()) {
    $_SESSION['message'] = "Event booked successfully!";
} else {
    $_SESSION['message'] = "Failed to book event. Please try again.";
}

$stmt->close();
$conn->close();

header("Location: dashboard.php");
exit();
?>
