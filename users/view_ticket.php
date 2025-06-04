<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['booking_id'])) {
    header("Location: my_bookings.php");
    exit();
}

$booking_id = $_GET['booking_id'];
$user_id = $_SESSION['user_id'];

$sql = "SELECT e.title, e.description, e.date, e.time, e.location, e.category, b.booked_at
        FROM bookings b
        JOIN events e ON b.event_id = e.id
        WHERE b.id = ? AND b.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Ticket not found.";
    exit();
}

$ticket = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Ticket</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background: #f5f5f5;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    .ticket {
      max-width: 600px;
      margin: 50px auto;
      border: 2px dashed #7f5af0;
      padding: 30px;
      background: white;
      border-radius: 15px;
    }
    .ticket h3 {
      color: #7f5af0;
    }
    footer {
      margin-top: auto;
      background-color: #1f0036;
      color: white;
      text-align: center;
      padding: 15px 0;
    }
  </style>
</head>
<body>

<div class="ticket shadow">
  <h3>🎟 Santan Events - Event Ticket</h3>
  <hr>
  <p><strong>Event:</strong> <?= htmlspecialchars($ticket['title']) ?></p>
  <p><strong>Description:</strong> <?= htmlspecialchars($ticket['description']) ?></p>
  <p><strong>Date & Time:</strong> <?= date("F j, Y", strtotime($ticket['date'])) ?> @ <?= date("g:i A", strtotime($ticket['time'])) ?></p>
  <p><strong>Location:</strong> <?= htmlspecialchars($ticket['location']) ?></p>
  <p><strong>Category:</strong> <?= htmlspecialchars($ticket['category']) ?></p>
  <p><strong>Booking Time:</strong> <?= date("F j, Y, g:i a", strtotime($ticket['booked_at'])) ?></p>
  <hr>
  <p><strong>Ticket No:</strong> <?= sprintf("TCKT-%06d", $booking_id) ?></p>

  <a href="my_bookings.php" class="btn btn-secondary mt-3">⬅ Back to Bookings</a>
</div>

<footer>
  <div class="container">
    <small>&copy; <?= date("Y") ?> Santan Events. All rights reserved.</small>
  </div>
</footer>

</body>
</html>
