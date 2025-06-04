<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["is_admin"])) {
    header("Location: login.php");
    exit;
}

// Handle booking deletion
if (isset($_GET['delete'])) {
    $booking_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id=?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    header("Location: bookings.php");
    exit;
}

// Fetch all bookings with user and event details
$sql = "SELECT b.id, u.name as user_name, e.title as event_title, b.booked_at
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN events e ON b.event_id = e.id
        ORDER BY b.booked_at DESC";

$bookings = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manage Bookings - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    html, body {
      height: 100%;
      margin: 0;
      display: flex;
      flex-direction: column;
    }

    body {
      background: #f9f9f9;
    }

    .container {
      flex: 1;
      margin-top: 40px;
    }

    .table td, .table th {
      vertical-align: middle;
    }

    footer {
      background-color: #1f0036;
      color: white;
      text-align: center;
      padding: 15px 0;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg" style="background-color: #1f0036;">
  <div class="container-fluid">
    <span class="navbar-brand text-white fw-bold">📋 Manage Bookings</span>
    <div class="ms-auto">
      <a href="admin_dashboard.php" class="btn btn-outline-light">Go to Dashboard</a>
    </div>
  </div>
</nav>

<div class="container">

  <?php if ($bookings->num_rows > 0): ?>
  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>User</th>
        <th>Event</th>
        <th>Booked At</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($booking = $bookings->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($booking['user_name']) ?></td>
        <td><?= htmlspecialchars($booking['event_title']) ?></td>
        <td><?= date("F j, Y, g:i a", strtotime($booking['booked_at'])) ?></td>
        <td>
          <a href="view_ticket.php?booking_id=<?= $booking['id'] ?>" class="btn btn-sm btn-primary">🎟 View Ticket</a>
          <a href="bookings.php?delete=<?= $booking['id'] ?>" onclick="return confirm('Delete this booking?');" class="btn btn-sm btn-danger">🗑 Delete</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  <?php else: ?>
    <div class="alert alert-info">No bookings found.</div>
  <?php endif; ?>

</div>

<!-- Footer -->
<footer>
  <div class="container">
    <small>&copy; <?= date("Y") ?> Santan Events. All rights reserved.</small>
  </div>
</footer>

</body>
</html>
