<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT b.id as booking_id, e.id as event_id, e.title, e.description, e.date, e.time, e.location, e.category, e.ticket_price, b.booked_at
        FROM bookings b
        JOIN events e ON b.event_id = e.id
        WHERE b.user_id = ?
        ORDER BY b.booked_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>My Bookings - Santan Events</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    html, body {
      height: 100%;
      margin: 0;
    }

    body {
      background: #f0f2f5;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      flex-direction: column;
    }

    .navbar {
      background: linear-gradient(to right, #1f0036, #42075f);
    }

    .navbar-brand {
      font-size: 1.5rem;
      color: white;
      text-decoration: none;
    }

    .navbar-brand:hover {
      color: white;
      text-decoration: none;
    }

    .main-content {
      flex: 1 0 auto;
      padding-bottom: 20px;
    }

    .card {
      border: none;
      border-radius: 15px;
      background: #ffffff;
      transition: all 0.3s ease-in-out;
    }

    .card:hover {
      transform: scale(1.02);
      box-shadow: 0 10px 20px rgba(127, 90, 240, 0.3);
    }

    .search-input {
      width: 100%;
      max-width: 400px;
      margin-bottom: 20px;
    }

    .hidden {
      display: none !important;
    }

    .btn-back {
      margin-bottom: 20px;
    }

    footer {
      background-color: #1f0036;
      color: white;
      text-align: center;
      padding: 15px 0;
      flex-shrink: 0;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg px-3">
  <a class="navbar-brand" href="dashboard.php">🎧 Santan Events</a>
  <div class="ms-auto d-flex gap-2">
    <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
  </div>
</nav>

<div class="container mt-5 main-content">
  <h3 class="mb-4">My Booked Events</h3>

  <div class="text-end">
    <a href="dashboard.php" class="btn btn-secondary btn-sm btn-back">⬅ Back to Dashboard</a>
  </div>

  <input type="text" id="searchInput" class="form-control search-input" placeholder="Search by title, location, or category...">

  <?php if ($result->num_rows > 0): ?>
    <div class="row" id="bookingsList">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col-md-6 col-lg-4 mb-4 booking-card">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <h5 class="card-title text-primary"><?= htmlspecialchars($row['title']) ?></h5>
              <p class="card-text"><?= htmlspecialchars($row['description']) ?></p>
              <p><strong>Date:</strong> <?= date("F j, Y", strtotime($row['date'])) ?> |
                 <strong>Time:</strong> <?= date("g:i A", strtotime($row['time'])) ?></p>
              <p><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
              <p><strong>Category:</strong> <?= htmlspecialchars($row['category']) ?></p>
              <p><strong>Ticket Price:</strong> Ksh <?= number_format($row['ticket_price'], 2) ?></p>
              <small class="text-muted d-block mb-2">Booked on <?= date("F j, Y, g:i a", strtotime($row['booked_at'])) ?></small>

              <form method="POST" action="cancel_booking.php" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                <input type="hidden" name="booking_id" value="<?= $row['booking_id'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger">Cancel Booking</button>
              </form>

              <a href="view_ticket.php?booking_id=<?= $row['booking_id'] ?>" class="btn btn-sm btn-primary mt-2">View Ticket</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <div class="alert alert-info">You haven’t booked any events yet.</div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.getElementById("searchInput").addEventListener("input", function () {
    const value = this.value.toLowerCase();
    const cards = document.querySelectorAll(".booking-card");

    cards.forEach(card => {
      const text = card.innerText.toLowerCase();
      card.classList.toggle("hidden", !text.includes(value));
    });
  });
</script>

<!--Footer -->
<footer>
  <div class="container">
    <small>&copy; <?= date("Y") ?> Santan Events. All rights reserved.</small>
  </div>
</footer>

</body>
</html>
