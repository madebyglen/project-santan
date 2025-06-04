<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["is_admin"])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    html, body {
      height: 100%;
      margin: 0;
      display: flex;
      flex-direction: column;
    }

    body {
      background-color: #f8f9fa;
    }

    main {
      flex: 1;
    }

    .admin-header {
      background: linear-gradient(to right, #1f0036, #1f0036);
      color: white;
    }

    .dashboard-card {
      border-radius: 12px;
      transition: 0.3s ease;
    }

    .dashboard-card:hover {
      transform: scale(1.03);
      box-shadow: 0 8px 16px rgba(0,0,0,0.1);
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
<nav class="navbar navbar-expand-lg navbar-dark admin-header p-3">
  <div class="container">
    <a class="navbar-brand" href="#">🎧 Admin Dashboard</a>
    <div class="ms-auto">
      <span class="me-3">Welcome, <?= htmlspecialchars($_SESSION["user_name"]) ?></span>
      <a href="admin_logout.php" class="btn btn-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<!-- Main Content -->
<main class="container mt-5">
  <h2 class="mb-4">Admin Control Panel</h2>
  <div class="row g-4">

    <div class="col-md-4">
      <div class="card dashboard-card p-4">
        <h4>Manage Events</h4>
        <p>Create, edit, or delete events</p>
        <a href="events.php" class="btn btn-primary">Go</a>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card dashboard-card p-4">
        <h4>Manage Users</h4>
        <p>View and manage registered users</p>
        <a href="users.php" class="btn btn-primary">Go</a>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card dashboard-card p-4">
        <h4>Manage Bookings</h4>
        <p>View event bookings</p>
        <a href="bookings.php" class="btn btn-primary">Go</a>
      </div>
    </div>

  </div>
</main>

<!-- Footer -->
<footer>
  <div class="container">
    <small>&copy; <?= date("Y") ?> Santan Events. All rights reserved.</small>
  </div>
</footer>

</body>
</html>
