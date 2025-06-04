<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Get search/filter inputs
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

// Base query
$sql = "SELECT * FROM events WHERE date >= CURDATE()";

// Filter: Search
if (!empty($search)) {
    $searchSafe = $conn->real_escape_string($search);
    $sql .= " AND (title LIKE '%$searchSafe%' OR location LIKE '%$searchSafe%')";
}

// Filter: Category
if (!empty($category)) {
    $categorySafe = $conn->real_escape_string($category);
    $sql .= " AND category = '$categorySafe'";
}

$sql .= " ORDER BY date ASC";
$result = $conn->query($sql);
$events = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Fetch categories for dropdown
$categories = [];
$catResult = $conn->query("SELECT DISTINCT category FROM events WHERE category IS NOT NULL");
if ($catResult) {
    while ($row = $catResult->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Dashboard - Project Santan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f4f6f8;
      font-family: 'Segoe UI', sans-serif;
    }
    .navbar {
      background: linear-gradient(to right, #7f5af0, #6246ea);
    }
    .navbar-brand {
      color: white;
      font-weight: bold;
    }
    .navbar-brand:hover {
      color: #e0e0e0;
    }
    .navbar-text, .btn-outline-light {
      color: white;
    }
    .dashboard-header {
      background: linear-gradient(45deg, #7f5af0, #4e44ce);
      color: white;
      border-radius: 12px;
      padding: 30px;
      margin-bottom: 30px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }
    .dashboard-header h2 {
      margin-bottom: 5px;
    }
    .event-card {
      border-radius: 12px;
      overflow: hidden;
      transition: all 0.3s ease-in-out;
    }
    .event-card:hover {
      box-shadow: 0 12px 24px rgba(127, 90, 240, 0.3);
      transform: translateY(-5px);
    }
    .event-img {
      height: 200px;
      object-fit: cover;
    }
    .card-title {
      font-weight: 600;
    }
    .event-footer {
      border-top: 1px solid #eee;
      padding-top: 10px;
      margin-top: 10px;
      font-size: 0.9rem;
    }
    .category-badge {
      background-color: #7f5af0;
      color: white;
      font-size: 0.75rem;
      padding: 5px 10px;
      border-radius: 20px;
    }

    .btn-custom {
  padding: 8px 20px;
  font-size: 0.95rem;
  border-radius: 30px;
  transition: 0.3s;
}

.btn-custom:hover {
  box-shadow: 0 0 12px rgba(255, 255, 255, 0.5);
  transform: scale(1.05);
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

<nav class="navbar navbar-expand-lg navbar-dark px-4" style="background: linear-gradient(90deg, #1f0036, #42075f);">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="../index.php" style="font-family: 'Orbitron', sans-serif; font-size: 1.8rem;">
      🎧 Santan Events
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon text-white"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarContent">
      <ul class="navbar-nav align-items-center">
        <li class="nav-item me-3 text-white">
          Hello, <strong><?= htmlspecialchars($user_name); ?></strong>
        </li>
        <li class="nav-item me-2">
          <a href="my_bookings.php" class="btn btn-outline-light btn-sm btn-custom">My Bookings</a>
        </li>
        <li class="nav-item">
          <a href="logout.php" class="btn btn-light btn-sm btn-custom">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>


<div class="container mt-5">

  <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-info">
      <?= $_SESSION['message']; unset($_SESSION['message']); ?>
    </div>
  <?php endif; ?>

  <div class="dashboard-header text-center">
    <h2><i class="bi bi-calendar-event"></i> Upcoming Events</h2>
    <p class="mb-0">Explore and book events with a click.</p>
  </div>

  <!-- Search and Filter -->
  <form class="row mb-4" method="GET" action="">
    <div class="col-md-6 mb-2">
      <input type="text" name="search" class="form-control" placeholder="Search by title or location" value="<?= htmlspecialchars($search); ?>">
    </div>
    <div class="col-md-4 mb-2">
      <select name="category" class="form-select">
        <option value="">All Categories</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= htmlspecialchars($cat); ?>" <?= $cat === $category ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2 mb-2">
      <button type="submit" class="btn btn-primary w-100">Filter</button>
    </div>
  </form>

  <div class="row">
    <?php if (count($events) > 0): ?>
      <?php foreach ($events as $event): ?>
        <div class="col-md-6 col-lg-4 mb-4">
          <div class="card event-card shadow-sm h-100">
            <?php if (!empty($event['image'])): ?>
              <img src="../uploads/<?= $event['image']; ?>" class="card-img-top event-img" alt="Event Image">
            <?php endif; ?>
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($event['title']); ?></h5>
              <?php if (!empty($event['category'])): ?>
                <span class="category-badge mb-2"><?= htmlspecialchars($event['category']); ?></span>
              <?php endif; ?>
              <p class="card-text"><?= nl2br(htmlspecialchars($event['description'])); ?></p>
              <div class="mt-auto event-footer">
                <p class="text-muted mb-1"><strong>Date:</strong> <?= date("F j, Y", strtotime($event['date'])); ?> at <?= date("g:i A", strtotime($event['time'])); ?></p>
                <p class="text-muted"><strong>Location:</strong> <?= htmlspecialchars($event['location']); ?></p>
              </div>
              <a href="book_event.php?event_id=<?= $event['id']; ?>" class="btn btn-primary w-100 mt-3">Book Event</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="text-center text-muted">
        <p>No matching events found. Try adjusting your search or filters.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<!-- Footer -->
<footer>
  <div class="container">
    <small>&copy; <?= date("Y") ?> Santan Events. All rights reserved.</small>
  </div>
</footer>

</body>
</html>
